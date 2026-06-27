@php
    $supplier = null;
    $approvedDoId = null;
    $rejectedDoCount = 0;
    $notificationCount = 0;

    if (session('supplier_id')) {
        $supplier = \App\Models\Supplier::find(session('supplier_id'));
        $approvedDoId = \App\Models\DeliveryOrder::where('supplier_id', session('supplier_id'))
            ->where('status', 'Approved')
            ->latest()
            ->value('do_id');
        $rejectedDoCount = \App\Models\DeliveryOrder::where('supplier_id', session('supplier_id'))
            ->where('status', 'Rejected')
            ->count();
        $notificationCount = \App\Models\Notification::where('supplier_id', session('supplier_id'))
            ->where('status', 'unread')
            ->count();
    }

    if (auth()->check()) {
        $notificationCount = \App\Models\Notification::where('cust_id', auth()->id())
            ->where('status', 'unread')
            ->count();
    }

    $customerLinks = [
        ['label' => 'Dashboard', 'short' => 'Dash', 'icon' => 'dashboard', 'route' => 'customer.dashboard', 'href' => route('customer.dashboard')],
        ['label' => 'Delivery Orders', 'short' => 'DO', 'icon' => 'delivery', 'route' => 'customer.delivery-orders.*', 'href' => route('customer.delivery-orders.index')],
        ['label' => 'Invoices', 'short' => 'INV', 'icon' => 'invoice', 'route' => 'customer.invoices.*', 'href' => route('customer.invoices.index')],
        ['label' => 'Notifications', 'short' => 'Bell', 'icon' => 'bell', 'route' => 'customer.notifications.*', 'href' => route('customer.notifications.index'), 'badge' => $notificationCount],
        ['label' => 'Audit Logs', 'short' => 'Audit', 'icon' => 'audit', 'route' => 'customer.audit-logs.*', 'href' => route('customer.audit-logs.index')],
    ];

    $supplierLinks = [
        ['label' => 'Dashboard', 'short' => 'Dash', 'icon' => 'dashboard', 'route' => 'supplier.profile', 'href' => route('supplier.profile')],
        ['label' => 'Delivery Order', 'short' => 'DO', 'icon' => 'delivery', 'route' => 'supplier.do.*', 'href' => route('supplier.do.status'), 'badge' => $rejectedDoCount],
        [
            'label' => 'Invoice',
            'short' => 'INV',
            'icon' => 'invoice',
            'route' => 'supplier.invoice.*',
            'href' => $approvedDoId ? route('supplier.invoice.create', $approvedDoId) : route('supplier.invoice.status'),
        ],
        ['label' => 'Notifications', 'short' => 'Bell', 'icon' => 'bell', 'route' => 'supplier.notifications', 'href' => route('supplier.notifications'), 'badge' => $notificationCount],
        ['label' => 'Profile', 'short' => 'Me', 'icon' => 'profile', 'route' => 'supplier.details', 'href' => route('supplier.details')],
    ];

    $links = auth()->check() ? $customerLinks : $supplierLinks;
    $displayRole = auth()->check() ? 'KTM Officer' : 'Supplier';
@endphp

<aside class="ktm-sidebar d-flex flex-column">
    <div class="ktm-brand-block px-3">
        <img class="ktm-sidebar-logo" src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
        <div class="ktm-brand-copy">Keretapi Tanah Melayu Berhad (KTMB)</div>
    </div>

    <nav class="ktm-sidebar-nav d-grid gap-2 p-3">
        @foreach($links as $link)
            <a class="rounded px-3 py-2 d-flex align-items-center gap-3 {{ request()->routeIs($link['route']) ? 'active' : '' }}"
               href="{{ $link['href'] }}">
                <span class="sidebar-icon">@include('shared.dashboard-icon', ['name' => $link['icon']])</span>
                <span class="sidebar-label">{{ $link['label'] }}</span>
                <span class="sidebar-label-short">{{ $link['short'] ?? $link['label'] }}</span>
                @if(($link['badge'] ?? 0) > 0)
                    <span class="sidebar-badge ms-auto">{{ $link['badge'] }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="mt-auto ktm-sidebar-footer p-3">
        <div class="sidebar-role mb-3 d-none d-sm-block">{{ $displayRole }}</div>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-link text-white text-decoration-none p-0 fw-bold d-inline-flex align-items-center gap-3" type="submit">
                    <span class="sidebar-icon">@include('shared.dashboard-icon', ['name' => 'logout'])</span>
                    <span class="sidebar-label">Logout</span>
                    <span class="sidebar-label-short">Out</span>
                </button>
            </form>
        @else
            @if(session('supplier_id'))
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button class="btn btn-link text-white text-decoration-none p-0 fw-bold d-inline-flex align-items-center gap-3" type="submit">
                        <span class="sidebar-icon">@include('shared.dashboard-icon', ['name' => 'logout'])</span>
                        <span class="sidebar-label">Logout</span>
                        <span class="sidebar-label-short">Out</span>
                    </button>
                </form>
            @endif
        @endauth
    </div>
</aside>
