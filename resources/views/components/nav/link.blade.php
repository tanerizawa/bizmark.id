@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
    'badge' => null,
    'badgeVariant' => 'neutral',
    'class' => '',
])

@php
    $baseClasses = 'flex items-center justify-between px-3 py-2 rounded-[10px] text-sm font-medium transition-all duration-200';

    $stateClasses = $active
        ? 'bg-[var(--apple-blue,#007AFF)] text-white'
        : 'text-[var(--dark-text-secondary)] hover:bg-[var(--dark-bg-tertiary,#2C2C2E)] hover:text-[var(--dark-text-primary)]';

    $badgeVariants = [
        'neutral' => 'bg-[var(--dark-bg-tertiary,#2C2C2E)] text-[var(--dark-text-secondary)]',
        'alert' => 'bg-[#EF4444] text-white',
        'warning' => 'bg-[#F59E0B] text-white',
        'primary' => 'bg-[var(--apple-blue,#007AFF)] text-white',
    ];

    $activeBadge = $active ? 'bg-white text-[var(--apple-blue,#007AFF)]' : ($badgeVariants[$badgeVariant] ?? '');
    $classes = trim("{$baseClasses} {$stateClasses} {$class}");
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    <span class="flex items-center gap-3">
        @if($icon)
            <i class="{{ $icon }} w-5 text-center text-sm"></i>
        @endif
        <span>{{ $slot }}</span>
    </span>
    @if($badge !== null)
        <span class="px-1.5 py-0.5 text-[0.6875rem] font-semibold rounded-full {{ $activeBadge }}">
            {{ $badge }}
        </span>
    @endif
</a>
