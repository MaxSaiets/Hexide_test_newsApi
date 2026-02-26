@props(['href' => '#', 'active' => false])

@php
    $classes = ($active ?? false)
        ? 'rounded-md px-3 py-2 text-sm font-medium shadow-sm ring-2 ring-white/20 bg-blue-600 text-white hover:bg-blue-700'
        : 'rounded-md px-3 py-2 text-sm font-medium transition-colors duration-200 bg-blue-500 text-white hover:bg-blue-600';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>