@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $primaryCtaRoute = route('landing.service-inquiry.create');

    $primaryTools = [
        [
            'icon' => 'fa-robot',
            'color' => '#b8860b',
            'colorBg' => 'rgba(184,134,11,.1)',
            'title' => $isEn ? 'AI Permit Checker' : 'Cek Kebutuhan Izin (AI)',
            'desc' => $isEn
                ? 'Tell us about your business and our AI instantly maps every permit you need — at no cost.'
                : 'Ceritakan jenis usaha Anda, dan AI kami langsung memetakan semua izin yang perlu Anda urus — tanpa biaya.',
            'stat' => $isEn ? 'KBLI 2020 database · Free' : 'Database KBLI 2020 · Gratis',
            'cta' => $isEn ? 'Check now' : 'Cek sekarang',
            'href' => $primaryCtaRoute,
            'mockup' => 'ai',
            'eyebrow' => $isEn ? 'Guided intake' : 'Intake terpandu',
        ],
        [
            'icon' => 'fa-brain',
            'color' => '#047857',
            'colorBg' => 'rgba(4,120,87,.1)',
            'title' => $isEn ? 'Permit Cost Estimator' : 'Estimasi Biaya Perizinan',
            'desc' => $isEn
                ? 'Select your KBLI business code and get an instant breakdown of permit costs and processing timelines.'
                : 'Pilih kode KBLI usaha Anda dan dapatkan perkiraan biaya serta estimasi durasi pengurusan izin secara langsung.',
            'stat' => $isEn ? 'Real-time · AI' : 'Real-time · AI',
            'cta' => $isEn ? 'Estimate now' : 'Hitung Biaya',
            'href' => route('consultation.index'),
            'mockup' => 'estimator',
            'eyebrow' => $isEn ? 'Cost visibility' : 'Visibilitas biaya',
        ],
        [
            'icon' => 'fa-draw-polygon',
            'color' => '#1d4ed8',
            'colorBg' => 'rgba(29,78,216,.1)',
            'title' => 'Polygon SHP Maker',
            'desc' => $isEn
                ? 'Draw your business polygon on an interactive map and export an OSS-RBA ready SHP file in minutes.'
                : 'Gambar poligon lokasi usaha Anda di peta interaktif dan ekspor file SHP siap pakai untuk OSS-RBA dalam hitungan menit.',
            'stat' => $isEn ? 'OSS-RBA ready' : 'Siap OSS-RBA',
            'cta' => $isEn ? 'Create SHP' : 'Buat SHP',
            'href' => route('polygon.shp.index'),
            'mockup' => 'map',
            'eyebrow' => $isEn ? 'Spatial planning' : 'Perencanaan spasial',
        ],
        [
            'icon' => 'fa-calculator',
            'color' => '#b45309',
            'colorBg' => 'rgba(180,83,9,.1)',
            'title' => $isEn ? 'Permit Cost Calculator' : 'Kalkulator Biaya Perizinan',
            'desc' => $isEn
                ? 'A detailed cost breakdown by permit type, required documents, and expected processing time.'
                : 'Rincian biaya lengkap berdasarkan jenis izin, dokumen yang diperlukan, dan estimasi waktu pengurusan.',
            'stat' => $isEn ? '50+ permit types' : '50+ jenis izin',
            'cta' => $isEn ? 'Calculate now' : 'Hitung Biaya',
            'href' => route('calculator.index'),
            'mockup' => 'calculator',
            'eyebrow' => $isEn ? 'Scenario planning' : 'Simulasi biaya',
        ],
    ];

    $blogCount = cache()->remember('blog_published_count', 3600, fn() => \App\Models\Article::where('status', 'published')->whereNotNull('published_at')->count());
    $blogSub   = $blogCount . ' ' . ($isEn ? 'articles' : 'artikel');

    $secondaryTools = [
        ['icon' => 'fa-newspaper', 'label' => $isEn ? 'Insights & Guides' : 'Artikel & Panduan', 'sub' => $blogSub, 'href' => $isEn ? route('blog.index.en') : route('blog.index.id')],
        ['icon' => 'fa-list-check',  'label' => $isEn ? 'Document Checklist AI' : 'Checklist Dokumen AI', 'sub' => $isEn ? 'Free · AI-generated' : 'Gratis · Dibuat AI', 'href' => route('checklist.index')],
        ['icon' => 'fa-envelope',   'label' => $isEn ? 'Newsletter' : 'Buletin Regulasi',        'sub' => $isEn ? 'Monthly regulatory digest' : 'Pembaruan regulasi bulanan', 'href' => '#newsletter'],
        ['icon' => 'fa-briefcase',  'label' => $isEn ? 'Careers' : 'Karir',                      'sub' => $isEn ? 'Join our team' : 'Bergabung bersama kami', 'href' => route('career.index')],
    ];
