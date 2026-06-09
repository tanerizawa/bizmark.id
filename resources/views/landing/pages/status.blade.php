@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'System Status' : 'Status Sistem';

    $metrics      = config('landing_metrics');
    $contact      = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';

    $pageDescription = $isEn
        ? 'Real-time status of Bizmark.ID services and the Indonesian permit portals we integrate with.'
        : 'Status real-time layanan Bizmark.ID dan portal perizinan Indonesia yang kami integrasikan.';

    // Internal services — manually maintained; in future hook to a healthcheck job
    $internal = [
        ['name' => 'Bizmark.ID Web',           'status' => 'operational', 'note' => $isEn ? 'All routes responding' : 'Semua route merespons'],
        ['name' => 'Client Portal',            'status' => 'operational', 'note' => $isEn ? 'Login & permit tracking' : 'Login & pelacakan izin'],
        ['name' => 'AI Permit Checker',        'status' => 'operational', 'note' => $isEn ? 'Average response 2-4s' : 'Respons rata-rata 2-4 dtk'],
        ['name' => 'Cost Calculator',          'status' => 'operational', 'note' => $isEn ? 'Public tool' : 'Tool publik'],
        ['name' => 'SHP Polygon Maker',        'status' => 'operational', 'note' => $isEn ? 'Export pipeline healthy' : 'Pipeline ekspor sehat'],
        ['name' => 'Email & WA Notifications', 'status' => 'operational', 'note' => $isEn ? 'Outbound queue normal' : 'Antrian outbound normal'],
    ];

    // External agency portals — informational, link out
    $external = [
        ['name' => 'OSS-RBA',           'href' => 'https://oss.go.id',                    'agency' => 'BKPM',                       'kind' => $isEn ? 'Business Identity Number (NIB)' : 'Nomor Induk Berusaha (NIB)'],
        ['name' => 'AHU Online',        'href' => 'https://ahu.go.id',                    'agency' => 'Kemenkumham',                'kind' => $isEn ? 'Company legal entity' : 'Pendirian badan hukum'],
        ['name' => 'AMDALNET',          'href' => 'https://amdalnet.menlhk.go.id',        'agency' => 'KLHK',                       'kind' => $isEn ? 'AMDAL / UKL-UPL submission' : 'Pengajuan AMDAL / UKL-UPL'],
        ['name' => 'SIMBG',             'href' => 'https://simbg.pu.go.id',               'agency' => 'PUPR',                       'kind' => $isEn ? 'PBG / SLF building permits' : 'PBG / SLF izin bangunan'],
        ['name' => 'DJP Online',        'href' => 'https://djponline.pajak.go.id',        'agency' => 'DJP',                        'kind' => $isEn ? 'Tax filing & NPWP' : 'Pelaporan pajak & NPWP'],
        ['name' => 'INSW',              'href' => 'https://insw.go.id',                   'agency' => 'Kemenkeu',                   'kind' => $isEn ? 'Trade single window' : 'Single window perdagangan'],
    ];

    $statusColor = [
        'operational' => ['bg' => 'rgba(16,185,129,.12)', 'fg' => '#047857', 'icon' => 'fa-circle-check', 'label' => $isEn ? 'Operational' : 'Operasional'],
        'degraded'    => ['bg' => 'rgba(245,158,11,.14)', 'fg' => '#b45309', 'icon' => 'fa-circle-exclamation', 'label' => $isEn ? 'Degraded' : 'Terganggu'],
        'down'        => ['bg' => 'rgba(239,68,68,.14)',  'fg' => '#b91c1c', 'icon' => 'fa-circle-xmark', 'label' => $isEn ? 'Down' : 'Mati'],
    ];

    $allOperational = collect($internal)->every(fn($s) => $s['status'] === 'operational');
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)
@section('og_title', $pageTitle . ' — Bizmark.ID')
@section('og_description', $pageDescription)

