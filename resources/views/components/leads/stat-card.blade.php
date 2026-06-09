@props([
    'label' => '',
    'value' => '',
    'sub' => '',
    'color' => 'var(--dark-text-primary)',
    'bg' => 'transparent',
    'icon' => 'fa-circle',
    'variant' => 'primary', // primary | secondary
])

@php
    $isPrimary = $variant === 'primary';
    $cardClass = $isPrimary ? 'lead-stat-card' : 'lead-stat-card-secondary';
    $valueSize = $isPrimary
        ? 'font-size:2rem;font-weight:800;margin:4px 0 2px;line-height:1'
        : 'font-size:1.5rem;font-weight:700;margin:2px 0 0;line-height:1.1';
@endphp

<div class="{{ $cardClass }}" style="background:linear-gradient(135deg,color-mix(in srgb,{{ $bg }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $bg }} 25%,var(--dark-separator))">
    <div class="lead-stat-icon" style="color:{{ $color }}"><i class="fas {{ $icon }}"></i></div>
    <p class="lead-stat-label" style="color:{{ $color }}">{{ $label }}</p>
    <p style="{{ $valueSize }};color:{{ $color }}">{{ $value }}</p>
    @if($sub)
    <p class="lead-stat-sub">{{ $sub }}</p>
    @endif
</div>
