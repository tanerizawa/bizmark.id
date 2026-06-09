@extends('layouts.admin')

@section('title', 'Edit Pelanggan - ' . $subscriber->email)
@section('page-title', 'Edit Pelanggan')

@section('content')
{{-- Hero --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
    </div>
    <div class="relative flex items-center gap-4">
        <a href="{{ route('admin.subscribers.show', $subscriber->id) }}" style="color: rgba(235,235,245,0.6);" class="hover:text-white transition-colors">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Audiens</p>
            <h1 class="text-xl font-bold text-white">Edit Pelanggan</h1>
            <p class="text-sm mt-0.5" style="color: rgba(235,235,245,0.5);">{{ $subscriber->email }}</p>
        </div>
    </div>
</section>

@if(session('error'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(255,69,58,0.12); border: 1px solid rgba(255,69,58,0.3); color: rgba(255,69,58,1);">
        <i class="fas fa-exclamation-circle"></i>
        <span class="text-sm">{{ session('error') }}</span>
    </div>
@endif

<form action="{{ route('admin.subscribers.update', $subscriber->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-5">

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.7);">
                Email <span style="color: rgba(255,69,58,1);">*</span>
            </label>
            <input
                type="email"
                name="email"
                value="{{ old('email', $subscriber->email) }}"
                required
                class="w-full px-4 py-2.5 rounded-apple-lg text-sm"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(235,235,245,0.9);"
            >
            @error('email')
                <p class="text-xs mt-1.5" style="color: rgba(255,69,58,1);">{{ $message }}</p>
            @enderror
        </div>

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.7);">
                Nama
            </label>
            <input
                type="text"
                name="name"
                value="{{ old('name', $subscriber->name) }}"
                placeholder="Nama lengkap"
                class="w-full px-4 py-2.5 rounded-apple-lg text-sm"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(235,235,245,0.9);"
            >
            @error('name')
                <p class="text-xs mt-1.5" style="color: rgba(255,69,58,1);">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.7);">
                Nomor HP
            </label>
            <input
                type="text"
                name="phone"
                value="{{ old('phone', $subscriber->phone) }}"
                placeholder="08xx-xxxx-xxxx"
                class="w-full px-4 py-2.5 rounded-apple-lg text-sm"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(235,235,245,0.9);"
            >
            @error('phone')
                <p class="text-xs mt-1.5" style="color: rgba(255,69,58,1);">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.7);">
                Status <span style="color: rgba(255,69,58,1);">*</span>
            </label>
            <select
                name="status"
                required
                class="w-full px-4 py-2.5 rounded-apple-lg text-sm"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(235,235,245,0.9);"
            >
                <option value="active" {{ old('status', $subscriber->status) === 'active' ? 'selected' : '' }} style="background:#1c1c1e;">Aktif</option>
                <option value="unsubscribed" {{ old('status', $subscriber->status) === 'unsubscribed' ? 'selected' : '' }} style="background:#1c1c1e;">Berhenti Berlangganan</option>
                <option value="bounced" {{ old('status', $subscriber->status) === 'bounced' ? 'selected' : '' }} style="background:#1c1c1e;">Email Gagal</option>
            </select>
            @error('status')
                <p class="text-xs mt-1.5" style="color: rgba(255,69,58,1);">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tags -->
        <div>
            <label class="block text-sm font-medium mb-1.5" style="color: rgba(235,235,245,0.7);">
                Tags <span class="text-xs" style="color: rgba(235,235,245,0.4);">(pisahkan dengan koma)</span>
            </label>
            <input
                type="text"
                name="tags_input"
                value="{{ old('tags_input', is_array($subscriber->tags) ? implode(', ', $subscriber->tags) : $subscriber->tags) }}"
                placeholder="newsletter, promo"
                class="w-full px-4 py-2.5 rounded-apple-lg text-sm"
                style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: rgba(235,235,245,0.9);"
            >
            @error('tags')
                <p class="text-xs mt-1.5" style="color: rgba(255,69,58,1);">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <!-- Actions -->
    <div class="flex gap-3 mt-5">
        <button
            type="submit"
            class="flex-1 px-6 py-3 rounded-apple-lg text-sm font-semibold transition-colors"
            style="background: rgba(10,132,255,1); color: #FFFFFF;"
            onmouseover="this.style.background='rgba(10,132,255,0.85)'"
            onmouseout="this.style.background='rgba(10,132,255,1)'"
        >
            <i class="fas fa-save mr-2"></i>Simpan Perubahan
        </button>
        <a
            href="{{ route('admin.subscribers.show', $subscriber->id) }}"
            class="px-6 py-3 rounded-apple-lg text-sm font-semibold transition-colors"
            style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.7);"
            onmouseover="this.style.background='rgba(255,255,255,0.12)'"
            onmouseout="this.style.background='rgba(255,255,255,0.08)'"
        >
            Batal
        </a>
    </div>
</form>
@endsection
