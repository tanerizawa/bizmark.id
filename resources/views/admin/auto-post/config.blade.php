@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="card-elevated rounded-apple-xl p-5 md:p-6 mb-6 relative overflow-hidden">
        <!-- Background Gradient Effects -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        
        <div class="relative flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.4em] mb-1" style="color: rgba(235,235,245,0.5);">AUTOMATION</p>
                <h1 class="text-lg font-bold text-white">Konfigurasi Auto-Post</h1>
                <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">
                    Atur sistem posting artikel otomatis dengan AI
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- Status Toggle -->
                <button 
                    id="toggleAutoPost"
                    data-enabled="{{ $config->is_enabled ? 'true' : 'false' }}"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 {{ $config->is_enabled ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-700' }}">
                    <span class="sr-only">Toggle auto-post</span>
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $config->is_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
                <span class="text-sm font-medium text-white">
                    {{ $config->is_enabled ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/20 p-4 text-sm text-green-800 dark:text-green-300 border border-green-200 dark:border-green-900/50">
            {{ session('success') }}
        </div>
    @endif

    <!-- Configuration Form -->
    <form action="{{ route('auto-post.config.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Scheduling Settings -->
        <div class="card-elevated rounded-lg shadow-sm p-6 border border-gray-700/50">
            <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-clock" style="color: rgba(10,132,255,1);"></i>
                Pengaturan Jadwal
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Jumlah Post Per Hari
                    </label>
                    <input 
                        type="number" 
                        name="posts_per_day" 
                        value="{{ old('posts_per_day', $config->posts_per_day) }}" 
                        min="1" 
                        max="10"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                    @error('posts_per_day')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Cooldown Days
                    </label>
                    <input 
                        type="number" 
                        name="cooldown_days" 
                        value="{{ old('cooldown_days', $config->cooldown_days) }}" 
                        min="1" 
                        max="365"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                    <p class="mt-1 text-xs" style="color: rgba(235,235,245,0.6);">Hari sebelum topik serupa bisa digunakan lagi</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                    Waktu Posting (Format 24 jam: HH:MM)
                </label>
                <div id="postTimes" class="space-y-2">
                    @foreach($config->post_times as $index => $time)
                        <div class="flex items-center space-x-2">
                            <input 
                                type="time" 
                                name="post_times[]" 
                                value="{{ $time }}"
                                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                            @if($index > 0)
                                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button 
                    type="button" 
                    onclick="addTimeSlot()"
                    class="mt-2 text-sm text-blue-400 hover:text-blue-300">
                    + Tambah Waktu
                </button>
            </div>
        </div>

        <!-- AI Settings -->
        <div class="card-elevated rounded-lg shadow-sm p-6 border border-gray-700/50">
            <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-brain" style="color: rgba(175,82,222,1);"></i>
                Pengaturan AI
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        AI Model
                    </label>
                    <select 
                        name="ai_model" 
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                        <option value="anthropic/claude-3.5-sonnet" {{ $config->ai_model === 'anthropic/claude-3.5-sonnet' ? 'selected' : '' }}>
                            Claude 3.5 Sonnet (Recommended)
                        </option>
                        <option value="anthropic/claude-3-haiku" {{ $config->ai_model === 'anthropic/claude-3-haiku' ? 'selected' : '' }}>
                            Claude 3 Haiku (Faster)
                        </option>
                        <option value="openai/gpt-4" {{ $config->ai_model === 'openai/gpt-4' ? 'selected' : '' }}>
                            GPT-4
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Content Quality Settings -->
        <div class="card-elevated rounded-lg shadow-sm p-6 border border-gray-700/50">
            <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle" style="color: rgba(52,199,89,1);"></i>
                Kualitas Konten
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Min Word Count
                    </label>
                    <input 
                        type="number" 
                        name="min_word_count" 
                        value="{{ old('min_word_count', $config->min_word_count) }}" 
                        min="300"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Max Word Count
                    </label>
                    <input 
                        type="number" 
                        name="max_word_count" 
                        value="{{ old('max_word_count', $config->max_word_count) }}" 
                        min="500"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Duplicate Threshold (0-1)
                    </label>
                    <input 
                        type="number" 
                        name="duplicate_threshold" 
                        value="{{ old('duplicate_threshold', $config->duplicate_threshold) }}" 
                        min="0" 
                        max="1" 
                        step="0.01"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                    <p class="mt-1 text-xs" style="color: rgba(235,235,245,0.6);">Deteksi duplikat artikel (0 = no check, 1 = exact match)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235,235,245,0.8);">
                        Internal Links Count
                    </label>
                    <input 
                        type="number" 
                        name="internal_links_count" 
                        value="{{ old('internal_links_count', $config->internal_links_count) }}" 
                        min="0" 
                        max="10"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                    <p class="mt-1 text-xs" style="color: rgba(235,235,245,0.6);">Jumlah anchor link otomatis</p>
                </div>
            </div>
        </div>

        <!-- Language & Market Settings -->
        <div class="card-elevated rounded-lg shadow-sm p-6 border border-gray-700/50">
            <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-globe" style="color: rgba(52,199,89,1);"></i>
                Language & Target Market
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Language Distribution -->
                <div>
                    <label class="block text-sm font-medium mb-3" style="color: rgba(235,235,245,0.8);">
                        Distribusi Bahasa (%)
                    </label>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs" style="color: rgba(235,235,245,0.6);">
                                🇮🇩 Bahasa Indonesia (Local Market)
                            </label>
                            <input 
                                type="number" 
                                name="language_distribution[id]" 
                                value="{{ old('language_distribution.id', $config->language_distribution['id'] ?? 60) }}" 
                                min="0" 
                                max="100"
                                class="w-full mt-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                            <p class="mt-1 text-xs" style="color: rgba(235,235,245,0.5);">
                                Untuk pemilik usaha lokal, UMKM
                            </p>
                        </div>
                        <div>
                            <label class="text-xs" style="color: rgba(235,235,245,0.6);">
                                🇬🇧 English (PMA/Foreign Investment)
                            </label>
                            <input 
                                type="number" 
                                name="language_distribution[en]" 
                                value="{{ old('language_distribution.en', $config->language_distribution['en'] ?? 40) }}" 
                                min="0" 
                                max="100"
                                class="w-full mt-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                            <p class="mt-1 text-xs" style="color: rgba(235,235,245,0.5);">
                                Untuk foreign investors, PMA companies
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Market Focus -->
                <div>
                    <label class="block text-sm font-medium mb-3" style="color: rgba(235,235,245,0.8);">
                        Target Market Focus
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input 
                                type="checkbox" 
                                name="market_focus[local]" 
                                value="1"
                                {{ ($config->market_focus['local'] ?? true) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                            <span class="ml-2">
                                <span class="block text-sm font-medium" style="color: rgba(235,235,245,0.8);">
                                    Local Business (UMKM, CV, PT Lokal)
                                </span>
                                <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                                    Fokus perizinan lokal, NIB, IMB, izin usaha kecil-menengah
                                </span>
                            </span>
                        </label>

                        <label class="flex items-start">
                            <input 
                                type="checkbox" 
                                name="market_focus[pma]" 
                                value="1"
                                {{ ($config->market_focus['pma'] ?? true) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                            <span class="ml-2">
                                <span class="block text-sm font-medium" style="color: rgba(235,235,245,0.8);">
                                    PMA / Foreign Investment
                                </span>
                                <span class="text-xs" style="color: rgba(235,235,245,0.5);">
                                    Foreign investment licensing, PMA regulations, expat business setup
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-4 p-3 rounded-lg bg-blue-500/10 border border-blue-500/30">
                        <p class="text-xs" style="color: rgba(10,132,255,0.9);">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Tips:</strong> Aktifkan kedua market untuk coverage maksimal. Sistem akan generate konten dalam bahasa yang sesuai dengan target market.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Publishing Options -->
        <div class="card-elevated rounded-lg shadow-sm p-6 border border-gray-700/50">
            <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-rocket" style="color: rgba(255,149,0,1);"></i>
                Opsi Publishing
            </h2>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="auto_publish" 
                        value="1"
                        {{ $config->auto_publish ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 dark:text-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
                    <span class="ml-2 text-sm" style="color: rgba(235,235,245,0.8);">
                        Auto-publish artikel (langsung published tanpa review)
                    </span>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('articles.index') }}" class="px-4 py-2 text-sm font-medium text-gray-300 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-600">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addTimeSlot() {
    const container = document.getElementById('postTimes');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input 
            type="time" 
            name="post_times[]" 
            class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-500 focus:ring-blue-500 dark:focus:ring-blue-500">
        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 text-red-600 hover:text-red-700 dark:text-red-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(div);
}

// Toggle auto-post
document.getElementById('toggleAutoPost').addEventListener('click', function() {
    const button = this;
    const isEnabled = button.dataset.enabled === 'true';
    
    fetch('{{ route('auto-post.config.toggle') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.dataset.enabled = data.is_enabled ? 'true' : 'false';
            button.classList.toggle('bg-blue-600', data.is_enabled);
            button.classList.toggle('bg-gray-300', !data.is_enabled);
            button.classList.toggle('dark:bg-gray-700', !data.is_enabled);
            button.querySelector('span:last-child').classList.toggle('translate-x-6', data.is_enabled);
            button.querySelector('span:last-child').classList.toggle('translate-x-1', !data.is_enabled);
            // Update status text (next sibling span)
            const statusText = button.nextElementSibling;
            if (statusText) statusText.textContent = data.is_enabled ? 'Aktif' : 'Nonaktif';
            
            // Show toast
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            toast.textContent = data.message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    });
});
</script>
@endpush
@endsection
