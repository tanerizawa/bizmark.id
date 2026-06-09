@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'class' => '',
])

@php
    $baseClasses = 'flex items-center gap-3 px-3 py-2 rounded-lg text-[0.8125rem] transition-all duration-200 mb-0.5';

    $stateClasses = $active
        ? 'bg-[var(--apple-blue,#007AFF)] text-white'
        : 'text-[var(--dark-text-secondary)] hover:bg-[var(--dark-bg-tertiary,#2C2C2E)] hover:text-[var(--dark-text-primary)]';

    $classes = trim("{$baseClasses} {$stateClasses} {$class}");
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} w-4 text-center text-xs"></i>
    @endif
    <span>{{ $slot }}</span>
</a>
