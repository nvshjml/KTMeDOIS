@extends('layouts.app')

@section('title', 'Supplier Dashboard - KTM eDOIS')
@section('page-title', 'Supplier Dashboard')
@section('page-kicker')
    Welcome back, <span class="fw-bold">{{ $supplier->supplier_name }}</span>
@endsection

@section('content')
@php
    $statCards = [
        [
            'label' => 'Submitted DOs',
            'value' => $stats['submitted_dos'],
            'note' => 'This Month',
            'trend' => '12% vs last month',
            'trendClass' => 'text-primary',
            'icon' => 'document',
            'tone' => 'blue',
        ],
        [
            'label' => 'Pending DO Review',
            'value' => $stats['pending_review'],
            'note' => 'Awaiting Approval',
            'trend' => '5% vs last month',
            'trendClass' => 'text-danger',
            'icon' => 'clock',
            'tone' => 'amber',
        ],
        [
            'label' => 'Approved DOs',
            'value' => $stats['approved_delivery_orders'],
            'note' => 'This Month',
            'trend' => '18% vs last month',
            'trendClass' => 'text-success',
            'icon' => 'check',
            'tone' => 'green',
        ],
        [
            'label' => 'Invoice Claims',
            'value' => $stats['invoice_claims'],
            'note' => 'This Month',
            'trend' => '8% vs last month',
            'trendClass' => 'text-primary',
            'icon' => 'invoice',
            'tone' => 'purple',
        ],
        [
            'label' => 'Paid Invoices',
            'value' => $stats['paid_invoices'],
            'note' => 'This Month',
            'trend' => '15% vs last month',
            'trendClass' => 'text-success',
            'icon' => 'card',
            'tone' => 'teal',
        ],
    ];
@endphp

