@extends('layouts.admin')

@section('title', 'Edit Topic')
@section('page-title', 'Edit Topic')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-orange opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between">
                <div class="space-y-2">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Content Management</p>
                    <h1 class="text-2xl md:text-xl font-bold" style="color: #FFFFFF;">
                        Edit Topic: {{ $topic->title }}
                    </h1>
                    <p class="text-sm" style="color: rgba(235,235,245,0.75);">
                        Update topic untuk artikel auto-generated oleh AI
                    </p>
                </div>
                <a href="{{ route('auto-post.topics.index') }}" class="px-4 py-2.5 rounded-apple text-sm font-medium text-dark-text-primary transition-apple" 
                   style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </section>

    {{-- Form --}}
    <div class="card-elevated rounded-apple-xl p-6">
        <form action="{{ route('auto-post.topics.update', $topic) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Title Field --}}
            <div>
                <label class="block text-sm font-medium text-dark-text-primary mb-3">
                    Judul Topic <span class="text-apple-red">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title', $topic->title) }}" 
                    required
                    placeholder="Contoh: 10 Tips Mengurus IMB Dengan Cepat"
                    class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                    style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                @error('title')
                    <p class="mt-2 text-sm text-apple-red">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-dark-text-tertiary">
                    <i class="fas fa-info-circle mr-1"></i>Judul akan digunakan sebagai konteks untuk AI dalam generate artikel
                </p>
            </div>

            {{-- Description Field --}}
            <div>
                <label class="block text-sm font-medium text-dark-text-primary mb-3">
                    Deskripsi
                </label>
                <textarea 
                    name="description" 
                    rows="4"
                    placeholder="Brief description atau angle yang ingin dibahas..."
                    class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                    style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">{{ old('description', $topic->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-apple-red">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category & Priority --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-dark-text-primary mb-3">
                        Kategori <span class="text-apple-red">*</span>
                    </label>
                    <select 
                        name="category" 
                        required
                        class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                        style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                        <option value="">Pilih Kategori</option>
                        <option value="tips" {{ old('category', $topic->category) === 'tips' ? 'selected' : '' }}>Tips</option>
                        <option value="guide" {{ old('category', $topic->category) === 'guide' ? 'selected' : '' }}>Guide</option>
                        <option value="case-study" {{ old('category', $topic->category) === 'case-study' ? 'selected' : '' }}>Case Study</option>
                        <option value="news" {{ old('category', $topic->category) === 'news' ? 'selected' : '' }}>News</option>
                        <option value="regulation" {{ old('category', $topic->category) === 'regulation' ? 'selected' : '' }}>Regulation</option>
                        <option value="general" {{ old('category', $topic->category) === 'general' ? 'selected' : '' }}>General</option>
                    </select>
                    @error('category')
                        <p class="mt-2 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark-text-primary mb-3">
                        Priority (1-10) <span class="text-apple-red">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="priority" 
                        value="{{ old('priority', $topic->priority) }}" 
                        min="1" 
                        max="10"
                        required
                        class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                        style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    @error('priority')
                        <p class="mt-2 text-sm text-apple-red">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-dark-text-tertiary">
                        <i class="fas fa-info-circle mr-1"></i>10 = Highest priority
                    </p>
                </div>
            </div>

            {{-- Keywords --}}
            <div>
                <label class="block text-sm font-medium text-dark-text-primary mb-3">
                    Keywords <span class="text-apple-red">*</span>
                </label>
                <div id="keywords" class="space-y-3">
                    @foreach(old('keywords', $topic->keywords) as $keyword)
                    <div class="flex items-center space-x-2">
                        <input 
                            type="text" 
                            name="keywords[]" 
                            value="{{ $keyword }}"
                            required
                            placeholder="Masukkan keyword..."
                            class="flex-1 px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                            style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                        @if(!$loop->first)
                        <button type="button" onclick="this.parentElement.remove()" class="p-2 text-apple-red hover:text-red-400 transition-apple">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
                <button 
                    type="button" 
                    onclick="addKeyword()"
                    class="mt-3 inline-flex items-center text-sm text-apple-blue hover:text-apple-blue-dark transition-apple">
                    <i class="fas fa-plus mr-2"></i>Tambah Keyword
                </button>
                @error('keywords')
                    <p class="mt-2 text-sm text-apple-red">{{ $message }}</p>
                @enderror
                <p class="mt-3 text-xs text-dark-text-tertiary">
                    <i class="fas fa-info-circle mr-1"></i>Keywords digunakan AI untuk memahami konteks dan menghasilkan konten yang relevan
                </p>
            </div>

            {{-- Advanced Options --}}
            <div class="space-y-6">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-cogs text-apple-purple"></i>
                    <h3 class="text-lg font-semibold text-dark-text-primary">Advanced Options</h3>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-dark-text-primary mb-3">
                            Generation Notes
                        </label>
                        <textarea 
                            name="generation_notes" 
                            rows="3"
                            placeholder="Instruksi khusus untuk AI: target audience, content angle, gaya penulisan, dll."
                            class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                            style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">{{ old('generation_notes', $topic->generation_notes) }}</textarea>
                        <p class="mt-2 text-xs text-dark-text-tertiary">
                            <i class="fas fa-info-circle mr-1"></i>Contoh: Targetkan pengusaha UMKM, fokus panduan praktis, gunakan bahasa sederhana
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-dark-text-primary mb-3">
                                Language
                            </label>
                            <select 
                                name="language"
                                class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                                style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <option value="id" {{ old('language', $topic->language ?? 'id') === 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                <option value="en" {{ old('language', $topic->language) === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark-text-primary mb-3">
                                Target Market
                            </label>
                            <select 
                                name="target_market"
                                class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
                                style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                                <option value="local" {{ old('target_market', $topic->target_market ?? 'local') === 'local' ? 'selected' : '' }}>Local (UMKM, CV, PT Lokal)</option>
                                <option value="pma" {{ old('target_market', $topic->target_market) === 'pma' ? 'selected' : '' }}>PMA / Foreign Investment</option>
                                <option value="both" {{ old('target_market', $topic->target_market) === 'both' ? 'selected' : '' }}>Both</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Buttons --}}
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3 pt-6 border-t" style="border-color: var(--dark-separator);">
                <a href="{{ route('auto-post.topics.index') }}" class="px-6 py-3 text-sm font-medium text-dark-text-primary rounded-apple transition-apple text-center" 
                   style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="px-6 py-3 text-sm font-medium text-white bg-apple-orange rounded-apple hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-apple-orange transition-apple">
                    <i class="fas fa-save mr-2"></i>Update Topic
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addKeyword() {
    const container = document.getElementById('keywords');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input 
            type="text" 
            name="keywords[]" 
            placeholder="Masukkan keyword..."
            class="flex-1 px-4 py-3 rounded-apple text-sm text-dark-text-primary focus:outline-none focus:ring-2 focus:ring-apple-blue" 
            style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
        <button type="button" onclick="this.parentElement.remove()" class="p-2 text-apple-red hover:text-red-400 transition-apple">
            <i class="fas fa-trash text-sm"></i>
        </button>
    `;
    container.appendChild(div);
}
</script>
@endpush
@endsection
