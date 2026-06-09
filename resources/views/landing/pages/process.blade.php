@extends('landing.layout')

@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $pageTitle = $isEn ? 'Our Process' : 'Proses Kami';
    $pageDescription = $isEn
        ? 'How Bizmark.ID delivers permits: a 6-stage process with SLA-backed results, weekly progress reporting, and a dedicated project manager per engagement.'
        : 'Cara Bizmark.ID mengurus perizinan: proses 6 tahap dengan hasil kerja bergaransi SLA, laporan kemajuan mingguan, dan manajer proyek khusus untuk setiap proyek.';

    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');

    $stages = [
        [
            'num' => '01',
            'title' => $isEn ? 'Initial Consultation & Mapping' : 'Konsultasi Awal & Pemetaan',
            'duration' => $isEn ? '1-2 days' : '1-2 hari',
            'deliverable' => $isEn ? 'Permit gap analysis + KBLI mapping (free)' : 'Analisis celah perizinan + pemetaan KBLI (gratis)',
            'client_input' => $isEn ? 'Business description, KBLI if known, scale' : 'Deskripsi usaha, kode KBLI jika ada, skala operasional',
            'desc' => $isEn
                ? 'We audit your business context and identify every permit you legally need — or already hold — across environmental, operational, and sectoral categories.'
                : 'Kami menelaah konteks usaha Anda dan mengidentifikasi setiap izin yang wajib Anda miliki — maupun yang sudah ada — dari kategori lingkungan, operasional, hingga sektoral.',
            'icon' => 'fa-compass',
        ],
        [
            'num' => '02',
            'title' => $isEn ? 'Proposal & SLA Agreement' : 'Proposal & Kesepakatan SLA',
            'duration' => $isEn ? '2-3 days' : '2-3 hari',
            'deliverable' => $isEn ? 'Formal proposal with scope, cost, SLA' : 'Proposal resmi: lingkup pekerjaan, biaya, SLA',
            'client_input' => $isEn ? 'Internal approvals, company data sheet' : 'Persetujuan internal, lembar data perusahaan',
            'desc' => $isEn
                ? 'Clear scope, transparent cost, and written SLA terms — all documented before work begins. No surprises mid-project.'
                : 'Lingkup pekerjaan, biaya, dan ketentuan SLA yang jelas — semua terdokumentasi sebelum pekerjaan dimulai. Tidak ada kejutan di tengah proyek.',
            'icon' => 'fa-file-signature',
        ],
        [
            'num' => '03',
            'title' => $isEn ? 'Data & Document Collection' : 'Pengumpulan Data & Dokumen',
            'duration' => $isEn ? '3-7 days' : '3-7 hari',
            'deliverable' => $isEn ? 'Complete document checklist + validation' : 'Daftar dokumen lengkap + validasi',
            'client_input' => $isEn ? 'Legal docs, site data, technical drawings' : 'Dokumen hukum, data lokasi, gambar teknis',
            'desc' => $isEn
                ? 'We provide a clear document checklist. Every document is validated before submission — no rejection cycles.'
                : 'Kami menyediakan daftar dokumen yang jelas. Setiap dokumen divalidasi sebelum pengajuan — tanpa siklus penolakan berulang.',
            'icon' => 'fa-folder-open',
        ],
        [
            'num' => '04',
            'title' => $isEn ? 'Submission & Field Follow-through' : 'Pengajuan & Tindak Lanjut Lapangan',
            'duration' => $isEn ? 'Varies by permit' : 'Variatif per jenis izin',
            'deliverable' => $isEn ? 'Submission receipts + weekly SLA report' : 'Tanda terima pengajuan + laporan SLA mingguan',
            'client_input' => $isEn ? 'Site access for inspections (if required)' : 'Akses lokasi untuk inspeksi (jika diperlukan)',
            'desc' => $isEn
                ? 'Our project manager handles OSS submission, ministry coordination, and inspection scheduling. You receive weekly status reports.'
                : 'Manajer proyek kami menangani pengajuan OSS, koordinasi kementerian, dan penjadwalan inspeksi. Anda menerima laporan status setiap minggu.',
            'icon' => 'fa-paper-plane',
        ],
        [
            'num' => '05',
            'title' => $isEn ? 'Issuance & Quality Control' : 'Penerbitan & Kendali Mutu',
            'duration' => $isEn ? '1-3 days' : '1-3 hari',
            'deliverable' => $isEn ? 'Final permits + internal QC sign-off' : 'Izin final + persetujuan kendali mutu internal',
            'client_input' => $isEn ? 'Review & approval' : 'Peninjauan & persetujuan',
            'desc' => $isEn
                ? 'Every permit goes through internal quality control before handover. We verify dates, scope, and all attachments.'
                : 'Setiap izin melewati proses kendali mutu internal sebelum diserahkan. Kami memverifikasi tanggal, lingkup pekerjaan, dan semua lampiran.',
            'icon' => 'fa-clipboard-check',
        ],
        [
            'num' => '06',
            'title' => $isEn ? 'Handover & Compliance Roadmap' : 'Serah Terima & Peta Jalan Kepatuhan',
            'duration' => $isEn ? '1 week' : '1 minggu',
            'deliverable' => $isEn ? 'Digital archive + renewal calendar' : 'Arsip digital + kalender perpanjangan izin',
            'client_input' => $isEn ? 'Kick-off of ongoing monitoring (optional)' : 'Mulai pemantauan berkelanjutan (opsional)',
            'desc' => $isEn
                ? 'We hand over organized digital archives and a compliance roadmap. Optional ongoing monitoring keeps you renewal-ready.'
                : 'Kami menyerahkan arsip digital yang terorganisir beserta peta jalan kepatuhan. Pemantauan berkelanjutan opsional tersedia agar perpanjangan izin Anda selalu siap.',
            'icon' => 'fa-map',
        ],
    ];
