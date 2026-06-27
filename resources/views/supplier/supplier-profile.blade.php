@extends('layouts.app')

@section('title', 'Supplier Profile - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-4">
    <div>
        <h1 class="h3 mb-1">{{ $supplier->supplier_name }}</h1>
        <p class="text-muted mb-0">Verified external supplier · {{ $supplier->vendor_number }}</p>
    </div>

    <div class="row g-3">
        @foreach([
            'Delivery Orders' => $stats['delivery_orders'],
            'Approved DOs' => $stats['approved_delivery_orders'],
            'Invoices' => $stats['invoices'],
            'Unread Notices' => $stats['unread_notifications'],
        ] as $label => $value)
            <div class="col-6 col-lg-3">
                <div class="content-card p-3 h-100">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-3 fw-bold">{{ $value }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Supplier Master Details</h2>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Billing Address</dt>
                    <dd class="col-sm-8">{{ $supplier->billing_address }}</dd>
                    <dt class="col-sm-4">Contact Person</dt>
                    <dd class="col-sm-8">{{ $supplier->contact_person }}</dd>
                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $supplier->supplier_phone }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $supplier->supplier_email }}</dd>
                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">@include('shared.status-badge', ['status' => $supplier->supplier_status])</dd>
                </dl>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="content-card p-3 h-100">
                <h2 class="h5">Recent Notifications</h2>
                <div class="list-group list-group-flush">
                    @forelse($notifications as $notification)
                        <div class="list-group-item px-0">
                            <div class="small text-muted">{{ str_replace('_', ' ', $notification->type) }}</div>
                            <div>{{ $notification->content }}</div>
                        </div>
                    @empty
                        <div class="text-muted">No supplier notifications yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
