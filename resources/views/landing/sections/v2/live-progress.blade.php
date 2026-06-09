@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    // Audit Mei 2026: tickerStats lama (200–300, 40–60, 10–15, 138+) di-replace
    // dengan capability framing karena angka spesifik tidak dapat diverifikasi.
    $tickerStats = [
        ['icon' => 'fa-bolt',         'value' => '24/7',       'label' => $isEn ? 'portal monitoring' : 'pantau portal'],
        ['icon' => 'fa-bell',         'value' => $isEn ? 'Real-time' : 'Real-time', 'label' => $isEn ? 'milestone alerts' : 'notifikasi milestone'],
        ['icon' => 'fa-globe',        'value' => 'EN / ID',    'label' => $isEn ? 'bilingual support' : 'dukungan bilingual'],
        ['icon' => 'fa-handshake',    'value' => 'PMA',        'label' => $isEn ? 'foreign-investment ready' : 'siap investasi asing'],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     LIVE PROGRESS — Flow diagram: Tools → AI Processing → Client Portal
     Shows the ecosystem journey in 3 connected nodes
     ──────────────────────────────────────────────── --}}
<section class="section-v2 section-premium relative" aria-labelledby="live-progress-heading">
    <div class="container-wide">

        {{-- Section header --}}
        <div class="text-center max-w-2xl mx-auto mb-5" data-aos="fade-up">
            <span class="eyebrow block mb-3">{{ $isEn ? 'Platform Experience' : 'Pengalaman Platform' }}</span>
            <h2 id="live-progress-heading" class="display-md mb-4">
                {{ $isEn ? 'Your permits, always visible.' : 'Progres izin Anda, selalu terlihat.' }}
            </h2>
            <p class="text-sm text-gray-600 leading-relaxed">
                {{ $isEn
                    ? 'From initial AI diagnostics to final approval — track every step inside your Client Portal.'
                    : 'Dari diagnostik AI awal hingga persetujuan akhir — pantau setiap langkah di Portal Klien Anda.' }}
            </p>
        </div>

        {{-- 3-node flow diagram --}}
        <div class="flow-diagram" data-aos="fade-up" data-aos-delay="100">

            {{-- Node 1: Smart Tools --}}
            <div class="flow-node">
                <div class="flow-node-icon-stack">
                    <span class="editorial-icon-badge">
                        <i class="fas fa-robot icon-lg" aria-hidden="true" style="color: var(--accent);"></i>
                    </span>
                    <span class="editorial-icon-badge" style="transform: scale(.75); margin-top: -.5rem;">
                        <i class="fas fa-draw-polygon icon-md" aria-hidden="true" style="color: var(--accent);"></i>
                    </span>
                    <span class="editorial-icon-badge" style="transform: scale(.65); margin-top: -.5rem;">
                        <i class="fas fa-calculator icon-md" aria-hidden="true" style="color: var(--accent);"></i>
                    </span>
                </div>
                <div>
                    <div class="flow-node-title">{{ $isEn ? 'Smart Tools' : 'Tools Cerdas' }}</div>
                    <div class="flow-node-desc">
                        {{ $isEn ? 'AI Permit Checker, Cost Estimator, Map Maker — free for everyone.' : 'AI Permit Checker, Estimasi Biaya, Polygon SHP — gratis untuk semua.' }}
                    </div>
                </div>
            </div>

            {{-- Arrow 1 --}}
            <div class="flow-arrow" aria-hidden="true">
                <div class="flow-arrow-line">
                    <div class="flow-arrow-dot"></div>
                </div>
                <span class="flow-arrow-label">{{ $isEn ? 'AI Analysis' : 'Analisis AI' }}</span>
            </div>

            {{-- Node 2: Data Processing (center, highlighted) --}}
            <div class="flow-node flow-node-center flow-node-highlight">
                <span class="editorial-icon-badge">
                    <i class="fas fa-microchip icon-lg" aria-hidden="true" style="color: var(--accent);"></i>
                </span>
                <div>
                    <div class="flow-node-title">{{ $isEn ? 'Data Processing' : 'Pemrosesan Data' }}</div>
                    <div class="flow-node-desc">
                        {{ $isEn ? 'Our system maps your business profile to applicable permits and compliance requirements.' : 'Sistem kami memetakan profil usaha ke izin yang berlaku dan persyaratan kepatuhan.' }}
                    </div>
                </div>
                <div class="flex items-center gap-1 text-xs text-gray-500 font-medium">
                    <i class="fas fa-bolt text-amber-500 text-xs" aria-hidden="true"></i>
                    {{ $isEn ? 'Automated & expert-reviewed' : 'Otomatis & diverifikasi ahli' }}
                </div>
            </div>

            {{-- Arrow 2 --}}
            <div class="flow-arrow" aria-hidden="true">
                <div class="flow-arrow-line">
                    <div class="flow-arrow-dot flow-arrow-dot-delay"></div>
                </div>
                <span class="flow-arrow-label">{{ $isEn ? 'Real-time Sync' : 'Sinkron Real-time' }}</span>
            </div>

            {{-- Node 3: Client Portal --}}
            <div class="flow-node flow-node-highlight">
                <span class="editorial-icon-badge">
                    <i class="fas fa-gauge-high icon-lg" aria-hidden="true" style="color: var(--accent);"></i>
                </span>
                <div>
                    <div class="flow-node-title">{{ $isEn ? 'Client Portal' : 'Portal Klien' }}</div>
                    <div class="flow-node-desc">
                        {{ $isEn ? 'Monitor every permit stage, receive alerts, and download documents — 24/7.' : 'Pantau setiap tahap izin, terima notifikasi, dan unduh dokumen — 24/7.' }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Capability strip — platform features (no fake numbers, audit Mei 2026) --}}
        <div class="mt-10 mb-2" data-aos="fade-up" data-aos-delay="150">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 max-w-4xl mx-auto">
                @foreach($tickerStats as $stat)
                    <div class="premium-card text-center" style="padding: 1.25rem 1rem; border-color: var(--border-subtle); background: rgba(255,255,255,.7); backdrop-filter: blur(4px);">
                        <div class="editorial-icon-badge is-circle mx-auto mb-2" style="width:2.25rem;height:2.25rem;">
                            <i class="fas {{ $stat['icon'] }} text-sm" aria-hidden="true"></i>
                        </div>
                        <div class="font-bold text-xl leading-tight" style="color: var(--text-primary);">{{ $stat['value'] }}</div>
                        <div class="text-[11px] mt-1 leading-snug uppercase tracking-wider" style="color: var(--text-secondary); letter-spacing: .06em;">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <p class="text-[11px] text-center mt-3" style="color: var(--text-muted);">
                <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
                {{ $isEn ? 'Platform capabilities available to every client.' : 'Kapabilitas platform yang tersedia untuk setiap klien.' }}
            </p>
        </div>

        {{-- CTA — auth-aware. If already logged in, shows shortcut to dashboard.
             For guests we DO NOT repeat the navbar Sign In; instead we route to
             the AI Permit Checker (free entry point) so the section adds value
             rather than duplicating navbar affordances. --}}
        <div class="flex justify-center mt-7" data-aos="fade-up" data-aos-delay="200">
            <div class="text-center">
                @auth('client')
                    <a href="{{ route('client.dashboard') }}" class="btn btn-gold btn-lg"
                       x-data
                       @click="if(window.trackEvent) trackEvent('CTA','click','live_progress_dashboard')">
                        <i class="fas fa-gauge-high" aria-hidden="true"></i>
                        <span>{{ $isEn ? 'Open your Dashboard' : 'Buka Dashboard Anda' }}</span>
                        <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                    <p class="text-sm text-gray-500 mt-3">
                        <i class="fas fa-circle-check text-green-600 mr-1" aria-hidden="true"></i>
                        {{ $isEn ? 'Signed in as ' . auth('client')->user()->name : 'Masuk sebagai ' . auth('client')->user()->name }}
                    </p>
                @else
                    <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-gold btn-lg"
                       x-data
                       @click="if(window.trackEvent) trackEvent('CTA','click','live_progress_ai_check')">
                        <i class="fas fa-robot" aria-hidden="true"></i>
                        <span>{{ $isEn ? 'Run free AI Permit Check' : 'Cek Izin Gratis dengan AI' }}</span>
                        <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                    </a>
                    <p class="text-xs text-gray-500 mt-3">
                        {{ $isEn ? 'Already a client? Sign in via the top navigation.' : 'Sudah jadi klien? Masuk lewat navigasi atas.' }}
                    </p>
                @endauth
            </div>
        </div>

    </div>
</section>
