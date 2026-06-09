@php
    $locale = $locale ?? app()->getLocale();
    $isEn = $locale === 'en';
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
    $primaryCtaRoute = route('landing.service-inquiry.create');
@endphp

{{-- ────────────────────────────────────────────────
     FINAL CTA — editorial closing block
──────────────────────────────────────────────── --}}
<section class="section-v2-sm section-premium relative overflow-hidden final-cta-section" aria-labelledby="final-cta-heading">
    {{-- Decorative background pattern --}}
    <div class="absolute inset-0 pointer-events-none final-cta-pattern" aria-hidden="true">
        <img src="{{ asset('images/illustrations/cta-pattern.svg') }}"
             alt=""
             loading="lazy"
             class="final-cta-pattern-image select-none"
             draggable="false">
    </div>
    <div class="container-wide relative">
        <div class="final-cta-shell max-w-5xl mx-auto text-center" data-aos="fade-up">
            <span class="eyebrow block mb-4 text-center">{{ $isEn ? 'Get Started' : 'Mulai' }}</span>
            <h2 id="final-cta-heading" class="display-xl mb-5">
                {{ $isEn
                    ? 'Ready to move your permits forward?'
                    : 'Siap mengurus perizinan usaha Anda?' }}
            </h2>
            <p class="final-cta-copy text-lg leading-relaxed max-w-2xl mx-auto mb-6 text-gray-600">
                {{ $isEn
                    ? 'Run a free AI permit check in under a minute — or connect with our specialist team for a tailored assessment. No strings attached.'
                    : 'Jalankan cek perizinan AI secara gratis dalam hitungan detik — atau terhubung langsung dengan tim spesialis kami untuk asesmen yang lebih mendalam. Tanpa syarat apa pun.' }}
            </p>

            <div class="final-cta-actions">
                <a href="{{ $primaryCtaRoute }}" class="btn btn-gold btn-lg"
                   @click="if(window.trackEvent) trackEvent('CTA','click','final_cta_primary')">
                    <i class="fas fa-robot" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan AI — Gratis' }}</span>
                </a>
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                   class="btn btn-ghost btn-lg"
                   @click="if(window.trackEvent) trackEvent('CTA','click','final_cta_whatsapp')">
                    <i class="fab fa-whatsapp" aria-hidden="true"></i>
                    <span>{{ $isEn ? 'Chat on WhatsApp' : 'Hubungi via WhatsApp' }}</span>
                </a>
            </div>

            <div class="final-cta-meta" aria-label="{{ $isEn ? 'Capabilities' : 'Kapabilitas' }}">
                <span><i class="fas fa-clock" aria-hidden="true"></i> {{ $isEn ? 'Operating since 2014' : 'Beroperasi sejak 2014' }}</span>
                <span><i class="fas fa-globe" aria-hidden="true"></i> Bilingual ID / EN</span>
                <span><i class="fas fa-map-marked-alt" aria-hidden="true"></i> {{ $isEn ? 'Nationwide coverage' : 'Cakupan se-Indonesia' }}</span>
            </div>
        </div>
    </div>
</section>
