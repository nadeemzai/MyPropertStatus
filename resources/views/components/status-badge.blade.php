@props(['variant' => 'neutral'])

@php
    $variants = [
        'success' => 'bg-success-50 text-success-700',
        'warning' => 'bg-warning-50 text-warning-700',
        'danger' => 'bg-danger-50 text-danger-700',
        'info' => 'bg-info-50 text-info-700',
        'neutral' => 'bg-gray-100 text-gray-600',
    ];
    $classes = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "rounded-full px-2 py-0.5 text-xs font-medium {$classes}"]) }}>
    {{ $slot }}
</span>
