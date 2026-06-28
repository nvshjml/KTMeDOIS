@extends('layouts.app')

@section('title', 'Audit Logs - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div>
        <h1 class="h3 mb-1">Audit Logs</h1>
        <p class="text-muted mb-0">Tracked activity for admin actions, supplier validation, submissions, and downloads.</p>
    </div>

    <form class="content-card p-3 row g-3 align-items-end" method="GET">
        <div class="col-lg-4 col-md-6">
            <label class="form-label" for="search">Search</label>
            <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Action, record, admin, supplier">
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label" for="start_date">Start Date</label>
            <input class="form-control" id="start_date" name="start_date" type="date" value="{{ request('start_date') }}">
        </div>
        <div class="col-lg-3 col-md-6">
            <label class="form-label" for="end_date">End Date</label>
            <input class="form-control" id="end_date" name="end_date" type="date" value="{{ request('end_date') }}">
        </div>
        <div class="col-lg-2 col-md-6 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Filter</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.audit-logs.index') }}">Reset</a>
        </div>
    </form>

    <section class="content-card p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Action</th>
                        <th>Record</th>
                        <th>Admin</th>
                        <th>Supplier</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $log)
                        <tr>
                            <td>{{ $log->timestamp?->format('d M Y, h:i A') }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->affected_record }}</td>
                            <td>{{ $log->customer?->username ?? '-' }}</td>
                            <td>{{ $log->supplier?->supplier_name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No audit logs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditLogs->links() }}
    </section>
</div>
@endsection

