@extends('new.layouts.app')

@section('title', 'Free Tools — Bizmark.ID')
@section('description', 'Free business licensing tools from Bizmark.ID: AI License Checker, Cost Estimator, Polygon SHP Maker, Permit Cost Calculator.')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16 lg:pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Free Toolkit</span>
            </span>
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Use the platform yourself. <span class="text-[#0D9488]"><em>At no cost.</em></span>
            </h1>
            <p class="text-base text-[#6B6560] leading-relaxed">Built from <strong>12+ years</strong> of real licensing experience. Run the AI checker, estimate costs, draw polygons — on your own. When you need field execution, our specialist team steps in.</p>
        </div>

        <div class="grid md:grid-cols-2 gap-5 sm:gap-6">
            <a href="/konsultasi-gratis" class="md:col-span-2 card-hover-icon card-elevated p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center gap-6 group">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-[#0D9488] flex items-center justify-center flex-shrink-0 card-icon">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="sm:flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h2 class="text-lg sm:text-xl font-bold text-[#2D2A24]">AI License Checker</h2>
                        <span class="inline-flex text-[11px] font-semibold px-2 py-0.5 rounded-full bg-[#0D9488] text-white">AI · Free</span>
                    </div>
                    <p class="text-sm text-[#6B6560] leading-relaxed">Tell us about your business, and our <strong>AI</strong> will map all the licenses you need — <em>at no cost.</em> Takes about 30 seconds.</p>
                </div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:gap-2.5 transition-all duration-200 flex-shrink-0">
                    Check now
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <a href="/estimasi-biaya" class="card-hover-icon card-borderless p-6 flex flex-col group">
                <div class="card-icon w-12 h-12 rounded-xl bg-[#CCFBF1] flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="inline-flex text-[10px] font-semibold tracking-wider uppercase text-[#0D9488] mb-1">Real-time · AI</span>
                <h2 class="text-base sm:text-lg font-bold text-[#2D2A24] mb-1">Cost Estimator</h2>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4 flex-1"><strong>Select KBLI code</strong>, get estimated costs and processing duration for your permits instantly.</p>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:gap-2.5 transition-all duration-200 mt-auto">
                    Estimate Cost
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <a href="/polygon-shp-maker" class="card-hover-icon card-borderless p-6 flex flex-col group">
                <div class="card-icon w-12 h-12 rounded-xl bg-[#CCFBF1] flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <span class="inline-flex text-[10px] font-semibold tracking-wider uppercase text-[#0D9488] mb-1">OSS-RBA Ready</span>
                <h2 class="text-base sm:text-lg font-bold text-[#2D2A24] mb-1">Polygon SHP Maker</h2>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4 flex-1">Draw your business location on an <strong>interactive map</strong> and export SHP files ready for OSS-RBA.</p>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:gap-2.5 transition-all duration-200 mt-auto">
                    Make SHP
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            <a href="/kalkulator-perizinan" class="md:col-span-2 card-hover-icon card-borderless p-6 flex flex-col sm:flex-row sm:items-center gap-4 group">
                <div class="card-icon w-12 h-12 rounded-xl bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="sm:flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <h2 class="text-base sm:text-lg font-bold text-[#2D2A24]">Permit Cost Calculator</h2>
                        <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59]">50+ permit types</span>
                    </div>
                    <p class="text-sm text-[#6B6560] leading-relaxed">Detailed cost breakdown based on <strong>permit type</strong>, required documents, and estimated processing time.</p>
                </div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:gap-2.5 transition-all duration-200 flex-shrink-0">
                    Calculate Cost
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm text-[#6B6560] mb-4">Need execution help? Our specialist team is ready to step in.</p>
            <a href="/en/inquiry" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:scale-[1.02]">
                Free Consultation
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
@endsection