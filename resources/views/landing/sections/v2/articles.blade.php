@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $blogIndex = $isEn ? route('blog.index.en') : route('blog.index.id');
    $blogShow = $isEn ? 'blog.article.en' : 'blog.article.id';

    // $latestArticles passed from controller (landing.id.index / landing.en.index)
    $articles = collect($latestArticles ?? [])->take(5);

    $categoryLabels = [
        'perizinan-lb3' => $isEn ? 'B3 Waste Permits' : 'Perizinan LB3',
        'amdal' => 'AMDAL',
        'ukl-upl' => 'UKL-UPL',
        'oss-nib' => 'OSS / NIB',
        'izin-operasional' => $isEn ? 'Operational Permits' : 'Izin Operasional',
        'pbg-slf' => 'PBG / SLF',
        'izin-k3' => 'K3',
        'konsultan-lingkungan' => $isEn ? 'Environmental' : 'Konsultan Lingkungan',
        'monitoring-digital' => $isEn ? 'Digital Monitoring' : 'Monitoring Digital',
        'regulation' => $isEn ? 'Regulation' : 'Regulasi',
        'case-study' => $isEn ? 'Case Study' : 'Studi Kasus',
        'news' => $isEn ? 'News' : 'Berita',
        'tips' => 'Tips',
        'general' => $isEn ? 'General' : 'Umum',
    ];

    $fmtCategory = function($cat) use ($categoryLabels) {
        return $categoryLabels[$cat] ?? \Illuminate\Support\Str::title(str_replace('-', ' ', (string)$cat));
    };
@endphp

@if($articles->count() > 0)
{{-- ────────────────────────────────────────────────
     LATEST ARTICLES — from database
     Pulls $latestArticles passed by PublicArticleController
     SEO: adds ArticleList schema + internal linking
──────────────────────────────────────────────── --}}
<section class="section-v2" aria-labelledby="articles-heading">
    <div class="container-wide">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
            <div class="max-w-2xl">
                <span class="eyebrow block mb-3">{{ $isEn ? 'Insights & Expertise' : 'Wawasan & Pengetahuan' }}</span>
                <h2 id="articles-heading" class="display-md mb-3">
                    {{ $isEn ? 'Regulatory insight, written by practitioners.' : 'Wawasan regulasi dari para praktisi perizinan.' }}
                </h2>
                <p class="text-lg leading-relaxed text-gray-600">
                    {{ $isEn
                        ? 'Deep-dive articles on Indonesian permit law, real case breakdowns, and practical compliance playbooks — written by our regulatory specialists.'
                        : 'Ulasan mendalam regulasi perizinan Indonesia, bedah kasus nyata, dan panduan kepatuhan yang langsung bisa diterapkan — ditulis oleh para spesialis regulasi kami.' }}
                </p>
            </div>
            <a href="{{ $blogIndex }}" class="btn btn-outline-primary btn-sm flex-shrink-0">
                <span>{{ $isEn ? 'All articles' : 'Semua artikel' }}</span>
                <i class="fas fa-arrow-right text-xs flex-shrink-0 leading-none" aria-hidden="true"></i>
            </a>
        </div>

        <div class="grid lg:grid-cols-12 gap-6">
            {{-- Featured (left, large) --}}
            @php $featured = $articles->first(); @endphp
            <a href="{{ route($blogShow, $featured->slug) }}" class="article-card featured lg:col-span-7">
                <div class="article-image">
                    @if($featured->featured_image)
                        <img src="{{ \Illuminate\Support\Str::startsWith($featured->featured_image, ['http','/']) ? $featured->featured_image : asset('storage/' . $featured->featured_image) }}"
                             alt="{{ $featured->title }}"
                             loading="lazy" decoding="async" fetchpriority="low"
                             width="800" height="600">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-ink-gradient">
                            <i class="fas fa-newspaper text-4xl text-white/40"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="article-meta mb-3">
                        <span class="article-cat">{{ $fmtCategory($featured->category) }}</span>
                        <span>·</span>
                        <time datetime="{{ optional($featured->published_at)->toIso8601String() }}">
                            {{ optional($featured->published_at)->translatedFormat('d M Y') }}
                        </time>
                        @if($featured->reading_time)
                            <span>·</span>
                            <span>{{ $featured->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}</span>
                        @endif
                    </div>
                    <h3 class="article-title mb-3">{{ $featured->title }}</h3>
                    <p class="article-excerpt">{{ \Illuminate\Support\Str::limit($featured->excerpt ?? strip_tags((string) $featured->content), 180) }}</p>
                </div>
            </a>

            {{-- Sub-artikel (right, text-only list, 4 items) --}}
            <div class="lg:col-span-5 flex flex-col divide-y divide-gray-200 border-t border-gray-200">
                @foreach($articles->slice(1) as $article)
                    <a href="{{ route($blogShow, $article->slug) }}" class="article-list-item">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="article-cat">{{ $fmtCategory($article->category) }}</span>
                            <span class="text-gray-400 text-[.7rem]">·</span>
                            <time datetime="{{ optional($article->published_at)->toIso8601String() }}" class="text-gray-400 text-[.75rem];">
                                {{ optional($article->published_at)->translatedFormat('d M Y') }}
                            </time>
                        </div>
                        <h3 class="article-list-title">{{ $article->title }}</h3>
                        @if($article->reading_time)
                            <span class="article-list-meta">{{ $article->reading_time }} {{ $isEn ? 'min read' : 'menit baca' }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Structured data: ItemList of articles for SEO --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "ItemList",
        "itemListElement": [
            @foreach($articles as $i => $article)
            {
                "@@type": "ListItem",
                "position": {{ $i + 1 }},
                "url": "{{ route($blogShow, $article->slug) }}",
                "name": @json($article->title)
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
</section>
@endif
