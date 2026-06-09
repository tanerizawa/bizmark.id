@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');
    // Specific service page routes for each segment
    $segRoutes = [
        'umkm' => $isEn ? route('services.show.en', 'oss-nib')              : route('services.show.id', 'oss-nib'),
        'corp' => $isEn ? route('services.show.en', 'amdal')                 : route('services.show.id', 'amdal'),
        'pma'  => $isEn ? route('services.show.en', 'pma-investasi-asing')   : route('services.show.id', 'pma-investasi-asing'),
    ];
@endphp

{{-- ────────────────────────────────────────────────
     SEGMENTATION — Self-selection by business type
     Hick's Law: reduce cognitive load via explicit paths
──────────────────────────────────────────────── --}}
<section class="section-v2-sm section-premium geo-motif" aria-labelledby="segment-heading">
    <div class="container-wide">
        <div class="max-w-2xl mx-auto mb-8 text-center">
            <span class="eyebrow block mb-3">{{ $isEn ? 'Solutions by business type' : 'Solusi per Jenis Usaha' }}</span>
            <h2 id="segment-heading" class="display-md mb-3">
                {{ $isEn ? 'Which type of business are you?' : 'Usaha Anda termasuk yang mana?' }}
            </h2>
            <p class="text-sm text-gray-600">
                {{ $isEn
                    ? 'Every solution we offer is structured around your business scale, sector, and regulatory obligations.'
                    : 'Setiap solusi yang kami tawarkan dirancang sesuai skala usaha, bidang industri, dan kewajiban regulasi Anda.' }}
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-5 max-w-5xl mx-auto items-stretch">
            {{-- MSME --}}
            <a href="{{ $segRoutes['umkm'] }}" class="platform-card group no-underline flex flex-col">
                <div class="platform-card__head">
                    <span class="platform-card__num">01 / 03</span>
                    <span class="platform-card__status">UMKM</span>
                </div>
                <i class="fas fa-store text-2xl mb-3" style="color: var(--accent);" aria-hidden="true"></i>
                <h3 class="platform-card__title">{{ $isEn ? 'MSME / Startup' : 'UMKM / Startup' }}</h3>
                <p class="platform-card__body flex-1">
                    {{ $isEn
                        ? 'NIB registration, OSS-RBA setup, basic environmental documents, and initial business permits.'
                        : 'Registrasi NIB, pengaturan OSS-RBA, dokumen lingkungan dasar, serta izin usaha tahap awal.' }}
                </p>
                <div class="platform-card__meta">
                    <span>{{ $isEn ? 'NIB · OSS-RBA' : 'NIB · OSS-RBA' }}</span>
                    <span class="font-semibold" style="color: var(--accent);">{{ $isEn ? 'Explore' : 'Telusuri' }} <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i></span>
                </div>
            </a>

            {{-- Corporate (featured) --}}
            <a href="{{ $segRoutes['corp'] }}" class="platform-card group no-underline flex flex-col" style="border-color: rgba(var(--accent-rgb), .35); box-shadow: 0 12px 32px -16px rgba(var(--accent-rgb), .25);">
                <div class="platform-card__head">
                    <span class="platform-card__num" style="color: var(--accent);">02 / 03 · {{ $isEn ? 'POPULAR' : 'POPULER' }}</span>
                    <span class="platform-card__status">{{ $isEn ? 'Most chosen' : 'Paling diminati' }}</span>
                </div>
                <i class="fas fa-industry text-2xl mb-3" style="color: var(--accent);" aria-hidden="true"></i>
                <h3 class="platform-card__title">{{ $isEn ? 'Mid-to-Large Corporation' : 'Korporasi Menengah & Besar' }}</h3>
                <p class="platform-card__body flex-1">
                    {{ $isEn
                        ? 'AMDAL, B3 waste management, UKL-UPL, PBG, SLF — full environmental and building compliance.'
                        : 'AMDAL, pengelolaan limbah B3, UKL-UPL, PBG, SLF — kepatuhan lingkungan dan bangunan secara menyeluruh.' }}
                </p>
                <div class="platform-card__meta">
                    <span>AMDAL · PBG · SLF</span>
                    <span class="font-semibold" style="color: var(--accent);">{{ $isEn ? 'Explore' : 'Telusuri' }} <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i></span>
                </div>
            </a>

            {{-- PMA --}}
            <a href="{{ $segRoutes['pma'] }}" class="platform-card group no-underline flex flex-col">
                <div class="platform-card__head">
                    <span class="platform-card__num">03 / 03</span>
                    <span class="platform-card__status">PMA</span>
                </div>
                <i class="fas fa-globe-asia text-2xl mb-3" style="color: var(--tools, var(--accent));" aria-hidden="true"></i>
                <h3 class="platform-card__title">{{ $isEn ? 'Foreign-Invested Company (PMA)' : 'Perusahaan PMA / Investasi Asing' }}</h3>
                <p class="platform-card__body flex-1">
                    {{ $isEn
                        ? 'BKPM registration, sector-specific permits, and complete bilingual guidance throughout the entire process.'
                        : 'Pendaftaran BKPM, pengurusan izin sektoral, dan pendampingan bilingual di setiap tahap proses.' }}
                </p>
                <div class="platform-card__meta">
                    <span>BKPM · KPA · Sektoral</span>
                    <span class="font-semibold" style="color: var(--accent);">{{ $isEn ? 'Explore' : 'Telusuri' }} <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i></span>
                </div>
            </a>
        </div>
    </div>
</section>
