@extends('layouts.app')

@section('title', 'Invoice '.$invoice->invoice_number.' - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
            <h1 class="h3 mb-1">{{ $invoice->invoice_number }}</h1>
            <p class="text-muted mb-0">{{ $invoice->deliveryOrder->supplier->supplier_name }} · DO {{ $invoice->deliveryOrder->do_number }}</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-dark btn-sm" target="_blank" href="{{ route('admin.invoices.print', $invoice->invoice_id) }}">Print / Save PDF</a>
            @include('shared.status-badge', ['status' => $invoice->status])
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Invoice Details</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Issue Date</dt>
                    <dd class="col-sm-8">{{ $invoice->issue_date?->format('d M Y') }}</dd>
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $invoice->description ?: 'None' }}</dd>
                    <dt class="col-sm-4">PO Price</dt>
                    <dd class="col-sm-8">RM {{ number_format($invoice->subtotal, 2) }}</dd>
                    <dt class="col-sm-4">Tax (6%)</dt>
                    <dd class="col-sm-8">RM {{ number_format($invoice->tax, 2) }}</dd>
                    <dt class="col-sm-4">Discount / Credit Note</dt>
                    <dd class="col-sm-8">RM {{ number_format($invoice->credit_note, 2) }}</dd>
                    <dt class="col-sm-4">Penalty (1%)</dt>
                    <dd class="col-sm-8">RM {{ number_format($invoice->penalty, 2) }}</dd>
                    <dt class="col-sm-4">Total</dt>
                    <dd class="col-sm-8 fw-bold">RM {{ number_format($invoice->total, 2) }}</dd>
                    <dt class="col-sm-4">Assigned Finance</dt>
                    <dd class="col-sm-8">{{ $invoice->assignedFinance?->name ?? 'Not assigned' }}</dd>
                    <dt class="col-sm-4">Forwarded By</dt>
                    <dd class="col-sm-8">{{ $invoice->assignedBy?->name ?? 'Not forwarded' }}</dd>
                    <dt class="col-sm-4">Forwarded At</dt>
                    <dd class="col-sm-8">{{ $invoice->forwarded_at?->format('d M Y, h:i A') ?? '-' }}</dd>
                    <dt class="col-sm-4">Reason</dt>
                    <dd class="col-sm-8">{{ $invoice->reason ?: 'None' }}</dd>
                </dl>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Payment Workflow</h2>

                @if(! in_array(auth()->user()->user_role ?? 'admin', ['reviewer', 'finance'], true))
                    <form method="POST" action="{{ route('admin.invoices.assign-finance', $invoice->invoice_id) }}" class="mb-3">
                        @csrf
                        <label class="form-label" for="assigned_finance_id">Assign Finance Officer</label>
                        <select class="form-select mb-2" id="assigned_finance_id" name="assigned_finance_id" required>
                            <option value="">Select KTM officer</option>
                            @foreach($financeOfficers as $financeOfficer)
                                <option value="{{ $financeOfficer->cust_id }}" @selected((int) old('assigned_finance_id', $invoice->assigned_finance_id) === (int) $financeOfficer->cust_id)>
                                    {{ $financeOfficer->name }} ({{ $financeOfficer->user_email }})
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary w-100" type="submit">Forward to Finance</button>
                    </form>
                @endif

                @if($invoice->assignedFinance)
                    <div class="alert alert-light border small">
                        Assigned to {{ $invoice->assignedFinance->name }} for finance review and payment.
                    </div>
                @endif

                @php
                    $canFinanceReview = $invoice->assigned_finance_id && (int) $invoice->assigned_finance_id === (int) auth()->id();
                @endphp

                @if(! $canFinanceReview && ! in_array($invoice->status, ['Paid', 'Rejected'], true))
                    <div class="alert alert-warning small">Only the assigned finance officer can update this Invoice payment workflow.</div>
                @endif

                @if($canFinanceReview && in_array($invoice->status, ['Finance Review', 'Reviewed'], true))
                    <form method="POST" action="{{ route('admin.invoices.payment-processing', $invoice->invoice_id) }}" class="mb-3">
                        @csrf
                        <button class="btn btn-warning w-100" type="submit">Move to Payment Processing</button>
                    </form>
                @endif

                @if($canFinanceReview && $invoice->status === 'Payment Processing')
                    <form method="POST" action="{{ route('admin.invoices.paid', $invoice->invoice_id) }}" class="mb-3">
                        @csrf
                        <button class="btn btn-success w-100" type="submit">Mark as Paid</button>
                    </form>
                @endif

                @if($canFinanceReview && $invoice->status !== 'Rejected' && $invoice->status !== 'Paid')
                    <form method="POST" action="{{ route('admin.invoices.reject', $invoice->invoice_id) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label" for="reason">Rejection Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required>{{ old('reason') }}</textarea>
                        </div>
                        <button class="btn btn-outline-danger w-100" type="submit">Reject Invoice</button>
                    </form>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

