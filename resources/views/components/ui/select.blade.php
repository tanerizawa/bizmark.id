@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'options' => [],
    'value' => '',
    'error' => null,
    'size' => 'md',
    'compact' => false,
    'required' => false,
    'disabled' => false,
    'helperText' => '',
    'leadingIcon' => null,
    'multiple' => false,
    'searchable' => false,
    'class' => '',
])

@php
$selectBase = 'block w-full rounded-xl border transition-all duration-200 font-sans appearance-none bg-no-repeat';
$selectBase .= ' bg-[var(--surface)] text-[var(--text-primary)]';
$selectBase .= ' focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-glow)] focus:outline-none';

$chevron = "data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e";

$sizeClasses = [
    'sm' => 'px-3 py-1.5 text-sm pr-8 bg-[length:16px_12px] bg-[right_0.5rem_center]',
    'md' => 'px-4 py-2.5 text-sm pr-10 bg-[length:16px_12px] bg-[right_0.75rem_center]',
    'lg' => 'px-5 py-3.5 text-base pr-12 bg-[length:16px_12px] bg-[right_1rem_center]',
];

$compactClasses = $compact ? 'px-2.5 py-1.5 text-[0.8125rem] rounded-md pr-8 bg-[length:16px_12px] bg-[right_0.5rem_center]' : '';

$stateBorder = $error
    ? 'border-[var(--color-error)] ring-1 ring-[var(--color-error)]'
    : 'border-[var(--border-medium)]';

$selectClasses = trim($selectBase . ' ' . ($compact ? $compactClasses : $sizeClasses[$size]) . ' ' . $stateBorder . ' bg-[url(\"' . $chevron . '\")] ' . $class);

$labelClasses = $compact
    ? 'block text-xs font-medium text-[var(--text-secondary)] mb-1'
    : 'block text-sm font-semibold text-[var(--text-primary)] mb-1.5';

$normalizedOptions = [];
foreach ($options as $key => $option) {
    if (is_array($option) && isset($option['value'])) {
        $normalizedOptions[] = $option;
    } elseif (is_array($option) && isset($option['label'])) {
        $normalizedOptions[] = $option;
    } else {
        $normalizedOptions[] = ['value' => $key, 'label' => $option];
    }
}
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

    <div class="relative">
        @if($leadingIcon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-[var(--text-tertiary)] z-10">
                <i class="{{ $leadingIcon }}"></i>
            </div>
        @endif

        @if($searchable)
            <div
                x-data="{
                    open: false,
                    query: '',
                    selectedValue: '{{ $value }}',
                    get filteredOptions() {
                        return {{ json_encode($normalizedOptions) }}.filter(o =>
                            o.label.toLowerCase().includes(this.query.toLowerCase())
                        );
                    },
                    select(val) {
                        this.selectedValue = val;
                        this.open = false;
                        this.query = '';
                        $refs.hiddenInput.value = val;
                        $refs.hiddenInput.dispatchEvent(new Event('change'));
                    }
                }"
                class="relative"
            >
                <button
                    type="button"
                    @click="open = !open"
                    @keydown.escape="open = false"
                    :aria-expanded="open"
                    aria-haspopup="listbox"
                    class="{{ $selectClasses }} w-full text-left"
                >
                    <span x-text="selectedValue ? ({{ json_encode($normalizedOptions) }}.find(o => o.value == selectedValue)?.label || '{{ $placeholder }}') : '{{ $placeholder }}'"
                          :class="{'text-[var(--text-tertiary)]': !selectedValue}"
                          class="{{ $value ? '' : 'text-[var(--text-tertiary)]' }}"
                    >{{ $value ? (collect($normalizedOptions)->firstWhere('value', $value)['label'] ?? $placeholder) : $placeholder }}</span>
                </button>

                <input type="hidden" name="{{ $name }}" x-ref="hiddenInput" value="{{ $value }}" />

                <div
                    x-show="open"
                    @click.outside="open = false"
                    x-transition
                    class="absolute z-50 mt-1 w-full rounded-xl border border-[var(--border-medium)] bg-[var(--surface-raised)] shadow-lg"
                    role="listbox"
                >
                    <div class="p-2">
                        <input
                            type="text"
                            x-model="query"
                            placeholder="Cari..."
                            class="w-full rounded-lg border border-[var(--border-subtle)] px-3 py-2 text-sm bg-[var(--surface)] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--accent-glow)]"
                            @click.stop
                        />
                    </div>

                    <template x-if="filteredOptions.length === 0">
                        <div class="px-3 py-4 text-sm text-[var(--text-tertiary)] text-center">
                            Tidak ada hasil
                        </div>
                    </template>

                    <template x-for="(option, index) in filteredOptions" :key="index">
                        <button
                            type="button"
                            @click="select(option.value)"
                            :class="{
                                'bg-[var(--accent-glow)] text-[var(--accent-text)]': selectedValue == option.value,
                                'hover:bg-[var(--surface-cool)] text-[var(--text-primary)]': selectedValue != option.value,
                            }"
                            class="w-full text-left px-3 py-2 text-sm rounded-lg transition-colors duration-150"
                            role="option"
                            :aria-selected="selectedValue == option.value"
                            x-text="option.label"
                        ></button>
                    </template>
                </div>
            </div>
        @else
            <select
                name="{{ $name }}"
                id="{{ $name }}"
                @if($multiple) multiple @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                {{ $attributes->merge(['class' => $selectClasses]) }}
            >
                @if($placeholder)
                    <option value="" disabled selected>{{ $placeholder }}</option>
                @endif
                @foreach($normalizedOptions as $option)
                    <option
                        value="{{ $option['value'] }}"
                        {{ old($name, $value) == $option['value'] ? 'selected' : '' }}
                    >
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    @if($error)
        <p class="mt-1.5 text-sm text-[var(--color-error)] flex items-center gap-1">
            <i class="fas fa-exclamation-circle"></i> {{ $error }}
        </p>
    @endif

    @if($helperText && !$error)
        <p class="mt-1.5 text-xs text-[var(--text-tertiary)]">{{ $helperText }}</p>
    @endif
</div>
