@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-white">📊 SEO Scores</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Audit SEO otomatis per artikel (skor 0-100)</p>
        </div>
        <div class="flex gap-2">
            <form action="{{ route('admin.seo.rescore-all') }}" method="POST" x-data @submit.prevent="if(confirm('Re-score semua artikel? Proses ini bisa memakan waktu.')) $el.submit()">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 px-3 py-2 rounded-apple text-sm font-medium transition" style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1); border: 1px solid rgba(175,82,222,0.3);">
                    🔄 Re-score All
                </button>
            </form>
            <button type="button" id="batchFixBtn"
                class="inline-flex items-center gap-1 px-3 py-2 rounded-apple text-sm font-medium transition"
                style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);"
                onclick="startBatchFix()">
                🛠️ Batch Fix (Score &lt;80)
            </button>
            <a href="{{ route('admin.seo.dashboard') }}" class="btn-secondary-sm">← Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</pre>
    </div>
    @endif

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-5 text-center">
            <p class="text-xl font-bold text-white">{{ $summary['total'] }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Total Scored</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5 text-center">
            <p class="text-xl font-bold" style="color: {{ $summary['avg'] >= 70 ? 'rgba(52,199,89,1)' : 'rgba(255,214,10,1)' }};">{{ $summary['avg'] }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Average Score</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5 text-center">
            <p class="text-xl font-bold" style="color: rgba(52,199,89,1);">{{ $summary['excellent'] }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Excellent (80+)</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5 text-center">
            <p class="text-xl font-bold" style="color: rgba(255,59,48,1);">{{ $summary['needs_work'] }}</p>
            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.55);">Needs Work (<60)</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <form method="GET" action="{{ route('admin.seo.scores') }}" class="flex items-center gap-2">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari artikel..."
                   class="rounded-apple text-sm px-3 py-1.5 w-48"
                   style="background: rgba(28,28,30,0.6); color: rgba(235,235,245,0.8); border: 1px solid rgba(84,84,88,0.35);">
            <button type="submit" class="px-3 py-1.5 text-sm rounded-apple font-medium" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">🔍</button>
            @if($search)
                <a href="{{ route('admin.seo.scores', ['filter' => $filter, 'sort' => $sort]) }}" class="px-2 py-1.5 text-xs rounded-apple" style="color: rgba(255,59,48,1);">✕ Reset</a>
            @endif
        </form>
        <span class="border-l border-white/10 mx-1 h-6"></span>
        @foreach(['all' => 'Semua', 'poor' => 'Perlu Perbaikan', 'excellent' => 'Excellent'] as $key => $label)
        <a href="{{ route('admin.seo.scores', ['filter' => $key, 'sort' => $sort, 'search' => $search]) }}"
           class="px-3 py-1.5 text-sm rounded-apple font-medium"
           style="{{ $filter === $key ? 'background: rgba(10,132,255,0.9); color: #fff;' : 'background: rgba(28,28,30,0.4); color: rgba(235,235,245,0.65); border: 1px solid rgba(84,84,88,0.35);' }}">
            {{ $label }}
        </a>
        @endforeach
        <span class="border-l border-white/10 mx-2"></span>
        @foreach(['score_asc' => '↑ Score', 'score_desc' => '↓ Score', 'recent' => 'Terbaru'] as $key => $label)
        <a href="{{ route('admin.seo.scores', ['sort' => $key, 'filter' => $filter, 'search' => $search]) }}"
           class="px-3 py-1.5 text-sm rounded-apple font-medium"
           style="{{ $sort === $key ? 'background: rgba(175,82,222,0.9); color: #fff;' : 'background: rgba(28,28,30,0.4); color: rgba(235,235,245,0.65); border: 1px solid rgba(84,84,88,0.35);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <!-- Scores Table -->
    <div class="card-elevated rounded-apple-xl overflow-x-auto">
        <table class="min-w-full">
            <thead style="background: rgba(28,28,30,0.45);">
                <tr>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Score</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Artikel</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Grade</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Views</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Issues</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Scored</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Detail</th>
                    <th class="px-4 py-3 text-center text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Fix</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scores as $score)
                @php
                    $sc = $score->total_score;
                    $sBg = $sc >= 80 ? 'rgba(52,199,89,0.15)' : ($sc >= 60 ? 'rgba(10,132,255,0.15)' : ($sc >= 40 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)'));
                    $sColor = $sc >= 80 ? 'rgba(52,199,89,1)' : ($sc >= 60 ? 'rgba(10,132,255,1)' : ($sc >= 40 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)'));
                    $gradeColor = ($score->grade === 'A+' || $score->grade === 'A') ? 'rgba(52,199,89,1)' : ($score->grade === 'B' ? 'rgba(10,132,255,1)' : 'rgba(255,59,48,1)');
                @endphp
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-apple text-sm font-bold"
                              style="background: {{ $sBg }}; color: {{ $sColor }};">
                            {{ $score->total_score }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="max-w-xs truncate text-sm font-medium text-white">
                            {{ $score->article?->title ?? 'Deleted' }}
                        </div>
                        <div class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $score->article?->category }}</div>
                    </td>
                    <td class="px-4 py-3 text-sm font-bold" style="color: {{ $gradeColor }};">
                        {{ $score->grade }}
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235,235,245,0.6);">
                        {{ number_format($score->article?->views_count ?? 0) }}
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235,235,245,0.55);">
                        {{ count($score->recommendations ?? []) }}
                    </td>
                    <td class="px-4 py-3 text-xs" style="color: rgba(235,235,245,0.55);">
                        {{ $score->scored_at?->diffForHumans() }}
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.seo.score-detail', $score->article_id) }}" class="text-sm font-medium" style="color: rgba(10,132,255,1);">
                            Detail →
                        </a>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($score->total_score < 80)
                        <form action="{{ route('admin.seo.fix-single', $score->article_id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);" title="Auto-fix SEO">
                                🛠️ Fix
                            </button>
                        </form>
                        @else
                        <span class="text-xs" style="color: rgba(52,199,89,0.6);">✓</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: rgba(175,82,222,0.1);">
                                <span class="text-2xl">📊</span>
                            </div>
                            <p class="text-sm font-medium text-white">Belum ada data SEO Score</p>
                            <p class="text-xs max-w-sm" style="color: rgba(235,235,245,0.55);">Jalankan analisis SEO untuk menilai semua artikel yang dipublikasi secara otomatis.</p>
                            <button type="button" onclick="startScoreArticles()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-apple text-sm font-semibold transition" style="background: rgba(175,82,222,0.9); color: #fff;">
                                🎯 Mulai Score Artikel
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $scores->appends(request()->query())->links() }}
    </div>
