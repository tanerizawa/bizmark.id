<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Primary Meta Tags -->
@php
    $companyName = 'PT Cangah Pajaratan Mandiri';
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $schemaPhone = $contact['phone'] ?? '+62 838 7960 2855';
    $schemaPhone = preg_replace('/\s+/', '', $schemaPhone);
    $schemaWhatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $defaultTitle = app()->getLocale() == 'id' 
        ? "Bizmark.ID – Platform Legal-Tech Perizinan & Kepatuhan Usaha Indonesia" 
        : "Bizmark.ID – Legal-Tech Platform for Business Permits & Compliance in Indonesia";
    $defaultMetaTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID – Platform Legal-Tech Perizinan & Kepatuhan Usaha Indonesia' 
        : 'Bizmark.ID – Legal-Tech Platform for Business Permits & Compliance in Indonesia';
    $defaultDescription = app()->getLocale() == 'id' 
        ? "Bizmark.ID adalah platform legal-tech untuk perizinan usaha, kepatuhan lingkungan (LB3, AMDAL, UKL-UPL), dan legalitas usaha. AI Permit Checker, Client Portal, dan konsultan berpengalaman untuk UMKM hingga korporasi di seluruh Indonesia." 
        : "Bizmark.ID is an AI-powered legal-tech platform for business licensing, environmental compliance (AMDAL, UKL-UPL, B3 Waste), and regulatory affairs in Indonesia. Trusted by 150+ companies from SMEs to multinationals.";
    $defaultKeywords = app()->getLocale() == 'id' 
        ? 'platform perizinan usaha, konsultan perizinan indonesia, ai permit checker, lb3 amdal ukl-upl, oss rba, perizinan lingkungan, legalitas usaha, konsultan karawang jawa barat' 
        : 'business permit indonesia, legal tech indonesia, ai permit checker, environmental compliance indonesia, amdal ukl-upl, oss rba permit, pma establishment, indonesia regulatory affairs';
@endphp
<title>@yield('title', $defaultTitle)</title>
<meta name="title" content="@yield('meta_title', $defaultMetaTitle)">
<meta name="description" content="@yield('meta_description', $defaultDescription)">
<meta name="keywords" content="@yield('meta_keywords', $defaultKeywords)">
<meta name="robots" content="index, follow">
<meta name="language" content="{{ app()->getLocale() }}">
<meta name="author" content="{{ $companyName }} (Bizmark.ID)">
<link rel="canonical" href="{{ url()->current() }}">

<!-- RSS Feed Auto-Discovery -->
<link rel="alternate" type="application/rss+xml" title="Bizmark.ID Blog RSS" href="{{ route('feed.rss') }}">
<link rel="alternate" type="application/atom+xml" title="Bizmark.ID Blog Atom" href="{{ route('feed.atom') }}">

<!-- Alternate Languages (Hreflang) -->
<link rel="alternate" hreflang="id" href="{{ url('/') }}">
<link rel="alternate" hreflang="en" href="{{ url('/en') }}">
<link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

<!-- WebSite Schema + Sitelinks Searchbox -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebSite",
    "name": "Bizmark.ID",
    "alternateName": "{{ $companyName }}",
    "url": "{{ url('/') }}",
    "inLanguage": ["id", "en"],
    "potentialAction": {
        "@@type": "SearchAction",
        "target": {
            "@@type": "EntryPoint",
            "urlTemplate": "{{ url('/blog') }}?q={search_term_string}"
        },
        "query-input": "required name=search_term_string"
    }
}
</script>

<!-- Open Graph / Facebook -->
@php
    $defaultOgTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID – Platform Legal-Tech Perizinan & Kepatuhan Usaha Indonesia' 
        : 'Bizmark.ID – Legal-Tech Platform for Business Permits & Compliance in Indonesia';
    $defaultOgDescription = app()->getLocale() == 'id' 
        ? 'Platform legal-tech untuk perizinan usaha, kepatuhan lingkungan (LB3, AMDAL, UKL-UPL), dan legalitas usaha. AI Permit Checker & Client Portal.' 
        : 'AI-powered legal-tech platform for business licensing, environmental compliance, and regulatory affairs in Indonesia. Operating since 2014.';
