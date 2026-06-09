@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'Transparent Pricing' : 'Harga Transparan';

    $metrics      = config('landing_metrics');
    $contact      = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';

    $pageDescription = $isEn
        ? 'Three ways to work with Bizmark.ID — free DIY tools, fixed-fee consultation, or full-service managed permits. Transparent ranges. No hidden fees.'
        : 'Tiga cara bekerja dengan Bizmark.ID — alat DIY gratis, konsultasi biaya tetap, atau layanan terkelola penuh. Rentang harga transparan. Tanpa biaya tersembunyi.';

    $consultationRoute = route('landing.service-inquiry.create');
    $aiCheckerRoute    = route('landing.service-inquiry.create');
    $calculatorRoute   = route('calculator.index');
    $shpRoute          = route('polygon.shp.index');
    $pmaRoute          = route('pma.inquiry.create');

    $tiers = [
        [
            'key'      => 'diy',
            'eyebrow'  => $isEn ? 'Free forever' : 'Gratis selamanya',
            'name'     => $isEn ? 'DIY Tools' : 'Alat DIY',
            'price'    => $isEn ? 'IDR 0' : 'Rp 0',
            'priceSub' => $isEn ? 'Free, no signup' : 'Gratis, tanpa daftar',
            'tagline'  => $isEn
                ? 'Map your permits, estimate costs, and prepare files yourself.'
                : 'Petakan izin sendiri, estimasi biaya, dan siapkan berkas sendiri.',
            'features' => $isEn ? [
                'AI Permit Checker (KBLI mapping in 30s)',
                'Cost Estimator (range per service & city)',
                'SHP Polygon Maker (downloadable .shp)',
                'Knowledge base & city pages',
                'Email support during business hours',
            ] : [
                'AI Permit Checker (mapping KBLI dalam 30 detik)',
                'Estimasi Biaya (rentang per layanan & kota)',
                'SHP Polygon Maker (file .shp siap unduh)',
                'Basis pengetahuan & halaman kota',
                'Dukungan email pada jam kerja',
            ],
            'cta'      => $isEn ? 'Start free' : 'Mulai gratis',
            'ctaUrl'   => $aiCheckerRoute,
            'palette'  => 'tools', // emerald
            'highlight' => false,
        ],
        [
            'key'      => 'consult',
            'eyebrow'  => $isEn ? 'Fixed fee — most popular' : 'Biaya tetap — paling diminati',
            'name'     => $isEn ? 'Self-service Consultation' : 'Konsultasi Self-service',
            'price'    => $isEn ? 'From IDR 2.5jt' : 'Mulai Rp 2,5jt',
            'priceSub' => $isEn ? 'per session, fixed fee' : 'per sesi, biaya tetap',
            'tagline'  => $isEn
                ? 'Sit with a senior consultant. Walk away with a permit roadmap and document checklist.'
                : 'Diskusi dengan konsultan senior. Pulang membawa peta perizinan dan checklist dokumen.',
            'features' => $isEn ? [
                '60-90 min video / on-site session',
                'Permit roadmap (KBLI, sektoral, lingkungan)',
                'Document checklist & priority order',
                'Cost & timeline estimate (written)',
                'Follow-up email Q&A — 14 days',
                'Credited if you upgrade to Managed',
            ] : [
                'Sesi 60-90 menit (video / on-site)',
                'Peta perizinan (KBLI, sektoral, lingkungan)',
                'Checklist dokumen & urutan prioritas',
                'Estimasi biaya & timeline (tertulis)',
                'Tanya-jawab via email — 14 hari',
                'Dikreditkan bila upgrade ke Managed',
            ],
            'cta'      => $isEn ? 'Book a consultation' : 'Pesan konsultasi',
            'ctaUrl'   => $consultationRoute,
            'palette'  => 'gold',
            'highlight' => true,
        ],
        [
            'key'      => 'managed',
            'eyebrow'  => $isEn ? 'Custom quote' : 'Penawaran khusus',
            'name'     => $isEn ? 'Full-service Managed' : 'Layanan Terkelola Penuh',
            'price'    => $isEn ? 'Custom' : 'Custom',
            'priceSub' => $isEn ? 'IDR 15jt – 500jt+ per project' : 'Rp 15jt – 500jt+ per proyek',
            'tagline'  => $isEn
                ? 'We handle everything end-to-end — submission, agency liaison, revisions, and SLA reporting.'
                : 'Kami urus end-to-end — pengajuan, hubungan instansi, revisi, dan laporan SLA.',
            'features' => $isEn ? [
                'Dedicated senior project manager',
                'Submission & agency liaison (BKPM, OSS, DLH, etc.)',
                'Document drafting & revisions included',
                'Weekly SLA report (status per permit)',
                'Force-majeure & policy-change protection',
                'PMA-ready: BKPM, KPA, sectoral roadmap',
            ] : [
                'Manajer proyek senior terdedikasi',
                'Pengajuan & hubungan instansi (BKPM, OSS, DLH, dll.)',
                'Draf dokumen & revisi termasuk',
                'Laporan SLA mingguan (status per izin)',
                'Proteksi force-majeure & perubahan kebijakan',
                'PMA-ready: BKPM, KPA, peta izin sektoral',
            ],
            'cta'      => $isEn ? 'Request a quote' : 'Minta penawaran',
            'ctaUrl'   => $whatsappLink,
            'ctaTarget' => '_blank',
            'palette'  => 'gold-strong',
            'highlight' => false,
        ],
    ];

    // Sample range table (anonymized realistic numbers)
    $sampleRanges = [
        ['service' => 'NIB + KBLI mapping (UMKM)',          'range' => 'Rp 2,5 – 5jt',   'sla' => '3-7 hari'],
        ['service' => 'NIB + KBLI mapping (Korporat)',      'range' => 'Rp 5 – 12jt',    'sla' => '7-14 hari'],
        ['service' => 'UKL-UPL',                             'range' => 'Rp 25 – 75jt',   'sla' => '4-8 minggu'],
        ['service' => 'AMDAL (full)',                        'range' => 'Rp 150 – 500jt+', 'sla' => '4-9 bulan'],
        ['service' => 'PBG + SLF (gudang/pabrik)',          'range' => 'Rp 35 – 200jt',  'sla' => '6-16 minggu'],
        ['service' => 'PMA setup (Pendirian + KPA)',         'range' => 'Rp 50 – 150jt',  'sla' => '4-8 minggu'],
    ];
    $sampleRangesEn = [
        ['service' => 'NIB + KBLI mapping (SME)',           'range' => 'IDR 2.5 – 5M',   'sla' => '3-7 days'],
        ['service' => 'NIB + KBLI mapping (Corporate)',     'range' => 'IDR 5 – 12M',    'sla' => '7-14 days'],
        ['service' => 'UKL-UPL',                             'range' => 'IDR 25 – 75M',   'sla' => '4-8 weeks'],
        ['service' => 'AMDAL (full)',                        'range' => 'IDR 150 – 500M+', 'sla' => '4-9 months'],
        ['service' => 'PBG + SLF (warehouse/factory)',      'range' => 'IDR 35 – 200M',  'sla' => '6-16 weeks'],
        ['service' => 'PMA setup (Establishment + KPA)',    'range' => 'IDR 50 – 150M',  'sla' => '4-8 weeks'],
    ];
    $ranges = $isEn ? $sampleRangesEn : $sampleRanges;

    $faqs = $isEn ? [
        ['q' => 'Why ranges and not fixed prices?', 'a' => 'Permit fees vary by KBLI risk level, business scale, location, and current regulations. We commit to a fixed quote after a free 30-min scoping call — no surprises after engagement.'],
        ['q' => 'Are government fees included?',     'a' => 'Yes — our quotes include both consultancy fee and government PNBP. We list both line items separately on every invoice.'],
        ['q' => 'What if regulations change mid-project?', 'a' => 'You are protected. If a regulatory change extends timeline or adds permit steps, we absorb the consultancy delta. Government fee changes pass through at cost with itemization.'],
        ['q' => 'Do you offer payment terms?',      'a' => 'Yes. Standard split: 40% on engagement, 30% at midpoint milestone, 30% on completion. Larger projects (>Rp 100jt) can be split further on request.'],
    ] : [
        ['q' => 'Kenapa rentang, bukan harga tetap?', 'a' => 'Biaya izin bervariasi berdasarkan tingkat risiko KBLI, skala usaha, lokasi, dan regulasi terkini. Kami commit fixed quote setelah scoping call gratis 30 menit — tanpa kejutan setelah engagement.'],
        ['q' => 'Apakah biaya pemerintah termasuk?',   'a' => 'Ya — penawaran kami mencakup fee konsultansi dan PNBP pemerintah. Keduanya kami pisahkan jelas sebagai line item di setiap invoice.'],
        ['q' => 'Bagaimana jika regulasi berubah di tengah proyek?', 'a' => 'Anda terlindungi. Jika perubahan regulasi memperpanjang timeline atau menambah tahapan, kami serap selisih fee konsultansi. Perubahan PNBP pemerintah pass-through dengan rincian.'],
        ['q' => 'Apakah ada termin pembayaran?',       'a' => 'Ya. Split standar: 40% engagement, 30% milestone tengah, 30% completion. Proyek >Rp 100jt bisa di-split lebih panjang berdasarkan permintaan.'],
    ];
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)
@section('og_title', $pageTitle . ' — Bizmark.ID')
@section('og_description', $pageDescription)

