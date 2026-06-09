@extends('new.layouts.app')

@section('title', 'Layanan — Bizmark.ID')
@section('description', 'Layanan perizinan usaha lengkap dari Bizmark.ID')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Layanan</h1>
            <p class="text-base text-[#6B6560] max-w-2xl">Layanan perizinan usaha lengkap dari Bizmark.ID, didampingi oleh tim ahli yang berpengalaman.</p>
        </div>

        @if(isset($services) && count($services) > 0)
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @foreach($services as $service)
            <a href="/layanan/{{ $service['slug'] ?? '#' }}" class="card-hover-icon card-borderless p-5 flex flex-col group">
                <h3 class="text-base font-bold text-[#2D2A24] mb-2">{{ $service['title'] ?? $service['name'] ?? 'Layanan' }}</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed flex-1">{{ $service['description'] ?? Str::limit(($service['excerpt'] ?? ''), 120) }}</p>
                <div class="mt-3 flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:gap-2.5 transition-all duration-200">
                    Detail
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-sm text-[#6B6560]">Layanan akan segera hadir.</p>
        @endif
    </div>
</section>
@endsection
