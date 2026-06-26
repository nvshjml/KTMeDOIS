@props(['disabled' => false])
<input {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition w-full']) !!}>