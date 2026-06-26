@props(['messages'])
@if ($messages)
    @foreach ((array) $messages as $message)
    <p {{ $attributes->merge(['class' => 'mt-1 text-xs text-red-600 flex items-center gap-1']) }}>
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        {{ $message }}
    </p>
    @endforeach
@endif