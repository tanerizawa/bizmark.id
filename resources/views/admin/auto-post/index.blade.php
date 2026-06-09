@extends('layouts.admin')

@section('title', 'Auto-Post AI')

@section('content')
<div class="space-y-4"
     x-data="{
         activeTab: '{{ $activeTab ?? 'config' }}',
         isEnabled: {{ $config->is_enabled ? 'true' : 'false' }},
         toggleAutoPost() {
             fetch('{{ route("auto-post.config.toggle") }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                 }
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     this.isEnabled = data.is_enabled;
                 }
             })
             .catch(() => {
                 alert('Gagal mengubah status auto-post. Silakan coba lagi.');
             });
         }
     }"
     x-init="
         $watch('activeTab', val => {
             const url = new URL(window.location);
             url.searchParams.set('tab', val);
             window.history.pushState({}, '', url);
         });
         window.addEventListener('popstate', () => {
             const params = new URLSearchParams(window.location.search);
             activeTab = params.get('tab') || 'config';
         });
     ">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-3xl rounded-full absolute -top-12 -right-8"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Content Automation</p>
                <h1 class="admin-hero-title text-white">Auto-Post AI</h1>
                <p class="admin-hero-desc max-w-xl">Otomatisasi pembuatan dan publikasi artikel AI</p>
                <div class="admin-hero-meta flex flex-wrap gap-3">
                    <span><i class="fas fa-robot mr-1.5"></i><span x-text="isEnabled ? 'Aktif' : 'Nonaktif'"></span></span>
                    <span><i class="fas fa-lightbulb mr-1.5"></i>{{ $stats['available_topics'] }} topics</span>
                    <span><i class="fas fa-clock mr-1.5"></i>{{ $scheduleStats['pending'] }} pending</span>
                </div>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-apple" style="background: rgba(255,255,255,0.08);">
                <button
                    @click="toggleAutoPost()"
                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none"
                    :class="isEnabled ? 'bg-apple-blue' : 'bg-gray-600'">
                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"
                          :class="isEnabled ? 'translate-x-4' : 'translate-x-1'"></span>
                </button>
                <span class="admin-label text-white" x-text="isEnabled ? 'Aktif' : 'Nonaktif'"></span>
            </div>
        </div>
    </section>

    {{-- Alert Messages - Compact --}}
    @if(session('success'))
        <div class="admin-alert flex items-center justify-between" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2" style="color: var(--apple-green);"></i>
                <span class="admin-body" style="color: var(--apple-green);">{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="admin-small opacity-60 hover:opacity-100" style="color: var(--apple-green);">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="admin-alert flex items-center justify-between" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2" style="color: var(--apple-red);"></i>
                <span class="admin-body" style="color: var(--apple-red);">{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="admin-small opacity-60 hover:opacity-100" style="color: var(--apple-red);">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Tab Navigation - Compact --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="border-b" style="border-color: var(--dark-separator);">
            <div class="flex gap-1 p-1.5 overflow-x-auto" role="tablist">
                <button @click="activeTab = 'config'"
                        :class="{ 'active': activeTab === 'config' }"
                        class="tab-button whitespace-nowrap"
                        :aria-selected="activeTab === 'config'"
                        role="tab">
                    <i class="fas fa-cog mr-1.5"></i>Konfigurasi
                </button>
                <button @click="activeTab = 'analytics'"
                        :class="{ 'active': activeTab === 'analytics' }"
                        class="tab-button whitespace-nowrap"
                        :aria-selected="activeTab === 'analytics'"
                        role="tab">
                    <i class="fas fa-chart-line mr-1.5"></i>Analytics
                </button>
                <button @click="activeTab = 'topics'"
                        :class="{ 'active': activeTab === 'topics' }"
                        class="tab-button whitespace-nowrap"
                        :aria-selected="activeTab === 'topics'"
                        role="tab">
                    <i class="fas fa-lightbulb mr-1.5"></i>Topics
                    <span class="ml-1.5 admin-badge"
                          :class="activeTab === 'topics' ? 'bg-white text-apple-blue' : 'bg-apple-purple text-white'">
                        {{ $topicStats['available'] }}
                    </span>
                </button>
                <button @click="activeTab = 'schedules'"
                        :class="{ 'active': activeTab === 'schedules' }"
                        class="tab-button whitespace-nowrap"
                        :aria-selected="activeTab === 'schedules'"
                        role="tab">
                    <i class="fas fa-calendar-alt mr-1.5"></i>Jadwal
                    @if($scheduleStats['pending'] > 0)
                        <span class="ml-1.5 admin-badge"
                              :class="activeTab === 'schedules' ? 'bg-white text-apple-blue' : 'bg-yellow-500 text-white'">
                            {{ $scheduleStats['pending'] }}
                        </span>
                    @endif
                </button>
            </div>
        </div>

        <div class="p-4">
            {{-- Config Tab --}}
            <div x-show="activeTab === 'config'" x-transition.opacity.duration.200ms role="tabpanel">
                @include('admin.auto-post.tabs.config')
            </div>
            
            {{-- Analytics Tab --}}
            <div x-show="activeTab === 'analytics'" x-transition.opacity.duration.200ms role="tabpanel">
                @include('admin.auto-post.tabs.analytics')
            </div>
            
            {{-- Topics Tab --}}
            <div x-show="activeTab === 'topics'" x-transition.opacity.duration.200ms role="tabpanel">
                @include('admin.auto-post.tabs.topics')
            </div>
            
            {{-- Schedules Tab --}}
            <div x-show="activeTab === 'schedules'" x-transition.opacity.duration.200ms role="tabpanel">
                @include('admin.auto-post.tabs.schedules')
            </div>
        </div>
    </section>
</div>

<style>
.tab-button {
    @apply px-4 py-2.5 rounded-apple font-medium;
    color: rgba(235, 235, 245, 0.6);
    background: transparent;
}

.tab-button:hover:not(.active) {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(235, 235, 245, 0.8);
}

.tab-button.active {
    background: var(--apple-blue);
    color: #FFFFFF;
}
</style>
@endsection
