@extends('layouts.app')

@section('title', 'My Profile - KTM eDOIS')
@section('page-title', 'My Profile')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<div class="row g-4">
    <div class="col-xl-8">
        <section class="content-card p-4">
            <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                <span class="ktm-avatar">{{ strtoupper(substr($supplier->supplier_name, 0, 1)) }}</span>
                <div>
                    <h2 class="h5 fw-bold mb-1">{{ $supplier->supplier_name }}</h2>
                    <div class="small text-muted">{{ $supplier->vendor_number }}</div>
                </div>
            </div>

            <div class="panel-muted p-3 mb-4 small text-primary">
                Vendor profile data is retrieved from KTM procurement master data in read-only mode.
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Vendor Number</div>
                    <input
                        class="form-control readonly-field"
                        id="supplier_id"
                        name="supplier_id"
                        value="{{ $supplier->vendor_number }}"
                        readonly
                    >
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Company Name</div>
                    <input
                        class="form-control readonly-field"
                        id="supplier_name"
                        name="supplier_name"
                        value="{{ $supplier->supplier_name }}"
                        readonly
                    >
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Reference No</div>
                    <input
                        class="form-control readonly-field"
                        id="reference_no"
                        name="reference_no"
                        value="{{ $supplier->SUPPLIER_COMP_REG_NO }}"
                        readonly
                    >
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Contact Person</div>
                    <div class="readonly-field">{{ $supplier->contact_person }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Phone</div>
                    <div class="readonly-field">{{ $supplier->supplier_phone }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Email</div>
                    <div class="readonly-field">{{ $supplier->supplier_email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted mb-1">Status</div>
                    <div class="readonly-field">@include('shared.status-badge', ['status' => $supplier->supplier_status])</div>
                </div>
                <div class="col-12">
                    <div class="small text-muted mb-1">Billing Address</div>
                    <div class="readonly-field">{{ $supplier->billing_address }}</div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
