@props([
    'title' => '',
    'icon' => null,
    'active' => false,
    'class' => '',
])

@php
    $baseClasses = 'mb-1';

    $toggleClasses = 'w-full flex items-center justify-between px-3 py-2 rounded-[10px] text-sm font-medium bg-transparent border-none text-left cursor-pointer transition-all duration-200';

    $toggleState = $active
        ? 'bg-[rgba(0,122,255,0.12)] text-[var(--apple-blue,#007AFF)]'
        : 'text-[var(--dark-text-secondary)] hover:bg-[var(--dark-bg-tertiary,#2C2C2E)] hover:text-[var(--dark-text-primary)]';

    $classes = trim("{$baseClasses} {$class}");
@endphp

<div
    x-data="{ open: {{ $active ? 'true' : 'false' }} }"
    :class="{ '{{ addcslashes($toggleState, '[]()\/') }}': open }"
    class="{{ $classes }}"
>
    <button
        type="button"
        @click="open = !open"
        :class="{
            'bg-[rgba(0,122,255,0.12)] text-[var(--apple-blue,#007AFF)]': open,
            'text-[var(--dark-text-secondary)] hover:bg-[var(--dark-bg-tertiary,#2C2C2E)] hover:text-[var(--dark-text-primary)]': !open
        }"
        class="{{ $toggleClasses }}"
    >
        <span class="flex items-center gap-3">
            @if($icon)
                <i class="{{ $icon }} w-5 text-center text-sm"></i>
            @endif
            <span>{{ $title }}</span>
        </span>
        <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
           :style="open ? 'transform: rotate(180deg)' : ''"></i>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="pl-8 mt-1"
    >
        {{ $slot }}
    </div>
</div>
