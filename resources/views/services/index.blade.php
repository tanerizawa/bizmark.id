@extends('landing.layout')

@section('title', $title ?? 'Layanan Kami - Bizmark.ID')
@section('meta_description', $meta_description ?? 'Layanan lengkap perizinan industri dan konsultasi lingkungan')

@section('content')
@php
    $contact = data_get(config('landing_metrics'), 'contact', []);
    $whatsapp = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $waText = 'Halo, saya ingin konsultasi layanan perizinan';
    $waHref = $whatsapp . (str_contains($whatsapp, '?') ? '&' : '?') . 'text=' . rawurlencode($waText);
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <span class="eyebrow mb-4 inline-block">Layanan</span>
        <h1 class="display-lg mb-4">Solusi Perizinan End-to-End untuk Bisnis Anda</h1>
        <p class="text-lg leading-relaxed max-w-3xl mb-8" style="color: var(--text-secondary);">Pilih layanan sesuai kebutuhan usaha Anda dan pelajari cakupan, proses, persyaratan, serta langkah pelaksanaannya secara lengkap.</p>

        {{-- Tools-First Ribbon --}}
        <a href="{{ route('landing.service-inquiry.create') }}" class="inline-flex items-center gap-3 px-4 py-3 rounded-xl no-underline mb-6 group" style="background: var(--tools-glow); border: 1px solid rgba(var(--tools-rgb),.25); transition: border-color .2s, transform .2s;">
            <span class="editorial-icon-badge is-tools is-circle" style="width:2.25rem;height:2.25rem;flex-shrink:0;">
                <i class="fas fa-robot text-sm" aria-hidden="true"></i>
            </span>
            <span class="flex-1">
                <span class="block text-[10px] font-bold uppercase tracking-[.14em]" style="color: var(--tools);">Belum yakin izin apa?</span>
                <span class="block text-sm font-semibold" style="color: var(--text-primary);">Cek pakai AI gratis dulu — tanpa daftar</span>
            </span>
            <i class="fas fa-arrow-right" style="color: var(--tools);" aria-hidden="true"></i>
        </a>

        <div class="flex flex-wrap gap-3">
            <a href="{{ $waHref }}" class="btn btn-secondary"><i class="fab fa-whatsapp"></i> Konsultasi Gratis — Langsung Jawab</a>
            <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-ghost"><i class="fas fa-robot"></i> Cek Kebutuhan Izin via AI</a>
        </div>
    </div>
</section>

{{-- Sticky category anchor nav — gold active indicator --}}
<div class="sticky top-16 z-30 bg-white border-b border-gray-200 shadow-sm"
     x-data="{ activeCat: '' }"
     x-init="
         const observer = new IntersectionObserver((entries) => {
             entries.forEach(entry => {
                 if (entry.isIntersecting) activeCat = entry.target.id.replace('cat-', '');
             });
         }, { rootMargin: '-120px 0px 0px 0px' });
         document.querySelectorAll('[id^=cat-]').forEach(el => observer.observe(el));
     ">
    <div class="container-wide overflow-x-auto">
        <div class="flex items-center gap-1 py-2 min-w-max">
            @foreach($groupedServices as $categoryName => $_)
            @php $catSlug = \Str::slug($categoryName); @endphp
            <a href="#cat-{{ $catSlug }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all duration-200 border"
               :class="activeCat === '{{ $catSlug }}' ? 'text-amber-800 bg-amber-50 border-amber-300 shadow-sm' : 'text-gray-600 hover:text-amber-700 hover:bg-amber-50 border-transparent hover:border-amber-200'">
                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                      :class="activeCat === '{{ $catSlug }}' ? 'bg-amber-500' : 'bg-transparent'"
                      aria-hidden="true"></span>
                {{ $categoryName }}
            </a>
            @endforeach
        </div>
    </div>
</div>

