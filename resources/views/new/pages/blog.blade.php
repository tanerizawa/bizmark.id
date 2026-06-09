@extends('new.layouts.app')

@section('title', 'Blog — Bizmark.ID')
@section('description', 'Wawasan dan panduan perizinan usaha dari para praktisi Bizmark.ID')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Blog</h1>
            <p class="text-base text-[#6B6560]">Wawasan dan panduan perizinan usaha dari para praktisi Bizmark.ID.</p>
        </div>

        @if(isset($articles) && $articles->count() > 0)
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($articles as $article)
            <a href="/blog/{{ $article->slug }}" class="card-borderless overflow-hidden group">
                @if($article->featured_image)
                <div class="aspect-[16/10] overflow-hidden bg-[#F0ECE6]">
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                </div>
                @endif
                <div class="p-5">
                    @if($article->category)
                    <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59] mb-2">{{ $article->category }}</span>
                    @endif
                    <h3 class="text-sm font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors leading-snug mb-1">{{ $article->title }}</h3>
                    <p class="text-xs text-[#6B6560]">{{ $article->published_at?->format('d M Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>

        @if(method_exists($articles, 'links'))
        <div class="mt-8">
            {{ $articles->links() }}
        </div>
        @endif
        @else
        <p class="text-sm text-[#6B6560]">Belum ada artikel.</p>
        @endif
    </div>
</section>
@endsection
