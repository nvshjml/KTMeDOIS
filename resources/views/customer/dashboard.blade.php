@extends('layouts.app')

@section('title', 'KTM Officer Dashboard - KTM eDOIS')
@section('page-title', 'KTM Officer Dashboard')
@section('page-kicker')
    Welcome back, <span class="fw-bold text-primary">{{ auth()->user()->name ?? auth()->user()->username }}</span>
@endsection

@section('content')
@php
    $metricCards = [
        [
            'label' => 'Total DOs',
            'value' => number_format($stats['total_dos']),
            'trend' => '12.5% vs last month',
            'trendClass' => 'text-success',
            'icon' => 'document',
            'tone' => 'blue',
        ],
        [
            'label' => 'Pending Review',
            'value' => number_format($stats['pending_review']),
            'trend' => '6.3% vs last month',
            'trendClass' => 'text-danger',
            'icon' => 'clock',
            'tone' => 'amber',
        ],
        [
            'label' => 'Approved Invoices',
            'value' => number_format($stats['approved_invoices']),
            'trend' => '18.7% vs last month',
            'trendClass' => 'text-success',
            'icon' => 'check',
            'tone' => 'green',
        ],
        [
            'label' => 'Payment Updates',
            'value' => number_format($stats['payment_updates']),
            'trend' => '9.2% vs last month',
            'trendClass' => 'text-success',
            'icon' => 'money',
            'tone' => 'purple',
        ],
    ];

    $summaryItems = [
        ['label' => 'Overdue DOs', 'value' => $stats['overdue_dos'], 'note' => 'Needs officer review', 'tone' => 'blue', 'icon' => 'calendar', 'valueClass' => 'text-danger'],
        ['label' => 'Overdue Payments', 'value' => $stats['overdue_payments'], 'note' => 'Rejected payment claims', 'tone' => 'amber', 'icon' => 'invoice', 'valueClass' => 'text-danger'],
        ['label' => 'Active Suppliers', 'value' => $stats['active_customers'], 'note' => 'With active DOs', 'tone' => 'purple', 'icon' => 'users', 'valueClass' => 'text-primary'],
        ['label' => 'DOs This Month', 'value' => $stats['dos_this_month'], 'note' => '11.8% vs last month', 'tone' => 'teal', 'icon' => 'chart', 'valueClass' => 'text-primary'],
    ];
@endphp

