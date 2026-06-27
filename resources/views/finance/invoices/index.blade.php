<x-app-layout>
    <x-slot name="header">Invoice Review</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Invoices — Review Queue</h2>
        <p class="text-sm text-gray-500">Review and approve or reject vendor invoices for payment</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Invoice No.</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Vendor</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Amount (RM)</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Status</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Submitted</th>
                    <th class="px-6 py-3 text-right text-gray-500 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $invoice->invoice_number }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ optional($invoice->supplier)->supplier_name ?? optional($invoice->supplier)->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-gray-800 font-medium">{{ number_format($invoice->total, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $invoice->status === 'Paid' ? 'bg-green-100 text-green-700' :
                               ($invoice->status === 'Rejected' ? 'bg-red-100 text-red-700' :
                               ($invoice->status === 'Payment Processing' ? 'bg-purple-100 text-purple-700' : 'bg-yellow-100 text-yellow-700')) }}">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $invoice->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($invoice->status === 'Submitted')
                        <div class="flex items-center justify-end gap-2">
                            <form method="POST" action="{{ route('finance.invoices.approve', $invoice->invoice_id) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-lg hover:bg-green-700 transition">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('finance.invoices.reject', $invoice->invoice_id) }}">
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">No invoices found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if(method_exists($invoices, 'hasPages') && $invoices->hasPages())
        <div class="px-6 py-4 border-t border-gray-50">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
