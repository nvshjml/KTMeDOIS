@extends('layouts.app')

@section('title', 'Customer Dashboard - KTM eDOIS')

@section('content')
@php
    $statCards = [
        ['label' => 'Registered Vendors', 'value' => $stats['suppliers'], 'note' => 'Synced from vendor master'],
        ['label' => 'DOs In Review', 'value' => $stats['submitted_dos'], 'note' => 'Submitted and under review'],
        ['label' => 'Approved DOs', 'value' => $stats['approved_dos'], 'note' => 'Ready for invoice claims'],
        ['label' => 'Open Invoices', 'value' => $stats['open_invoices'], 'note' => 'Finance action required'],
        ['label' => 'Paid Invoices', 'value' => $stats['paid_invoices'], 'note' => 'Completed claims'],
        ['label' => 'Unread Notices', 'value' => $stats['unread_notifications'], 'note' => 'New system alerts'],
    ];

    $modules = [
        ['number' => '1.1', 'title' => 'Vendor Registry Integration', 'body' => 'Active KTM vendors are validated from master data before they submit DOs or invoices.'],
        ['number' => '1.2', 'title' => 'Delivery Order Submission', 'body' => 'Digital DO uploads support PDF or image files with PO reference validation and status tracking.'],
        ['number' => '1.3', 'title' => 'Invoice Submission & Claim', 'body' => 'Approved DOs can be converted into invoice claims with tax, discount, and total calculations.'],
        ['number' => '1.4', 'title' => 'Internal Review Workflow', 'body' => 'Officers review, approve, reject, notify, and audit every claim movement in one place.'],
    ];

    $workflow = [
        ['title' => 'Vendor Submit', 'text' => 'Supplier uploads DO and proof documents.'],
        ['title' => 'KTM Review', 'text' => 'Authorised officer checks PO and delivery details.'],
        ['title' => 'Finance Process', 'text' => 'Invoice claim moves through finance review.'],
        ['title' => 'Audit Trail', 'text' => 'Every decision is logged for compliance.'],
    ];
@endphp

<div class="d-flex flex-column gap-4">
    <section class="content-card p-4 p-xl-5">
        <div class="row g-4 align-items-center">
            <div class="col-lg-8">
                <div class="page-kicker mb-2">Internal Digital Platform</div>
                <h1 class="page-title display-6 mb-3">KTM eDOIS Dashboard</h1>
                <p class="text-muted fs-6 mb-0">
                    Streamline Delivery Order and Invoice submission, verification, approval, and audit tracking for vendors registered under KTM procurement.
                </p>
            </div>
            <div class="col-lg-4">
                <div class="panel-muted p-3 d-flex align-items-center gap-3">
                    <img class="ktm-logo" src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
                    <div>
                        <div class="fw-bold text-uppercase small text-muted">Workspace</div>
                        <div class="fw-bold fs-5">Customer Review</div>
                        <div class="small text-muted">Real-time DO and invoice tracking</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3">
        @foreach($statCards as $card)
            <div class="col-md-6 col-xl-2">
                <section class="content-card stat-card p-3 h-100">
                    <div class="text-muted small fw-semibold mb-3">{{ $card['label'] }}</div>
                    <div class="stat-value">{{ $card['value'] }}</div>
                    <div class="small text-muted mt-3">{{ $card['note'] }}</div>
                </section>
            </div>
        @endforeach
    </div>

    <section class="content-card p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
            <div>
                <div class="page-kicker mb-1">Functional Modules</div>
                <h2 class="h4 page-title mb-0">One workflow from vendor validation to paid claim</h2>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-primary" href="{{ route('customer.delivery-orders.index') }}">Review DOs</a>
                <a class="btn btn-primary" href="{{ route('customer.invoices.index') }}">Review Invoices</a>
            </div>
        </div>

        <div class="row g-3">
            @foreach($modules as $module)
                <div class="col-md-6 col-xl-3">
                    <article class="panel-muted module-card p-3 h-100">
                        <span class="module-number mb-3">{{ $module['number'] }}</span>
                        <h3 class="h6 fw-bold mb-2">{{ $module['title'] }}</h3>
                        <p class="small text-muted mb-0">{{ $module['body'] }}</p>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="content-card p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
            <div>
                <div class="page-kicker mb-1">Review Pipeline</div>
                <h2 class="h4 page-title mb-0">Status movement</h2>
            </div>
            <span class="small text-muted">Submitted -> Under Review -> Approved / Rejected -> Finance Review -> Paid</span>
        </div>

        <div class="workflow-strip">
            @foreach($workflow as $step)
                <div class="workflow-step">
                    <strong class="d-block mb-2">{{ $step['title'] }}</strong>
                    <span class="small text-muted">{{ $step['text'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    <section class="content-card p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-3">
            <div>
                <div class="page-kicker mb-1">Vendor Registry Integration</div>
                <h2 class="h4 page-title mb-0">Supplier master snapshot</h2>
            </div>
            <span class="small text-muted">Read-only vendor information from the master registry</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Vendor ID</th>
                        <th>Company</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="fw-bold text-primary">{{ $supplier->vendor_number }}</td>
                            <td>{{ $supplier->supplier_name }}</td>
                            <td>{{ $supplier->contact_person }}</td>
                            <td>{{ $supplier->supplier_email }}</td>
                            <td>@include('shared.status-badge', ['status' => $supplier->supplier_status])</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted py-4">No supplier records are available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-xl-6">
            <section class="content-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="page-kicker mb-1">Delivery Orders</div>
                        <h2 class="h4 page-title mb-0">Latest submissions</h2>
                    </div>
                    <a href="{{ route('customer.delivery-orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>Supplier</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestDeliveryOrders as $deliveryOrder)
                                <tr>
                                    <td><a class="fw-bold text-decoration-none" href="{{ route('customer.delivery-orders.show', $deliveryOrder->do_id) }}">{{ $deliveryOrder->do_number }}</a></td>
                                    <td>{{ $deliveryOrder->supplier->supplier_name }}</td>
                                    <td>@include('shared.status-badge', ['status' => $deliveryOrder->status])</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted py-4">No Delivery Orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-6">
            <section class="content-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="page-kicker mb-1">Invoices</div>
                        <h2 class="h4 page-title mb-0">Claims awaiting action</h2>
                    </div>
                    <a href="{{ route('customer.invoices.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestInvoices as $invoice)
                                <tr>
                                    <td><a class="fw-bold text-decoration-none" href="{{ route('customer.invoices.show', $invoice->invoice_id) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->deliveryOrder->supplier->supplier_name }}</td>
                                    <td>RM {{ number_format($invoice->total, 2) }}</td>
                                    <td>@include('shared.status-badge', ['status' => $invoice->status])</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted py-4">No invoices yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
