@extends('layouts.admin')

@section('title', 'Detail Pelanggan - ' . $subscriber->email)
@section('page-title', 'Detail Pelanggan')

@section('content')
@php
    $statusMeta = [
        'active'       => ['label' => 'Aktif',                   'bg' => 'rgba(52,199,89,0.15)',  'text' => 'rgba(52,199,89,1)'],
        'unsubscribed' => ['label' => 'Berhenti Berlangganan',   'bg' => 'rgba(255,149,0,0.15)',  'text' => 'rgba(255,149,0,1)'],
        'bounced'      => ['label' => 'Email Gagal',             'bg' => 'rgba(255,69,58,0.15)',  'text' => 'rgba(255,69,58,1)'],
    ];
    $sm = $statusMeta[$subscriber->status] ?? ['label' => ucfirst($subscriber->status), 'bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(235,235,245,0.6)'];
@endphp

{{-- Hero --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
    </div>
    <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.subscribers.index') }}" style="color: rgba(235,235,245,0.6);" class="hover:text-white transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Audiens</p>
                <h1 class="text-xl font-bold text-white">{{ $subscriber->name ?: $subscriber->email }}</h1>
                @if($subscriber->name)
                    <p class="text-sm" style="color: rgba(235,235,245,0.5);">{{ $subscriber->email }}</p>
                @endif
            </div>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-semibold" style="background: {{ $sm['bg'] }}; color: {{ $sm['text'] }};">
                {{ $sm['label'] }}
            </span>
            <a href="{{ route('admin.subscribers.edit', $subscriber->id) }}"
               class="px-4 py-1.5 rounded-apple-lg text-xs font-semibold transition-colors"
               style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);"
               onmouseover="this.style.background='rgba(10,132,255,0.3)'"
               onmouseout="this.style.background='rgba(10,132,255,0.2)'">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
        </div>
    </div>
</section>

@if(session('success'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle"></i>
        <span class="text-sm">{{ session('success') }}</span>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Info Card -->
    <div class="lg:col-span-1">
        <div class="card-elevated rounded-apple-xl p-5 space-y-4">
            <h2 class="font-semibold text-sm uppercase tracking-wide" style="color: rgba(235,235,245,0.5);">Informasi Pelanggan</h2>

            <dl class="space-y-3 text-sm">
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Email</dt>
                    <dd style="color: rgba(235,235,245,0.9);">{{ $subscriber->email }}</dd>
                </div>
                @if($subscriber->name)
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Nama</dt>
                    <dd style="color: rgba(235,235,245,0.9);">{{ $subscriber->name }}</dd>
                </div>
                @endif
                @if($subscriber->phone)
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Nomor HP</dt>
                    <dd style="color: rgba(235,235,245,0.9);">{{ $subscriber->phone }}</dd>
                </div>
                @endif
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Sumber</dt>
                    <dd style="color: rgba(235,235,245,0.7);">{{ ucfirst($subscriber->source ?? 'manual') }}</dd>
                </div>
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Bergabung</dt>
                    <dd style="color: rgba(235,235,245,0.7);">{{ $subscriber->created_at?->format('d M Y') ?? '—' }}</dd>
                </div>
                @if($subscriber->unsubscribed_at)
                <div>
                    <dt style="color: rgba(235,235,245,0.4);" class="text-xs mb-0.5">Berhenti</dt>
                    <dd style="color: rgba(255,149,0,0.9);">{{ $subscriber->unsubscribed_at->format('d M Y') }}</dd>
                </div>
                @endif
            </dl>

            @if(!empty($subscriber->tags))
            <div>
                <p class="text-xs mb-2" style="color: rgba(235,235,245,0.4);">Tags</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach((is_array($subscriber->tags) ? $subscriber->tags : [$subscriber->tags]) as $tag)
                    <span class="px-2 py-0.5 rounded-full text-xs" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                        {{ $tag }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="pt-2 flex flex-col gap-2">
                @if($subscriber->status === 'active')
                <form action="{{ route('admin.subscribers.update', $subscriber->id) }}" method="POST" x-data @submit.prevent="if(confirm('Nonaktifkan pelanggan ini?')) $el.submit()">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="unsubscribed">
                    <input type="hidden" name="email" value="{{ $subscriber->email }}">
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-apple-lg text-xs font-medium transition-colors"
                        style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1);">
                        <i class="fas fa-user-slash mr-1"></i>Nonaktifkan
                    </button>
                </form>
                @else
                <form action="{{ route('admin.subscribers.update', $subscriber->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="active">
                    <input type="hidden" name="email" value="{{ $subscriber->email }}">
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-apple-lg text-xs font-medium transition-colors"
                        style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                        <i class="fas fa-user-check mr-1"></i>Aktifkan Kembali
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.subscribers.destroy', $subscriber->id) }}" method="POST" x-data @submit.prevent="if(confirm('Hapus pelanggan ini secara permanen?')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full px-4 py-2 rounded-apple-lg text-xs font-medium transition-colors"
                        style="background: rgba(255,69,58,0.12); color: rgba(255,69,58,1);">
                        <i class="fas fa-trash mr-1"></i>Hapus Pelanggan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Email Logs -->
    <div class="lg:col-span-2">
        <div class="card-elevated rounded-apple-xl p-5">
            <h2 class="font-semibold mb-4" style="color: #FFFFFF;">
                <i class="fas fa-history mr-2" style="color: rgba(10,132,255,1);"></i>
                Riwayat Email
                <span class="ml-2 text-xs px-2 py-0.5 rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">
                    {{ $subscriber->emailLogs?->count() ?? 0 }}
                </span>
            </h2>

            @if($subscriber->emailLogs && $subscriber->emailLogs->count() > 0)
                <div class="space-y-3">
                    @foreach($subscriber->emailLogs->sortByDesc('created_at') as $log)
                    <div class="flex items-start gap-3 p-3 rounded-apple-lg" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);">
                        @php
                            $logStatus = $log->status ?? 'sent';
                            $logIcon = match($logStatus) {
                                'delivered' => ['icon' => 'fa-check-double', 'color' => 'rgba(52,199,89,1)'],
                                'opened'    => ['icon' => 'fa-envelope-open', 'color' => 'rgba(10,132,255,1)'],
                                'clicked'   => ['icon' => 'fa-mouse-pointer', 'color' => 'rgba(88,86,214,1)'],
                                'bounced'   => ['icon' => 'fa-exclamation-triangle', 'color' => 'rgba(255,69,58,1)'],
                                'failed'    => ['icon' => 'fa-times-circle', 'color' => 'rgba(255,69,58,1)'],
                                default     => ['icon' => 'fa-paper-plane', 'color' => 'rgba(255,149,0,1)'],
                            };
                        @endphp
                        <div class="mt-0.5">
                            <i class="fas {{ $logIcon['icon'] }} text-sm" style="color: {{ $logIcon['color'] }};"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium truncate" style="color: rgba(235,235,245,0.9);">
                                {{ $log->subject ?? $log->campaign_name ?? 'Email' }}
                            </p>
                            <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.4);">
                                {{ $log->created_at?->format('d M Y, H:i') ?? '—' }}
                                @if($log->campaign_type)
                                    · {{ ucfirst($log->campaign_type) }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full flex-shrink-0"
                              style="background: {{ $logIcon['color'] }}22; color: {{ $logIcon['color'] }};">
                            {{ ucfirst($logStatus) }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10">
                    <i class="fas fa-inbox text-3xl mb-3" style="color: rgba(235,235,245,0.15);"></i>
                    <p class="text-sm" style="color: rgba(235,235,245,0.4);">Belum ada riwayat email</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
