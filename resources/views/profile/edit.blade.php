<x-app-layout>
    <x-slot name="header">My Profile</x-slot>

    <div class="max-w-2xl space-y-5">
        <!-- Profile Info Card -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold" style="background-color: #003580">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ auth()->user()->name }}</h2>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Change Password Card -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Change Password</h3>
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account Card -->
        <div class="bg-white rounded-xl border border-red-100 shadow-sm p-6">
            <h3 class="font-semibold text-red-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-500 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>