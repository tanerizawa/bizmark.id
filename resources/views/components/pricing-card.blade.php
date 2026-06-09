@props([
    'name' => '',
    'price' => '',
    'priceSuffix' => '',
    'features' => [],
    'cta' => '',
    'ctaUrl' => '',
    'featured' => false,
    'variant' => 'gold', // gold | navy | emerald
    'class' => '',
])

@php
    $tierClass = 'tier-editorial';
    if ($featured) $tierClass .= ' tier-editorial--featured';
    if ($class) $tierClass .= ' ' . $class;
@endphp

<div class="{{ $tierClass }}">
    @if($name)
        <div class="text-sm font-bold uppercase tracking-widest"
             style="color: var(--accent-text);">{{ $name }}</div>
    @endif
    @if($price)
        <div class="tier-editorial__price">
            {{ $price }}
            @if($priceSuffix)
                <span class="tier-editorial__price-suffix">{{ $priceSuffix }}</span>
            @endif
        </div>
    @endif
    @if(!empty($features))
        <ul class="flex flex-col gap-2.5 my-5">
            @foreach($features as $feature)
                <li class="flex items-start gap-2.5 text-sm" style="color: var(--text-secondary);">
                    <i class="fas fa-check flex-shrink-0 mt-0.5" style="color: var(--accent); font-size: .75rem;" aria-hidden="true"></i>
                    <span>{{ $feature }}</span>
                </li>
            @endforeach
        </ul>
    @endif
    @if($cta && $ctaUrl)
        <div class="mt-auto pt-4">
            <a href="{{ $ctaUrl }}" class="btn btn-gold w-full justify-center">
                {{ $cta }}
            </a>
        </div>
    @endif
</div>