@endphp

@section('title', $pageTitle . ' — Bizmark.ID')
@section('meta_description', $pageDescription)
@section('og_title', $pageTitle . ' — Bizmark.ID')
@section('og_description', $pageDescription)

@section('structured_data')
@php
    $howToSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'HowTo',
        'name' => $isEn ? 'How Bizmark.ID delivers Indonesian permits in 6 stages' : 'Cara Bizmark.ID mengurus perizinan Indonesia dalam 6 tahap',
        'description' => $pageDescription,
        'totalTime' => 'P30D',
        'inLanguage' => $isEn ? 'en' : 'id',
        'step' => collect($stages)->map(fn($s, $i) => [
            '@type' => 'HowToStep',
            'position' => $i + 1,
            'name' => $s['title'],
            'text' => $s['desc'],
        ])->all(),
    ];
@endphp
<script type="application/ld+json">{!! json_encode($howToSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection

@section('content')

{{-- HERO — EDITORIAL --}}
<section class="section-v2 geo-motif bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <span class="eyebrow block mb-4">{{ $isEn ? 'How We Work' : 'Cara Kerja Kami' }}</span>
        <div class="editorial-split">
            <div>
                <h1 class="display-xl mb-6" style="font-size: clamp(2.75rem, 6.5vw, 5rem);">
                    {{ $isEn ? 'Six stages.' : 'Enam tahap.' }}<br>
                    <span style="color: var(--accent);">{{ $isEn ? 'One owner.' : 'Satu penanggung jawab.' }}</span><br>
                    {{ $isEn ? 'Written SLA.' : 'SLA tertulis.' }}
                </h1>
            </div>
            <aside class="hidden lg:block pt-2">
                <div class="editorial-quote">
                    {{ $isEn
                        ? 'No handoff confusion. One project manager from kickoff to handover.'
                        : 'Tanpa lempar-lemparan tanggung jawab. Satu manajer proyek dari awal hingga akhir.' }}
                    <span class="editorial-quote__cite">{{ $isEn ? 'Bizmark Operating Standard' : 'Standar Kerja Bizmark' }}</span>
                </div>
            </aside>
        </div>
        <p class="text-xl leading-relaxed max-w-3xl text-gray-600 mt-10">
            {{ $isEn
                ? 'Six stages. One dedicated project manager per engagement. SLA-backed results at every step — documented in writing and reported weekly.'
                : 'Enam tahap. Satu manajer proyek khusus per proyek. Hasil kerja bergaransi SLA di setiap langkah — terdokumentasi secara tertulis dan dilaporkan setiap minggu.' }}
        </p>
    </div>
</section>

{{-- STAGES --}}
<section class="section-v2">
    <div class="container-wide">
        <div class="max-w-4xl mb-6">
            <p class="text-sm" style="color: var(--text-muted);">
                <i class="fas fa-hand-pointer mr-1.5" aria-hidden="true"></i>
                {{ $isEn ? 'Click any stage to expand details' : 'Klik tahap mana saja untuk melihat detail lengkap' }}
            </p>
        </div>
        <div class="space-y-4 process-stages" x-data="{ open: 0 }">
            @foreach($stages as $i => $s)
                <article class="premium-card transition-all"
                         :class="open === {{ $i }} ? 'shadow-lg' : ''"
                         :style="open === {{ $i }} ? 'border-color: rgba(var(--accent-rgb),.4);' : ''">
                    <button type="button"
                            class="w-full text-left p-0 bg-transparent border-0 cursor-pointer"
                            @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
                            :aria-expanded="open === {{ $i }} ? 'true' : 'false'"
                            aria-controls="process-stage-{{ $i }}-panel"
                            id="process-stage-{{ $i }}-trigger">
                    {{-- Header row (always visible) --}}
                    <div class="grid lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-3 flex items-center gap-4">
                            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full flex-shrink-0" style="background: var(--accent-glow); color: var(--accent);">
                                <i class="fas {{ $s['icon'] }} text-xl"></i>
                            </span>
                            <div>
                                <div class="font-display text-3xl font-bold leading-none" style="color: var(--accent-soft);">{{ $s['num'] }}</div>
                                <div class="text-xs font-semibold mt-1 uppercase tracking-wider text-gray-600">
                                    {{ $s['duration'] }}
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-8">
                            <h2 class="font-display font-bold text-2xl mb-2">{{ $s['title'] }}</h2>
                            <p class="text-base leading-relaxed text-gray-600">{{ $s['desc'] }}</p>
                        </div>

                        <div class="lg:col-span-1 flex justify-end pt-1">
                            <i class="fas fa-chevron-down text-lg transition-transform"
                               :class="open === {{ $i }} ? 'rotate-180' : ''"
                               style="color: var(--accent);"
                               aria-hidden="true"></i>
                        </div>
                    </div>
                    </button>

                    {{-- Expandable details --}}
                    <div x-show="open === {{ $i }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak
                         id="process-stage-{{ $i }}-panel"
                         role="region"
                         aria-labelledby="process-stage-{{ $i }}-trigger"
                         class="grid md:grid-cols-2 gap-3 mt-5 pt-5"
                         style="border-top: 1px solid var(--border-subtle);">
                        <div class="rounded-lg p-3 bg-amber-500/10">
                            <div class="text-[10px] font-bold uppercase tracking-[.15em] mb-1.5" style="color: var(--accent-text);">
                                <i class="fas fa-cube text-[10px] mr-1"></i>
                                {{ $isEn ? 'Deliverable' : 'Hasil Kerja' }}
                            </div>
                            <div class="text-sm font-medium">{{ $s['deliverable'] }}</div>
                        </div>
                        <div class="rounded-lg p-3 bg-gray-100/60">
                            <div class="text-[10px] font-bold uppercase tracking-[.15em] mb-1.5 text-gray-500">
                                <i class="fas fa-user text-[10px] mr-1"></i>
                                {{ $isEn ? 'From you' : 'Dari Anda' }}
                            </div>
                            <div class="text-sm font-medium">{{ $s['client_input'] }}</div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- TRUST BAND — EDITORIAL NUMBERS --}}
