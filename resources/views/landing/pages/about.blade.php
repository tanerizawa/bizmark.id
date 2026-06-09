@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'About Us' : 'Tentang Kami';

    // Stats dari single source of truth
    $metrics        = config('landing_metrics');
    $stats          = $metrics['stats'] ?? [];
    $expYears       = $metrics['experience']['years'] ?? ((int) date('Y') - 2014);
    $clientsActive  = $stats['clients_active_label'] ?? null;
    $permitsIssued  = $stats['permits_issued_label'] ?? null;
    $slaOntime      = $stats['sla_ontime_label'] ?? null;

    $pageDescription = $isEn
        ? 'Bizmark.ID — ' . $expYears . '+ years of permit consultancy for Indonesian manufacturing, infrastructure, and foreign-investment businesses. Bilingual ID/EN, nationwide coverage.'
        : 'Bizmark.ID — ' . $expYears . '+ tahun konsultansi perizinan untuk bisnis manufaktur, infrastruktur, dan PMA di Indonesia. Bilingual ID/EN, cakupan nasional.';

    $contact = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');
    $servicesIndexRoute = $isEn ? route('services.index.en') : route('services.index.id');

    $timeline = [
        ['year' => '2014', 'title' => $isEn ? 'Founded in Karawang' : 'Didirikan di Karawang', 'desc' => $isEn ? 'Started as a specialist for manufacturing environmental permits.' : 'Dimulai sebagai spesialis perizinan lingkungan manufaktur.'],
        ['year' => '2017', 'title' => $isEn ? 'AMDAL practice expanded' : 'Praktik AMDAL diperluas', 'desc' => $isEn ? 'Added full AMDAL, UKL-UPL studies across West Java.' : 'Menambahkan studi AMDAL, UKL-UPL lengkap di Jawa Barat.'],
        ['year' => '2020', 'title' => $isEn ? 'Digital transformation: OSS-RBA' : 'Transformasi digital: OSS-RBA', 'desc' => $isEn ? 'Among the first consultants to offer integrated digital permit tracking for clients.' : 'Menjadi salah satu konsultan pertama yang menawarkan pelacakan perizinan digital terintegrasi bagi klien.'],
        ['year' => '2024', 'title' => $isEn ? 'AI-powered tools launched' : 'Peluncuran alat berbasis AI', 'desc' => $isEn ? 'AI permit checker, cost estimator, and SHP maker — free for all users.' : 'Cek perizinan AI, estimasi biaya, dan pembuat SHP — tersedia gratis untuk semua pengguna.'],
        ['year' => (string) date('Y'), 'title' => $isEn ? 'Active across multiple sectors' : 'Aktif di berbagai sektor', 'desc' => $isEn ? 'Manufacturing, logistics, energy, and PMA clients across Indonesia.' : 'Klien manufaktur, logistik, energi, dan PMA di seluruh Indonesia.'],
    ];

    $values = [
        ['icon' => 'fa-shield-halved', 'title' => $isEn ? 'Transparent by default' : 'Transparan sejak awal', 'desc' => $isEn ? 'Every milestone, cost, and timeline documented. No surprises.' : 'Setiap tahap, biaya, dan timeline didokumentasikan. Tanpa kejutan.'],
        ['icon' => 'fa-compass',       'title' => $isEn ? 'Regulatory-first' : 'Regulasi adalah peta', 'desc' => $isEn ? 'Our team tracks every KBLI and policy change in-house.' : 'Tim kami mengikuti setiap perubahan KBLI dan kebijakan secara internal.'],
        ['icon' => 'fa-users',         'title' => $isEn ? 'One team, one owner' : 'Satu tim, satu penanggung jawab', 'desc' => $isEn ? 'Dedicated project manager per project — no handoff confusion.' : 'Manajer proyek khusus per klien — tanpa lempar-lemparan tanggung jawab.'],
        ['icon' => 'fa-clock',         'title' => $isEn ? 'Built on experience' : 'Dibangun dari pengalaman', 'desc' => $isEn ? 'Operating since 2014 across manufacturing, AMDAL, and PMA cases.' : 'Beroperasi sejak 2014 di sektor manufaktur, AMDAL, dan PMA.'],
    ];

    $certifications = [
        ['icon' => 'fa-clock',          'label' => $expYears . '+ ' . ($isEn ? 'Years experience' : 'Tahun pengalaman')],
        ['icon' => 'fa-shield-halved',  'label' => $isEn ? 'OSS-RBA Familiar' : 'Familiar OSS-RBA'],
        ['icon' => 'fa-globe',          'label' => 'Bilingual EN / ID'],
        ['icon' => 'fa-map-marked-alt', 'label' => $isEn ? 'Nationwide coverage' : 'Cakupan se-Indonesia'],
    ];
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)
@section('og_title', $pageTitle . ' — Bizmark.ID')
@section('og_description', $pageDescription)

