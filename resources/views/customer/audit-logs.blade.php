@extends('layouts.app')

@section('title', 'Audit Logs - KTMeDOIS')
@section('page-title', 'Audit Logs')
@section('page-kicker', 'KTM eDOIS - Document Audit Trail')

@section('content')
@php
    $exportFilters = request()->only(['search', 'record_type', 'action', 'start_date', 'end_date']);
    $recordTypes = [
        'delivery_orders' => 'Delivery Orders',
        'invoices' => 'Invoices',
    ];
@endphp

<div class="audit-report-page page-stack">
    <section class="content-card audit-report-shell page-filter-card">
        <div class="audit-report-header">
            <div>
                <p class="audit-report-kicker mb-1">Audit Log Report</p>
                <h1 class="audit-report-title mb-1">Complete audit trail</h1>
                <p class="text-muted mb-0">Search Delivery Order and Invoice audit activity by number, action, officer, or supplier.</p>
            </div>
            <div class="audit-report-actions">
                <span class="audit-result-count">{{ number_format($filteredCount) }} records</span>
                @if($activeFilters > 0)
                    <span class="audit-filter-count">{{ $activeFilters }} filters active</span>
                @endif
                <a class="btn btn-dark audit-export-button" href="{{ route('admin.audit-logs.export', $exportFilters) }}">
                    @include('shared.dashboard-icon', ['name' => 'download'])
                    Export
                </a>
            </div>
        </div>

        <form class="audit-filter-grid" method="GET">
            <div class="audit-search-field">
                <label class="form-label" for="search">Search</label>
                <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="DO, invoice, action, admin, supplier">
            </div>
            <div>
                <label class="form-label" for="record_type">Record Type</label>
                <select class="form-select" id="record_type" name="record_type">
                    <option value="">All records</option>
                    @foreach($recordTypes as $value => $label)
                        <option value="{{ $value }}" @selected(request('record_type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="action">Action</label>
                <select class="form-select" id="action" name="action">
                    <option value="">All actions</option>
                    @foreach($actionOptions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ \Illuminate\Support\Str::headline($action) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" for="start_date">Start Date</label>
                <input class="form-control" id="start_date" name="start_date" type="date" value="{{ request('start_date') }}">
            </div>
            <div>
                <label class="form-label" for="end_date">End Date</label>
                <input class="form-control" id="end_date" name="end_date" type="date" value="{{ request('end_date') }}">
            </div>
            <div class="audit-filter-actions">
                <button class="btn btn-primary" type="submit">Find</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.audit-logs.index') }}">Reset</a>
            </div>
        </form>
    </section>

    <section class="content-card audit-log-report page-table-card">
        <div class="audit-section-heading">
            <div>
                <h2 class="mb-1">Audit Log Report</h2>
                <p class="text-muted mb-0">Grouped by Delivery Order or Invoice so related events stay together.</p>
            </div>
            <span>
                Showing {{ $auditLogs->firstItem() ?? 0 }} to {{ $auditLogs->lastItem() ?? 0 }} of {{ number_format($filteredCount) }}
            </span>
        </div>

        <div class="audit-record-list">
            @forelse($auditGroups as $group)
                <article class="audit-record-card">
                    <div class="audit-record-header">
                        <div class="min-w-0">
                            <div class="audit-record-title-row">
                                @if($group['href'])
                                    <a href="{{ $group['href'] }}">{{ $group['title'] }}</a>
                                @else
                                    <span>{{ $group['title'] }}</span>
                                @endif
                                <span class="audit-record-type">{{ $group['type'] }}</span>
                            </div>
                            <p class="audit-record-subtitle mb-0">{{ $group['subtitle'] }}</p>
                        </div>
                        <time datetime="{{ $group['latest_timestamp']?->toIso8601String() }}">
                            {{ $group['latest_timestamp']?->format('Y-m-d h:i A') }}
                        </time>
                    </div>

                    <ol class="audit-timeline">
                        @foreach($group['timeline'] as $entry)
                            <li class="audit-timeline-item audit-tone-{{ $entry['tone'] }}">
                                <div class="audit-timeline-main">
                                    <div class="audit-event-row">
                                        <strong>{{ $entry['action'] }}</strong>
                                        <time datetime="{{ $entry['timestamp']?->toIso8601String() }}">
                                            {{ $entry['timestamp']?->format('Y-m-d h:i A') }}
                                        </time>
                                    </div>
                                    <div class="audit-event-actor">{{ $entry['actor'] }}</div>
                                    <div class="audit-event-detail">{{ $entry['detail'] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </article>
            @empty
                <div class="audit-empty-state">
                    <h2>No audit logs found</h2>
                    <p class="text-muted mb-0">Try a wider date range or remove one of the active filters.</p>
                </div>
            @endforelse
        </div>

        <div class="audit-pagination">
            {{ $auditLogs->links() }}
        </div>
    </section>
</div>
@endsection
