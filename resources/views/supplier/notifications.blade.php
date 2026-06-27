@extends('layouts.app')

@section('title', 'Notifications - KTM eDOIS')
@section('page-title', 'Notifications')
@section('page-kicker', 'KTM eDOIS - Vendor Portal')

@section('content')
<section class="content-card">
    <div class="p-4 border-bottom">
        <h2 class="h5 fw-bold mb-1">Notifications</h2>
        <div class="small text-muted">System updates for Delivery Orders, Invoices, and claim progress.</div>
    </div>

    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <article class="list-group-item p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                    <div>
                        <div class="small text-muted text-uppercase">{{ str_replace('_', ' ', $notification->type) }}</div>
                        <div class="fw-semibold">{{ $notification->content }}</div>
                    </div>
                    <div class="small text-muted flex-shrink-0">{{ $notification->created_at?->format('Y-m-d H:i') }}</div>
                </div>
            </article>
        @empty
            <div class="p-4 text-muted">No supplier notifications yet.</div>
        @endforelse
    </div>
</section>

<div class="mt-3">
    {{ $notifications->links() }}
</div>
@endsection
