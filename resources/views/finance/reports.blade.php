<x-app-layout>
    <x-slot name="header">Reports & Analytics</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Financial Reports & Analytics</h2>
        <p class="text-sm text-gray-500">Summary of delivery orders and invoices in the KTM eDOIS system</p>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4 mb-8">
        @foreach([
            ['label' => 'Total DOs',          'value' => $stats['total_dos'],           'color' => 'blue'],
            ['label' => 'Approved DOs',        'value' => $stats['approved_dos'],        'color' => 'green'],
            ['label' => 'Pending DOs',         'value' => $stats['pending_dos'],         'color' => 'orange'],
            ['label' => 'Total Invoices',      'value' => $stats['total_invoices'],      'color' => 'purple'],
            ['label' => 'Invoices Paid',       'value' => $stats['paid_invoices'],       'color' => 'green'],
        ] as $stat)
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <div class="text-2xl font-bold text-gray-800">{{ $stat['value'] }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    <!-- Invoice Value Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Invoice Summary</h3>
            @foreach([
                ['label' => 'Total Invoice Value',  'value' => 'RM ' . number_format($stats['total_invoice_value'], 2)],
                ['label' => 'Paid Invoice Value',   'value' => 'RM ' . number_format($stats['paid_invoice_value'], 2)],
                ['label' => 'Pending Invoice Value','value' => 'RM ' . number_format($stats['total_invoice_value'] - $stats['paid_invoice_value'], 2)],
                ['label' => 'Total Pending DOs',    'value' => $stats['pending_dos']],
                ['label' => 'Rejected DOs',         'value' => $stats['rejected_dos']],
            ] as $item)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <span class="text-sm font-semibold text-gray-800">{{ $item['value'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('finance.do') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">Review Delivery Orders</div>
                        <div class="text-xs text-gray-500">{{ $stats['pending_dos'] }} pending</div>
                    </div>
                </a>
                <a href="{{ route('finance.invoices') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-100 hover:bg-gray-50 transition">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-800">Review Invoices</div>
                        <div class="text-xs text-gray-500">{{ $stats['pending_invoices'] }} pending</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
