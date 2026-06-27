<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\SupplierMasterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(
        Request $request,
        AuditService $auditService,
        SupplierMasterService $supplierMasterService
    ): RedirectResponse {
        if ($request->input('login_as') === 'supplier') {
            return $this->storeSupplierSession($request, $auditService, $supplierMasterService);
        }

        return $this->storeCustomerSession($request, $auditService);
    }

    private function storeCustomerSession(Request $request, AuditService $auditService): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'login_as' => ['nullable', 'in:customer,supplier'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'user_email' : 'username';

        $attempt = Auth::attempt([
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
            'user_status' => 'active',
        ], $request->boolean('remember'));

        if (! $attempt) {
            throw ValidationException::withMessages([
                'login' => 'The provided customer credentials are invalid or inactive.',
            ]);
        }

        $request->session()->regenerate();

        $customer = Auth::user();
        $customer->update(['last_login' => now()]);

        $auditService->record('customer login', 'customers:'.$customer->cust_id, $customer);

        return redirect()->intended(route('customer.dashboard'));
    }

    private function storeSupplierSession(
        Request $request,
        AuditService $auditService,
        SupplierMasterService $supplierMasterService
    ): RedirectResponse {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'email', 'max:255'],
            'login_as' => ['required', 'in:supplier'],
        ], [], [
            'login' => 'vendor number',
            'password' => 'supplier email',
        ]);

        $supplier = $supplierMasterService->findByVendorAndEmail(
            $credentials['login'],
            $credentials['password']
        );

        $auditService->record(
            'supplier validation',
            'suppliers:'.$credentials['login'],
            null,
            $supplier
        );

        if (! $supplier || ! $supplier->isActive()) {
            throw ValidationException::withMessages([
                'login' => 'The supplier details are invalid or inactive.',
            ]);
        }

        $request->session()->put('supplier_id', $supplier->supplier_id);
        $request->session()->regenerate();

        return redirect()->route('supplier.profile')->with('success', 'Supplier verified successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
