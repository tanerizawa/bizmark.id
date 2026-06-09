@extends('landing.layout')

@section('title', ($article->meta_title ?: $article->title) . ' - Bizmark.ID')
@section('meta_title', $article->meta_title ?: $article->title)
@section('meta_description', $article->meta_description ?: $article->excerpt)
@section('meta_keywords', $article->meta_keywords)
@section('og_title', $article->title)
@section('og_description', $article->excerpt)
@section('twitter_title', $article->title)
@section('twitter_description', $article->excerpt)

@push('head')
<!-- Article Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ $article->title }}",
    "description": "{{ $article->meta_description ?? $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}",
    "image": "{{ $article->featured_image ? asset('storage/' . $article->featured_image) : asset('images/default-article.jpg') }}",
    "datePublished": "{{ ($article->published_at ?? $article->created_at)->toIso8601String() }}",
    "dateModified": "{{ $article->updated_at->toIso8601String() }}",
    "author": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "url": "https://bizmark.id"
    },
    "publisher": {
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "logo": {
            "@@type": "ImageObject",
            "url": "https://bizmark.id/images/logo.png"
        }
    },
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@id": "{{ url()->current() }}"
    }
}
</script>

<!-- Breadcrumb Schema -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@@type": "ListItem",
            "position": 1,
            "name": "Beranda",
            "item": "{{ route('landing.id') }}"
        },
        {
            "@@type": "ListItem",
            "position": 2,
            "name": "Artikel",
            "item": "{{ route('blog.index.id') }}"
        },
        @if($article->category)
        {
            "@@type": "ListItem",
            "position": 3,
            "name": "{{ $article->category->name }}",
            "item": "{{ route('blog.category.id', $article->category->slug) }}"
        },
        @endif
        {
            "@@type": "ListItem",
            "position": {{ $article->category ? 4 : 3 }},
            "name": "{{ $article->title }}",
            "item": "{{ url()->current() }}"
        }
    ]
}
</script>
@endpush

@section('content')
<style>
    .article-hero {
        background: linear-gradient(135deg, #f9fafb 0%, #eef2ff 100%);
    }

    .article-prose {
        font-size: 1.05rem;
        line-height: 1.85;
        color: #334155;
    }

    .article-prose h2,
    .article-prose h3,
    .article-prose h4 {
        color: #0f172a;
        font-weight: 700;
        margin-top: 2.5rem;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .article-prose h3 {
        font-size: 1.5rem;
    }

    .article-prose h4 {
        font-size: 1.25rem;
    }

    .article-prose p {
        margin-bottom: 1.5rem;
        color: #475569;
    }

    .article-prose ul,
    .article-prose ol {
        margin: 1.5rem 0 1.5rem 1.5rem;
        color: #475569;
    }

    .article-prose li {
        margin-bottom: 0.75rem;
    }

    .article-prose a {
        color: #2563eb;
        font-weight: 600;
        text-decoration: underline;
    }

    .article-prose blockquote {
        border-left: 4px solid rgba(37, 99, 235, 0.4);
        padding-left: 1.25rem;
        margin: 2rem 0;
        font-style: italic;
        color: #1e293b;
        background: rgba(37, 99, 235, 0.05);
        border-radius: 0 1rem 1rem 0;
    }

    .article-prose code {
        background: #f1f5f9;
        padding: 0.3rem 0.5rem;
        border-radius: 0.35rem;
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .article-prose pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 1.5rem;
        border-radius: 1rem;
        overflow-x: auto;
        margin: 2rem 0;
    }

    .article-share a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.5rem;
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.25s ease;
    }

    .article-share a:hover {
        transform: translateY(-2px);
    }

    .related-article-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: 1.25rem;
        overflow: hidden;
        background: #ffffff;
    }

    .related-article-card:hover {
        transform: translateY(-6px);
        border-color: rgba(37, 99, 235, 0.3);
        box-shadow: 0 24px 50px -36px rgba(30, 64, 175, 0.6);
    }
</style>

