<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\SupplierMasterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'login_as' => ['nullable', 'in:admin,supplier'],
        ]);

        $attempt = Auth::attempt([
            'username' => $credentials['login'],
            'password' => $credentials['password'],
            'user_status' => 'active',
        ], $request->boolean('remember'));

        if (! $attempt) {
            throw ValidationException::withMessages([
                'login' => 'The provided admin credentials are invalid or inactive.',
            ]);
        }

        $request->session()->regenerate();

        $customer = Auth::user();
        $customer->update(['last_login' => now()]);

        $auditService->record('admin login', 'customers:'.$customer->cust_id, $customer);

        return redirect()->intended(route('admin.dashboard'));
    }

    private function storeSupplierSession(
        Request $request,
        AuditService $auditService,
        SupplierMasterService $supplierMasterService
    ): RedirectResponse {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
            'login_as' => ['required', 'in:supplier'],
        ], [], [
            'login' => 'vendor number',
            'password' => 'password',
        ]);

        $supplier = $supplierMasterService->findByVendorNumber($credentials['login']);

        $auditService->record(
            'supplier validation',
            'suppliers:'.$credentials['login'],
            null,
            $supplier
        );

        if (! $supplier || ! Hash::check($credentials['password'], (string) $supplier->password_hash)) {
            throw ValidationException::withMessages([
                'login' => 'The supplier username or password is invalid.',
            ]);
        }

        if (Hash::needsRehash((string) $supplier->password_hash)) {
            $supplier->update([
                'password_hash' => Hash::make($credentials['password']),
            ]);
        }

        $request->session()->put('supplier_id', $supplier->supplier_id);
        $request->session()->regenerate();

        if (! $supplier->isActive()) {
            return redirect()
                ->route('supplier.do.status')
                ->with('warning', 'Supplier logged in, but this supplier is inactive. Delivery Order upload is disabled.');
        }

        return redirect()->route('supplier.do.create')->with('success', 'Supplier logged in successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}

