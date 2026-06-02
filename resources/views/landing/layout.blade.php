<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth overflow-x-hidden">
<script>
// --- Dark/Light Mode Initialization ---
// Must run synchronously before paint to prevent flash
(function() {
    // Force light mode on landing page (root path)
    if (window.location.pathname === '/') {
        var theme = 'light';
    } else {
        var theme = localStorage.getItem('bizmark_theme');
        if (!theme) {
            theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
        }
    }
    if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();
</script>
<head>
    @include('landing.partials.head')
    @vite('resources/css/landing-theme.css')
    @vite('resources/js/app.js')
    @yield('structured_data')
    @stack('styles')
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

{{-- Persona detection (UTM + ?persona + cookie, 30-day TTL).
     Sets <body data-persona="..."> as early as possible.
     Personas: diy (DIY tools-first), team (consult), pma (foreign investor), default.
     Sections can opt-in via [data-persona="..."] CSS or Alpine `$el.closest('body').dataset.persona`. --}}
<script>
(function () {
    try {
        var KEY = 'bizmark_persona';
        var TTL_MS = 30 * 24 * 60 * 60 * 1000; // 30 days
        var url = new URL(window.location.href);
        var qs = url.searchParams;
        var fromQuery = qs.get('persona');
        var utmCampaign = (qs.get('utm_campaign') || '').toLowerCase();
        var utmMedium = (qs.get('utm_medium') || '').toLowerCase();
        var utmSource = (qs.get('utm_source') || '').toLowerCase();
        var allowed = ['diy', 'team', 'pma'];
        var persona = '';

        if (fromQuery && allowed.indexOf(fromQuery) !== -1) {
            persona = fromQuery;
        } else if (/pma|foreign|investor/.test(utmCampaign + ' ' + utmSource)) {
            persona = 'pma';
        } else if (/diy|self|tools/.test(utmCampaign + ' ' + utmMedium)) {
            persona = 'diy';
        } else if (/consult|team|enterprise/.test(utmCampaign + ' ' + utmMedium)) {
            persona = 'team';
        }

        // Persist or read
        var stored = null;
        try { stored = JSON.parse(localStorage.getItem(KEY) || 'null'); } catch (_) {}
        var now = Date.now();
        if (persona) {
            try { localStorage.setItem(KEY, JSON.stringify({ p: persona, t: now })); } catch (_) {}
        } else if (stored && stored.p && (now - (stored.t || 0)) < TTL_MS && allowed.indexOf(stored.p) !== -1) {
            persona = stored.p;
        }

        if (persona) {
            document.body.setAttribute('data-persona', persona);
        }
    } catch (e) { /* fail silent — default rendering */ }
})();
</script>

@php
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
@endphp

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    
    <!-- Main Content -->
    <main id="main-content" class="flex-1">
        @yield('content')
    </main>
    
    @include('landing.partials.footer')
    
    {{-- Back-to-top FAB only. WhatsApp floating button removed:
         live chat is handled via Tawk.to (in-platform), and contact
         actions are surfaced contextually within sections. --}}
    <button type="button"
            id="backToTop"
            class="fab-back-to-top"
            aria-label="{{ app()->getLocale() === 'id' ? 'Kembali ke atas' : 'Back to top' }}"
            x-data
            @click="window.scrollTo({top:0,behavior:'smooth'}); if(window.trackEvent) trackEvent('Navigation','scroll_to_top','fab');">
        <i class="fas fa-arrow-up" aria-hidden="true"></i>
    </button>
    
    @include('landing.partials.scripts')
    
    @stack('scripts')
    
    {{-- ============================================================
         Tawk.to Live Chat Widget — AKTIF
         Config: config/services.php → tawk.property_id / tawk.widget_id
         ============================================================ --}}
    @if(config('services.tawk.property_id') && config('services.tawk.widget_id'))
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
    (function(){
        var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
        s1.async=true;
        s1.src='https://embed.tawk.to/{{ config('services.tawk.property_id') }}/{{ config('services.tawk.widget_id') }}';
        s1.charset='UTF-8';
        s1.setAttribute('crossorigin','*');
        s0.parentNode.insertBefore(s1,s0);

        Tawk_API.onChatStarted = function(){
            if (typeof gtag !== 'undefined') {
                gtag('event', 'chat_started', {
                    'event_category': 'Engagement',
                    'event_label': 'Tawk.to Chat'
                });
            }
        };
    })();
    </script>
    @endif
</body>
</html>
