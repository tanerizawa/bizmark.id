@extends('layouts.admin')

@section('title', 'Email Inbox')
@section('page-title', 'Email Inbox')

@section('content')
@php
    $categoryTabs = [
        'inbox' => ['label' => 'Masuk', 'icon' => 'inbox', 'count' => $stats['unread'] ?? 0, 'badge' => true],
        'sent' => ['label' => 'Terkirim', 'icon' => 'paper-plane'],
        'trash' => ['label' => 'Sampah', 'icon' => 'trash', 'count' => $stats['trash'] ?? 0],
        'spam' => ['label' => 'Spam', 'icon' => 'ban', 'count' => $stats['spam'] ?? 0],
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
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Komunikasi</p>
            <h1 class="text-2xl md:text-xl font-bold text-white">Kotak Masuk Email</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola pesan masuk, balasan, dan prioritas komunikasi tim secara terpusat.
            </p>
            <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-envelope-open-text mr-2"></i>{{ $stats['total'] ?? 0 }} total email</span>
                <span><i class="fas fa-circle mr-2"></i>{{ $stats['unread'] ?? 0 }} belum dibaca</span>
                <span><i class="fas fa-star mr-2"></i>{{ $stats['starred'] ?? 0 }} tersimpan</span>
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.inbox.compose') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Tulis Email
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
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Email</p>
        <p class="text-xl font-bold text-white">{{ $stats['total'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $stats['inbox'] ?? 0 }} di kotak masuk</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,59,48,0.9);">Belum Dibaca</p>
        <p class="text-xl font-bold text-white">{{ $stats['unread'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu tindak lanjut</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Berbintang</p>
        <p class="text-xl font-bold text-white">{{ $stats['starred'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pesan prioritas</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Terkirim</p>
        <p class="text-xl font-bold text-white">{{ $stats['sent'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Email keluar</p>
    </div>
</section>

{{-- Category tabs --}}
<section class="card-elevated rounded-apple-xl p-4 mb-5">
    <div class="flex flex-wrap gap-2">
        @foreach($categoryTabs as $key => $tab)
            @php $isActive = $category === $key; @endphp
            <a href="{{ route('admin.inbox.index', array_merge(['category' => $key], request()->except('category'))) }}"
               class="pill {{ $isActive ? 'pill-active' : '' }}">
                <i class="fas fa-{{ $tab['icon'] }} mr-2"></i>{{ $tab['label'] }}
                @if(!empty($tab['badge']) && ($tab['count'] ?? 0) > 0)
                    <span>{{ $tab['count'] }}</span>
                @elseif(in_array($key, ['trash', 'spam']) && ($tab['count'] ?? 0) > 0)
                    <span class="opacity-60">{{ $tab['count'] }}</span>
                @endif
            </a>
        @endforeach
        <a href="{{ route('admin.inbox.index', array_merge(['is_starred' => 1], request()->except(['is_starred', 'category']))) }}"
           class="pill {{ request('is_starred') ? 'pill-active' : '' }}">
            <i class="fas fa-star mr-2"></i>Berbintang
        </a>
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-sm font-semibold text-white">Cari Email</h2>
        </div>
        <div class="flex items-center gap-3">
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $emails->total() }} email ditemukan</p>
            @if($category === 'trash' && $emails->total() > 0)
                <form method="POST" action="{{ route('admin.inbox.empty-trash') }}" x-data @submit.prevent="if(confirm('Yakin ingin menghapus semua email di sampah secara permanen?')) $el.submit()">
                    @csrf
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-apple font-semibold" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1); border: 1px solid rgba(255,59,48,0.3);">
                        <i class="fas fa-trash mr-1"></i>Kosongkan Sampah
                    </button>
                </form>
            @endif
        </div>
    </div>
    <form method="GET" action="{{ route('admin.inbox.index', ['category' => $category]) }}">
        <div class="flex flex-col gap-3 md:flex-row md:items-end">
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Subjek, pengirim, atau isi"
                           class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                </div>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                <select name="is_read" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Status</option>
                    <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Belum Dibaca</option>
                    <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Akun Email</label>
                <select name="to_email" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                        style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                    <option value="">Semua Akun</option>
                    @foreach(\App\Models\EmailAccount::all() as $account)
                        <option value="{{ $account->email }}" {{ request('to_email') === $account->email ? 'selected' : '' }}>
                            {{ $account->email }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary-sm"><i class="fas fa-search mr-2"></i>Terapkan</button>
                <a href="{{ route('admin.inbox.index', ['category' => $category]) }}" class="btn-secondary-sm text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>
</section>

    {{-- Email list --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-3 border-b border-white/5 flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.6); background: rgba(28,28,30,0.4);">
            <span>{{ $emails->total() }} email ditemukan</span>
            <span>Menampilkan {{ $emails->firstItem() ?? 0 }} - {{ $emails->lastItem() ?? 0 }}</span>
        </div>
        <div class="divide-y divide-white/5">
            @forelse($emails as $email)
                @php
                    $isSentCategory = $category === 'sent';
                    $senderEmail = $isSentCategory ? $email->to_email : $email->from_email;
                    $senderName = $isSentCategory ? null : trim((string) $email->from_name);

                    if (!$isSentCategory && (!$senderName || strcasecmp($senderName, (string) $senderEmail) === 0 || str_contains($senderName, '@'))) {
                        $senderName = $senderEmail
                            ? (string) Str::of(strstr($senderEmail, '@', true) ?: $senderEmail)->replace(['.', '_', '-'], ' ')->title()
                            : null;
                    }

                    $senderPrimary = $isSentCategory
                        ? ($email->to_email ?: 'Penerima tidak diketahui')
                        : ($senderName ?: ($senderEmail ?: 'Pengirim tidak diketahui'));

                    $senderSecondary = !$isSentCategory && $senderEmail && strcasecmp($senderPrimary, $senderEmail) !== 0
                        ? $senderEmail
                        : null;

                    $senderInitials = collect(preg_split('/\s+/', trim($senderPrimary)) ?: [])
                        ->filter()
                        ->take(2)
                        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
                        ->implode('');

                    $senderInitials = $senderInitials !== ''
                        ? $senderInitials
                        : Str::upper(Str::substr($senderPrimary, 0, 2));

                    $previewText = $email->preview ?: 'Tidak ada ringkasan isi email.';
                @endphp
                <div x-data="{ starred: {{ $email->is_starred ? 'true' : 'false' }} }"
                     @click="window.location='{{ route('admin.inbox.show', $email) }}'"
                     class="email-item px-6 py-4 hover:bg-white/5 transition-colors {{ !$email->is_read ? 'bg-white/5' : '' }}">
                    <div class="flex items-start gap-4">
                        <button type="button"
                                @click.stop="fetch('/admin/inbox/{{ $email->id }}/star', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' } }).then(r => r.json()).then(d => { if (d.success) starred = !starred; }).catch(console.error)"
                                :class="starred ? 'active' : ''"
                                class="mt-1 star-button">
                            <i class="fas fa-star"></i>
                        </button>
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                {{ $senderInitials }}
                            </div>
                        </div>
                        <div class="flex-grow min-w-0 cursor-pointer">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <p class="text-sm font-{{ !$email->is_read ? 'semibold' : 'medium' }} text-white truncate">
                                            {{ $senderPrimary }}
                                        </p>
                                        @if(!$email->is_read && $category !== 'sent')
                                            <span class="inline-flex items-center px-2 py-0.5 text-[10px] font-semibold rounded-full flex-shrink-0" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                                                NEW
                                            </span>
                                        @endif
                                        @if($email->has_attachments)
                                            <i class="fas fa-paperclip text-xs flex-shrink-0" style="color: rgba(235,235,245,0.5);"></i>
                                        @endif
                                    </div>
                                    @if($senderSecondary)
                                        <p class="text-xs truncate mt-0.5" style="color: rgba(235,235,245,0.5);">
                                            {{ $senderSecondary }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs whitespace-nowrap" style="color: rgba(235,235,245,0.6);">
                                        @if($email->received_at->isToday())
                                            {{ $email->received_at->format('H:i') }}
                                        @elseif($email->received_at->isYesterday())
                                            Kemarin
                                        @elseif($email->received_at->diffInDays() < 7)
                                            {{ $email->received_at->locale('id')->isoFormat('dddd') }}
                                        @else
                                            {{ $email->received_at->format('d M Y') }}
                                        @endif
                                    </span>
                                    @if($category === 'trash')
                                        <form action="{{ route('admin.inbox.delete', $email->id) }}"
                                              method="POST"
                                              @click.stop
                                              @submit.prevent="if(confirm('Yakin ingin menghapus email ini secara PERMANEN?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <h3 class="text-sm font-{{ !$email->is_read ? 'semibold' : 'normal' }} text-white mb-1 truncate">
                                {{ $email->subject ?: '(No subject)' }}
                            </h3>
                            <p class="text-xs line-clamp-2 leading-5 mb-2" style="color: rgba(235,235,245,0.58);">
                                {{ $previewText }}
                            </p>
                            @if($email->emailAccount)
                                <span class="inline-flex items-center px-2 py-1 text-xs rounded-apple" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.7);">
                                    <i class="fas fa-at mr-1"></i>{{ $email->emailAccount->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 space-y-2">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-inbox text-2xl" style="color: rgba(235,235,245,0.35);"></i>
                    </div>
                    <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                        @if(request('search'))
                            Tidak ada email yang sesuai dengan pencarian.
                        @else
                            Kotak masuk sedang kosong.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
        @if($emails->hasPages())
            <div class="px-6 py-4 border-t border-white/5 flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.6);">
                <div>Menampilkan {{ $emails->firstItem() ?? 0 }} - {{ $emails->lastItem() ?? 0 }} dari {{ $emails->total() }} email</div>
                <div class="space-x-2">
                    @if($emails->onFirstPage())
                        <span class="px-4 py-2 rounded-apple" style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.35);">
                            <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                        </span>
                    @else
                        <a href="{{ $emails->previousPageUrl() }}" class="px-4 py-2 rounded-apple" style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.75);">
                            <i class="fas fa-chevron-left mr-2"></i>Sebelumnya
                        </a>
                    @endif
                    @if($emails->hasMorePages())
                        <a href="{{ $emails->nextPageUrl() }}" class="px-4 py-2 rounded-apple" style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.75);">
                            Berikutnya<i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @else
                        <span class="px-4 py-2 rounded-apple" style="background: rgba(255,255,255,0.05); color: rgba(235,235,245,0.35);">
                            Berikutnya<i class="fas fa-chevron-right ml-2"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </section>

<style>
.pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    border: 1px solid rgba(84,84,88,0.35);
    background: rgba(28,28,30,0.4);
    color: rgba(235,235,245,0.7);
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.pill span {
    padding: 0.1rem 0.45rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.1);
    font-size: 0.7rem;
}
.pill-active {
    border-color: rgba(10,132,255,0.5);
    background: rgba(10,132,255,0.15);
    color: rgba(10,132,255,0.95);
}
.star-button {
    color: rgba(235,235,245,0.4);
    transition: color 0.2s ease;
}
.star-button.active {
    color: rgba(255,214,10,1);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.email-item {
    position: relative;
}
.email-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: rgba(10,132,255,0.6);
    opacity: 0;
    transition: opacity 0.2s ease;
}
.email-item:hover::before {
    opacity: 1;
}
.border-top {
    border-top: 1px solid rgba(255,255,255,0.08);
}
</style>

@endsection
