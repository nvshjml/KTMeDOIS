@extends('layouts.app')

@section('title', 'Notifications - KTMeDOIS')

@section('content')
<div class="d-flex flex-column gap-3">
    <div>
        <h1 class="h3 mb-1">Notifications</h1>
        <p class="text-muted mb-0">Customer notifications created by supplier and invoice activity.</p>
    </div>

    <section class="content-card p-3">
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
                                    <form method="POST" action="{{ route('customer.notifications.read', $notification->notification_id) }}">
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
