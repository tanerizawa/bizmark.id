@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'hint' => null,
    'size' => 'md',
    'compact' => false,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'maxLength' => null,
    'resize' => 'vertical',
    'class' => '',
])

@php
$textareaBase = 'block w-full rounded-xl border transition-all duration-200 font-sans';
$textareaBase .= ' bg-[var(--surface)] text-[var(--text-primary)] placeholder:text-[var(--text-tertiary)]';
$textareaBase .= ' focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-glow)] focus:outline-none';

$resizeClasses = [
    'none' => 'resize-none',
    'vertical' => 'resize-y',
    'both' => 'resize',
];

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
$readonlyClasses = $readonly ? 'cursor-default' : '';

$classes = trim($textareaBase . ' ' . $resizeClasses[$resize] . ' ' . ($compact ? $compactClasses : $sizeClasses[$size]) . ' ' . $stateBorder . ' ' . $disabledClasses . ' ' . $readonlyClasses . ' ' . $class);

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
            @if($maxLength)
                <span class="text-xs text-[var(--text-tertiary)] font-normal ml-2">(max. {{ $maxLength }} karakter)</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($maxLength) maxlength="{{ $maxLength }}" @endif
        {{ $attributes->merge(['class' => $classes]) }}
    >{{ $value }}</textarea>

    @if($error)
        <p class="mt-1.5 text-sm text-[var(--color-error)] flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i> {{ $error }}
        </p>
    @endif

    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-[var(--text-tertiary)]">{{ $hint }}</p>
    @endif
</div>
