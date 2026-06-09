@extends('layouts.admin')

@section('title', 'Parafrase Dokumen dengan AI - ' . $project->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <a href="{{ route('projects.show', $project) }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold" style="color: #FFFFFF;">
                <i class="fas fa-robot mr-3 text-apple-blue-dark"></i>Parafrase Dokumen dengan AI
            </h1>
            <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">{{ $project->name }}</p>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="card-elevated rounded-apple-lg p-4 mb-6" style="background: rgba(0, 122, 255, 0.1); border-left: 4px solid #007AFF;">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-apple-blue-dark text-xl mr-3 mt-1"></i>
            <div style="color: rgba(235, 235, 245, 0.9);">
                <p class="font-medium mb-2">Cara Kerja AI Paraphrase:</p>
                <ul class="text-sm space-y-1" style="color: rgba(235, 235, 245, 0.7);">
                    <li>1. Pilih template dokumen perizinan (Pertek BPN, UKL-UPL, AMDAL, dll)</li>
                    <li>2. AI akan membaca template dan menyesuaikan dengan data proyek Anda</li>
                    <li>3. Proses berjalan di background (5-15 menit untuk dokumen 50+ halaman)</li>
                    <li>4. Hasil draft dapat diedit dan disetujui sebelum diexport</li>
                    <li>5. Menggunakan model AI via OpenRouter: <strong>{{ config('services.openrouter.model') }}</strong></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form action="{{ route('ai.paraphrase.store', $project) }}" method="POST" id="paraphraseForm">
        @csrf

        <div class="space-y-6">
            <!-- Template Selection -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-file-alt mr-2 text-apple-blue-dark"></i>Pilih Template Dokumen
                </h3>

                @if($templates->isEmpty())
                    <div class="text-center py-8" style="color: rgba(235, 235, 245, 0.6);">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Belum ada template tersedia</p>
                        <p class="text-sm mt-2">Hubungi administrator untuk upload template dokumen</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($templates as $template)
                        <label class="template-card cursor-pointer">
                            <input type="radio" name="template_id" value="{{ $template->id }}" 
                                   class="hidden template-radio" required
                                   data-required-fields="{{ json_encode($template->required_fields ?? []) }}">
                            
                            <div class="p-4 rounded-lg border-2 transition-all"
                                 style="background: rgba(28, 28, 30, 0.6); border-color: rgba(142, 142, 147, 0.3);">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                                            <h4 class="font-semibold text-lg" style="color: #FFFFFF;">
                                                {{ $template->name }}
                                            </h4>
                                        </div>
                                        
                                        <div class="flex flex-wrap gap-3 mb-3">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium"
                                                  style="background: rgba(0, 122, 255, 0.2); color: #007AFF;">
                                                {{ strtoupper(str_replace('_', ' ', $template->permit_type)) }}
                                            </span>
                                            @if($template->page_count)
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                                <i class="fas fa-file-alt mr-1"></i>{{ $template->page_count }} halaman
                                            </span>
                                            @endif
                                            <span class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                                <i class="fas fa-hdd mr-1"></i>{{ $template->file_size_human }}
                                            </span>
                                        </div>

                                        @if($template->description)
                                        <p class="text-sm mb-3" style="color: rgba(235, 235, 245, 0.7);">
                                            {{ $template->description }}
                                        </p>
                                        @endif

                                        @if($template->required_fields && count($template->required_fields) > 0)
                                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                            <strong>Field yang diperlukan:</strong>
                                            {{ implode(', ', array_map(fn($f) => ucwords(str_replace('_', ' ', $f)), $template->required_fields)) }}
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="ml-4">
                                        <i class="fas fa-check-circle text-2xl check-icon hidden" 
                                           style="color: #34C759;"></i>
                                        <i class="fas fa-circle text-2xl circle-icon" 
                                           style="color: rgba(142, 142, 147, 0.3);"></i>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Additional Context (Optional) -->
            <div class="card-elevated rounded-apple-lg p-6" id="contextSection" style="display: none;">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-database mr-2 text-apple-blue-dark"></i>Konteks Tambahan (Opsional)
                </h3>
                
                <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.7);">
                    Data berikut akan digunakan oleh AI untuk menyesuaikan dokumen. Data proyek sudah otomatis terisi.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Luas Tanah (m²)
                        </label>
                        <input type="text" name="additional_context[land_area]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: 1000">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Luas Bangunan (m²)
                        </label>
                        <input type="text" name="additional_context[building_area]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: 750">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Nomor Sertifikat Tanah
                        </label>
                        <input type="text" name="additional_context[land_certificate]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: SHM No. 12345">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Jenis Usaha
                        </label>
                        <input type="text" name="additional_context[business_type]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: Perhotelan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Provinsi
                        </label>
                        <input type="text" name="additional_context[province]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: DKI Jakarta">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Kabupaten/Kota
                        </label>
                        <input type="text" name="additional_context[regency]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: Jakarta Selatan">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Kecamatan
                        </label>
                        <input type="text" name="additional_context[district]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: Kebayoran Baru">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Instansi Tujuan
                        </label>
                        <input type="text" name="additional_context[institution]" 
                               class="input-dark w-full px-3 py-2 rounded-lg"
                               placeholder="contoh: BPN Kota Jakarta Selatan">
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-6 py-3 rounded-lg font-medium transition-colors"
                   style="background: rgba(142, 142, 147, 0.2); color: rgba(235, 235, 245, 0.8);">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>

                <button type="submit" id="submitBtn"
                        class="px-6 py-3 rounded-lg font-medium transition-all"
                        style="background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%); color: #FFFFFF;"
                        disabled>
                    <i class="fas fa-magic mr-2"></i>
                    <span id="btnText">Pilih Template Terlebih Dahulu</span>
                </button>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.template-card input:checked ~ div {
    background: rgba(0, 122, 255, 0.15) !important;
    border-color: #007AFF !important;
}

.template-card input:checked ~ div .check-icon {
    display: block !important;
}

.template-card input:checked ~ div .circle-icon {
    display: none !important;
}

.template-card:hover div {
    border-color: rgba(0, 122, 255, 0.5) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('paraphraseForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const contextSection = document.getElementById('contextSection');
    const templateRadios = document.querySelectorAll('.template-radio');

    templateRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                btnText.textContent = 'Proses Dokumen dengan AI';
                contextSection.style.display = 'block';
            }
        });
    });

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memulai Proses AI...';
        btnText.textContent = 'Mohon tunggu...';
    });
});
</script>
@endpush
@endsection
