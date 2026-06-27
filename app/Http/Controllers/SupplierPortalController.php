<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Notification;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPortalController extends Controller
{
    public function profile(Request $request): View
    {
        $supplier = $this->currentSupplier($request);
        $deliveryOrders = DeliveryOrder::with('invoices')
            ->where('supplier_id', $supplier->supplier_id)
            ->latest()
            ->get();
        $invoices = $deliveryOrders
            ->flatMap(fn ($deliveryOrder) => $deliveryOrder->invoices)
            ->sortByDesc('created_at')
            ->values();

        $stats = [
            'delivery_orders' => $deliveryOrders->count(),
            'submitted_dos' => $deliveryOrders->count(),
            'pending_review' => $deliveryOrders->whereIn('status', ['Submitted', 'Under Review'])->count(),
            'approved_delivery_orders' => $deliveryOrders->where('status', 'Approved')->count(),
            'invoice_claims' => $invoices->count(),
            'paid_invoices' => $invoices->where('status', 'Paid')->count(),
            'unread_notifications' => Notification::where('supplier_id', $supplier->supplier_id)
                ->where('status', 'unread')
                ->count(),
        ];

        $recentDeliveryOrders = $deliveryOrders->take(8);
        $recentNotifications = Notification::where('supplier_id', $supplier->supplier_id)
            ->latest()
            ->limit(4)
            ->get();
        $approvedDoId = $deliveryOrders->firstWhere('status', 'Approved')?->do_id;

        return view('supplier.supplier-profile', compact(
            'supplier',
            'stats',
            'recentDeliveryOrders',
            'recentNotifications',
            'approvedDoId',
        ));
    }

    public function details(Request $request): View
    {
        $supplier = $this->currentSupplier($request);

        return view('supplier.profile-details', compact('supplier'));
    }

    public function notifications(Request $request): View
    {
        $supplier = $this->currentSupplier($request);
        $notifications = Notification::where('supplier_id', $supplier->supplier_id)
            ->latest()
            ->paginate(10);

        return view('supplier.notifications', compact('supplier', 'notifications'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('supplier_id');

        return redirect()
            ->route('login', ['login_as' => 'supplier'])
            ->with('success', 'Supplier session ended.');
    }

    private function currentSupplier(Request $request, array $with = []): Supplier
    {
        return Supplier::with($with)->findOrFail($request->session()->get('supplier_id'));
    }
}