<section class="section-v2-sm section-premium">
    <div class="container-wide">
        <span class="eyebrow block mb-4 text-center">{{ $isEn ? 'What we guarantee' : 'Apa yang kami jamin' }}</span>
        <div class="editorial-number-grid mt-6">
            <div class="editorial-number">
                <div class="editorial-number__value"><span class="editorial-number__suffix" style="font-size:1em;letter-spacing:0;">{{ $isEn ? 'SLA' : 'SLA' }}</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Written SLA' : 'SLA Tertulis' }}</strong>{{ $isEn ? 'Timeline & deliverables in writing' : 'Timeline & hasil tercantum tertulis' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value">7<span class="editorial-number__suffix">d</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Weekly reports' : 'Laporan mingguan' }}</strong>{{ $isEn ? 'Every status, every week' : 'Status setiap minggu' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value">1<span class="editorial-number__suffix">PM</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Dedicated PM' : 'Manajer khusus' }}</strong>{{ $isEn ? 'No handoff confusion' : 'Tanpa lempar tanggung jawab' }}</div>
            </div>
            <div class="editorial-number">
                <div class="editorial-number__value">6<span class="editorial-number__suffix">/6</span></div>
                <div class="editorial-number__label"><strong>{{ $isEn ? 'Stages tracked' : 'Tahap terlacak' }}</strong>{{ $isEn ? 'From kickoff to handover' : 'Awal hingga akhir' }}</div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="section-v2 section-premium">
    <div class="container-wide">
        <div class="max-w-3xl mx-auto text-center">
            <span class="gold-rule"></span>
            <h2 class="display-lg mb-6">
                {{ $isEn ? 'See how this process applies to your permits.' : 'Terapkan proses ini pada kebutuhan perizinan Anda.' }}
            </h2>
            <p class="text-lg leading-relaxed mb-8 text-gray-600">
                {{ $isEn
                    ? 'Start with a free AI permit check to get your compliance roadmap — then speak with our team.'
                    : 'Mulai dengan cek perizinan AI gratis untuk mendapatkan peta jalan kepatuhan Anda — lalu bicara langsung dengan tim kami.' }}
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-gold btn-lg">
                    <i class="fas fa-robot text-lg flex-shrink-0 leading-none" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
                </a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-lg">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Chat on WhatsApp' : 'Hubungi via WhatsApp' }}</span>
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
