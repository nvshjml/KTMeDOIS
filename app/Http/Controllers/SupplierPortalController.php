<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\SupplierMasterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPortalController extends Controller
{
    public function verifyForm(): View
    {
        return view('supplier.supplier-verify');
    }

    public function verify(
        Request $request,
        SupplierMasterService $supplierMasterService,
        AuditService $auditService
    ): RedirectResponse {
        $validated = $request->validate([
            'vendor_number' => ['required', 'string', 'max:100'],
            'supplier_email' => ['required', 'email', 'max:255'],
        ]);

        $supplier = $supplierMasterService->findByVendorAndEmail(
            $validated['vendor_number'],
            $validated['supplier_email']
        );

        $auditService->record(
            'supplier validation',
            'suppliers:'.$validated['vendor_number'],
            null,
            $supplier
        );

        if (! $supplier) {
            return back()->withErrors([
                'vendor_number' => 'No supplier master record matches these details.',
            ])->withInput();
        }

        if (! $supplier->isActive()) {
            return back()->withErrors([
                'vendor_number' => 'This supplier is inactive and cannot submit Delivery Orders or Invoices.',
            ])->withInput();
        }

        $request->session()->put('supplier_id', $supplier->supplier_id);
        $request->session()->regenerate();

        return redirect()->route('supplier.profile')->with('success', 'Supplier verified successfully.');
    }

    public function profile(Request $request): View
    {
        $supplier = Supplier::with('deliveryOrders.invoices')->findOrFail($request->session()->get('supplier_id'));

        $stats = [
            'delivery_orders' => $supplier->deliveryOrders()->count(),
            'approved_delivery_orders' => $supplier->deliveryOrders()->where('status', 'Approved')->count(),
            'invoices' => Invoice::whereHas('deliveryOrder', function ($query) use ($supplier): void {
                $query->where('supplier_id', $supplier->supplier_id);
            })->count(),
            'unread_notifications' => Notification::where('supplier_id', $supplier->supplier_id)
                ->where('status', 'unread')
                ->count(),
        ];

        $notifications = Notification::where('supplier_id', $supplier->supplier_id)
            ->latest()
            ->limit(5)
            ->get();

        return view('supplier.supplier-profile', compact('supplier', 'stats', 'notifications'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('supplier_id');

        return redirect()->route('supplier.verify')->with('success', 'Supplier session ended.');
    }
}
