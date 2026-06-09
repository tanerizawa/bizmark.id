@extends('layouts.admin')

@section('title', 'Email Subscribers')
@section('page-title', 'Email Subscribers')

@section('content')
@php
    $statusMeta = [
        'active' => ['label' => 'Aktif', 'bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)'],
        'unsubscribed' => ['label' => 'Berhenti', 'bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)'],
        'bounced' => ['label' => 'Gagal', 'bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
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
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Audiens</p>
            <h1 class="text-2xl md:text-xl font-bold text-white">Pelanggan Newsletter</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola daftar pelanggan, status keaktifan, dan kategori audiens secara terpusat.
            </p>
            <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-users mr-2"></i>{{ number_format($stats['total']) }} total pelanggan</span>
                <span><i class="fas fa-check-circle mr-2"></i>{{ number_format($stats['active']) }} aktif</span>
                <span><i class="fas fa-exclamation-triangle mr-2"></i>{{ number_format($stats['bounced']) }} gagal</span>
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.subscribers.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tambah Pelanggan
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
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Pelanggan</p>
        <p class="text-xl font-bold text-white">{{ number_format($stats['total']) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ number_format($stats['active']) }} aktif</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Aktif</p>
        <p class="text-xl font-bold text-white">{{ number_format($stats['active']) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Dapat menerima email</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Berhenti</p>
        <p class="text-xl font-bold text-white">{{ number_format($stats['unsubscribed']) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu tindak lanjut</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Gagal</p>
        <p class="text-xl font-bold text-white">{{ number_format($stats['bounced']) }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu verifikasi email</p>
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-sm font-semibold text-white">Cari Pelanggan</h2>
        </div>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $subscribers->total() }} pelanggan ditemukan</p>
    </div>
    <form method="GET" action="{{ route('admin.subscribers.index') }}">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Email atau nama"
                           class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                </div>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="unsubscribed" {{ request('status') === 'unsubscribed' ? 'selected' : '' }}>Berhenti</option>
                    <option value="bounced" {{ request('status') === 'bounced' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary-sm">
                    <i class="fas fa-search mr-2"></i>Terapkan
                </button>
                <a href="{{ route('admin.subscribers.index') }}" class="btn-secondary-sm text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>
</section>

    {{-- Table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($subscribers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Label</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Bergabung</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Sumber</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscribers as $subscriber)
                            @php $meta = $statusMeta[$subscriber->status] ?? $statusMeta['active']; @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $subscriber->name ?? $subscriber->email }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $subscriber->email }}</p>
                                    @if($subscriber->phone)
                                        <p class="text-xs" style="color: rgba(235,235,245,0.5);"><i class="fas fa-phone mr-1"></i>{{ $subscriber->phone }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $meta['bg'] }}; color: {{ $meta['text'] }};">
                                        {{ ucfirst($subscriber->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if(is_array($subscriber->tags) && count($subscriber->tags))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($subscriber->tags as $tag)
                                                <span class="px-2 py-0.5 text-[10px] rounded-full" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.75);">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs" style="color: rgba(235,235,245,0.4);">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.8);">
                                        {{ $subscriber->subscribed_at ? $subscriber->subscribed_at->format('d M Y') : '-' }}
                                    </p>
                                    @if($subscriber->subscribed_at)<p class="text-xs" style="color: rgba(235,235,245,0.5);">{{ $subscriber->subscribed_at->locale('id')->diffForHumans() }}</p>@endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.8);">{{ ucfirst($subscriber->source ?? 'tidak diketahui') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.subscribers.show', $subscriber) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>Lihat
                                        </a>
                                        <a href="{{ route('admin.subscribers.edit', $subscriber) }}" class="btn-primary-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.subscribers.destroy', $subscriber) }}" method="POST" x-data @submit.prevent="if(confirm('Hapus subscriber ini?')) $el.submit()">
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
            @if($subscribers->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $subscribers->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16 space-y-3">
                <i class="fas fa-users text-4xl" style="color: rgba(235,235,245,0.35);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada pelanggan. Mulai bangun audiens Anda.</p>
                <a href="{{ route('admin.subscribers.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Pelanggan
                </a>
            </div>
        @endif
    </section>
@endsection
