@extends('layouts.app')

@section('title', 'My Delivery Orders - KTM eDOIS')
@section('page-title', 'My Delivery Orders')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<section class="content-card">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 p-4 border-bottom">
        <h2 class="h5 fw-bold mb-0">My Delivery Orders ({{ $deliveryOrders->total() }})</h2>
        <div class="d-flex flex-column flex-sm-row gap-2">
            <input class="form-control" type="search" placeholder="Search DOs..." aria-label="Search Delivery Orders">
            @if($supplier->isActive())
                <a class="btn btn-primary px-4" href="{{ route('supplier.do.create') }}">+ New DO</a>
            @else
                <button class="btn btn-secondary px-4" type="button" disabled>Upload Disabled</button>
            @endif
        </div>
    </div>

    @unless($supplier->isActive())
        <div class="alert alert-warning m-4 mb-0">
            This supplier is inactive. You can view existing records, but Delivery Order upload is disabled.
        </div>
    @endunless

    <div class="d-grid">
        @forelse($deliveryOrders as $deliveryOrder)
            <article class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="fw-bold">{{ $deliveryOrder->do_number }}</div>
                        <div class="small text-muted">{{ $deliveryOrder->po_number }} &middot; {{ $deliveryOrder->created_at?->format('Y-m-d H:i') }}</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @include('shared.status-badge', ['status' => $deliveryOrder->status === 'Under Review' ? 'Pending Approval' : $deliveryOrder->status])
                        <a class="small text-decoration-none" href="{{ route('supplier.invoice.create', $deliveryOrder->do_id) }}"
                           @if($deliveryOrder->status !== 'Approved') aria-disabled="true" onclick="return false;" style="pointer-events:none;opacity:.45" @endif>
                            Submit Invoice
                        </a>
                        @if($deliveryOrder->status === 'Draft' && $supplier->isActive())
                            <form method="POST" action="{{ route('supplier.do.submit-draft', $deliveryOrder->do_id) }}">
                                @csrf
                                <button class="btn btn-link btn-sm p-0 text-decoration-none" type="submit">Submit Draft</button>
                            </form>
                        @endif
                        <a class="small text-decoration-none" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'do']) }}">
                            Download DO
                        </a>
                    </div>
                </div>

                @if($deliveryOrder->status === 'Rejected')
                    @include('shared.status-stepper', ['type' => 'do', 'status' => 'Rejected'])
                    <div class="rejection-note mt-3">Rejection reason: {{ $deliveryOrder->reason ?: 'Please review the submitted document and resubmit with complete proof of delivery.' }}</div>
                @elseif($deliveryOrder->status === 'Draft')
                    <div class="alert alert-light border mt-3 mb-0">
                        Draft saved. Submit the Delivery Order when the uploaded documents are ready for KTM review.
                    </div>
                @else
                    @include('shared.status-stepper', ['type' => 'do', 'status' => $deliveryOrder->status])
                @endif

                <details class="submitted-document mt-3" @if((int) session('submitted_do_id') === $deliveryOrder->do_id) open @endif>
                    <summary class="fw-bold text-primary">Uploaded Documents</summary>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold">Delivery Order File</div>
                                <div class="small text-muted text-break mb-3">{{ basename((string) $deliveryOrder->do_link) }}</div>
                                <a class="btn btn-sm btn-dark" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'do']) }}">Download DO</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <div class="fw-semibold">Proof of Delivery</div>
                                <div class="small text-muted text-break mb-3">{{ basename((string) $deliveryOrder->proof_link) }}</div>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'proof']) }}">Download Proof</a>
                            </div>
                        </div>
                    </div>
                </details>
            </article>
        @empty
            <div class="p-4 text-muted">No Delivery Orders submitted yet.</div>
        @endforelse
    </div>
</section>

<div class="mt-3">
    {{ $deliveryOrders->links() }}
</div>
@endsection