@section('structured_data')
@php
    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'inLanguage' => $isEn ? 'en' : 'id',
        'mainEntity' => collect($faqs)->map(fn($f) => [
            '@type' => 'Question',
            'name' => $f['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $f['a'],
            ],
        ])->all(),
    ];
    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'serviceType' => 'Indonesian permit consultancy',
        'provider' => [
            '@type' => 'Organization',
            'name' => 'Bizmark.ID',
            'url' => url('/'),
        ],
        'areaServed' => 'ID',
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => $isEn ? 'Pricing tiers' : 'Tier harga',
            'itemListElement' => collect($tiers)->map(fn($t) => [
                '@type' => 'Offer',
                'name' => $t['name'],
                'description' => $t['tagline'],
                'priceCurrency' => 'IDR',
                'price' => $t['key'] === 'diy' ? '0' : ($t['key'] === 'consult' ? '2500000' : '0'),
            ])->all(),
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')

{{-- HERO — EDITORIAL --}}
<section class="section-v2 geo-motif" style="background: linear-gradient(180deg, var(--bg-raised) 0%, transparent 100%);">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'How we charge' : 'Cara kami menetapkan biaya' }}</span>
        <div class="editorial-split">
            <div>
                <h1 class="display-xl mb-6" style="font-size: clamp(2.75rem, 6.5vw, 5rem);">
                    {{ $isEn ? 'Three ways' : 'Tiga jalur' }}<br>
                    <span style="color: var(--accent);">{{ $isEn ? 'to work with us.' : 'untuk bekerja dengan kami.' }}</span>
                </h1>
                <p class="text-lg leading-relaxed text-gray-600 mb-6 max-w-xl">
                    {{ $isEn
                        ? 'Free DIY tools, fixed-fee consultation, or full-service managed permits. Honest ranges. No hidden fees.'
                        : 'Alat DIY gratis, konsultasi biaya tetap, atau layanan terkelola penuh. Rentang jujur. Tanpa biaya tersembunyi.' }}
                </p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <span class="partner-chip"><i class="fas fa-check" style="color: var(--accent);"></i> {{ $isEn ? 'PNBP itemized' : 'PNBP dirinci' }}</span>
                    <span class="partner-chip"><i class="fas fa-check" style="color: var(--accent);"></i> {{ $isEn ? 'Fixed quote after scoping' : 'Fixed quote setelah scoping' }}</span>
                    <span class="partner-chip"><i class="fas fa-check" style="color: var(--accent);"></i> {{ $isEn ? 'Regulatory change protection' : 'Proteksi perubahan regulasi' }}</span>
                </div>
            </div>
            <aside class="hidden lg:block pt-2">
                <div class="editorial-quote">
                    {{ $isEn
                        ? 'A range you can plan around. A fixed quote after we scope.'
                        : 'Rentang yang bisa Anda rencanakan. Quote tetap setelah kami scoping.' }}
                    <span class="editorial-quote__cite">{{ $isEn ? 'Bizmark Pricing Standard' : 'Standar Harga Bizmark' }}</span>
                </div>
            </aside>
        </div>
    </div>
