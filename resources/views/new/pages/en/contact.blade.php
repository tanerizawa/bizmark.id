@extends('new.layouts.app')

@section('title', 'Contact — Bizmark.ID')
@section('description', 'Contact the Bizmark.ID team for business licensing consultation.')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Contact Us</h1>
            <p class="text-base text-[#6B6560]">Our team is ready to help. Reach us through any of the channels below.</p>
        </div>

        <div class="grid sm:grid-cols-3 gap-4 sm:gap-5">
            <div class="card-borderless p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-[#2D2A24] mb-1">Email</h3>
                <a href="/cdn-cgi/l/email-protection#8ee7e0e8e1ceece7f4e3effce5a0e7ea" class="text-sm text-[#0D9488] hover:text-[#0F766E]">hello@bizmark.id</a>
            </div>

            <div class="card-borderless p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-[#2D2A24] mb-1">Phone</h3>
                <a href="tel:+6283879602855" class="text-sm text-[#0D9488] hover:text-[#0F766E]">+62 838 7960 2855</a>
            </div>

            <div class="card-borderless p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-sm font-semibold text-[#2D2A24] mb-1">WhatsApp</h3>
                <a href="https://wa.me/6283879602855" target="_blank" rel="noopener" class="text-sm text-[#0D9488] hover:text-[#0F766E]">+62 838 7960 2855</a>
            </div>
        </div>
    </div>
</section>
@endsection