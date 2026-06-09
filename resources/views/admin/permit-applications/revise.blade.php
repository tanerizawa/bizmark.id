@extends('layouts.admin')

@section('title', 'Revisi Paket Aplikasi')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto"
     x-data="{ activeTab: 'preset' }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Revisi Paket Aplikasi</h1>
            <p class="text-gray-400 mt-1">{{ $application->application_number }} - {{ $application->client->name }}</p>
        </div>
        <a href="{{ route('admin.permit-applications.show', $application->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.permit-applications.revisions.store', $application->id) }}" method="POST" id="revisionForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Main Form --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Section 1: Alasan Revisi --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700 bg-blue-600/20">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-edit text-blue-400"></i>1. Alasan Revisi
                        </h5>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Tipe Revisi <span class="text-red-400">*</span>
                            </label>
                            <select name="revision_type" required
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('revision_type') border-red-500 @enderror">
                                <option value="">Pilih Tipe Revisi</option>
                                <option value="technical_adjustment" {{ old('revision_type') == 'technical_adjustment' ? 'selected' : '' }}>Penyesuaian Teknis</option>
                                <option value="client_request" {{ old('revision_type') == 'client_request' ? 'selected' : '' }}>Permintaan Client</option>
                                <option value="cost_update" {{ old('revision_type') == 'cost_update' ? 'selected' : '' }}>Update Biaya</option>
                                <option value="document_incomplete" {{ old('revision_type') == 'document_incomplete' ? 'selected' : '' }}>Dokumen Tidak Lengkap</option>
                            </select>
                            @error('revision_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-white mb-1">
                                Penjelasan Detail <span class="text-red-400">*</span>
                            </label>
                            <textarea name="revision_reason" rows="4" required
                                      placeholder="Jelaskan alasan revisi paket ini..."
                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('revision_reason') border-red-500 @enderror">{{ old('revision_reason') }}</textarea>
                            @error('revision_reason')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Contoh: "Setelah kajian teknis di lokasi, diperlukan penambahan izin X karena..."</p>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Daftar Izin --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700 bg-blue-600/20">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-400"></i>2. Daftar Izin
                        </h5>
                    </div>
                    <div class="p-5">
                        <div id="permitsContainer" class="space-y-3">
                            @if(old('permits'))
                                @foreach(old('permits') as $index => $permit)
                                    <div class="permit-item bg-gray-900 border border-gray-700 rounded-xl p-4" data-index="{{ $index }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <h6 class="text-white font-medium text-sm">Izin #<span class="permit-number">{{ $index + 1 }}</span></h6>
                                            <button type="button" onclick="removePermit(this)"
                                                    class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs text-gray-400 mb-1">Jenis Izin <span class="text-red-400">*</span></label>
                                                <select name="permits[{{ $index }}][permit_type_id]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 permit-type-select" required onchange="updatePermitInfo(this)">
                                                    <option value="">Pilih Jenis Izin</option>
                                                    @foreach($permitTypes as $permitType)
                                                        <option value="{{ $permitType->id }}" data-name="{{ $permitType->name }}" data-base-price="{{ $permitType->base_price }}" {{ $permit['permit_type_id'] == $permitType->id ? 'selected' : '' }}>{{ $permitType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Jenis Layanan <span class="text-red-400">*</span></label>
                                                <select name="permits[{{ $index }}][service_type]" required class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="bizmark" {{ $permit['service_type'] == 'bizmark' ? 'selected' : '' }}>BizMark</option>
                                                    <option value="owned" {{ $permit['service_type'] == 'owned' ? 'selected' : '' }}>Milik Sendiri</option>
                                                    <option value="self" {{ $permit['service_type'] == 'self' ? 'selected' : '' }}>Urus Sendiri</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Biaya (Rp) <span class="text-red-400">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][unit_price]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 permit-price" value="{{ $permit['unit_price'] }}" min="0" step="1000" required onkeyup="calculateTotal()">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Estimasi (Hari) <span class="text-red-400">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][estimated_days]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $permit['estimated_days'] }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @php $index = 0; @endphp
                                @foreach($currentPackage['permits'] as $permit)
                                    <div class="permit-item bg-gray-900 border border-gray-700 rounded-xl p-4" data-index="{{ $index }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <h6 class="text-white font-medium text-sm">Izin #<span class="permit-number">{{ $index + 1 }}</span></h6>
                                            <button type="button" onclick="removePermit(this)"
                                                    class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                                                <i class="fas fa-trash mr-1"></i>Hapus
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div class="sm:col-span-2">
                                                <label class="block text-xs text-gray-400 mb-1">Jenis Izin <span class="text-red-400">*</span></label>
                                                <select name="permits[{{ $index }}][permit_type_id]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 permit-type-select" required onchange="updatePermitInfo(this)">
                                                    <option value="">Pilih Jenis Izin</option>
                                                    @foreach($permitTypes as $permitType)
                                                        <option value="{{ $permitType->id }}" data-name="{{ $permitType->name }}" data-base-price="{{ $permitType->base_price }}" {{ isset($permit['permit_type_id']) && $permit['permit_type_id'] == $permitType->id ? 'selected' : '' }}>{{ $permitType->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Jenis Layanan <span class="text-red-400">*</span></label>
                                                <select name="permits[{{ $index }}][service_type]" required class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <option value="bizmark" {{ isset($permit['service_type']) && $permit['service_type'] == 'bizmark' ? 'selected' : '' }}>BizMark</option>
                                                    <option value="owned" {{ isset($permit['service_type']) && $permit['service_type'] == 'owned' ? 'selected' : '' }}>Milik Sendiri</option>
                                                    <option value="self" {{ isset($permit['service_type']) && $permit['service_type'] == 'self' ? 'selected' : '' }}>Urus Sendiri</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Biaya (Rp) <span class="text-red-400">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][unit_price]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 permit-price" value="{{ $permit['unit_price'] ?? 0 }}" min="0" step="1000" required onkeyup="calculateTotal()">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-400 mb-1">Estimasi (Hari) <span class="text-red-400">*</span></label>
                                                <input type="number" name="permits[{{ $index }}][estimated_days]" class="w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $permit['estimated_days'] ?? 30 }}" min="1" required>
                                            </div>
                                        </div>
                                    </div>
                                    @php $index++; @endphp
                                @endforeach
                            @endif
                        </div>

                        <button type="button" onclick="addPermit()"
                            class="mt-4 inline-flex items-center px-4 py-2 border border-blue-600 text-blue-400 hover:bg-blue-600/10 text-sm font-medium rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i>Tambah Izin
                        </button>
                    </div>
                </div>

                {{-- Section 3: Data Lokasi --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700 bg-blue-600/20 flex items-center justify-between">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-blue-400"></i>3. Data Lokasi Proyek
                        </h5>
                        <span class="text-xs text-gray-400">Opsional</span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @php
                                $locFields = [
                                    ['province', 'Provinsi', 'Contoh: Jawa Barat'],
                                    ['city_regency', 'Kabupaten/Kota', 'Contoh: Bandung'],
                                    ['district', 'Kecamatan', 'Contoh: Cimahi'],
                                    ['sub_district', 'Kelurahan', 'Contoh: Cimahi Tengah'],
                                ];
                            @endphp
                            @foreach($locFields as [$key, $label, $placeholder])
                            <div>
                                <label class="block text-sm text-white mb-1">{{ $label }}</label>
                                <input type="text" name="location[{{ $key }}]" placeholder="{{ $placeholder }}"
                                       value="{{ old('location.'.$key, $application->locationDetail->{$key} ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            @endforeach
                            <div class="sm:col-span-2">
                                <label class="block text-sm text-white mb-1">Alamat Lengkap</label>
                                <textarea name="location[full_address]" rows="3" placeholder="Jl. ..."
                                          class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('location.full_address', $application->locationDetail->full_address ?? '') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Kode Pos</label>
                                <input type="text" name="location[postal_code]" placeholder="40000"
                                       value="{{ old('location.postal_code', $application->locationDetail->postal_code ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Latitude</label>
                                <input type="text" name="location[latitude]" placeholder="-6.123456"
                                       value="{{ old('location.latitude', $application->locationDetail->latitude ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Longitude</label>
                                <input type="text" name="location[longitude]" placeholder="106.123456"
                                       value="{{ old('location.longitude', $application->locationDetail->longitude ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Zona/Kawasan</label>
                                <select name="location[zone_type]" class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Zona</option>
                                    @foreach(['industrial' => 'Industri', 'commercial' => 'Komersial', 'residential' => 'Residensial', 'mixed' => 'Campuran', 'special_economic_zone' => 'KEK'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('location.zone_type', $application->locationDetail->zone_type ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Status Lahan</label>
                                <select name="location[land_status]" class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Status</option>
                                    @foreach(['HGB' => 'HGB', 'HGU' => 'HGU', 'Hak_Milik' => 'Hak Milik', 'Girik' => 'Girik', 'Sewa' => 'Sewa', 'Other' => 'Lainnya'] as $val => $lbl)
                                    <option value="{{ $val }}" {{ old('location.land_status', $application->locationDetail->land_status ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm text-white mb-1">Nomor Sertifikat</label>
                                <input type="text" name="location[land_certificate_number]" placeholder="No. Sertifikat..."
                                       value="{{ old('location.land_certificate_number', $application->locationDetail->land_certificate_number ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <hr class="border-gray-700 my-5">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-white mb-1">Luas Tanah (m²)</label>
                                <input type="number" name="land_area" min="0" step="0.01"
                                       value="{{ old('land_area', $currentPackage['project_data']['land_area'] ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Luas Bangunan (m²)</label>
                                <input type="number" name="building_area" min="0" step="0.01"
                                       value="{{ old('building_area', $currentPackage['project_data']['building_area'] ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-white mb-1">Nilai Investasi (Rp)</label>
                                <input type="number" name="investment_value" min="0" step="1000000"
                                       value="{{ old('investment_value', $currentPackage['project_data']['investment_value'] ?? '') }}"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Dokumen Legalitas (Alpine.js tabs) --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700 bg-blue-600/20 flex items-center justify-between">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-400"></i>4. Dokumen Legalitas
                        </h5>
                        <span class="text-xs text-gray-400">Opsional</span>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-400 text-sm mb-4">
                            <i class="fas fa-info-circle mr-1"></i>Tandai dokumen yang tersedia/diperlukan, atau tambahkan dokumen custom
                        </p>

                        {{-- Tab Nav --}}
                        <div class="flex gap-1 border-b border-gray-700 mb-5">
                            <button type="button" @click="activeTab = 'preset'"
                                    class="px-4 py-2 text-sm font-medium rounded-t-lg transition"
                                    :class="activeTab === 'preset' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'">
                                <i class="fas fa-list mr-1.5"></i>Dokumen Standar
                            </button>
                            <button type="button" @click="activeTab = 'custom'"
                                    class="px-4 py-2 text-sm font-medium rounded-t-lg transition"
                                    :class="activeTab === 'custom' ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-white'">
                                <i class="fas fa-plus-circle mr-1.5"></i>Dokumen Custom
                            </button>
                        </div>

                        {{-- Preset Documents --}}
                        <div x-show="activeTab === 'preset'" x-cloak>
                            @php
                                $documentCategories = [
                                    'land_ownership'  => ['label' => 'Sertifikat Tanah',       'icon' => 'fa-file-alt',        'examples' => 'HGB, HGU, Hak Milik, atau Girik'],
                                    'company_legal'   => ['label' => 'Legalitas Perusahaan',    'icon' => 'fa-building',        'examples' => 'Akta Pendirian, NPWP, NIB, TDP'],
                                    'existing_permits'=> ['label' => 'Izin Yang Sudah Ada',     'icon' => 'fa-stamp',           'examples' => 'IMB Existing, SIPA, SIUP'],
                                    'power_of_attorney'=> ['label' => 'Surat Kuasa',            'icon' => 'fa-file-signature',  'examples' => 'Jika diwakilkan oleh pihak lain'],
                                    'technical'       => ['label' => 'Dokumen Teknis',          'icon' => 'fa-drafting-compass','examples' => 'Site Plan, Gambar Arsitek, DED'],
                                ];
                                $existingDocs = $application->legalityDocuments->keyBy('document_category');
                            @endphp

                            <div class="space-y-3">
                                @foreach($documentCategories as $category => $info)
                                @php $existing = $existingDocs->get($category); @endphp
                                <div class="document-item border border-gray-700 rounded-xl p-4 hover:border-gray-500 transition">
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox"
                                               class="w-4 h-4 mt-0.5 accent-blue-500 preset-doc-checkbox"
                                               name="legality_documents[preset_{{ $loop->index }}][is_available]"
                                               value="1"
                                               id="doc_{{ $category }}"
                                               {{ $existing && $existing->is_available ? 'checked' : '' }}
                                               onchange="toggleDocumentFields(this)">
                                        <div class="flex-1">
                                            <label for="doc_{{ $category }}" class="flex items-center gap-2 font-medium text-white text-sm cursor-pointer mb-1">
                                                <i class="fas {{ $info['icon'] }} text-blue-400"></i>{{ $info['label'] }}
                                            </label>
                                            <p class="text-xs text-gray-400 mb-2">
                                                <i class="fas fa-arrow-right mr-1"></i>{{ $info['examples'] }}
                                            </p>

                                            <input type="hidden" name="legality_documents[preset_{{ $loop->index }}][category]" value="{{ $category }}">
                                            <input type="hidden" name="legality_documents[preset_{{ $loop->index }}][name]" value="{{ $info['label'] }}">

                                            <div class="document-details grid grid-cols-2 gap-2 mt-2" style="display: {{ $existing && $existing->is_available ? 'grid' : 'none' }};">
                                                <div class="flex items-center border border-gray-600 rounded-lg overflow-hidden">
                                                    <span class="px-2 py-1.5 bg-gray-700 text-gray-400 text-xs border-r border-gray-600"><i class="fas fa-hashtag"></i></span>
                                                    <input type="text" name="legality_documents[preset_{{ $loop->index }}][number]"
                                                           class="flex-1 bg-gray-900 text-white text-xs px-2 py-1.5 focus:outline-none"
                                                           placeholder="No. Dokumen"
                                                           value="{{ $existing->document_number ?? '' }}">
                                                </div>
                                                <div class="flex items-center border border-gray-600 rounded-lg overflow-hidden">
                                                    <span class="px-2 py-1.5 bg-gray-700 text-gray-400 text-xs border-r border-gray-600"><i class="fas fa-sticky-note"></i></span>
                                                    <input type="text" name="legality_documents[preset_{{ $loop->index }}][notes]"
                                                           class="flex-1 bg-gray-900 text-white text-xs px-2 py-1.5 focus:outline-none"
                                                           placeholder="Catatan / Keterangan"
                                                           value="{{ $existing->notes ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex items-start gap-3 bg-blue-500/10 border border-blue-500/30 text-blue-300 rounded-xl px-4 py-3 mt-4 text-xs">
                                <i class="fas fa-lightbulb mt-0.5 flex-shrink-0"></i>
                                <span><strong>Tips:</strong> Centang dokumen yang sudah tersedia atau yang diperlukan. Untuk dokumen lain, gunakan tab "Dokumen Custom".</span>
                            </div>
                        </div>

                        {{-- Custom Documents --}}
                        <div x-show="activeTab === 'custom'" x-cloak>
                            <p class="text-gray-400 text-sm mb-4">
                                <i class="fas fa-info-circle mr-1"></i>Tambahkan dokumen legalitas lain yang spesifik untuk aplikasi ini
                            </p>

                            @php
                                $customDocs = $application->legalityDocuments->where('document_category', 'other');
                                $customIndex = 0;
                            @endphp

                            <div id="customDocsContainer" class="space-y-3">
                                @foreach($customDocs as $customDoc)
                                <div class="custom-doc-item border border-green-800 rounded-xl p-4" data-index="{{ $customIndex }}">
                                    <div class="flex items-center justify-between mb-3">
                                        <h6 class="text-white text-sm font-medium">
                                            <i class="fas fa-file-alt text-green-400 mr-2"></i>Dokumen Custom #<span class="doc-number">{{ $customIndex + 1 }}</span>
                                        </h6>
                                        <button type="button" onclick="removeCustomDoc(this)"
                                                class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="legality_documents[custom_{{ $customIndex }}][category]" value="other">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs text-gray-400 mb-1">Nama Dokumen <span class="text-red-400">*</span></label>
                                            <input type="text" name="legality_documents[custom_{{ $customIndex }}][name]" required
                                                   placeholder="Contoh: Surat Persetujuan Tetangga"
                                                   value="{{ $customDoc->document_name }}"
                                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-400 mb-1">Status</label>
                                            <select name="legality_documents[custom_{{ $customIndex }}][is_available]" class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="1" {{ $customDoc->is_available ? 'selected' : '' }}>✓ Tersedia</option>
                                                <option value="0" {{ !$customDoc->is_available ? 'selected' : '' }}>✗ Belum Ada</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-400 mb-1">No. Dokumen</label>
                                            <input type="text" name="legality_documents[custom_{{ $customIndex }}][number]" placeholder="No. / Ref"
                                                   value="{{ $customDoc->document_number }}"
                                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs text-gray-400 mb-1">Tanggal Terbit</label>
                                            <input type="date" name="legality_documents[custom_{{ $customIndex }}][issued_date]"
                                                   value="{{ $customDoc->issued_date?->format('Y-m-d') }}"
                                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs text-gray-400 mb-1">Catatan</label>
                                            <textarea name="legality_documents[custom_{{ $customIndex }}][notes]" rows="2"
                                                      placeholder="Keterangan tambahan..."
                                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $customDoc->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                @php $customIndex++; @endphp
                                @endforeach
                            </div>

                            <button type="button" onclick="addCustomDoc()"
                                class="mt-4 inline-flex items-center px-4 py-2 border border-green-600 text-green-400 hover:bg-green-600/10 text-sm font-medium rounded-lg transition">
                                <i class="fas fa-plus mr-2"></i>Tambah Dokumen Custom
                            </button>

                            <div class="flex items-start gap-3 bg-yellow-500/10 border border-yellow-500/30 text-yellow-300 rounded-xl px-4 py-3 mt-4 text-xs">
                                <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
                                <span><strong>Catatan:</strong> Dokumen custom akan masuk kategori "other". Pastikan nama dokumen jelas dan spesifik.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: Summary & Actions --}}
            <div>
                <div class="sticky top-4 space-y-4">

                    {{-- Cost Summary --}}
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                        <div class="px-5 py-4 border-b border-gray-700 bg-green-600/20">
                            <h5 class="text-white font-semibold flex items-center gap-2">
                                <i class="fas fa-calculator text-green-400"></i>Ringkasan Biaya
                            </h5>
                        </div>
                        <div class="p-5 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Jumlah Izin:</span>
                                <strong class="text-white" id="totalPermitsCount">0</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Estimasi Total:</span>
                                <strong class="text-white" id="totalDaysEstimate">0 hari</strong>
                            </div>
                            <hr class="border-gray-700">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-medium">Total Biaya:</span>
                                <span class="text-green-400 text-xl font-bold" id="totalCost">Rp 0</span>
                            </div>
                        </div>
                        <div class="px-5 pb-5 space-y-2">
                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                                <i class="fas fa-save mr-2"></i>Simpan Revisi
                            </button>
                            <a href="{{ route('admin.permit-applications.show', $application->id) }}"
                               class="block w-full text-center border border-gray-600 text-gray-300 hover:text-white font-medium py-2.5 rounded-xl transition text-sm">
                                Batal
                            </a>
                        </div>
                    </div>

                    {{-- Previous Revisions --}}
                    @if($revisions->count() > 0)
                    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                        <div class="px-5 py-4 border-b border-gray-700 bg-cyan-600/20">
                            <h6 class="text-white font-semibold flex items-center gap-2 text-sm">
                                <i class="fas fa-history text-cyan-400"></i>Riwayat Revisi
                            </h6>
                        </div>
                        <div class="divide-y divide-gray-700">
                            @foreach($revisions as $rev)
                            <div class="px-5 py-3">
                                <div class="flex items-center justify-between mb-1">
                                    <strong class="text-white text-sm">Revisi #{{ $rev->revision_number }}</strong>
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @if($rev->status == 'approved') bg-green-500/20 text-green-400
                                        @elseif($rev->status == 'rejected') bg-red-500/20 text-red-400
                                        @else bg-yellow-500/20 text-yellow-400
                                        @endif">
                                        {{ ucfirst($rev->status) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">{{ $rev->created_at->format('d M Y H:i') }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($rev->revision_reason, 50) }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let permitIndex = {{ $index ?? 0 }};
let customDocIndex = {{ $customIndex ?? 0 }};

const inputCls = 'w-full bg-gray-800 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500';
const selectCls = inputCls;
const labelCls = 'block text-xs text-gray-400 mb-1';

// ── Permit Functions ──────────────────────────────────────────
function addPermit() {
    permitIndex++;
    const html = `
        <div class="permit-item bg-gray-900 border border-gray-700 rounded-xl p-4" data-index="${permitIndex}">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-white font-medium text-sm">Izin #<span class="permit-number">${permitIndex + 1}</span></h6>
                <button type="button" onclick="removePermit(this)" class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                    <i class="fas fa-trash mr-1"></i>Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="${labelCls}">Jenis Izin <span class="text-red-400">*</span></label>
                    <select name="permits[${permitIndex}][permit_type_id]" class="${selectCls} permit-type-select" required onchange="updatePermitInfo(this)">
                        <option value="">Pilih Jenis Izin</option>
                        @foreach($permitTypes as $permitType)
                        <option value="{{ $permitType->id }}" data-name="{{ $permitType->name }}" data-base-price="{{ $permitType->base_price }}">{{ $permitType->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="${labelCls}">Jenis Layanan <span class="text-red-400">*</span></label>
                    <select name="permits[${permitIndex}][service_type]" required class="${selectCls}">
                        <option value="bizmark">BizMark</option>
                        <option value="owned">Milik Sendiri</option>
                        <option value="self">Urus Sendiri</option>
                    </select>
                </div>
                <div>
                    <label class="${labelCls}">Biaya (Rp) <span class="text-red-400">*</span></label>
                    <input type="number" name="permits[${permitIndex}][unit_price]" class="${inputCls} permit-price" value="0" min="0" step="1000" required onkeyup="calculateTotal()">
                </div>
                <div>
                    <label class="${labelCls}">Estimasi (Hari) <span class="text-red-400">*</span></label>
                    <input type="number" name="permits[${permitIndex}][estimated_days]" class="${inputCls}" value="30" min="1" required>
                </div>
            </div>
        </div>`;
    document.getElementById('permitsContainer').insertAdjacentHTML('beforeend', html);
    updatePermitNumbers();
    calculateTotal();
}

function removePermit(button) {
    if (document.querySelectorAll('.permit-item').length <= 1) {
        alert('Minimal harus ada 1 izin');
        return;
    }
    button.closest('.permit-item').remove();
    updatePermitNumbers();
    calculateTotal();
}

function updatePermitNumbers() {
    document.querySelectorAll('.permit-item').forEach((item, i) => {
        item.querySelector('.permit-number').textContent = i + 1;
    });
}

function updatePermitInfo(select) {
    const basePrice = select.options[select.selectedIndex].getAttribute('data-base-price');
    const priceInput = select.closest('.permit-item').querySelector('.permit-price');
    if (basePrice && parseFloat(priceInput.value) === 0) {
        priceInput.value = basePrice;
        calculateTotal();
    }
}

function calculateTotal() {
    let total = 0, totalDays = 0, count = 0;
    document.querySelectorAll('.permit-price').forEach(i => { total += parseFloat(i.value) || 0; count++; });
    document.querySelectorAll('input[name$="[estimated_days]"]').forEach(i => { totalDays = Math.max(totalDays, parseInt(i.value) || 0); });
    document.getElementById('totalCost').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalPermitsCount').textContent = count;
    document.getElementById('totalDaysEstimate').textContent = totalDays + ' hari';
}

// ── Document Functions ────────────────────────────────────────
function toggleDocumentFields(checkbox) {
    const details = checkbox.closest('.document-item').querySelector('.document-details');
    details.style.display = checkbox.checked ? 'grid' : 'none';
}

function addCustomDoc() {
    customDocIndex++;
    const html = `
        <div class="custom-doc-item border border-green-800 rounded-xl p-4" data-index="${customDocIndex}">
            <div class="flex items-center justify-between mb-3">
                <h6 class="text-white text-sm font-medium">
                    <i class="fas fa-file-alt text-green-400 mr-2"></i>Dokumen Custom #<span class="doc-number">${customDocIndex + 1}</span>
                </h6>
                <button type="button" onclick="removeCustomDoc(this)" class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input type="hidden" name="legality_documents[custom_${customDocIndex}][category]" value="other">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="${labelCls}">Nama Dokumen <span class="text-red-400">*</span></label>
                    <input type="text" name="legality_documents[custom_${customDocIndex}][name]" required placeholder="Contoh: Surat Persetujuan Tetangga, AMDAL, UKL-UPL" class="${inputCls}">
                    <p class="text-xs text-gray-500 mt-1">Berikan nama yang jelas dan spesifik</p>
                </div>
                <div>
                    <label class="${labelCls}">Status Dokumen</label>
                    <select name="legality_documents[custom_${customDocIndex}][is_available]" class="${selectCls}">
                        <option value="1">✓ Tersedia</option>
                        <option value="0" selected>✗ Belum Ada</option>
                    </select>
                </div>
                <div>
                    <label class="${labelCls}">No. Dokumen / Ref</label>
                    <input type="text" name="legality_documents[custom_${customDocIndex}][number]" placeholder="No. / Ref" class="${inputCls}">
                </div>
                <div>
                    <label class="${labelCls}">Tanggal Terbit</label>
                    <input type="date" name="legality_documents[custom_${customDocIndex}][issued_date]" class="${inputCls}">
                </div>
                <div class="sm:col-span-2">
                    <label class="${labelCls}">Catatan / Keterangan</label>
                    <textarea name="legality_documents[custom_${customDocIndex}][notes]" rows="2" placeholder="Tambahkan keterangan jika diperlukan..." class="${inputCls}"></textarea>
                </div>
            </div>
        </div>`;
    document.getElementById('customDocsContainer').insertAdjacentHTML('beforeend', html);
    updateCustomDocNumbers();
}

function removeCustomDoc(button) {
    if (confirm('Hapus dokumen custom ini?')) {
        button.closest('.custom-doc-item').remove();
        updateCustomDocNumbers();
    }
}

function updateCustomDocNumbers() {
    document.querySelectorAll('.custom-doc-item').forEach((item, i) => {
        item.querySelector('.doc-number').textContent = i + 1;
    });
}

document.addEventListener('DOMContentLoaded', () => calculateTotal());
</script>
@endpush
@endsection
