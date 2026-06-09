@extends('layouts.admin')

@section('title', 'Tambah Jenis Izin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('permit-types.index') }}" 
           class="inline-flex items-center text-sm mb-4 hover:text-apple-blue transition-colors" 
           style="color: rgba(235, 235, 245, 0.6);">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Tambah Jenis Izin Baru</h1>
        <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">Tambahkan jenis izin atau perizinan baru ke katalog</p>
    </div>

    <form action="{{ route('permit-types.store') }}" method="POST">
        @csrf

        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Informasi Dasar</h3>

            <!-- Name -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                    Nama Izin <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="input-dark w-full px-3 py-2 rounded-md @error('name') border-red-500 @enderror"
                       placeholder="Contoh: Upaya Pengelolaan Lingkungan (UKL-UPL)"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                    Kode Izin <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="code" 
                       value="{{ old('code') }}"
                       class="input-dark w-full px-3 py-2 rounded-md @error('code') border-red-500 @enderror"
                       placeholder="Contoh: UKL-UPL"
                       required>
                @error('code')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs" style="color: rgba(235, 235, 245, 0.5);">
                    Kode unik untuk identifikasi izin
                </p>
            </div>

            <!-- Category & Institution -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category" 
                            class="input-dark w-full px-3 py-2 rounded-md @error('category') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Kategori</option>
                        <option value="environmental" {{ old('category') == 'environmental' ? 'selected' : '' }}>Environmental</option>
                        <option value="land" {{ old('category') == 'land' ? 'selected' : '' }}>Land</option>
                        <option value="building" {{ old('category') == 'building' ? 'selected' : '' }}>Building</option>
                        <option value="transportation" {{ old('category') == 'transportation' ? 'selected' : '' }}>Transportation</option>
                        <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Business</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Institusi Penerbit <span class="text-red-500">*</span>
                    </label>
                    <select name="institution_id" 
                            class="input-dark w-full px-3 py-2 rounded-md @error('institution_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Institusi</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('institution_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                    Deskripsi
                </label>
                <textarea name="description" 
                          rows="3"
                          class="input-dark w-full px-3 py-2 rounded-md @error('description') border-red-500 @enderror"
                          placeholder="Deskripsi singkat tentang izin ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Estimasi Waktu & Biaya</h3>

            <!-- Processing Days -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                    Rata-rata Waktu Proses (hari)
                </label>
                <input type="number" 
                       name="avg_processing_days" 
                       value="{{ old('avg_processing_days') }}"
                       min="1"
                       class="input-dark w-full px-3 py-2 rounded-md @error('avg_processing_days') border-red-500 @enderror"
                       placeholder="Contoh: 14">
                @error('avg_processing_days')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Cost Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Estimasi Biaya Minimum (Rp)
                    </label>
                    <input type="number" 
                           name="estimated_cost_min" 
                           value="{{ old('estimated_cost_min') }}"
                           min="0"
                           step="100000"
                           class="input-dark w-full px-3 py-2 rounded-md @error('estimated_cost_min') border-red-500 @enderror"
                           placeholder="Contoh: 5000000">
                    @error('estimated_cost_min')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Estimasi Biaya Maksimum (Rp)
                    </label>
                    <input type="number" 
                           name="estimated_cost_max" 
                           value="{{ old('estimated_cost_max') }}"
                           min="0"
                           step="100000"
                           class="input-dark w-full px-3 py-2 rounded-md @error('estimated_cost_max') border-red-500 @enderror"
                           placeholder="Contoh: 15000000">
                    @error('estimated_cost_max')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Dokumen yang Diperlukan</h3>
            
            <div id="documents-container">
                @if(old('required_documents'))
                    @foreach(old('required_documents') as $index => $doc)
                        <div class="document-item mb-3 flex items-center gap-2">
                            <input type="text" 
                                   name="required_documents[]" 
                                   value="{{ $doc }}"
                                   class="input-dark flex-1 px-3 py-2 rounded-md"
                                   placeholder="Contoh: Formulir Permohonan">
                            <button type="button" 
                                    class="remove-document px-3 py-2 rounded-md hover:bg-red-600 transition-colors" 
                                    style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <div class="document-item mb-3 flex items-center gap-2">
                        <input type="text" 
                               name="required_documents[]" 
                               class="input-dark flex-1 px-3 py-2 rounded-md"
                               placeholder="Contoh: Formulir Permohonan">
                        <button type="button" 
                                class="remove-document px-3 py-2 rounded-md hover:bg-red-600 transition-colors" 
                                style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif
            </div>

            <button type="button" 
                    id="add-document"
                    class="mt-3 px-4 py-2 rounded-md transition-colors" 
                    style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                <i class="fas fa-plus mr-2"></i>Tambah Dokumen
            </button>

            @error('required_documents')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
            @error('required_documents.*')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="card-elevated rounded-apple-lg p-6 mb-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="is_active" 
                       value="1"
                       {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-5 h-5 rounded" 
                       style="background: rgba(58, 58, 60, 0.8); border-color: rgba(99, 99, 102, 0.5);">
                <span class="ml-3 text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">
                    Aktifkan jenis izin ini
                </span>
            </label>
            <p class="mt-2 text-xs ml-8" style="color: rgba(235, 235, 245, 0.5);">
                Jenis izin yang tidak aktif tidak akan muncul dalam pilihan saat membuat proyek
            </p>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('permit-types.index') }}" 
               class="px-6 py-2 rounded-md transition-colors" 
               style="background: rgba(142, 142, 147, 0.3); color: rgba(235, 235, 245, 0.8);">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2 rounded-md transition-colors" 
                    style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                <i class="fas fa-save mr-2"></i>Simpan Jenis Izin
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('documents-container');
    const addButton = document.getElementById('add-document');

    // Add new document field
    addButton.addEventListener('click', function() {
        const newItem = document.createElement('div');
        newItem.className = 'document-item mb-3 flex items-center gap-2';
        newItem.innerHTML = `
            <input type="text" 
                   name="required_documents[]" 
                   class="input-dark flex-1 px-3 py-2 rounded-md"
                   placeholder="Contoh: Formulir Permohonan">
            <button type="button" 
                    class="remove-document px-3 py-2 rounded-md hover:bg-red-600 transition-colors" 
                    style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
    });

    // Remove document field (using event delegation)
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-document')) {
            const item = e.target.closest('.document-item');
            if (container.children.length > 1) {
                item.remove();
            } else {
                alert('Minimal harus ada satu dokumen');
            }
        }
    });
});
</script>
@endpush
@endsection
