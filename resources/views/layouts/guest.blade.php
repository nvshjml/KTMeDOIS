<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KTM eDOIS — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex" style="background: linear-gradient(135deg, #002b6b 0%, #003580 50%, #004bad 100%);">

    <!-- Left branding panel -->
    <div class="hidden lg:flex flex-1 flex-col justify-center items-center p-12 text-white">
        <div class="max-w-md w-full">
            <div class="flex items-center gap-4 mb-8">
                <div class="bg-yellow-400 rounded-xl p-3 flex-shrink-0">
                    <svg class="w-10 h-10 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-black tracking-tight">KTM</div>
                    <div class="text-sm text-blue-200 tracking-widest">BERHAD</div>
                </div>
            </div>
            <h1 class="text-4xl font-bold mb-4 leading-tight">
                Electronic Delivery Order & Invoice System
            </h1>
            <p class="text-blue-200 text-lg mb-8">
                A one-stop digital platform for streamlined vendor document submission, real-time tracking, and transparent financial processing.
            </p>
            <div class="space-y-3">
                @foreach(['Secure role-based access control', 'Real-time DO & Invoice tracking', 'Automated workflow & notifications', 'Full audit trail for compliance'] as $feature)
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded-full bg-yellow-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-blue-100">{{ $feature }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-10 pt-8 border-t border-blue-400/30">
                <p class="text-blue-300 text-sm italic">"Peneraju Pengangkutan Rel Negara"</p>
                <p class="text-blue-400 text-xs mt-1">© {{ date('Y') }} Keretapi Tanah Melayu Berhad. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="flex-1 flex items-center justify-center p-6 lg:bg-white lg:rounded-l-3xl">
        <div class="w-full max-w-md">
            <!-- Mobile header -->
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8 text-white">
                <div class="bg-yellow-400 rounded-xl p-2">
                    <svg class="w-8 h-8 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black">KTM eDOIS</div>
                    <div class="text-xs text-blue-200">KERETAPI TANAH MELAYU BERHAD</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl lg:shadow-none p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>