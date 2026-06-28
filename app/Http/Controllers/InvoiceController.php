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
        if ((auth()->user()->user_role ?? 'admin') === 'reviewer') {
            abort(403);
        }

        $invoices = Invoice::with('deliveryOrder.supplier', 'customer', 'assignedFinance')
            ->when((auth()->user()->user_role ?? 'admin') === 'finance', function ($query): void {
                $query->where('assigned_finance_id', auth()->id());
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->string('search');
                $supplierIds = $this->matchingSupplierIds($search);
                $deliveryOrderIds = DeliveryOrder::query()
                    ->where('do_number', 'like', "%{$search}%")
                    ->orWhere('po_number', 'like', "%{$search}%")
                    ->when($supplierIds->isNotEmpty(), function ($deliveryOrderQuery) use ($supplierIds): void {
                        $deliveryOrderQuery->orWhereIn('supplier_id', $supplierIds);
                    })
                    ->limit(100)
                    ->pluck('do_id');

                $query->where(function ($inner) use ($search, $deliveryOrderIds): void {
                    $inner->where('invoice_number', 'like', "%{$search}%");

                    if ($deliveryOrderIds->isNotEmpty()) {
                        $inner->orWhereIn('do_id', $deliveryOrderIds);
                    }
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
        if ((auth()->user()->user_role ?? 'admin') === 'reviewer') {
            abort(403);
        }

        $invoice = Invoice::with('deliveryOrder.supplier', 'customer', 'assignedFinance', 'assignedBy')
            ->when((auth()->user()->user_role ?? 'admin') === 'finance', function ($query): void {
                $query->where('assigned_finance_id', auth()->id());
            })
            ->findOrFail($id);
        $financeOfficers = $this->activeOfficers();

        return view('customer.invoices.show', compact('invoice', 'financeOfficers'));
    }

    public function customerPrint(int $id): View
    {
        if ((auth()->user()->user_role ?? 'admin') === 'reviewer') {
            abort(403);
        }

        $invoice = Invoice::with('deliveryOrder.supplier', 'customer')
            ->when((auth()->user()->user_role ?? 'admin') === 'finance', function ($query): void {
                $query->where('assigned_finance_id', auth()->id());
            })
            ->findOrFail($id);

        return view('print.invoice', compact('invoice'));
    }

    public function reject(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        if (in_array(auth()->user()->user_role ?? 'admin', ['reviewer', 'finance'], true)) {
            return back()->with('error', 'Only an admin officer can assign finance reviewers.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $invoice = Invoice::with('deliveryOrder.supplier')->findOrFail($id);
        $supplier = $invoice->deliveryOrder->supplier;

        if (! $this->currentOfficerCanReviewInvoice($invoice)) {
            return back()->with('error', 'Only the assigned finance officer can reject this Invoice.');
        }

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

        if (! $this->currentOfficerCanReviewInvoice($invoice)) {
            return back()->with('error', 'Only the assigned finance officer can move this Invoice to payment processing.');
        }

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

        if ((int) $invoice->assigned_finance_id !== (int) auth()->id() || $invoice->status !== 'Payment Processing') {
            return back()->with('error', 'Only the assigned finance officer can mark this Invoice as paid.');
        }

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

    public function assignFinance(
        Request $request,
        int $id,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $validated = $request->validate([
            'assigned_finance_id' => ['required', 'exists:customers,cust_id'],
        ]);

        $financeOfficer = Customer::where('user_status', 'active')->findOrFail($validated['assigned_finance_id']);
        $invoice = Invoice::with('deliveryOrder.supplier')->findOrFail($id);
        $supplier = $invoice->deliveryOrder->supplier;

        if (in_array($invoice->status, ['Paid', 'Rejected'], true)) {
            return back()->with('error', 'Completed Invoices cannot be reassigned.');
        }

        $invoice->update([
            'assigned_finance_id' => $financeOfficer->cust_id,
            'assigned_by_id' => auth()->id(),
            'forwarded_at' => now(),
            'status' => 'Finance Review',
            'reason' => null,
        ]);

        $notificationService->forCustomer(
            $financeOfficer,
            'invoice_assigned',
            'Invoice '.$invoice->invoice_number.' has been assigned to you for finance review.'
        );

        $notificationService->forSupplier(
            $supplier,
            'invoice_finance_review',
            'Invoice '.$invoice->invoice_number.' has been forwarded to KTM Finance.'
        );

        $auditService->record(
            'invoice finance assignment',
            'invoices:'.$invoice->invoice_id.':finance:'.$financeOfficer->cust_id,
            auth()->user(),
            $supplier
        );

        return back()->with('success', 'Invoice forwarded to finance.');
    }

    public function supplierCreate(Request $request, int $do_id): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $deliveryOrder = DeliveryOrder::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($do_id);
        $invoiceNumber = $this->generateInvoiceNumber($deliveryOrder);

        return view('supplier.invoice-submit', compact('supplier', 'deliveryOrder', 'invoiceNumber'));
    }

    public function supplierPreview(
        Request $request,
        InvoiceCalculatorService $invoiceCalculatorService
    ): View {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $validated = $this->validateSupplierInvoice($request, false);
        $deliveryOrder = DeliveryOrder::with('supplier')
            ->where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($validated['do_id']);
        $amounts = $this->invoiceAmounts($request, $validated, $invoiceCalculatorService);
        $customer = Customer::where('user_status', 'active')->first();

        $invoice = new Invoice([
            'invoice_number' => $request->input('invoice_number', $this->generateInvoiceNumber($deliveryOrder)),
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issue_date'],
            'subtotal' => $amounts['subtotal'],
            'tax' => $amounts['tax'],
            'credit_note' => $amounts['credit_note'],
            'penalty' => $amounts['penalty'],
            'total' => $amounts['total'],
            'status' => 'Preview',
        ]);
        $invoice->setRelation('deliveryOrder', $deliveryOrder);
        $invoice->setRelation('customer', $customer);

        return view('print.invoice', [
            'invoice' => $invoice,
            'autoPrint' => false,
        ]);
    }

    public function supplierStore(
        Request $request,
        InvoiceCalculatorService $invoiceCalculatorService,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));

        $validated = $this->validateSupplierInvoice($request);

        $deliveryOrder = DeliveryOrder::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($validated['do_id']);

        $customer = Customer::where('user_status', 'active')->first();

        if (! $customer) {
            return back()->with('error', 'No active admin account is available to review invoices.');
        }

        $amounts = $this->invoiceAmounts($request, $validated, $invoiceCalculatorService);
        $status = ($validated['action'] ?? 'submit') === 'draft' ? 'Draft' : 'Submitted';

        $invoice = Invoice::create([
            'do_id' => $deliveryOrder->do_id,
            'cust_id' => $customer->cust_id,
            'invoice_number' => $this->generateInvoiceNumber($deliveryOrder),
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issue_date'],
            'subtotal' => $amounts['subtotal'],
            'tax' => $amounts['tax'],
            'credit_note' => $amounts['credit_note'],
            'penalty' => $amounts['penalty'],
            'total' => $amounts['total'],
            'status' => $status,
        ]);

        if ($status === 'Submitted') {
            $notificationService->forAllCustomers(
                'invoice_submitted',
                $supplier->supplier_name.' submitted Invoice '.$invoice->invoice_number.'.'
            );
        }

        $auditService->record(
            $status === 'Submitted' ? 'invoice submission' : 'invoice draft',
            'invoices:'.$invoice->invoice_id,
            null,
            $supplier
        );

        return redirect()
            ->route('supplier.invoice.status')
            ->with('success', $status === 'Submitted' ? 'Invoice submitted successfully.' : 'Invoice saved as draft.');
    }

    public function supplierStatus(Request $request): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $invoices = Invoice::with('deliveryOrder')
            ->whereHas('deliveryOrder', function ($query) use ($supplier): void {
                $query->where('supplier_id', $supplier->supplier_id);
            })
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = (string) $request->string('search');
                $query->where(function ($inner) use ($search): void {
                    $inner->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('deliveryOrder', function ($deliveryOrderQuery) use ($search): void {
                            $deliveryOrderQuery->where('do_number', 'like', "%{$search}%")
                                ->orWhere('po_number', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('supplier.invoice-status', compact('supplier', 'invoices'));
    }

    public function supplierEdit(Request $request, int $id): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $invoice = $this->supplierOwnedInvoice($supplier, $id)
            ->where('status', 'Draft')
            ->firstOrFail();
        $deliveryOrder = $invoice->deliveryOrder;
        $invoiceNumber = $invoice->invoice_number;

        return view('supplier.invoice-submit', compact('supplier', 'deliveryOrder', 'invoice', 'invoiceNumber'));
    }

    public function supplierUpdate(
        Request $request,
        int $id,
        InvoiceCalculatorService $invoiceCalculatorService,
        AuditService $auditService,
        NotificationService $notificationService
    ): RedirectResponse {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $invoice = $this->supplierOwnedInvoice($supplier, $id)
            ->where('status', 'Draft')
            ->firstOrFail();

        $validated = $this->validateSupplierInvoice($request);
        $deliveryOrder = DeliveryOrder::where('supplier_id', $supplier->supplier_id)
            ->where('status', 'Approved')
            ->findOrFail($validated['do_id']);

        abort_unless((int) $invoice->do_id === (int) $deliveryOrder->do_id, 404);

        $customer = Customer::where('user_status', 'active')->first();

        if (! $customer) {
            return back()->with('error', 'No active admin account is available to review invoices.');
        }

        $amounts = $this->invoiceAmounts($request, $validated, $invoiceCalculatorService);
        $status = ($validated['action'] ?? 'submit') === 'draft' ? 'Draft' : 'Submitted';

        $invoice->update([
            'cust_id' => $customer->cust_id,
            'description' => $validated['description'] ?? null,
            'issue_date' => $validated['issue_date'],
            'subtotal' => $amounts['subtotal'],
            'tax' => $amounts['tax'],
            'credit_note' => $amounts['credit_note'],
            'penalty' => $amounts['penalty'],
            'total' => $amounts['total'],
            'status' => $status,
        ]);

        if ($status === 'Submitted') {
            $notificationService->forAllCustomers(
                'invoice_submitted',
                $supplier->supplier_name.' submitted Invoice '.$invoice->invoice_number.'.'
            );
        }

        $auditService->record(
            $status === 'Submitted' ? 'invoice submission' : 'invoice draft update',
            'invoices:'.$invoice->invoice_id,
            null,
            $supplier
        );

        return redirect()
            ->route('supplier.invoice.status')
            ->with('success', $status === 'Submitted' ? 'Invoice submitted successfully.' : 'Invoice draft updated.');
    }

    public function supplierPrint(Request $request, int $id): View
    {
        $supplier = Supplier::findOrFail($request->session()->get('supplier_id'));
        $invoice = Invoice::with('deliveryOrder.supplier', 'customer')
            ->whereHas('deliveryOrder', function ($query) use ($supplier): void {
                $query->where('supplier_id', $supplier->supplier_id);
            })
            ->findOrFail($id);

        return view('print.invoice', compact('invoice'));
    }

    private function supplierOwnedInvoice(Supplier $supplier, int $id)
    {
        return Invoice::with('deliveryOrder.supplier', 'customer')
            ->whereHas('deliveryOrder', function ($query) use ($supplier): void {
                $query->where('supplier_id', $supplier->supplier_id);
            })
            ->where('invoice_id', $id);
    }

    private function activeOfficers()
    {
        return Customer::where('user_status', 'active')
            ->orderBy('display_name')
            ->orderBy('username')
            ->get();
    }

    private function currentOfficerCanReviewInvoice(Invoice $invoice): bool
    {
        return $invoice->assigned_finance_id !== null
            && (int) $invoice->assigned_finance_id === (int) auth()->id()
            && in_array($invoice->status, ['Finance Review', 'Reviewed'], true);
    }

    private function validateSupplierInvoice(Request $request, bool $allowAction = true): array
    {
        $rules = [
            'do_id' => ['required', 'exists:delivery_orders,do_id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'issue_date' => ['required', 'date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'credit_note' => ['nullable', 'numeric', 'min:0'],
            'apply_penalty' => ['nullable', 'boolean'],
        ];

        if ($allowAction) {
            $rules['action'] = ['nullable', 'in:draft,submit'];
        }

        $validated = $request->validate($rules);

        if ((float) ($validated['credit_note'] ?? 0) > (float) $validated['subtotal']) {
            abort_if(! $allowAction, 422, 'Discount / credit note cannot be greater than the Purchase Order price.');

            back()
                ->withErrors(['credit_note' => 'Discount / credit note cannot be greater than the Purchase Order price.'])
                ->withInput()
                ->throwResponse();
        }

        return $validated;
    }

    private function invoiceAmounts(Request $request, array $validated, InvoiceCalculatorService $invoiceCalculatorService): array
    {
        $purchaseOrderPrice = (float) $validated['subtotal'];
        $tax = $invoiceCalculatorService->tax($purchaseOrderPrice);
        $creditNote = (float) ($validated['credit_note'] ?? 0);
        $penalty = $request->boolean('apply_penalty')
            ? $invoiceCalculatorService->delayPenalty($purchaseOrderPrice)
            : 0.0;

        return [
            'subtotal' => $purchaseOrderPrice,
            'tax' => $tax,
            'credit_note' => $creditNote,
            'penalty' => $penalty,
            'total' => $invoiceCalculatorService->calculate(
                $purchaseOrderPrice,
                $tax,
                $creditNote,
                $penalty
            ),
        ];
    }

    private function generateInvoiceNumber(DeliveryOrder $deliveryOrder): string
    {
        $base = str_starts_with($deliveryOrder->do_number, 'DO-')
            ? 'INV-'.substr($deliveryOrder->do_number, 3)
            : 'INV-'.$deliveryOrder->do_number;

        $invoiceNumber = $base;
        $suffix = 2;

        while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            $invoiceNumber = $base.'-'.str_pad((string) $suffix, 2, '0', STR_PAD_LEFT);
            $suffix++;
        }

        return $invoiceNumber;
    }
}