@endphp

{{-- ────────────────────────────────────────────────
     ECOSYSTEM HUB — Feature showcase (the highlight)
     Reciprocity: free tools build trust + qualified leads
──────────────────────────────────────────────── --}}
<section class="section-v2 ecosystem-hub relative" aria-labelledby="ecosystem-heading">
    <div class="container-wide">
        <div class="max-w-2xl mb-5">
            <span class="eyebrow block mb-3" style="color: var(--color-tools);">{{ $isEn ? 'Self-serve toolkit' : 'Toolkit Mandiri' }}</span>
            <h2 id="ecosystem-heading" class="display-md mb-2">
                {{ $isEn ? 'Use the platform yourself. Pay nothing.' : 'Pakai platformnya sendiri. Tanpa biaya.' }}
            </h2>
            <p class="text-sm leading-relaxed text-gray-600">
                {{ $isEn
                    ? 'Built on ' . config('landing_metrics.experience.years', 12) . '+ years of real permit cases across Indonesia. Run the AI checker, estimate cost, draw your polygon — yourself. When you need on-the-ground execution, our specialists step in.'
                    : 'Dibangun dari ' . config('landing_metrics.experience.years', 12) . '+ tahun pengalaman izin nyata di Indonesia. Jalankan AI checker, estimasi biaya, gambar poligon — sendiri. Saat Anda butuh eksekusi lapangan, tim spesialis kami turun tangan.' }}
            </p>
        </div>

        {{-- Primary tools: Bento grid layout --}}
        <div class="bento-grid mb-4">
            @foreach($primaryTools as $index => $tool)
                <a href="{{ $tool['href'] }}" class="tool-card is-tools {{ $index === 0 ? 'bento-featured' : ($index === 3 ? 'bento-wide' : 'bento-medium') }}"
                   style="--tool-accent: {{ $tool['color'] }}; --tool-accent-bg: {{ $tool['colorBg'] }};">
                    <div class="tool-card-copy">
                        <div class="flex items-start justify-between gap-4 flex-wrap">
                            <span class="editorial-icon-badge" style="background: {{ $tool['colorBg'] }}; color: {{ $tool['color'] }};">
                                <i class="fas {{ $tool['icon'] }} icon-xl" aria-hidden="true"></i>
                            </span>
                            <span class="tool-stat">{{ $tool['stat'] }}</span>
                        </div>
                        <div>
                            <div class="tool-kicker mb-2">{{ $tool['eyebrow'] }}</div>
                            <div class="tool-title mb-1.5">{{ $tool['title'] }}</div>
                            <div class="tool-desc">{{ $tool['desc'] }}</div>
                        </div>
                        <div class="tool-cta pt-1">
                            <span>{{ $tool['cta'] }}</span>
                            <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Secondary ecosystem links --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-gray-200">
            @foreach($secondaryTools as $s)
                <a href="{{ $s['href'] }}" class="subtle-link-card">
                    <span class="editorial-icon-badge">
                        <i class="fas {{ $s['icon'] }} icon-md flex-shrink-0" aria-hidden="true"></i>
                    </span>
                    <div class="min-w-0">
                        <div class="text-sm font-semibold leading-tight text-gray-900">{{ $s['label'] }}</div>
                        <div class="text-xs leading-tight mt-0.5 text-gray-500">{{ $s['sub'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
