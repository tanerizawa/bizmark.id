@extends('layouts.admin')

@section('title', 'Auto-Post Schedules')

@section('content')
<div class="container-custom">
    {{-- Hero Header --}}
    <section class="card-apple p-5 md:p-6 relative overflow-hidden mb-6">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>

        <div class="relative space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Content Automation</p>
                    <h1 class="text-2xl md:text-xl font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-calendar-alt mr-2"></i>Auto-Post Schedules
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Kelola jadwal publikasi artikel otomatis dengan AI
                    </p>
                </div>
                <div class="flex gap-3">
                    <button onclick="toggleLogs()" 
                            class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium transition-apple"
                            style="background: rgba(94,92,230,0.15); color: rgba(94,92,230,1); border: 1px solid rgba(94,92,230,0.3);">
                        <i class="fas fa-terminal mr-2"></i>View Logs
                    </button>
                    <button onclick="document.getElementById('batchModal').classList.remove('hidden')" 
                            class="inline-flex items-center px-4 py-2.5 rounded-apple text-sm font-medium transition-apple"
                            style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                        <i class="fas fa-layer-group mr-2"></i>Generate Batch
                    </button>
                    <a href="{{ route('auto-post.schedules.create') }}" 
                       class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Jadwal Manual
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Status Tabs --}}
    <div class="card-apple mb-6 overflow-hidden">
        <div class="flex border-b" style="border-color: rgba(255,255,255,0.1);">
            <a href="{{ route('auto-post.schedules.index') }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-apple {{ !request('status') ? 'border-b-2' : '' }}"
               style="color: {{ !request('status') ? 'rgba(10,132,255,1)' : 'rgba(235,235,245,0.6)' }}; border-color: {{ !request('status') ? 'rgba(10,132,255,1)' : 'transparent' }};">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-list"></i>
                    <span>All</span>
                    <span class="px-2 py-0.5 rounded-apple text-xs font-bold" 
                          style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.8);">
                        {{ $stats['pending'] + $stats['processing'] + $stats['completed'] + $stats['failed'] }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('auto-post.schedules.index', ['status' => 'pending']) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-apple {{ request('status') === 'pending' ? 'border-b-2' : '' }}"
               style="color: {{ request('status') === 'pending' ? 'rgba(255,214,10,1)' : 'rgba(235,235,245,0.6)' }}; border-color: {{ request('status') === 'pending' ? 'rgba(255,214,10,1)' : 'transparent' }};">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>Pending</span>
                    <span class="px-2 py-0.5 rounded-apple text-xs font-bold" 
                          style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1);">
                        {{ $stats['pending'] }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('auto-post.schedules.index', ['status' => 'processing']) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-apple {{ request('status') === 'processing' ? 'border-b-2' : '' }}"
               style="color: {{ request('status') === 'processing' ? 'rgba(10,132,255,1)' : 'rgba(235,235,245,0.6)' }}; border-color: {{ request('status') === 'processing' ? 'rgba(10,132,255,1)' : 'transparent' }};">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-spinner {{ request('status') === 'processing' ? 'fa-spin' : '' }}"></i>
                    <span>Processing</span>
                    <span class="px-2 py-0.5 rounded-apple text-xs font-bold" 
                          style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                        {{ $stats['processing'] }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('auto-post.schedules.index', ['status' => 'completed']) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-apple {{ request('status') === 'completed' ? 'border-b-2' : '' }}"
               style="color: {{ request('status') === 'completed' ? 'rgba(48,209,88,1)' : 'rgba(235,235,245,0.6)' }}; border-color: {{ request('status') === 'completed' ? 'rgba(48,209,88,1)' : 'transparent' }};">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed</span>
                    <span class="px-2 py-0.5 rounded-apple text-xs font-bold" 
                          style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1);">
                        {{ $stats['completed'] }}
                    </span>
                </div>
            </a>
            
            <a href="{{ route('auto-post.schedules.index', ['status' => 'failed']) }}" 
               class="flex-1 px-6 py-4 text-center font-medium transition-apple {{ request('status') === 'failed' ? 'border-b-2' : '' }}"
               style="color: {{ request('status') === 'failed' ? 'rgba(255,69,58,1)' : 'rgba(235,235,245,0.6)' }}; border-color: {{ request('status') === 'failed' ? 'rgba(255,69,58,1)' : 'transparent' }};">
                <div class="flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Failed</span>
                    <span class="px-2 py-0.5 rounded-apple text-xs font-bold" 
                          style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1);">
                        {{ $stats['failed'] }}
                    </span>
                </div>
            </a>
        </div>
    </div>
    
    {{-- Date Filters (Optional) --}}
    @if(request('date_from') || request('date_to'))
    <div class="card-apple p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm" style="color: rgba(235,235,245,0.6);">
                    <i class="fas fa-filter mr-2"></i>Filtered by date:
                </span>
                @if(request('date_from'))
                    <span class="px-3 py-1 rounded-apple text-xs font-medium" 
                          style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                        From: {{ request('date_from') }}
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="px-3 py-1 rounded-apple text-xs font-medium" 
                          style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                        To: {{ request('date_to') }}
                    </span>
                @endif
            </div>
            <a href="{{ route('auto-post.schedules.index', ['status' => request('status')]) }}" 
               class="px-3 py-1 rounded-apple text-xs font-medium transition-apple"
               style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1);">
                <i class="fas fa-times mr-1"></i>Clear Filters
            </a>
        </div>
    </div>
    @endif

    {{-- Schedules Table --}}
    <div class="card-apple overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y" style="border-color: rgba(255,255,255,0.1);">
                <thead>
                    <tr style="background: rgba(255,255,255,0.03);">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-tag mr-2"></i>Topic
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-calendar mr-2"></i>Scheduled
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-info-circle mr-2"></i>Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-file-alt mr-2"></i>Article
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: rgba(255,255,255,0.1);">
                    @forelse($schedules as $schedule)
                        <tr class="hover-row" data-status="{{ $schedule->status }}">
                            <td class="px-6 py-4">
                                <div class="font-medium" style="color: #FFFFFF;">{{ $schedule->topic?->title ?? 'Topic dihapus' }}</div>
                                <div class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                                    <i class="fas fa-folder text-xs mr-1"></i>{{ $schedule->topic?->category ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm" style="color: rgba(235,235,245,0.9);">
                                    {{ $schedule->scheduled_at->format('d M Y') }}
                                </div>
                                <div class="text-xs" style="color: rgba(235,235,245,0.6);">
                                    {{ $schedule->scheduled_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($schedule->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-apple text-xs font-medium"
                                          style="background: rgba(255,214,10,0.15); color: rgba(255,214,10,1); border: 1px solid rgba(255,214,10,0.3);">
                                        <i class="fas fa-clock mr-1.5"></i>Pending
                                    </span>
                                @elseif($schedule->status === 'processing')
                                    <span class="inline-flex items-center px-3 py-1 rounded-apple text-xs font-medium animate-pulse"
                                          style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">
                                        <i class="fas fa-spinner fa-spin mr-1.5"></i>Processing
                                    </span>
                                    <button onclick="toggleLogs(); setTimeout(() => { document.getElementById('logsContent').scrollTop = document.getElementById('logsContent').scrollHeight; }, 500);"
                                            class="ml-2 inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium transition-apple"
                                            style="background: rgba(94,92,230,0.15); color: rgba(94,92,230,1); border: 1px solid rgba(94,92,230,0.3);">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                @elseif($schedule->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-apple text-xs font-medium"
                                          style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1); border: 1px solid rgba(48,209,88,0.3);">
                                        <i class="fas fa-check-circle mr-1.5"></i>Completed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-apple text-xs font-medium"
                                          style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1); border: 1px solid rgba(255,69,58,0.3);">
                                        <i class="fas fa-exclamation-triangle mr-1.5"></i>Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($schedule->article)
                                    <a href="{{ route('articles.edit', $schedule->article) }}" 
                                       class="inline-flex items-center text-sm font-medium transition-apple"
                                       style="color: rgba(10,132,255,1);">
                                        <i class="fas fa-eye mr-1.5"></i>View Article
                                    </a>
                                @else
                                    <span style="color: rgba(235,235,245,0.4);">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                @if($schedule->status === 'pending')
                                    <form action="{{ route('auto-post.schedules.process-now', $schedule) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple"
                                                style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">
                                            <i class="fas fa-play mr-1.5"></i>Process Now
                                        </button>
                                    </form>
                                @endif
                                @if($schedule->status === 'failed')
                                    <form action="{{ route('auto-post.schedules.retry', $schedule) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple"
                                                style="background: rgba(48,209,88,0.15); color: rgba(48,209,88,1); border: 1px solid rgba(48,209,88,0.3);">
                                            <i class="fas fa-redo mr-1.5"></i>Retry
                                        </button>
                                    </form>
                                @endif
                                @if($schedule->status !== 'completed')
                                    <form action="{{ route('auto-post.schedules.destroy', $schedule) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Yakin hapus jadwal ini?')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 rounded-apple text-xs font-medium transition-apple"
                                                style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1); border: 1px solid rgba(255,69,58,0.3);">
                                            <i class="fas fa-trash mr-1.5"></i>Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                                     style="background: rgba(255,255,255,0.05);">
                                    <i class="fas fa-calendar-times text-2xl" style="color: rgba(235,235,245,0.3);"></i>
                                </div>
                                <p class="text-sm font-medium" style="color: rgba(235,235,245,0.6);">
                                    Belum ada jadwal auto-post
                                </p>
                                <a href="{{ route('auto-post.schedules.create') }}" 
                                   class="inline-flex items-center px-4 py-2 text-xs font-medium rounded-apple transition-apple"
                                   style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">
                                    <i class="fas fa-plus mr-2"></i>Buat Jadwal Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($schedules->hasPages())
        <div class="px-6 py-4" style="border-top: 1px solid rgba(255,255,255,0.1);">
            {{ $schedules->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Batch Modal --}}
<div id="batchModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(10px);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="card-apple p-6 w-full max-w-md">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold" style="color: #FFFFFF;">Generate Batch Schedule</h3>
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">Buat jadwal otomatis untuk satu hari</p>
                </div>
                <button type="button" 
                        onclick="document.getElementById('batchModal').classList.add('hidden')"
                        class="w-8 h-8 rounded-full flex items-center justify-center transition-apple"
                        style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.6);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('auto-post.schedules.generate-batch') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.9);">
                        <i class="fas fa-calendar mr-2"></i>Pilih Tanggal
                    </label>
                    <input type="date" 
                           name="date" 
                           required 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                           class="apple-input w-full rounded-apple">
                    <p class="text-xs mt-2" style="color: rgba(235,235,245,0.5);">
                        Sistem akan generate jadwal berdasarkan topik yang tersedia
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="document.getElementById('batchModal').classList.add('hidden')" 
                            class="flex-1 px-4 py-2.5 rounded-apple text-sm font-medium transition-apple"
                            style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-magic mr-2"></i>Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Live Logs Panel --}}
<div id="logsPanel" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background: rgba(0,0,0,0.5); backdrop-filter: blur(10px);">
    <div class="flex items-start justify-center min-h-screen px-4 pt-20">
        <div class="card-apple w-full max-w-6xl" style="max-height: 80vh; display: flex; flex-direction: column;">
            <div class="flex items-center justify-between p-6 border-b" style="border-color: rgba(255,255,255,0.1);">
                <div>
                    <h3 class="text-lg font-bold" style="color: #FFFFFF;">
                        <i class="fas fa-terminal mr-2"></i>Auto-Post Processing Logs
                    </h3>
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">Real-time monitoring of article generation</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="refreshLogs()" 
                            class="inline-flex items-center px-3 py-2 rounded-apple text-xs font-medium transition-apple"
                            style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1); border: 1px solid rgba(10,132,255,0.3);">
                        <i class="fas fa-sync-alt mr-1.5"></i>Refresh
                    </button>
                    <button type="button" 
                            onclick="toggleLogs()"
                            class="w-8 h-8 rounded-full flex items-center justify-center transition-apple"
                            style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.6);">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6" id="logsContent" style="font-family: 'Courier New', monospace; font-size: 12px; background: rgba(0,0,0,0.3);">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2" style="color: rgba(10,132,255,1);"></i>
                        <p style="color: rgba(235,235,245,0.6);">Loading logs...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let logsRefreshInterval = null;
