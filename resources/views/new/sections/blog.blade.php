@if(isset($latestArticles) && $latestArticles->count() > 0)
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24]">Wawasan & Pengetahuan</h2>
                <p class="text-base text-[#6B6560] mt-2">Ulasan mendalam regulasi perizinan dari para praktisi.</p>
            </div>
            <a href="/blog" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] hover:text-[#0F766E]">
                Semua artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-4 sm:gap-5">
            @foreach($latestArticles as $article)
            <a href="/blog/{{ $article->slug }}" class="card-borderless overflow-hidden group">
                @if($article->featured_image)
                <div class="aspect-[16/10] overflow-hidden bg-[#F0ECE6]">
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" loading="lazy">
                </div>
                @endif
                <div class="p-5">
                    @if($article->category)
                    <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59] mb-2">{{ $article->category }}</span>
                    @endif
                    <h3 class="text-sm font-bold text-[#2D2A24] leading-snug mb-1">{{ $article->title }}</h3>
                    <p class="text-xs text-[#6B6560]">{{ $article->published_at?->format('d M Y') }} · {{ $article->reading_time ?? '5' }} menit baca</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6 text-center sm:hidden">
            <a href="/blog" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488]">Semua artikel <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></a>
        </div>
    </div>
</section>
@endif
