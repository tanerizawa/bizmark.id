@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white">🔍 SEO Audit: {{ \Illuminate\Support\Str::limit($article->title, 60) }}</h1>
            <p class="text-sm mt-1">
                <a href="{{ config('app.url') }}/blog/{{ $article->slug }}" target="_blank" style="color: rgba(10,132,255,1);">{{ config('app.url') }}/blog/{{ $article->slug }}</a>
            </p>
        </div>
        <div class="flex gap-2">
            @if(!empty($competitorAnalysis))
            <a href="{{ route('admin.seo.competitor-smart-fix', $competitorAnalysis->id) }}"
               class="inline-flex items-center gap-1 px-3 py-2 rounded-apple text-sm font-medium transition"
               style="background: linear-gradient(135deg, rgba(52,199,89,0.15), rgba(10,132,255,0.15)); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                🤖 Smart Fix
            </a>
            @endif
            <form action="{{ route('admin.seo.fix-single', $article->id) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-apple text-sm font-medium transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    🛠️ Auto-Fix SEO
                </button>
            </form>
            <a href="{{ route('admin.seo.scores') }}" class="btn-secondary-sm">← Kembali</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</pre>
    </div>
    @endif

    <!-- Overall Score -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            $sc = $score->total_score;
            $sBg = $sc >= 80 ? 'rgba(52,199,89,0.15)' : ($sc >= 60 ? 'rgba(10,132,255,0.15)' : ($sc >= 40 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)'));
            $sColor = $sc >= 80 ? 'rgba(52,199,89,1)' : ($sc >= 60 ? 'rgba(10,132,255,1)' : ($sc >= 40 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)'));
        @endphp
        <div class="card-elevated rounded-apple-xl p-6 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full text-xl font-bold"
                 style="background: {{ $sBg }}; color: {{ $sColor }};">
                {{ $score->total_score }}
            </div>
            <p class="text-lg font-bold mt-2 text-white">Grade: {{ $score->grade }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Last scored: {{ $score->scored_at?->diffForHumans() }}</p>
        </div>
        <div class="card-elevated rounded-apple-xl p-6">
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Views</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(10,132,255,1);">{{ number_format($article->views_count) }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">{{ $article->reading_time ?? '-' }} min read</p>
        </div>
        <div class="card-elevated rounded-apple-xl p-6">
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Published</p>
            <p class="text-sm font-semibold text-white mt-1">{{ $article->published_at?->format('d M Y') }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Updated {{ $article->updated_at->diffForHumans() }}</p>
        </div>
        <div class="card-elevated rounded-apple-xl p-6">
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Rekomendasi</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,149,0,1);">{{ count($score->recommendations ?? []) }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">perbaikan tersedia</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Factor Breakdown -->
        <div class="lg:col-span-2 card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">📊 Breakdown per Faktor</h3>
            <div class="space-y-4">
                @foreach($score->factors ?? [] as $key => $factor)
                @php
                    $pct = $factor['max'] > 0 ? round($factor['score'] / $factor['max'] * 100) : 0;
                    $barColor = $pct >= 80 ? 'rgba(52,199,89,1)' : ($pct >= 50 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                    $labels = [
                        'title' => '📝 Title',
                        'meta_description' => '📋 Meta Description',
                        'meta_keywords' => '🏷️ Meta Keywords',
                        'content' => '📄 Content Quality',
                        'headings' => '📑 Heading Structure',
                        'internal_links' => '🔗 Internal Links',
                        'images' => '🖼️ Image Optimization',
                        'slug' => '🔗 URL/Slug',
                        'freshness' => '🕐 Freshness',
                        'excerpt_schema' => '📝 Excerpt & Schema',
                    ];
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium" style="color: rgba(235,235,245,0.6);">{{ $labels[$key] ?? ucfirst($key) }}</span>
                        <span class="font-bold" style="color: {{ $barColor }};">{{ $factor['score'] }}/{{ $factor['max'] }}</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-white/10">
                        <div class="h-2.5 rounded-full transition-all" style="width: {{ $pct }}%; background: {{ $barColor }};"></div>
                    </div>
                    @if(!empty($factor['issues']))
                    <div class="mt-1 space-y-0.5">
                        @foreach($factor['issues'] as $issue)
                        <p class="text-xs" style="color: rgba(255,59,48,0.8);">⚠ {{ $issue }}</p>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">💡 Rekomendasi</h3>
            @if(empty($score->recommendations))
                <p class="text-sm" style="color: rgba(52,199,89,1);">✅ Tidak ada rekomendasi — artikel sudah optimal!</p>
            @else
            <div class="space-y-3">
                @foreach($score->recommendations as $i => $rec)
                <div class="flex items-start gap-2 p-2 rounded-apple-lg" style="background: rgba(255,149,0,0.08);">
                    <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full text-xs font-bold" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">{{ $i + 1 }}</span>
                    <p class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $rec }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- View Trends -->
    <div class="card-elevated rounded-apple-xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">📈 View Trends (30 hari)</h3>
        <div class="h-48">
            @if(empty($viewTrends))
                <div class="flex items-center justify-center h-full text-sm" style="color: rgba(235,235,245,0.4);">
                    Belum ada data view trends untuk artikel ini
                </div>
            @else
                <canvas id="articleTrendsCanvas"></canvas>
            @endif
        </div>
    </div>
</div>

@if(!empty($viewTrends))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('articleTrendsCanvas');
    if (!ctx) return;
    const data = @json($viewTrends);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(d => d.date),
            datasets: [{
                label: 'Views',
                data: data.map(d => d.views),
                backgroundColor: 'rgba(10,132,255,0.6)',
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { maxTicksLimit: 10, font: { size: 10 }, color: 'rgba(235,235,245,0.5)' } },
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { font: { size: 10 }, color: 'rgba(235,235,245,0.5)' } }
            }
        }
    });
});
</script>
@endpush
@endif
@endsection