</section>

{{-- TIERS --}}
<section class="section-v2" aria-labelledby="tiers-heading">
    <div class="container-wide">
        <h2 id="tiers-heading" class="sr-only">{{ $isEn ? 'Pricing tiers' : 'Tier harga' }}</h2>
        <div class="grid lg:grid-cols-3 gap-6 items-stretch">
            @foreach($tiers as $tier)
                @php
                    $isHighlight = $tier['highlight'];
                    $isTools = $tier['palette'] === 'tools';
                    $borderStyle = $isHighlight
                        ? 'border: 2px solid var(--accent); box-shadow: 0 14px 40px rgba(184,134,11,.18);'
                        : ($isTools ? 'border: 1px solid rgba(16,185,129,.25);' : 'border: 1px solid var(--border);');
                    $eyebrowStyle = $isTools
                        ? 'background: rgba(16,185,129,.1); color: #047857;'
                        : ($isHighlight ? 'background: var(--accent-glow); color: var(--accent-text);' : 'background: var(--surface); color: var(--text-secondary);');
                    $iconStyle = $isTools
                        ? 'background: rgba(16,185,129,.12); color: #059669;'
                        : 'background: var(--accent-glow); color: var(--accent);';
                    $ctaClass = $isTools
                        ? 'btn btn-lg w-full justify-center text-white'
                        : 'btn btn-gold btn-lg w-full justify-center';
                    $ctaInline = $isTools ? 'background: #059669;' : '';
                    $iconClass = $tier['key'] === 'diy' ? 'fa-rocket' : ($tier['key'] === 'consult' ? 'fa-handshake' : 'fa-user-tie');
                @endphp
                <article class="premium-card flex flex-col h-full relative" style="{{ $borderStyle }}">
                    @if($isHighlight)
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-[.15em] text-white whitespace-nowrap" style="background: var(--accent);">
                            {{ $isEn ? 'Most popular' : 'Paling diminati' }}
                        </div>
                    @endif

                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="{{ $iconStyle }}">
                            <i class="fas {{ $iconClass }} text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-[.15em] px-2.5 py-1 rounded-full" style="{{ $eyebrowStyle }}">
                            {{ $tier['eyebrow'] }}
                        </span>
                    </div>

                    <h3 class="font-display font-black text-2xl mb-2">{{ $tier['name'] }}</h3>
                    <p class="text-sm text-gray-600 mb-5 leading-relaxed">{{ $tier['tagline'] }}</p>

                    <div class="mb-5 pb-5" style="border-bottom: 1px solid var(--border);">
                        <div class="flex items-baseline gap-2">
                            <span class="font-display font-black text-4xl" style="color: var(--text-primary);">{{ $tier['price'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $tier['priceSub'] }}</p>
                    </div>

                    <ul class="space-y-2.5 mb-6 flex-1">
                        @foreach($tier['features'] as $feat)
                            <li class="flex items-start gap-2.5 text-sm">
                                <i class="fas fa-check-circle mt-0.5 flex-shrink-0" style="color: {{ $isTools ? '#059669' : 'var(--accent)' }};"></i>
                                <span class="text-gray-700 leading-relaxed">{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ $tier['ctaUrl'] }}"
                       @if(!empty($tier['ctaTarget'])) target="{{ $tier['ctaTarget'] }}" rel="noopener" @endif
                       class="{{ $ctaClass }}"
                       @if($ctaInline) style="{{ $ctaInline }}" @endif>
                        {{ $tier['cta'] }} <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- SAMPLE RANGE TABLE --}}
