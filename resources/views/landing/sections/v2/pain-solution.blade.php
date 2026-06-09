@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $primaryCtaRoute = route('landing.service-inquiry.create');

    $items = [
        [
            'pain' => $isEn
                ? 'SHP files rejected by OSS-RBA — wrong format or coordinate projection.'
                : 'File SHP ditolak OSS-RBA karena format berkas atau sistem proyeksi tidak sesuai.',
            'solve' => $isEn
                ? 'Generate an OSS-RBA compliant SHP file in minutes using our interactive polygon tool.'
                : 'Buat file SHP standar OSS-RBA dalam hitungan menit menggunakan alat pemetaan interaktif kami.',
            'cta'  => $isEn ? 'View OSS-NIB service' : 'Lihat layanan OSS-NIB',
            'href' => $isEn ? route('services.show.en', 'oss-nib') : route('services.show.id', 'oss-nib'),
            'icon' => 'fa-id-card',
        ],
        [
            'pain' => $isEn
                ? 'You don\'t know which permits your business actually needs.'
                : 'Bingung izin apa saja yang benar-benar dibutuhkan oleh usaha Anda?',
            'solve' => $isEn
                ? 'Our AI maps 1,000+ KBLI codes to your exact permit requirements in seconds — free.'
                : 'AI kami memetakan lebih dari 1.000 kode KBLI ke kebutuhan izin spesifik usaha Anda — langsung dan gratis.',
            'cta'  => $isEn ? 'Check my permits — free' : 'Cek kebutuhan izin saya',
            'href' => $primaryCtaRoute,
            'icon' => 'fa-robot',
        ],
        [
            'pain' => $isEn
                ? 'Months go by with no clear update on your permit status.'
                : 'Berbulan-bulan berlalu tanpa ada kejelasan mengenai perkembangan izin Anda.',
            'solve' => $isEn
                ? 'Weekly SLA reports and field follow-through, with milestone alerts the moment a permit advances.'
                : 'Laporan perkembangan setiap minggu disertai tindak lanjut lapangan, lengkap dengan notifikasi setiap kali izin maju ke tahap berikutnya.',
            'cta'  => $isEn ? 'See how our SLA works' : 'Pelajari cara kerja SLA kami',
            'href' => $isEn ? route('process.en') : route('process.id'),
            'icon' => 'fa-clock',
        ],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     PAIN → SOLUTION — loss aversion before commit
──────────────────────────────────────────────── --}}
<section class="section-v2 section-premium" aria-labelledby="pain-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-5" data-aos="fade-up">
            <span class="eyebrow block mb-3">{{ $isEn ? 'Common Problems' : 'Masalah Umum' }}</span>
            <h2 id="pain-heading" class="display-md mb-0">
                {{ $isEn ? 'We remove the roadblocks slowing you down.' : 'Kami atasi hambatan yang memperlambat usaha Anda.' }}
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6 grid-equal">
            @foreach($items as $idx => $it)
                <article class="premium-card flex flex-col h-full" data-aos="fade-up" data-aos-delay="{{ $idx * 120 }}">
                    {{-- Problem --}}
                    <div class="status-badge is-danger mb-3">
                        <span class="status-dot">
                            <i class="fas fa-times text-xs" aria-hidden="true"></i>
                        </span>
                        <span>{{ $isEn ? 'Problem' : 'Masalah' }}</span>
                    </div>
                    <p class="font-semibold text-base leading-snug mb-6 text-gray-900">
                        {{ $it['pain'] }}
                    </p>

                    {{-- Divider arrow --}}
                    <div class="flex items-center gap-3 mb-6" aria-hidden="true">
                        <span class="flex-1 h-px bg-gray-200"></span>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-500/15 text-amber-600">
                            <i class="fas fa-arrow-down text-xs"></i>
                        </span>
                        <span class="flex-1 h-px bg-gray-200"></span>
                    </div>

                    {{-- Bizmark solution --}}
                    <div class="status-badge is-success mb-3">
                        <span class="status-dot">
                            <i class="fas fa-check text-xs" aria-hidden="true"></i>
                        </span>
                        <span>Bizmark</span>
                    </div>
                    <p class="text-sm leading-relaxed flex-1 text-gray-600">
                        {{ $it['solve'] }}
                    </p>

                    <a href="{{ $it['href'] }}"
                       class="mt-5 inline-flex items-center gap-2 text-sm font-semibold group/cta text-amber-600 no-underline">
                        <i class="fas {{ $it['icon'] }} text-xs" aria-hidden="true"></i>
                        <span>{{ $it['cta'] }}</span>
                        <i class="fas fa-arrow-right text-xs transition-transform group-hover/cta:translate-x-1" aria-hidden="true"></i>
                    </a>
                </article>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ $primaryCtaRoute }}" class="btn btn-primary btn-lg">
                <i class="fas fa-robot" aria-hidden="true"></i>
                <span>{{ $isEn ? 'Get my free permit analysis' : 'Cek kebutuhan perizinan saya — gratis' }}</span>
            </a>
        </div>
    </div>
</section>
