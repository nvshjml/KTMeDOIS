<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Supplier;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'suppliers' => Supplier::count(),
            'submitted_dos' => DeliveryOrder::whereIn('status', ['Submitted', 'Under Review'])->count(),
            'approved_dos' => DeliveryOrder::where('status', 'Approved')->count(),
            'open_invoices' => Invoice::whereIn('status', ['Submitted', 'Reviewed', 'Payment Processing'])->count(),
            'paid_invoices' => Invoice::where('status', 'Paid')->count(),
            'unread_notifications' => Notification::where('cust_id', auth()->id())->where('status', 'unread')->count(),
        ];

        $suppliers = Supplier::orderBy('SUPPLIER_COMP_NAME')->get();
        $latestDeliveryOrders = DeliveryOrder::with('supplier')->latest()->limit(5)->get();
        $latestInvoices = Invoice::with('deliveryOrder.supplier')->latest()->limit(5)->get();

        return view('customer.dashboard', compact('stats', 'suppliers', 'latestDeliveryOrders', 'latestInvoices'));
    }
}
