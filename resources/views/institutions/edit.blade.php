@extends('layouts.admin')

@section('title', 'Edit Institusi - ' . $institution->name)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="text-sm breadcrumbs mb-4">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-apple-blue-dark hover:text-apple-blue">Dashboard</a>
                    <svg class="w-3 h-3 mx-3" style="color: rgba(142, 142, 147, 0.8);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('institutions.index') }}" class="text-apple-blue-dark hover:text-apple-blue">Institusi</a>
                    <svg class="w-3 h-3 mx-3" style="color: rgba(142, 142, 147, 0.8);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('institutions.show', $institution) }}" class="text-apple-blue-dark hover:text-apple-blue">{{ Str::limit($institution->name, 30) }}</a>
                    <svg class="w-3 h-3 mx-3" style="color: rgba(142, 142, 147, 0.8);" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li style="color: rgba(235, 235, 245, 0.6);">Edit</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Edit Institusi</h1>
        <p class="mt-2" style="color: rgba(235, 235, 245, 0.6);">Perbarui informasi institusi {{ $institution->name }}</p>
    </div>

    <form action="{{ route('institutions.update', $institution) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <!-- Basic Information -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-6" style="color: #FFFFFF;">Informasi Dasar</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Nama Institusi <span class="text-apple-red-dark">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $institution->name) }}"
                                   placeholder="Contoh: Dinas Lingkungan Hidup DKI Jakarta"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('name') ring-2 ring-apple-red @enderror"
                                   required>
                            @error('name')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Tipe Institusi <span class="text-apple-red-dark">*</span>
                            </label>
                            <input type="text" 
                                   id="type" 
                                   name="type" 
                                   value="{{ old('type', $institution->type) }}"
                                   placeholder="Contoh: Pemerintah, Swasta, NGO"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('type') ring-2 ring-apple-red @enderror"
                                   required>
                            @error('type')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="is_active" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Status
                            </label>
                            <select id="is_active" 
                                    name="is_active" 
                                    class="input-dark w-full px-3 py-2 rounded-lg @error('is_active') ring-2 ring-apple-red @enderror">
                                <option value="1" {{ old('is_active', $institution->is_active) ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ !old('is_active', $institution->is_active) ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                            @error('is_active')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Alamat
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      placeholder="Alamat lengkap institusi..."
                                      class="input-dark w-full px-3 py-2 rounded-lg @error('address') ring-2 ring-apple-red @enderror">{{ old('address', $institution->address) }}</textarea>
                            @error('address')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-6" style="color: #FFFFFF;">Informasi Kontak</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Nama Kontak
                            </label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   value="{{ old('contact_person', $institution->contact_person) }}"
                                   placeholder="Nama person in charge"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('contact_person') ring-2 ring-apple-red @enderror">
                            @error('contact_person')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Position -->
                        <div>
                            <label for="contact_position" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Jabatan Kontak
                            </label>
                            <input type="text" 
                                   id="contact_position" 
                                   name="contact_position" 
                                   value="{{ old('contact_position', $institution->contact_position) }}"
                                   placeholder="Contoh: Kepala Bidang, Manager"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('contact_position') ring-2 ring-apple-red @enderror">
                            @error('contact_position')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Nomor Telepon
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $institution->phone) }}"
                                   placeholder="021-12345678"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('phone') ring-2 ring-apple-red @enderror">
                            @error('phone')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Email
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $institution->email) }}"
                                   placeholder="contact@institusi.com"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('email') ring-2 ring-apple-red @enderror">
                            @error('email')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <h2 class="text-xl font-semibold mb-6" style="color: #FFFFFF;">Informasi Tambahan</h2>
                    
                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Catatan
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  placeholder="Catatan khusus tentang institusi..."
                                  class="input-dark w-full px-3 py-2 rounded-lg @error('notes') ring-2 ring-apple-red @enderror">{{ old('notes', $institution->notes) }}</textarea>
                        @error('notes')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Current Info -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Informasi Saat Ini</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Nama</div>
                            <div class="font-medium" style="color: #FFFFFF;">{{ $institution->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Tipe</div>
                            <div class="font-medium" style="color: #FFFFFF;">{{ $institution->type }}</div>
                        </div>
                        <div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Status</div>
                            <span class="px-2 py-1 rounded-full text-xs font-medium"
                                style="@if($institution->is_active) background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1); @else background: rgba(255, 69, 58, 0.15); color: rgba(255, 69, 58, 1); @endif">
                                {{ $institution->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                        @if($institution->contact_person)
                            <div>
                                <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Kontak</div>
                                <div class="font-medium" style="color: #FFFFFF;">{{ $institution->contact_person }}</div>
                                @if($institution->contact_position)
                                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $institution->contact_position }}</div>
                                @endif
                            </div>
                        @endif
                        <div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Proyek</div>
                            <div class="font-medium" style="color: #FFFFFF;">{{ $institution->projects->count() }} proyek</div>
                        </div>
                        <div>
                            <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Terdaftar</div>
                            <div class="font-medium" style="color: #FFFFFF;">{{ $institution->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <div class="space-y-3">
                        <button type="submit" 
                                class="btn-primary w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                        
                        <a href="{{ route('institutions.show', $institution) }}" 
                           class="w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center" style="background: rgba(142, 142, 147, 0.6); color: rgba(235, 235, 245, 0.8);">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>

                        @if($institution->projects->count() === 0)
                            <hr class="my-4" style="border-color: rgba(142, 142, 147, 0.3);">
                            <form action="{{ route('institutions.destroy', $institution) }}"
                                  method="POST"
                                  x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus institusi ini?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn-danger w-full py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                    <i class="fas fa-trash mr-2"></i>
                                    Hapus Institusi
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection