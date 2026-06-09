@extends('new.layouts.app')

@section('title', 'Harga — Bizmark.ID')
@section('description', 'Informasi harga layanan perizinan usaha dari Bizmark.ID')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Harga Layanan</h1>
            <p class="text-base text-[#6B6560]">Dapatkan estimasi biaya yang transparan untuk setiap layanan perizinan. Hubungi tim kami untuk konsultasi gratis.</p>
        </div>
        <div class="text-center py-10">
            <a href="/konsultasi-gratis" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md">
                Dapatkan Estimasi Biaya
            </a>
        </div>
    </div>
</section>
@endsection
