@php($iconName = $name ?? 'document')

@switch($iconName)
    @case('search')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="m16 16 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('dashboard')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 13h7V4H4v9Zm9 7h7V4h-7v16ZM4 20h7v-5H4v5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break

    @case('delivery')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 7h12v10H3V7Zm12 3h3l3 3v4h-6v-7Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M7 20a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm10 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break

    @case('document')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 3h7l4 4v14H7V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M14 3v5h5M10 12h6M10 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('invoice')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 3h10v18l-2-1.3-2 1.3-2-1.3-2 1.3-2-1.3V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M10 8h4M10 12h4M10 16h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('clock')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('check')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M20 7 10 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 21a9 9 0 1 0-8.5-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('money')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M7 9v.01M17 15v.01" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
        </svg>
        @break

    @case('card')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M4 10h16M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('calendar')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 5h14v15H5V5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M8 3v4M16 3v4M5 10h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('users')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M16 19c0-2.2-1.8-4-4-4s-4 1.8-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM20 18c0-1.7-1.1-3.1-2.6-3.7M16.5 6.4a2.5 2.5 0 0 1 0 4.8M4 18c0-1.7 1.1-3.1 2.6-3.7M7.5 6.4a2.5 2.5 0 0 0 0 4.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('chart')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 19V9M10 19V5M15 19v-7M20 19V8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M4 19h17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('filter')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 6h14l-5.5 6.2V18l-3 1v-6.8L5 6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
        </svg>
        @break

    @case('eye')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M3 12s3.3-5 9-5 9 5 9 5-3.3 5-9 5-9-5-9-5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 14.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" stroke="currentColor" stroke-width="1.8"/>
        </svg>
        @break

    @case('review')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 3h10v18H7V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M10 8h4M10 12h2M10 16l1.5 1.5L15 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('download')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 4v10m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('list')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M8 7h12M8 12h12M8 17h12M4 7h.01M4 12h.01M4 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        @break

    @case('more')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 12h.01M12 12h.01M18 12h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
        </svg>
        @break

    @case('upload')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 16V5m0 0 4 4m-4-4-4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M5 15v4h14v-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('bell')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M13.7 21a2 2 0 0 1-3.4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('back')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M15 18 9 12l6-6" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @case('audit')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6 3h12v18H6V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 8h6M9 12h6M9 16h3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('profile')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        @break

    @case('logout')
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M10 17l5-5-5-5M15 12H3M21 4v16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        @break

    @default
        <svg class="dashboard-svg" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 3h7l4 4v14H7V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M14 3v5h5M10 12h6M10 16h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
@endswitch
