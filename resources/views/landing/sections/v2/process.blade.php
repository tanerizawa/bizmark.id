@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $processRoute = $isEn ? route('process.en') : route('process.id');

    $steps = [
        [
            'num' => '01',
            'title' => $isEn ? 'Initial Discovery & Mapping' : 'Kajian Awal & Pemetaan',
            'desc' => $isEn
                ? 'We audit your business context, KBLI codes, and permit gaps — at no cost.'
                : 'Kami telaah konteks usaha, kode KBLI, dan celah perizinan Anda — tanpa biaya.',
            'duration' => $isEn ? '1-2 days' : '1-2 hari',
        ],
        [
            'num' => '02',
            'title' => $isEn ? 'Proposal & Agreement' : 'Proposal & Kesepakatan',
            'desc' => $isEn
                ? 'Clear scope, cost, and SLA terms — all documented before we start. No surprises.'
                : 'Lingkup pekerjaan, biaya, dan ketentuan SLA yang jelas — semua terdokumentasi sebelum mulai. Tanpa kejutan.',
            'duration' => $isEn ? '2-3 days' : '2-3 hari',
        ],
        [
            'num' => '03',
            'title' => $isEn ? 'Execution & Reporting' : 'Pelaksanaan & Pelaporan',
            'desc' => $isEn
                ? 'Weekly SLA progress reports, dedicated project manager, and field follow-through.'
                : 'Laporan kemajuan SLA mingguan, manajer proyek khusus, dan tindak lanjut langsung di lapangan.',
            'duration' => $isEn ? 'Varies' : 'Variatif',
            'portal_badge' => true,
        ],
        [
            'num' => '04',
            'title' => $isEn ? 'Delivery & Handover' : 'Terbit & Serah Terima',
            'desc' => $isEn
                ? 'Final permits delivered with a compliance roadmap and ongoing support options.'
                : 'Izin diserahkan lengkap beserta peta jalan kepatuhan dan pilihan dukungan berkelanjutan.',
            'duration' => $isEn ? '1 week' : '1 minggu',
        ],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     PROCESS — 4-step timeline
     Reduces uncertainty before commitment
──────────────────────────────────────────────── --}}
<section class="section-v2" aria-labelledby="process-heading" id="process">
    <div class="container-wide">
        <div class="max-w-2xl mb-5">
            <span class="eyebrow block mb-3">{{ $isEn ? 'How We Work' : 'Cara Kerja Kami' }}</span>
            <h2 id="process-heading" class="display-md mb-2">
                {{ $isEn ? 'A process built for clarity and accountability.' : 'Proses yang mengutamakan kejelasan dan akuntabilitas.' }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-600">
                {{ $isEn
                    ? 'Four clear steps. One accountable team. SLA-backed results at every stage.'
                    : 'Empat langkah yang jelas. Satu tim yang bertanggung jawab. Hasil terukur dengan SLA di setiap tahap.' }}
            </p>
        </div>

        <ol class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 relative process-step-grid">
            @foreach($steps as $idx => $step)
                <li class="relative">
                    {{-- Connector line (desktop only, hidden on last) --}}
                    @if(!$loop->last)
                        <div aria-hidden="true" class="hidden lg:block absolute top-6 left-full w-full h-[2px] -translate-x-2 z-0"
                             style="background: repeating-linear-gradient(90deg, rgba(0,0,0,.08) 0 6px, transparent 6px 12px);"></div>
                    @endif

                    <div class="premium-card timeline-step-card relative z-10">
                        <div class="timeline-step-top">
                            <span class="timeline-step-num">{{ $step['num'] }}</span>
                            <span class="timeline-step-pill">{{ $step['duration'] }}</span>
                        </div>

                        <h3 class="font-display font-bold text-xl mb-2 text-gray-900">{{ $step['title'] }}</h3>
                        <p class="text-sm leading-relaxed flex-1 text-gray-600">{{ $step['desc'] }}</p>
                        @if(!empty($step['portal_badge']))
                            <div class="mt-3">
                                <span class="live-indicator">
                                    <span class="live-dot" aria-hidden="true"></span>
                                    <span class="text-xs font-bold text-green-700">{{ $isEn ? 'Tracked via Client Portal' : 'Terpantau via Portal Klien' }}</span>
                                </span>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        <div class="text-center mt-8">
            <a href="{{ $processRoute }}" class="link-primary text-sm font-semibold inline-flex items-center gap-2">
                {{ $isEn ? 'See the full process' : 'Pelajari proses kami secara lengkap' }}
                <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>
