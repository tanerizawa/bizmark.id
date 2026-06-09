@props([
    'title' => '',
    'url' => '',
    'excerpt' => '',
    'category' => '',
    'date' => '',
    'image' => null,
    'featured' => false,
    'class' => '',
])

@php
    $classes = $featured ? 'article-card featured' : 'article-card';
    if ($class) $classes .= ' ' . $class;
@endphp

<a href="{{ $url }}" class="{{ $classes }}" @if($attributes->has('wire:navigate')) wire:navigate @endif>
    @if($image)
        <div class="article-image">
            <img src="{{ $image }}" alt="{{ $title }}" loading="lazy">
        </div>
    @endif
    <div>
        @if($category || $date)
            <div class="article-meta">
                @if($category)
                    <span class="article-cat">{{ $category }}</span>
                @endif
                @if($date)
                    <span>{{ $date }}</span>
                @endif
            </div>
        @endif
        <h3 class="article-title">{{ $title }}</h3>
        @if($excerpt)
            <p class="article-excerpt">{{ $excerpt }}</p>
        @endif
    </div>
</a>
