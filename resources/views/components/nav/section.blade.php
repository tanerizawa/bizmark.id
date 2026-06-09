@props([
    'title' => '',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'mb-6 ' . $class]) }}>
    @if($title)
        <h6 class="px-3 py-2 text-[0.6875rem] font-semibold text-[var(--dark-text-tertiary)] uppercase tracking-[0.05em] mb-2">
            {{ $title }}
        </h6>
    @endif
    <nav class="flex flex-col gap-1">
        {{ $slot }}
    </nav>
</div>
