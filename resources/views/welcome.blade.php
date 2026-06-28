<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KTM eDOIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen" style="background: linear-gradient(135deg, #002b6b 0%, #003580 50%, #004bad 100%)">
    <button
        type="button"
        onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('login') }}'"
        class="fixed top-5 left-5 z-50 inline-flex items-center gap-2 rounded-lg border border-white/40 bg-white/95 px-4 py-2 text-sm font-bold text-blue-900 shadow-lg transition hover:bg-yellow-300 focus:outline-none focus:ring-4 focus:ring-yellow-300/40"
    >
        &larr; Back
    </button>

    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-4 mb-6">
                <div class="bg-yellow-400 rounded-2xl p-4">
                    <svg class="w-12 h-12 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white mb-2">KTM eDOIS</h1>
            <p class="text-blue-200 text-lg">Electronic Delivery Order & Invoice System</p>
            <p class="text-blue-300 text-sm mt-1">Keretapi Tanah Melayu Berhad</p>
        </div>

        <div class="flex gap-4">
            @if (Route::has('login'))
                @auth
                <a href="{{ url('/dashboard') }}" class="px-8 py-3 bg-yellow-400 text-blue-900 font-bold rounded-xl hover:bg-yellow-300 transition">
                    Go to Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="px-8 py-3 bg-yellow-400 text-blue-900 font-bold rounded-xl hover:bg-yellow-300 transition">
                    Login to System
                </a>
                @endauth
            @endif
        </div>

        <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl w-full">
            @foreach(['Vendor Module', 'DO Management', 'Invoice Module', 'Admin & Reports'] as $module)
            <div class="bg-white/10 backdrop-blur rounded-xl p-4 text-center text-white border border-white/20">
                <div class="text-sm font-semibold">{{ $module }}</div>
            </div>
            @endforeach
        </div>

        <p class="text-blue-400 text-xs mt-12">© {{ date('Y') }} Keretapi Tanah Melayu Berhad · All rights reserved</p>
    </div>
</body>
</html>