@endphp
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="@yield('og_title', $defaultOgTitle)">
<meta property="og:description" content="@yield('og_description', $defaultOgDescription)">
@php
    $ogImage = app()->getLocale() == 'id' ? asset('images/og-image-id.jpg') : asset('images/og-image-en.jpg');
@endphp
<meta property="og:image" content="@yield('og_image', $ogImage)">
<meta property="og:image:alt" content="@yield('og_image_alt', app()->getLocale() == 'id' ? 'Bizmark.ID — Platform Legal-Tech Perizinan & Kepatuhan Usaha Indonesia' : 'Bizmark.ID — Legal-Tech Platform for Business Permits & Compliance in Indonesia')">
@hasSection('article_published_time')
<meta property="article:published_time" content="@yield('article_published_time')">
<meta property="article:modified_time" content="@yield('article_modified_time')">
<meta property="article:section" content="@yield('article_section')">
@endif
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="{{ app()->getLocale() == 'id' ? 'id_ID' : 'en_US' }}">
<meta property="og:site_name" content="Bizmark.ID">

<!-- Twitter -->
@php
    $defaultTwitterTitle = app()->getLocale() == 'id' 
        ? 'Bizmark.ID – Platform Legal-Tech Perizinan & Kepatuhan Usaha' 
        : 'Bizmark.ID – Legal-Tech Platform for Business Permits & Compliance';
    $defaultTwitterDescription = app()->getLocale() == 'id' 
        ? 'Platform AI untuk perizinan usaha, AMDAL, LB3, dan kepatuhan lingkungan. Sederhanakan Birokrasi, percepat Perizinan.' 
        : 'AI-powered platform for business permits, AMDAL, B3 Waste, and environmental compliance. Simplify bureaucracy. Accelerate licensing.';
@endphp
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="@yield('twitter_title', $defaultTwitterTitle)">
<meta name="twitter:description" content="@yield('twitter_description', $defaultTwitterDescription)">
<meta name="twitter:image" content="{{ $ogImage }}">
<meta name="twitter:image:alt" content="@yield('og_image_alt', app()->getLocale() == 'id' ? 'Bizmark.ID — Platform Legal-Tech Perizinan & Kepatuhan Usaha Indonesia' : 'Bizmark.ID — Legal-Tech Platform for Business Permits & Compliance in Indonesia')">

<!-- Favicons -->
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon.png') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-mark.svg') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
<link rel="mask-icon" href="{{ asset('images/logo-mark.svg') }}" color="#00a1e9">
<meta name="theme-color" content="#00a1e9">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Bizmark.ID">

<!-- PWA Manifest -->
<link rel="manifest" href="/manifest.json">

<!-- Preconnect for external resources -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://www.googletagmanager.com">
<link rel="dns-prefetch" href="https://wa.me">
<link rel="dns-prefetch" href="https://api.whatsapp.com">
@if(config('services.tawk.property_id'))
<link rel="preconnect" href="https://embed.tawk.to">
<link rel="dns-prefetch" href="https://embed.tawk.to">
<link rel="dns-prefetch" href="https://va.tawk.to">
@endif

<!-- Google Fonts - Inter (body/UI) + Fraunces (display serif) - non-blocking load -->
<link rel="preload" as="style"
      href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap"
      onload="this.rel='stylesheet'">
<noscript>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,700;9..144,800&family=Inter:wght@400;500;600;700&display=swap">
</noscript>

<!-- Tailwind CSS (compiled) -->
@vite('resources/css/public.css')

<!-- Critical CSS (Inline for LCP) -->
@include('landing.partials.critical-css')

<!-- Structured Data (JSON-LD Schema.org) -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Organization",
    "name": "Bizmark.ID",
    "legalName": "{{ $companyName }}",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "description": "{{ __('landing.schema_description') }}",
    "address": {
        "@@type": "PostalAddress",
        "addressCountry": "ID",
        "addressRegion": "West Java",
        "addressLocality": "Karawang"
    },
    "contactPoint": [{
        "@@type": "ContactPoint",
        "telephone": "{{ $schemaPhone }}",
        "contactType": "customer service",
        "availableLanguage": ["Indonesian", "English"],
        "areaServed": "ID"
    }],
    "sameAs": [
        "{{ $schemaWhatsapp }}"
    ]
}
</script>

