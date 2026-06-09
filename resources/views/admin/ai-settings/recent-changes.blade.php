@extends('layouts.admin')

@section('title', 'AI Settings - Recent Changes')

@section('content')
<div class="space-y-4">
    {{-- Compact Header Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Audit Trail</p>
                <h1 class="admin-hero-title text-white">Recent Changes</h1>
                <p class="admin-hero-desc">History of all AI settings modifications</p>
            </div>
            <a href="{{ route('admin.ai-settings.index') }}" 
               class="admin-btn admin-btn-sm rounded" style="background: rgba(142,142,147,0.25); color: #fff;">
                <i class="fas fa-arrow-left mr-1.5"></i>Back to Settings
            </a>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="card-elevated rounded-apple-lg p-4">
        {{-- Time Filter Tabs --}}
        <nav class="flex flex-wrap gap-1 mb-4" role="tablist">
            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 1]) }}" 
               class="tab-button {{ $days == 1 ? 'active' : '' }}">
                <i class="fas fa-clock"></i>
                <span>24 Jam</span>
            </a>
            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 7]) }}" 
               class="tab-button {{ $days == 7 ? 'active' : '' }}">
                <i class="fas fa-calendar-day"></i>
                <span>7 Hari</span>
            </a>
            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 30]) }}" 
               class="tab-button {{ $days == 30 ? 'active' : '' }}">
                <i class="fas fa-calendar-week"></i>
                <span>30 Hari</span>
            </a>
            <a href="{{ route('admin.ai-settings.recent-changes', ['days' => 90]) }}" 
               class="tab-button {{ $days == 90 ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                <span>90 Hari</span>
            </a>
        </nav>

        @if($changes->isEmpty())
        {{-- Empty State --}}
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
                <i class="fas fa-history text-2xl" style="color: var(--apple-blue);"></i>
            </div>
            <h3 class="admin-section text-white mb-2">Belum Ada Perubahan</h3>
            <p class="admin-body text-dark-text-secondary">
                Tidak ada perubahan settings dalam {{ $days }} hari terakhir.
            </p>
        </div>
        @else
        {{-- Changes Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Setting</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Perubahan</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Diubah Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @foreach($changes as $change)
                    <tr class="hover:bg-dark-bg-tertiary transition-apple">
                        <td class="px-4 py-3">
                            <div class="space-y-0.5">
                                <p class="text-sm font-medium text-white">{{ $change->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-dark-text-secondary">{{ $change->created_at->format('H:i:s') }}</p>
                                <p class="text-xs text-dark-text-tertiary">{{ $change->created_at->diffForHumans() }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-0.5">
                                <p class="text-sm font-medium text-white font-mono">{{ $change->setting->key ?? $change->key }}</p>
                                <span class="px-2 py-0.5 text-xs rounded-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                                    {{ $change->setting->category ?? 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="space-y-1">
                                @if($change->old_value)
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 text-xs rounded" style="background: rgba(255,59,48,0.15); color: var(--apple-red);">OLD</span>
                                    <span class="text-xs text-dark-text-secondary font-mono">{{ \Str::limit($change->old_value, 40) }}</span>
                                </div>
                                @endif
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 text-xs rounded" style="background: rgba(52,199,89,0.15); color: var(--apple-green);">NEW</span>
                                    <span class="text-xs text-white font-mono">{{ \Str::limit($change->new_value, 40) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium text-white" style="background: var(--apple-blue);">
                                    {{ strtoupper(substr($change->changed_by_name ?? 'S', 0, 2)) }}
                                </div>
                                <span class="text-sm text-white">{{ $change->changed_by_name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-dark-text-secondary">{{ $change->reason ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($changes->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $changes->links() }}
        </div>
        @endif
        @endif
    </section>
</div>
@endsection
