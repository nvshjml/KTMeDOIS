@extends('layouts.app')

@section('title', 'Overview - KTM eDOIS')
@section('page-title', 'Overview')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
@php
    $deliveryOrders = $supplier->deliveryOrders->sortByDesc('created_at')->values();
    $recentDeliveryOrders = $deliveryOrders->take(3);
    $recentInvoices = $deliveryOrders
        ->flatMap(fn ($deliveryOrder) => $deliveryOrder->invoices)
        ->sortByDesc('created_at')
        ->values()
        ->take(3);

    $statCards = [
        ['label' => 'DOs Submitted', 'value' => $stats['delivery_orders']],
        ['label' => 'DOs Approved', 'value' => $stats['approved_delivery_orders']],
        ['label' => 'Invoices Submitted', 'value' => $stats['invoices']],
        ['label' => 'Invoices Paid', 'value' => $recentInvoices->where('status', 'Paid')->count()],
    ];
@endphp

<div class="d-flex flex-column gap-4">
    <section class="content-card p-3 p-lg-4" style="background:#223f96;color:#fff;">
        <div class="d-flex align-items-start gap-3">
            <span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:22px;height:22px;border:1px solid rgba(255,255,255,.55);">i</span>
            <div>
                <div class="fw-bold mb-1">Usability NFR Active</div>
                <div class="small">Intuitive interface for non-technical users - Mobile-friendly responsive design - Clear error messages with guidance - Accessibility features enabled</div>
            </div>
        </div>
    </section>

    <div class="row g-3">
        @foreach($statCards as $card)
            <div class="col-md-6 col-xl-3">
                <section class="content-card stat-card p-4 h-100">
                    <div class="stat-value mb-2">{{ $card['value'] }}</div>
                    <div class="text-muted">{{ $card['label'] }}</div>
                </section>
            </div>
        @endforeach
    </div>

    <section class="content-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold mb-0">Recent Delivery Orders</h2>
            <a class="small fw-bold text-decoration-none" href="{{ route('supplier.do.status') }}">View all &rsaquo;</a>
        </div>

        <div class="d-grid gap-4">
            @forelse($recentDeliveryOrders as $deliveryOrder)
                <article>
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <div class="fw-bold">{{ $deliveryOrder->do_number }}</div>
                            <div class="small text-muted">{{ $deliveryOrder->po_number }} &middot; {{ $deliveryOrder->created_at?->format('Y-m-d H:i') }}</div>
                        </div>
                        @include('shared.status-badge', ['status' => $deliveryOrder->status === 'Under Review' ? 'Pending Approval' : $deliveryOrder->status])
                    </div>

                    @if($deliveryOrder->status === 'Rejected' && $deliveryOrder->reason)
                        <div class="rejection-note mb-3">Rejection reason: {{ $deliveryOrder->reason }}</div>
                    @else
                        @include('shared.status-stepper', ['type' => 'do', 'status' => $deliveryOrder->status])
                    @endif
                </article>
            @empty
                <div class="text-muted py-2">No Delivery Orders submitted yet.</div>
            @endforelse
        </div>
    </section>

    <section class="content-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold mb-0">Recent Invoices &amp; Claims</h2>
            <a class="small fw-bold text-decoration-none" href="{{ route('supplier.invoice.status') }}">View all &rsaquo;</a>
        </div>

        <div class="d-grid gap-4">
            @forelse($recentInvoices as $invoice)
                <article>
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                            <div class="small text-muted">{{ $invoice->deliveryOrder->do_number }} &middot; RM {{ number_format($invoice->total, 2) }}</div>
                        </div>
                        @include('shared.status-badge', ['status' => $invoice->status])
                    </div>

                    @include('shared.status-stepper', ['type' => 'invoice', 'status' => $invoice->status])
                </article>
            @empty
                <div class="text-muted py-2">No invoices submitted yet.</div>
            @endforelse
        </div>
    </section>

</div>
@endsection