<div class="dashboard-page d-grid gap-4">
    @unless($supplier->isActive())
        <div class="alert alert-warning mb-0">
            This supplier is inactive. You can view existing records, but Delivery Order upload is disabled.
        </div>
    @endunless

    <section class="dashboard-metrics supplier-metrics">
        @foreach($statCards as $card)
            <article class="metric-card metric-card-vertical">
                <span class="metric-icon metric-icon-{{ $card['tone'] }}">
                    @include('shared.dashboard-icon', ['name' => $card['icon']])
                </span>
                <div class="metric-label">{{ $card['label'] }}</div>
                <div class="metric-value metric-value-{{ $card['tone'] }}">{{ number_format($card['value']) }}</div>
                <div class="metric-note">{{ $card['note'] }}</div>
                <div class="metric-trend {{ $card['trendClass'] }}">
                    <span>{!! $card['trendClass'] === 'text-danger' ? '&darr;' : '&uarr;' !!}</span>
                    <span>{{ $card['trend'] }}</span>
                </div>
            </article>
        @endforeach
    </section>

    <div class="dashboard-layout supplier-dashboard-layout">
        <section class="dashboard-panel p-0 overflow-hidden">
            <div class="dashboard-panel-header">
                <div class="d-flex align-items-center gap-2">
                    @include('shared.dashboard-icon', ['name' => 'list'])
                    <h2 class="dashboard-panel-title mb-0">Delivery Order Status Tracking</h2>
                </div>
                <div class="dashboard-panel-actions">
                    <a class="btn btn-sm btn-outline-primary dashboard-action-button" href="{{ route('supplier.do.status') }}">View All</a>
                    <button class="btn btn-sm btn-outline-secondary dashboard-square-button" type="button" aria-label="Filter">
                        @include('shared.dashboard-icon', ['name' => 'filter'])
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table dashboard-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>DO Number</th>
                            <th>Invoice Number</th>
                            <th>Submission Date</th>
                            <th>Current Status</th>
                            <th>Payment Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeliveryOrders as $deliveryOrder)
                            @php
                                $invoice = $deliveryOrder->invoices->sortByDesc('created_at')->first();
                                $paymentStatus = $invoice?->status === 'Paid' ? 'Paid' : 'Unpaid';
                            @endphp
                            <tr>
                                <td>
                                    <a class="dashboard-link" target="_blank" href="{{ route('supplier.do.print', $deliveryOrder->do_id) }}">{{ $deliveryOrder->do_number }}</a>
                                </td>
                                <td>{{ $invoice?->invoice_number ?? '-' }}</td>
                                <td>{{ $deliveryOrder->created_at?->format('d M Y') ?? 'N/A' }}</td>
                                <td>@include('shared.status-badge', ['status' => $deliveryOrder->status === 'Submitted' ? 'Pending Review' : $deliveryOrder->status])</td>
                                <td>@include('shared.status-badge', ['status' => $paymentStatus])</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link dashboard-menu-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Actions">
                                            @include('shared.dashboard-icon', ['name' => 'more'])
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" target="_blank" href="{{ route('supplier.do.print', $deliveryOrder->do_id) }}">Print DO</a></li>
                                            @if($deliveryOrder->status === 'Approved')
                                                <li><a class="dropdown-item" href="{{ route('supplier.invoice.create', $deliveryOrder->do_id) }}">Submit Invoice</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted py-4 text-center">No Delivery Orders submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="dashboard-table-footer">
                <span>Showing 1 to {{ $recentDeliveryOrders->count() }} of {{ $stats['delivery_orders'] }} entries</span>
                <div class="dashboard-pagination" aria-label="Delivery order table pagination">
                    <button type="button" disabled>&laquo;</button>
                    <button type="button" disabled>&lsaquo;</button>
                    <button class="active" type="button">1</button>
                    <button type="button">2</button>
                    <button type="button">3</button>
                    <button type="button">&rsaquo;</button>
                    <button type="button">&raquo;</button>
                </div>
            </div>
        </section>

        <aside class="dashboard-side d-grid gap-4">
            <section class="dashboard-panel">
                <div class="d-flex align-items-start gap-2 mb-2">
                    @include('shared.dashboard-icon', ['name' => 'upload'])
                    <div>
                        <h2 class="dashboard-panel-title mb-1">Upload Shortcuts</h2>
                        <p class="small text-muted mb-0">Quickly upload documents related to Delivery Orders.</p>
                    </div>
                </div>
                <div class="d-grid gap-2 mt-3">
                    @if($supplier->isActive())
                        <a class="btn btn-primary dashboard-upload-button" href="{{ route('supplier.do.create') }}">
                            @include('shared.dashboard-icon', ['name' => 'upload'])
                            <span>Upload Delivery Order</span>
                        </a>
                    @else
                        <button class="btn btn-secondary dashboard-upload-button" type="button" disabled>Upload Disabled</button>
                    @endif
                    <a class="btn btn-outline-primary dashboard-upload-button" href="{{ $approvedDoId ? route('supplier.invoice.create', $approvedDoId) : route('supplier.do.status') }}">
                        @include('shared.dashboard-icon', ['name' => 'upload'])
                        <span>Upload Proof of Delivery</span>
                    </a>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel-header px-0 pt-0">
                    <div class="d-flex align-items-center gap-2">
                        @include('shared.dashboard-icon', ['name' => 'bell'])
                        <h2 class="dashboard-panel-title mb-0">Recent Notifications</h2>
                    </div>
                    <a class="dashboard-small-link" href="{{ route('supplier.notifications') }}">View All</a>
                </div>
                <div class="notification-list">
                    @forelse($recentNotifications as $notification)
                        <article class="notification-item">
                            <span class="notification-icon notification-icon-{{ $notification->type === 'invoice' ? 'green' : ($notification->type === 'payment' ? 'blue' : 'amber') }}">
                                @include('shared.dashboard-icon', ['name' => $notification->type === 'payment' ? 'money' : ($notification->type === 'invoice' ? 'check' : 'clock')])
                            </span>
                            <div class="min-w-0">
                                <div class="notification-text">{{ $notification->content }}</div>
                                <div class="notification-time">{{ $notification->created_at?->diffForHumans() }}</div>
                            </div>
                        </article>
                    @empty
                        <div class="text-muted small">No notifications yet.</div>
                    @endforelse
                </div>
                <a class="dashboard-view-all-link" href="{{ route('supplier.notifications') }}">
                    <span>View all notifications</span>
                    <span>&rarr;</span>
                </a>
            </section>
        </aside>
    </div>
</div>
@endsection