@section('structured_data')
@php
    $webPageSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => $pageTitle,
        'description' => $pageDescription,
        'inLanguage' => $isEn ? 'en' : 'id',
        'dateModified' => now()->toIso8601String(),
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => 'Bizmark.ID',
            'url' => url('/'),
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($webPageSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')

{{-- HERO --}}
<section class="section-v2-sm" style="background: linear-gradient(180deg, var(--surface) 0%, transparent 100%);">
    <div class="container-wide">
        <div class="max-w-3xl">
            <span class="eyebrow mb-4">
                <i class="fas fa-heart-pulse text-xs"></i>
                {{ $isEn ? 'System Status' : 'Status Sistem' }}
            </span>
            <h1 class="display-xl mt-3 mb-4">
                {{ $isEn ? 'Live status of our platform.' : 'Status platform kami secara langsung.' }}
            </h1>
            <p class="text-lg leading-relaxed text-gray-600 mb-6">
                {{ $isEn
                    ? 'Internal services + the Indonesian government portals we integrate with. Updated manually for now; automated healthchecks coming soon.'
                    : 'Layanan internal + portal pemerintah Indonesia yang kami integrasikan. Diperbarui manual; healthcheck otomatis akan menyusul.' }}
            </p>

            <div class="premium-card flex items-center gap-4" style="border: 1px solid rgba(16,185,129,.25);">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(16,185,129,.12);">
                    <i class="fas fa-circle-check text-2xl" style="color: #059669;"></i>
                </div>
                <div>
                    <h2 class="font-display font-bold text-lg" style="color: #047857;">
                        {{ $allOperational
                            ? ($isEn ? 'All systems operational' : 'Semua sistem operasional')
                            : ($isEn ? 'Some systems degraded' : 'Sebagian sistem terganggu') }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        {{ $isEn ? 'Last checked' : 'Pengecekan terakhir' }}: {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- INTERNAL --}}
<section class="section-v2" aria-labelledby="internal-status">
    <div class="container-wide">
        <div class="mb-8">
            <span class="eyebrow mb-3">{{ $isEn ? 'Internal services' : 'Layanan internal' }}</span>
            <h2 id="internal-status" class="display-lg mt-2">
                {{ $isEn ? 'Bizmark.ID platform' : 'Platform Bizmark.ID' }}
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($internal as $svc)
                @php $c = $statusColor[$svc['status']]; @endphp
                <div class="premium-card flex items-start gap-4">
                    <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center" style="background: {{ $c['bg'] }};">
                        <i class="fas {{ $c['icon'] }} text-lg" style="color: {{ $c['fg'] }};"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="font-display font-bold text-base">{{ $svc['name'] }}</h3>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full" style="background: {{ $c['bg'] }}; color: {{ $c['fg'] }};">
                                {{ $c['label'] }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $svc['note'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- EXTERNAL AGENCIES --}}
<section class="section-v2" style="background: var(--surface);" aria-labelledby="external-status">
    <div class="container-wide">
        <div class="max-w-2xl mb-8">
            <span class="eyebrow mb-3">{{ $isEn ? 'Government portals' : 'Portal pemerintah' }}</span>
            <h2 id="external-status" class="display-lg mt-2 mb-3">
                {{ $isEn ? 'Indonesian agencies we integrate with' : 'Instansi pemerintah yang kami integrasikan' }}
            </h2>
            <p class="text-base leading-relaxed text-gray-600">
                {{ $isEn
                    ? 'These external portals are operated by Indonesian government agencies. We are not affiliated, but we link out for your convenience.'
                    : 'Portal eksternal di bawah dikelola instansi pemerintah Indonesia. Kami tidak berafiliasi, hanya menyediakan link untuk kemudahan Anda.' }}
            </p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($external as $ext)
                <a href="{{ $ext['href'] }}" target="_blank" rel="noopener noreferrer nofollow"
                   class="premium-card group transition-transform hover:-translate-y-0.5">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-0.5">{{ $ext['name'] }}</h3>
                            <p class="text-[11px] font-bold uppercase tracking-[.12em] mb-2" style="color: var(--accent-text);">{{ $ext['agency'] }}</p>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square text-sm opacity-50 group-hover:opacity-100 transition-opacity" style="color: var(--accent);"></i>
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $ext['kind'] }}</p>
                </a>
            @endforeach
        </div>

        <p class="mt-6 text-xs text-gray-500 max-w-2xl">
            <i class="fas fa-info-circle mr-1.5" style="color: var(--accent);"></i>
            {{ $isEn
                ? 'Government portal availability fluctuates. If a portal is down during your engagement, our team handles re-submission and timeline communication automatically.'
                : 'Ketersediaan portal pemerintah berubah-ubah. Jika portal down selama proyek berjalan, tim kami menangani pengajuan ulang dan komunikasi timeline secara otomatis.' }}
        </p>
    </div>
</section>

{{-- INCIDENTS PLACEHOLDER --}}
<section class="section-v2">
    <div class="container-wide max-w-3xl">
        <span class="eyebrow mb-3">{{ $isEn ? 'Recent incidents' : 'Insiden terkini' }}</span>
        <h2 class="display-md mt-2 mb-6">
            {{ $isEn ? 'No incidents reported in the last 90 days.' : 'Tidak ada insiden tercatat dalam 90 hari terakhir.' }}
        </h2>
        <div class="premium-card flex items-start gap-4">
            <i class="fas fa-shield-halved text-2xl" style="color: var(--accent);"></i>
            <div>
                <h3 class="font-display font-bold text-base mb-1">{{ $isEn ? 'Subscribe to status updates' : 'Berlangganan update status' }}</h3>
                <p class="text-sm text-gray-600 mb-3">
                    {{ $isEn
                        ? 'For active client engagements, status updates arrive automatically via email & WhatsApp as part of your weekly SLA report.'
                        : 'Untuk klien aktif, update status disampaikan otomatis via email & WhatsApp sebagai bagian dari laporan SLA mingguan.' }}
                </p>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">
                    <i class="fab fa-whatsapp"></i> {{ $isEn ? 'Contact us' : 'Hubungi kami' }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
