@props([
    'type' => 'submit',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark
    'size' => 'md', // sm, md, lg
    'disabled' => false,
    'class' => '',
])

@php
    // Map variants to Bootstrap classes
    $variantClasses = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
        'light' => 'btn-light',
        'dark' => 'btn-dark',
        'link' => 'btn-link',
    ];

    // Map sizes to Bootstrap classes
    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $baseClasses = 'btn inline-flex items-center font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150';
    $variantClass = $variantClasses[$variant] ?? $variantClasses['primary'];
    $sizeClass = $sizeClasses[$size] ?? '';
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "$baseClasses $variantClass $sizeClass $class"]) }}
    {{ $disabled ? 'disabled' : '' }}
>
    {{ $slot }}
</button>