{{-- DEPRECATED: Use <x-ui.button> instead. This component will be removed in a future update. --}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading...',
    'type' => 'button',
    'href' => null,
    'class' => '',
])

@php
    $baseClasses = 'btn';

    $variantClasses = [
        'primary' => 'btn-gold',
        'gold' => 'btn-gold',
        'ghost' => 'btn-ghost',
        'outline' => 'btn-outline-primary',
        'success' => 'btn-success',
        'tools' => 'btn-success',
    ];

    $sizeClasses = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];

    $classes = trim("{$baseClasses} {$variantClasses[$variant]} {$sizeClasses[$size]} {$class}");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled || $loading) disabled @endif
    >
        @if($loading)
            <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
            <span>{{ $loadingText }}</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
