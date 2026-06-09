@php
    $currentLocale = app()->getLocale();
    $landingUrl = $currentLocale === 'en' ? route('landing.en') : route('landing.id');
    $servicesUrl = $currentLocale === 'en' ? route('services.index.en') : route('services.index.id');
    $processUrl = $currentLocale === 'en' ? route('process.en') : route('process.id');
    $aboutUrl = $currentLocale === 'en' ? route('about.en') : route('about.id');
    $blogUrl = $currentLocale === 'en' ? route('blog.index.en') : route('blog.index.id');
    $pricingUrl = $currentLocale === 'en' ? route('pricing.en') : route('pricing.id');
    $aiCheckerUrl = route('landing.service-inquiry.create');
@endphp

{{-- Simplified navbar — flat links, no mega-dropdowns. --}}
<nav class="app-navbar fixed top-0 left-0 right-0 z-50"
     role="navigation"
     aria-label="{{ $currentLocale === 'id' ? 'Navigasi utama' : 'Main navigation' }}"
     x-data="{ localeMenu: false }">
    <div class="container-wide">
        <div class="app-navbar-inner">
            <a href="{{ $landingUrl }}" class="brand-mark" aria-label="Bizmark.ID">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-8 w-auto" loading="eager" fetchpriority="high">
                <span class="font-bold text-xl tracking-tight">Bizmark.ID</span>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ $servicesUrl }}"
                   class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                    {{ __('landing.nav.services') }}
                </a>

                <a href="{{ $processUrl }}"
                   class="nav-link {{ request()->routeIs('process.*') ? 'active' : '' }}">
                    {{ __('landing.nav.process') }}
                </a>

                <a href="{{ $pricingUrl }}"
                   class="nav-link {{ request()->routeIs('pricing.*') ? 'active' : '' }}">
                    {{ $currentLocale === 'en' ? 'Pricing' : 'Harga' }}
                </a>

                <a href="{{ $aboutUrl }}"
                   class="nav-link {{ request()->routeIs('about.*') ? 'active' : '' }}">
                    {{ __('landing.nav.about') }}
                </a>

                <a href="{{ $blogUrl }}"
                   class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                    {{ __('landing.nav.blog') }}
                </a>
            </div>

            <div class="hidden md:flex items-center gap-2">
                {{-- Locale toggle (compact) --}}
                <div class="relative"
                     @click.outside="localeMenu = false"
                     @keydown.escape.window="localeMenu = false">
                    <button type="button"
                            class="nav-control-btn"
                            :aria-expanded="localeMenu"
                            aria-haspopup="true"
                            @click="localeMenu = !localeMenu">
                        <span>{{ $currentLocale === 'en' ? 'EN' : 'ID' }}</span>
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </button>
                    <div x-show="localeMenu"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         class="nav-dropdown"
                         role="menu">
                        <a href="{{ route('locale.set', 'id') }}" role="menuitem"
                           class="nav-dropdown-item {{ $currentLocale === 'id' ? 'active' : '' }}">
                            <span>ID — Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" role="menuitem"
                           class="nav-dropdown-item {{ $currentLocale === 'en' ? 'active' : '' }}">
                            <span>EN — English</span>
                        </a>
                    </div>
                </div>

                {{-- Auth + Primary CTA --}}
                @if(auth('client')->check())
                    <a href="{{ route('client.dashboard') }}" class="btn btn-gold btn-sm">
                        <i class="fas fa-gauge-high"></i>
                        <span>Portal</span>
                    </a>
                @elseif(auth('web')->check())
                    <a href="/dashboard" class="btn btn-gold btn-sm">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="nav-link nav-link--auth"
                       aria-label="{{ $currentLocale === 'id' ? 'Masuk ke akun' : 'Sign in' }}">
                        <i class="fas fa-arrow-right-to-bracket" aria-hidden="true"></i>
                        <span>{{ $currentLocale === 'id' ? 'Masuk' : 'Sign In' }}</span>
                    </a>
                    <a href="{{ route('permohonan.index') }}" class="btn btn-gold btn-sm">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>{{ $currentLocale === 'id' ? 'Layanan Permohonan Izin' : 'Permit Application Services' }}</span>
                    </a>
                @endif
            </div>

            <button class="md:hidden nav-control-btn"
                    @click="$dispatch('open-mobile-menu')"
                    aria-label="{{ $currentLocale === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>
