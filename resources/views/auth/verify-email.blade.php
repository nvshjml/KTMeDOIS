<x-guest-layout>
    <div class="mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Verify Your Email</h2>
        <p class="text-sm text-gray-500 mt-2">Thanks for signing up! Please verify your email by clicking the link we sent to your address.</p>
    </div>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            A new verification link has been sent to your email address.
        </div>
    @endif
    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center">{{ __('Resend Verification Email') }}</x-primary-button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-secondary-button class="w-full justify-center" onclick="this.closest('form').submit()">{{ __('Log Out') }}</x-secondary-button>
        </form>
    </div>
</x-guest-layout>