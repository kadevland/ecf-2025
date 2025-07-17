@props(['active' => false])

@php
$classes = $active ?? false
            ? 'block px-4 py-2 text-sm text-gray-700 bg-gray-100'
            : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>