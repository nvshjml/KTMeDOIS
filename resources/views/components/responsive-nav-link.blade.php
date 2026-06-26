@props(['active'])
@php $classes = $active ?? false
    ? 'block px-4 py-2 text-sm font-semibold text-blue-900 bg-yellow-400 rounded-lg'
    : 'block px-4 py-2 text-sm text-white/70 hover:bg-white/10 hover:text-white rounded-lg transition';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>