@extends('new.layouts.app')

@section('title', 'Proses — Bizmark.ID')
@section('description', 'Proses pengurusan perizinan usaha yang transparan dan terukur dari Bizmark.ID')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Proses Kami</h1>
            <p class="text-base text-[#6B6560]">Proses yang mengutamakan kejelasan dan akuntabilitas di setiap tahap.</p>
        </div>
        @include('new.sections.process')
    </div>
</section>
@endsection
