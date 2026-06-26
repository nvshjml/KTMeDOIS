<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-5 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold text-sm bg-white hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500']) }}>
    {{ $slot }}
</button>