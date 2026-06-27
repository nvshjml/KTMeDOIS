@extends('layouts.app')

@section('title', 'Submit Invoice - KTM eDOIS')

@section('content')
<div class="d-flex flex-column gap-4">
    <section class="content-card p-4 p-xl-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker mb-2">Invoice Submission &amp; Claim</div>
                <h1 class="page-title h2 mb-3">Create invoice from approved DO</h1>
                <p class="text-muted mb-0">
                    Submit claim details against the approved Delivery Order. The total is calculated from subtotal, tax, and credit note values.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="panel-muted p-3">
                    <div class="small text-muted fw-bold text-uppercase mb-2">Approved Delivery Order</div>
                    <div class="fw-bold">{{ $deliveryOrder->do_number }}</div>
                    <div class="small text-muted">PO {{ $deliveryOrder->po_number }}</div>
                    <div class="mt-2">@include('shared.status-badge', ['status' => $deliveryOrder->status])</div>
                </div>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('supplier.invoice.store') }}">
        @csrf
        <input type="hidden" name="do_id" value="{{ $deliveryOrder->do_id }}">

        <div class="row g-4">
            <div class="col-xl-4">
                <section class="content-card p-4 h-100">
                    <div class="page-kicker mb-2">Claim Reference</div>
                    <h2 class="h5 form-section-title mb-3">Read-only DO details</h2>

                    <div class="d-grid gap-3">
                        <div>
                            <div class="small text-muted mb-1">Supplier</div>
                            <div class="readonly-field">{{ $supplier->supplier_name }}</div>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Delivery Order</div>
                            <div class="readonly-field fw-bold">{{ $deliveryOrder->do_number }}</div>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Purchase Order</div>
                            <div class="readonly-field">{{ $deliveryOrder->po_number }}</div>
                        </div>
                    </div>

                    <div class="amount-preview p-3 mt-4">
                        <div class="small text-uppercase fw-bold mb-1">Estimated Total</div>
                        <div class="fs-3 fw-bold" id="total_preview">RM 0.00</div>
                        <div class="small">Subtotal + tax - credit note</div>
                    </div>
                </section>
            </div>

            <div class="col-xl-8">
                <section class="content-card p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                        <div>
                            <div class="page-kicker mb-2">Invoice Details</div>
                            <h2 class="h5 form-section-title mb-0">Header, description, and amount</h2>
                        </div>
                        <span class="badge text-bg-light align-self-start">Finance Review -> Payment Processing -> Paid</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="invoice_number">Invoice Number</label>
                            <input class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="Example: INV-2026-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="issue_date">Issue Date</label>
                            <input class="form-control" id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe delivered items, quantities, or supporting claim details.">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="subtotal">Subtotal</label>
                            <input class="form-control js-amount" id="subtotal" name="subtotal" type="number" min="0" step="0.01" value="{{ old('subtotal') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tax">Tax</label>
                            <input class="form-control js-amount" id="tax" name="tax" type="number" min="0" step="0.01" value="{{ old('tax', 0) }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="credit_note">Credit Note</label>
                            <input class="form-control js-amount" id="credit_note" name="credit_note" type="number" min="0" step="0.01" value="{{ old('credit_note', 0) }}" placeholder="0.00">
                        </div>
                    </div>

                    <div class="panel-muted p-3 mt-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="small text-muted mb-1">Initial Status</div>
                                <div class="fw-bold">Submitted</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted mb-1">Next Step</div>
                                <div class="fw-bold">Finance Review</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted mb-1">Notification</div>
                                <div class="fw-bold">Sent to KTM officers</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button class="btn btn-primary px-4" type="submit">Submit Invoice</button>
                        <a class="btn btn-outline-primary px-4" href="{{ route('supplier.invoice.status') }}">View Invoice Status</a>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>

<script>
    const subtotalInput = document.getElementById('subtotal');
    const taxInput = document.getElementById('tax');
    const creditNoteInput = document.getElementById('credit_note');
    const totalPreview = document.getElementById('total_preview');

    function money(value) {
        return new Intl.NumberFormat('en-MY', {
            style: 'currency',
            currency: 'MYR',
        }).format(value).replace('MYR', 'RM');
    }

    function numberValue(input) {
        const value = Number.parseFloat(input.value);
        return Number.isFinite(value) ? value : 0;
    }

    function updateTotalPreview() {
        const total = Math.max(0, numberValue(subtotalInput) + numberValue(taxInput) - numberValue(creditNoteInput));
        totalPreview.textContent = money(total);
    }

    document.querySelectorAll('.js-amount').forEach((input) => {
        input.addEventListener('input', updateTotalPreview);
    });

    updateTotalPreview();
</script>
@endsection
