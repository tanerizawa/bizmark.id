@extends('new.layouts.app')

@section('title', ($article->meta_title ?: $article->title) . ' - Bizmark.ID')
@section('description', $article->meta_description ?: $article->excerpt)

@push('styles')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->title }}">
<meta property="og:description" content="{{ $article->excerpt }}">
<meta property="og:image" content="{{ Storage::url($article->featured_image) }}">
<meta name="twitter:title" content="{{ $article->title }}">
<meta name="twitter:description" content="{{ $article->excerpt }}">
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "headline": "{{ $article->title }}",
    "description": "{{ $article->meta_description ?? $article->excerpt ?? Str::limit(strip_tags($article->content), 160) }}",
    "image": "{{ Storage::url($article->featured_image) }}",
    "datePublished": "{{ ($article->published_at ?? $article->created_at)->toIso8601String() }}",
    "dateModified": "{{ $article->updated_at->toIso8601String() }}",
    "author": {"@@type": "Organization", "name": "Bizmark.ID", "url": "https://bizmark.id"},
    "publisher": {"@@type": "Organization", "name": "Bizmark.ID", "logo": {"@@type": "ImageObject", "url": "https://bizmark.id/images/logo.png"}},
    "mainEntityOfPage": {"@@type": "WebPage", "@@id": "{{ url()->current() }}"}
}
</script>
@endpush

@section('content')
<div class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Desktop: 3-column grid --}}
        <div class="lg:grid lg:grid-cols-[280px_1fr_280px] lg:gap-8 xl:gap-10">

            {{-- Left Sidebar (desktop only) --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-6">
                    {{-- Recent Articles --}}
                    @if(isset($recentArticles) && $recentArticles->count() > 0)
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-[#9C9690] mb-3">Artikel Terbaru</h4>
                        <ul class="space-y-2.5">
                            @foreach($recentArticles as $recent)
                            <li>
                                <a href="/blog/{{ $recent->slug }}" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors leading-snug block">{{ $recent->title }}</a>
                                <span class="text-[10px] text-[#9C9690]">{{ $recent->published_at?->format('d M Y') }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="min-w-0">
                {{-- Breadcrumb --}}
                <div class="flex items-center gap-2 text-xs text-[#9C9690] mb-6">
                    <a href="/" class="hover:text-[#0D9488] transition-colors">Beranda</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <a href="/blog" class="hover:text-[#0D9488] transition-colors">Blog</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-[#6B6560]">{{ $article->category ?? 'Artikel' }}</span>
                </div>

                {{-- Header --}}
                <header class="mb-8">
                    @if($article->category)
                    <span class="inline-flex text-[11px] font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59] mb-4">{{ $article->category }}</span>
                    @endif
                    <h1 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold leading-[1.15] tracking-tight text-[#2D2A24] mb-4">{{ $article->title }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-[#6B6560]">
                        @if($article->author)
                        <span class="font-medium text-[#2D2A24]">{{ $article->author->name ?? $article->author }}</span>
                        <span class="text-[#E5E0DB]">·</span>
                        @endif
                        <span>{{ ($article->published_at ?? $article->created_at)->format('d M Y') }}</span>
                        <span class="text-[#E5E0DB]">·</span>
                        <span>{{ $article->reading_time ?? '5' }} menit baca</span>
                    </div>
                </header>

                {{-- Featured Image --}}
                @if($article->featured_image)
                <div class="mb-8 rounded-xl overflow-hidden bg-[#F0ECE6]">
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-auto object-cover" loading="eager">
                </div>
                @endif

                {{-- Content --}}
                <div class="article-prose">
                    {!! $article->content !!}
                </div>

                {{-- Share --}}
                <div class="mt-10 pt-6 border-t border-[#F0ECE6]">
                    <p class="text-sm font-semibold text-[#2D2A24] mb-3">Bagikan artikel ini</p>
                    <div class="flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-[#F0ECE6] flex items-center justify-center text-[#6B6560] hover:bg-[#0D9488] hover:text-white transition-all duration-200" aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385h-3.047v-3.47h3.047v-2.642c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953h-1.514c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385c5.737-.9 10.125-5.864 10.125-11.854z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-[#F0ECE6] flex items-center justify-center text-[#6B6560] hover:bg-[#0D9488] hover:text-white transition-all duration-200" aria-label="Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($article->title) }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-[#F0ECE6] flex items-center justify-center text-[#6B6560] hover:bg-[#0D9488] hover:text-white transition-all duration-200" aria-label="LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </main>

            {{-- Right Sidebar (desktop only) --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 space-y-6">
                    {{-- Categories --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div>
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-[#9C9690] mb-3">Kategori</h4>
                        <ul class="space-y-1.5">
                            @foreach($categories as $cat)
                            <li>
                                <a href="/blog/kategori/{{ urlencode($cat) }}" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">{{ $cat }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Newsletter Mini --}}
                    <div class="p-4 rounded-xl bg-[#FCFAF8] border border-[#F0ECE6]">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-[#9C9690] mb-2">Buletin</h4>
                        <p class="text-xs text-[#6B6560] leading-relaxed mb-3">Ringkasan bulanan perubahan regulasi.</p>
                        <form action="/subscribe" method="POST" class="flex flex-col gap-2">
                            @csrf
                            <input type="email" name="email" required placeholder="Email"
                                   class="px-3 py-2 bg-white border border-[#E5E0DB] rounded-lg text-xs text-[#2D2A24] placeholder:text-[#9C9690] focus:outline-none focus:border-[#0D9488] focus:ring-1 focus:ring-[#0D9488]/20 transition-all">
                            <button type="submit" class="w-full px-3 py-2 bg-[#0D9488] text-white text-xs font-semibold rounded-lg hover:bg-[#0F766E] transition-colors">
                                Langganan
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</div>

{{-- Related Articles --}}
@if(isset($relatedArticles) && $relatedArticles->count() > 0)
<section class="pb-12 sm:pb-16 lg:pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-xl font-bold text-[#2D2A24] mb-6">Artikel Terkait</h2>
            <div class="grid sm:grid-cols-3 gap-4 sm:gap-5">
                @foreach($relatedArticles as $related)
                <a href="/blog/{{ $related->slug }}" class="card-borderless overflow-hidden group">
                    @if($related->featured_image)
                    <div class="aspect-[16/10] overflow-hidden bg-[#F0ECE6]">
                        <img src="{{ Storage::url($related->featured_image) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                    </div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-sm font-bold text-[#2D2A24] leading-snug">{{ $related->title }}</h3>
                        <p class="text-xs text-[#6B6560] mt-1">{{ $related->published_at?->format('d M Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
@include('new.sections.newsletter')
@endsection