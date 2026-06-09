@extends('layouts.admin')

@section('title', 'Email Campaigns')
@section('page-title', 'Email Campaigns')

@section('content')
@php
    $statusMeta = [
        'draft' => ['label' => 'Draf', 'bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,0.95)'],
        'scheduled' => ['label' => 'Terjadwal', 'bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,0.95)'],
        'sending' => ['label' => 'Mengirim', 'bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,0.95)'],
        'sent' => ['label' => 'Terkirim', 'bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,0.95)'],
    ];
@endphp

{{-- Hero --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
        <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    </div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-3 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Pemasaran</p>
            <h1 class="text-2xl md:text-xl font-bold text-white">Kampanye Email</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola kampanye draf, jadwal pengiriman, dan performa email secara terpusat.
            </p>
            <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-paper-plane mr-2"></i>{{ $stats['total'] }} total kampanye</span>
                <span><i class="fas fa-clock mr-2"></i>{{ $stats['scheduled'] }} terjadwal</span>
                <span><i class="fas fa-check-circle mr-2"></i>{{ $stats['sent'] }} terkirim</span>
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.campaigns.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Buat Kampanye
            </a>
        </div>
    </div>
</section>

{{-- Flash messages --}}
@if(session('success'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle"></i>
        <span class="text-sm">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(255,59,48,0.12); border: 1px solid rgba(255,59,48,0.3); color: rgba(255,59,48,1);">
        <i class="fas fa-exclamation-circle"></i>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
@endif

{{-- Stats --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Kampanye</p>
        <p class="text-xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $stats['draft'] }} masih draf</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Draf</p>
        <p class="text-xl font-bold text-white">{{ $stats['draft'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu finalisasi</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Terjadwal</p>
        <p class="text-xl font-bold text-white">{{ $stats['scheduled'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Siap dikirim</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Terkirim</p>
        <p class="text-xl font-bold text-white">{{ $stats['sent'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Kampanye selesai</p>
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-sm font-semibold text-white">Temukan Kampanye</h2>
        </div>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $campaigns->total() }} kampanye ditemukan</p>
    </div>
    <form method="GET" action="{{ route('admin.campaigns.index') }}">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau subjek"
                           class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                </div>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draf</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>Mengirim</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary-sm">
                    <i class="fas fa-search mr-2"></i>Terapkan
                </button>
                <a href="{{ route('admin.campaigns.index') }}" class="btn-secondary-sm text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>
</section>

    {{-- Campaign table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($campaigns->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kampanye</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Penerima</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Jadwal</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Template</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $campaign)
                            @php $meta = $statusMeta[$campaign->status] ?? $statusMeta['draft']; @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $campaign->name }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $campaign->subject }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.5);">Dibuat {{ $campaign->created_at->locale('id')->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $meta['bg'] }}; color: {{ $meta['text'] }};">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ number_format($campaign->total_recipients) }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ ucfirst($campaign->recipient_type) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $campaign->scheduled_at ? \Carbon\Carbon::parse($campaign->scheduled_at)->format('d M Y H:i') : '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $campaign->template->name ?? 'Konten kustom' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        @if(!in_array($campaign->status, ['sending', 'sent']))
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn-primary-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        @endif
                                        <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" x-data @submit.prevent="if(confirm('Hapus campaign ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9); border: 1px solid rgba(255,59,48,0.3);">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($campaigns->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $campaigns->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 space-y-3">
                <i class="fas fa-envelope-open-text text-4xl" style="color: rgba(235,235,245,0.3);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada kampanye. Buat kampanye pertama Anda!</p>
                <a href="{{ route('admin.campaigns.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Buat Kampanye
                </a>
            </div>
        @endif
    </section>
@endsection