<!-- SoftwareApplication Schema (AI Tools) -->
<script type="application/ld+json">
[
  {
    "@@context": "https://schema.org",
    "@@type": "SoftwareApplication",
    "name": "{{ app()->getLocale() == 'id' ? 'AI Permit Checker' : 'AI Permit Checker' }}",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url('/kalkulator-perizinan') }}",
    "description": "{{ app()->getLocale() == 'id' ? 'Cek jenis izin yang dibutuhkan usaha Anda secara otomatis menggunakan AI. Gratis.' : 'Automatically check what permits your business needs using AI. Free.' }}",
    "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "IDR" },
    "provider": { "@@type": "Organization", "name": "Bizmark.ID" }
  },
  {
    "@@context": "https://schema.org",
    "@@type": "SoftwareApplication",
    "name": "{{ app()->getLocale() == 'id' ? 'Polygon SHP Maker' : 'Polygon SHP Maker' }}",
    "applicationCategory": "BusinessApplication",
    "operatingSystem": "Web",
    "url": "{{ url('/polygon-shp-maker') }}",
    "description": "{{ app()->getLocale() == 'id' ? 'Buat peta poligon lokasi usaha format SHP untuk keperluan perizinan lingkungan.' : 'Create business location polygon maps in SHP format for environmental permits.' }}",
    "offers": { "@@type": "Offer", "price": "0", "priceCurrency": "IDR" },
    "provider": { "@@type": "Organization", "name": "Bizmark.ID" }
  }
]
</script>

<!-- Page-specific head content -->
@stack('head')

<!-- Google Analytics 4 -->
@php $gaId = config('services.google.analytics_id'); @endphp
@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}" fetchpriority="low"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}');
</script>
@endif

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .then((registration) => {
                // Show a non-intrusive update banner instead of force-reloading.
                const showUpdateBanner = () => {
                    if (document.getElementById('sw-update-banner')) return;
                    const banner = document.createElement('div');
                    banner.id = 'sw-update-banner';
                    banner.setAttribute('role', 'status');
                    banner.style.cssText = 'position:fixed;bottom:1rem;left:50%;transform:translateX(-50%);z-index:9999;background:#1e293b;color:#fff;padding:.75rem 1.5rem;border-radius:.75rem;font-size:.875rem;display:flex;align-items:center;gap:.75rem;box-shadow:0 4px 16px rgba(0,0,0,.25)';
                    banner.innerHTML = '<span>{{ app()->getLocale() === "id" ? "Pembaruan tersedia" : "Update available" }}</span>'
                        + '<button onclick="window.location.reload()" style="background:#f59e0b;color:#1e293b;border:none;padding:.375rem .875rem;border-radius:.5rem;font-weight:600;cursor:pointer;font-size:.8125rem">{{ app()->getLocale() === "id" ? "Perbarui" : "Refresh" }}</button>'
                        + '<button onclick="this.parentNode.remove()" aria-label="Tutup" style="background:transparent;border:none;color:#94a3b8;cursor:pointer;font-size:1.25rem;line-height:1">&times;</button>';
                    document.body.appendChild(banner);
                };

                const activateWaitingWorker = () => {
                    if (registration.waiting) {
                        registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                    }
                };

                // When the new SW takes control, show banner instead of hard reload.
                navigator.serviceWorker.addEventListener('controllerchange', showUpdateBanner);

                // Check for updates in the background (not on every load — only on focus).
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        registration.update().catch(() => {});
                    }
                });

                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    if (!newWorker) return;

                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // A new version is ready — activate it, banner will appear on controllerchange.
                            activateWaitingWorker();
                        }
                    });
                });
            })
            .catch(() => {
                // Service Worker registration failures are expected in
                // some environments (offline, private browsing, etc.)
            });
    });
}
</script>
