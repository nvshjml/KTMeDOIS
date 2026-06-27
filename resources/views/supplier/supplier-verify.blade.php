@extends('layouts.app')

@section('title', 'Supplier Verification - KTMeDOIS')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="content-card p-4">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="ktm-mark">K</span>
                    <div>
                        <h1 class="h4 mb-0">Supplier Verification</h1>
                        <div class="text-muted small">External suppliers verify against mirrored master data</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('supplier.verify.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="vendor_number">Vendor Number</label>
                    <input class="form-control" id="vendor_number" name="vendor_number" value="{{ old('vendor_number', 'V001') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="supplier_email">Supplier Email</label>
                    <input class="form-control" id="supplier_email" name="supplier_email" type="email" value="{{ old('supplier_email', 'supplier1@test.com') }}" required>
                </div>

                <button class="btn btn-warning w-100" type="submit">Verify Supplier</button>
            </form>

            <div class="border-top mt-4 pt-3 small text-muted">
                Customers should use <a href="{{ route('login') }}">Customer Login</a>.
            </div>
        </div>
    </div>
</div>
@endsection
