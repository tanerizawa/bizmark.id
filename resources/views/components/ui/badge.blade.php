@props([
    'variant' => 'neutral',
    'size' => 'sm',
    'pill' => true,
    'dot' => false,
    'status' => null,
    'class' => '',
])

@php
$base = 'inline-flex items-center gap-1.5 font-semibold leading-none';

$statusClasses = [
    'draft'       => 'bg-gray-100 text-gray-600 border border-gray-200',
    'submitted'   => 'bg-blue-50 text-blue-700 border border-blue-200',
    'in_progress' => 'bg-cyan-50 text-cyan-700 border border-cyan-200',
    'approved'    => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    'rejected'    => 'bg-red-50 text-red-700 border border-red-200',
    'expiring'    => 'bg-amber-50 text-amber-700 border border-amber-200',
    'expired'     => 'bg-red-50 text-red-700 border border-red-200',
    'quoted'      => 'bg-purple-50 text-purple-700 border border-purple-200',
    'paid'        => 'bg-amber-50 text-amber-700 border border-amber-200',
    'completed'   => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
    'cancelled'   => 'bg-red-50 text-red-700 border border-red-200',
];

$variantClasses = [
    'neutral' => 'bg-[var(--surface-cool)] text-[var(--text-secondary)] border border-[var(--border-subtle)]',
    'primary' => 'bg-[var(--accent-glow)] text-[var(--accent-text)] border border-[var(--accent)]/15',
    'success' => 'bg-[var(--color-success-bg)] text-[var(--color-success)] border border-[var(--color-success)]/20',
    'warning' => 'bg-[var(--color-warning-bg)] text-[var(--color-warning)] border border-[var(--color-warning)]/20',
    'danger'  => 'bg-[var(--color-error-bg)] text-[var(--color-error)] border border-[var(--color-error)]/20',
    'info'    => 'bg-[var(--color-info-bg)] text-[var(--color-info)] border border-[var(--color-info)]/20',
];

$sizeClasses = [
    'xs' => 'px-2 py-0.5 text-[0.6875rem]',
    'sm' => 'px-2.5 py-1 text-xs',
    'md' => 'px-3 py-1.5 text-sm',
];

$dotColors = [
    'draft' => 'bg-gray-400',
    'submitted' => 'bg-blue-500',
    'in_progress' => 'bg-cyan-500',
    'approved' => 'bg-emerald-500',
    'rejected' => 'bg-red-500',
    'expiring' => 'bg-amber-500',
    'expired' => 'bg-red-500',
    'neutral' => 'bg-gray-400',
    'primary' => 'bg-[var(--accent)]',
    'success' => 'bg-[var(--color-success)]',
    'warning' => 'bg-[var(--color-warning)]',
    'danger' => 'bg-[var(--color-error)]',
    'info' => 'bg-[var(--color-info)]',
];

$resolvedVariant = $status ? ($statusClasses[$status] ?? $variantClasses['neutral']) : ($variantClasses[$variant] ?? $variantClasses['neutral']);

$radius = $pill ? 'rounded-full' : 'rounded-md';
$classes = trim($base . ' ' . $resolvedVariant . ' ' . $sizeClasses[$size] . ' ' . $radius . ' ' . $class);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full inline-block {{ $dotColors[$status ?? $variant] ?? 'bg-gray-400' }}"></span>
    @endif
    {{ $slot }}
</span>
