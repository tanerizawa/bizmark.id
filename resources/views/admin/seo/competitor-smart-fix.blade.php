@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-white">🤖 Smart Fix Komprehensif</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Perbaikan cerdas berdasarkan analisis kompetitor untuk "<strong class="text-white">{{ $analysis->keyword }}</strong>"</p>
        </div>
        <a href="{{ route('admin.seo.competitor-detail', $analysis->id) }}" class="btn-secondary-sm">← Kembali ke Detail</a>
    </div>

    <!-- Data Source -->
    @if($analysis->isRealData())
    <div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.2);">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">{{ $analysis->getDataSourceLabel() }}</span>
        <span class="text-xs" style="color: rgba(52,199,89,0.8);">Perbaikan berdasarkan data kompetitor REAL dari mesin pencari.</span>
    </div>
    @else
    <div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(255,149,0,0.08); border: 1px solid rgba(255,149,0,0.2);">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">AI Estimasi</span>
        <span class="text-xs" style="color: rgba(255,149,0,0.8);">⚠️ Perbaikan berdasarkan data AI — re-analisis dengan SearXNG untuk hasil lebih akurat.</span>
    </div>
    @endif

    <!-- Two-column layout: Left = What will be fixed, Right = Current state -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Fix Plan (2 columns wide) -->
        <div class="lg:col-span-2 space-y-4">

            <!-- Step 1: Meta Optimization -->
            <div class="card-elevated rounded-apple-xl p-5" id="step-meta">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">1</span>
                    <div>
                        <h3 class="text-base font-semibold text-white">🏷️ Optimasi Meta Tags</h3>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Meta title, description, keywords, excerpt — dibuat lebih baik dari kompetitor</p>
                    </div>
                    <span class="ml-auto step-badge" id="badge-meta">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">Menunggu</span>
                    </span>
                </div>
                <div class="space-y-2 text-xs" style="color: rgba(235,235,245,0.5);">
                    @if($article)
                    <div class="p-2 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <span style="color: rgba(235,235,245,0.35);">Saat ini:</span>
                        <div class="mt-1">Title: <span class="text-white">{{ $article->meta_title ?? $article->title }}</span></div>
                        <div>Desc: <span class="text-white">{{ Str::limit($article->meta_description ?? '', 80) ?: '(kosong)' }}</span></div>
                        <div>Keywords: <span class="text-white">{{ $article->meta_keywords ?: '(kosong)' }}</span></div>
                    </div>
                    @endif
                    <p>AI akan membuat meta tags yang <strong class="text-white">mengalahkan</strong> kompetitor berdasarkan analisis SERP real.</p>
                    @if(!empty($analysis->recommendations))
                    <p style="color: rgba(255,149,0,0.8);">Rekomendasi terkait meta:</p>
                    <ul class="space-y-0.5 ml-3">
                        @foreach(collect($analysis->recommendations)->filter(fn($r) => Str::contains(mb_strtolower($r), ['title', 'meta', 'description', 'judul', 'deskripsi', 'keyword'])) as $rec)
                        <li>• {{ $rec }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div id="result-meta" class="hidden mt-3"></div>
            </div>

            <!-- Step 2: Content Enhancement -->
            <div class="card-elevated rounded-apple-xl p-5" id="step-content">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">2</span>
                    <div>
                        <h3 class="text-base font-semibold text-white">📝 Perkaya Konten</h3>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Tutup content gaps — tambahkan topik yang kompetitor sudah cover</p>
                    </div>
                    <span class="ml-auto step-badge" id="badge-content">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">Menunggu</span>
                    </span>
                </div>
                @if(!empty($analysis->content_gaps))
                <div class="mb-3">
                    <p class="text-xs font-semibold mb-1.5" style="color: rgba(255,59,48,0.8);">Content gaps yang akan ditutup ({{ count($analysis->content_gaps) }}):</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($analysis->content_gaps as $gap)
                        <span class="inline-flex items-center px-2 py-1 rounded-apple text-[11px]" style="background: rgba(255,59,48,0.1); color: rgba(255,59,48,0.8); border: 1px solid rgba(255,59,48,0.15);">{{ Str::limit($gap, 40) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($article)
                <div class="text-xs" style="color: rgba(235,235,245,0.4);">
                    Konten saat ini: <strong class="text-white">{{ str_word_count(strip_tags($article->content ?? '')) }} kata</strong>
                    · {{ $article->reading_time ?? '?' }} menit baca
                </div>
                @endif
                <div id="result-content" class="hidden mt-3"></div>
            </div>

            <!-- Step 3: Structure Fix -->
            <div class="card-elevated rounded-apple-xl p-5" id="step-structure">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold" style="background: rgba(175,82,222,0.2); color: rgba(175,82,222,1);">3</span>
                    <div>
                        <h3 class="text-base font-semibold text-white">🔧 Perbaikan Struktur SEO</h3>
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">Heading hierarchy, keyword density, bold keywords</p>
                    </div>
                    <span class="ml-auto step-badge" id="badge-structure">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">Menunggu</span>
                    </span>
                </div>
                <div class="text-xs" style="color: rgba(235,235,245,0.5);">
                    <p>AI akan memastikan keyword "<strong class="text-white">{{ $analysis->keyword }}</strong>" muncul dengan densitas optimal, heading terstruktur, dan keyword di-bold.</p>
                </div>
                <div id="result-structure" class="hidden mt-3"></div>
            </div>
        </div>

        <!-- Right sidebar: Article & Competitors -->
        <div class="space-y-4">
            <!-- Linked Article -->
            <div class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-sm font-semibold text-white mb-3">📄 Artikel Target</h3>
                @if($article)
                <div class="space-y-2">
                    <a href="/blog/{{ $article->slug }}" target="_blank" class="text-sm font-medium hover:underline" style="color: rgba(10,132,255,1);">{{ $article->title }}</a>
                    <div class="text-xs space-y-1" style="color: rgba(235,235,245,0.45);">
                        <div>📂 {{ ucfirst($article->category ?? 'general') }}</div>
                        <div>👁 {{ number_format($article->views_count ?? 0) }} views</div>
                        <div>📝 {{ str_word_count(strip_tags($article->content ?? '')) }} kata</div>
                    </div>
                    @if($seoScore)
                    <div class="mt-2 flex items-center gap-2">
                        @php
                            $sc = $seoScore->total_score;
                            $sColor = $sc >= 80 ? 'rgba(52,199,89,1)' : ($sc >= 60 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                            $sBg = $sc >= 80 ? 'rgba(52,199,89,0.15)' : ($sc >= 60 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                        @endphp
                        <span class="text-xs" style="color: rgba(235,235,245,0.5);">SEO Score:</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-bold" style="background: {{ $sBg }}; color: {{ $sColor }};">{{ $sc }} ({{ $seoScore->grade }})</span>
                    </div>
                    @endif
                </div>
                @else
                <div class="p-4 rounded-apple-lg text-center" style="background: rgba(52,199,89,0.05); border: 1px dashed rgba(52,199,89,0.2);">
                    <div class="text-2xl mb-1">🤖</div>
                    <p class="text-xs font-medium text-white">Artikel akan dibuat otomatis</p>
                    <p class="text-[11px] mt-1" style="color: rgba(52,199,89,0.7);">AI akan membuat artikel baru yang komprehensif berdasarkan analisis kompetitor, lalu mengoptimasi SEO-nya.</p>
                </div>
                @endif
            </div>

            <!-- Top 3 Competitors -->
            <div class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-sm font-semibold text-white mb-3">🏢 Kompetitor Teratas</h3>
                <div class="space-y-2">
                    @foreach(array_slice($analysis->top_competitors ?? [], 0, 3) as $comp)
                    <div class="p-2 rounded-apple" style="background: rgba(255,255,255,0.03);">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-[9px] font-bold" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">#{{ $comp['position'] ?? '?' }}</span>
                            <span class="text-xs font-medium text-white">{{ $comp['domain'] ?? 'Unknown' }}</span>
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: rgba(235,235,245,0.4);">{{ Str::limit($comp['title'] ?? '', 50) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recommendations Summary -->
            <div class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-sm font-semibold text-white mb-3">💡 Rekomendasi ({{ count($analysis->recommendations ?? []) }})</h3>
                <ol class="space-y-1.5 text-[11px]" style="color: rgba(235,235,245,0.5);">
                    @foreach($analysis->recommendations ?? [] as $i => $rec)
                    <li class="flex items-start gap-1.5">
                        <span class="flex-shrink-0 w-4 h-4 flex items-center justify-center rounded-full text-[9px] font-bold" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">{{ $i + 1 }}</span>
                        <span>{{ Str::limit($rec, 70) }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card-elevated rounded-apple-xl p-5" id="action-bar">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                @if($article)
                <p class="text-sm font-semibold text-white">Siap menjalankan Smart Fix?</p>
                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.5);">
                    3 tahap perbaikan akan dijalankan secara berurutan menggunakan AI (OpenRouter).
                    Semua perubahan langsung disimpan ke artikel.
                </p>
                @else
                <p class="text-sm font-semibold text-white">🤖 Smart Fix + Buat Artikel Baru</p>
                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.5);">
                    AI akan membuat artikel baru untuk keyword "<strong class="text-white">{{ $analysis->keyword }}</strong>",
                    lalu menjalankan 3 tahap optimasi. Artikel disimpan sebagai <strong class="text-white">Draft</strong>.
                </p>
                @endif
            </div>
            <button id="btn-execute"
                    onclick="executeSmartFix()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-apple-lg text-sm font-bold transition"
                    style="background: linear-gradient(135deg, rgba(52,199,89,1), rgba(48,176,80,1)); color: white; box-shadow: 0 4px 12px rgba(52,199,89,0.3);">
                🚀 {{ $article ? 'Jalankan Smart Fix' : 'Buat Artikel + Smart Fix' }}
            </button>
        </div>
    </div>

    <!-- Result Summary (hidden until execution complete) -->
    <div id="result-summary" class="hidden mt-6 card-elevated rounded-apple-xl p-6">
    </div>
</div>

@if($article)
<form id="verify-form" action="{{ route('admin.seo.competitor-verify', $analysis->id) }}" method="POST" class="hidden">
    @csrf
</form>
@endif

<script>
function executeSmartFix() {
    const btn = document.getElementById('btn-execute');
    btn.disabled = true;
    btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> {{ $article ? "Menjalankan AI..." : "Membuat artikel + AI Fix..." }}';
    btn.style.opacity = '0.6';
    btn.style.cursor = 'not-allowed';

    // Set all steps to "processing"
    setStepBadge('meta', 'processing', '⏳ Memproses...');
    setStepBadge('content', 'waiting', '⏳ Antrian');
    setStepBadge('structure', 'waiting', '⏳ Antrian');

    fetch('{{ route("admin.seo.competitor-execute-smart-fix", $analysis->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.result) {
            const r = data.result;

            // Update each step badge and result
            r.steps.forEach(step => {
                const key = step.step === 'Meta Optimization' ? 'meta'
                          : step.step === 'Content Enhancement' ? 'content'
                          : 'structure';

                if (step.status === 'applied') {
                    setStepBadge(key, 'success', '✅ Diterapkan');
                } else if (step.status === 'failed') {
                    setStepBadge(key, 'error', '❌ Gagal');
                } else {
                    setStepBadge(key, 'skipped', '⏭️ Dilewati');
                }

                // Show step result
                const resultEl = document.getElementById('result-' + key);
                if (resultEl && step.fixes.length > 0) {
                    resultEl.classList.remove('hidden');
                    resultEl.innerHTML = '<div class="space-y-1">' +
                        step.fixes.map(f => '<div class="text-xs flex items-start gap-1.5 p-1.5 rounded" style="background: rgba(52,199,89,0.06);"><span style="color: rgba(52,199,89,1);">✓</span><span style="color: rgba(52,199,89,0.8);">' + escapeHtml(f) + '</span></div>').join('') +
                        '</div>';
                } else if (resultEl && step.error) {
                    resultEl.classList.remove('hidden');
                    resultEl.innerHTML = '<div class="text-xs p-1.5 rounded" style="background: rgba(255,59,48,0.06); color: rgba(255,59,48,0.8);">' + escapeHtml(step.error || step.reason || 'Tidak ada perubahan') + '</div>';
                } else if (resultEl && step.reason) {
                    resultEl.classList.remove('hidden');
                    resultEl.innerHTML = '<div class="text-xs p-1.5 rounded" style="background: rgba(255,214,10,0.06); color: rgba(255,214,10,0.8);">' + escapeHtml(step.reason) + '</div>';
                }
            });

            // Show summary
            showSummary(r);

            // Update button
            btn.innerHTML = '✅ Selesai!';
            btn.style.background = 'rgba(52,199,89,0.2)';
            btn.style.color = 'rgba(52,199,89,1)';
            btn.style.boxShadow = 'none';
            btn.style.opacity = '1';
        } else {
            btn.innerHTML = '❌ Gagal';
            btn.style.background = 'rgba(255,59,48,0.15)';
            btn.style.color = 'rgba(255,59,48,1)';
            btn.style.boxShadow = 'none';
            btn.style.opacity = '1';
            setStepBadge('meta', 'error', '❌ Error');
            setStepBadge('content', 'error', '❌ Error');
            setStepBadge('structure', 'error', '❌ Error');

            const summary = document.getElementById('result-summary');
            summary.classList.remove('hidden');
            summary.innerHTML = '<div class="text-center py-4"><p class="text-sm font-medium" style="color: rgba(255,59,48,1);">❌ ' + escapeHtml(data.error || 'Terjadi kesalahan saat menjalankan Smart Fix') + '</p></div>';
        }
    })
    .catch(err => {
        btn.innerHTML = '❌ Network Error';
        btn.style.background = 'rgba(255,59,48,0.15)';
        btn.style.color = 'rgba(255,59,48,1)';
        btn.style.opacity = '1';
        console.error(err);
    });
}

function setStepBadge(key, type, text) {
    const el = document.getElementById('badge-' + key);
    if (!el) return;

    const styles = {
        processing: 'background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);',
        waiting: 'background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);',
        success: 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);',
        error: 'background: rgba(255,59,48,0.15); color: rgba(255,59,48,1);',
        skipped: 'background: rgba(235,235,245,0.08); color: rgba(235,235,245,0.4);',
    };

    el.innerHTML = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold" style="' + (styles[type] || styles.waiting) + '">' + text + '</span>';
}

function showSummary(r) {
    const el = document.getElementById('result-summary');
    el.classList.remove('hidden');

    const scoreColor = r.new_score >= 80 ? 'rgba(52,199,89,1)' : (r.new_score >= 60 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
    const scoreBg = r.new_score >= 80 ? 'rgba(52,199,89,0.15)' : (r.new_score >= 60 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
    const changeSign = r.score_change >= 0 ? '+' : '';
    const changeColor = r.score_change > 0 ? 'rgba(52,199,89,1)' : (r.score_change < 0 ? 'rgba(255,59,48,1)' : 'rgba(235,235,245,0.5)');

    const articleEditUrl = r.article_edit_url || '';
    const articleBanner = r.article_created
        ? `<div class="mb-4 p-3 rounded-apple-lg flex items-center gap-2" style="background: rgba(52,199,89,0.08); border: 1px solid rgba(52,199,89,0.2);">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase" style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);">NEW</span>
            <span class="text-xs" style="color: rgba(52,199,89,0.8);">Artikel baru "<strong>${escapeHtml(r.title)}</strong>" berhasil dibuat sebagai Draft. ${articleEditUrl ? `<a href="${articleEditUrl}" style="color: rgba(10,132,255,1); text-decoration: underline;">Edit →</a>` : ''}</span>
           </div>`
        : '';

    el.innerHTML = `
        <h3 class="text-lg font-bold text-white mb-4">📊 Hasil Smart Fix</h3>
        ${articleBanner}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
            <div class="p-3 rounded-apple-lg text-center" style="background: rgba(255,255,255,0.03);">
                <div class="text-lg font-bold text-white">${r.total_fixes}</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(235,235,245,0.45);">Perbaikan</div>
            </div>
            <div class="p-3 rounded-apple-lg text-center" style="background: rgba(255,255,255,0.03);">
                <div class="text-2xl font-bold" style="color: ${scoreColor};">${r.new_score}</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(235,235,245,0.45);">Skor Baru (${r.new_grade})</div>
            </div>
            <div class="p-3 rounded-apple-lg text-center" style="background: rgba(255,255,255,0.03);">
                <div class="text-2xl font-bold" style="color: ${changeColor};">${changeSign}${r.score_change}</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(235,235,245,0.45);">Perubahan Skor</div>
            </div>
            <div class="p-3 rounded-apple-lg text-center" style="background: rgba(255,255,255,0.03);">
                <div class="text-2xl font-bold" style="color: rgba(255,149,0,1);">${r.remaining_issues}</div>
                <div class="text-[11px] mt-0.5" style="color: rgba(235,235,245,0.45);">Issue Tersisa</div>
            </div>
        </div>
        <!-- Score bar -->
        <div class="flex items-center gap-3 mb-4">
            <span class="text-xs" style="color: rgba(235,235,245,0.4);">${r.old_score}</span>
            <div class="flex-1 h-3 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.06);">
                <div class="h-full rounded-full transition-all" style="width: ${r.new_score}%; background: ${scoreColor};"></div>
            </div>
            <span class="text-xs font-bold" style="color: ${scoreColor};">${r.new_score}</span>
        </div>
        <!-- All fixes list -->
        <div class="space-y-1.5">
            ${r.fixes.map(f => '<div class="text-xs flex items-start gap-1.5 p-2 rounded-apple" style="background: rgba(52,199,89,0.04);"><span style="color: rgba(52,199,89,1);">✓</span><span style="color: rgba(235,235,245,0.6);">' + escapeHtml(f) + '</span></div>').join('')}
        </div>
        <div class="mt-4 flex gap-3">
            <a href="${'{{ route("admin.seo.competitor-detail", $analysis->id) }}'}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">← Kembali ke Detail</a>
            ${document.getElementById('verify-form') ? '<a href="#" onclick="event.preventDefault(); document.getElementById(\'verify-form\').submit();" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold" style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1);">✅ Verifikasi Ulang</a>' : ''}
            ${r.article_created && articleEditUrl ? '<a href="' + articleEditUrl + '" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">📝 Edit Artikel Baru</a>' : ''}
        </div>
    `;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
