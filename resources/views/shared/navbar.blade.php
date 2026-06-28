@php
    $notificationCount = 0;
    $notificationHref = '#';
    $profileHref = null;
    $displayName = 'Guest';
    $displayRole = 'Visitor';
    $displayInitials = 'KT';

    if (auth()->check()) {
        $notificationCount = \App\Models\Notification::where('cust_id', auth()->id())
            ->where('status', 'unread')
            ->count();
        $notificationHref = route('admin.notifications.index');
        $displayName = auth()->user()->name ?? auth()->user()->username;
        $displayRole = 'KTM Admin';
        $displayInitials = collect(explode(' ', $displayName))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'KO';
    } elseif (session('supplier_id')) {
        $supplier = \App\Models\Supplier::find(session('supplier_id'));
        $notificationCount = \App\Models\Notification::where('supplier_id', session('supplier_id'))
            ->where('status', 'unread')
            ->count();
        $notificationHref = route('supplier.notifications');
        $profileHref = route('supplier.details');
        $displayName = $supplier?->supplier_name ?? 'Supplier Portal';
        $displayRole = 'Supplier';
        $displayInitials = collect(explode(' ', $displayName))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'SP';
    }
@endphp

<nav class="navbar ktm-topbar px-3 px-lg-4">
    <div class="topbar-heading min-w-0">
        <h1 class="h3 page-title mb-1">@yield('page-title', 'KTM eDOIS')</h1>
        <div class="page-kicker">@yield('page-kicker', 'KTM eDOIS Dashboard')</div>
    </div>

    @if(auth()->check() || session('supplier_id'))
        @if($profileHref)
            <a class="topbar-profile" href="{{ $profileHref }}" aria-label="Open profile">
                <span class="topbar-avatar">{{ $displayInitials }}</span>
                <span class="topbar-profile-name">{{ $displayName }}</span>
                <span class="topbar-user-role">{{ $displayRole }}</span>
            </a>
        @else
            <div class="topbar-profile" aria-label="Profile">
                <span class="topbar-avatar">{{ $displayInitials }}</span>
                <span class="topbar-profile-name">{{ $displayName }}</span>
                <span class="topbar-user-role">{{ $displayRole }}</span>
            </div>
        @endif

        <div class="topbar-search-row">
            <label class="topbar-search mb-0">
                @include('shared.dashboard-icon', ['name' => 'search'])
                <input type="search" placeholder="Search DO, Invoice, or Reference..." aria-label="Search DO, Invoice, or Reference">
            </label>
        </div>

        <div class="topbar-notification-row">
            <a class="notification-button" href="{{ $notificationHref }}" aria-label="Notifications">
                @include('shared.dashboard-icon', ['name' => 'bell'])
                @if($notificationCount > 0)
                    <span class="notification-count">{{ $notificationCount }}</span>
                @endif
            </a>
        </div>
    @else
        <div class="topbar-actions">
            <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Admin Login</a>
            <a class="btn btn-warning btn-sm" href="{{ route('login', ['login_as' => 'supplier']) }}">Supplier Login</a>
        </div>
    @endif
</nav>

