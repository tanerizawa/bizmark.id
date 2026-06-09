@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $testimonialData = collect(config('landing.testimonials', []));

    $fallback = [
        [
            'quote' => $isEn
                ? 'Bizmark\'s team is outstanding. Our AMDAL process — typically a multi-month ordeal — was completed on time with thorough documentation. A partner we genuinely trust.'
                : 'Tim Bizmark sangat profesional. Proses AMDAL yang biasanya memakan berbulan-bulan berhasil diselesaikan tepat waktu dengan dokumentasi yang lengkap. Mitra yang benar-benar kami andalkan.',
            'name' => $isEn ? 'Director of Operations' : 'Direktur Operasional',
            'company' => $isEn ? 'Manufacturing Company · West Java' : 'Perusahaan Manufaktur · Jawa Barat',
            'icon' => 'fa-industry',
            'color' => 'var(--accent)',
        ],
        [
            'quote' => $isEn
                ? 'Our NIB and OSS-RBA were secured faster than we expected. Bizmark clearly understands the regulatory landscape, and they kept us informed every step of the way.'
                : 'NIB dan OSS-RBA kami selesai lebih cepat dari yang kami bayangkan. Bizmark benar-benar paham seluk-beluk regulasi dan selalu memberi kabar di setiap tahapan.',
            'name' => 'General Manager',
            'company' => $isEn ? 'Logistics Company · Jakarta' : 'Perusahaan Logistik · Jakarta',
            'icon' => 'fa-truck',
            'color' => 'var(--accent)',
        ],
        [
            'quote' => $isEn
                ? 'As a foreign-invested company, Bizmark\'s bilingual service was indispensable. Every sectoral permit was handled smoothly, and the weekly progress reports kept our board informed and confident.'
                : 'Sebagai perusahaan PMA, layanan bilingual Bizmark benar-benar membantu. Semua izin sektoral terurus dengan lancar, dan laporan kemajuan mingguan membuat manajemen kami selalu tenang.',
            'name' => 'Country Manager',
            'company' => $isEn ? 'PMA Energy Sector · Jakarta' : 'PMA Sektor Energi · Jakarta',
            'icon' => 'fa-globe-asia',
            'color' => 'var(--accent)',
        ],
    ];

    // EN page: DB testimonials are ID-only — always use bilingual fallback on English
    $items = (!$isEn && $testimonialData->count() > 0) ? $testimonialData->take(3)->values() : collect($fallback);
@endphp

{{-- ────────────────────────────────────────────────
     CASE STUDIES — 3 testimonials editorial
──────────────────────────────────────────────── --}}
<section class="section-v2 section-premium" aria-labelledby="testimonials-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-5">
            <span class="eyebrow block mb-3">{{ $isEn ? 'Client Stories' : 'Testimoni Klien' }}</span>
            <h2 id="testimonials-heading" class="display-md mb-2">
                {{ $isEn ? 'Trusted by industry leaders.' : 'Dipercaya berbagai industri.' }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-600">
                {{ $isEn
                    ? 'Real results from manufacturers, logistics operators, and foreign-invested companies we have served across Indonesia.'
                    : 'Hasil nyata dari klien kami di bidang manufaktur, logistik, dan perusahaan PMA di seluruh Indonesia.' }}
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 grid-equal">
            @foreach($items as $t)
                @php
                    $quote = is_array($t) ? ($t['text'] ?? $t['quote'] ?? '') : ($t->text ?? $t->quote ?? '');
                    $name = is_array($t) ? ($t['name'] ?? '') : ($t->name ?? '');
                    $position = is_array($t) ? ($t['position'] ?? '') : ($t->position ?? '');
                    $company = is_array($t) ? ($t['company'] ?? '') : ($t->company ?? '');
                    $displayCompany = trim(implode(' · ', array_filter([$position, $company])));
                    $icon = is_array($t) ? ($t['icon'] ?? 'fa-building') : 'fa-building';
                @endphp
                <figure class="quote-card flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ $loop->index * 120 }}">
                    <span aria-hidden="true" class="absolute top-4 right-5 font-display text-5xl leading-none text-gray-200 opacity-70 pointer-events-none select-none">&ldquo;</span>

                    {{-- Header: stars + verified badge --}}
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <div class="flex items-center gap-1" aria-label="5 of 5 stars">
                            @for($i = 0; $i < 5; $i++)
                                <i class="fas fa-star text-xs text-amber-400"></i>
                            @endfor
                        </div>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider bg-green-50 text-green-700 border border-green-200">
                            <i class="fas fa-check-circle text-[9px]"></i>
                            {{ $isEn ? 'Verified Client' : 'Klien Terverifikasi' }}
                        </span>
                    </div>

                    <blockquote class="text-sm leading-relaxed font-normal italic flex-1 mb-4 text-gray-600">
                        {{ $quote }}
                    </blockquote>

                    <figcaption class="flex items-center gap-3 pt-5 mt-5 border-t border-gray-200">
                        <span class="editorial-icon-badge is-circle flex-shrink-0" style="width: 2.75rem; height: 2.75rem;">
                            <i class="fas {{ $icon }} icon-md" aria-hidden="true"></i>
                        </span>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-gray-900">{{ $name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ $displayCompany ?: $company }}</div>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <p class="text-center text-xs mt-8 text-gray-400">
            {{ $isEn
                ? '* Detailed case studies are available under NDA — contact our team to schedule a confidential briefing.'
                : '* Studi kasus lengkap tersedia melalui NDA — hubungi tim kami untuk mengatur sesi tinjauan bersama.' }}
        </p>
    </div>
</section>
