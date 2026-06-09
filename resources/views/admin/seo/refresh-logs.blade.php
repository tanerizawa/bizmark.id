@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-white"><i class="fas fa-arrows-rotate mr-1.5" style="color: rgba(10,132,255,1);"></i>Content Refresh Logs</h1>
            <p class="mt-0.5 text-xs" style="color: rgba(235,235,245,0.6);">Audit trail pembaruan konten otomatis via AI
                @if($summary['last_run'])
                    <span class="ml-1" style="color: rgba(235,235,245,0.4);">&middot; Last run: {{ $summary['last_run']->diffForHumans() }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-1.5">
            <form action="{{ route('admin.seo.refresh-logs.run') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="limit" value="2">
                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.9); color: #fff;" title="Jalankan AI Content Refresh pada artikel stale (>90 hari)">
                    <i class="fas fa-bolt"></i> Run Refresh
                </button>
            </form>
            <a href="{{ route('admin.seo.dashboard') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-medium transition" style="background: rgba(142,142,147,0.15); color: rgba(235,235,245,0.7); border: 1px solid rgba(84,84,88,0.35);"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-3 p-3 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <p class="text-xs" style="color: rgba(52,199,89,1);"><i class="fas fa-check-circle mr-1"></i>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-3 p-3 rounded-apple-lg" style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.3);">
        <p class="text-xs" style="color: rgba(255,69,58,1);"><i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total Refresh</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Berhasil</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(52,199,89,1);">{{ number_format($summary['refreshed']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Error</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(255,59,48,1);">{{ number_format($summary['errors']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Terakhir Refresh</p>
            <p class="text-sm font-medium text-white mt-0.5">{{ $summary['last_run'] ? $summary['last_run']->diffForHumans() : 'Belum pernah' }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total AI Tokens</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(175,82,222,1);">{{ number_format($summary['total_tokens']) }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="flex gap-1.5 mb-3">
        @foreach(['all' => 'Semua', 'refreshed' => 'Berhasil', 'error' => 'Error'] as $key => $label)
            <a href="{{ route('admin.seo.refresh-logs', ['filter' => $key]) }}"
               class="px-2.5 py-1 text-xs rounded-apple font-medium transition"
               style="{{ $filter === $key ? 'background: rgba(10,132,255,0.9); color: #fff;' : 'background: rgba(28,28,30,0.4); color: rgba(235,235,245,0.65); border: 1px solid rgba(84,84,88,0.35);' }}">
                @if($key === 'refreshed')<i class="fas fa-check mr-0.5" style="font-size:9px;"></i>@elseif($key === 'error')<i class="fas fa-xmark mr-0.5" style="font-size:9px;"></i>@else<i class="fas fa-list mr-0.5" style="font-size:9px;"></i>@endif{{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Logs Table -->
    <div class="card-elevated rounded-apple-xl overflow-x-auto">
        <table class="min-w-full text-xs">
            <thead style="background: rgba(28,28,30,0.45);">
                <tr>
                    <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Waktu</th>
                    <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Article</th>
                    <th class="px-2 py-1.5 text-center text-[10px] uppercase tracking-widest w-[70px]" style="color: rgba(235,235,245,0.6);">Status</th>
                    <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Perubahan</th>
                    <th class="px-2 py-1.5 text-center text-[10px] uppercase tracking-widest w-[55px]" style="color: rgba(235,235,245,0.6);">Trigger</th>
                    <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[55px]" style="color: rgba(235,235,245,0.6);">Tokens</th>
                    <th class="px-2 py-1.5 text-center text-[10px] uppercase tracking-widest w-[70px]" style="color: rgba(235,235,245,0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $logBg = $log->status === 'refreshed' ? 'rgba(52,199,89,0.15)' : 'rgba(255,59,48,0.15)';
                    $logColor = $log->status === 'refreshed' ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)';
                @endphp
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="px-2 py-1.5 whitespace-nowrap" style="color: rgba(235,235,245,0.5);">
                        {{ $log->created_at->format('d M H:i') }}
                    </td>
                    <td class="px-2 py-1.5">
                        <div class="font-medium text-white truncate max-w-[200px]" title="{{ $log->article->title ?? 'Deleted' }}">
                            {{ $log->article->title ?? 'Deleted' }}
                        </div>
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-apple text-[10px] font-semibold"
                              style="background: {{ $logBg }}; color: {{ $logColor }};">
                            @if($log->status === 'refreshed')
                                <i class="fas fa-check" style="font-size:8px;"></i> OK
                            @else
                                <i class="fas fa-xmark" style="font-size:8px;"></i> Error
                            @endif
                        </span>
                    </td>
                    <td class="px-2 py-1.5">
                        @if($log->changes && is_array($log->changes))
                            <div class="flex flex-wrap gap-0.5">
                                @foreach(array_keys($log->changes) as $field)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-apple text-[10px]" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">{{ $field }}</span>
                                @endforeach
                            </div>
                        @elseif($log->error_message)
                            <p class="text-[10px] truncate max-w-[180px]" style="color: rgba(255,59,48,0.8);" title="{{ e($log->error_message) }}">{{ Str::limit($log->error_message, 60) }}</p>
                        @else
                            <span style="color: rgba(235,235,245,0.3);">—</span>
                        @endif
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        @php
                            $triggerIcon = match($log->triggered_by) {
                                'manual' => 'fa-hand-pointer',
                                'auto' => 'fa-robot',
                                default => 'fa-clock',
                            };
                        @endphp
                        <span title="{{ $log->triggered_by ?? 'cron' }}"><i class="fas {{ $triggerIcon }}" style="color: rgba(235,235,245,0.45); font-size:10px;"></i></span>
                    </td>
                    <td class="px-2 py-1.5 text-right" style="color: rgba(175,82,222,0.8);">
                        {{ $log->ai_tokens_used ? number_format($log->ai_tokens_used) : '—' }}
                    </td>
                    <td class="px-2 py-1.5 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="showLogDetail({{ $log->id }})" class="w-6 h-6 inline-flex items-center justify-center rounded-apple transition" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);" title="Lihat Detail">
                                <i class="fas fa-eye" style="font-size:10px;"></i>
                            </button>
                            @if($log->status === 'error' && $log->article)
                            <form action="{{ route('admin.seo.refresh-logs.retry', $log->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-6 h-6 inline-flex items-center justify-center rounded-apple transition" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);" title="Retry Refresh">
                                    <i class="fas fa-rotate-right" style="font-size:10px;"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.seo.refresh-logs.delete', $log->id) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus log ini?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6 h-6 inline-flex items-center justify-center rounded-apple transition" style="background: rgba(255,69,58,0.1); color: rgba(255,69,58,0.7);" title="Hapus Log">
                                    <i class="fas fa-trash-can" style="font-size:10px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center">
                        <i class="fas fa-arrows-rotate text-lg mb-2" style="color: rgba(235,235,245,0.15);"></i>
                        <p class="text-xs" style="color: rgba(235,235,245,0.45);">
                            @if($filter === 'refreshed')
                                Belum ada artikel yang berhasil di-refresh.
                            @elseif($filter === 'error')
                                Tidak ada error. Semua refresh berjalan lancar.
                            @else
                                Belum ada log content refresh. Klik "Run Refresh" atau sistem akan otomatis berjalan via cron.
                            @endif
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="mt-3">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Detail Modal -->
<div id="logDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center" style="background: rgba(0,0,0,0.6);">
    <div class="card-elevated rounded-apple-xl p-4 w-full max-w-lg mx-4 max-h-[80vh] overflow-y-auto" style="border: 1px solid rgba(84,84,88,0.35);">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-white"><i class="fas fa-file-lines mr-1" style="color: rgba(10,132,255,0.8);"></i>Detail Log</h3>
            <button onclick="closeLogDetail()" class="w-6 h-6 inline-flex items-center justify-center rounded-apple" style="background: rgba(142,142,147,0.15); color: rgba(235,235,245,0.6);">
                <i class="fas fa-xmark" style="font-size:10px;"></i>
            </button>
        </div>
        <div id="logDetailContent" class="space-y-3">
            <p class="text-xs text-center" style="color: rgba(235,235,245,0.5);">Loading...</p>
        </div>
    </div>
</div>

<script>
function showLogDetail(id) {
    const modal = document.getElementById('logDetailModal');
    const content = document.getElementById('logDetailContent');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    content.innerHTML = '<p class="text-xs text-center" style="color: rgba(235,235,245,0.5);">Loading...</p>';

    fetch('{{ url("admin/seo/refresh-logs") }}/' + id, {
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        let html = '';
        html += '<div class="grid grid-cols-2 gap-2 text-xs">';
        html += '<div><span style="color:rgba(235,235,245,0.5);">Artikel:</span><br><span class="text-white font-medium">' + escHtml(data.article_title) + '</span></div>';
        html += '<div><span style="color:rgba(235,235,245,0.5);">Waktu:</span><br><span class="text-white">' + escHtml(data.created_at) + '</span></div>';
        html += '<div><span style="color:rgba(235,235,245,0.5);">Status:</span><br><span style="color:' + (data.status === 'refreshed' ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)') + ';">' + escHtml(data.status) + '</span></div>';
        html += '<div><span style="color:rgba(235,235,245,0.5);">Trigger:</span><br><span class="text-white">' + escHtml(data.triggered_by) + '</span></div>';
        html += '<div><span style="color:rgba(235,235,245,0.5);">AI Tokens:</span><br><span style="color:rgba(175,82,222,1);">' + (data.ai_tokens_used || 0).toLocaleString() + '</span></div>';
        html += '</div>';

        if (data.changes && Object.keys(data.changes).length > 0) {
            html += '<div class="mt-2"><p class="text-[10px] uppercase tracking-wide mb-1" style="color:rgba(235,235,245,0.5);">Fields Changed</p>';
            html += '<div class="flex flex-wrap gap-1">';
            Object.keys(data.changes).forEach(k => {
                html += '<span class="px-1.5 py-0.5 rounded-apple text-[10px]" style="background:rgba(10,132,255,0.15);color:rgba(10,132,255,1);">' + escHtml(k) + '</span>';
            });
            html += '</div></div>';
        }

        if (data.before_snapshot && Object.keys(data.before_snapshot).length > 0) {
            html += '<div class="mt-2"><p class="text-[10px] uppercase tracking-wide mb-1" style="color:rgba(235,235,245,0.5);">Before &rarr; After</p>';
            html += '<div class="space-y-2">';
            Object.keys(data.before_snapshot).forEach(k => {
                const bv = data.before_snapshot[k] || '—';
                const av = (data.after_snapshot && data.after_snapshot[k]) || '—';
                html += '<div class="rounded-apple p-2" style="background:rgba(28,28,30,0.6);">';
                html += '<p class="text-[10px] font-semibold mb-1" style="color:rgba(10,132,255,0.8);">' + escHtml(k) + '</p>';
                html += '<p class="text-[10px] mb-1" style="color:rgba(255,59,48,0.7);"><i class="fas fa-minus mr-0.5" style="font-size:8px;"></i>' + escHtml(String(bv).substring(0, 200)) + '</p>';
                html += '<p class="text-[10px]" style="color:rgba(52,199,89,0.8);"><i class="fas fa-plus mr-0.5" style="font-size:8px;"></i>' + escHtml(String(av).substring(0, 200)) + '</p>';
                html += '</div>';
            });
            html += '</div></div>';
        }

        if (data.error_message) {
            html += '<div class="mt-2 p-2 rounded-apple" style="background:rgba(255,69,58,0.08); border:1px solid rgba(255,69,58,0.2);">';
            html += '<p class="text-[10px] uppercase tracking-wide mb-1" style="color:rgba(255,69,58,0.6);">Error Message</p>';
            html += '<p class="text-xs" style="color:rgba(255,69,58,0.9);">' + escHtml(data.error_message) + '</p>';
            html += '</div>';
        }

        content.innerHTML = html;
    })
    .catch(() => {
        content.innerHTML = '<p class="text-xs text-center" style="color:rgba(255,69,58,0.8);">Gagal memuat detail log.</p>';
    });
}

function closeLogDetail() {
    const modal = document.getElementById('logDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('logDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeLogDetail();
});

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}
</script>
@endsection
