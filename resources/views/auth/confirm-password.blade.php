<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Confirm Password</h2>
        <p class="text-gray-500 text-sm mt-1">This is a secure area. Please confirm your password before continuing.</p>
    </div>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>
        <x-primary-button class="w-full justify-center">{{ __('Confirm') }}</x-primary-button>
    </form>
</x-guest-layout>