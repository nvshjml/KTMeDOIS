<x-app-layout>
    <x-slot name="header">DO Review</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Delivery Orders — Review Queue</h2>
        <p class="text-sm text-gray-500">Review and approve or reject vendor delivery orders</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">DO Number</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Vendor</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">PO Number</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Status</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Submitted</th>
                    <th class="px-6 py-3 text-gray-500 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dos as $do)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $do->DO_number }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $do->supplier->name ?? $do->supplier->supplier_name ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $do->po_number ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $do->status === 'Approved' ? 'bg-green-100 text-green-700' :
                               ($do->status === 'Rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $do->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $do->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($do->status === 'Pending Approval')
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('finance.do.approve', $do->DO_ID) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('finance.do.reject', $do->DO_ID) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600 transition">Reject</button>
                            </form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">Reviewed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        No delivery orders found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($dos, 'hasPages') && $dos->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $dos->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
