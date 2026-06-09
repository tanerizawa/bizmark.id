@props([
    'name' => '',
    'type' => 'text',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'hint' => null,
    'prefix' => null,
    'suffix' => null,
    'size' => 'md',
    'compact' => false,
    'required' => false,
    'disabled' => false,
    'leadingIcon' => null,
    'trailingIcon' => null,
    'class' => '',
])

@php
$inputBase = 'block w-full rounded-xl border transition-all duration-200 font-sans';
$inputBase .= ' bg-[var(--surface)] text-[var(--text-primary)] placeholder:text-[var(--text-tertiary)]';
$inputBase .= ' focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-glow)] focus:outline-none';

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm',
    'md' => 'px-4 py-2.5 text-sm',
    'lg' => 'px-5 py-3.5 text-base',
];

$compactClasses = $compact ? 'px-2.5 py-1.5 text-[0.8125rem] rounded-md' : '';

$stateBorder = $error
    ? 'border-[var(--color-error)] ring-1 ring-[var(--color-error)]'
    : 'border-[var(--border-medium)]';

$disabledClasses = $disabled ? 'opacity-50 cursor-not-allowed' : '';

$leadingPad = $prefix ? '' : ($leadingIcon ? ($size === 'sm' ? 'pl-8' : ($size === 'lg' ? 'pl-11' : 'pl-9')) : '');

$inputClasses = trim($inputBase . ' ' . ($compact ? $compactClasses : $sizeClasses[$size]) . ' ' . $stateBorder . ' ' . $disabledClasses . ' ' . $leadingPad . ' ' . $class);

$labelClasses = $compact
    ? 'block text-xs font-medium text-[var(--text-secondary)] mb-1'
    : 'block text-sm font-semibold text-[var(--text-primary)] mb-1.5';
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-[var(--color-error)] ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex items-center">
        @if($prefix)
            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-[var(--border-medium)] bg-[var(--surface-warm)] text-[var(--text-secondary)] text-sm min-h-[44px]">
                {{ $prefix }}
            </span>
        @endif
        @if($leadingIcon && !$prefix)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[var(--text-tertiary)]">
                <i class="{{ $leadingIcon }}"></i>
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            {{ $attributes->merge(['class' => $inputClasses . ($prefix ? ' rounded-l-none' : '') . ($suffix ? ' rounded-r-none' : '')]) }}
        />

        @if($suffix)
            <span class="inline-flex items-center px-3 rounded-r-xl border border-l-0 border-[var(--border-medium)] bg-[var(--surface-warm)] text-[var(--text-secondary)] text-sm min-h-[44px]">
                {{ $suffix }}
            </span>
        @endif
        @if($trailingIcon && !$suffix)
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-[var(--text-tertiary)]">
                <i class="{{ $trailingIcon }}"></i>
            </div>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-[var(--color-error)] flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i>
            {{ $error }}
        </p>
    @endif

    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-[var(--text-tertiary)]">{{ $hint }}</p>
    @endif
</div>
