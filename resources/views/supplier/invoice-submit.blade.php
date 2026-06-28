@extends('layouts.app')

@section('title', (isset($invoice) ? 'Edit Invoice Draft' : 'Submit Invoice').' - KTM eDOIS')
@section('page-title', isset($invoice) ? 'Edit Invoice Draft' : 'Submit Invoice')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
@include('shared.back-button', ['href' => route('supplier.invoice.status'), 'label' => 'Back to Invoices'])

<form method="POST" action="{{ isset($invoice) ? route('supplier.invoice.update', $invoice->invoice_id) : route('supplier.invoice.store') }}">
    @csrf
    <input type="hidden" name="do_id" value="{{ $deliveryOrder->do_id }}">
    <input type="hidden" name="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}">

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <section class="content-card p-4">
                <h2 class="h5 fw-bold mb-1">Invoice Creation</h2>
                <p class="text-muted small mb-4">Invoice claims are generated against approved Delivery Orders and routed to KTM finance review.</p>

                <div class="panel-muted p-3 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div>
                            <div class="small text-muted mb-1">Invoice Header</div>
                            <div class="fw-bold">Invoice Creation / Editing</div>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Generated Invoice Number</div>
                            <div class="fw-bold text-primary">{{ old('invoice_number', $invoiceNumber) }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="invoice_number_display">Invoice Number</label>
                        <input class="form-control" id="invoice_number_display" value="{{ old('invoice_number', $invoiceNumber) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="issue_date">Invoice Date *</label>
                        <input class="form-control" id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', isset($invoice) ? $invoice->issue_date->toDateString() : now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Billing Address</div>
                        <div class="readonly-field">Keretapi Tanah Melayu Berhad<br>KTMB Headquarters, Kuala Lumpur</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Supplier Name</div>
                        <div class="readonly-field">{{ $supplier->supplier_name }}<br>Vendor No: {{ $supplier->vendor_number }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Customer Information</div>
                        <div class="readonly-field">Keretapi Tanah Melayu Berhad<br>Company Registration No: 199101015631</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted mb-1">Approved DO / PO Reference</div>
                        <div class="readonly-field">{{ $deliveryOrder->do_number }}<br>{{ $deliveryOrder->po_number }}</div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" id="description" name="description" value="{{ old('description', $invoice->description ?? '') }}">

                        <section class="invoice-item-panel">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-3">
                                <div>
                                    <h3 class="h5 fw-bold mb-1">Description / Item Details</h3>
                                    <p class="text-muted small mb-0">Add items, quantities, rates and other details for this invoice.</p>
                                </div>
                                <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2" id="add_invoice_item" type="button">
                                    @include('shared.dashboard-icon', ['name' => 'plus'])
                                    <span>Add Item</span>
                                </button>
                            </div>

                            <datalist id="invoice_item_suggestions">
                                <option value="Rail Fastening System"></option>
                                <option value="Steel Rail 60E1"></option>
                                <option value="Concrete Sleeper"></option>
                                <option value="Miscellaneous Hardware"></option>
                                <option value="Track Renewal Service"></option>
                                <option value="Signal Maintenance Service"></option>
                                <option value="Cable Installation Work"></option>
                                <option value="Proof of Delivery Service"></option>
                            </datalist>

                            <div class="table-responsive">
                                <table class="table invoice-items-table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:60px">#</th>
                                            <th>Item Description</th>
                                            <th style="width:150px">Quantity</th>
                                            <th style="width:150px">Unit</th>
                                            <th style="width:170px">Unit Price (RM)</th>
                                            <th style="width:170px" class="text-end">Amount (RM)</th>
                                            <th style="width:110px" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice_items_body"></tbody>
                                </table>
                            </div>
                        </section>
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
                            <input class="form-control js-amount" id="subtotal" name="subtotal" type="number" min="0" step="0.01" value="{{ old('subtotal', $invoice->subtotal ?? '') }}" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tax_preview">Tax (6% of PO Price)</label>
                            <input class="form-control" id="tax_preview" value="RM 0.00" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="credit_note">Discount / Credit Note</label>
                            <input class="form-control js-amount" id="credit_note" name="credit_note" type="number" min="0" step="0.01" value="{{ old('credit_note', $invoice->credit_note ?? 0) }}" placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="penalty_preview">Delay Penalty (1% of PO Price)</label>
                            <input class="form-control" id="penalty_preview" value="RM 0.00" readonly>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input js-amount" type="checkbox" role="switch" id="apply_penalty" name="apply_penalty" value="1" @checked(old('apply_penalty', isset($invoice) && (float) $invoice->penalty > 0))>
                                <label class="form-check-label fw-semibold" for="apply_penalty">Apply 1% delay penalty</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel-muted p-3 mt-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="small text-muted mb-1">{{ isset($invoice) ? 'Current Status' : 'Initial Status' }}</div>
                            <div class="fw-bold">{{ isset($invoice) ? $invoice->status : 'Submitted' }}</div>
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
                    <button class="btn btn-outline-primary px-5" type="submit" name="action" value="draft">Save as Draft</button>
                    <button class="btn btn-primary px-5" type="submit" name="action" value="submit">Submit</button>
                    <button
                        class="btn btn-warning px-5"
                        type="submit"
                        formaction="{{ route('supplier.invoice.preview') }}"
                        formtarget="_blank"
                    >
                        Preview PDF
                    </button>
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
    const descriptionInput = document.getElementById('description');
    const invoiceItemsBody = document.getElementById('invoice_items_body');
    const addInvoiceItemButton = document.getElementById('add_invoice_item');
    const taxPreview = document.getElementById('tax_preview');
    const penaltyPreview = document.getElementById('penalty_preview');
    const poPreview = document.getElementById('po_preview');
    const taxSummary = document.getElementById('tax_summary');
    const discountSummary = document.getElementById('discount_summary');
    const penaltySummary = document.getElementById('penalty_summary');
    const totalPreview = document.getElementById('total_preview');
    let invoiceItems = [];

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

    function parseExistingDescription() {
        const description = descriptionInput.value.trim();

        if (! description) {
            return [];
        }

        return description
            .split(/\r?\n/)
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const normalized = line.replace(/^\d+\.\s*/, '');
                const parts = normalized.split('|').map((part) => part.trim());
                const item = {
                    description: parts[0] || normalized,
                    quantity: '',
                    unit: 'Unit',
                    unitPrice: '',
                };

                parts.slice(1).forEach((part) => {
                    const [rawKey, ...rawValue] = part.split(':');
                    const key = (rawKey || '').trim().toLowerCase();
                    const value = rawValue.join(':').trim().replace(/RM|,/gi, '');

                    if (key === 'qty') {
                        item.quantity = value;
                    }

                    if (key === 'unit') {
                        item.unit = value || 'Unit';
                    }

                    if (key === 'unit price') {
                        item.unitPrice = value;
                    }
                });

                return item;
            });
    }

    function addInvoiceItem(item = {}) {
        invoiceItems.push({
            description: item.description || '',
            quantity: item.quantity || '',
            unit: item.unit || 'Unit',
            unitPrice: item.unitPrice || '',
        });
        renderInvoiceItems();
    }

    function removeInvoiceItem(index) {
        invoiceItems.splice(index, 1);

        if (invoiceItems.length === 0) {
            addInvoiceItem();
            return;
        }

        renderInvoiceItems();
    }

    function updateInvoiceItem(index, key, value) {
        invoiceItems[index][key] = value;
        refreshInvoiceItemTotals();
    }

    function itemAmount(item) {
        const quantity = Number.parseFloat(item.quantity);
        const unitPrice = Number.parseFloat(item.unitPrice);

        if (! Number.isFinite(quantity) || ! Number.isFinite(unitPrice)) {
            return 0;
        }

        return quantity * unitPrice;
    }

    function syncInvoiceDescriptionAndSubtotal() {
        const itemLines = invoiceItems
            .filter((item) => item.description || item.quantity || item.unitPrice)
            .map((item, index) => {
                return `${index + 1}. ${item.description || 'Item'} | Qty: ${item.quantity || 0} | Unit: ${item.unit || 'Unit'} | Unit Price: RM ${Number.parseFloat(item.unitPrice || 0).toFixed(2)} | Amount: RM ${itemAmount(item).toFixed(2)}`;
            });
        const subtotal = invoiceItems.reduce((sum, item) => sum + itemAmount(item), 0);

        descriptionInput.value = itemLines.join('\n');

        if (subtotal > 0 || invoiceItems.some((item) => item.quantity || item.unitPrice)) {
            subtotalInput.value = subtotal.toFixed(2);
        }
    }

    function refreshInvoiceItemTotals() {
        invoiceItems.forEach((item, index) => {
            const amountCell = invoiceItemsBody.querySelector(`[data-amount-index="${index}"]`);

            if (amountCell) {
                amountCell.textContent = itemAmount(item).toLocaleString('en-MY', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                });
            }
        });

        syncInvoiceDescriptionAndSubtotal();
        updateTotalPreview();
    }

    function renderInvoiceItems() {
        invoiceItemsBody.innerHTML = '';

        invoiceItems.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="fw-bold text-primary">${index + 1}</td>
                <td>
                    <input class="form-control" type="text" list="invoice_item_suggestions" value="${escapeHtml(item.description)}" placeholder="Select or type item description" data-field="description" data-index="${index}">
                </td>
                <td>
                    <input class="form-control" type="number" min="0" step="0.01" value="${escapeHtml(item.quantity)}" data-field="quantity" data-index="${index}">
                </td>
                <td>
                    <select class="form-select" data-field="unit" data-index="${index}">
                        ${['Unit', 'Set', 'Meter', 'Lot', 'Box', 'Service'].map((unit) => `<option value="${unit}" ${unit === item.unit ? 'selected' : ''}>${unit}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input class="form-control" type="number" min="0" step="0.01" value="${escapeHtml(item.unitPrice)}" data-field="unitPrice" data-index="${index}">
                </td>
                <td class="text-end fw-semibold" data-amount-index="${index}">${itemAmount(item).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                <td class="text-center">
                    <div class="d-inline-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary invoice-item-action" type="button" data-edit-index="${index}" aria-label="Edit item ${index + 1}">
                            @include('shared.dashboard-icon', ['name' => 'edit'])
                        </button>
                        <button class="btn btn-sm btn-outline-danger invoice-item-action" type="button" data-remove-index="${index}" aria-label="Delete item ${index + 1}">
                            @include('shared.dashboard-icon', ['name' => 'trash'])
                        </button>
                    </div>
                </td>
            `;
            invoiceItemsBody.appendChild(row);
        });

        refreshInvoiceItemTotals();
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
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

    invoiceItemsBody.addEventListener('input', (event) => {
        const field = event.target.dataset.field;
        const index = Number.parseInt(event.target.dataset.index, 10);

        if (field && Number.isInteger(index)) {
            updateInvoiceItem(index, field, event.target.value);
        }
    });

    invoiceItemsBody.addEventListener('change', (event) => {
        const field = event.target.dataset.field;
        const index = Number.parseInt(event.target.dataset.index, 10);

        if (field && Number.isInteger(index)) {
            updateInvoiceItem(index, field, event.target.value);
        }
    });

    invoiceItemsBody.addEventListener('click', (event) => {
        const editButton = event.target.closest('[data-edit-index]');
        const removeButton = event.target.closest('[data-remove-index]');

        if (editButton) {
            const index = Number.parseInt(editButton.dataset.editIndex, 10);
            invoiceItemsBody.querySelector(`[data-field="description"][data-index="${index}"]`)?.focus();
        }

        if (removeButton) {
            removeInvoiceItem(Number.parseInt(removeButton.dataset.removeIndex, 10));
        }
    });

    addInvoiceItemButton.addEventListener('click', () => addInvoiceItem());

    const existingItems = parseExistingDescription();
    if (existingItems.length > 0) {
        invoiceItems = existingItems;
        renderInvoiceItems();
    } else {
        addInvoiceItem();
    }
</script>
@endsection