@section('structured_data')
@php
    $aboutSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'AboutPage',
        'name' => $pageTitle,
        'description' => $pageDescription,
        'inLanguage' => $isEn ? 'en' : 'id',
        'mainEntity' => [
            '@type' => 'Organization',
            'name' => 'Bizmark.ID',
            'legalName' => 'PT Cangah Pajaratan Mandiri',
            'url' => url('/'),
            'logo' => url('/images/logo.png'),
            'foundingDate' => '2014',
            'description' => $pageDescription,
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'ID',
                'addressRegion' => 'West Java',
                'addressLocality' => 'Karawang',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'url' => $whatsappLink,
                'availableLanguage' => ['id', 'en'],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($aboutSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')

{{-- HERO — EDITORIAL --}}
<section class="section-v2 geo-motif bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'About Bizmark' : 'Tentang Bizmark' }}</span>

        <div class="editorial-split">
            <div>
                <h1 class="display-xl mb-6" style="font-size: clamp(2.75rem, 6.5vw, 5rem);">
                    {{ $isEn
                        ? 'A permit practice'
                        : 'Praktik perizinan' }}<br>
                    <span style="color: var(--accent);">{{ $isEn ? 'built on transparency.' : 'dibangun atas transparansi.' }}</span>
                </h1>
                <p class="text-xl leading-relaxed max-w-2xl text-gray-600">
                    {{ $isEn
                        ? 'Bizmark.ID (PT Cangah Pajaratan Mandiri) is a specialist permit consultancy serving manufacturing, infrastructure, energy, and foreign-investment clients across Indonesia.'
                        : 'Bizmark.ID (PT Cangah Pajaratan Mandiri) adalah konsultan perizinan spesialis yang melayani klien manufaktur, infrastruktur, energi, dan PMA di seluruh Indonesia.' }}
                </p>
            </div>

            <aside class="hidden lg:block pt-2">
                <div class="editorial-quote">
                    {{ $isEn
                        ? 'Every SLA, cost, and timeline — written down before the work begins.'
                        : 'Setiap SLA, biaya, dan timeline — tertulis sebelum pekerjaan dimulai.' }}
                    <span class="editorial-quote__cite">{{ $isEn ? 'Bizmark Operating Principle' : 'Prinsip Kerja Bizmark' }}</span>
                </div>
            </aside>
        </div>

        {{-- Editorial number grid — audit Mei 2026: hanya angka terverifikasi --}}
        <div class="editorial-number-grid mt-16">
            <div class="editorial-number">
                <div class="editorial-number__value">{{ $expYears }}<span class="editorial-number__suffix">y</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Years operating' : 'Tahun beroperasi' }}</strong>{{ $isEn ? 'Founded Karawang, 2014' : 'Berdiri di Karawang, 2014' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value">2014<span class="editorial-number__suffix"></span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Founded' : 'Berdiri' }}</strong>{{ $isEn ? 'Manufacturing-permit specialist' : 'Spesialis perizinan manufaktur' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value"><span class="editorial-number__suffix" style="font-size:1em;letter-spacing:0;">ID/EN</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Bilingual delivery' : 'Layanan bilingual' }}</strong>{{ $isEn ? 'Reports & contracts in both languages' : 'Laporan & kontrak dua bahasa' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value"><span class="editorial-number__suffix" style="font-size:1em;letter-spacing:0;">PMA</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Foreign-investment ready' : 'Siap investasi asing' }}</strong>{{ $isEn ? 'PMA, joint-venture, OSS-RBA' : 'PMA, joint venture, OSS-RBA' }}</div>
            </div>
        </div>
    </div>
</section>

{{-- MISSION / STORY — EDITORIAL SPLIT --}}
<section class="section-v2" aria-labelledby="story-heading">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'Our Story' : 'Cerita Kami' }}</span>

        <div class="editorial-split">
            <div>
                <h2 id="story-heading" class="display-lg mb-6">
                    {{ $isEn ? 'Built to tackle Indonesian regulatory complexity.' : 'Dibangun untuk mengatasi kerumitan regulasi Indonesia.' }}
                </h2>
                <div class="text-lg leading-relaxed space-y-5 text-gray-600 max-w-2xl">
                    <p>
                        {{ $isEn
                            ? 'We started in 2014 because Indonesian permit regulations change faster than most in-house teams can keep up with. Factory deadlines do not wait for anyone.'
                            : 'Bizmark hadir pada 2014 karena regulasi perizinan Indonesia berubah lebih cepat dari yang mampu diikuti oleh kebanyakan tim internal. Tenggat waktu di pabrik tidak bisa menunggu.' }}
                    </p>
                    <p>
                        {{ $isEn
                            ? 'Today, we serve clients across manufacturing, logistics, energy, and PMA sectors with a single promise: every permit, every milestone, every report — fully documented and transparently delivered.'
                            : 'Kini, kami melayani klien di sektor manufaktur, logistik, energi, dan PMA dengan satu janji: setiap izin, setiap tahap, setiap laporan — terdokumentasi lengkap dan disampaikan transparan.' }}
                    </p>
                </div>
            </div>
            <aside>
                {{-- KBLI tree mini-viz: visual signature --}}
                <div class="kbli-tree" aria-hidden="true">
                    <svg class="kbli-tree__svg" viewBox="0 0 320 240" xmlns="http://www.w3.org/2000/svg">
                        {{-- root --}}
                        <circle class="kbli-tree__node" cx="160" cy="30" r="22"/>
                        <text class="kbli-tree__label kbli-tree__label--root" x="160" y="34" text-anchor="middle">KBLI</text>
                        {{-- lines --}}
                        <path class="kbli-tree__line" d="M160 52 Q 160 75 60 100"/>
                        <path class="kbli-tree__line" d="M160 52 L 160 100"/>
                        <path class="kbli-tree__line" d="M160 52 Q 160 75 260 100"/>
                        {{-- mid --}}
                        <circle class="kbli-tree__node" cx="60" cy="110" r="16"/>
                        <text class="kbli-tree__label" x="60" y="114" text-anchor="middle">C</text>
                        <circle class="kbli-tree__node" cx="160" cy="110" r="16"/>
                        <text class="kbli-tree__label" x="160" y="114" text-anchor="middle">F</text>
                        <circle class="kbli-tree__node" cx="260" cy="110" r="16"/>
                        <text class="kbli-tree__label" x="260" y="114" text-anchor="middle">G</text>
                        {{-- leaf lines --}}
                        <path class="kbli-tree__line" d="M60 126 L 30 180"/>
                        <path class="kbli-tree__line" d="M60 126 L 90 180"/>
                        <path class="kbli-tree__line" d="M160 126 L 130 180"/>
                        <path class="kbli-tree__line" d="M160 126 L 190 180"/>
                        <path class="kbli-tree__line" d="M260 126 L 230 180"/>
                        <path class="kbli-tree__line" d="M260 126 L 290 180"/>
                        {{-- leaves --}}
                        <circle class="kbli-tree__node" cx="30" cy="190" r="10"/>
                        <circle class="kbli-tree__node" cx="90" cy="190" r="10"/>
                        <circle class="kbli-tree__node" cx="130" cy="190" r="10"/>
                        <circle class="kbli-tree__node" cx="190" cy="190" r="10"/>
                        <circle class="kbli-tree__node" cx="230" cy="190" r="10"/>
                        <circle class="kbli-tree__node" cx="290" cy="190" r="10"/>
                        <text class="kbli-tree__label" x="160" y="230" text-anchor="middle" style="font-size:10px;">21 sectors · 1.700+ codes</text>
                    </svg>
                </div>
                <p class="text-xs text-center mt-2" style="color: var(--text-tertiary, var(--text-secondary));">
                    {{ $isEn ? 'Indonesian KBLI taxonomy — our daily map.' : 'Taksonomi KBLI Indonesia — peta harian kami.' }}
                </p>
            </aside>
        </div>
    </div>
