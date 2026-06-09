@extends('layouts.admin')

@section('title', $project->name)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
<style>
    .project-shell {
        font-family: 'Manrope', 'Inter', 'Segoe UI', sans-serif;
        letter-spacing: -0.01em;
        color: var(--dark-text-primary);
    }

    .project-shell .page-card {
        background: var(--dark-bg-elevated);
        border: 1px solid var(--dark-separator);
        border-radius: 16px;
        padding: 1.25rem;
    }

    @media (min-width: 768px) {
        .project-shell .page-card {
            padding: 1.5rem;
        }
    }

    .project-shell .section-label {
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--dark-text-tertiary);
    }

    .project-shell .muted {
        color: var(--dark-text-secondary);
    }

    .project-shell .muted-2 {
        color: var(--dark-text-tertiary);
    }

    .project-shell .kpi-card {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .project-shell .kpi-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #FFFFFF;
    }

    .project-shell .card-title {
        font-size: 0.95rem;
        font-weight: 700;
    }

    .project-shell .tab-button {
        background: transparent;
        border: 1px solid transparent;
        color: var(--dark-text-secondary);
        padding: 0.6rem 0.75rem;
        border-radius: 10px;
        font-weight: 600;
        min-height: 42px;
    }

    .project-shell .tab-button.active {
        background: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
        border-color: var(--dark-separator);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
    }

    .project-shell .tab-button:hover {
        background: var(--dark-bg-tertiary);
        color: var(--dark-text-primary);
    }

    .project-shell a {
        text-decoration: none;
    }

    .project-shell a:hover {
        text-decoration: none;
    }

    .project-shell .pill {
        padding: 0.35rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .project-shell .data-block {
        padding: 0.85rem 1rem;
        border-radius: 12px;
        border: 1px solid var(--dark-separator);
        background: rgba(255, 255, 255, 0.02);
    }
</style>
@endpush

@section('content')
@php
    $totalTasks = $project->tasks->count();
    $completedTasks = $project->tasks->where('status', 'done')->count();
    $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
    $documentsCount = $project->documents->count();
    $paymentProgress = $totalBudget > 0 ? round(($totalReceived / $totalBudget) * 100, 1) : 0;
    $permitCompletionRate = $statistics['completion_rate'] ?? 0;
    $permitCompleted = $statistics['completed'] ?? 0;
    $clientDisplay = $project->client ? ($project->client->company_name ?? $project->client->name) : ($project->client_name ?? 'Klien Bizmark');
    $statusColor = match($project->status->code ?? '') {
        'PENAWARAN' => 'background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 0.95); border: 1px solid rgba(255, 149, 0, 0.4);',
        'KONTRAK' => 'background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 0.95); border: 1px solid rgba(10, 132, 255, 0.4);',
        'PENGUMPULAN_DOK' => 'background: rgba(255, 159, 10, 0.2); color: rgba(255, 159, 10, 0.95); border: 1px solid rgba(255, 159, 10, 0.4);',
        'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS' => 'background: rgba(191, 90, 242, 0.2); color: rgba(191, 90, 242, 0.95); border: 1px solid rgba(191, 90, 242, 0.4);',
        'MENUNGGU_PERSETUJUAN' => 'background: rgba(255, 204, 0, 0.2); color: rgba(255, 204, 0, 0.95); border: 1px solid rgba(255, 204, 0, 0.4);',
        'SK_TERBIT' => 'background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 0.95); border: 1px solid rgba(52, 199, 89, 0.4);',
        'SELESAI' => 'background: rgba(16, 185, 129, 0.2); color: rgba(16, 185, 129, 0.95); border: 1px solid rgba(16, 185, 129, 0.4);',
        'DIBATALKAN' => 'background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 0.95); border: 1px solid rgba(255, 59, 48, 0.4);',
        'DITUNDA' => 'background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 0.95); border: 1px solid rgba(142, 142, 147, 0.4);',
        default => 'background: rgba(58, 58, 60, 0.4); color: rgba(235, 235, 245, 0.85); border: 1px solid rgba(58, 58, 60, 0.8);'
    };
    
    // Deadline state logic - FIXED: Check if project is completed first
    if ($project->completed_at) {
        // Project completed - use actual completion status
        $completionStatus = $project->getCompletionStatus();
        $deadlineState = match($completionStatus) {
            'early' => 'completed-early',
            'on-time' => 'completed-ontime',
            'late' => 'completed-late',
            default => 'completed'
        };
    } else {
        // Project not completed - check deadline vs today
        $deadlineState = $project->deadline
            ? ($project->deadline->isPast() ? 'overdue' : ($project->deadline->diffInDays(now()) <= 7 ? 'urgent' : 'ontrack'))
            : null;
    }
    
    $deadlineBadge = match($deadlineState) {
        'overdue' => ['label' => 'Terlambat', 'style' => 'background: rgba(255,59,48,0.15); color: rgba(255,59,48,0.95);'],
        'urgent' => ['label' => 'Mendesak', 'style' => 'background: rgba(255,159,10,0.15); color: rgba(255,159,10,0.95);'],
        'ontrack' => ['label' => 'On Track', 'style' => 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,0.95);'],
        'completed-early' => ['label' => 'Selesai Lebih Cepat ⚡', 'style' => 'background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.95);'],
        'completed-ontime' => ['label' => 'Selesai Tepat Waktu ⏰', 'style' => 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,0.95);'],
        'completed-late' => ['label' => 'Selesai Terlambat ⚠️', 'style' => 'background: rgba(255,149,10,0.15); color: rgba(255,149,10,0.95);'],
        'completed' => ['label' => 'Selesai', 'style' => 'background: rgba(52,199,89,0.15); color: rgba(52,199,89,0.95);'],
        default => ['label' => 'Jadwal belum ditentukan', 'style' => 'background: rgba(142,142,147,0.15); color: rgba(142,142,147,0.9);']
    };
@endphp

<div class="project-shell max-w-7xl mx-auto space-y-4 md:space-y-5"
     x-data="{
         activeTab: 'overview',
         paymentModalOpen: false,
         expenseModalOpen: false,
         receivableEnabled: false,
         init() {
             const hash = window.location.hash.substring(1);
             const validTabs = ['overview', 'financial', 'permits', 'tasks', 'documents'];
             if (hash && validTabs.includes(hash)) {
                 this.activeTab = hash;
                 return;
             }
             const params = new URLSearchParams(window.location.search);
             const tab = params.get('tab');
             if (tab && validTabs.includes(tab)) {
                 this.activeTab = tab;
             }
         }
     }"
     x-init="$watch('activeTab', value => {
         history.replaceState(null, null, '#' + value);
         if (value === 'permits' && typeof window.initializePermitsSortable === 'function') {
             setTimeout(() => window.initializePermitsSortable(), 100);
         } else if (value === 'tasks' && typeof window.initializeTasksSortable === 'function') {
             setTimeout(() => window.initializeTasksSortable(), 100);
         }
     })">
    <section class="page-card relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none hidden md:block" aria-hidden="true">
            <div class="w-56 h-56 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-10 -right-4"></div>
            <div class="w-40 h-40 bg-apple-green opacity-15 blur-2xl rounded-full absolute bottom-2 left-6"></div>
        </div>
        <div class="relative flex flex-col gap-4">
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
                <div class="space-y-2.5">
                    <div class="flex flex-wrap items-center gap-2 section-label">
                        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 hover:text-white transition-apple">
                            <i class="fas fa-arrow-left text-xs"></i> Projects
                        </a>
                        <span class="text-dark-text-tertiary">/</span>
                        <span class="muted">{{ $project->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-lg md:text-xl font-semibold text-white leading-tight">{{ $project->name }}</h1>
                        <span class="pill" style="{{ $statusColor }}">
                            {{ $project->status->name ?? 'Status Tidak Tersedia' }}
                        </span>
                    </div>
                    <p class="text-sm leading-relaxed muted">
                        {{ $project->description ?? 'Belum ada deskripsi proyek. Tambahkan ringkasan agar tim memahami konteks bisnis.' }}
                    </p>
                    <div class="flex flex-wrap gap-3 text-sm muted">
                        <span class="inline-flex items-center gap-1.5"><i class="fas fa-user-tie"></i>{{ $clientDisplay }}</span>
                        @if($project->institution)
                            <span class="inline-flex items-center gap-1.5"><i class="fas fa-landmark"></i>{{ $project->institution->name }}</span>
                        @endif
                        <span class="inline-flex items-center gap-1.5"><i class="fas fa-sync"></i>{{ $project->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="space-y-3 w-full xl:w-72">
                    <div class="data-block">
                        <p class="section-label mb-1">Timeline</p>
                        <p class="text-sm font-semibold text-white">
                            {{ $project->deadline ? $project->deadline->format('d M Y') : 'Deadline belum ditentukan' }}
                        </p>
                        <p class="text-xs muted">
                            {{ $project->start_date ? 'Dimulai ' . $project->start_date->format('d M Y') : 'Tanggal mulai belum tersedia' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2 w-full">
                        <a href="{{ route('projects.edit', $project) }}" class="btn-primary-sm flex-1">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="flex-1" x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus proyek ini?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary-sm w-full bg-apple-red/15 text-apple-red border-apple-red/40">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="kpi-card bg-[rgba(10,132,255,0.08)]">
                    <p class="text-[11px] uppercase tracking-widest text-[rgba(10,132,255,0.9)]">Nilai Kontrak</p>
                    <p class="kpi-value mt-1">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
                    <p class="text-xs muted">Budget disepakati</p>
                </div>
                <div class="kpi-card bg-[rgba(52,199,89,0.08)]">
                    <p class="text-[11px] uppercase tracking-widest text-[rgba(52,199,89,0.9)]">Diterima</p>
                    <p class="kpi-value mt-1">Rp {{ number_format($totalReceived, 0, ',', '.') }}</p>
                    <p class="text-xs muted">{{ $paymentProgress }}%</p>
                </div>
                <div class="kpi-card bg-[rgba(191,90,242,0.08)]">
                    <p class="text-[11px] uppercase tracking-widest text-[rgba(191,90,242,0.9)]">Progress Izin</p>
                    <p class="kpi-value mt-1">{{ $permitCompletionRate }}%</p>
                    <p class="text-xs muted">{{ $permitCompleted }}/{{ $statistics['total'] ?? 0 }} izin</p>
                </div>
                <div class="kpi-card bg-[rgba(255,149,0,0.08)]">
                    <p class="text-[11px] uppercase tracking-widest text-[rgba(255,149,0,0.9)]">Dokumen</p>
                    <p class="kpi-value mt-1">{{ $documentsCount }}</p>
                    <p class="text-xs muted">Asset tersimpan</p>
                </div>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <section class="page-card space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="section-label">Operasional</p>
                <h2 class="card-title text-white">Update Status & Health</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="{{ $deadlineBadge['style'] }}">{{ $deadlineBadge['label'] }}</span>
                <span class="text-xs muted">{{ $project->updated_at->diffForHumans() }}</span>
            </div>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <div class="xl:col-span-2 space-y-3">
                <form action="{{ route('projects.update-status', $project) }}" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    @csrf
                    @method('PATCH')
                    <div class="md:col-span-4 space-y-1">
                        <label class="text-[11px] uppercase tracking-widest block muted">Status</label>
                        <select name="status_id" class="w-full">
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $project->status_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-5 space-y-1">
                        <label class="text-[11px] uppercase tracking-widest block muted">Catatan</label>
                        <input type="text" name="notes" placeholder="Catatan singkat (opsional)" class="w-full">
                    </div>
                    <div class="md:col-span-3 flex items-end">
                        <button type="submit" class="btn-primary-sm w-full">
                            <i class="fas fa-save mr-2"></i>Update
                        </button>
                    </div>
                </form>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="data-block">
                        <p class="text-[11px] uppercase tracking-widest muted mb-1">Target Selesai</p>
                        <p class="text-sm font-semibold text-white">
                            {{ $project->deadline ? $project->deadline->format('d M Y') : 'Belum ditentukan' }}
                        </p>
                        <p class="text-xs muted">
                            {{ $project->deadline ? $project->deadline->diffForHumans() : 'Tambahkan deadline' }}
                        </p>
                    </div>
                    
                    @if($project->completed_at)
                    <div class="data-block">
                        <p class="text-[11px] uppercase tracking-widest muted mb-1">Selesai Aktual</p>
                        <p class="text-sm font-semibold text-white">
                            {{ $project->completed_at->format('d M Y') }}
                        </p>
                        @php
                            $completionStatus = $project->getCompletionStatus();
                            $statusColor = $project->getCompletionStatusColor();
                        @endphp
                        @if($completionStatus)
                        <p class="text-xs flex items-center gap-1" style="color: {{ $statusColor }}">
                            @if($completionStatus === 'early')
                                <i class="fas fa-bolt"></i>
                            @elseif($completionStatus === 'on-time')
                                <i class="fas fa-check-circle"></i>
                            @else
                                <i class="fas fa-exclamation-triangle"></i>
                            @endif
                            {{ $project->getCompletionStatusMessage() }}
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="data-block">
                        <p class="text-[11px] uppercase tracking-widest muted mb-1">Receivable Outstanding</p>
                        <p class="text-sm font-semibold text-white">Rp {{ number_format($receivableOutstanding, 0, ',', '.') }}</p>
                        <p class="text-xs muted">Nilai piutang belum dibayar</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="data-block space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[11px] uppercase tracking-widest muted">Snapshot</p>
                        <h3 class="card-title text-white">Progress Proyek</h3>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <div class="flex items-center justify-between text-xs muted">
                            <span>Payment Progress</span>
                            <span>{{ $paymentProgress }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-gradient-to-r from-apple-blue to-apple-green" style="width: {{ min(100, $paymentProgress) }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs muted">
                            <span>Permit Completion</span>
                            <span>{{ $permitCompletionRate }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-gradient-to-r from-apple-purple to-apple-blue" style="width: {{ min(100, $permitCompletionRate) }}%"></div>
                        </div>
                        <p class="text-xs mt-1 muted">{{ $permitCompleted }}/{{ $statistics['total'] ?? 0 }} izin</p>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs muted">
                            <span>Task Completion</span>
                            <span>{{ $taskProgress }}%</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-white/10 mt-1">
                            <div class="h-full rounded-full bg-gradient-to-r from-apple-orange to-apple-pink" style="width: {{ min(100, $taskProgress) }}%"></div>
                        </div>
                        <p class="text-xs mt-1 muted">{{ $completedTasks }}/{{ $totalTasks }} tugas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
        <!-- Main Information -->
        <div class="xl:col-span-2 min-w-0 space-y-3">
            <!-- Tab Navigation -->
            <div class="page-card p-2" role="tablist">
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="tab-button flex-1 px-3 py-2 rounded-apple text-sm font-semibold transition-apple"
                            :class="{ 'active': activeTab === 'overview' }"
                            @click="activeTab = 'overview'">
                        <i class="fas fa-info-circle mr-2"></i>Overview
                    </button>
                    <button type="button" class="tab-button flex-1 px-3 py-2 rounded-apple text-sm font-semibold transition-apple"
                            :class="{ 'active': activeTab === 'permits' }"
                            @click="activeTab = 'permits'">
                        <i class="fas fa-certificate mr-2"></i>Izin & Prasyarat
                    </button>
                    <button type="button" class="tab-button flex-1 px-3 py-2 rounded-apple text-sm font-semibold transition-apple"
                            :class="{ 'active': activeTab === 'tasks' }"
                            @click="activeTab = 'tasks'">
                        <i class="fas fa-tasks mr-2"></i>Tugas
                    </button>
                    <button type="button" class="tab-button flex-1 px-3 py-2 rounded-apple text-sm font-semibold transition-apple"
                            :class="{ 'active': activeTab === 'documents' }"
                            @click="activeTab = 'documents'">
                        <i class="fas fa-file-alt mr-2"></i>Dokumen
                    </button>
                    <button type="button" class="tab-button flex-1 px-3 py-2 rounded-apple text-sm font-semibold transition-apple"
                            :class="{ 'active': activeTab === 'financial' }"
                            @click="activeTab = 'financial'">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Financial
                    </button>
                </div>
            </div>

            <!-- Tab Content: Overview -->
            <div x-show="activeTab === 'overview'" x-transition:enter.duration.200ms class="space-y-3">
            <!-- Project Overview -->
            <div class="page-card space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="section-label">Informasi Proyek</p>
                        <h3 class="card-title text-white">Ringkasan Proyek</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Nama Proyek</p>
                        <p class="text-sm font-semibold break-words">{{ $project->name }}</p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Institusi Tujuan</p>
                        <p class="text-sm break-words muted">{{ $project->institution->name ?? '-' }}</p>
                    </div>
                    
                    <div class="md:col-span-2 space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Deskripsi</p>
                        <p class="text-sm break-words muted">{{ $project->description ?? '-' }}</p>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-[11px] uppercase tracking-widest muted">Status Saat Ini</p>
                        @php
                            $statusBadgeStyle = match($project->status->code ?? '') {
                                'PENAWARAN' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'KONTRAK' => 'background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);',
                                'PENGUMPULAN_DOK' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'PROSES_DLH', 'PROSES_BPN', 'PROSES_OSS', 'PROSES_NOTARIS' => 'background: rgba(191, 90, 242, 0.3); color: rgba(191, 90, 242, 1);',
                                'MENUNGGU_PERSETUJUAN' => 'background: rgba(255, 149, 0, 0.3); color: rgba(255, 149, 0, 1);',
                                'SK_TERBIT' => 'background: rgba(52, 199, 89, 0.3); color: rgba(52, 199, 89, 1);',
                                'SELESAI' => 'background: rgba(16, 185, 129, 0.3); color: rgba(16, 185, 129, 1);',
                                'DIBATALKAN' => 'background: rgba(255, 59, 48, 0.3); color: rgba(255, 59, 48, 1);',
                                'DITUNDA' => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);',
                                default => 'background: rgba(142, 142, 147, 0.3); color: rgba(142, 142, 147, 1);'
                            };
                        @endphp
                        <span class="pill" style="{{ $statusBadgeStyle }}">
                            {{ $project->status->name }}
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-[11px] uppercase tracking-widest muted">Progress</p>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 rounded-full h-1.5 bg-white/10">
                                <div class="h-full rounded-full transition-all duration-300" 
                                     style="width: {{ $project->progress_percentage }}%; background: linear-gradient(90deg, rgba(10, 132, 255, 1), rgba(30, 150, 255, 1));"></div>
                            </div>
                            <span class="text-sm font-semibold">{{ $project->progress_percentage }}%</span>
                        </div>
                    </div>
                    
                    <!-- Timeline Information -->
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Tanggal Mulai</p>
                        <p class="text-sm break-words">
                            @if($project->start_date)
                                <i class="fas fa-calendar-check mr-1 text-apple-green"></i>
                                {{ $project->start_date->format('d M Y') }}
                            @else
                                <span class="muted">Belum ditentukan</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Target Penyelesaian</p>
                        <p class="text-sm break-words">
                            @if($project->deadline)
                                <i class="fas fa-flag mr-1 text-apple-orange"></i>
                                {{ $project->deadline->format('d M Y') }}
                                @if($project->deadline->isPast() && !$project->actual_completion_date)
                                    <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-apple-red/20 text-apple-red">
                                        Terlambat {{ $project->deadline->diffForHumans(null, true) }}
                                    </span>
                                @elseif($project->deadline->diffInDays(now()) <= 7 && $project->deadline->isFuture())
                                    <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-apple-orange/20 text-apple-orange">
                                        {{ $project->deadline->diffForHumans() }}
                                    </span>
                                @endif
                            @else
                                <span class="muted">Belum ditentukan</span>
                            @endif
                        </p>
                    </div>
                    
                    @if($project->actual_completion_date)
                    <div class="md:col-span-2 space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Tanggal Penyelesaian Aktual</p>
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-apple-green">
                                <i class="fas fa-check-circle mr-1"></i>
                                {{ $project->actual_completion_date->format('d M Y') }}
                            </p>
                            @if($project->deadline)
                                @php
                                    $daysDiff = $project->actual_completion_date->diffInDays($project->deadline, false);
                                    $isEarly = $daysDiff > 0;
                                    $isLate = $daysDiff < 0;
                                @endphp
                                @if($isEarly)
                                    <span class="text-[11px] px-2 py-0.5 rounded bg-apple-green/20 text-apple-green">
                                        <i class="fas fa-thumbs-up mr-1"></i>{{ abs($daysDiff) }} hari lebih cepat
                                    </span>
                                @elseif($isLate)
                                    <span class="text-[11px] px-2 py-0.5 rounded bg-apple-red/20 text-apple-red">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ abs($daysDiff) }} hari terlambat
                                    </span>
                                @else
                                    <span class="text-[11px] px-2 py-0.5 rounded bg-apple-green/20 text-apple-green">
                                        <i class="fas fa-bullseye mr-1"></i>Tepat waktu
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Client Information -->
            <div class="page-card space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="section-label">Informasi Klien</p>
                        <h3 class="card-title text-white">Kontak & Perusahaan</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Nama Klien</p>
                        @if($project->client)
                            <p class="text-sm font-semibold break-words flex items-center">
                                {{ $project->client->company_name ?? $project->client->name }}
                                <a href="{{ route('clients.show', $project->client) }}" 
                                   class="ml-2 text-[12px] px-2 py-0.5 rounded transition-colors bg-apple-blue/15 text-apple-blue">
                                    <i class="fas fa-external-link-alt mr-1"></i>Lihat Detail
                                </a>
                            </p>
                            @if($project->client->company_name && $project->client->name != $project->client->company_name)
                                <p class="text-xs mt-0.5 muted">Contact: {{ $project->client->name }}</p>
                            @endif
                        @else
                            <p class="text-sm font-semibold">{{ $project->client_name ?? '-' }}</p>
                        @endif
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Kontak</p>
                        @if($project->client)
                            <div class="space-y-1.5">
                                @if($project->client->email)
                                    <p class="text-sm flex items-center">
                                        <i class="fas fa-envelope mr-2 text-apple-blue"></i>
                                        <a href="mailto:{{ $project->client->email }}" class="text-apple-blue hover:underline">{{ $project->client->email }}</a>
                                    </p>
                                @endif
                                @if($project->client->mobile)
                                    <p class="text-sm flex items-center">
                                        <i class="fab fa-whatsapp mr-2 text-apple-green"></i>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $project->client->mobile) }}" target="_blank" class="text-apple-blue hover:underline">{{ $project->client->mobile }}</a>
                                    </p>
                                @elseif($project->client->phone)
                                    <p class="text-sm flex items-center">
                                        <i class="fas fa-phone mr-2 text-apple-blue"></i>
                                        <a href="tel:{{ $project->client->phone }}" class="text-apple-blue hover:underline">{{ $project->client->phone }}</a>
                                    </p>
                                @endif
                                @if(!$project->client->email && !$project->client->mobile && !$project->client->phone)
                                    <p class="text-sm muted">-</p>
                                @endif
                            </div>
                        @else
                            <p class="text-sm">{{ $project->client_contact ?? '-' }}</p>
                        @endif
                    </div>
                    
                    @if(($project->client && $project->client->address) || $project->client_address)
                    <div class="md:col-span-2 space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Alamat</p>
                        <p class="text-sm break-words muted">
                            {{ $project->client ? $project->client->address : $project->client_address }}
                            @if($project->client && ($project->client->city || $project->client->province))
                                <span class="text-xs muted">
                                    @if($project->client->city), {{ $project->client->city }}@endif
                                    @if($project->client->province), {{ $project->client->province }}@endif
                                    @if($project->client->postal_code) {{ $project->client->postal_code }}@endif
                                </span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="page-card space-y-3">
                <div>
                    <p class="section-label">Jadwal Proyek</p>
                    <h3 class="card-title text-white">Timeline & Durasi</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Tanggal Mulai</p>
                        <p class="text-sm font-semibold">
                            {{ $project->start_date ? $project->start_date->format('d M Y') : 'Belum ditentukan' }}
                        </p>
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Target Selesai</p>
                        @if($project->deadline)
                            @php
                                // FIXED: Check if project is completed first
                                if ($project->completed_at) {
                                    // Project completed - show actual completion status
                                    $completionStatus = $project->getCompletionStatus();
                                    $isOverdue = $completionStatus === 'late';
                                    $isUrgent = false;
                                    $isOnTime = $completionStatus === 'on-time';
                                    $isEarly = $completionStatus === 'early';
                                } else {
                                    // Project ongoing - check deadline vs today
                                    $isOverdue = $project->deadline->isPast();
                                    $isUrgent = !$isOverdue && $project->deadline->diffInDays(now()) <= 7;
                                    $isOnTime = false;
                                    $isEarly = false;
                                }
                            @endphp
                            <p class="text-sm font-semibold" style="color: {{ $isOverdue ? 'rgba(255, 59, 48, 1)' : ($isUrgent ? 'rgba(255, 149, 0, 1)' : ($isEarly ? 'rgba(10, 132, 255, 1)' : '#FFFFFF')) }};">
                                {{ $project->deadline->format('d M Y') }}
                                @if($project->completed_at)
                                    @if($isEarly)
                                        <span class="text-xs text-[rgba(10,132,255,0.8)]">⚡ (Lebih cepat)</span>
                                    @elseif($isOnTime)
                                        <span class="text-xs text-[rgba(52,199,89,0.8)]">⏰ (Tepat waktu)</span>
                                    @elseif($isOverdue)
                                        <span class="text-xs text-[rgba(255,149,10,0.8)]">⚠️ (Terlambat)</span>
                                    @endif
                                @else
                                    @if($isOverdue)
                                        <span class="text-xs muted">(Terlambat)</span>
                                    @elseif($isUrgent)
                                        <span class="text-xs muted">(Mendesak)</span>
                                    @endif
                                @endif
                            </p>
                        @else
                            <p class="text-sm muted-2">Belum ditentukan</p>
                        @endif
                    </div>
                    
                    <div class="space-y-1">
                        <p class="text-[11px] uppercase tracking-widest muted">Durasi</p>
                        @if($project->start_date && $project->deadline)
                            <p class="text-sm font-semibold">{{ $project->start_date->diffInDays($project->deadline) }} hari</p>
                        @else
                            <p class="text-sm muted-2">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Financial Summary -->
            <div class="page-card space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="card-title text-white flex items-center">
                        <i class="fas fa-wallet mr-2 text-apple-blue-dark"></i>Ringkasan Keuangan
                    </h3>
                    <button @click="activeTab = 'financial'" class="text-xs px-2.5 py-1 rounded-md transition-colors bg-apple-blue/15 text-apple-blue">
                        <i class="fas fa-arrow-right mr-1"></i>Lihat Detail
                    </button>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="data-block text-center">
                        <p class="text-xs mb-1 muted">Nilai Kontrak</p>
                        <p class="text-sm font-bold">Rp {{ number_format($totalBudget, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="data-block text-center bg-[rgba(52,199,89,0.12)] border-[rgba(52,199,89,0.25)]">
                        <p class="text-xs mb-1 muted">Diterima</p>
                        <p class="text-sm font-bold text-apple-green">Rp {{ number_format($totalReceived, 0, ',', '.') }}</p>
                        <p class="text-xs muted-2">
                            {{ $totalBudget > 0 ? number_format(($totalReceived / $totalBudget) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    
                    <div class="data-block text-center bg-[rgba(255,59,48,0.12)] border-[rgba(255,59,48,0.25)]">
                        <p class="text-xs mb-1 muted">Pengeluaran</p>
                        <p class="text-sm font-bold text-apple-red">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                        <p class="text-xs muted-2">
                            {{ $totalBudget > 0 ? number_format(($totalExpenses / $totalBudget) * 100, 1) : 0 }}%
                        </p>
                    </div>
                    
                    <div class="data-block text-center" style="background: {{ $profitMargin >= 0 ? 'rgba(0, 122, 255, 0.12)' : 'rgba(255, 59, 48, 0.12)' }}; border-color: {{ $profitMargin >= 0 ? 'rgba(0, 122, 255, 0.25)' : 'rgba(255, 59, 48, 0.25)' }};">
                        <p class="text-xs mb-1 muted">Profit</p>
                        <p class="text-sm font-bold" style="color: {{ $profitMargin >= 0 ? 'rgba(0, 122, 255, 1)' : 'rgba(255, 59, 48, 1)' }};">
                            {{ $profitMargin < 0 ? '-' : '' }}Rp {{ number_format(abs($profitMargin), 0, ',', '.') }}
                        </p>
                        <p class="text-xs muted-2">
                            {{ $totalReceived > 0 ? number_format(($profitMargin / $totalReceived) * 100, 1) : 0 }}% margin
                        </p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($project->notes)
            <div class="page-card space-y-2">
                <h3 class="card-title text-white flex items-center">
                    <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Catatan
                </h3>
                <p class="text-sm whitespace-pre-line muted">{{ $project->notes }}</p>
            </div>
            @endif
            </div>
            <!-- End of Overview Tab Content -->

            <!-- Tab Content: Financial (Sprint 6) -->
            <div x-show="activeTab === 'financial'" x-transition:enter.duration.200ms>
                <div class="space-y-6 min-w-0" data-scope="financial-tab">
                    @include('projects.partials.financial-tab')
                </div>
            </div>

            <!-- Tab Content: Permits -->
            <div x-show="activeTab === 'permits'" x-transition:enter.duration.200ms>
                <div class="space-y-6 min-w-0" data-scope="permits-tab">
                    @include('projects.partials.permits-tab')
                </div>
            </div>

            <!-- Tab Content: Tasks -->
            <div x-show="activeTab === 'tasks'" x-transition:enter.duration.200ms>
                <div class="space-y-6 min-w-0" data-scope="tasks-tab">
                    @include('projects.partials.tasks-tab')
                </div>
            </div>

            <!-- Tab Content: Documents -->
            <div x-show="activeTab === 'documents'" x-transition:enter.duration.200ms>
                <div class="space-y-6 min-w-0" data-scope="documents-tab">
                    <div class="page-card space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="card-title text-white flex items-center">
                                <i class="fas fa-file-alt mr-2 text-apple-blue-dark"></i>Daftar Dokumen Proyek
                            </h3>
                            <a href="{{ route('documents.create', ['project_id' => $project->id]) }}" 
                               class="btn-primary-sm bg-[rgba(10,132,255,0.9)] text-white">
                                <i class="fas fa-plus mr-2"></i>Tambah Dokumen
                            </a>
                        </div>

                        @if($project->documents->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($project->documents as $document)
                                    <div class="data-block">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-shrink-0">
                                                @php
                                                    $iconColor = match($document->type) {
                                                        'KONTRAK' => 'rgba(10, 132, 255, 1)',
                                                        'IZIN' => 'rgba(52, 199, 89, 1)',
                                                        'LAPORAN' => 'rgba(191, 90, 242, 1)',
                                                        'SURAT' => 'rgba(255, 149, 0, 1)',
                                                        default => 'rgba(142, 142, 147, 1)'
                                                    };
                                                @endphp
                                                <i class="fas fa-file-alt text-xl" style="color: {{ $iconColor }};"></i>
                                            </div>
                                            
                                            <div class="flex-1 min-w-0 space-y-1">
                                                <a href="{{ route('documents.show', $document) }}" 
                                                   class="text-sm font-semibold hover:text-apple-blue-dark transition-colors block text-white">
                                                    {{ $document->name }}
                                                </a>
                                                
                                                <div class="flex flex-wrap items-center gap-2 text-[12px] text-dark-text-primary/70">
                                                    <span class="inline-flex px-2 py-0.5 rounded bg-[rgba(58,58,60,0.8)]">
                                                        {{ $document->type }}
                                                    </span>
                                                    
                                                    @if($document->number)
                                                        <span>
                                                            <i class="fas fa-hashtag mr-1"></i>
                                                            {{ $document->number }}
                                                        </span>
                                                    @endif
                                                    
                                                    @if($document->date)
                                                        <span>
                                                            <i class="fas fa-calendar mr-1"></i>
                                                            {{ $document->date->format('d M Y') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                
                                                @if($document->status)
                                                    @php
                                                        $docStatusStyle = match($document->status) {
                                                            'DISETUJUI' => 'background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);',
                                                            'MENUNGGU_PERSETUJUAN' => 'background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 1);',
                                                            'DITOLAK' => 'background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);',
                                                            default => 'background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 1);'
                                                        };
                                                    @endphp
                                                    <div>
                                                        <span class="inline-flex px-2 py-0.5 text-[12px] font-semibold rounded-full" style="{{ $docStatusStyle }}">
                                                            {{ str_replace('_', ' ', $document->status) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-file-alt text-4xl mb-3 text-dark-text-tertiary/50"></i>
                                <p class="text-dark-text-secondary">Belum ada dokumen untuk proyek ini</p>
                                <a href="{{ route('documents.create', ['project_id' => $project->id]) }}" 
                                   class="inline-block mt-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-[rgba(10,132,255,0.9)] text-white">
                                    <i class="fas fa-plus mr-2"></i>Upload Dokumen Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- End of Documents Tab Content -->

            <!-- Notes (commented out) -->
            @if(false && $project->notes)
            <div class="card-elevated rounded-apple-lg p-3.5">
                <h3 class="text-lg font-semibold mb-3 text-white">
                    <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Catatan
                </h3>
                <p class="whitespace-pre-line text-dark-text-primary/80">{{ $project->notes }}</p>
            </div>
            @endif
        </div>
        <!-- End of Main Content Wrapper (xl:col-span-2) -->

        <!-- Sidebar -->
        <div class="min-w-0 space-y-3">
            <!-- Combined Card: Recent Activity + Project Statistics -->
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <!-- Recent Activity Section -->
                <div class="p-6 border-b border-white/5">
                <h3 class="text-lg font-semibold mb-3 text-white">
                    <i class="fas fa-history mr-2 text-apple-blue-dark"></i>Aktivitas Terbaru
                </h3>
                @if($project->logs && $project->logs->count() > 0)
                <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                    @foreach($project->logs->take(10) as $log)
                    <div class="flex items-start space-x-2 p-3 rounded-lg transition-colors hover:bg-opacity-80 bg-[rgba(58,58,60,0.5)]">
                        <div class="flex-shrink-0 mt-1">
                            @if(str_contains(strtolower($log->description), 'selesai') || str_contains(strtolower($log->description), 'completed'))
                                <i class="fas fa-check-circle text-sm text-apple-green/90"></i>
                            @elseif(str_contains(strtolower($log->description), 'tambah') || str_contains(strtolower($log->description), 'created'))
                                <i class="fas fa-plus-circle text-sm text-apple-blue/90"></i>
                            @elseif(str_contains(strtolower($log->description), 'ubah') || str_contains(strtolower($log->description), 'updated'))
                                <i class="fas fa-edit text-sm text-[rgba(255,204,0,0.9)]"></i>
                            @elseif(str_contains(strtolower($log->description), 'hapus') || str_contains(strtolower($log->description), 'deleted'))
                                <i class="fas fa-trash text-sm text-apple-red/90"></i>
                            @else
                                <i class="fas fa-circle text-xs text-apple-blue/90"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm break-words text-dark-text-primary/90">{{ $log->description }}</p>
                            <div class="flex items-center mt-1 space-x-2">
                                <p class="text-xs text-dark-text-secondary">
                                    {{ $log->created_at->diffForHumans() }}
                                </p>
                                @if($log->user)
                                <span class="text-xs text-dark-text-tertiary">•</span>
                                <p class="text-xs text-dark-text-secondary">
                                    {{ $log->user->name }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl mb-3 text-dark-text-tertiary/50"></i>
                    <p class="text-sm text-dark-text-secondary">Belum ada aktivitas</p>
                </div>
                @endif
                </div>

                <!-- Project Statistics Section -->
                <div class="p-6">
                <h3 class="text-lg font-semibold mb-3 text-white">
                    <i class="fas fa-chart-pie mr-2 text-apple-blue-dark"></i>Statistik Proyek
                </h3>
                <div class="space-y-4">
                    <!-- Progress -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-dark-text-secondary">Progress Proyek</span>
                            <span class="font-semibold text-dark-text-primary/90">{{ $project->progress_percentage ?? 0 }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-[rgba(58,58,60,0.6)]">
                            <div class="h-full rounded-full transition-all" 
                                 style="width: {{ $project->progress_percentage ?? 0 }}%; background: rgba(0, 122, 255, 0.9);"></div>
                        </div>
                    </div>
                    
                    <!-- Tasks -->
                    <div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-text-secondary">Total Tugas</span>
                            <span class="font-semibold text-dark-text-primary/90">{{ $project->tasks->count() }}</span>
                        </div>
                        @if($project->tasks->count() > 0)
                        <div class="mt-1 flex justify-between text-xs">
                            <span class="text-[rgba(52,199,89,0.9)]">
                                <i class="fas fa-check-circle mr-1"></i>{{ $project->tasks->where('status', 'done')->count() }} selesai
                            </span>
                            <span class="text-[rgba(255,204,0,0.9)]">
                                <i class="fas fa-clock mr-1"></i>{{ $project->tasks->where('status', 'in_progress')->count() }} berjalan
                            </span>
                            <span class="text-[rgba(255,149,0,0.9)]">
                                <i class="fas fa-pause-circle mr-1"></i>{{ $project->tasks->whereIn('status', ['todo', 'blocked'])->count() }} pending
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Permits -->
                    <div class="flex justify-between items-center">
                        @php
                            $completedPermits = $project->permits->whereIn('status', [
                                \App\Models\ProjectPermit::STATUS_APPROVED,
                                \App\Models\ProjectPermit::STATUS_EXISTING,
                            ])->count();
                        @endphp
                        <span class="text-sm text-dark-text-secondary">Izin & Prasyarat</span>
                        <span class="font-semibold text-dark-text-primary/90">
                            {{ $completedPermits }}/{{ $project->permits->count() }}
                        </span>
                    </div>
                    
                    <!-- Documents -->
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-dark-text-secondary">Dokumen</span>
                        <span class="font-semibold text-dark-text-primary/90">{{ $project->documents->count() }}</span>
                    </div>
                    
                    <!-- Financial Summary -->
                    @if($project->contract_value > 0)
                    <div class="pt-3 border-t border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-dark-text-primary/80">Financial</span>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs">
                                <span class="text-dark-text-secondary">Nilai Kontrak</span>
                                <span class="text-dark-text-primary/90">Rp {{ number_format($project->contract_value, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-dark-text-secondary">Pembayaran Diterima</span>
                                <span class="text-apple-green/90">Rp {{ number_format($project->payment_received ?? 0, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-dark-text-secondary">Total Pengeluaran</span>
                                <span class="text-apple-red/90">Rp {{ number_format($project->expenses->sum('amount') ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Timeline -->
                    <div class="pt-3 border-t border-white/5">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-dark-text-secondary">Dibuat</span>
                            <span class="text-sm text-dark-text-secondary">{{ $project->created_at->format('d M Y') }}</span>
                        </div>
                        @if($project->deadline)
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-dark-text-secondary">Deadline</span>
                            @php
                                // FIXED: Check if project is completed first
                                if ($project->completed_at) {
                                    $completionStatus = $project->getCompletionStatus();
                                    $deadlineColor = match($completionStatus) {
                                        'early' => 'rgba(10, 132, 255, 0.9)',
                                        'on-time' => 'rgba(52, 199, 89, 0.9)',
                                        'late' => 'rgba(255, 149, 0, 0.9)',
                                        default => 'rgba(235, 235, 245, 0.9)'
                                    };
                                    $deadlineLabel = match($completionStatus) {
                                        'early' => '⚡ Lebih cepat',
                                        'on-time' => '⏰ Tepat waktu',
                                        'late' => '⚠️ Terlambat',
                                        default => 'Selesai'
                                    };
                                } else {
                                    // Project ongoing
                                    $isPastDeadline = $project->deadline->isPast();
                                    $deadlineColor = $isPastDeadline ? 'rgba(255, 59, 48, 0.9)' : 'rgba(235, 235, 245, 0.9)';
                                    $deadlineLabel = $isPastDeadline ? 'Terlambat' : $project->deadline->diffForHumans();
                                }
                            @endphp
                            <span class="text-sm font-medium" style="color: {{ $deadlineColor }};">
                                {{ $project->deadline->format('d M Y') }}
                                <span class="text-xs" style="color: {{ $deadlineColor }};">({{ $deadlineLabel }})</span>
                            </span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-dark-text-secondary">Terakhir Update</span>
                            <span class="text-sm text-dark-text-secondary">{{ $project->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div x-show="paymentModalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="paymentModalOpen = false">
    <div class="card-elevated rounded-apple-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-money-bill-wave mr-2 text-apple-blue-dark"></i>Tambah Pembayaran
            </h3>
            <button @click="paymentModalOpen = false" type="button"
                    class="text-2xl hover:opacity-75 transition-opacity text-dark-text-secondary">×</button>
        </div>

        <form action="{{ route('projects.payments.store', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        <i class="fas fa-file-invoice mr-1 text-apple-blue"></i>
                        Invoice (Opsional)
                    </label>
                    <select name="invoice_id" id="payment_invoice_id" class="input-dark w-full px-4 py-2 rounded-lg"
                            x-ref="invoiceSelect"
                            @change="$refs.amountInput.value = $refs.invoiceSelect.options[$refs.invoiceSelect.selectedIndex]?.dataset?.remaining || ''">
                        <option value="">Tidak terkait invoice (pembayaran umum)</option>
                        @foreach($project->invoices()->whereIn('status', ['sent', 'partial', 'overdue'])->get() as $inv)
                        <option value="{{ $inv->id }}" data-remaining="{{ $inv->remaining_amount }}">
                            {{ $inv->invoice_number }} - Sisa: Rp {{ number_format($inv->remaining_amount, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs mt-1 text-dark-text-tertiary">
                        Pilih invoice jika pembayaran ini untuk melunasi invoice tertentu. Biarkan kosong jika pembayaran umum.
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Tanggal Pembayaran *</label>
                    <input type="date" name="payment_date" required
                           class="input-dark w-full px-4 py-2 rounded-lg" value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Jumlah (Rp) *</label>
                    <input type="number" name="amount" id="payment_amount" x-ref="amountInput" required min="0" step="0.01"
                           class="input-dark w-full px-4 py-2 rounded-lg" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Tipe Pembayaran *</label>
                    <select name="payment_type" required class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih tipe...</option>
                        <option value="dp">Down Payment (DP)</option>
                        <option value="progress">Progress/Termin</option>
                        <option value="final">Pelunasan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Metode Pembayaran *</label>
                    <select name="payment_method" required class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih metode...</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="giro">Giro</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Akun Bank/Kas</label>
                    <select name="bank_account_id" class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih akun...</option>
                        @foreach(\App\Models\CashAccount::active()->get() as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->formatted_balance }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">No. Referensi</label>
                    <input type="text" name="reference_number" maxlength="100"
                           class="input-dark w-full px-4 py-2 rounded-lg" placeholder="No. transfer/cek/giro">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Deskripsi/Catatan</label>
                    <textarea name="description" rows="2"
                              class="input-dark w-full px-4 py-2 rounded-lg" placeholder="Catatan pembayaran..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Upload Bukti (PDF/Gambar, max 5MB)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="input-dark w-full px-4 py-2 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" @click="paymentModalOpen = false"
                        class="px-6 py-2 rounded-lg font-medium transition-colors hover:opacity-80 bg-[rgba(58,58,60,0.8)] text-dark-text-primary/80">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 rounded-lg font-medium transition-colors hover:opacity-90 bg-apple-green/90 text-white">
                    <i class="fas fa-save mr-2"></i>Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Expense Modal -->
<div x-show="expenseModalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="expenseModalOpen = false">
    <div class="card-elevated rounded-apple-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-shopping-cart mr-2 text-apple-blue-dark"></i>Tambah Pengeluaran
            </h3>
            <button @click="expenseModalOpen = false" type="button"
                    class="text-2xl hover:opacity-75 transition-opacity text-dark-text-secondary">×</button>
        </div>

        <form id="expenseForm" action="{{ route('projects.financial-expenses.store', $project) }}" method="POST" enctype="multipart/form-data" x-data @submit.prevent="handleExpenseSubmit($event)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Tanggal Pengeluaran *</label>
                    <input type="date" name="expense_date" required
                           class="input-dark w-full px-4 py-2 rounded-lg" value="{{ date('Y-m-d') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Jumlah (Rp) *</label>
                    <input type="number" name="amount" required min="0" step="0.01"
                           class="input-dark w-full px-4 py-2 rounded-lg" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Kategori *</label>
                    <select name="category" required class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih kategori...</option>
                        <optgroup label="SDM & Personel">
                            <option value="personnel">Gaji & Honor</option>
                            <option value="commission">Komisi</option>
                            <option value="allowance">Tunjangan & Bonus</option>
                        </optgroup>
                        <optgroup label="Rekanan & Subkontraktor">
                            <option value="subcontractor">Subkontraktor</option>
                            <option value="consultant">Konsultan Eksternal</option>
                            <option value="supplier">Rekanan/Partner</option>
                        </optgroup>
                        <optgroup label="Layanan Teknis">
                            <option value="laboratory">Laboratorium</option>
                            <option value="survey">Survey & Pengukuran</option>
                            <option value="testing">Testing & Inspeksi</option>
                            <option value="certification">Sertifikasi</option>
                        </optgroup>
                        <optgroup label="Peralatan & Perlengkapan">
                            <option value="equipment_rental">Sewa Alat</option>
                            <option value="equipment_purchase">Pembelian Alat</option>
                            <option value="materials">Perlengkapan & Supplies</option>
                            <option value="maintenance">Maintenance & Perbaikan</option>
                        </optgroup>
                        <optgroup label="Operasional">
                            <option value="travel">Perjalanan Dinas</option>
                            <option value="accommodation">Akomodasi</option>
                            <option value="transportation">Transportasi</option>
                            <option value="communication">Komunikasi & Internet</option>
                            <option value="office_supplies">ATK & Supplies</option>
                            <option value="printing">Printing & Dokumen</option>
                        </optgroup>
                        <optgroup label="Legal & Administrasi">
                            <option value="permit">Perizinan</option>
                            <option value="insurance">Asuransi</option>
                            <option value="tax">Pajak & Retribusi</option>
                            <option value="legal">Legal & Notaris</option>
                            <option value="administration">Administrasi</option>
                        </optgroup>
                        <optgroup label="Marketing & Lainnya">
                            <option value="marketing">Marketing & Promosi</option>
                            <option value="entertainment">Entertainment & Jamuan</option>
                            <option value="donation">Donasi & CSR</option>
                            <option value="other">Lainnya</option>
                        </optgroup>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Nama Rekanan/Penerima</label>
                    <input type="text" name="vendor_name" maxlength="255"
                           class="input-dark w-full px-4 py-2 rounded-lg" placeholder="Nama rekanan atau penerima pembayaran">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Metode Pembayaran *</label>
                    <select name="payment_method" required class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih metode...</option>
                        <option value="bank_transfer">Transfer Bank</option>
                        <option value="cash">Tunai</option>
                        <option value="check">Cek</option>
                        <option value="giro">Giro</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Akun Bank/Kas</label>
                    <select name="bank_account_id" class="input-dark w-full px-4 py-2 rounded-lg">
                        <option value="">Pilih akun...</option>
                        @foreach(\App\Models\CashAccount::active()->get() as $account)
                        <option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->formatted_balance }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Deskripsi/Catatan</label>
                    <textarea name="description" rows="2"
                              class="input-dark w-full px-4 py-2 rounded-lg" placeholder="Detail pengeluaran..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_receivable" value="1" class="rounded bg-[rgba(58,58,60,0.8)]" x-model="receivableEnabled">
                        <span class="text-sm text-dark-text-primary/80">Kasbon/Piutang Internal (perlu dikembalikan oleh karyawan/pihak internal)</span>
                    </label>
                    <p class="text-xs mt-1 text-dark-text-tertiary">
                        Centang jika ini adalah kasbon atau uang yang dipinjamkan ke karyawan/tim internal yang harus dikembalikan
                    </p>
                </div>

                <div x-show="receivableEnabled" x-cloak class="md:col-span-2">
                    <div class="p-4 rounded-lg bg-[rgba(255,204,0,0.1)] border border-[rgba(255,204,0,0.3)]">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Nama Penerima Kasbon *</label>
                                <input type="text" name="receivable_from" maxlength="255"
                                       class="input-dark w-full px-4 py-2 rounded-lg" placeholder="Nama karyawan/pihak internal">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Status</label>
                                <select name="receivable_status" class="input-dark w-full px-4 py-2 rounded-lg">
                                    <option value="pending">Belum Bayar</option>
                                    <option value="partial">Sebagian</option>
                                    <option value="paid">Lunas</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Catatan Kasbon</label>
                                <textarea name="receivable_notes" rows="2"
                                          class="input-dark w-full px-4 py-2 rounded-lg" placeholder="Catatan tambahan untuk kasbon..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Upload Bukti (PDF/Gambar, max 5MB)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="input-dark w-full px-4 py-2 rounded-lg">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" @click="expenseModalOpen = false"
                        class="px-6 py-2 rounded-lg font-medium transition-colors hover:opacity-80 bg-[rgba(58,58,60,0.8)] text-dark-text-primary/80">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 rounded-lg font-medium transition-colors hover:opacity-90 bg-apple-orange/90 text-white">
                    <i class="fas fa-save mr-2"></i>Simpan Pengeluaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ===== GLOBAL PERMITS TAB FUNCTIONS =====
// These functions need to be globally accessible for permits tab
function showTemplateModal() {
    const modal = document.getElementById('template-modal');
    if (!modal) {
        console.error('Template modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeTemplateModal() {
    const modal = document.getElementById('template-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function showAddPermitModal() {
    const modal = document.getElementById('add-permit-modal');
    if (!modal) {
        console.error('Add Permit modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
}

function closeAddPermitModal() {
    const modal = document.getElementById('add-permit-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

function selectTemplate(templateId) {
    document.querySelectorAll('[id^="preview-"]').forEach(el => {
        el.classList.add('hidden');
    });
    
    const preview = document.getElementById('preview-' + templateId);
    if (preview) {
        preview.classList.remove('hidden');
    }
}

function showUploadModal(permitId) {
    const permitCard = document.querySelector(`[data-permit-id="${permitId}"]`);
    if (!permitCard) {
        alert('Permit data not found');
        return;
    }
    
    window.currentUploadPermitId = permitId;
    
    document.getElementById('upload-permit-sequence').textContent = permitCard.dataset.sequence;
    document.getElementById('upload-permit-name').textContent = permitCard.dataset.permitName;
    
    const modal = document.getElementById('upload-document-modal');
    if (!modal) {
        console.error('Upload modal not found!');
        return;
    }
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}

function closeUploadModal() {
    const modal = document.getElementById('upload-document-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.style.display = 'none';
    document.getElementById('upload-document-form').reset();
    window.currentUploadPermitId = null;
}

function showManageDependenciesModal(permitId) {
    // This function will be called from permits tab
    if (typeof window.showManageDependenciesModalImpl === 'function') {
        window.showManageDependenciesModalImpl(permitId);
    }
}

function updatePermitStatus(permitId) {
    // This function will be called from permits tab
    if (typeof window.updatePermitStatusImpl === 'function') {
        window.updatePermitStatusImpl(permitId);
    }
}

function deletePermit(permitId) {
    if (!confirm('Yakin ingin menghapus izin ini?\n\nPerhatian: Jika izin ini menjadi prasyarat izin lain, penghapusan akan gagal.')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/permits/${permitId}`;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    form.innerHTML = `
        <input type="hidden" name="_token" value="${csrfToken}">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="redirect_to_tab" value="permits">
    `;
    
    document.body.appendChild(form);
    form.submit();
}
</script>

<style>
.tab-button {
    color: rgba(235, 235, 245, 0.65);
    background: transparent;
    border: 1px solid transparent;
}
.tab-button:hover {
    background: rgba(255, 255, 255, 0.05) !important;
    color: rgba(235, 235, 245, 0.9) !important;
    border-color: rgba(255, 255, 255, 0.08);
}
.tab-button.active {
    background: rgba(0, 122, 255, 0.18) !important;
    border-color: rgba(0, 122, 255, 0.4);
    color: #FFFFFF !important;
    box-shadow: inset 0 0 0 1px rgba(0, 122, 255, 0.25);
}

/* Protective scoping for tab content partials */
[data-scope="financial-tab"],
[data-scope="permits-tab"],
[data-scope="tasks-tab"],
[data-scope="documents-tab"] {
    min-width: 0;
    overflow: hidden; /* Prevent content from breaking out */
}

/* Ensure images and tables don't overflow */
[data-scope] img,
[data-scope] table {
    max-width: 100%;
}

/* Add horizontal scroll for wide content */
[data-scope] .overflow-x-auto {
    overflow-x: auto;
}

/* Custom scrollbar for activity feed */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(58, 58, 60, 0.3);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(0, 122, 255, 0.5);
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 122, 255, 0.7);
}
</style>

{{-- Include Financial Modals (Sprint 6) --}}
@include('projects.partials.financial-modals')

@endsection
