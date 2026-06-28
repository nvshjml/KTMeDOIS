@extends('layouts.app')

@section('title', 'My Delivery Orders - KTM eDOIS')
@section('page-title', 'My Delivery Orders')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<section class="content-card do-status-page">
    <div class="do-status-header">
        <div>
            <h2 class="h5 fw-bold mb-1">My Delivery Orders ({{ $deliveryOrders->total() }})</h2>
            <p class="text-muted small mb-0">Track submitted Delivery Orders, review progress, and access uploaded documents.</p>
        </div>
        <div class="do-status-toolbar">
            <form class="do-status-search" method="GET">
                <span class="do-status-search-icon">@include('shared.dashboard-icon', ['name' => 'search'])</span>
                <input class="form-control" name="search" type="search" value="{{ request('search') }}" placeholder="Search DOs..." aria-label="Search Delivery Orders">
                <button class="btn btn-primary" type="submit">Find</button>
                @if(request('search'))
                    <a class="btn btn-outline-secondary" href="{{ route('supplier.do.status') }}">Reset</a>
                @endif
            </form>
            @if($supplier->isActive())
                <a class="btn btn-primary do-new-button" href="{{ route('supplier.do.create') }}">
                    @include('shared.dashboard-icon', ['name' => 'plus'])
                    <span>New DO</span>
                </a>
            @else
                <button class="btn btn-secondary do-new-button" type="button" disabled>Upload Disabled</button>
            @endif
        </div>
    </div>

    @unless($supplier->isActive())
        <div class="alert alert-warning m-4 mb-0">
            This supplier is inactive. You can view existing records, but Delivery Order upload is disabled.
        </div>
    @endunless

    <div class="do-status-list">
        @forelse($deliveryOrders as $deliveryOrder)
            @php
                $displayStatus = $deliveryOrder->status === 'Under Review' ? 'Pending Approval' : $deliveryOrder->status;
                $canSubmitInvoice = $deliveryOrder->status === 'Approved';
            @endphp
            <article class="do-status-card">
                <div class="do-status-card-header">
                    <div class="do-record-heading">
                        <span class="do-record-icon">@include('shared.dashboard-icon', ['name' => 'delivery'])</span>
                        <div>
                            <div class="fw-bold">{{ $deliveryOrder->do_number }}</div>
                            <div class="do-record-meta">
                                <span>{{ $deliveryOrder->po_number }}</span>
                                <span>{{ $deliveryOrder->created_at?->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="do-status-actions">
                        @include('shared.status-badge', ['status' => $displayStatus])
                        <a class="btn btn-sm {{ $canSubmitInvoice ? 'btn-outline-primary' : 'btn-light disabled' }}" href="{{ route('supplier.invoice.create', $deliveryOrder->do_id) }}"
                           @if(! $canSubmitInvoice) aria-disabled="true" tabindex="-1" onclick="return false;" @endif>
                            @include('shared.dashboard-icon', ['name' => 'invoice'])
                            <span>Submit Invoice</span>
                        </a>
                        @if($deliveryOrder->status === 'Draft' && $supplier->isActive())
                            <form method="POST" action="{{ route('supplier.do.submit-draft', $deliveryOrder->do_id) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" type="submit">Submit Draft</button>
                            </form>
                        @endif
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'do']) }}">
                            @include('shared.dashboard-icon', ['name' => 'download'])
                            <span>Download DO</span>
                        </a>
                    </div>
                </div>

                <div class="do-stepper-panel">
                    @if($deliveryOrder->status === 'Rejected')
                        @include('shared.status-stepper', ['type' => 'do', 'status' => 'Rejected'])
                        <div class="rejection-note mt-3">Rejection reason: {{ $deliveryOrder->reason ?: 'Please review the submitted document and resubmit with complete proof of delivery.' }}</div>
                    @elseif($deliveryOrder->status === 'Draft')
                        <div class="alert alert-light border mb-0">
                            Draft saved. Submit the Delivery Order when the uploaded documents are ready for KTM review.
                        </div>
                    @else
                        @include('shared.status-stepper', ['type' => 'do', 'status' => $deliveryOrder->status])
                    @endif
                </div>

                <details class="submitted-document do-document-details" @if((int) session('submitted_do_id') === $deliveryOrder->do_id) open @endif>
                    <summary>
                        <span class="do-document-summary-icon">@include('shared.dashboard-icon', ['name' => 'document'])</span>
                        <span>Uploaded Documents</span>
                    </summary>
                    <div class="do-document-grid">
                        <div class="do-document-tile">
                            <span class="do-document-icon">@include('shared.dashboard-icon', ['name' => 'document'])</span>
                            <div class="do-document-copy">
                                <div class="fw-semibold">Delivery Order File</div>
                                <div class="small text-muted text-break">{{ basename((string) $deliveryOrder->do_link) }}</div>
                            </div>
                            <a class="btn btn-sm btn-dark" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'do']) }}">Download DO</a>
                        </div>
                        <div class="do-document-tile">
                            <span class="do-document-icon do-document-icon-proof">@include('shared.dashboard-icon', ['name' => 'upload'])</span>
                            <div class="do-document-copy">
                                <div class="fw-semibold">Proof of Delivery</div>
                                <div class="small text-muted text-break">{{ basename((string) $deliveryOrder->proof_link) }}</div>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('supplier.do.download', [$deliveryOrder->do_id, 'proof']) }}">Download Proof</a>
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
