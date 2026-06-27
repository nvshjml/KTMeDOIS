@extends('layouts.app')

@section('title', 'My Invoices - KTM eDOIS')
@section('page-title', 'My Invoices')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<section class="content-card">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 p-4 border-bottom">
        <h2 class="h5 fw-bold mb-0">My Invoices &amp; Claims ({{ $invoices->total() }})</h2>
        <input class="form-control" style="max-width:260px" type="search" placeholder="Search invoices..." aria-label="Search invoices">
    </div>

    <div class="d-grid">
        @forelse($invoices as $invoice)
            <article class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                        <div class="small text-muted">{{ $invoice->deliveryOrder->do_number }} &middot; RM {{ number_format($invoice->total, 2) }}</div>
                    </div>
                    @include('shared.status-badge', ['status' => $invoice->status])
                </div>

                @include('shared.status-stepper', ['type' => 'invoice', 'status' => $invoice->status])

                @if($invoice->reason)
                    <div class="rejection-note mt-3">{{ $invoice->reason }}</div>
                @endif
            </article>
        @empty
            <div class="p-4 text-muted">No invoices submitted yet.</div>
        @endforelse
    </div>
</section>

<div class="mt-3">
    {{ $invoices->links() }}
</div>
@endsection
