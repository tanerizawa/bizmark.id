@extends('layouts.admin')

@section('title', 'Edit Klien - ' . $client->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center">
            <a href="{{ route('clients.show', $client) }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-white">Edit Klien</h1>
                <p class="text-xs mt-1 text-dark-text-secondary">Perbarui informasi klien: {{ $client->name }}</p>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('clients.show', $client) }}" 
               class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center bg-white/10 text-dark-text-primary/80">
                <i class="fas fa-eye mr-1.5"></i>Lihat Detail
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card-elevated rounded-apple-lg">
        <form action="{{ route('clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Informasi Dasar -->
            <div class="px-4 py-2.5 border-b border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Dasar
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label for="name" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Nama Klien <span class="text-apple-red">*</span>
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('name') border-apple-red @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $client->name) }}" 
                               required>
                        @error('name')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_name" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Nama Perusahaan
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('company_name') border-apple-red @enderror" 
                               id="company_name" 
                               name="company_name" 
                               value="{{ old('company_name', $client->company_name) }}">
                        @error('company_name')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="client_type" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Tipe Klien <span class="text-apple-red">*</span>
                        </label>
                        <select class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('client_type') border-apple-red @enderror" 
                                id="client_type" 
                                name="client_type" 
                                required>
                            <option value="">Pilih Tipe Klien</option>
                            <option value="individual" {{ old('client_type', $client->client_type) == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="company" {{ old('client_type', $client->client_type) == 'company' ? 'selected' : '' }}>Perusahaan</option>
                            <option value="government" {{ old('client_type', $client->client_type) == 'government' ? 'selected' : '' }}>Pemerintah</option>
                        </select>
                        @error('client_type')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="industry" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Industri
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('industry') border-apple-red @enderror" 
                               id="industry" 
                               name="industry" 
                               value="{{ old('industry', $client->industry) }}" 
                               placeholder="Contoh: Konstruksi, Perdagangan, dll">
                        @error('industry')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Status <span class="text-apple-red">*</span>
                        </label>
                        <select class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('status') border-apple-red @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="active" {{ old('status', $client->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $client->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="potential" {{ old('status', $client->status) == 'potential' ? 'selected' : '' }}>Potensial</option>
                        </select>
                        @error('status')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Informasi Kontak -->
            <div class="px-4 py-2.5 border-b border-white/10 border-t border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-address-book mr-2 text-apple-green"></i>Informasi Kontak
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label for="contact_person" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Nama Contact Person
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('contact_person') border-apple-red @enderror" 
                               id="contact_person" 
                               name="contact_person" 
                               value="{{ old('contact_person', $client->contact_person) }}">
                        @error('contact_person')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Email
                        </label>
                        <input type="email" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('email') border-apple-red @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $client->email) }}">
                        @error('email')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Telepon
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('phone') border-apple-red @enderror" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $client->phone) }}" 
                               placeholder="Contoh: 021-12345678">
                        @error('phone')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Handphone / WhatsApp
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('mobile') border-apple-red @enderror" 
                               id="mobile" 
                               name="mobile" 
                               value="{{ old('mobile', $client->mobile) }}" 
                               placeholder="Contoh: 083879602855">
                        @error('mobile')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Alamat -->
            <div class="px-4 py-2.5 border-b border-white/10 border-t border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-map-marker-alt mr-2 text-apple-orange"></i>Alamat
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 gap-3">
                    <div>
                        <label for="address" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Alamat Lengkap
                        </label>
                        <textarea class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('address') border-apple-red @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3">{{ old('address', $client->address) }}</textarea>
                        @error('address')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label for="city" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                                Kota
                            </label>
                            <input type="text" 
                                   class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('city') border-apple-red @enderror" 
                                   id="city" 
                                   name="city" 
                                   value="{{ old('city', $client->city) }}">
                            @error('city')
                                <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="province" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                                Provinsi
                            </label>
                            <input type="text" 
                                   class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('province') border-apple-red @enderror" 
                                   id="province" 
                                   name="province" 
                                   value="{{ old('province', $client->province) }}">
                            @error('province')
                                <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                                Kode Pos
                            </label>
                            <input type="text" 
                                   class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('postal_code') border-apple-red @enderror" 
                                   id="postal_code" 
                                   name="postal_code" 
                                   value="{{ old('postal_code', $client->postal_code) }}">
                            @error('postal_code')
                                <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Pajak -->
            <div class="px-4 py-2.5 border-b border-white/10 border-t border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-file-invoice mr-2 text-apple-purple"></i>Informasi Pajak
                </h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label for="npwp" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            NPWP
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('npwp') border-apple-red @enderror" 
                               id="npwp" 
                               name="npwp" 
                               value="{{ old('npwp', $client->npwp) }}" 
                               placeholder="Contoh: 12.345.678.9-012.345">
                        @error('npwp')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_name" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Nama di NPWP
                        </label>
                        <input type="text" 
                               class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('tax_name') border-apple-red @enderror" 
                               id="tax_name" 
                               name="tax_name" 
                               value="{{ old('tax_name', $client->tax_name) }}">
                        @error('tax_name')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="tax_address" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                            Alamat NPWP
                        </label>
                        <textarea class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('tax_address') border-apple-red @enderror" 
                                  id="tax_address" 
                                  name="tax_address" 
                                  rows="2">{{ old('tax_address', $client->tax_address) }}</textarea>
                        @error('tax_address')
                            <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="px-4 py-2.5 border-b border-white/10 border-t border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-sticky-note mr-2 text-apple-teal"></i>Catatan
                </h3>
            </div>
            <div class="p-4">
                <div>
                    <label for="notes" class="block text-xs font-medium mb-1 text-dark-text-secondary">
                        Catatan Tambahan
                    </label>
                    <textarea class="input-dark w-full px-3 py-2 rounded-lg text-sm @error('notes') border-apple-red @enderror" 
                              id="notes" 
                              name="notes" 
                              rows="3" 
                              placeholder="Catatan internal tentang klien ini...">{{ old('notes', $client->notes) }}</textarea>
                    @error('notes')
                        <p class="text-xs mt-1 text-apple-red">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Metadata Info -->
            <div class="px-4 py-2 bg-[#1C1C1E]/50 border-t border-white/10">
                <div class="flex items-center justify-between text-xs text-dark-text-tertiary/50">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="fas fa-calendar-plus mr-1.5"></i>Dibuat: {{ $client->created_at->format('d M Y H:i') }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar-check mr-1.5"></i>Terakhir diubah: {{ $client->updated_at->format('d M Y H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-2 p-4 border-t border-white/10">
                <a href="{{ route('clients.show', $client) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center bg-white/10 text-dark-text-primary/80">
                    <i class="fas fa-times mr-1.5"></i>Batal
                </a>
                <button type="submit" 
                        class="btn-primary px-4 py-2 text-white rounded-lg text-sm font-medium inline-flex items-center">
                    <i class="fas fa-save mr-1.5"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
