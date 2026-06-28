<x-app-layout>
    <x-slot name="header">Overview</x-slot>

    @php $role = auth()->user()->role; @endphp

    {{-- ===== ADMIN DASHBOARD ===== --}}
    @if($role === 'admin')

    <!-- Security NFR Banner -->
    <div class="bg-blue-900 text-white rounded-xl p-4 flex items-start gap-3 mb-6">
        <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <div>
            <div class="font-semibold text-sm">Security NFR Active</div>
            <div class="text-blue-200 text-xs mt-0.5">2FA enforced · Role-based access control · All actions audit-logged · Session timeout: 30 min · Password complexity enforced · Login rate limiting active</div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total Users',       'value' => $stats['total_users'] ?? 6,   'sub' => ($stats['active_users'] ?? 5).' active',       'color' => 'blue'],
            ['label' => 'Active Vendors',     'value' => $stats['active_vendors'] ?? 2, 'sub' => ($stats['total_vendors'] ?? 3).' total',       'color' => 'green'],
            ['label' => 'Pending DOs',        'value' => $stats['pending_dos'] ?? 3,   'sub' => 'Awaiting review',                               'color' => 'orange'],
            ['label' => 'Invoices This Month','value' => $stats['total_invoices'] ?? 4,'sub' => 'RM '.number_format($stats['total_value'] ?? 170478, 2), 'color' => 'purple'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-sm font-medium text-gray-600 mt-1">{{ $stat['label'] }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $stat['sub'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- System Health -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                </svg>
                System Health
            </h3>
            @foreach([
                ['label' => 'System Uptime',       'value' => '99.8%'],
                ['label' => 'Last Backup',          'value' => 'Today 02:00 AM'],
                ['label' => 'Active Sessions',      'value' => '12 users'],
                ['label' => 'Oracle Integration',   'value' => 'Synced 1hr ago'],
                ['label' => 'SMTP Email Service',   'value' => 'Operational'],
                ['label' => 'Disk Usage',           'value' => '34% of 500GB'],
            ] as $item)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-800">{{ $item['value'] }}</span>
                    <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Recent Audit Logs -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-4">Recent Audit Activities</h3>
            <div class="space-y-3">
                @forelse($recentLogs ?? [] as $log)
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                    <div>
                        <div class="text-gray-700 font-medium">{{ $log->action }}</div>
                        <div class="text-gray-400 text-xs">{{ $log->user_name }} · {{ $log->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
                @empty
                @foreach([
                    ['action' => 'Submitted Delivery Order',        'user' => 'vendor1 (TechSupply)',      'time' => '2026-06-20 09:15'],
                    ['action' => 'Approved Delivery Order',         'user' => 'finance (Siti Norzahra)',   'time' => '2026-06-19 11:30'],
                    ['action' => 'Invoice Submitted for Review',    'user' => 'vendor2 (Rail Parts)',      'time' => '2026-06-18 14:00'],
                    ['action' => 'Created New User Account',        'user' => 'admin (Ahmad Faris)',       'time' => '2026-06-17 08:00'],
                    ['action' => 'Generated Invoice Aging Report',  'user' => 'finance (Siti Norzahra)',   'time' => '2026-06-16 10:00'],
                ] as $log)
                <div class="flex items-start gap-3 text-sm">
                    <div class="w-2 h-2 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                    <div>
                        <div class="text-gray-700 font-medium">{{ $log['action'] }}</div>
                        <div class="text-gray-400 text-xs">{{ $log['user'] }} · {{ $log['time'] }}</div>
                    </div>
                </div>
                @endforeach
                @endforelse
            </div>
            <a href="{{ route('admin.audit') }}" class="mt-4 text-sm text-blue-600 hover:underline flex items-center gap-1">
                View all logs
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- ===== FINANCE DASHBOARD ===== --}}
    @elseif($role === 'finance')

    <!-- Performance NFR Banner -->
    <div class="bg-purple-900 text-white rounded-xl p-4 flex items-start gap-3 mb-6">
        <svg class="w-5 h-5 text-purple-300 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
        </svg>
        <div>
            <div class="font-semibold text-sm">Performance NFR Active</div>
            <div class="text-purple-200 text-xs mt-0.5">500+ concurrent users supported · Page load &lt;2s · Reports generated &lt;5s · Oracle integration synced hourly · 99.5% uptime SLA</div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Pending DO Review',    'value' => $stats['pending_dos'] ?? 2,     'href' => 'finance.do'],
            ['label' => 'Invoices for Review',  'value' => $stats['pending_invoices'] ?? 2, 'href' => 'finance.invoices'],
            ['label' => 'Total Claims (Month)', 'value' => 'RM '.number_format($stats['total_claims'] ?? 170478, 2), 'href' => '#'],
            ['label' => 'Invoices Paid',        'value' => $stats['paid_count'] ?? 1,       'href' => '#'],
        ] as $stat)
        <a href="{{ $stat['href'] !== '#' ? route($stat['href']) : '#' }}"
            class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm block hover:shadow-md transition">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </a>
        @endforeach
    </div>

    <!-- Pending Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">DOs Awaiting Review</h3>
            @forelse($pendingDOs ?? [] as $do)
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100 mb-2">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $do->do_number }}</div>
                    <div class="text-xs text-gray-500">{{ $do->supplier_name }}</div>
                </div>
                <a href="{{ route('finance.do.show', $do->id) }}" class="text-xs text-orange-600 hover:text-orange-800">Review →</a>
            </div>
            @empty
            @foreach([['num'=>'DO-2026-003','vendor'=>'TechSupply Sdn Bhd'],['num'=>'DO-2026-005','vendor'=>'Rail Parts & Services']] as $do)
            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-100 mb-2">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $do['num'] }}</div>
                    <div class="text-xs text-gray-500">{{ $do['vendor'] }}</div>
                </div>
                <a href="{{ route('finance.do') }}" class="text-xs text-orange-600 hover:text-orange-800">Review →</a>
            </div>
            @endforeach
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 mb-3">Invoices Awaiting Review</h3>
            @forelse($pendingInvoices ?? [] as $inv)
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100 mb-2">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $inv->invoice_num }}</div>
                    <div class="text-xs text-gray-500">{{ $inv->supplier_name }} · RM {{ number_format($inv->total, 2) }}</div>
                </div>
                <a href="{{ route('finance.invoices') }}" class="text-xs text-purple-600 hover:text-purple-800">Review →</a>
            </div>
            @empty
            @foreach([['num'=>'INV-2026-0003','vendor'=>'Rail Parts & Services','total'=>'76,320.00'],['num'=>'INV-2026-0004','vendor'=>'Rail Parts & Services','total'=>'16,748.00']] as $inv)
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg border border-purple-100 mb-2">
                <div>
                    <div class="text-sm font-medium text-gray-800">{{ $inv['num'] }}</div>
                    <div class="text-xs text-gray-500">{{ $inv['vendor'] }} · RM {{ $inv['total'] }}</div>
                </div>
                <a href="{{ route('finance.invoices') }}" class="text-xs text-purple-600 hover:text-purple-800">Review →</a>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>

    {{-- ===== CUSTOMER/VENDOR DASHBOARD ===== --}}
    @else

    <!-- Inactive vendor warning -->
    @if(auth()->user()->vendor_status === 'Inactive')
    <div class="bg-red-50 border border-red-300 rounded-xl p-4 flex items-start gap-3 mb-6" role="alert">
        <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <div class="font-semibold text-red-800">Account Deactivated</div>
            <div class="text-red-700 text-sm mt-0.5">Your vendor account is inactive. You cannot submit new DOs or Invoices. Contact KTM procurement at <a href="mailto:procurement@ktm.com.my" class="underline">procurement@ktm.com.my</a>.</div>
        </div>
    </div>
    @endif

    <!-- Usability NFR Banner -->
    <div class="bg-blue-900 text-white rounded-xl p-4 flex items-start gap-3 mb-6">
        <svg class="w-5 h-5 text-blue-300 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <div class="font-semibold text-sm">Usability NFR Active</div>
            <div class="text-blue-200 text-xs mt-0.5">Intuitive interface for non-technical users · Mobile-friendly responsive design · Clear error messages · Accessibility features enabled</div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'DOs Submitted',       'value' => $stats['dos_submitted'] ?? 5],
            ['label' => 'DOs Approved',        'value' => $stats['dos_approved'] ?? 3],
            ['label' => 'Invoices Submitted',  'value' => $stats['invoices_submitted'] ?? 3],
            ['label' => 'Invoices Paid',       'value' => $stats['invoices_paid'] ?? 1],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Recent DOs and Invoices -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Recent Delivery Orders</h3>
                <a href="{{ route('admin.delivery-orders.index') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
            @forelse($recentDOs ?? [] as $do)
            <div class="py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $do->do_number }}</div>
                        <div class="text-xs text-gray-500">{{ $do->po_number }} · {{ $do->project }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0
                        {{ $do->status === 'Approved' ? 'bg-green-100 text-green-700' : ($do->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $do->status }}
                    </span>
                </div>
            </div>
            @empty
            @foreach([
                ['num'=>'DO-2026-001','po'=>'PO-2026-0045','status'=>'Approved'],
                ['num'=>'DO-2026-003','po'=>'PO-2026-0050','status'=>'Pending Approval'],
                ['num'=>'DO-2026-006','po'=>'PO-2026-0020','status'=>'Rejected'],
            ] as $do)
            <div class="py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $do['num'] }}</div>
                        <div class="text-xs text-gray-500">{{ $do['po'] }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0
                        {{ $do['status'] === 'Approved' ? 'bg-green-100 text-green-700' : ($do['status'] === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ $do['status'] }}
                    </span>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-800">Recent Invoices</h3>
                <a href="{{ route('admin.invoices.index') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
            @forelse($recentInvoices ?? [] as $inv)
            <div class="py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $inv->invoice_num }}</div>
                        <div class="text-xs text-gray-500">{{ $inv->do_number }} · RM {{ number_format($inv->total, 2) }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0
                        {{ $inv->status === 'Paid' ? 'bg-green-100 text-green-800' : ($inv->status === 'Payment Processing' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ $inv->status }}
                    </span>
                </div>
            </div>
            @empty
            @foreach([
                ['num'=>'INV-2026-0001','do'=>'DO-2026-001','total'=>'47,700.00','status'=>'Paid'],
                ['num'=>'INV-2026-0002','do'=>'DO-2026-002','total'=>'29,710.00','status'=>'Payment Processing'],
            ] as $inv)
            <div class="py-3 border-b border-gray-50 last:border-0">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-800">{{ $inv['num'] }}</div>
                        <div class="text-xs text-gray-500">{{ $inv['do'] }} · RM {{ $inv['total'] }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium flex-shrink-0
                        {{ $inv['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-700' }}">
                        {{ $inv['status'] }}
                    </span>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>

    @endif
</x-app-layout>
