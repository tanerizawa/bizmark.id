@extends('layouts.admin')

@section('page-title', 'Notifikasi')

@section('content')
<!-- Hero Section -->
<div class="card-elevated rounded-apple-xl p-5 md:p-6 mb-6 relative overflow-hidden">
    <div class="w-72 h-72 bg-apple-purple opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
    <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    
    <div class="relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-1">PEMBERITAHUAN</p>
                <h1 class="text-2xl font-bold text-dark-text-primary mb-2">Notifikasi</h1>
                <p class="text-sm text-dark-text-secondary">
                    Pantau semua aktivitas dan update penting
                </p>
            </div>
            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm text-apple-blue font-medium rounded-apple transition" style="background: transparent;" onmouseover="this.style.backgroundColor='rgba(0,122,255,0.10)'" onmouseout="this.style.backgroundColor='transparent'">
                    <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Notifications List -->
<div class="card-elevated rounded-apple-xl overflow-hidden">
    <div class="divide-y divide-dark-separator">
        @forelse($notifications as $notification)
            <a href="{{ $notification->action_url ? route('admin.notifications.read', $notification->id) : '#' }}" 
               class="block p-5 hover:bg-dark-bg-tertiary transition {{ !$notification->is_read ? 'bg-dark-bg-secondary' : '' }}">
                <div class="flex items-start gap-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center" 
                         style="background: rgba({{ 
                             $notification->color === 'apple-blue' ? '10,132,255' : 
                             ($notification->color === 'apple-purple' ? '175,82,222' : 
                             ($notification->color === 'apple-orange' ? '255,159,10' : 
                             ($notification->color === 'apple-green' ? '52,199,89' : '255,59,48'))) 
                         }}, 0.15);">
                        <i class="fas {{ $notification->icon }} text-{{ $notification->color }} text-lg"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="text-sm font-semibold text-dark-text-primary">
                                {{ $notification->title }}
                            </h3>
                            @if(!$notification->is_read)
                                <span class="ml-2 w-2 h-2 bg-apple-blue rounded-full flex-shrink-0"></span>
                            @endif
                        </div>
                        <p class="text-sm text-dark-text-secondary mb-2">
                            {{ $notification->message }}
                        </p>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-dark-text-tertiary">
                                <i class="far fa-clock mr-1"></i>{{ $notification->time_ago }}
                            </span>
                            @if($notification->action_url)
                                <span class="text-xs text-apple-blue font-medium">
                                    Lihat Detail →
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-dark-bg-tertiary flex items-center justify-center">
                    <i class="fas fa-bell-slash text-dark-text-tertiary text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-dark-text-primary mb-2">Tidak ada notifikasi</h3>
                <p class="text-sm text-dark-text-secondary">
                    Anda sudah membaca semua notifikasi
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="p-4 border-t border-dark-separator">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
