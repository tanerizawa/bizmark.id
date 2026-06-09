@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading...',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'compact' => false,
    'class' => '',
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-xl transition-all duration-200 cursor-pointer select-none border no-underline';
$base .= ' focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--accent)]';
$base .= ' disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none';
$base .= ' active:scale-[0.98]';

$variants = [
    'primary'   => 'bg-[var(--accent)] text-white hover:brightness-110 shadow-sm border-transparent',
    'secondary' => 'bg-transparent text-[var(--text-primary)] border-2 border-[var(--border-medium)] hover:border-[var(--accent)] hover:text-[var(--accent-text)]',
    'ghost'     => 'bg-transparent text-[var(--text-secondary)] hover:bg-[var(--accent-glow)] hover:text-[var(--accent-text)] border-transparent',
    'outline'   => 'bg-transparent text-[var(--accent-text)] border border-[var(--accent)]/30 hover:bg-[var(--accent-glow)]',
    'danger'    => 'bg-[var(--color-error)] text-white hover:brightness-110 border-transparent',
    'gold'      => 'bg-[var(--accent)] text-white shadow-[0_4px_16px_rgba(var(--accent-rgb),0.35)] hover:shadow-[0_8px_24px_rgba(var(--accent-rgb),0.45)] border-transparent',
    'tools'     => 'bg-[var(--tools)] text-white hover:bg-[var(--tools-dark)] border-transparent',
];

$sizes = [
    'xs' => 'px-2.5 py-1 text-xs rounded-lg gap-1.5',
    'sm' => 'px-3 py-1.5 text-sm min-h-[36px]',
    'md' => 'px-5 py-2.5 text-[0.9375rem] min-h-[44px]',
    'lg' => 'px-7 py-3.5 text-base min-h-[52px]',
];

$compactClasses = $compact ? 'px-2.5 py-1 text-xs rounded-lg' : '';

$classes = trim("{$base} {$variants[$variant]} " . ($compact ? $compactClasses : $sizes[$size]) . " {$class}");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}
       @if($disabled || $loading) aria-disabled="true" tabindex="-1" @endif>
        @if($loading)
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ $loadingText }}</span>
        @else
            @if($icon)
                <i class="fas fa-{{ $icon }}"></i>
            @endif
            {{ $slot }}
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}
            @if($disabled || $loading) disabled @endif>
        @if($loading)
            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24" fill="none">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <span>{{ $loadingText }}</span>
        @else
            @if($icon)
                <i class="fas fa-{{ $icon }}"></i>
            @endif
            {{ $slot }}
        @endif
    </button>
@endif
