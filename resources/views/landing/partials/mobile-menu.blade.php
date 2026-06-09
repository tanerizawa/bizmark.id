@php
    $locale = app()->getLocale();
    $landingUrl = $locale === 'en' ? route('landing.en') : route('landing.id');
    $servicesUrl = $locale === 'en' ? route('services.index.en') : route('services.index.id');
    $processUrl = $locale === 'en' ? route('process.en') : route('process.id');
    $aboutUrl = $locale === 'en' ? route('about.en') : route('about.id');
    $blogUrl = $locale === 'en' ? route('blog.index.en') : route('blog.index.id');
    $aiCheckerUrl = route('landing.service-inquiry.create');
    $landingMetrics = config('landing_metrics');
    $contact = $landingMetrics['contact'] ?? [];
    $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
    $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
    $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
    $emailAddress = $contact['email'] ?? 'info@bizmark.id';
@endphp

<div x-data="{ open: false }"
     x-show="open"
     x-cloak
     @open-mobile-menu.window="open = true"
     @close-mobile-menu.window="open = false"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-[60]"
     role="dialog"
     aria-label="Mobile navigation menu"
     aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm"
         x-show="open"
         x-transition.opacity
         @click="open = false"></div>

    <div class="fixed top-0 right-0 w-80 max-w-[88vw] h-full shadow-2xl bg-[var(--surface-dark)]"
         x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">
        <div class="p-6 h-full flex flex-col overflow-y-auto overscroll-contain">
            <div class="flex justify-between items-center mb-7">
                <div id="mobile-menu-title" class="text-base font-semibold tracking-wide uppercase text-white/80">{{ $locale === 'id' ? 'Navigasi' : 'Navigation' }}</div>
                <button @click="open = false"
                        class="text-white/90 hover:bg-white/10 rounded-lg p-2.5 -mr-1 transition active:scale-95 min-w-[44px] min-h-[44px] flex items-center justify-center"
                        aria-label="{{ $locale === 'id' ? 'Tutup menu navigasi' : 'Close navigation menu' }}">
                    <i class="fas fa-times text-lg" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="flex flex-col space-y-1 flex-1" role="navigation" aria-labelledby="mobile-menu-title">
                <a href="{{ $landingUrl }}"
                   class="text-white/95 hover:text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-home w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.home') }}</span>
                </a>

                {{-- TOOLS — promoted ke atas, emerald accent untuk signal platform-first --}}
                <div class="px-4 pt-3 pb-1 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: var(--tools);" aria-hidden="true"></span>
                    <p class="text-white/70 text-[11px] uppercase tracking-wider font-bold">
                        {{ $locale === 'en' ? 'Free Tools' : 'Alat Gratis' }}
                    </p>
                    <span class="text-white/40 text-[10px]">·</span>
                    <span class="text-white/40 text-[10px]">{{ $locale === 'en' ? 'No login' : 'Tanpa daftar' }}</span>
                </div>
                <a href="{{ route('permohonan.index') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px] border"
                   style="background: rgba(52,211,153,.08); border-color: rgba(52,211,153,.22);"
                   @click="open = false">
                    <i class="fas fa-file-invoice-dollar w-6 inline-block" style="color: var(--tools-soft);" aria-hidden="true"></i>
                    <span class="ml-1 font-semibold">{{ $locale === 'en' ? 'Permit Application' : 'Layanan Permohonan Izin' }}</span>
                </a>
                <a href="{{ route('consultation.index') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-brain w-6 inline-block" style="color: var(--tools-soft);" aria-hidden="true"></i>
                    <span class="ml-1">{{ $locale === 'en' ? 'Cost Estimator' : 'Estimasi Biaya' }}</span>
                </a>
                <a href="{{ route('polygon.shp.index') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-draw-polygon w-6 inline-block" style="color: var(--tools-soft);" aria-hidden="true"></i>
                    <span class="ml-1">Polygon SHP Maker</span>
                </a>
                <a href="{{ route('calculator.index') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-calculator w-6 inline-block" style="color: var(--tools-soft);" aria-hidden="true"></i>
                    <span class="ml-1">{{ $locale === 'en' ? 'Cost Calculator' : 'Kalkulator Biaya' }}</span>
                </a>

                {{-- SERVICES + PROCESS --}}
                <div class="px-4 pt-4 pb-1 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: var(--accent-soft);" aria-hidden="true"></span>
                    <p class="text-white/70 text-[11px] uppercase tracking-wider font-bold">
                        {{ $locale === 'en' ? 'With Our Team' : 'Didampingi Ahli' }}
                    </p>
                </div>
                <a href="{{ $servicesUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-briefcase w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.services') }}</span>
                </a>
                <a href="{{ $processUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-tasks w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.process') }}</span>
                </a>
                <a href="{{ $locale === 'en' ? route('pricing.en') : route('pricing.id') }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-tag w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ $locale === 'en' ? 'Pricing' : 'Harga' }}</span>
                </a>
                {{-- RESOURCES --}}
                <div class="px-4 pt-4 pb-1">
                    <p class="text-white/70 text-[11px] uppercase tracking-wider font-bold">{{ $locale === 'en' ? 'Resources' : 'Sumber Daya' }}</p>
                </div>
                <a href="{{ $blogUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-newspaper w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.blog') }}</span>
                </a>
                <a href="{{ $aboutUrl }}"
                   class="text-white transition px-4 py-3.5 rounded-lg hover:bg-white/10 active:bg-white/20 flex items-center min-h-[44px]"
                   @click="open = false">
                    <i class="fas fa-info-circle w-6 inline-block text-white/70" aria-hidden="true"></i>
                    <span class="ml-1">{{ __('landing.nav.about') }}</span>
                </a>

                <div class="border-t border-white/15 my-4"></div>
                <div class="px-4 py-3">
                    <p class="text-white/60 text-xs uppercase tracking-wider font-semibold mb-3">{{ __('landing.footer.language') }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('locale.set', 'id') }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ app()->getLocale() === 'id' ? 'bg-white font-semibold text-[var(--color-primary)]' : 'bg-white/10 text-white hover:bg-white/20 active:bg-white/30' }}">
                            <span class="font-bold text-sm">ID</span>
                            <span class="text-sm">Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}"
                           class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ app()->getLocale() === 'en' ? 'bg-white font-semibold text-[var(--color-primary)]' : 'bg-white/10 text-white hover:bg-white/20 active:bg-white/30' }}">
                            <span class="font-bold text-sm">EN</span>
                            <span class="text-sm">English</span>
                        </a>
                    </div>
                </div>

                @if(auth('client')->check())
                    <a href="{{ route('client.dashboard') }}"
                       class="mx-4 mt-4 block text-center px-6 py-3.5 rounded-lg font-bold transition min-h-[44px] active:scale-[0.98] text-white"
                       style="background: var(--accent); box-shadow: 0 4px 14px rgba(var(--accent-rgb),.4);"
                       @click="open = false">
                        <i class="fas fa-gauge-high mr-1"></i> {{ auth('client')->user()->name }}
                    </a>
                    <form method="POST" action="{{ route('client.logout') }}" class="mx-4 mt-2">
                        @csrf
                        <button type="submit" class="w-full text-center px-6 py-2.5 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition text-white text-sm min-h-[44px]">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                @elseif(auth('web')->check())
                    <a href="/dashboard"
                       class="mx-4 mt-4 block text-center px-6 py-3.5 rounded-lg font-bold transition min-h-[44px] active:scale-[0.98] text-white"
                       style="background: var(--accent); box-shadow: 0 4px 14px rgba(var(--accent-rgb),.4);"
                       @click="open = false">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                @else
                    <a href="/login"
                       class="mx-4 mt-4 block text-center px-6 py-3.5 rounded-lg font-bold transition min-h-[44px] active:scale-[0.98] text-white"
                       style="background: var(--accent); box-shadow: 0 4px 14px rgba(var(--accent-rgb),.4);"
                       @click="open = false">
                        <i class="fas fa-user-lock mr-1"></i> {{ $locale === 'id' ? 'Masuk ke Portal' : 'Sign In' }}
                    </a>
                @endif
            </nav>

            <div class="pt-6 border-t border-white/15 mt-auto">
                <p class="text-white/80 text-sm mb-3">{{ __('landing.footer.contact_us') }}:</p>
                <a href="{{ $whatsappLink }}"
                   class="text-white flex items-center gap-3 mb-2 py-2 min-h-[44px] active:opacity-80"
                   target="_blank"
                   rel="noopener noreferrer">
                    <span class="inline-grid place-items-center w-5 h-5 flex-shrink-0">
                        <i class="fab fa-whatsapp text-lg leading-none" aria-hidden="true"></i>
                    </span>
                    <span class="text-sm">{{ $phoneNumber }}</span>
                </a>
                <a href="mailto:{{ $emailAddress }}"
                   class="text-white flex items-center gap-3 py-2 min-h-[44px] active:opacity-80">
                    <span class="inline-grid place-items-center w-5 h-5 flex-shrink-0">
                        <i class="fas fa-envelope text-lg leading-none" aria-hidden="true"></i>
                    </span>
                    <span class="text-sm">{{ $emailAddress }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
