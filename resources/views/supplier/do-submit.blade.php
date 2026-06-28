@extends('layouts.app')

@section('title', 'Submit DO - KTM eDOIS')
@section('page-title', 'Submit DO')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
@include('shared.back-button', ['href' => route('supplier.do.status'), 'label' => 'Back to Delivery Orders'])

<form method="POST" action="{{ route('supplier.do.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-4 align-items-start">
        <div class="col-xxl-8 col-xl-9">
            <section class="content-card p-4">
                <h2 class="h5 fw-bold mb-1">Delivery Order Creation</h2>
                <p class="text-muted small mb-4">Upload the Delivery Order file and proof of delivery. The uploaded DO document should already contain the order details.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="po_number">PO Number *</label>
                        <input class="form-control" id="po_number" name="po_number" value="{{ old('po_number') }}" placeholder="e.g. PO-2026-0099" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cust_id">Admin *</label>
                        <select class="form-select" id="cust_id" name="cust_id" required>
                            <option value="">Select admin</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->cust_id }}" @selected((string) old('cust_id') === (string) $customer->cust_id)>
                                    {{ $customer->display_name ?? $customer->username }} ({{ $customer->user_email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="upload-panel h-100 text-center">
                            <label class="form-label" for="do_file">Delivery Order File *</label>
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
                    <button class="btn btn-outline-primary px-5" type="submit" name="action" value="draft">Save as Draft</button>
                    <button class="btn btn-primary px-5" type="submit" name="action" value="submit">Submit</button>
                    <a class="btn btn-outline-primary px-5" href="{{ route('supplier.do.status') }}">View Status</a>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection
