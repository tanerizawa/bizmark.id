@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-white">📋 Report: {{ ucfirst($report->period) }}</h1>
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">{{ $report->period_start->format('d M Y') }} — {{ $report->period_end->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.seo.reports') }}" class="btn-secondary-sm">← Reports</a>
    </div>

    @php $m = $report->metrics; @endphp

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <p class="text-lg font-bold text-white">{{ $m['total_published'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Total Published</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <p class="text-2xl font-bold" style="color: rgba(52,199,89,1);">+{{ $m['new_articles'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">New Articles</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <p class="text-2xl font-bold" style="color: rgba(10,132,255,1);">{{ number_format($m['period_views'] ?? 0) }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Period Views</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            @php $g = $m['views_growth_pct'] ?? 0; @endphp
            <p class="text-2xl font-bold" style="color: {{ $g >= 0 ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)' }};">{{ $g >= 0 ? '+' : '' }}{{ $g }}%</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Views Growth</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <p class="text-2xl font-bold" style="color: rgba(175,82,222,1);">{{ $m['avg_seo_score'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Avg SEO Score</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 text-center">
            <p class="text-2xl font-bold" style="color: rgba(52,199,89,1);">{{ $m['sitemap_urls'] ?? 0 }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Sitemap URLs</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Alerts -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">🚨 Alerts</h3>
            @if(empty($report->alerts))
                <p class="text-sm" style="color: rgba(52,199,89,1);">✅ Tidak ada alert — semua berjalan normal</p>
            @else
                <div class="space-y-2">
                    @foreach($report->alerts as $alert)
                    <div class="flex items-center gap-3 p-3 rounded-apple-lg"
                         style="background: {{ $alert['level'] === 'warning' ? 'rgba(255,149,0,0.08)' : 'rgba(10,132,255,0.08)' }};">
                        <span>{{ $alert['level'] === 'warning' ? '⚠️' : 'ℹ️' }}</span>
                        <span class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $alert['message'] }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Infrastructure -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">🏗️ Infrastructure</h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <p class="text-lg font-bold text-white">{{ $m['keyword_clusters'] ?? 0 }}</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">Keyword Clusters</p>
                </div>
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <p class="text-lg font-bold text-white">{{ $m['topic_clusters'] ?? 0 }}</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">Topic Clusters</p>
                </div>
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <p class="text-lg font-bold text-white">{{ $m['excellent_seo_count'] ?? 0 }}</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">Excellent SEO</p>
                </div>
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <p class="text-lg font-bold" style="color: rgba(255,59,48,1);">{{ $m['needs_work_count'] ?? 0 }}</p>
                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">Needs Work</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Articles -->
    @if(!empty($report->top_articles))
    <div class="card-elevated rounded-apple-xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">🏆 Top Articles This Period</h3>
        <div class="space-y-3">
            @foreach($report->top_articles as $i => $art)
            <div class="flex items-center gap-3">
                <span class="flex-shrink-0 w-7 h-7 flex items-center justify-center rounded-full text-xs font-bold"
                      style="background: {{ $i < 3 ? 'rgba(255,214,10,0.15)' : 'rgba(142,142,147,0.15)' }}; color: {{ $i < 3 ? 'rgba(255,214,10,1)' : 'rgba(142,142,147,1)' }};">
                    {{ $i + 1 }}
                </span>
                <div class="flex-1">
                    <p class="text-sm font-medium text-white">{{ $art['title'] }}</p>
                </div>
                <span class="text-sm font-semibold" style="color: rgba(10,132,255,1);">{{ number_format($art['views']) }} views</span>
                @if(isset($art['seo_score']))
                @php
                    $asBg = $art['seo_score'] >= 70 ? 'rgba(52,199,89,0.15)' : 'rgba(255,214,10,0.15)';
                    $asColor = $art['seo_score'] >= 70 ? 'rgba(52,199,89,1)' : 'rgba(255,214,10,1)';
                @endphp
                <span class="text-xs px-2 py-1 rounded-apple" style="background: {{ $asBg }}; color: {{ $asColor }};">
                    SEO: {{ $art['seo_score'] }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
