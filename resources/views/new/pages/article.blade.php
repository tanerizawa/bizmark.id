@php
    // Parse headings from article content for dynamic Table of Contents
    $tocItems = [];
    $modifiedContent = $article->content;

    if (preg_match_all('/<h([23])\s*[^>]*>(.*?)<\/h[23]>/si', $article->content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $i => $m) {
            $level = (int)$m[1];
            $text = strip_tags($m[2]);
            $id = 'heading-' . $i . '-' . Str::slug($text);

            $tocItems[] = ['level' => $level, 'text' => $text, 'id' => $id];

            $originalTag = $m[0];
            $replacement = '<h' . $level . ' id="' . $id . '">' . $m[2] . '</h' . $level . '>';
            $modifiedContent = str_replace($originalTag, $replacement, $modifiedContent);
        }
    }
@endphp

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
<style>
.toc-list { list-style: none; padding: 0; margin: 0; }
.toc-list li { margin-bottom: 0.375rem; }
.toc-list a {
    display: block;
    font-size: 0.8125rem;
    color: #6B6560;
    transition: color 0.2s, padding-left 0.2s;
    padding: 0.2rem 0;
    border-left: 2px solid transparent;
    padding-left: 0.5rem;
}
.toc-list a:hover,
.toc-list a.active {
    color: #0D9488;
    border-left-color: #0D9488;
    padding-left: 1rem;
}
.toc-h3 { padding-left: 1rem !important; }
.toc-h3:hover,
.toc-h3.active { padding-left: 1.5rem !important; }
</style>
@endpush

@section('content')
<div class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-[1fr_300px] lg:gap-8 xl:gap-10">

            {{-- Main Content (expanded left) --}}
            <main class="min-w-0 max-w-4xl">
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

                {{-- Content (with heading IDs injected) --}}
                <div class="article-prose">
                    {!! $modifiedContent !!}
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

            {{-- Right Sidebar: Dynamic Table of Contents --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24">
                    @if(count($tocItems) > 0)
                    <nav aria-label="Daftar Isi">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-[#9C9690] mb-3">Daftar Isi</h4>
                        <ul class="toc-list">
                            @foreach($tocItems as $item)
                            <li>
                                <a href="#{{ $item['id'] }}"
                                   class="{{ $item['level'] === 3 ? 'toc-h3' : '' }}"
                                   @click.prevent="
                                       document.getElementById('{{ $item['id'] }}')?.scrollIntoView({ behavior: 'smooth' });
                                       document.querySelectorAll('.toc-list a').forEach(a => a.classList.remove('active'));
                                       $el.classList.add('active');
                                   ">
                                    {{ $item['text'] }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </nav>
                    @endif
                </div>
            </aside>

        </div>
    </div>
</div>
@endsection