</div>

<!-- Batch Fix Progress Modal -->
<div id="batchFixModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">
    <div class="card-elevated rounded-apple-xl p-6 w-full max-w-lg mx-4" style="border: 1px solid rgba(84,84,88,0.35);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">🤖 AI Batch Fix</h3>
            <button id="batchFixClose" onclick="closeBatchFix()" class="text-white/40 hover:text-white text-xl hidden">&times;</button>
        </div>

        <div id="bfStatus" class="text-sm mb-4" style="color: rgba(235,235,245,0.8);">Memuat daftar artikel...</div>

        <!-- Progress bar -->
        <div class="w-full rounded-full h-3 mb-3" style="background: rgba(28,28,30,0.6);">
            <div id="bfBar" class="h-3 rounded-full transition-all duration-300" style="width: 0%; background: linear-gradient(135deg, rgba(52,199,89,1), rgba(48,209,88,1));"></div>
        </div>
        <div class="flex justify-between text-xs mb-4" style="color: rgba(235,235,245,0.55);">
            <span id="bfCount">0 / 0</span>
            <span id="bfPercent">0%</span>
        </div>

        <!-- Current article -->
        <div id="bfCurrent" class="text-xs mb-3 hidden p-2 rounded-apple" style="background: rgba(10,132,255,0.1); color: rgba(10,132,255,1);">
            ⏳ Memproses: <span id="bfCurrentTitle">-</span>
        </div>

        <!-- Results log -->
        <div id="bfLog" class="max-h-48 overflow-y-auto space-y-1 text-xs" style="color: rgba(235,235,245,0.65);"></div>

        <!-- Summary (shown when done) -->
        <div id="bfSummary" class="hidden mt-4 p-3 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
            <div class="text-sm font-semibold" style="color: rgba(52,199,89,1);" id="bfSummaryText"></div>
        </div>

        <div class="mt-4 flex gap-2">
            <button id="bfReloadBtn" onclick="location.reload()" class="hidden w-full px-4 py-2 rounded-apple text-sm font-medium transition" style="background: rgba(10,132,255,0.9); color: #fff;">
                🔄 Muat Ulang Halaman
            </button>
        </div>
    </div>