<section class="article-hero pt-36 pb-16">
    <div class="container max-w-4xl">
        <!-- Breadcrumb -->
        <nav class="flex flex-wrap items-center gap-2 text-sm text-slate-500 mb-6">
            <a href="{{ route('landing.id') }}" class="hover:text-primary transition">Beranda</a>
            <span>/</span>
            <a href="{{ route('blog.index.id') }}" class="hover:text-primary transition">Artikel</a>
            <span>/</span>
            <a href="{{ route('blog.category.id', $article->category) }}" class="hover:text-primary transition">
                {{ $article->category_label }}
            </a>
        </nav>

        <!-- Category -->
        <div class="flex flex-wrap items-center gap-3 mb-5">
            <a href="{{ route('blog.category.id', $article->category) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold text-primary bg-primary/10 rounded-full">
                <i class="fas fa-folder-open text-xs"></i>
                {{ $article->category_label }}
            </a>
            @if($article->is_featured)
                <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold text-amber-600 bg-amber-100 rounded-full">
                    <i class="fas fa-star text-xs"></i>
                    Featured
                </span>
            @endif
        </div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-black leading-tight text-slate-900 mb-6">
            {{ $article->title }}
        </h1>

        <!-- Meta -->
        <div class="flex flex-wrap gap-6 text-sm text-slate-500 mb-8">
            <div class="flex items-center gap-3">
                <div class="h-11 w-11 rounded-full bg-primary/10 text-primary font-semibold flex items-center justify-center">
                    {{ strtoupper(substr($article->author->name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-700">{{ $article->author->name }}</p>
                    <p>{{ $article->published_at->format('d F Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-clock text-primary"></i>
                <span>{{ $article->reading_time }} menit baca</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-eye text-primary"></i>
                <span>{{ number_format($article->views_count) }} pembaca</span>
            </div>
        </div>

        @if($article->excerpt)
            <p class="text-xl md:text-2xl text-slate-600 font-medium leading-relaxed border-l-4 border-primary pl-6">
                {{ $article->excerpt }}
            </p>
        @endif
    </div>
</section>

@if($article->featured_image)
    <section class="py-10 bg-white">
        <div class="container max-w-4xl">
            <img src="{{ Storage::url($article->featured_image) }}"
                 alt="{{ $article->title }}"
                 fetchpriority="high" decoding="async"
                 class="w-full rounded-3xl shadow-[0_30px_70px_-60px_rgba(15,23,42,0.6)] object-cover">
        </div>
    </section>
@endif

<section class="pb-16 bg-white">
    <div class="container max-w-4xl">
        <article class="bg-white border border-slate-200 rounded-3xl p-8 md:p-12 shadow-[0_35px_80px_-60px_rgba(15,23,42,0.6)]">
            <div class="article-prose">
                {{-- SAFE: sanitized via HtmlSanitizer whitelist (strips scripts, event handlers, dangerous attrs) --}}
                {!! \App\Support\HtmlSanitizer::clean($article->content) !!}
            </div>

            @if($article->tags && count($article->tags) > 0)
                <div class="mt-12 pt-8 border-t border-slate-200">
                    <h4 class="text-lg font-semibold text-slate-900 mb-4">Tag:</h4>
                    <div class="flex flex-wrap gap-3">
                        @foreach($article->tags as $tag)
                            <a href="{{ route('blog.tag.id', $tag) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-semibold hover:bg-primary/15 transition">
                                #{{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-12 pt-8 border-t border-slate-200">
                <h4 class="text-lg font-semibold text-slate-900 mb-4">Bagikan artikel ini</h4>
                <div class="flex flex-wrap gap-4 article-share">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="bg-slate-100 text-slate-700 hover:bg-slate-200">
                        <i class="fab fa-facebook-f text-primary"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="bg-slate-100 text-slate-700 hover:bg-slate-200">
                        <i class="fab fa-x-twitter text-slate-800"></i>X (Twitter)
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="bg-slate-100 text-slate-700 hover:bg-slate-200">
                        <i class="fab fa-whatsapp text-green-600"></i>WhatsApp
                    </a>
                </div>
            </div>
        </article>
    </div>
</section>

@if($relatedArticles->count() > 0)
    <section class="py-16 bg-[#F7F8FC]">
        <div class="container max-w-6xl">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-10">
                <h3 class="text-3xl font-bold text-slate-900">Artikel Terkait</h3>
                <a href="{{ route('blog.index.id') }}" class="text-primary font-semibold hover:text-primary/80 transition">
                    Lihat semua artikel <i class="fas fa-arrow-right text-xs ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedArticles as $related)
                    <div class="related-article-card">
                        @if($related->featured_image)
                            <img src="{{ Storage::url($related->featured_image) }}"
                                 alt="{{ $related->title }}"
                                 loading="lazy" decoding="async"
                                 class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-br from-primary to-indigo-500 flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-4xl"></i>
                            </div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold">
                                    {{ $related->category_label }}
                                </span>
                                <span class="text-xs text-slate-400">
                                    {{ $related->published_at->format('d M Y') }}
                                </span>
                            </div>

                            <h4 class="text-lg font-bold text-slate-900 mb-3 line-clamp-2">
                                <a href="{{ route('blog.article.id', $related->slug) }}"
                                   class="hover:text-primary transition">
                                    {{ $related->title }}
                                </a>
                            </h4>

                            <p class="text-sm text-slate-500 mb-5 line-clamp-2">
                                {{ $related->excerpt }}
                            </p>

                            <a href="{{ route('blog.article.id', $related->slug) }}"
                               class="inline-flex items-center gap-2 text-primary font-semibold text-sm hover:gap-3 transition">
                                Baca selengkapnya <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

<section class="py-16">
    <div class="container max-w-4xl">
        <div class="rounded-3xl bg-gradient-to-r from-primary via-indigo-500 to-blue-600 text-white p-10 md:p-14 text-center md:text-left space-y-6 shadow-[0_35px_80px_-60px_rgba(15,23,42,0.6)]">
            <h3 class="text-3xl md:text-4xl font-bold leading-tight">Butuh Konsultasi Perizinan?</h3>
            <p class="text-lg md:text-xl text-white/90">
                Tim PT Cangah Pajaratan Mandiri siap membantu kebutuhan perizinan industri Anda dari awal hingga tuntas.
            </p>
            <div class="flex flex-col md:flex-row items-center gap-4">
                <a href="{{ route('consultation.index') }}"
                   class="inline-flex items-center gap-3 px-6 py-3 bg-white text-primary font-semibold rounded-full hover:bg-white/90 transition">
                    <i class="fas fa-calculator text-lg"></i>
                    Estimasi Biaya Gratis
                </a>
                <a href="{{ config('landing_metrics.contact.whatsapp_link') }}?text={{ rawurlencode('Halo PT Cangah Pajaratan Mandiri, saya ingin konsultasi') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-3 px-6 py-3 border border-white/60 rounded-full font-semibold hover:bg-white/10 transition">
                    <i class="fab fa-whatsapp text-lg"></i>
                    Chat WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
