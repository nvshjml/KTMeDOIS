<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $role = auth()->user()->user_role ?? 'admin';
        $officerId = auth()->id();
        $pendingStatuses = ['Submitted', 'Under Review'];
        $approvedInvoiceStatuses = ['Finance Review', 'Payment Processing', 'Paid'];
        $visibleDeliveryOrders = DeliveryOrder::where('status', '!=', 'Draft')
            ->when($role === 'reviewer', fn ($query) => $query->where('assigned_reviewer_id', $officerId))
            ->when($role === 'finance', fn ($query) => $query->whereRaw('1 = 0'));
        $visibleInvoices = Invoice::query()
            ->when($role === 'finance', fn ($query) => $query->where('assigned_finance_id', $officerId))
            ->when($role === 'reviewer', fn ($query) => $query->whereRaw('1 = 0'));

        $stats = [
            'total_dos' => (clone $visibleDeliveryOrders)->count(),
            'pending_review' => (clone $visibleDeliveryOrders)->whereIn('status', $pendingStatuses)->count()
                + (clone $visibleInvoices)->whereIn('status', ['Submitted', 'Finance Review'])->count(),
            'approved_invoices' => (clone $visibleInvoices)->whereIn('status', $approvedInvoiceStatuses)->count(),
            'payment_updates' => (clone $visibleInvoices)->whereIn('status', ['Payment Processing', 'Paid'])->count(),
            'overdue_dos' => (clone $visibleDeliveryOrders)->where('status', 'Rejected')->count(),
            'overdue_payments' => (clone $visibleInvoices)->where('status', 'Rejected')->count(),
            'active_customers' => (clone $visibleDeliveryOrders)->distinct('supplier_id')->count('supplier_id'),
            'dos_this_month' => (clone $visibleDeliveryOrders)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'submitted_dos' => (clone $visibleDeliveryOrders)->whereIn('status', $pendingStatuses)->count(),
            'approved_dos' => (clone $visibleDeliveryOrders)->where('status', 'Approved')->count(),
            'open_invoices' => (clone $visibleInvoices)->whereIn('status', ['Submitted', 'Finance Review', 'Payment Processing'])->count(),
            'paid_invoices' => (clone $visibleInvoices)->where('status', 'Paid')->count(),
            'unread_notifications' => Notification::where('cust_id', auth()->id())->where('status', 'unread')->count(),
        ];

        $latestDeliveryOrders = DeliveryOrder::with(['supplier', 'invoices'])
            ->where('status', '!=', 'Draft')
            ->when($role === 'reviewer', fn ($query) => $query->where('assigned_reviewer_id', $officerId))
            ->when($role === 'finance', fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(5)
            ->get();

        $latestInvoices = Invoice::with('deliveryOrder.supplier')
            ->when($role === 'finance', fn ($query) => $query->where('assigned_finance_id', $officerId))
            ->when($role === 'reviewer', fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->limit(5)
            ->get();

        $dashboardRows = $this->dashboardRows($latestDeliveryOrders, $latestInvoices);

        return view('customer.dashboard', compact(
            'stats',
            'latestDeliveryOrders',
            'latestInvoices',
            'dashboardRows',
            'role',
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
                'href' => route('admin.delivery-orders.show', $deliveryOrder->do_id),
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
                'href' => route('admin.invoices.show', $invoice->invoice_id),
                'action' => $invoice->status === 'Paid' ? 'Download' : 'Review',
            ];
        });

        return collect($deliveryOrderRows->all())
            ->merge($invoiceRows->all())
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }
}

