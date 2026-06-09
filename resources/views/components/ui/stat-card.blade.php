@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'trendLabel' => '',
    'icon' => null,
    'iconBg' => null,
    'variant' => 'default',
    'compact' => false,
    'class' => '',
])

@php
$base = 'flex items-center gap-4 border transition-all duration-200';
$hover = 'hover:-translate-y-0.5 hover:shadow-md';

$variants = [
    'default' => 'bg-[var(--surface-raised)] border-[var(--border-subtle)] rounded-xl',
    'primary' => 'bg-[var(--surface-raised)] border-l-4 border-l-[var(--accent)] border-[var(--border-subtle)] rounded-xl',
    'success' => 'bg-[var(--surface-raised)] border-l-4 border-l-[var(--color-success)] border-[var(--border-subtle)] rounded-xl',
    'warning' => 'bg-[var(--surface-raised)] border-l-4 border-l-[var(--color-warning)] border-[var(--border-subtle)] rounded-xl',
    'danger'  => 'bg-[var(--surface-raised)] border-l-4 border-l-[var(--color-error)] border-[var(--border-subtle)] rounded-xl',
];

$padding = $compact ? 'p-3 rounded-lg' : 'p-5';
$iconSize = $compact ? 'w-9 h-9 rounded-lg text-base' : 'w-12 h-12 rounded-xl text-xl';
$valueSize = $compact ? 'text-xl' : 'text-[1.875rem]';
$labelSize = $compact ? 'text-xs' : 'text-sm';

$iconDefaults = [
    'default' => 'bg-[var(--accent-glow)] text-[var(--accent-text)]',
    'primary' => 'bg-[var(--accent-glow)] text-[var(--accent-text)]',
    'success' => 'bg-[var(--color-success-bg)] text-[var(--color-success)]',
    'warning' => 'bg-[var(--color-warning-bg)] text-[var(--color-warning)]',
    'danger'  => 'bg-[var(--color-error-bg)] text-[var(--color-error)]',
];

$iconClass = $iconBg ?? $iconDefaults[$variant] ?? $iconDefaults['default'];
$classes = trim("{$base} {$hover} {$variants[$variant]} {$padding} {$class}");

$trendUp = $trend !== null && $trend >= 0;
$trendDown = $trend !== null && $trend < 0;
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <div class="flex-shrink-0 {{ $iconSize }} flex items-center justify-center {{ $iconClass }}">
            <i class="{{ $icon }}"></i>
        </div>
    @endif

    <div class="flex-1 min-w-0">
        <p class="{{ $labelSize }} text-[var(--text-secondary)] truncate">{{ $label }}</p>
        <p class="mt-1 {{ $valueSize }} font-bold tracking-tight text-[var(--text-primary)]">{{ $value }}</p>

        @if($trend !== null)
            <div class="mt-1.5 flex items-center gap-1.5">
                @if($trend > 0)
                    <i class="fa-solid fa-arrow-up text-xs text-[var(--color-success)]"></i>
                    <span class="text-xs font-medium text-[var(--color-success)]">+{{ number_format($trend, 1) }}%</span>
                @elseif($trend < 0)
                    <i class="fa-solid fa-arrow-down text-xs text-[var(--color-error)]"></i>
                    <span class="text-xs font-medium text-[var(--color-error)]">{{ number_format($trend, 1) }}%</span>
                @else
                    <span class="text-xs font-medium text-[var(--text-tertiary)]">0.0%</span>
                @endif
                @if($trendLabel)
                    <span class="text-xs text-[var(--text-tertiary)]">{{ $trendLabel }}</span>
                @endif
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
