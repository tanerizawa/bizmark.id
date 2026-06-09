{{-- DEPRECATED: Use <x-ui.card variant="elevated"> instead. This component will be removed in a future update. --}}
<div {{ $attributes->merge(['class' => 'card-elevated rounded-apple-lg p-4 hover-lift']) }}>
    {{ $slot }}
</div>
