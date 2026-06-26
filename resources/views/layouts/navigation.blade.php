@php
    $role = auth()->user()->role ?? 'customer';
    $sidebarBg = match($role) {
        'admin'   => '#003580',
        'finance' => '#1a1a2e',
        default   => '#0f4c81',
    };
    $accentBg = match($role) {
        'admin'   => 'bg-yellow-400 text-blue-900',
        'finance' => 'bg-purple-600 text-white',
        default   => 'bg-yellow-400 text-blue-900',
    };
    $navItems = match($role) {
        'admin' => [
            ['route' => 'dashboard',        'label' => 'Overview',          'icon' => 'grid'],
            ['route' => 'admin.users',      'label' => 'User Management',   'icon' => 'users'],
            ['route' => 'admin.vendors',    'label' => 'Vendor Registry',   'icon' => 'database'],
            ['route' => 'admin.audit',      'label' => 'Audit Logs',        'icon' => 'document'],
            ['route' => 'admin.config',     'label' => 'System Config',     'icon' => 'cog'],
        ],
        'finance' => [
            ['route' => 'dashboard',        'label' => 'Overview',          'icon' => 'grid'],
            ['route' => 'finance.do',       'label' => 'DO Review',         'icon' => 'document'],
            ['route' => 'finance.invoices', 'label' => 'Invoice Review',    'icon' => 'currency'],
            ['route' => 'finance.reports',  'label' => 'Reports & Analytics','icon' => 'chart'],
            ['route' => 'finance.notif',    'label' => 'Notifications',     'icon' => 'bell'],
        ],
        default => [
            ['route' => 'dashboard',        'label' => 'Overview',          'icon' => 'grid'],
            ['route' => 'customer.profile', 'label' => 'My Profile',        'icon' => 'user'],
            ['route' => 'customer.do.create','label'=> 'Submit DO',         'icon' => 'upload'],
            ['route' => 'customer.do.index','label' => 'My Delivery Orders','icon' => 'package'],
            ['route' => 'customer.inv.create','label'=> 'Submit Invoice',   'icon' => 'plus'],
            ['route' => 'customer.inv.index','label'=> 'My Invoices',       'icon' => 'credit'],
            ['route' => 'customer.notif',   'label' => 'Notifications',     'icon' => 'bell'],
            ['route' => 'customer.help',    'label' => 'Help & Support',    'icon' => 'help'],
        ],
    };
@endphp

@php
function ktmIcon($name) {
    return match($name) {
        'grid'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
        'users'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
        'database' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>',
        'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'cog'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
        'currency' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'chart'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
        'bell'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
        'user'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'upload'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>',
        'package'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
        'plus'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>',
        'credit'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
        'help'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        default    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>',
    };
}
@endphp

<aside class="w-64 flex flex-col shadow-lg flex-shrink-0" style="background-color: {{ $sidebarBg }}">

    <!-- Logo -->
    <div class="flex items-center gap-3 p-4 border-b border-white/10">
        <div class="bg-yellow-400 rounded-lg p-1.5 flex-shrink-0">
            <svg class="w-6 h-6 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
            </svg>
        </div>
        <div>
            <div class="text-white font-bold text-sm">KTM eDOIS</div>
            <div class="text-xs" style="color: rgba(255,255,255,0.5)">
                @if($role === 'admin') Admin Portal
                @elseif($role === 'finance') Finance Portal
                @else Vendor Portal
                @endif
            </div>
        </div>
    </div>

    <!-- User info -->
    <div class="px-4 py-3 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-yellow-400 flex items-center justify-center text-blue-900 font-bold text-sm flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs" style="color: rgba(255,255,255,0.5)">
                    {{ ucfirst($role) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-3 px-2 space-y-1 overflow-y-auto">
        @foreach($navItems as $item)
        @php
            try { $isActive = request()->routeIs($item['route']); } catch(\Exception $e) { $isActive = false; }
        @endphp
        <a href="{{ route($item['route']) }}"
            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all text-sm
            {{ $isActive ? $accentBg . ' font-semibold' : 'text-white/60 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {!! ktmIcon($item['icon']) !!}
            </svg>
            <span>{{ $item['label'] }}</span>
        </a>
        @endforeach
    </nav>

    <!-- Logout -->
    <div class="p-2 border-t border-white/10">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-red-300 hover:bg-red-800/50 hover:text-white transition text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</aside>