<section class="section-v2" style="background: var(--surface);" aria-labelledby="ranges-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-10">
            <span class="eyebrow mb-3">{{ $isEn ? 'Sample ranges' : 'Contoh rentang' }}</span>
            <h2 id="ranges-heading" class="display-lg mt-2 mb-3">
                {{ $isEn ? 'Realistic ballpark for common permits.' : 'Perkiraan realistis untuk izin umum.' }}
            </h2>
            <p class="text-base leading-relaxed text-gray-600">
                {{ $isEn
                    ? 'Final fees vary by KBLI risk level, scale, and location. These ranges represent typical client engagements over the past 12 months.'
                    : 'Biaya final bervariasi berdasarkan tingkat risiko KBLI, skala, dan lokasi. Rentang ini mewakili engagement klien tipikal dalam 12 bulan terakhir.' }}
            </p>
        </div>
        <div class="premium-card overflow-x-auto p-0">
            <table class="w-full text-sm">
                <thead style="background: var(--surface-2, #faf7f0); border-bottom: 1px solid var(--border);">
                    <tr>
                        <th class="text-left font-display font-bold px-5 py-4">{{ $isEn ? 'Service' : 'Layanan' }}</th>
                        <th class="text-left font-display font-bold px-5 py-4">{{ $isEn ? 'Total range (consultancy + PNBP)' : 'Rentang total (konsultansi + PNBP)' }}</th>
                        <th class="text-left font-display font-bold px-5 py-4 hidden sm:table-cell">SLA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranges as $row)
                        <tr style="border-bottom: 1px solid var(--border); {{ $loop->even ? 'background: var(--surface-warm);' : '' }}">
                            <td class="px-5 py-4 font-semibold text-gray-800">{{ $row['service'] }}</td>
                            <td class="px-5 py-4" style="color: var(--accent-text); font-weight: 600;">{{ $row['range'] }}</td>
                            <td class="px-5 py-4 text-gray-600 hidden sm:table-cell">{{ $row['sla'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-xs text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            {{ $isEn
                ? 'Disclaimer: ranges are indicative based on past 12 months. Final quote provided after free 30-min scoping call.'
                : 'Disclaimer: rentang bersifat indikatif berdasarkan 12 bulan terakhir. Penawaran final diberikan setelah scoping call gratis 30 menit.' }}
        </p>
    </div>
</section>

{{-- FAQ --}}
<section class="section-v2" aria-labelledby="pricing-faq-heading">
    <div class="container-wide max-w-3xl">
        <div class="mb-10">
            <span class="eyebrow mb-3">FAQ</span>
            <h2 id="pricing-faq-heading" class="display-lg mt-2">
                {{ $isEn ? 'Pricing questions' : 'Pertanyaan seputar harga' }}
            </h2>
        </div>
        <div class="space-y-3" x-data="{ open: [] }">
            @foreach($faqs as $i => $faq)
                <div class="premium-card p-0 overflow-hidden">
                    <button type="button"
                            @click="open.includes({{ $i }}) ? open = open.filter(x => x !== {{ $i }}) : open.push({{ $i }})"
                            :aria-expanded="open.includes({{ $i }})"
                            class="w-full flex items-center justify-between gap-4 p-5 text-left hover:bg-gray-50 transition-colors">
                        <span class="font-display font-bold text-base">{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-sm flex-shrink-0 transition-transform"
                           :class="{ 'rotate-180': open.includes({{ $i }}) }"
                           style="color: var(--accent);"></i>
                    </button>
                    <div x-show="open.includes({{ $i }})" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="px-5 pb-5 text-sm leading-relaxed text-gray-700">
                        {{ $faq['a'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- BOTTOM CTA --}}
<section class="section-premium text-center">
    <div class="container-wide max-w-3xl mx-auto">
        <h2 class="font-black mb-4 text-gray-900" style="font-size: clamp(1.5rem,3vw,2.25rem); letter-spacing: -0.02em;">
            {{ $isEn ? 'Not sure which tier fits?' : 'Belum yakin tier mana yang cocok?' }}
        </h2>
        <p class="text-lg mb-8 font-light text-gray-600">
            {{ $isEn
                ? 'Free 30-min scoping call. We will recommend the smallest tier that solves your problem.'
                : 'Scoping call gratis 30 menit. Kami rekomendasikan tier paling kecil yang menyelesaikan masalah Anda.' }}
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $consultationRoute }}" class="btn btn-gold btn-lg" style="border-radius: var(--radius-full);">
                <i class="fas fa-calendar-check"></i>
                {{ $isEn ? 'Book free scoping call' : 'Pesan scoping call gratis' }}
            </a>
            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-lg" style="border-radius: var(--radius-full);">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
        </div>
    </div>
</section>

@endsection
