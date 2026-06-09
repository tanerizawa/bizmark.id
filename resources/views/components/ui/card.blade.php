@props([
    'variant' => 'elevated',
    'padding' => 'md',
    'hover' => true,
    'class' => '',
])

@php
$base = 'rounded-xl border transition-all duration-200';
$base .= $hover ? ' hover:-translate-y-0.5' : '';

$variants = [
    'elevated'    => 'bg-[var(--surface-raised)] border-[var(--border-subtle)] shadow-sm hover:shadow-md hover:border-[var(--accent)]/25',
    'bordered'    => 'bg-transparent border-[var(--border-medium)] hover:border-[var(--accent)]/35',
    'flat'        => 'bg-[var(--surface-warm)] border-transparent hover:border-[var(--border-subtle)]',
    'interactive' => 'bg-[var(--surface-raised)] border-[var(--border-subtle)] cursor-pointer hover:shadow-lg hover:border-[var(--accent)]/30',
    'featured'    => 'bg-[var(--surface-raised)] border-[var(--accent)]/30 shadow-md shadow-[var(--accent-glow)]',
];

$paddings = [
    'none' => '',
    'sm' => 'p-4',
    'md' => 'p-5 md:p-6',
    'lg' => 'p-6 md:p-8',
];

$classes = trim("{$base} {$variants[$variant]} {$paddings[$padding]} {$class}");
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @isset($header)
        <div class="mb-4 pb-4 border-b border-[var(--border-subtle)]">
            {{ $header }}
        </div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-[var(--border-subtle)]">
            {{ $footer }}
        </div>
    @endisset
</div>
