<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $pendingStatuses = ['Submitted', 'Under Review'];
        $approvedInvoiceStatuses = ['Reviewed', 'Payment Processing', 'Paid'];

        $stats = [
            'total_dos' => DeliveryOrder::count(),
            'pending_review' => DeliveryOrder::whereIn('status', $pendingStatuses)->count()
                + Invoice::whereIn('status', ['Submitted', 'Reviewed'])->count(),
            'approved_invoices' => Invoice::whereIn('status', $approvedInvoiceStatuses)->count(),
            'payment_updates' => Invoice::whereIn('status', ['Payment Processing', 'Paid'])->count(),
            'overdue_dos' => DeliveryOrder::where('status', 'Rejected')->count(),
            'overdue_payments' => Invoice::where('status', 'Rejected')->count(),
            'active_customers' => DeliveryOrder::distinct('supplier_id')->count('supplier_id'),
            'dos_this_month' => DeliveryOrder::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'submitted_dos' => DeliveryOrder::whereIn('status', $pendingStatuses)->count(),
            'approved_dos' => DeliveryOrder::where('status', 'Approved')->count(),
            'open_invoices' => Invoice::whereIn('status', ['Submitted', 'Reviewed', 'Payment Processing'])->count(),
            'paid_invoices' => Invoice::where('status', 'Paid')->count(),
            'unread_notifications' => Notification::where('cust_id', auth()->id())->where('status', 'unread')->count(),
        ];

        $latestDeliveryOrders = DeliveryOrder::with(['supplier', 'invoices'])
            ->latest()
            ->limit(5)
            ->get();

        $latestInvoices = Invoice::with('deliveryOrder.supplier')
            ->latest()
            ->limit(5)
            ->get();

        $dashboardRows = $this->dashboardRows($latestDeliveryOrders, $latestInvoices);

        $notifications = Notification::where('cust_id', auth()->id())
            ->latest()
            ->limit(4)
            ->get();

        $recentActivity = AuditLog::with(['customer', 'supplier'])
            ->latest('timestamp')
            ->limit(4)
            ->get();

        return view('customer.dashboard', compact(
            'stats',
            'latestDeliveryOrders',
            'latestInvoices',
            'dashboardRows',
            'notifications',
            'recentActivity',
        ));
    }

    private function dashboardRows(Collection $deliveryOrders, Collection $invoices): Collection
    {
        $deliveryOrderRows = $deliveryOrders->map(function (DeliveryOrder $deliveryOrder): array {
            $invoice = $deliveryOrder->invoices->sortByDesc('created_at')->first();

            return [
                'reference' => $deliveryOrder->do_number,
                'customer' => $deliveryOrder->supplier?->supplier_name ?? 'Supplier pending sync',
                'type' => 'DO',
                'date' => $deliveryOrder->created_at,
                'status' => $deliveryOrder->status === 'Submitted' ? 'Pending Review' : $deliveryOrder->status,
                'amount' => $invoice?->total,
                'href' => route('customer.delivery-orders.show', $deliveryOrder->do_id),
                'action' => $deliveryOrder->status === 'Approved' ? 'View' : 'Review',
            ];
        });

        $invoiceRows = $invoices->map(function (Invoice $invoice): array {
            return [
                'reference' => $invoice->invoice_number,
                'customer' => $invoice->deliveryOrder?->supplier?->supplier_name ?? 'Supplier pending sync',
                'type' => 'Invoice',
                'date' => $invoice->issue_date ?? $invoice->created_at,
                'status' => $invoice->status === 'Submitted' ? 'Pending Approval' : $invoice->status,
                'amount' => $invoice->total,
                'href' => route('customer.invoices.show', $invoice->invoice_id),
                'action' => $invoice->status === 'Paid' ? 'Download' : 'Review',
            ];
        });

        return $deliveryOrderRows
            ->merge($invoiceRows)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }
}
