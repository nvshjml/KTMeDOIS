@php
    $notificationCount = 0;
    $notificationHref = '#';

    if (auth()->check()) {
        $notificationCount = \App\Models\Notification::where('cust_id', auth()->id())
            ->where('status', 'unread')
            ->count();
        $notificationHref = route('customer.notifications.index');
    } elseif (session('supplier_id')) {
        $notificationCount = \App\Models\Notification::where('supplier_id', session('supplier_id'))
            ->where('status', 'unread')
            ->count();
        $notificationHref = route('supplier.notifications');
    }
@endphp

<nav class="navbar ktm-topbar px-3 px-lg-4">
    <div>
        <h1 class="h5 fw-bold mb-1">@yield('page-title', 'KTM eDOIS')</h1>
        <div class="page-kicker">@yield('page-kicker', 'KTM eDOIS - Vendor Portal')</div>
    </div>

    <div class="d-flex align-items-center gap-2">
        @if(auth()->check() || session('supplier_id'))
            <a class="notification-button" href="{{ $notificationHref }}" aria-label="Notifications">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @if($notificationCount > 0)
                    <span class="notification-count">{{ $notificationCount }}</span>
                @endif
            </a>
        @else
            <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Customer Login</a>
            <a class="btn btn-warning btn-sm" href="{{ route('login', ['login_as' => 'supplier']) }}">Supplier Login</a>
        @endif
    </div>
</nav>
