<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Services\AuditService;
use App\Services\InvoiceCalculatorService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function customerIndex(Request $request): View
    {
        $invoices = Invoice::with('deliveryOrder.supplier', 'customer')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('deliveryOrder', function ($doQuery) use ($search): void {
                            $doQuery->where('do_number', 'like', "%{$search}%")
                                ->orWhereHas('supplier', function ($supplierQuery) use ($search): void {
                                    $supplierQuery->where('supplier_name', 'like', "%{$search}%")
                                        ->orWhere('vendor_number', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request): void {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customer.invoices.index', compact('invoices'));
    }

    public function customerShow(int $id): View
    {
        $invoice = Invoice::with('deliveryOrder.supplier', 'customer')->findOrFail($id);

        if ($invoice->status === 'Submitted') {
            $invoice->update(['status' => 'Reviewed']);
        }

        return view('customer.invoices.show', compact('invoice'));
    }

    public function reject(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $invoice = Invoice::with('deliveryOrder.supplier')->findOrFail($id);
        $supplier = $invoice->deliveryOrder->supplier;

        $invoice->update([
            'status' => 'Rejected',
            'reason' => $validated['reason'],
        ]);

        $notificationService->forSupplier(
            $supplier,
            'invoice_rejected',
            'Invoice '.$invoice->invoice_number.' was rejected: '.$validated['reason']
        );

        $auditService->record(
            'invoice rejection',
            'invoices:'.$invoice->invoice_id,
            auth()->user(),
            $supplier
        );

        return back()->with('success', 'Invoice rejected.');
    }

    public function paymentProcessing(
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $invoice = Invoice::with('deliveryOrder.supplier')->findOrFail($id);
        $supplier = $invoice->deliveryOrder->supplier;

        $invoice->update([
            'status' => 'Payment Processing',
            'reason' => null,
        ]);

        $notificationService->forSupplier(
            $supplier,
            'invoice_payment_processing',
            'Invoice '.$invoice->invoice_number.' moved to Payment Processing.'
        );

        $auditService->record(
            'invoice payment processing',
            'invoices:'.$invoice->invoice_id,
            auth()->user(),
            $supplier
        );

        return back()->with('success', 'Invoice moved to Payment Processing.');
    }

    public function paid(
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $invoice = Invoice::with('deliveryOrder.supplier')->findOrFail($id);
        $supplier = $invoice->deliveryOrder->supplier;

        $invoice->update([
            'status' => 'Paid',
            'reason' => null,
        ]);

        $notificationService->forSupplier(
            $supplier,
            'invoice_paid',
            'Invoice '.$invoice->invoice_number.' has been marked as Paid.'
        );

        $auditService->record(
            'invoice paid',
            'invoices:'.$invoice->invoice_id,
            auth()->user(),
            $supplier
        );

        return back()->with('success', 'Invoice marked as Paid.');
    }

    public function supplierCreate(Request $request, int $do_id): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrder = DeliveryOrder::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($do_id);

        return view('supplier.invoice-submit', compact('supplier', 'deliveryOrder'));
    }

    public function supplierStore(
        Request $request,
        InvoiceCalculatorService $invoiceCalculatorService,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));

        $validated = $request->validate([
            'do_id' => ['required', 'exists:delivery_orders,do_id'],
            'invoice_number' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'issue_date' => ['required', 'date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'credit_note' => ['nullable', 'numeric', 'min:0'],
        ]);

        $deliveryOrder = DeliveryOrder::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($validated['do_id']);

        $customer = Customer::where('user_status', 'active')->first();

        if (! $customer) {
            return back()->with('error', 'No active customer account is available to review invoices.');
        }

        $creditNote = (float) ($validated['credit_note'] ?? 0);
        $total = $invoiceCalculatorService->calculate(
            (float) $validated['subtotal'],
            (float) $validated['tax'],
            $creditNote
        );

        $invoice = Invoice::create([
            'do_id' => $deliveryOrder->do_id,
            'cust_id' => $customer->cust_id,
            'invoice_number' => $validated['invoice_number'],
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issue_date'],
            'subtotal' => $validated['subtotal'],
            'tax' => $validated['tax'],
            'credit_note' => $creditNote,
            'total' => $total,
            'status' => 'Submitted',
        ]);

        $notificationService->forAllCustomers(
            'invoice_submitted',
            $supplier->supplier_name.' submitted Invoice '.$invoice->invoice_number.'.'
        );

        $auditService->record(
            'invoice submission',
            'invoices:'.$invoice->invoice_id,
            null,
            $supplier
        );

        return redirect()->route('supplier.invoice.status')->with('success', 'Invoice submitted successfully.');
    }

    public function supplierStatus(Request $request): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $invoices = Invoice::with('deliveryOrder')
            ->whereHas('deliveryOrder', function ($query) use ($supplier): void {
                $query->where('supplier_id', $supplier->supplier_id);
            })
            ->latest()
            ->paginate(10);

        return view('supplier.invoice-status', compact('supplier', 'invoices'));
    }
}
