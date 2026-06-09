@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">🔎 Competitor Detail</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">{{ $analysis->keyword }}</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Re-Analyze Keyword -->
            <form action="{{ route('admin.seo.competitor-reanalyze', $analysis->id) }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Analyzing...';">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">
                    🔄 Re-Analyze
                </button>
            </form>
            <!-- Comprehensive Smart Fix Page (always available — creates article if none) -->
            <a href="{{ route('admin.seo.competitor-smart-fix', $analysis->id) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition"
               style="background: linear-gradient(135deg, rgba(52,199,89,0.2), rgba(10,132,255,0.2)); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                🤖 Smart Fix{{ $article ? '' : ' + Buat Artikel' }}
            </a>
            @if($article)
            <!-- Quick Fix (existing) -->
            <form action="{{ route('admin.seo.competitor-apply-fix', $analysis->id) }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Fixing...';">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    ⚡ Quick Fix
                </button>
            </form>
            <!-- Verify Score -->
            <form action="{{ route('admin.seo.competitor-verify', $analysis->id) }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Scoring...';">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1); border: 1px solid rgba(175,82,222,0.3);">
                    ✅ Verify
                </button>
            </form>
            @endif
            <a href="{{ route('admin.seo.competitors') }}" class="btn-secondary-sm">← Kembali</a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</pre>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(255,59,48,0.1); border: 1px solid rgba(255,59,48,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(255,59,48,1);">{{ session('error') }}</pre>
    </div>
    @endif

    <!-- Data Source Banner -->
    @if($analysis->data_source === 'searxng')
    <div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.2);">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">SearXNG</span>
        <span class="text-xs" style="color: rgba(52,199,89,0.8);">Data real dari mesin pencari via SearXNG (open-source). Domain, posisi, dan URL terverifikasi.</span>
    </div>
    @elseif($analysis->data_source === 'google_serp')
    <div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.2);">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">Google SERP</span>
        <span class="text-xs" style="color: rgba(52,199,89,0.8);">Data kompetitor berdasarkan hasil pencarian Google yang sebenarnya. Domain, posisi, dan URL terverifikasi.</span>
    </div>
    @else
    <div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(255,149,0,0.08); border: 1px solid rgba(255,149,0,0.2);">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">AI Estimasi</span>
        <span class="text-xs" style="color: rgba(255,149,0,0.8);">⚠️ Data dihasilkan oleh AI — domain, volume, & posisi mungkin tidak akurat. Aktifkan SearXNG atau Google API untuk data real.</span>
    </div>
    @endif

    <!-- Action Workflow Bar -->
    <div class="mb-6 p-4 rounded-apple-xl flex flex-wrap items-center gap-3" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(255,255,255,0.06);">
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest" style="color: rgba(235,235,245,0.45);">Workflow:</div>
        <div class="flex items-center gap-1.5">
            <span class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-bold" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">① Analisis</span>
            <span style="color: rgba(235,235,245,0.3);">→</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-bold {{ $article ? '' : 'opacity-40' }}" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">② Smart Fix</span>
            <span style="color: rgba(235,235,245,0.3);">→</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-bold {{ $seoScore ? '' : 'opacity-40' }}" style="background: rgba(175,82,222,0.2); color: rgba(175,82,222,1);">③ Verifikasi</span>
            <span style="color: rgba(235,235,245,0.3);">→</span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-bold opacity-40" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">④ Re-Analyze</span>
        </div>
        @if(!$article)
        <span class="ml-auto text-xs" style="color: rgba(52,199,89,1);">🤖 Smart Fix akan otomatis membuat artikel baru untuk keyword ini</span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Overview -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">Overview</h3>
            @php
                $posColor = $analysis->our_position ? ($analysis->our_position <= 10 ? 'rgba(52,199,89,1)' : ($analysis->our_position <= 30 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)')) : 'rgba(255,59,48,1)';
                $diffBg = $analysis->difficulty === 'easy' ? 'rgba(52,199,89,0.15)' : ($analysis->difficulty === 'medium' ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                $diffColor = $analysis->difficulty === 'easy' ? 'rgba(52,199,89,1)' : ($analysis->difficulty === 'medium' ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');

                $staleDays = $analysis->analyzed_at->diffInDays(now());
                $isStale = $staleDays > 7;
            @endphp
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Keyword</dt>
                    <dd class="text-sm font-medium text-white">{{ $analysis->keyword }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Our Position</dt>
                    <dd class="text-sm font-medium" style="color: {{ $posColor }};">
                        {{ $analysis->our_position ? '#' . $analysis->our_position : 'Not Ranking' }}
                        @if($previousAnalysis)
                            @php
                                $oldPos = $previousAnalysis->our_position;
                                $newPos = $analysis->our_position;
                                $delta = null;
                                if ($oldPos && $newPos) {
                                    $delta = $oldPos - $newPos; // positive = improved
                                }
                            @endphp
                            @if($delta !== null)
                                <span class="text-xs ml-1" style="color: {{ $delta > 0 ? 'rgba(52,199,89,1)' : ($delta < 0 ? 'rgba(255,59,48,1)' : 'rgba(235,235,245,0.4)') }};">
                                    {{ $delta > 0 ? '↑' . $delta : ($delta < 0 ? '↓' . abs($delta) : '—') }}
                                </span>
                            @endif
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Search Volume</dt>
                    <dd class="text-sm font-medium text-white">{{ number_format($analysis->search_volume ?? 0) }}/bulan</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Difficulty</dt>
                    <dd><span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium"
                              style="background: {{ $diffBg }}; color: {{ $diffColor }};">
                        {{ ucfirst($analysis->difficulty) }}
                    </span></dd>
                </div>
                @if($analysis->our_url)
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Our URL</dt>
                    <dd class="text-sm truncate max-w-[200px]"><a href="{{ $analysis->our_url }}" target="_blank" style="color: rgba(10,132,255,1);">{{ $analysis->our_url }}</a></dd>
                </div>
                @endif
                <div class="flex justify-between">
                    <dt class="text-sm" style="color: rgba(235,235,245,0.55);">Analyzed</dt>
                    <dd class="text-sm text-white">
                        {{ $analysis->analyzed_at->format('d M Y H:i') }}
                        @if($isStale)
                            <span class="inline-flex items-center ml-1 px-1.5 py-0.5 rounded text-[10px] font-semibold" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);">{{ $staleDays }}d ago</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Linked Article & SEO Score -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h3 class="text-sm font-semibold text-white mb-4">📄 Linked Article</h3>
            @if($article)
            <div class="space-y-4">
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <a href="/blog/{{ $article->slug }}" target="_blank" class="font-medium text-sm hover:underline" style="color: rgba(10,132,255,1);">{{ $article->title }}</a>
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.45);">{{ Str::limit(strip_tags($article->content), 120) }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs" style="color: rgba(235,235,245,0.5);">
                        <span>📂 {{ ucfirst($article->category ?? 'general') }}</span>
                        <span>👁 {{ number_format($article->views_count ?? 0) }} views</span>
                        <span>📅 {{ $article->published_at?->format('d M Y') }}</span>
                    </div>
                </div>

                @if($seoScore)
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">SEO Score</span>
                        @php
                            $scoreColor = $seoScore->total_score >= 80 ? 'rgba(52,199,89,1)' : ($seoScore->total_score >= 60 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                            $scoreBg = $seoScore->total_score >= 80 ? 'rgba(52,199,89,0.15)' : ($seoScore->total_score >= 60 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-apple text-sm font-bold" style="background: {{ $scoreBg }}; color: {{ $scoreColor }};">
                            {{ $seoScore->total_score }} ({{ $seoScore->grade }})
                        </span>
                    </div>
                    <!-- Score bar -->
                    <div class="w-full h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.08);">
                        <div class="h-full rounded-full transition-all" style="width: {{ $seoScore->total_score }}%; background: {{ $scoreColor }};"></div>
                    </div>

                    @if(!empty($seoScore->recommendations))
                    <div class="mt-3">
                        <p class="text-xs font-semibold mb-1.5" style="color: rgba(235,235,245,0.45);">Issue yang perlu diperbaiki ({{ count($seoScore->recommendations) }}):</p>
                        <ul class="space-y-1">
                            @foreach(array_slice($seoScore->recommendations, 0, 5) as $rec)
                            <li class="text-xs flex items-start gap-1.5" style="color: rgba(235,235,245,0.5);">
                                <span style="color: rgba(255,149,0,1);">⚠</span> {{ $rec }}
                            </li>
                            @endforeach
                            @if(count($seoScore->recommendations) > 5)
                            <li class="text-xs" style="color: rgba(235,235,245,0.35);">+{{ count($seoScore->recommendations) - 5 }} lainnya...</li>
                            @endif
                        </ul>
                    </div>
                    @endif

                    <p class="text-[10px] mt-2" style="color: rgba(235,235,245,0.3);">Scored: {{ $seoScore->scored_at?->format('d M Y H:i') ?? 'N/A' }}</p>
                </div>
                @else
                <div class="p-3 rounded-apple-lg text-center" style="background: rgba(255,214,10,0.05); border: 1px dashed rgba(255,214,10,0.2);">
                    <p class="text-xs" style="color: rgba(255,214,10,0.8);">Artikel belum di-score. Klik "✅ Verify" untuk score.</p>
                </div>
                @endif
            </div>
            @else
            <div class="p-6 rounded-apple-lg text-center" style="background: rgba(52,199,89,0.05); border: 1px dashed rgba(52,199,89,0.2);">
                <div class="text-2xl mb-2">🤖</div>
                <p class="text-sm font-medium text-white">Belum ada artikel terkait</p>
                <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">Belum ada artikel yang menargetkan keyword "{{ $analysis->keyword }}".</p>
                <a href="{{ route('admin.seo.competitor-smart-fix', $analysis->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 mt-3 rounded-apple text-xs font-bold transition"
                   style="background: linear-gradient(135deg, rgba(52,199,89,0.2), rgba(10,132,255,0.2)); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    🤖 Smart Fix + Buat Artikel Baru →
                </a>
                <p class="text-[11px] mt-2" style="color: rgba(235,235,245,0.35);">AI akan membuat artikel komprehensif berdasarkan analisis kompetitor</p>
            </div>
            @endif
        </div>

        <!-- Top Competitors -->
        <div class="card-elevated rounded-apple-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">🏢 Top Competitors</h3>
                @if(in_array($analysis->data_source, ['searxng', 'google_serp']))
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">{{ $analysis->data_source === 'searxng' ? 'SearXNG' : 'Google SERP' }}</span>
                @else
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);">AI Estimasi</span>
                @endif
            </div>
            @if(!empty($analysis->top_competitors))
            <div class="space-y-3">
                @foreach($analysis->top_competitors as $comp)
                <div class="p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.03);">
                    <div class="flex items-center gap-2">
                        <p class="font-medium text-sm text-white">{{ $comp['domain'] ?? $comp['title'] ?? 'Unknown' }}</p>
                        @if(!empty($comp['position']))
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">#{{ $comp['position'] }}</span>
                        @endif
                    </div>
                    @if(!empty($comp['title']))
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">{{ $comp['title'] }}</p>
                    @endif
                    @if(!empty($comp['url']))
                    <a href="{{ $comp['url'] }}" target="_blank" rel="noopener" class="text-[10px] mt-0.5 block truncate hover:underline" style="color: rgba(10,132,255,0.6);">{{ $comp['url'] }}</a>
                    @endif
                    @if(!empty($comp['snippet']))
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.4);">{{ Str::limit($comp['snippet'], 150) }}</p>
                    @endif
                    @if(!empty($comp['strengths']))
                    <p class="text-xs mt-1" style="color: rgba(52,199,89,1);">💪 {{ $comp['strengths'] }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Tidak ada data kompetitor.</p>
            @endif
        </div>
    </div>

    <!-- Content Gaps & Recommendations Full Width -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <div class="card-elevated rounded-apple-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">📋 Content Gaps</h3>
                @if(!empty($analysis->content_gaps))
                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-bold" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1);">{{ count($analysis->content_gaps) }} gaps</span>
                @endif
            </div>
            @if(!empty($analysis->content_gaps))
            <ul class="space-y-2">
                @foreach($analysis->content_gaps as $gap)
                <li class="flex items-start gap-2 p-2 rounded-apple-lg" style="background: rgba(255,59,48,0.05);">
                    <span style="color: rgba(255,59,48,1);" class="mt-0.5 flex-shrink-0">●</span>
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $gap }}</span>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Tidak ada content gap terdeteksi. 🎉</p>
            @endif
        </div>

        <div class="card-elevated rounded-apple-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-white">💡 Recommendations</h3>
                @if(!empty($analysis->recommendations))
                <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-bold" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);">{{ count($analysis->recommendations) }} items</span>
                @endif
            </div>
            @if(!empty($analysis->recommendations))
            <ol class="space-y-2">
                @foreach($analysis->recommendations as $i => $rec)
                <li class="flex items-start gap-2 p-2.5 rounded-apple-lg" style="background: rgba(255,149,0,0.06);">
                    <span class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full text-xs font-bold" style="background: rgba(255,149,0,0.25); color: rgba(255,149,0,1);">{{ $i + 1 }}</span>
                    <span class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $rec }}</span>
                </li>
                @endforeach
            </ol>

            <div class="mt-4 p-3 rounded-apple-lg flex items-center justify-between" style="background: linear-gradient(135deg, rgba(52,199,89,0.06), rgba(10,132,255,0.06)); border: 1px dashed rgba(52,199,89,0.2);">
                <span class="text-xs" style="color: rgba(52,199,89,0.8);">💡 {{ $article ? 'Terapkan semua rekomendasi secara komprehensif dengan AI.' : 'Buat artikel baru & terapkan semua rekomendasi dengan AI.' }}</span>
                <a href="{{ route('admin.seo.competitor-smart-fix', $analysis->id) }}"
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-apple text-xs font-bold transition"
                   style="background: linear-gradient(135deg, rgba(52,199,89,0.2), rgba(10,132,255,0.2)); color: rgba(52,199,89,1);">
                    🤖 Smart Fix{{ $article ? '' : ' + Buat Artikel' }} →
                </a>
            </div>
            @else
            <p class="text-sm" style="color: rgba(235,235,245,0.55);">Belum ada rekomendasi.</p>
            @endif
        </div>
    </div>

    <!-- Previous Analysis Comparison (if exists) -->
    @if($previousAnalysis)
    <div class="mt-6 card-elevated rounded-apple-xl p-6">
        <h3 class="text-sm font-semibold text-white mb-4">📊 Perbandingan dengan Analisis Sebelumnya</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="color: rgba(235,235,245,0.5);">
                        <th class="px-4 py-2 text-left">Metrik</th>
                        <th class="px-4 py-2 text-center">{{ $previousAnalysis->analyzed_at->format('d M Y') }}</th>
                        <th class="px-4 py-2 text-center">{{ $analysis->analyzed_at->format('d M Y') }}</th>
                        <th class="px-4 py-2 text-center">Perubahan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $metrics = [
                            ['label' => 'Position', 'old' => $previousAnalysis->our_position, 'new' => $analysis->our_position, 'invert' => true],
                            ['label' => 'Search Volume', 'old' => $previousAnalysis->search_volume, 'new' => $analysis->search_volume, 'invert' => false],
                            ['label' => 'Content Gaps', 'old' => count($previousAnalysis->content_gaps ?? []), 'new' => count($analysis->content_gaps ?? []), 'invert' => true],
                            ['label' => 'Recommendations', 'old' => count($previousAnalysis->recommendations ?? []), 'new' => count($analysis->recommendations ?? []), 'invert' => true],
                        ];
                    @endphp
                    @foreach($metrics as $m)
                    @php
                        $oldV = $m['old'];
                        $newV = $m['new'];
                        $diff = ($oldV !== null && $newV !== null) ? $newV - $oldV : null;
                        $improved = $diff !== null && ($m['invert'] ? $diff < 0 : $diff > 0);
                        $worsened = $diff !== null && ($m['invert'] ? $diff > 0 : $diff < 0);
                    @endphp
                    <tr class="border-t border-white/5">
                        <td class="px-4 py-2.5 font-medium text-white">{{ $m['label'] }}</td>
                        <td class="px-4 py-2.5 text-center" style="color: rgba(235,235,245,0.5);">{{ $oldV !== null ? ($m['label'] === 'Position' ? '#' . $oldV : number_format($oldV)) : '—' }}</td>
                        <td class="px-4 py-2.5 text-center text-white">{{ $newV !== null ? ($m['label'] === 'Position' ? '#' . $newV : number_format($newV)) : '—' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            @if($diff !== null && $diff !== 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold"
                                      style="background: {{ $improved ? 'rgba(52,199,89,0.15)' : 'rgba(255,59,48,0.15)' }}; color: {{ $improved ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)' }};">
                                    {{ $improved ? '✓' : '✗' }} {{ $diff > 0 ? '+' : '' }}{{ $m['label'] === 'Position' ? ($diff > 0 ? '↓' . $diff : '↑' . abs($diff)) : number_format($diff) }}
                                </span>
                            @else
                                <span style="color: rgba(235,235,245,0.3);">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
