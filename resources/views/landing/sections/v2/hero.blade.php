@php
    $locale = $locale ?? app()->getLocale();
    $metrics = config('landing_metrics');
    $contact = (array) data_get($metrics, 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = preg_replace('/\s+/', '', $phoneNumber);
    $primaryCtaRoute = route('landing.service-inquiry.create');
    $isEn = $locale === 'en';
    $featuredClients = collect(config('landing.clients', []))->take(5);
    $stats = config('landing_metrics.stats', []);
    $experienceLabel = $stats['experience_label'] ?? '12+ Tahun';
    // Audit Mei 2026: hapus klaim spesifik (500+, 1.000+, 98%) yang tidak terverifikasi.
@endphp

{{-- ────────────────────────────────────────────────
     HERO — Split-Screen: Value Prop (left) + Dashboard Mockup (right)
     Phase 1 redesign: outcome-specific copy + reciprocity framing +
     inline AI quick-check input + Indonesia map visual signature.
     ──────────────────────────────────────────────── --}}
<section class="relative overflow-hidden section-v2" aria-labelledby="hero-title">

    {{-- Subtle top accent line --}}
    <div aria-hidden="true" class="absolute inset-x-0 top-0 h-px pointer-events-none"
         style="background: linear-gradient(90deg, transparent, rgba(var(--accent-rgb),.18), transparent);"></div>

    {{-- Visual signature: Indonesia archipelago outline (very subtle) --}}
    <svg aria-hidden="true" class="hero-id-map hidden md:block" viewBox="0 0 1000 360" preserveAspectRatio="xMidYMid meet">
        {{-- Stylised abbreviated archipelago: Sumatra · Java · Kalimantan · Sulawesi · Papua --}}
        <g fill="none" stroke="currentColor" stroke-width="1" stroke-linejoin="round" stroke-linecap="round">
            {{-- Sumatra --}}
            <path d="M70,200 Q90,150 130,120 Q175,100 210,130 Q230,165 215,200 Q200,235 165,250 Q120,260 90,235 Z"/>
            {{-- Java --}}
            <path d="M260,235 Q320,225 390,238 Q420,245 415,258 Q380,268 320,262 Q280,258 258,250 Z"/>
            {{-- Kalimantan --}}
            <path d="M380,110 Q430,95 480,110 Q510,140 500,180 Q480,210 440,210 Q400,200 380,170 Q370,140 380,110 Z"/>
            {{-- Sulawesi --}}
            <path d="M580,130 Q610,115 625,140 Q620,165 610,180 Q625,200 615,230 Q600,245 585,230 Q580,205 590,185 Q575,165 580,130 Z"/>
            {{-- Papua --}}
            <path d="M740,150 Q800,135 870,150 Q920,170 920,205 Q900,230 850,230 Q790,225 750,210 Q725,185 740,150 Z"/>
        </g>
    </svg>

    {{-- Background glow orbs (desktop) --}}
    <div class="hero-bg-glow hero-bg-glow-right hidden md:block" aria-hidden="true"></div>
    <div class="hero-bg-glow hero-bg-glow-left hidden md:block" aria-hidden="true"></div>

    <div class="container-wide relative" style="z-index:1;">
        <div class="grid md:grid-cols-2 gap-10 lg:gap-14 items-center">

            {{-- ── KOLOM KIRI: Value Proposition ── --}}
            <div>
                {{-- Persona-aware welcome ribbon (only shows if body[data-persona] set) --}}
                <div x-data="{ persona: '' }"
                     x-init="persona = document.body.dataset.persona || ''"
                     x-show="persona === 'pma' || persona === 'team' || persona === 'diy'"
                     x-cloak
                     class="mb-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold"
                     :style="persona === 'pma' ? 'background: var(--accent-glow); color: var(--accent-text); border: 1px solid rgba(var(--accent-rgb),.25);' : (persona === 'team' ? 'background: var(--accent-glow); color: var(--accent-text); border: 1px solid rgba(var(--accent-rgb),.25);' : 'background: rgba(16,185,129,.1); color: #047857; border: 1px solid rgba(16,185,129,.25);')"
                     role="status"
                     aria-live="polite">
                    <template x-if="persona === 'pma'">
                        <span><i class="fas fa-globe-asia mr-1.5"></i>{{ $isEn ? 'Welcome, foreign investor — PMA permit roadmap below' : 'Selamat datang investor asing — peta izin PMA di bawah' }}</span>
                    </template>
                    <template x-if="persona === 'team'">
                        <span><i class="fas fa-handshake mr-1.5"></i>{{ $isEn ? 'Looking for a consultant? Skip to "Work with our team" below' : 'Mencari konsultan? Lompat ke "Bekerja dengan tim" di bawah' }}</span>
                    </template>
                    <template x-if="persona === 'diy'">
                        <span><i class="fas fa-tools mr-1.5"></i>{{ $isEn ? 'DIY mode — all 4 tools free, no signup' : 'Mode DIY — 4 alat gratis, tanpa daftar' }}</span>
                    </template>
                </div>

                {{-- Eyebrow — reciprocity framing --}}
                <div class="mb-5" data-aos="fade-up" data-aos-duration="600">
                    <span class="eyebrow eyebrow-hero">
                        <i class="fas fa-circle-check text-[.6rem] mr-1" style="color: var(--tools);" aria-hidden="true"></i>
                        {{ $isEn ? 'Free tools · No login required' : 'Gratis Selamanya · Tanpa Daftar' }}
                    </span>
                </div>

                {{-- Headline — outcome-specific --}}
                <h1 id="hero-title" class="display-xl mb-5" data-aos="fade-up" data-aos-duration="700" data-aos-delay="100">
                    @if($isEn)
                        Map every permit your business needs.<br>
                        <span class="text-amber-600">In 2 minutes. For free.</span>
                    @else
                        Peta lengkap perizinan usaha Anda.<br>
                        <span class="text-amber-600">Dalam 2 menit. Gratis.</span>
                    @endif
                </h1>

                {{-- Lead paragraph — tightened --}}
                <p class="text-lg leading-relaxed mb-6 max-w-[520px] text-gray-600"
                   data-aos="fade-up" data-aos-duration="600" data-aos-delay="200">
                    {{ $isEn
                        ? 'Bizmark.ID is the operating system for Indonesian business permits. Use our AI tools yourself — or hand the work to our specialists when you need on-the-ground execution.'
                        : 'Bizmark.ID adalah operating system perizinan usaha di Indonesia. Pakai alat AI kami sendiri — atau serahkan kepada tim spesialis saat Anda butuh eksekusi lapangan.' }}
                </p>

                {{-- Inline AI quick-check — primary conversion (step-1 embed) with KBLI typeahead --}}
                <div class="relative" style="max-width:540px;"
                     x-data="heroKbliTypeahead()"
                     @click.outside="open = false"
                     @keydown.escape.window="open = false">
                    <form method="GET" action="{{ $isEn ? route('pma.inquiry.create') : route('landing.service-inquiry.create') }}"
                          class="hero-quickcheck mb-3"
                          data-aos="fade-up" data-aos-duration="600" data-aos-delay="280"
                          x-init="persona = document.body.dataset.persona || ''; if (persona === 'pma') $el.action = '{{ route('pma.inquiry.create') }}';"
                          @submit="if(window.trackEvent) trackEvent('CTA','submit','hero_quickcheck', { persona: persona, kbli: kbli || null })">
                        <label for="hero-quickcheck-input" class="sr-only">{{ $isEn ? 'Describe your business or KBLI code' : 'Jenis usaha Anda atau kode KBLI' }}</label>
                        <span class="hero-quickcheck-icon" aria-hidden="true"><i class="fas fa-robot"></i></span>
                        <input id="hero-quickcheck-input" type="text" name="q"
                               x-model="query"
                               @input.debounce.250ms="search()"
                               @focus="if (query.trim().length >= 2) open = true"
                               @keydown.down.prevent="moveActive(1)"
                               @keydown.up.prevent="moveActive(-1)"
                               @keydown.enter="onEnter($event)"
                               role="combobox"
                               aria-autocomplete="list"
                               :aria-expanded="open ? 'true' : 'false'"
                               aria-controls="hero-typeahead-list"
                               placeholder="{{ $isEn ? 'KBLI code or business name (e.g. 56101, coffee shop, packaging)' : 'Kode KBLI atau jenis usaha (cth: 56101, kafe, pabrik kemasan)' }}"
                               class="hero-quickcheck-input"
                               autocomplete="off"
                               maxlength="120">
                        <input type="hidden" name="kbli" x-model="kbli">
                        <button type="submit" class="hero-quickcheck-btn">
                            <span class="hidden sm:inline">{{ $isEn ? 'Check permits' : 'Cek izin' }}</span>
                            <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                        </button>
                    </form>
                    <div id="hero-typeahead-list"
                         class="hero-typeahead"
                         role="listbox"
                         x-show="open && (loading || results.length || (query.trim().length >= 2 && !loading))"
                         x-cloak
                         x-transition.opacity.duration.150ms>
                        <template x-if="loading">
                            <div class="hero-typeahead-state" aria-live="polite">
                                <i class="fas fa-circle-notch fa-spin" aria-hidden="true"></i>
                                <span>{{ $isEn ? 'Searching KBLI…' : 'Mencari KBLI…' }}</span>
                            </div>
                        </template>
                        <template x-for="(r, i) in results" :key="r.code">
                            <button type="button"
                                    role="option"
                                    :aria-selected="i === active"
                                    :class="{ 'is-active': i === active }"
                                    class="hero-typeahead-item"
                                    @click="select(r)"
                                    @mouseenter="active = i">
                                <span class="kbli-code" x-text="r.code"></span>
                                <span class="kbli-desc" x-text="r.description"></span>
                            </button>
                        </template>
                        <template x-if="!loading && !results.length && query.trim().length >= 2">
                            <div class="hero-typeahead-state">
                                <i class="fas fa-circle-info" aria-hidden="true"></i>
                                <span>{{ $isEn ? 'No KBLI match — submit anyway, AI will infer.' : 'Tidak ada KBLI cocok — tetap kirim, AI akan menebak.' }}</span>
                            </div>
                        </template>
                    </div>
                </div>
                <p class="hero-quickcheck-hint mb-7" data-aos="fade-up" data-aos-duration="600" data-aos-delay="320">
                    <i class="fas fa-shield-halved" aria-hidden="true"></i>
                    {{ $isEn ? 'AI maps requirements, agencies & timeline · Free · Just contact info (~30 sec)' : 'AI petakan syarat, instansi & timeline · Gratis · Cukup info kontak (~30 detik)' }}
                </p>

                {{-- Secondary CTA --}}
                <div class="flex flex-wrap items-center gap-3 mb-8"
                     data-aos="fade-up" data-aos-duration="600" data-aos-delay="380">
                    <a href="{{ $isEn ? route('services.index.en') : route('services.index.id') }}"
                       class="btn btn-ghost"
                       x-data
                       @click="if(window.trackEvent) trackEvent('CTA','click','hero_secondary')">
                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                        <span>{{ $isEn ? 'Or browse services' : 'Atau lihat layanan' }}</span>
                    </a>
                </div>

                {{-- Trust line --}}
                <div class="hero-proof-block pt-6 border-t border-gray-200"
                     data-aos="fade-up" data-aos-duration="600" data-aos-delay="400">
                    <div class="hero-proof-title mb-4">
                        {{ $isEn
                            ? 'Operating since 2014 · Manufacturing, logistics, energy & PMA'
                            : 'Beroperasi sejak 2014 · Manufaktur, logistik, energi & PMA' }}
                    </div>
                    <div class="hero-logo-row mb-4">
                        @foreach($featuredClients as $clientName)
                            <span class="hero-logo-item">{{ \Illuminate\Support\Str::limit($clientName, 18) }}</span>
                        @endforeach
                    </div>
                    <div class="hero-proof-meta">
                        <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $experienceLabel }} {{ $isEn ? 'experience' : 'pengalaman' }}</span>
                        <span><i class="fas fa-map-marked-alt" aria-hidden="true"></i> {{ $isEn ? 'Nationwide coverage' : 'Cakupan se-Indonesia' }}</span>
                        <span><i class="fas fa-globe" aria-hidden="true"></i> Bilingual ID / EN</span>
                    </div>
                    </div>
                </div>
                <noscript>
                    <form method="GET" action="{{ $isEn ? route('pma.inquiry.create') : route('landing.service-inquiry.create') }}"
                          class="hero-quickcheck mb-3">
                        <label for="hero-quickcheck-noscript" class="sr-only">{{ $isEn ? 'Describe your business type' : 'Jenis usaha Anda' }}</label>
                        <span class="hero-quickcheck-icon" aria-hidden="true"><i class="fas fa-robot"></i></span>
                        <input id="hero-quickcheck-noscript" type="text" name="q"
                               placeholder="{{ $isEn ? 'Business type (e.g. coffee shop, packaging factory)' : 'Jenis usaha (cth: kafe, pabrik kemasan)' }}"
                               class="hero-quickcheck-input" maxlength="120">
                        <button type="submit" class="hero-quickcheck-btn">
                            <span class="hidden sm:inline">{{ $isEn ? 'Check permits' : 'Cek izin' }}</span>
                            <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
                        </button>
                    </form>
                </noscript>

            {{-- ── KOLOM KANAN: Dashboard Mockup ── --}}
            <figure class="hidden md:flex items-center justify-center dashboard-mockup-wrap mockup-float"
                 data-aos="fade-left" data-aos-duration="800" data-aos-delay="200"
                 role="img"
                 aria-label="{{ $isEn ? 'Preview of Bizmark.ID permit tracking dashboard — illustrative mockup' : 'Pratinjau dashboard pelacakan izin Bizmark.ID — ilustrasi' }}">
                <div class="dashboard-mockup-card w-full max-w-[500px]">

                    {{-- Top bar --}}
                    <div class="mockup-topbar">
                        <div class="mockup-topbar-brand">
                            <i class="fas fa-certificate"></i>
                            <span>Bizmark.ID</span>
                        </div>
                        <div class="mockup-topbar-search">
                            <i class="fas fa-search" style="font-size:.55rem;"></i>
                            <span>{{ $isEn ? 'Search something...' : 'Cari sesuatu...' }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center" style="font-size:.5rem;">
                                <i class="fas fa-bell text-gray-400"></i>
                            </span>
                            <span class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center" style="font-size:.45rem; font-weight:800; color:var(--accent);">JD</span>
                        </div>
                    </div>

                    {{-- Layout: sidebar + content --}}
                    <div class="mockup-layout">

                        {{-- Sidebar --}}
                        <div class="mockup-sidebar">
                            <div class="mockup-nav-item"><i class="fas fa-gauge-high"></i> Dashboard</div>
                            <div class="mockup-nav-item active"><i class="fas fa-file-contract"></i> {{ $isEn ? 'Permit Tracking' : 'Pelacakan Izin' }}</div>
                            <div class="mockup-nav-item"><i class="fas fa-robot"></i> AI Permit</div>
                            <div class="mockup-nav-item"><i class="fas fa-map-marked-alt"></i> Map Makers</div>
                            <div class="mockup-nav-item"><i class="fas fa-book-open"></i> {{ $isEn ? 'Legal Dir.' : 'Dir. Hukum' }}</div>
                            <div class="mockup-nav-item"><i class="fas fa-shield-halved"></i> Compliance</div>
                            <div class="mockup-nav-item"><i class="fas fa-folder-open"></i> Documents</div>
                            <div class="mockup-nav-item"><i class="fas fa-bell"></i> Alerts</div>
                            <div class="mockup-nav-item"><i class="fas fa-chart-line"></i> Reports</div>
                            <div class="mockup-nav-item"><i class="fas fa-gear"></i> Settings</div>
                        </div>

                        {{-- Main content --}}
                        <div class="mockup-content">
                            <div class="mockup-content-header">
                                <div>
                                    <div class="mockup-content-title">{{ $isEn ? 'Permit Tracking' : 'Pelacakan Izin' }}</div>
                                    <div class="mockup-content-sub">{{ $isEn ? 'Monitor and manage your permit applications in real-time' : 'Pantau permohonan izin Anda secara real-time' }}</div>
                                </div>
                                <span class="mockup-new-btn">+ {{ $isEn ? 'New Application' : 'Permohonan Baru' }}</span>
                            </div>

                            {{-- Stats --}}
                            <div class="mockup-stats">
                                <div class="mockup-stat-pill">
                                    <span class="sv">128</span>
                                    <span class="sl">{{ $isEn ? 'All time' : 'Semua' }}</span>
                                </div>
                                <div class="mockup-stat-pill">
                                    <span class="sv" style="color:#b45309;">72</span>
                                    <span class="sl">{{ $isEn ? 'In Progress' : 'Diproses' }}</span>
                                </div>
                                <div class="mockup-stat-pill">
                                    <span class="sv" style="color:#166534;">45</span>
                                    <span class="sl">{{ $isEn ? 'Approved' : 'Disetujui' }}</span>
                                </div>
                                <div class="mockup-stat-pill">
                                    <span class="sv" style="color:#991b1b;">11</span>
                                    <span class="sl">{{ $isEn ? 'Rejected' : 'Ditolak' }}</span>
                                </div>
                            </div>

                            <div class="mockup-main-grid">
                                <div>
                                    <div class="mockup-section-label">{{ $isEn ? 'Application Progress' : 'Progres Permohonan' }}</div>
                                    <div class="mockup-permit-list">
                                        <div class="mockup-permit-row">
                                            <span class="mockup-permit-name">{{ $isEn ? 'Building Permit' : 'Izin Mendirikan Bangunan' }}</span>
                                            <span class="mockup-badge badge-progress">{{ $isEn ? 'IN PROGRESS' : 'DIPROSES' }}</span>
                                            <div class="mockup-progress-bar"><div style="width:70%"></div></div>
                                        </div>
                                        <div class="mockup-permit-row">
                                            <span class="mockup-permit-name">{{ $isEn ? 'Environmental Permit' : 'Izin Lingkungan' }}</span>
                                            <span class="mockup-badge badge-progress">{{ $isEn ? 'IN PROGRESS' : 'DIPROSES' }}</span>
                                            <div class="mockup-progress-bar"><div style="width:40%"></div></div>
                                        </div>
                                        <div class="mockup-permit-row">
                                            <span class="mockup-permit-name">{{ $isEn ? 'Location Permit' : 'Izin Lokasi' }}</span>
                                            <span class="mockup-badge badge-review">{{ $isEn ? 'IN REVIEW' : 'DITINJAU' }}</span>
                                            <div class="mockup-progress-bar"><div style="width:60%"></div></div>
                                        </div>
                                        <div class="mockup-permit-row">
                                            <span class="mockup-permit-name">{{ $isEn ? 'Business License' : 'Izin Usaha (NIB)' }}</span>
                                            <span class="mockup-badge badge-approved">{{ $isEn ? 'APPROVED' : 'TERBIT' }}</span>
                                            <div class="mockup-progress-bar"><div style="width:100%"></div></div>
                                        </div>
                                        <div class="mockup-permit-row">
                                            <span class="mockup-permit-name">{{ $isEn ? 'Advertising Permit' : 'Izin Reklame' }}</span>
                                            <span class="mockup-badge badge-rejected">{{ $isEn ? 'REJECTED' : 'DITOLAK' }}</span>
                                            <div class="mockup-progress-bar"><div style="width:30%"></div></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mockup-map-card">
                                    <div class="mockup-map-title">{{ $isEn ? 'Application Map' : 'Peta Aplikasi' }}</div>
                                    <div class="mockup-map-surface">
                                        <div class="mockup-map-grid"></div>
                                        <span class="mockup-map-pin mockup-map-pin-a"></span>
                                        <span class="mockup-map-pin mockup-map-pin-b"></span>
                                        <span class="mockup-map-pin mockup-map-pin-c"></span>
                                    </div>
                                    <span class="mockup-map-link">{{ $isEn ? 'View on map' : 'Lihat di peta' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </figure>
            {{-- /kolom kanan --}}

        </div>
    </div>
</section>

@push('scripts')
<script>
    // Alpine component: KBLI typeahead for hero quickcheck.
    // Hits /api/kbli/search?q=... (rate-limited 60/min). Falls back gracefully if offline.
    window.heroKbliTypeahead = function () {
        return {
            persona: '',
            query: new URLSearchParams(window.location.search).get('q') || '',
            kbli: '',
            results: [],
            active: -1,
            open: false,
            loading: false,
            _abort: null,
            async search() {
                // Reset any prior selection when user keeps typing
                this.kbli = '';
                const q = this.query.trim();
                if (q.length < 2) { this.results = []; this.open = false; return; }
                if (this._abort) { try { this._abort.abort(); } catch (e) {} }
                this._abort = new AbortController();
                this.loading = true; this.open = true;
                try {
                    const res = await fetch('/api/kbli/search?q=' + encodeURIComponent(q), {
                        signal: this._abort.signal,
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error('http ' + res.status);
                    const json = await res.json();
                    const list = (json && json.data) ? json.data : [];
                    this.results = list.slice(0, 8);
                    this.active = this.results.length ? 0 : -1;
                } catch (e) {
                    if (e.name !== 'AbortError') { this.results = []; this.active = -1; }
                } finally { this.loading = false; }
            },
            moveActive(dir) {
                if (!this.results.length) return;
                this.open = true;
                this.active = (this.active + dir + this.results.length) % this.results.length;
            },
            select(r) {
                if (!r) return;
                this.query = r.code + ' — ' + r.description;
                this.kbli = r.code;
                this.open = false;
                this.results = [];
            },
            onEnter(ev) {
                if (this.open && this.active >= 0 && this.results[this.active]) {
                    ev.preventDefault();
                    this.select(this.results[this.active]);
                }
                // else: let form submit naturally
            },
        };
    };
</script>
@endpush
