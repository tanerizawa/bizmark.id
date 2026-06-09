@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');

    // Curated core services (consistent icons + real deliverables)
    $coreServices = [
        [
            'slug' => 'perizinan-lb3',
            'icon' => 'fa-recycle',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'B3 Waste Permits' : 'Izin Limbah B3',
            'desc' => $isEn ? 'TPS LB3, handling permits, manifest systems.' : 'TPS LB3, izin pengelolaan, manifest.',
            'bullets' => $isEn
                ? ['TPS LB3 permit', 'Transport manifest', 'Recovery / disposal']
                : ['Izin TPS LB3', 'Manifest angkut', 'Pemulihan / pembuangan'],
        ],
        [
            'slug' => 'amdal',
            'icon' => 'fa-leaf',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Environmental Impact' : 'Lingkungan',
            'desc' => $isEn ? 'AMDAL, UKL-UPL, SPPL, environmental audits.' : 'AMDAL, UKL-UPL, SPPL, audit lingkungan.',
            'bullets' => $isEn
                ? ['AMDAL full study', 'UKL-UPL document', 'SPPL']
                : ['Studi AMDAL lengkap', 'Dokumen UKL-UPL', 'SPPL'],
        ],
        [
            'slug' => 'pbg-slf',
            'icon' => 'fa-building',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Building Permits' : 'Perizinan Gedung',
            'desc' => $isEn ? 'PBG, SLF, IMB conversions, technical drawings.' : 'PBG, SLF, konversi IMB, gambar teknis.',
            'bullets' => $isEn
                ? ['PBG issuance', 'SLF certification', 'IMB conversion']
                : ['Penerbitan PBG', 'Sertifikasi SLF', 'Konversi IMB'],
        ],
        [
            'slug' => 'oss-nib',
            'icon' => 'fa-id-card',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Business License' : 'Izin Usaha',
            'desc' => $isEn ? 'NIB, OSS-RBA setup, sectoral business permits.' : 'NIB, setup OSS-RBA, izin sektoral.',
            'bullets' => $isEn
                ? ['NIB registration', 'OSS-RBA setup', 'SIUP · API']
                : ['Registrasi NIB', 'Setup OSS-RBA', 'SIUP · API'],
        ],
        [
            'slug' => 'pma-investasi-asing',
            'icon' => 'fa-globe-asia',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'PMA / Foreign Investment' : 'PMA / Investasi Asing',
            'desc' => $isEn ? 'BKPM setup, sectoral permits, bilingual full-service.' : 'Setup BKPM, izin sektoral, full-service bilingual.',
            'bullets' => $isEn
                ? ['BKPM registration', 'Sectoral permits', 'Bilingual support']
                : ['Registrasi BKPM', 'Izin sektoral', 'Dukungan bilingual'],
        ],
        [
            'slug' => 'izin-operasional',
            'icon' => 'fa-industry',
            'color' => 'var(--accent)',
            'title' => $isEn ? 'Operational Permits' : 'Operasional',
            'desc' => $isEn ? 'Sector-specific permits: industry, logistics, energy.' : 'Izin sektoral: industri, logistik, energi.',
            'bullets' => $isEn
                ? ['Industrial permits', 'Logistics · K3', 'Compliance audit']
                : ['Izin industri', 'Logistik · K3', 'Audit kepatuhan'],
        ],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     SERVICES GRID — 6 core, symmetrical 3×2
──────────────────────────────────────────────── --}}
<section class="section-v2" aria-labelledby="services-heading" id="services">
    <div class="container-wide">
        <div class="services-intro-row flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-5" data-aos="fade-up">
            <div class="services-intro-copy max-w-2xl">
                <span class="eyebrow block mb-3">{{ $isEn ? 'Core Services' : 'Layanan Utama' }}</span>
                <h2 id="services-heading" class="display-md mb-2">
                    {{ $isEn ? 'Fully managed permits. Expert-backed.' : 'Perizinan dikelola penuh. Dijamin para ahli.' }}
                </h2>
                <p class="text-sm leading-relaxed text-gray-600">
                    {{ $isEn
                        ? 'Six permit categories covered. One dedicated team assigned to your project. Clear SLA commitments from day one.'
                        : 'Enam kategori perizinan tersedia. Satu tim khusus untuk setiap proyek Anda. Komitmen SLA yang jelas sejak hari pertama.' }}
                </p>
            </div>
            <div class="services-intro-visual hidden md:block flex-shrink-0 w-64 lg:w-72" aria-hidden="true">
                <img src="{{ asset('images/illustrations/permits-stack.svg') }}"
                     alt=""
                     loading="lazy"
                     class="w-full h-auto select-none pointer-events-none opacity-[.96]"
                     draggable="false">
            </div>
        </div>

        <div class="services-grid grid md:grid-cols-2 lg:grid-cols-3 gap-5 grid-equal">
            @foreach($coreServices as $idx => $svc)
                <a href="{{ $isEn ? route('services.show.en', $svc['slug']) : route('services.show.id', $svc['slug']) }}"
                   class="platform-card services-card group flex flex-col no-underline"
                   data-aos="fade-up" data-aos-delay="{{ ($idx % 3) * 100 }}">
                    <div class="platform-card__head">
                        <span class="platform-card__num">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad(count($coreServices), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="platform-card__status">{{ $isEn ? 'Live SLA' : 'SLA aktif' }}</span>
                    </div>
                    <i class="fas {{ $svc['icon'] }} text-2xl mb-3" style="color: {{ $svc['color'] }};" aria-hidden="true"></i>
                    <h3 class="platform-card__title">{{ $svc['title'] }}</h3>
                    <p class="platform-card__body mb-3">{{ $svc['desc'] }}</p>
                    <ul class="space-y-1.5 mb-4 flex-1">
                        @foreach($svc['bullets'] as $b)
                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                <i class="fas fa-check text-[10px] mt-1.5 flex-shrink-0" style="color: var(--accent);"></i>
                                <span>{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <div class="platform-card__meta">
                        <span>{{ $isEn ? 'Service detail' : 'Detail layanan' }}</span>
                        <span class="font-semibold" style="color: var(--accent);">{{ $isEn ? 'Learn more' : 'Pelajari' }} <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i></span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ $servicesIndexRoute }}" class="btn btn-outline-primary">
                <span>{{ $isEn ? 'View all 20+ services' : 'Lihat semua 20+ layanan' }}</span>
                <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>