</section>

{{-- TIMELINE — HORIZONTAL EDITORIAL --}}
<section class="section-v2 section-premium" aria-labelledby="timeline-heading">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'Milestones' : 'Tonggak Sejarah' }}</span>
        <h2 id="timeline-heading" class="display-lg mb-3 max-w-2xl">
            {{ $isEn ? $expYears . ' years, forward.' : $expYears . ' tahun, melangkah maju.' }}
        </h2>
        <p class="text-base text-gray-600 mb-4 max-w-2xl">
            {{ $isEn ? 'Scroll horizontally to walk through every chapter.' : 'Geser untuk menyusuri setiap babak.' }}
        </p>

        <div class="h-timeline-scroll-hint mb-6" aria-hidden="true"
             x-data="{ visible: true }"
             x-init="setTimeout(() => visible = false, 8000)"
             x-show="visible"
             x-transition.opacity.duration.500ms>
            <i class="fas fa-arrows-left-right text-xs"></i>
            <span>{{ $isEn ? 'Scroll to explore' : 'Geser ke samping' }}</span>
            <i class="fas fa-hand-pointer text-xs animate-pulse"></i>
        </div>

        <div class="h-timeline">
            @foreach($timeline as $t)
                <article class="h-timeline__step">
                    <div class="h-timeline__year">{{ $t['year'] }}</div>
                    <h3 class="h-timeline__title">{{ $t['title'] }}</h3>
                    <p class="h-timeline__desc">{{ $t['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- VALUES — PLATFORM CARDS --}}
<section class="section-v2" aria-labelledby="values-heading">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'Our Values' : 'Nilai Kami' }}</span>
        <h2 id="values-heading" class="display-lg mb-12 max-w-2xl">
            {{ $isEn ? 'How we work — written rules, not slogans.' : 'Cara kami bekerja — aturan tertulis, bukan slogan.' }}
        </h2>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($values as $i => $v)
                <article class="platform-card" style="position: relative; border-top: 2px solid var(--accent); border-top-left-radius: 0; border-top-right-radius: 0;">
                    <div class="platform-card__head">
                        <span class="platform-card__num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }} / {{ str_pad(count($values), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="platform-card__status">{{ $isEn ? 'Live' : 'Aktif' }}</span>
                    </div>
                    <i class="fas {{ $v['icon'] }} text-xl mb-3" style="color: var(--accent);" aria-hidden="true"></i>
                    <h3 class="platform-card__title">{{ $v['title'] }}</h3>
                    <p class="platform-card__body">{{ $v['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- TEAM — EDITORIAL ROSTER --}}
<section class="section-v2" aria-labelledby="team-heading">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'The Practice' : 'Tim Kami' }}</span>
        <div class="editorial-split mb-10">
            <h2 id="team-heading" class="display-lg">
                {{ $isEn ? 'Senior consultants on every project.' : 'Konsultan senior di setiap proyek.' }}
            </h2>
            <p class="text-lg leading-relaxed text-gray-600 max-w-md">
                {{ $isEn
                    ? 'Each engagement is owned by a dedicated lead consultant — backed by a multi-disciplinary team of environmental, legal, tax, and PMA specialists.'
                    : 'Setiap proyek dipimpin oleh konsultan senior terdedikasi — didukung tim multidisiplin di bidang lingkungan, legal, pajak, dan PMA.' }}
            </p>
        </div>

        @php
            // Placeholder team roster — foto + LinkedIn URL akan menyusul (Open Decision #1)
            $teamRoles = [
                [
                    'role'    => $isEn ? 'Lead Environmental Consultant' : 'Konsultan Lingkungan Senior',
                    'focus'   => $isEn ? 'AMDAL, UKL-UPL, KLHS, environmental risk' : 'AMDAL, UKL-UPL, KLHS, risiko lingkungan',
                    'icon'    => 'fa-leaf',
                ],
                [
                    'role'    => $isEn ? 'OSS-RBA & Business Permit Lead' : 'Spesialis OSS-RBA & Izin Usaha',
                    'focus'   => $isEn ? 'NIB, KBLI mapping, sectoral permits' : 'NIB, pemetaan KBLI, izin sektoral',
                    'icon'    => 'fa-id-card',
                ],
                [
                    'role'    => $isEn ? 'PMA & Foreign Investment Lead' : 'Spesialis PMA & Investasi Asing',
                    'focus'   => $isEn ? 'BKPM, sectoral roadmap, KPA, capital structuring' : 'BKPM, peta izin sektor, KPA, struktur permodalan',
                    'icon'    => 'fa-globe-asia',
                ],
                [
                    'role'    => $isEn ? 'Building Permit Specialist' : 'Spesialis Izin Bangunan',
                    'focus'   => $isEn ? 'PBG, SLF, IMB, zoning compliance' : 'PBG, SLF, IMB, kepatuhan zonasi',
                    'icon'    => 'fa-building',
                ],
                [
                    'role'    => $isEn ? 'Tax & Compliance Consultant' : 'Konsultan Pajak & Kepatuhan',
                    'focus'   => $isEn ? 'NPWP, SPT, transfer pricing, tax planning' : 'NPWP, SPT, transfer pricing, perencanaan pajak',
                    'icon'    => 'fa-file-invoice-dollar',
                ],
                [
                    'role'    => $isEn ? 'Project Manager — Field Operations' : 'Manajer Proyek — Operasional Lapangan',
                    'focus'   => $isEn ? 'On-the-ground execution, agency liaison, SLA reporting' : 'Eksekusi lapangan, hubungan instansi, laporan SLA',
                    'icon'    => 'fa-route',
                ],
            ];
        @endphp

        <div class="grid md:grid-cols-2 gap-x-12">
            @foreach($teamRoles as $member)
                <article class="roster-card">
                    <div class="roster-card__monogram" aria-hidden="true">
                        <i class="fas {{ $member['icon'] }}"></i>
                    </div>
                    <div>
                        <h3 class="roster-card__role">{{ $member['role'] }}</h3>
                        <p class="roster-card__focus">{{ $member['focus'] }}</p>
                        <span class="roster-card__tag">{{ $isEn ? 'Senior · 5+ yrs' : 'Senior · 5+ thn' }}</span>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="mt-8 text-sm text-gray-500 max-w-2xl">
            <i class="fas fa-info-circle mr-1.5" style="color: var(--accent);"></i>
            {{ $isEn
                ? 'Team profiles with photos and LinkedIn links are being prepared. Contact us if you would like to know who will lead your engagement.'
                : 'Profil tim lengkap dengan foto dan LinkedIn sedang disiapkan. Hubungi kami bila Anda ingin tahu siapa yang akan memimpin proyek Anda.' }}
        </p>
    </div>
</section>

{{-- CERTIFICATIONS --}}
<section class="section-v2-sm section-premium">
    <div class="container-wide text-center">
        <span class="eyebrow mb-6 justify-center">{{ $isEn ? 'Certifications & Partnerships' : 'Sertifikasi & Kemitraan' }}</span>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
            @foreach($certifications as $c)
                <span class="cert-badge"><i class="fas {{ $c['icon'] }}"></i>{{ $c['label'] }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="gold-rule"></span>
            <h2 class="display-lg mb-6">
                {{ $isEn ? 'Work with a team that knows the full regulatory landscape.' : 'Bekerja dengan tim yang memahami seluruh peta regulasi perizinan Indonesia.' }}
            </h2>
            <p class="text-lg leading-relaxed mb-8 text-gray-600">
                {{ $isEn
                    ? 'Start with a free AI permit check — or speak directly with our consultant team.'
                    : 'Mulai dengan cek perizinan AI gratis — atau bicara langsung dengan tim konsultan kami.' }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-gold btn-lg">
                    <i class="fas fa-robot"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
                </a>
                <a href="{{ $servicesIndexRoute }}" class="btn btn-ghost btn-lg">
                    <span>{{ $isEn ? 'Explore Services' : 'Lihat Semua Layanan' }}</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
