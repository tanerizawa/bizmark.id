@props([
    'keys' => '',
    'size' => 'md',
    'class' => '',
])

@php
$sizeClasses = [
    'sm' => 'px-1.5 py-0.5 text-[0.6875rem]',
    'md' => 'px-2 py-1 text-xs',
];

$classes = trim(
    'inline-flex items-center justify-center font-mono leading-none rounded-md border ' .
    'bg-[var(--surface-cool)] text-[var(--text-secondary)] border-[var(--border-subtle)] ' .
    $sizeClasses[$size] . ' ' .
    $class
);
@endphp

<kbd {{ $attributes->merge(['class' => $classes]) }}>
    {{ $keys }}
</kbd>
