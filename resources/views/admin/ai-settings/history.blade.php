@extends('layouts.admin')

@section('title', 'AI Settings - History: ' . $setting->key)

@section('content')
<div class="space-y-4">
    {{-- Compact Header Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Setting History</p>
                <h1 class="admin-hero-title text-white font-mono">{{ $setting->key }}</h1>
                <p class="admin-hero-desc">{{ $setting->description }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 text-sm rounded-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                    {{ ucfirst($setting->category) }}
                </span>
                <a href="{{ route('admin.ai-settings.index', ['category' => $setting->category]) }}" 
                   class="admin-btn admin-btn-sm rounded" style="background: rgba(142,142,147,0.25); color: #fff;">
                    <i class="fas fa-arrow-left mr-1.5"></i>Back
                </a>
            </div>
        </div>
    </section>

    {{-- Current Value Card --}}
    <section class="card-elevated rounded-apple-lg p-4">
        <h3 class="admin-section text-white mb-3">Nilai Saat Ini</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-3 rounded-apple" style="background: rgba(52,199,89,0.1);">
                <p class="text-xs text-dark-text-secondary mb-1">Current Value</p>
                <p class="text-lg font-mono text-white">{{ $setting->value }}</p>
            </div>
            <div class="p-3 rounded-apple" style="background: rgba(142,142,147,0.1);">
                <p class="text-xs text-dark-text-secondary mb-1">Default Value</p>
                <p class="text-lg font-mono text-dark-text-secondary">{{ $setting->default_value }}</p>
            </div>
            <div class="p-3 rounded-apple" style="background: rgba(10,132,255,0.1);">
                <p class="text-xs text-dark-text-secondary mb-1">Data Type</p>
                <p class="text-lg font-mono text-white">{{ $setting->data_type }}</p>
            </div>
        </div>
    </section>

    {{-- History Table --}}
    <section class="card-elevated rounded-apple-lg p-4">
        <h3 class="admin-section text-white mb-3">Riwayat Perubahan</h3>
        
        @if($history->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
                <i class="fas fa-history text-2xl" style="color: var(--apple-blue);"></i>
            </div>
            <h3 class="admin-section text-white mb-2">Belum Ada Riwayat</h3>
            <p class="admin-body text-dark-text-secondary">
                Setting ini belum pernah diubah sejak dibuat.
            </p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Nilai Lama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Nilai Baru</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Diubah Oleh</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @foreach($history as $record)
                    <tr class="hover:bg-dark-bg-tertiary transition-apple">
                        <td class="px-4 py-3">
                            <p class="text-sm font-medium text-white">{{ $record->created_at->format('d M Y, H:i') }}</p>
                            <p class="text-xs text-dark-text-tertiary">{{ $record->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-mono px-2 py-1 rounded" style="background: rgba(255,59,48,0.15); color: var(--apple-red);">
                                {{ $record->old_value ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-mono px-2 py-1 rounded" style="background: rgba(52,199,89,0.15); color: var(--apple-green);">
                                {{ $record->new_value }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium text-white" style="background: var(--apple-blue);">
                                    {{ strtoupper(substr($record->user->name ?? 'S', 0, 2)) }}
                                </div>
                                <span class="text-sm text-white">{{ $record->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-dark-text-secondary">{{ $record->reason ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $history->links() }}
        </div>
        @endif
        @endif
    </section>
</div>
@endsection
