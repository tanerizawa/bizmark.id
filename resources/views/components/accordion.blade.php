@props([
    'items' => [],
    'class' => '',
])

@php
    $spaceClass = 'space-y-3';
    if ($class) $spaceClass .= ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $spaceClass]) }}>
    @foreach($items as $idx => $item)
        @php
            $question = is_array($item) ? ($item['question'] ?? $item['q'] ?? '') : '';
            $answer = is_array($item) ? ($item['answer'] ?? $item['a'] ?? '') : '';
            $open = $loop->first;
        @endphp
        <details class="faq-item" @if($open) open @endif>
            <summary class="faq-toggle">
                <span>{{ $question }}</span>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </summary>
            <div class="faq-content">
                <div class="faq-content-inner">{!! nl2br(e($answer)) !!}</div>
            </div>
        </details>
    @endforeach
</div>
