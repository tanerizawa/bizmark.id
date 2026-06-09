@props([
    'eyebrow' => null,
    'heading' => null,
    'description' => null,
    'headingClass' => 'display-md',
    'container' => 'container-wide',
    'background' => null,
    'id' => null,
    'class' => '',
    'headingLevel' => 'h2',
])

@php
    $sectionClass = 'section-v2';

    if ($background === 'warm') {
        $sectionClass .= ' section-premium';
    } elseif ($background === 'ink') {
        $sectionClass .= ' section-ink';
    } elseif ($background === 'cool') {
        $sectionClass .= ' bg-[var(--surface-cool)]';
    }

    if ($class) {
        $sectionClass .= ' ' . $class;
    }
@endphp

<section @if($id) id="{{ $id }}" @endif
         class="{{ $sectionClass }}"
         @if($heading) aria-labelledby="{{ Str::slug($heading) }}-heading" @endif>
    <div class="{{ $container }}">
        @if($eyebrow || $heading || $description)
            <div class="max-w-2xl mb-8 md:mb-10">
                @if($eyebrow)
                    <span class="eyebrow mb-3 block">{{ $eyebrow }}</span>
                @endif
                @if($heading)
                    <{{ $headingLevel }} id="{{ Str::slug($heading) }}-heading"
                        class="{{ $headingClass }} mb-3">
                        {{ $heading }}
                    </{{ $headingLevel }}>
                @endif
                @if($description)
                    <p class="section-description">{{ $description }}</p>
                @endif
            </div>
        @endif
        {{ $slot }}
    </div>
</section>