</div>

<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
const CANDIDATES_URL = '{{ route("admin.seo.fix-candidates") }}';
const FIX_URL_BASE = '{{ url("/admin/seo/scores/fix-single-ajax") }}';

let batchRunning = false;

async function startBatchFix() {
    if (batchRunning) return;
    if (!confirm('Auto-fix SEO dengan AI untuk semua artikel skor < 80?\nMeta title, description, keywords, excerpt akan di-generate otomatis.')) return;

    batchRunning = true;
    const modal = document.getElementById('batchFixModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const status = document.getElementById('bfStatus');
    const bar = document.getElementById('bfBar');
    const count = document.getElementById('bfCount');
    const percent = document.getElementById('bfPercent');
    const current = document.getElementById('bfCurrent');
    const currentTitle = document.getElementById('bfCurrentTitle');
    const log = document.getElementById('bfLog');
    const summary = document.getElementById('bfSummary');
    const summaryText = document.getElementById('bfSummaryText');
    const closeBtn = document.getElementById('batchFixClose');
    const reloadBtn = document.getElementById('bfReloadBtn');

    // Reset
    bar.style.width = '0%';
    count.textContent = '0 / 0';
    percent.textContent = '0%';
    log.innerHTML = '';
    summary.classList.add('hidden');
    closeBtn.classList.add('hidden');
    reloadBtn.classList.add('hidden');
    current.classList.add('hidden');

    try {
        // 1. Fetch candidates
        status.textContent = '📋 Memuat daftar artikel yang perlu diperbaiki...';
        const res = await fetch(CANDIDATES_URL + '?threshold=80');
        const data = await res.json();
        const candidates = data.candidates;

        if (!candidates || candidates.length === 0) {
            status.textContent = '✅ Semua artikel sudah memiliki skor ≥ 80. Tidak ada yang perlu diperbaiki.';
            closeBtn.classList.remove('hidden');
            batchRunning = false;
            return;
        }

        status.textContent = `🛠️ Memproses ${candidates.length} artikel dengan AI...`;
        count.textContent = `0 / ${candidates.length}`;
        current.classList.remove('hidden');

        let totalFixed = 0, totalFixes = 0, aiCount = 0, scoreChangeSum = 0;

        // 2. Process each article one by one
        for (let i = 0; i < candidates.length; i++) {
            const c = candidates[i];
            currentTitle.textContent = c.title;
            count.textContent = `${i} / ${candidates.length}`;

            try {
                const fixRes = await fetch(`${FIX_URL_BASE}/${c.id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
                });
                const result = await fixRes.json();

                const pct = Math.round(((i + 1) / candidates.length) * 100);
                bar.style.width = pct + '%';
                percent.textContent = pct + '%';
                count.textContent = `${i + 1} / ${candidates.length}`;

                if (result.fixes_count > 0) {
                    totalFixed++;
                    totalFixes += result.fixes_count;
                    scoreChangeSum += result.score_change;

                    const tag = result.ai_powered ? '🤖' : '⚙️';
                    const changeColor = result.score_change > 0 ? 'rgba(52,199,89,1)' : 'rgba(255,214,10,1)';
                    log.innerHTML += `<div class="p-1.5 rounded" style="background: rgba(52,199,89,0.06);">${tag} <span class="text-white/80">${result.title || c.title}</span> — <span style="color: ${changeColor};">${result.old_score}→${result.new_score} (+${result.score_change})</span> · ${result.fixes_count} fix</div>`;
                } else {
                    log.innerHTML += `<div class="p-1.5 rounded" style="background: rgba(255,255,255,0.02);">⏭️ <span class="text-white/50">${c.title}</span> — tidak ada perubahan</div>`;
                }

                if (result.ai_powered) aiCount++;
                log.scrollTop = log.scrollHeight;
            } catch (err) {
                log.innerHTML += `<div class="p-1.5 rounded" style="background: rgba(255,59,48,0.08);">❌ <span style="color: rgba(255,59,48,0.8);">${c.title}</span> — error: ${err.message}</div>`;
                log.scrollTop = log.scrollHeight;
            }
        }

        // 3. Done
        current.classList.add('hidden');
        const avgChange = totalFixed > 0 ? (scoreChangeSum / totalFixed).toFixed(1) : 0;
        status.textContent = '✅ Batch fix selesai!';
        summaryText.innerHTML = `📊 ${candidates.length} artikel diproses, ${totalFixed} diperbaiki<br>🔧 ${totalFixes} total perbaikan (${aiCount} AI-powered)<br>📈 Rata-rata peningkatan skor: +${avgChange}`;
        summary.classList.remove('hidden');
        closeBtn.classList.remove('hidden');
        reloadBtn.classList.remove('hidden');

    } catch (err) {
        status.textContent = '❌ Error: ' + err.message;
        closeBtn.classList.remove('hidden');
    }

    batchRunning = false;
}

function closeBatchFix() {
    const modal = document.getElementById('batchFixModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Re-score All loading state
document.querySelectorAll('form[action*="rescore-all"]').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '⏳ Memproses...';
            btn.style.opacity = '0.6';
            btn.style.cursor = 'wait';
        }
    });
});

// ── Score Articles Progress Modal ───────────────────────────
const SCORE_URL = '{{ route("admin.seo.run-score-articles") }}';
let scoreRunning = false;

async function startScoreArticles() {
    if (scoreRunning) return;
    scoreRunning = true;

    const modal = document.getElementById('scoreModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const status = document.getElementById('smStatus');
    const bar = document.getElementById('smBar');
    const count = document.getElementById('smCount');
    const percent = document.getElementById('smPercent');
    const current = document.getElementById('smCurrent');
    const currentTitle = document.getElementById('smCurrentTitle');
    const log = document.getElementById('smLog');
    const summary = document.getElementById('smSummary');
    const summaryText = document.getElementById('smSummaryText');
    const closeBtn = document.getElementById('smClose');
    const reloadBtn = document.getElementById('smReloadBtn');

    // Reset
    bar.style.width = '0%';
    count.textContent = '0 / 0';
    percent.textContent = '0%';
    log.innerHTML = '';
    summary.classList.add('hidden');
    closeBtn.classList.add('hidden');
    reloadBtn.classList.add('hidden');
    current.classList.add('hidden');

    try {
        // 1. Get unscored articles
        status.textContent = '📋 Memuat daftar artikel yang belum di-score...';
        const res = await fetch(SCORE_URL);
        const data = await res.json();
        const candidates = data.candidates;

        if (!candidates || candidates.length === 0) {
            status.textContent = '✅ Semua artikel sudah memiliki SEO score! Gunakan "Re-score All" untuk memperbarui scoring.';
            closeBtn.classList.remove('hidden');
            reloadBtn.classList.remove('hidden');
            scoreRunning = false;
            return;
        }

        status.textContent = `🎯 Scoring ${candidates.length} artikel...`;
        count.textContent = `0 / ${candidates.length}`;
        current.classList.remove('hidden');

        let totalScore = 0;
        let gradeCount = { 'A+': 0, 'A': 0, 'B': 0, 'C': 0, 'D': 0, 'F': 0 };

        // 2. Process each article
        for (let i = 0; i < candidates.length; i++) {
            const c = candidates[i];
            currentTitle.textContent = c.title;
            count.textContent = `${i} / ${candidates.length}`;

            try {
                const fixRes = await fetch(SCORE_URL, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `article_id=${c.id}`,
                });
                const result = await fixRes.json();

                const pct = Math.round(((i + 1) / candidates.length) * 100);
                bar.style.width = pct + '%';
                percent.textContent = pct + '%';
                count.textContent = `${i + 1} / ${candidates.length}`;

                totalScore += result.score;
                gradeCount[result.grade] = (gradeCount[result.grade] || 0) + 1;

                const scoreColor = result.score >= 80 ? 'rgba(52,199,89,1)' : (result.score >= 60 ? 'rgba(10,132,255,1)' : (result.score >= 40 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)'));
                log.innerHTML += `<div class="p-1.5 rounded" style="background: rgba(255,255,255,0.02);">📄 <span class="text-white/80">${result.title}</span> — <span style="color: ${scoreColor};">${result.score}/100</span> (${result.grade}) · ${result.recommendations} rekomendasi</div>`;
                log.scrollTop = log.scrollHeight;
            } catch (err) {
                log.innerHTML += `<div class="p-1.5 rounded" style="background: rgba(255,59,48,0.08);">❌ <span style="color: rgba(255,59,48,0.8);">${c.title}</span> — error: ${err.message}</div>`;
                log.scrollTop = log.scrollHeight;
            }
        }

        // 3. Done
        current.classList.add('hidden');
        const avgScore = candidates.length > 0 ? (totalScore / candidates.length).toFixed(1) : 0;
        status.textContent = '✅ Scoring selesai!';

        const gradeStr = Object.entries(gradeCount).filter(([,v]) => v > 0).map(([k,v]) => `${k}: ${v}`).join(', ');
        summaryText.innerHTML = `📊 ${candidates.length} artikel berhasil di-score<br>🎯 Rata-rata skor: ${avgScore}/100<br>📈 Distribusi: ${gradeStr}`;
        summary.classList.remove('hidden');
        closeBtn.classList.remove('hidden');
        reloadBtn.classList.remove('hidden');

    } catch (err) {
        status.textContent = '❌ Error: ' + err.message;
        closeBtn.classList.remove('hidden');
    }

    scoreRunning = false;
}

function closeScoreModal() {
    if (scoreRunning) return;
    const modal = document.getElementById('scoreModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Updated Escape key handler
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!batchRunning) closeBatchFix();
        if (!scoreRunning) closeScoreModal();
    }
});
</script>

<!-- Score Articles Progress Modal -->
<div id="scoreModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.7); backdrop-filter: blur(8px);">
    <div class="card-elevated rounded-apple-xl p-6 w-full max-w-lg mx-4" style="border: 1px solid rgba(84,84,88,0.35);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-white">🎯 Score Articles</h3>
            <button id="smClose" onclick="closeScoreModal()" class="text-white/40 hover:text-white text-xl hidden">&times;</button>
        </div>

        <div id="smStatus" class="text-sm mb-4" style="color: rgba(235,235,245,0.8);">Memuat...</div>

        <div class="w-full rounded-full h-3 mb-3" style="background: rgba(28,28,30,0.6);">
            <div id="smBar" class="h-3 rounded-full transition-all duration-300" style="width: 0%; background: linear-gradient(135deg, rgba(175,82,222,1), rgba(191,90,242,1));"></div>
        </div>
        <div class="flex justify-between text-xs mb-4" style="color: rgba(235,235,245,0.55);">
            <span id="smCount">0 / 0</span>
            <span id="smPercent">0%</span>
        </div>

        <div id="smCurrent" class="text-xs mb-3 hidden p-2 rounded-apple" style="background: rgba(175,82,222,0.1); color: rgba(175,82,222,1);">
            ⏳ Scoring: <span id="smCurrentTitle">-</span>
        </div>

        <div id="smLog" class="max-h-48 overflow-y-auto space-y-1 text-xs" style="color: rgba(235,235,245,0.65);"></div>

        <div id="smSummary" class="hidden mt-4 p-3 rounded-apple-lg" style="background: rgba(175,82,222,0.1); border: 1px solid rgba(175,82,222,0.3);">
            <div class="text-sm font-semibold" style="color: rgba(175,82,222,1);" id="smSummaryText"></div>
        </div>

        <div class="mt-4 flex gap-2">
            <button id="smReloadBtn" onclick="location.reload()" class="hidden w-full px-4 py-2 rounded-apple text-sm font-medium transition" style="background: rgba(10,132,255,0.9); color: #fff;">
                🔄 Muat Ulang Halaman
            </button>
        </div>
    </div>
</div>

@endsection
