@extends('layouts.app')

@section('title', 'Notifications - KTMeDOIS')
@section('page-title', 'Notifications')
@section('page-kicker', 'KTM eDOIS - Activity Updates')

@section('content')
<div class="page-stack">
    @include('shared.back-button', ['href' => route('admin.dashboard'), 'label' => 'Back to Dashboard'])

    <div class="page-heading">
        <h1 class="h3 mb-1">Notifications</h1>
        <p class="text-muted mb-0">Admin notifications created by supplier and invoice activity.</p>
    </div>

    <section class="content-card page-table-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Content</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>{{ str_replace('_', ' ', $notification->type) }}</td>
                            <td>{{ $notification->content }}</td>
                            <td>@include('shared.status-badge', ['status' => $notification->status])</td>
                            <td>{{ $notification->created_at?->format('d M Y, h:i A') }}</td>
                            <td class="text-end">
                                @if($notification->status === 'unread')
                                    <form method="POST" action="{{ route('admin.notifications.read', $notification->notification_id) }}">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-primary" type="submit">Mark Read</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted">No notifications yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $notifications->links() }}
    </section>
</div>
@endsection
