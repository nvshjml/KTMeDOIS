@extends('layouts.app')

@section('title', 'Submit Invoice - KTM eDOIS')
@section('page-title', 'Submit Invoice')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<form method="POST" action="{{ route('supplier.invoice.store') }}">
    @csrf
    <input type="hidden" name="do_id" value="{{ $deliveryOrder->do_id }}">

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <section class="content-card p-4">
                <h2 class="h5 fw-bold mb-1">Invoice Creation</h2>
                <p class="text-muted small mb-4">Invoice claims are generated against approved Delivery Orders and routed to KTM finance review.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="invoice_number">Invoice Number *</label>
                        <input class="form-control" id="invoice_number" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="e.g. INV-2026-0001" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="issue_date">Invoice Date *</label>
                        <input class="form-control" id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Bill-to</div>
                        <div class="readonly-field">Keretapi Tanah Melayu Berhad<br>KTMB Headquarters, Kuala Lumpur</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Ship-to / Delivery Order</div>
                        <div class="readonly-field">{{ $deliveryOrder->do_number }}<br>{{ $deliveryOrder->po_number }}</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Product / Service Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Example: KTMB service or goods delivered under approved DO">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="content-card p-3 mt-4 shadow-none">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">Amount Calculator</h3>
                        <span class="small text-muted">Total = PO Price + 6% Tax - Discount - 1% Penalty</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="subtotal">Purchase Order Price *</label>
                            <input class="form-control js-amount" id="subtotal" name="subtotal" type="number" min="0" step="0.01" value="{{ old('subtotal') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tax_preview">Tax (6% of PO Price)</label>
                            <input class="form-control" id="tax_preview" value="RM 0.00" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="credit_note">Discount / Credit Note</label>
                            <input class="form-control js-amount" id="credit_note" name="credit_note" type="number" min="0" step="0.01" value="{{ old('credit_note', 0) }}" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="penalty_preview">Delay Penalty (1% of PO Price)</label>
                            <input class="form-control" id="penalty_preview" value="RM 0.00" readonly>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input js-amount" type="checkbox" role="switch" id="apply_penalty" name="apply_penalty" value="1" @checked(old('apply_penalty'))>
                                <label class="form-check-label fw-semibold" for="apply_penalty">Apply 1% delay penalty</label>
                            </div>
                        </div>
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
                            <div class="small text-muted mb-1">Payment Terms</div>
                            <div class="fw-bold">30 days</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button class="btn btn-primary px-5" type="submit">Submit Invoice</button>
                    <a class="btn btn-outline-primary px-5" href="{{ route('supplier.invoice.status') }}">View Status</a>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="content-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo" style="width:110px;height:auto">
                    <h2 class="h4 fw-bold">INVOICE</h2>
                </div>

                <div class="small text-muted mb-4">
                    Keretapi Tanah Melayu Berhad<br>
                    Company Registration No: 199101015631<br>
                    SST No: W10-1808-31002103
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <div class="small text-muted mb-1">Bill-to</div>
                        <div class="fw-semibold small">Keretapi Tanah Melayu Berhad</div>
                    </div>
                    <div class="col-6">
                        <div class="small text-muted mb-1">Supplier</div>
                        <div class="fw-semibold small">{{ $supplier->supplier_name }}</div>
                    </div>
                </div>

                <div class="d-grid gap-2 small mb-4">
                    <div class="d-flex justify-content-between"><span>Approved DO</span><strong>{{ $deliveryOrder->do_number }}</strong></div>
                    <div class="d-flex justify-content-between"><span>PO Number</span><strong>{{ $deliveryOrder->po_number }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Tax Rate</span><strong>6%</strong></div>
                    <div class="d-flex justify-content-between"><span>Penalty Rate</span><strong>1%</strong></div>
                </div>

                <div class="amount-preview p-3">
                    <div class="small text-uppercase fw-bold mb-2">Balance Due Preview</div>
                    <div class="d-flex justify-content-between small mb-1"><span>PO Price</span><strong id="po_preview">RM 0.00</strong></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Tax</span><strong id="tax_summary">RM 0.00</strong></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Discount</span><strong id="discount_summary">RM 0.00</strong></div>
                    <div class="d-flex justify-content-between small mb-2"><span>Penalty</span><strong id="penalty_summary">RM 0.00</strong></div>
                    <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Total</span>
                        <strong class="fs-4" id="total_preview">RM 0.00</strong>
                    </div>
                </div>
            </section>
        </div>
    </div>
</form>

<script>
    const subtotalInput = document.getElementById('subtotal');
    const discountInput = document.getElementById('credit_note');
    const penaltySwitch = document.getElementById('apply_penalty');
    const taxPreview = document.getElementById('tax_preview');
    const penaltyPreview = document.getElementById('penalty_preview');
    const poPreview = document.getElementById('po_preview');
    const taxSummary = document.getElementById('tax_summary');
    const discountSummary = document.getElementById('discount_summary');
    const penaltySummary = document.getElementById('penalty_summary');
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
        const poPrice = numberValue(subtotalInput);
        const tax = poPrice * 0.06;
        const discount = numberValue(discountInput);
        const penalty = penaltySwitch.checked ? poPrice * 0.01 : 0;
        const total = Math.max(0, poPrice + tax - discount - penalty);

        poPreview.textContent = money(poPrice);
        taxPreview.value = money(tax);
        taxSummary.textContent = money(tax);
        discountSummary.textContent = money(discount);
        penaltyPreview.value = money(penalty);
        penaltySummary.textContent = money(penalty);
        totalPreview.textContent = money(total);
    }

    document.querySelectorAll('.js-amount').forEach((input) => {
        input.addEventListener('input', updateTotalPreview);
        input.addEventListener('change', updateTotalPreview);
    });

    updateTotalPreview();
</script>
@endsection
