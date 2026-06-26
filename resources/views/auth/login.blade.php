<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Welcome Back</h2>
        <p class="text-gray-500 text-sm mt-1">Sign in to your eDOIS account</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Username/Email -->
        <div>
            <x-input-label for="email" :value="__('Email / Username')" class="text-sm font-semibold text-gray-700 mb-1.5" />
            <x-text-input id="email" class="block w-full" type="email" name="email"
                :value="old('email')" required autofocus autocomplete="username"
                placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700 mb-1.5" />
            <div class="relative">
                <x-text-input id="password" class="block w-full pr-12" type="password"
                    name="password" required autocomplete="current-password"
                    placeholder="Enter your password" />
                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" aria-label="Toggle password">
                    <svg id="eye-icon" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember"
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
            <label for="remember_me" class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</label>
        </div>

        <!-- Login button -->
        <x-primary-button class="w-full justify-center">
            {{ __('Sign In') }}
        </x-primary-button>

        <!-- Forgot password -->
        <div class="text-center">
            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
                class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                {{ __('Forgot Password?') }}
            </a>
            @endif
        </div>
    </form>

    <!-- Demo credentials note -->
    <div class="mt-6 border-t border-gray-100 pt-5">
        <p class="text-xs text-gray-400 text-center mb-3 font-medium uppercase tracking-wide">Demo Accounts</p>
        <div class="grid grid-cols-3 gap-2 text-xs">
            <div class="bg-red-50 border border-red-100 rounded-lg p-2 text-center">
                <div class="font-semibold text-red-700">Admin</div>
                <div class="text-gray-500">admin@ktm.com</div>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded-lg p-2 text-center">
                <div class="font-semibold text-purple-700">Finance</div>
                <div class="text-gray-500">finance@ktm.com</div>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-2 text-center">
                <div class="font-semibold text-blue-700">Vendor</div>
                <div class="text-gray-500">vendor@ktm.com</div>
            </div>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Secured with HTTPS · Session expires after 30 mins
    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    </script>
</x-guest-layout>