<div class="dashboard-page d-grid gap-4">
    <section class="dashboard-metrics officer-metrics">
        @foreach($metricCards as $card)
            <article class="metric-card">
                <span class="metric-icon metric-icon-{{ $card['tone'] }}">
                    @include('shared.dashboard-icon', ['name' => $card['icon']])
                </span>
                <div class="min-w-0">
                    <div class="metric-label">{{ $card['label'] }}</div>
                    <div class="metric-value">{{ $card['value'] }}</div>
                    <div class="metric-trend {{ $card['trendClass'] }}">
                        <span>{!! $card['trendClass'] === 'text-danger' ? '&darr;' : '&uarr;' !!}</span>
                        <span>{{ $card['trend'] }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <div class="dashboard-layout">
        <div class="d-grid gap-4 min-w-0">
            <section class="dashboard-panel p-0 overflow-hidden">
                <div class="dashboard-panel-header">
                    <h2 class="dashboard-panel-title">Delivery Orders / Invoices Overview</h2>
                    <div class="dashboard-panel-actions">
                        <select class="form-select form-select-sm dashboard-filter" aria-label="Filter status">
                            <option>All Status</option>
                            <option>Pending Review</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                            <option>Paid</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary dashboard-action-button" type="button">
                            @include('shared.dashboard-icon', ['name' => 'filter'])
                            <span>Filter</span>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table dashboard-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>DO / Invoice No.</th>
                                <th>Supplier</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th class="text-end">Amount (MYR)</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dashboardRows as $row)
                                <tr>
                                    <td>
                                        <a class="dashboard-link" href="{{ $row['href'] }}">{{ $row['reference'] }}</a>
                                    </td>
                                    <td>{{ $row['customer'] }}</td>
                                    <td>{{ $row['type'] }}</td>
                                    <td>{{ $row['date']?->format('d M Y') ?? 'N/A' }}</td>
                                    <td>@include('shared.status-badge', ['status' => $row['status']])</td>
                                    <td class="text-end">{{ $row['amount'] ? number_format((float) $row['amount'], 2) : '-' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a class="btn btn-sm btn-outline-secondary dashboard-icon-button" href="{{ $row['href'] }}">
                                                @include('shared.dashboard-icon', ['name' => 'eye'])
                                                <span>View</span>
                                            </a>
                                            <a class="btn btn-sm btn-outline-primary dashboard-icon-button" href="{{ $row['href'] }}">
                                                @include('shared.dashboard-icon', ['name' => $row['action'] === 'Download' ? 'download' : 'review'])
                                                <span>{{ $row['action'] }}</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-muted py-4 text-center">No Delivery Orders or invoices are available yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="dashboard-table-footer">
                    <span>Showing 1 to {{ $dashboardRows->count() }} of {{ $stats['total_dos'] + $latestInvoices->count() }} entries</span>
                    <div class="dashboard-pagination" aria-label="Dashboard table pagination">
                        <button type="button" disabled>&lsaquo;</button>
                        <button class="active" type="button">1</button>
                        <button type="button">2</button>
                        <button type="button">3</button>
                        <button type="button">&rsaquo;</button>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel summary-strip">
                @foreach($summaryItems as $item)
                    <article class="summary-item">
                        <span class="metric-icon metric-icon-{{ $item['tone'] }}">
                            @include('shared.dashboard-icon', ['name' => $item['icon']])
                        </span>
                        <div>
                            <div class="summary-label">{{ $item['label'] }}</div>
                            <div class="summary-value {{ $item['valueClass'] }}">{{ number_format($item['value']) }}</div>
                            <div class="summary-note">{{ $item['note'] }}</div>
                        </div>
                    </article>
                @endforeach
            </section>
        </div>

        <aside class="dashboard-side d-grid gap-4">
            <section class="dashboard-panel">
                <div class="dashboard-panel-header px-0 pt-0">
                    <h2 class="dashboard-panel-title">Notifications</h2>
                    <a class="dashboard-small-link" href="{{ route('customer.notifications.index') }}">View All</a>
                </div>
                <div class="notification-list">
                    @forelse($notifications as $notification)
                        <article class="notification-item">
                            <span class="notification-icon notification-icon-{{ $notification->type === 'invoice' ? 'green' : ($notification->type === 'payment' ? 'blue' : 'amber') }}">
                                @include('shared.dashboard-icon', ['name' => $notification->type === 'payment' ? 'money' : ($notification->type === 'invoice' ? 'check' : 'clock')])
                            </span>
                            <div class="min-w-0">
                                <div class="notification-text">{{ $notification->content }}</div>
                                <div class="notification-time">{{ $notification->created_at?->diffForHumans() }}</div>
                            </div>
                            @if($notification->status === 'unread')
                                <span class="unread-dot"></span>
                            @endif
                        </article>
                    @empty
                        <div class="text-muted small">No notifications yet.</div>
                    @endforelse
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="dashboard-panel-header px-0 pt-0">
                    <h2 class="dashboard-panel-title">Recent Activity</h2>
                    <a class="dashboard-small-link" href="{{ route('customer.audit-logs.index') }}">View All</a>
                </div>
                <div class="activity-list">
                    @forelse($recentActivity as $activity)
                        <article class="activity-item">
                            <span class="activity-dot"></span>
                            <div>
                                <div class="notification-text">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</div>
                                <div class="notification-time">{{ $activity->timestamp?->format('M d, Y h:i A') ?? $activity->created_at?->format('M d, Y h:i A') }}</div>
                            </div>
                        </article>
                    @empty
                        <div class="text-muted small">No activity has been recorded yet.</div>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
