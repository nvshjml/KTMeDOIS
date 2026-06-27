<x-app-layout>
    <x-slot name="header">Audit Logs</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">System Audit Trail</h2>
        <p class="text-sm text-gray-500">All actions performed in the KTM eDOIS system</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Timestamp</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">User</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Action</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Record</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-3 text-gray-400 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-3 text-gray-700">{{ $log->user_name ?? ($log->user->name ?? 'System') }}</td>
                    <td class="px-6 py-3 text-gray-800">{{ $log->action }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $log->affected_record }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">No audit logs recorded yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
