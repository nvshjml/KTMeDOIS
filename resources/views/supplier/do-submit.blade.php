@extends('layouts.app')

@section('title', 'Submit DO - KTM eDOIS')
@section('page-title', 'Submit DO')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<form method="POST" action="{{ route('supplier.do.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4 align-items-start">
        <div class="col-xxl-9 col-xl-10">
            <section class="content-card p-4">
                <h2 class="h5 fw-bold mb-1">Submit Delivery Order</h2>
                <p class="text-muted small mb-4">Enter your DO details and upload the supporting documents. The KTMeDOIS delivery order preview will be generated after submission.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="do_number">DO Number *</label>
                        <input class="form-control" id="do_number" name="do_number" value="{{ old('do_number') }}" placeholder="e.g. DO-2026-0099" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="order_date">Order Date</label>
                        <input class="form-control" id="order_date" name="order_date" type="date" value="{{ old('order_date', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="invoice_reference">Invoice Reference No.</label>
                        <input class="form-control" id="invoice_reference" name="invoice_reference" value="{{ old('invoice_reference') }}" placeholder="e.g. INV-REF-2026-001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="po_number">Customer PO Number *</label>
                        <input class="form-control" id="po_number" name="po_number" value="{{ old('po_number') }}" placeholder="e.g. PO-2026-0099" required>
                        <div class="small text-muted mt-1">System validates this against KTM procurement records.</div>
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="project_reference">KTM Project Reference</label>
                        <input class="form-control" id="project_reference" name="project_reference" value="{{ old('project_reference') }}" placeholder="e.g. KTM Komuter Track Renewal Phase 3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="shipping_address">Shipping Address</label>
                        <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" placeholder="KTM receiving location">{{ old('shipping_address') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="invoice_address">Invoice Address</label>
                        <textarea class="form-control" id="invoice_address" name="invoice_address" rows="3" placeholder="KTM billing location">{{ old('invoice_address') }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Delivery Items</label>
                        <span class="small text-muted">Item no., description, quantity</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 20%">Item No.</th>
                                    <th>Description</th>
                                    <th style="width: 18%">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($index = 0; $index < 4; $index++)
                                    <tr>
                                        <td><input class="form-control" name="items[{{ $index }}][item_no]" value="{{ old('items.'.$index.'.item_no') }}" placeholder="{{ 1001 + $index }}"></td>
                                        <td><input class="form-control" name="items[{{ $index }}][description]" value="{{ old('items.'.$index.'.description') }}" placeholder="Item description"></td>
                                        <td><input class="form-control" name="items[{{ $index }}][quantity]" type="number" min="0" step="1" value="{{ old('items.'.$index.'.quantity') }}" placeholder="0"></td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label" for="delivery_date">Date of Delivery</label>
                        <input class="form-control" id="delivery_date" name="delivery_date" type="date" value="{{ old('delivery_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="delivery_time">Time of Delivery</label>
                        <input class="form-control" id="delivery_time" name="delivery_time" type="time" value="{{ old('delivery_time') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Delivery notes, recipient remarks, or special handling notes">{{ old('remarks') }}</textarea>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="upload-panel h-100 text-center">
                            <label class="form-label" for="do_file">DO Document (PDF / Image) *</label>
                            <input class="form-control" id="do_file" name="do_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="small text-muted mt-2">PDF, JPG, PNG - Max 5MB</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="upload-panel h-100 text-center">
                            <label class="form-label" for="proof_file">Proof of Delivery *</label>
                            <input class="form-control" id="proof_file" name="proof_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="small text-muted mt-2">Must include recipient signature or stamp</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button class="btn btn-primary px-5" type="submit">Submit DO</button>
                    <a class="btn btn-outline-primary px-5" href="{{ route('supplier.do.status') }}">View Status</a>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection
