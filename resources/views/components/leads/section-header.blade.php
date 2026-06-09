@props([
    'eyebrow' => '',
    'title' => '',
    'count' => '',
    'countLabel' => '',
    'emptyCount' => '0',
])

<div class="lead-table-header">
    <div>
        @if($eyebrow)
        <p class="lead-table-title">{{ $eyebrow }}</p>
        @endif
        @if($title)
        <h3 class="lead-table-heading">{{ $title }}</h3>
        @endif
    </div>
    <span class="lead-table-count">
        {{ $count ?: $emptyCount }}
    </span>
</div>
