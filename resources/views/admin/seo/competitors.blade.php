@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-white">🔍 Competitive Intelligence</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Analisis kompetitor dan peluang keyword</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.seo.run-competitor-analyze') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Analyzing...';">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    🔍 Batch Analyze
                </button>
            </form>
            <a href="{{ route('admin.seo.dashboard') }}" class="btn-secondary-sm">← Dashboard</a>
        </div>
    </div>

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

    <!-- Analyze Custom Keyword -->
    <div class="mb-6 card-elevated rounded-apple-xl p-4">
        <form action="{{ route('admin.seo.competitor-analyze-keyword') }}" method="POST" class="flex flex-wrap items-end gap-3" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').textContent='⏳ Analyzing...';">
            @csrf
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235,235,245,0.55);">Analyze Keyword Baru</label>
                <input type="text" name="keyword" required minlength="3" maxlength="200"
                       placeholder="contoh: jasa pengurusan AMDAL Jakarta"
                       class="w-full px-3 py-2 rounded-apple text-sm text-white placeholder-white/30"
                       style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-apple text-sm font-semibold transition" style="background: rgba(10,132,255,0.9); color: #fff;">
                🔍 Analyze
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <a href="{{ route('admin.seo.competitors') }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total Analyzed</p>
            <p class="text-lg font-bold text-white mt-1">{{ $summary['total_analyzed'] }}</p>
        </a>
        <a href="{{ route('admin.seo.competitors', ['position' => 'top10']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ request('position') === 'top10' ? 'ring-1 ring-green-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Ranking Top 10</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(52,199,89,1);">{{ $summary['ranking_top10'] }}</p>
        </a>
        <a href="{{ route('admin.seo.competitors', ['position' => 'opportunity']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ request('position') === 'opportunity' ? 'ring-1 ring-yellow-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Opportunity (11-30)</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,214,10,1);">{{ $summary['opportunity'] }}</p>
        </a>
        <a href="{{ route('admin.seo.competitors', ['position' => 'unranked']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ request('position') === 'unranked' ? 'ring-1 ring-red-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Not Ranking</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,59,48,1);">{{ $summary['not_ranking'] }}</p>
        </a>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Avg Position</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(10,132,255,1);">{{ $summary['avg_position'] }}</p>
        </div>
        <a href="{{ route('admin.seo.competitors', ['has_gaps' => '1']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ request('has_gaps') === '1' ? 'ring-1 ring-purple-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Content Gaps</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(175,82,222,1);">{{ $summary['total_gaps'] }}</p>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="mb-4 flex flex-wrap items-center gap-3">
        <form action="{{ route('admin.seo.competitors') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            @if(request('position'))
            <input type="hidden" name="position" value="{{ request('position') }}">
            @endif
            @if(request('has_gaps'))
            <input type="hidden" name="has_gaps" value="{{ request('has_gaps') }}">
            @endif

            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔎 Cari keyword..."
                   class="px-3 py-1.5 rounded-apple text-sm text-white placeholder-white/30 w-48"
                   style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">

            <select name="difficulty" onchange="this.form.submit()"
                    class="px-3 py-1.5 rounded-apple text-sm text-white"
                    style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
                <option value="">All Difficulty</option>
                <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
            </select>

            <button type="submit" class="px-3 py-1.5 rounded-apple text-xs font-semibold" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">Filter</button>

            @if(request()->hasAny(['search', 'difficulty', 'position', 'has_gaps']))
            <a href="{{ route('admin.seo.competitors') }}" class="px-3 py-1.5 rounded-apple text-xs font-semibold" style="color: rgba(255,59,48,0.8);">✕ Reset</a>
            @endif
        </form>
    </div>

    <!-- Analyses Table -->
    <div class="card-elevated rounded-apple-xl overflow-x-auto">
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-white">Keyword Analysis Results</h3>
            <span class="text-xs" style="color: rgba(235,235,245,0.4);">{{ $analyses->total() }} keyword</span>
        </div>
        <table class="min-w-full">
            <thead style="background: rgba(28,28,30,0.45);">
                <tr>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Keyword</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Position</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Volume</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Difficulty</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Gaps</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Recs</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Analyzed</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analyses as $analysis)
                @php
                    $posBg = $analysis->our_position ? ($analysis->our_position <= 10 ? 'rgba(52,199,89,0.15)' : ($analysis->our_position <= 30 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)')) : '';
                    $posColor = $analysis->our_position ? ($analysis->our_position <= 10 ? 'rgba(52,199,89,1)' : ($analysis->our_position <= 30 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)')) : '';
                    $diffBg = $analysis->difficulty === 'easy' ? 'rgba(52,199,89,0.15)' : ($analysis->difficulty === 'medium' ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                    $diffColor = $analysis->difficulty === 'easy' ? 'rgba(52,199,89,1)' : ($analysis->difficulty === 'medium' ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                    $staleDays = $analysis->analyzed_at->diffInDays(now());
                    $isStale = $staleDays > 7;
                    $gapCount = count($analysis->content_gaps ?? []);
                    $recCount = count($analysis->recommendations ?? []);
                @endphp
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="px-4 py-3">
                        <div class="font-medium text-sm text-white">{{ $analysis->keyword }}</div>
                        @if($analysis->our_url)
                        <div class="text-xs truncate max-w-xs" style="color: rgba(235,235,245,0.55);">{{ $analysis->our_url }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($analysis->our_position)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-apple text-xs font-semibold"
                                  style="background: {{ $posBg }}; color: {{ $posColor }};">
                                #{{ $analysis->our_position }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs" style="background: rgba(255,59,48,0.1); color: rgba(255,59,48,0.7);">Unranked</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-sm" style="color: rgba(235,235,245,0.6);">{{ number_format($analysis->search_volume ?? 0) }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-semibold"
                              style="background: {{ $diffBg }}; color: {{ $diffColor }};">
                            {{ ucfirst($analysis->difficulty) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($gapCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-semibold" style="background: rgba(255,59,48,0.1); color: rgba(255,59,48,1);">{{ $gapCount }}</span>
                        @else
                            <span class="text-xs" style="color: rgba(52,199,89,0.7);">✓</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($recCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-semibold" style="background: rgba(255,149,0,0.1); color: rgba(255,149,0,1);">{{ $recCount }}</span>
                        @else
                            <span class="text-xs" style="color: rgba(235,235,245,0.3);">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-xs" style="color: rgba(235,235,245,0.55);">
                        {{ $analysis->analyzed_at->format('d M Y') }}
                        @if($isStale)
                            <span class="inline-flex items-center ml-0.5 px-1 py-0.5 rounded text-[10px] font-bold" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);">stale</span>
                        @endif
                        <br>
                        @if(in_array($analysis->data_source, ['searxng', 'google_serp']))
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold" style="background: rgba(52,199,89,0.12); color: rgba(52,199,89,0.8);">{{ $analysis->data_source === 'searxng' ? 'SXG' : 'SERP' }}</span>
                        @else
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold" style="background: rgba(255,149,0,0.12); color: rgba(255,149,0,0.7);">AI</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.seo.competitor-smart-fix', $analysis->id) }}"
                               class="inline-flex items-center px-2 py-1 rounded-apple text-[11px] font-bold transition hover:opacity-80"
                               style="background: linear-gradient(135deg, rgba(52,199,89,0.15), rgba(10,132,255,0.15)); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.2);"
                               title="Smart Fix Komprehensif">
                                🤖 Fix
                            </a>
                            <a href="{{ route('admin.seo.competitor-detail', $analysis->id) }}" class="text-sm font-medium" style="color: rgba(10,132,255,1);">Detail →</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: rgba(52,199,89,0.1);">
                                <span class="text-2xl">🔍</span>
                            </div>
                            <p class="text-sm font-medium text-white">Belum ada data analisis kompetitor</p>
                            <p class="text-xs max-w-sm" style="color: rgba(235,235,245,0.55);">Analisis lanskap kompetitor untuk keyword target Anda secara otomatis, atau input keyword manual di atas.</p>
                            <form action="{{ route('admin.seo.run-competitor-analyze') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Analyzing...';">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-apple text-sm font-semibold transition" style="background: rgba(52,199,89,0.9); color: #fff;">
                                    🔍 Mulai Batch Analisis
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $analyses->links() }}
    </div>
</div>
@endsection
