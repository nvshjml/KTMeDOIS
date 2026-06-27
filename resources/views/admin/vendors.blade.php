<x-app-layout>
    <x-slot name="header">Vendor Registry</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Registered Vendors / Suppliers</h2>
        <p class="text-sm text-gray-500">All supplier accounts registered in the KTM eDOIS system</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Vendor Name</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Username</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Email</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Status</th>
                    <th class="text-left px-6 py-3 text-gray-500 font-medium">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($vendors as $vendor)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $vendor->name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $vendor->username }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $vendor->email ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">{{ $vendor->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">No vendors registered yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
