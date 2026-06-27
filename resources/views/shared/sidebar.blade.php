@php
    $customerLinks = [
        ['label' => 'Dashboard', 'route' => 'customer.dashboard'],
        ['label' => 'Delivery Orders', 'route' => 'customer.delivery-orders.index'],
        ['label' => 'Invoices', 'route' => 'customer.invoices.index'],
        ['label' => 'Notifications', 'route' => 'customer.notifications.index'],
        ['label' => 'Audit Logs', 'route' => 'customer.audit-logs.index'],
    ];

    $supplierLinks = [
        ['label' => 'Supplier Profile', 'route' => 'supplier.profile'],
        ['label' => 'Submit DO', 'route' => 'supplier.do.create'],
        ['label' => 'DO Status', 'route' => 'supplier.do.status'],
        ['label' => 'Invoice Status', 'route' => 'supplier.invoice.status'],
    ];

    $links = auth()->check() ? $customerLinks : $supplierLinks;
@endphp

<aside class="ktm-sidebar p-3">
    <div class="ktm-brand-block mb-4">
        <img class="ktm-logo mb-3" src="{{ asset('images/KTMLogo.png') }}" alt="KTM Berhad logo">
        <div>
            <div class="fw-bold fs-5">KTM eDOIS</div>
            <div class="small text-muted">{{ auth()->check() ? 'Customer Workspace' : 'Supplier Portal' }}</div>
        </div>
    </div>

    <nav class="d-grid gap-1">
        @foreach($links as $link)
            <a class="rounded px-3 py-2 {{ request()->routeIs($link['route']) ? 'active' : '' }}"
               href="{{ route($link['route']) }}">
                {{ $link['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="mt-4 pt-3 border-top border-white border-opacity-25 small text-white-50">
        Electronic Delivery Order &amp; Invoice System
    </div>
</aside>
