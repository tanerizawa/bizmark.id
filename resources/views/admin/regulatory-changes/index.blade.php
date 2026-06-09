@extends('layouts.admin')

@section('title', 'Regulatory Change Detector — Admin')

@push('styles')
<style>
    .rc-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: .875rem;
        margin-bottom: 1.5rem;
    }
    .rc-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .875rem;
        padding: 1rem;
        text-align: center;
    }
    .rc-stat .num { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .rc-stat .lbl { font-size: .75rem; color: #64748b; margin-top: 4px; }
    .badge-score {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 600;
    }
    .score-high   { background: #fee2e2; color: #991b1b; }
    .score-medium { background: #fef9c3; color: #854d0e; }
    .score-low    { background: #f1f5f9; color: #475569; }
    .card { background: #fff; border: 1px solid #e2e8f0; border-radius: .875rem; padding: 1.25rem; margin-bottom: 1rem; }
    .card-title { font-weight: 700; color: #1e293b; margin-bottom: .25rem; }
    .card-meta  { font-size: .8125rem; color: #64748b; margin-bottom: .625rem; }
    .card-summary { font-size: .875rem; color: #374151; margin-bottom: .75rem; }
    .cats { display: flex; flex-wrap: wrap; gap: .25rem; }
    .cat-badge { background: #eff6ff; color: #1d4ed8; border-radius: 9999px; padding: 2px 8px; font-size: .75rem; }
    .filter-bar { display: flex; gap: .625rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: flex-end; }
    .filter-bar select, .filter-bar button { font-size: .8125rem; padding: .4rem .75rem; border: 1px solid #d1d5db; border-radius: .5rem; }
    .btn-crawl { background: #6366f1; color: #fff; border: none; padding: .5rem 1.25rem; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; }
    .btn-crawl:hover { background: #4f46e5; }
    .btn-del { color: #dc2626; background: none; border: none; cursor: pointer; font-size: .8125rem; }
</style>
@endpush

@section('content')
<div class="content-header">
    <h1 class="content-title">AI Regulatory Change Detector</h1>
    <p class="content-subtitle">Monitor perubahan regulasi perizinan Indonesia secara otomatis</p>
</div>

@if(session('success'))
    <div class="alert alert-success mb-3">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="rc-grid">
    <div class="rc-stat">
        <div class="num">{{ $changes->total() }}</div>
        <div class="lbl">Total Regulasi</div>
    </div>
    <div class="rc-stat">
        <div class="num text-danger">{{ $changes->getCollection()->where('relevance_score', '>=', 0.7)->count() }}</div>
        <div class="lbl">Relevansi Tinggi</div>
    </div>
    <div class="rc-stat">
        <div class="num text-warning">{{ $changes->getCollection()->where('notified', false)->count() }}</div>
        <div class="lbl">Belum Dinotifikasi</div>
    </div>
</div>

{{-- Actions + Filter --}}
<div class="filter-bar">
    <form method="GET" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-end">
        <div>
            <label class="d-block" style="font-size:.75rem;margin-bottom:2px">Min. Relevansi</label>
            <select name="relevance">
                <option value="">Semua</option>
                <option value="0.3" @selected(request('relevance') == '0.3')>≥ 30%</option>
                <option value="0.5" @selected(request('relevance') == '0.5')>≥ 50%</option>
                <option value="0.7" @selected(request('relevance') == '0.7')>≥ 70%</option>
            </select>
        </div>
        <div>
            <label class="d-block" style="font-size:.75rem;margin-bottom:2px">Status Notif</label>
            <select name="notified">
                <option value="">Semua</option>
                <option value="0" @selected(request('notified') === '0')>Belum Notif</option>
                <option value="1" @selected(request('notified') === '1')>Sudah Notif</option>
            </select>
        </div>
        <button type="submit">Filter</button>
    </form>

    <form method="POST" action="{{ route('admin.regulatory-changes.crawl') }}" style="margin-left:auto">
        @csrf
        <button type="submit" class="btn-crawl">▶ Crawl Sekarang</button>
    </form>
</div>

{{-- Results --}}
@forelse($changes as $change)
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:.5rem">
            <div class="card-title">{{ $change->title }}</div>
            <span class="badge-score {{ $change->relevance_score >= 0.7 ? 'score-high' : ($change->relevance_score >= 0.4 ? 'score-medium' : 'score-low') }}">
                {{ number_format($change->relevance_score * 100, 0) }}%
            </span>
        </div>

        <div class="card-meta">
            {{ $change->document_number ? $change->document_number . ' · ' : '' }}
            {{ $change->published_at->format('d M Y') }} ·
            <a href="{{ $change->source_url }}" target="_blank" rel="noopener" style="color:#6366f1">Lihat Sumber ↗</a>
            @if($change->notified)
                · <span style="color:#059669">✓ Ternotifikasi</span>
            @else
                · <span style="color:#d97706">Belum notif</span>
            @endif
        </div>

        @if($change->summary_id)
            <div class="card-summary">{{ $change->summary_id }}</div>
        @endif

        @if(!empty($change->affected_service_categories))
            <div class="cats">
                @foreach($change->affected_service_categories as $cat)
                    <span class="cat-badge">{{ $cat }}</span>
                @endforeach
            </div>
        @endif

        <div style="margin-top:.75rem;text-align:right">
            <form method="POST" action="{{ route('admin.regulatory-changes.destroy', $change) }}" onsubmit="return confirm('Hapus entri ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-del">Hapus</button>
            </form>
        </div>
    </div>
@empty
    <div class="card" style="text-align:center;padding:2rem;color:#64748b">
        Belum ada data regulasi. Klik <strong>Crawl Sekarang</strong> untuk memulai.
    </div>
@endforelse

{{ $changes->links() }}
@endsection
