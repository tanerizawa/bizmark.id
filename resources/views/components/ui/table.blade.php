@props([
    'columns' => [],
    'rows' => [],
    'variant' => 'default',
    'striped' => false,
    'hoverable' => true,
    'sortable' => false,
    'sortField' => null,
    'sortDirection' => 'asc',
    'emptyMessage' => 'Tidak ada data',
    'showHeader' => true,
    'class' => '',
    'wrapperClass' => '',
    'cellRenderers' => [],
])

@php
$tableClasses = 'w-full text-left border-collapse';

$variantClasses = [
    'default' => [
        'th' => 'px-4 py-3 text-xs font-semibold uppercase tracking-wider',
        'td' => 'px-4 py-4',
    ],
    'compact' => [
        'th' => 'px-3 py-2 text-xs font-semibold uppercase tracking-wider',
        'td' => 'px-3 py-3',
    ],
];
@endphp

<div class="overflow-x-auto rounded-xl border border-[var(--border-subtle)] {{ $wrapperClass }}">
    <table class="{{ $tableClasses }} {{ $class }}">
        @if($showHeader && count($columns) > 0)
            <thead class="bg-[var(--surface-cool)] border-b border-[var(--border-subtle)]">
                <tr>
                    @foreach($columns as $column)
                        <th
                            scope="col"
                            class="{{ $variantClasses[$variant]['th'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['class'] ?? '' }} text-[var(--text-secondary)]"
                        >
                            @if($sortable && ($column['sortable'] ?? true))
                                <button
                                    type="button"
                                    @click="$dispatch('sort', { field: '{{ $column['key'] }}' })"
                                    class="inline-flex items-center gap-1 bg-transparent border-none cursor-pointer text-[var(--text-secondary)] p-0 hover:text-[var(--text-primary)]"
                                >
                                    <span>{{ $column['label'] }}</span>
                                    <span class="flex flex-col shrink-0">
                                        <svg class="w-2.5 h-2.5 {{ $sortField === $column['key'] && $sortDirection === 'asc' ? 'text-[var(--accent)]' : 'text-[var(--text-tertiary)]' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"/>
                                        </svg>
                                        <svg class="w-2.5 h-2.5 -mt-1 {{ $sortField === $column['key'] && $sortDirection === 'desc' ? 'text-[var(--accent)]' : 'text-[var(--text-tertiary)]' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <span class="text-[var(--text-secondary)]">{{ $column['label'] }}</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif

        <tbody>
            @forelse($rows as $row)
                <tr class="border-b border-[var(--border-subtle)] last:border-b-0 {{ $striped ? 'even:bg-[var(--surface-cool)]' : '' }} {{ $hoverable ? 'hover:bg-[color-mix(in_srgb,var(--accent)_4%,var(--surface))]' : '' }}">
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }} {{ $column['cellClass'] ?? '' }}">
                            @php $cellKey = $column['key'] ?? ''; @endphp

                            @if(isset($cellRenderers[$cellKey]) && is_callable($cellRenderers[$cellKey]))
                                {!! $cellRenderers[$cellKey]($row) !!}
                            @elseif(isset($column['render']) && is_callable($column['render']))
                                {!! $column['render']($row) !!}
                            @elseif(isset($column['component']))
                                <x-dynamic-component
                                    :component="$column['component']"
                                    :row="$row"
                                    :value="data_get($row, $column['key'])"
                                />
                            @elseif(isset($column['template']))
                                @php
                                    $templateContext = array_merge(
                                        ['row' => $row],
                                        $column['templateData'] ?? []
                                    );
                                @endphp
                                {!! \Illuminate\Support\Facades\Blade::render($column['template'], $templateContext) !!}
                            @else
                                <span class="text-sm text-[var(--text-primary)]">
                                    {{ data_get($row, $column['key']) }}
                                </span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) }}" class="py-12 px-4 text-center text-sm text-[var(--text-secondary)]">
                        {!! $emptyMessage !!}
                    </td>
                </tr>
            @endforelse
        </tbody>

        @isset($tfoot)
            <tfoot class="bg-[var(--surface-cool)] border-t border-[var(--border-subtle)]">
                <tr>
                    @foreach($columns as $column)
                        <td class="{{ $variantClasses[$variant]['td'] }} text-{{ $column['align'] ?? 'left' }}">
                            @php
                                $footerSlotName = 'footer-' . $column['key'];
                            @endphp
                            @if(isset($footerRenderers[$footerSlotName]) && is_callable($footerRenderers[$footerSlotName]))
                                {!! $footerRenderers[$footerSlotName]($rows) !!}
                            @elseif(isset(${$footerSlotName}))
                                {{ ${$footerSlotName}($rows) }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            </tfoot>
        @endisset
    </table>
</div>
