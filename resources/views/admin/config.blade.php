<x-app-layout>
    <x-slot name="header">System Configuration</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">System Settings</h2>
        <p class="text-sm text-gray-500">KTM eDOIS non-functional requirements and system parameters</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Security Settings -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Security NFR
            </h3>
            @foreach([
                ['label' => '2FA Enforcement',         'value' => 'Enabled',           'status' => 'green'],
                ['label' => 'Role-Based Access Control','value' => 'Active',            'status' => 'green'],
                ['label' => 'Audit Logging',            'value' => 'All Actions',       'status' => 'green'],
                ['label' => 'Session Timeout',          'value' => '30 minutes',        'status' => 'green'],
                ['label' => 'Password Complexity',      'value' => 'Min 4 chars',       'status' => 'green'],
                ['label' => 'Login Rate Limiting',      'value' => '5 attempts / min',  'status' => 'green'],
            ] as $item)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <span class="text-sm font-medium text-green-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $item['value'] }}
                </span>
            </div>
            @endforeach
        </div>

        <!-- Performance Settings -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Performance NFR
            </h3>
            @foreach([
                ['label' => 'Concurrent Users',       'value' => '500+'],
                ['label' => 'Page Load Target',       'value' => '< 2 seconds'],
                ['label' => 'Report Generation',      'value' => '< 5 seconds'],
                ['label' => 'Oracle Sync Frequency',  'value' => 'Every 1 hour'],
                ['label' => 'System Uptime SLA',      'value' => '99.5%'],
                ['label' => 'DB Backup Schedule',     'value' => 'Daily 02:00 AM'],
            ] as $item)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <span class="text-sm font-medium text-gray-800">{{ $item['value'] }}</span>
            </div>
            @endforeach
        </div>

        <!-- System Info -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">System Information</h3>
            @foreach([
                ['label' => 'Laravel Version', 'value' => app()->version()],
                ['label' => 'PHP Version',     'value' => PHP_VERSION],
                ['label' => 'Environment',     'value' => app()->environment()],
                ['label' => 'Timezone',        'value' => config('app.timezone')],
                ['label' => 'App Name',        'value' => config('app.name')],
            ] as $item)
            <div class="flex items-center justify-between py-2.5 border-b border-gray-50 last:border-0">
                <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                <span class="text-sm font-mono text-gray-800 bg-gray-50 px-2 py-0.5 rounded">{{ $item['value'] }}</span>
            </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
