@php
    $supplier = null;
    $approvedDoId = null;
    $rejectedDoCount = 0;

    if (session('supplier_id')) {
        $supplier = \App\Models\Supplier::find(session('supplier_id'));
        $approvedDoId = \App\Models\DeliveryOrder::where('supplier_id', session('supplier_id'))
            ->where('status', 'Approved')
            ->latest()
            ->value('do_id');
        $rejectedDoCount = \App\Models\DeliveryOrder::where('supplier_id', session('supplier_id'))
            ->where('status', 'Rejected')
            ->count();
    }

    $customerLinks = [
        ['label' => 'Overview', 'route' => 'customer.dashboard', 'href' => route('customer.dashboard')],
        ['label' => 'Delivery Orders', 'route' => 'customer.delivery-orders.*', 'href' => route('customer.delivery-orders.index')],
        ['label' => 'Invoices', 'route' => 'customer.invoices.*', 'href' => route('customer.invoices.index')],
        ['label' => 'Audit Logs', 'route' => 'customer.audit-logs.index', 'href' => route('customer.audit-logs.index')],
    ];

    $supplierLinks = [
        ['label' => 'Overview', 'route' => 'supplier.profile', 'href' => route('supplier.profile')],
        ['label' => 'My Profile', 'route' => 'supplier.details', 'href' => route('supplier.details')],
        ['label' => 'Submit DO', 'route' => 'supplier.do.create', 'href' => route('supplier.do.create')],
        ['label' => 'My Delivery Orders', 'route' => 'supplier.do.status', 'href' => route('supplier.do.status')],
        [
            'label' => 'Submit Invoice',
            'route' => 'supplier.invoice.create',
            'href' => $approvedDoId ? route('supplier.invoice.create', $approvedDoId) : route('supplier.do.status'),
        ],
        ['label' => 'My Invoices', 'route' => 'supplier.invoice.status', 'href' => route('supplier.invoice.status')],
    ];

    $links = auth()->check() ? $customerLinks : $supplierLinks;
    $displayName = auth()->check() ? auth()->user()->username : ($supplier?->supplier_name ?? 'Supplier Portal');
    $displayRef = auth()->check() ? auth()->user()->user_email : ($supplier?->vendor_number ?? 'Vendor verification');
    $displayInitial = strtoupper(substr($displayName, 0, 1));
@endphp

<aside class="ktm-sidebar d-flex flex-column">
    <div class="ktm-brand-block d-flex align-items-center gap-3 px-3">
        <span class="ktm-logo-tile">
            <img src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
        </span>
        <div>
            <div class="fw-bold">KTM eDOIS</div>
            <div class="small text-white-50">{{ auth()->check() ? 'Officer Portal' : 'Vendor Portal' }}</div>
        </div>
    </div>

    <div class="ktm-vendor-block d-flex align-items-center gap-3 px-3 py-3">
        <span class="ktm-avatar">{{ $displayInitial }}</span>
        <div class="min-w-0">
            <div class="fw-bold text-truncate">{{ $displayName }}</div>
            <div class="small">
                <span class="text-success">&bull;</span>
                <span class="text-white-50">Active &middot; {{ $displayRef }}</span>
            </div>
        </div>
    </div>

    <nav class="d-grid gap-1 p-2">
        @foreach($links as $link)
            <a class="rounded px-3 py-2 d-flex align-items-center justify-content-between {{ request()->routeIs($link['route']) ? 'active' : '' }}"
               href="{{ $link['href'] }}">
                <span>{{ $link['label'] }}</span>
                @if($link['label'] === 'My Delivery Orders' && $rejectedDoCount > 0)
                    <span class="sidebar-badge">{{ $rejectedDoCount }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="mt-auto border-top border-white border-opacity-25 p-3 d-grid gap-2">
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-link text-white text-decoration-none p-0 fw-bold" type="submit">Logout</button>
            </form>
        @else
            @if(session('supplier_id'))
                <form method="POST" action="{{ route('supplier.logout') }}">
                    @csrf
                    <button class="btn btn-link text-white text-decoration-none p-0 fw-bold" type="submit">Logout</button>
                </form>
            @endif
        @endauth
    </div>
</aside>
