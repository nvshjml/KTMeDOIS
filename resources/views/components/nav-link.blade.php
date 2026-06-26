@props(['active'])
@php $classes = $active ?? false
    ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg bg-yellow-400 text-blue-900 font-semibold text-sm'
    : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/60 hover:bg-white/10 hover:text-white transition text-sm';
@endphp
<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>