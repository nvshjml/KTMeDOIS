@extends('layouts.app')

@section('title', 'Submit Delivery Order - KTM eDOIS')

@section('content')
<div class="d-flex flex-column gap-4">
    <section class="content-card p-4 p-xl-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker mb-2">Delivery Order Submission</div>
                <h1 class="page-title h2 mb-3">Create a new Delivery Order</h1>
                <p class="text-muted mb-0">
                    Upload the DO and proof of delivery documents for KTM review. Active vendor information is locked from the master registry.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="panel-muted p-3">
                    <div class="small text-muted fw-bold text-uppercase mb-2">Verified Vendor</div>
                    <div class="fw-bold">{{ $supplier->supplier_name }}</div>
                    <div class="small text-muted">{{ $supplier->vendor_number }}</div>
                    <div class="mt-2">@include('shared.status-badge', ['status' => $supplier->supplier_status])</div>
                </div>
            </div>
        </div>
    </section>

    <form method="POST" action="{{ route('supplier.do.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <div class="col-xl-4">
                <section class="content-card p-4 h-100">
                    <div class="page-kicker mb-2">Vendor Registry</div>
                    <h2 class="h5 form-section-title mb-3">Read-only master data</h2>

                    <div class="d-grid gap-3">
                        <div>
                            <div class="small text-muted mb-1">Vendor ID</div>
                            <div class="readonly-field fw-bold">{{ $supplier->vendor_number }}</div>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Company Name</div>
                            <div class="readonly-field">{{ $supplier->supplier_name }}</div>
                        </div>
                        <div>
                            <div class="small text-muted mb-1">Contact Email</div>
                            <div class="readonly-field">{{ $supplier->supplier_email }}</div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xl-8">
                <section class="content-card p-4">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                        <div>
                            <div class="page-kicker mb-2">DO Details</div>
                            <h2 class="h5 form-section-title mb-0">Purchase order and delivery files</h2>
                        </div>
                        <span class="badge text-bg-light align-self-start">PDF, JPG, JPEG, PNG up to 5MB</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="do_number">Delivery Order Number</label>
                            <input class="form-control" id="do_number" name="do_number" value="{{ old('do_number') }}" placeholder="Example: DO-2026-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="po_number">Purchase Order Number</label>
                            <input class="form-control" id="po_number" name="po_number" value="{{ old('po_number') }}" placeholder="Example: PO-KTM-001" required>
                        </div>

                        <div class="col-md-6">
                            <div class="upload-panel h-100">
                                <label class="form-label" for="do_file">Delivery Order File</label>
                                <input class="form-control" id="do_file" name="do_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="small text-muted mt-2">Upload the official DO document for officer verification.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="upload-panel h-100">
                                <label class="form-label" for="proof_file">Proof of Delivery</label>
                                <input class="form-control" id="proof_file" name="proof_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                <div class="small text-muted mt-2">Attach delivery slip, acknowledgement receipt, or signed proof.</div>
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
                                <div class="fw-bold">KTM Review</div>
                            </div>
                            <div class="col-md-4">
                                <div class="small text-muted mb-1">Audit Trail</div>
                                <div class="fw-bold">Recorded on submit</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button class="btn btn-primary px-4" type="submit">Submit Delivery Order</button>
                        <a class="btn btn-outline-primary px-4" href="{{ route('supplier.do.status') }}">View DO Status</a>
                    </div>
                </section>
            </div>
        </div>
    </form>
</div>
@endsection