<section class="section overflow-hidden">
    <div class="container-wide">
        @foreach($groupedServices as $categoryName => $items)
            @php
                $totalInCat = count($items);
                $displayItems = $totalInCat > 3 ? array_slice($items, 0, 3, true) : $items;
                $catSlug = \Str::slug($categoryName);
                $catBg = $loop->even ? 'bg-[var(--surface-warm)]' : '';
            @endphp
            <div id="cat-{{ $catSlug }}" class="mb-14 last:mb-0 scroll-mt-24 rounded-2xl {{ $catBg }} p-6 -mx-6">
                {{-- Category header: badge + count inline (no repetitive h2) --}}
                <div class="flex items-center gap-3 mb-6">
                    <span class="eyebrow">{{ $categoryName }}</span>
                    <span class="text-sm" style="color: var(--text-muted);">{{ $totalInCat }} layanan tersedia</span>
                    <h2 class="sr-only">{{ $categoryName }}</h2>
                </div>

                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($displayItems as $slug => $service)
                        <article class="premium-card h-full flex flex-col">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <span class="editorial-icon-badge" style="width:3rem;height:3rem;border-radius:.75rem;flex-shrink:0;">
                                    <i class="fas {{ $service['icon'] ?? 'fa-layer-group' }} icon-md" aria-hidden="true"></i>
                                </span>
                                @if(!empty($service['badge']))
                                    <span class="badge-featured">{{ $service['badge'] }}</span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold mb-2 card-title line-clamp-2" style="color: var(--text-primary);">{{ $service['title'] ?? '' }}</h3>
                            <p class="text-sm mb-4 line-clamp-3" style="color: var(--text-secondary);">{{ $service['short_description'] ?? '' }}</p>

                            @if(!empty($service['process_time']) || !empty($service['price_range']))
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @if(!empty($service['process_time']))
                                        <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(15,23,42,.05); color: var(--text-secondary);"><i class="fas fa-clock mr-1"></i>{{ $service['process_time'] }}</span>
                                    @endif
                                    @if(!empty($service['price_range']))
                                        <span class="text-xs px-2 py-1 rounded-full font-semibold" style="background: var(--accent-glow); color: var(--accent-text);"><i class="fas fa-tag mr-1"></i>{{ $service['price_range'] }}</span>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto pt-3 space-y-2" style="border-top: 1px solid var(--border-subtle);">
                                <a href="{{ route('services.show.id', $slug) }}" class="link-primary text-sm inline-flex items-center w-full">
                                    Pelajari Lebih Lanjut <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                <a href="https://wa.me/6283879602855?text={{ rawurlencode('Halo, saya tertarik layanan ' . ($service['title'] ?? '') . '. Bisa jelaskan proses dan biayanya?') }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="btn btn-primary w-full btn-sm">
                                    <i class="fab fa-whatsapp"></i> Tanya Biaya & Proses
                                </a>
                            </div>
                        </article>
                    @endforeach

                    {{-- "Lihat Layanan Lainnya" card — only when category > 3 --}}
                    @if($totalInCat > 3)
                        <a href="{{ route('services.category.id', $catSlug) }}"
                           class="premium-card h-full flex flex-col items-center justify-center text-center border-dashed hover:border-amber-400 hover:bg-amber-50/30 transition-all duration-200 group min-h-[220px]">
                            <span class="editorial-icon-badge mb-4 group-hover:scale-110 transition-transform" style="width:3rem;height:3rem;border-radius:.75rem;">
                                <i class="fas fa-layer-group icon-md" aria-hidden="true"></i>
                            </span>
                            <p class="text-sm font-semibold mb-1" style="color: var(--text-primary);">
                                +{{ $totalInCat - 3 }} Layanan Lainnya
                            </p>
                            <p class="text-xs mb-3" style="color: var(--text-muted);">di kategori {{ $categoryName }}</p>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full" style="background: var(--accent-glow); color: var(--accent-text);">
                                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="display-md mb-3" style="color: var(--text-primary);">Perlu Rekomendasi Layanan yang Paling Tepat?</h2>
        <p class="mb-7" style="color: var(--text-secondary);">Ceritakan konteks usaha Anda, kami bantu susun peta izin prioritas.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $waHref }}" class="btn btn-primary"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-ghost"><i class="fas fa-robot"></i> Analisis Perizinan AI</a>
        </div>
    </div>
</section>
@endsection
