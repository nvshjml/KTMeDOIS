@props(['href' => '#'])
<a {{ $attributes->merge(['href' => $href, 'class' => 'block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition text-start']) }}>
    {{ $slot }}
</a>