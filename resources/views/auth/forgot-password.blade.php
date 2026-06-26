<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Password Recovery</h2>
        <p class="text-gray-500 text-sm mt-1">Enter your registered email and we will send a reset link.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block w-full mt-1" type="email"
                name="email" :value="old('email')" required autofocus placeholder="Enter your registered email" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>
        <x-primary-button class="w-full justify-center">
            {{ __('Send Reset Link') }}
        </x-primary-button>
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Back to Login</a>
        </div>
    </form>
</x-guest-layout>