let pageRefreshInterval = null;

// Auto-refresh page if there are processing schedules
document.addEventListener('DOMContentLoaded', function() {
    const hasProcessing = document.querySelectorAll('[data-status="processing"]').length > 0;
    if (hasProcessing) {
        // Refresh page every 10 seconds if there are processing items
        pageRefreshInterval = setInterval(function() {
            window.location.reload();
        }, 10000);
        
        // Show notification
        console.log('Auto-refresh enabled: Processing schedules detected');
    }
});

function toggleLogs() {
    const panel = document.getElementById('logsPanel');
    if (panel.classList.contains('hidden')) {
        panel.classList.remove('hidden');
        refreshLogs();
        // Auto-refresh every 3 seconds
        logsRefreshInterval = setInterval(refreshLogs, 3000);
    } else {
        panel.classList.add('hidden');
        if (logsRefreshInterval) {
            clearInterval(logsRefreshInterval);
            logsRefreshInterval = null;
        }
    }
}

async function refreshLogs() {
    const content = document.getElementById('logsContent');
    
    try {
        const response = await fetch('{{ route("auto-post.logs.recent") }}?format=json');
        const data = await response.json();
        
        let html = '';
        
        if (data.logs && data.logs.length > 0) {
            data.logs.forEach(log => {
                const levelColors = {
                    'info': 'rgba(10,132,255,1)',
                    'success': 'rgba(48,209,88,1)',
                    'warning': 'rgba(255,214,10,1)',
                    'error': 'rgba(255,69,58,1)'
                };
                
                const levelIcons = {
                    'info': 'fa-info-circle',
                    'success': 'fa-check-circle',
                    'warning': 'fa-exclamation-triangle',
                    'error': 'fa-times-circle'
                };
                
                const color = levelColors[log.level] || 'rgba(235,235,245,0.6)';
                const icon = levelIcons[log.level] || 'fa-circle';
                
                html += `
                    <div class="mb-2 p-3 rounded-apple" style="background: rgba(255,255,255,0.02); border-left: 3px solid ${color};">
                        <div class="flex items-start gap-3">
                            <i class="fas ${icon} mt-1" style="color: ${color};"></i>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span style="color: rgba(235,235,245,0.5); font-size: 11px;">${log.created_at}</span>
                                    ${log.schedule_id ? `<span class="px-2 py-0.5 rounded text-xs" style="background: rgba(94,92,230,0.15); color: rgba(94,92,230,1);">Schedule #${log.schedule_id}</span>` : ''}
                                </div>
                                <div style="color: ${color}; font-weight: 500;">${log.event}</div>
                                <div style="color: rgba(235,235,245,0.9); margin-top: 4px;">${log.message}</div>
                                ${log.context ? `<details class="mt-2"><summary style="color: rgba(235,235,245,0.5); cursor: pointer; font-size: 11px;">View Context</summary><pre class="mt-2 p-2 rounded" style="background: rgba(0,0,0,0.3); color: rgba(235,235,245,0.6); font-size: 10px; overflow-x: auto;">${JSON.stringify(log.context, null, 2)}</pre></details>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html = `
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-2xl mb-2" style="color: rgba(235,235,245,0.3);"></i>
                    <p style="color: rgba(235,235,245,0.5);">No logs available</p>
                </div>
            `;
        }
        
        content.innerHTML = html;
        
        // Auto-scroll to bottom
        content.scrollTop = content.scrollHeight;
        
    } catch (error) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-2xl mb-2" style="color: rgba(255,69,58,1);"></i>
                <p style="color: rgba(255,69,58,1);">Failed to load logs</p>
                <p style="color: rgba(235,235,245,0.5); font-size: 11px;">${error.message}</p>
            </div>
        `;
    }
}
</script>
@endsection
