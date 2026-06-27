<x-app-layout>
    <x-slot name="header">Notifications</x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800">Finance Notifications</h2>
        <p class="text-sm text-gray-500">System alerts and updates for the finance team</p>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notif)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-start gap-4 {{ $notif->status === 'unread' ? 'border-l-4 border-l-blue-500' : '' }}">
            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-gray-800">{{ $notif->type }}</div>
                <div class="text-sm text-gray-500 mt-0.5">{{ $notif->content }}</div>
                <div class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</div>
            </div>
            @if($notif->status === 'unread')
            <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 mt-1.5"></span>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm">No notifications yet.</p>
        </div>
        @endforelse
    </div>
</x-app-layout>
