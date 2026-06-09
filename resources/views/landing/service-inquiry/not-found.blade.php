<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Hasil Tidak Ditemukan | Bizmark.ID</title>

    <!-- Google Fonts preconnect (font loaded via Vite public.css) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Vite: Tailwind CSS + Font Awesome + Alpine.js (dari npm, bukan CDN) -->
    @vite(['resources/css/public.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .bg-mesh {
            background-image:
                radial-gradient(at 20% 20%, rgba(10, 102, 194, 0.06) 0%, transparent 50%),
                radial-gradient(at 80% 80%, rgba(249, 115, 22, 0.04) 0%, transparent 50%);
        }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="font-sans bg-white text-gray-900 min-h-screen flex flex-col">

    @php
        $contact = config('landing_metrics.contact');
        $currentLocale = app()->getLocale();
        $landingUrl = $currentLocale === 'en' ? route('landing.en') : route('landing.id');
        $blogUrl = $currentLocale === 'en' ? route('blog.index.en') : route('blog.index.id');
    @endphp

    <!-- Navigation -->
    <div x-data="{ mobileOpen: false, localeOpen: false }"
         @keydown.escape.window="mobileOpen = false; localeOpen = false"
         class="no-print">
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-200 shadow-sm" role="navigation" aria-label="{{ $currentLocale === 'id' ? 'Navigasi utama' : 'Main navigation' }}">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ $landingUrl }}" class="text-xl font-bold text-gray-900">
                        <i class="fas fa-certificate mr-2 text-blue-600"></i>Bizmark.ID
                    </a>
                    <div class="hidden md:flex items-center space-x-2 lg:space-x-3">
                        <a href="{{ $landingUrl }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">{{ __('landing.nav.home') }}</a>
                        <a href="{{ $currentLocale === 'en' ? route('services.index.en') : route('services.index.id') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">{{ __('landing.nav.services') }}</a>
                        <a href="{{ $currentLocale === 'en' ? route('process.en') : route('process.id') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">{{ __('landing.nav.process') }}</a>
                        <a href="{{ $currentLocale === 'en' ? route('about.en') : route('about.id') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">{{ __('landing.nav.about') }}</a>
                        <a href="{{ $blogUrl }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">{{ __('landing.nav.blog') }}</a>
                        <!-- Locale Switcher (Alpine.js) -->
                        <div class="relative inline-block text-left" @click.outside="localeOpen = false">
                            <button type="button" @click="localeOpen = !localeOpen"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border transition-colors text-gray-600 border-gray-200 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-600"
                                    :aria-expanded="localeOpen"
                                    aria-haspopup="true"
                                    aria-label="{{ $currentLocale === 'id' ? 'Ganti bahasa' : 'Change language' }}">
                                <span class="text-base">{{ $currentLocale === 'en' ? '🇬🇧' : '🇮🇩' }}</span>
                                <span class="hidden sm:inline">{{ $currentLocale === 'en' ? 'EN' : 'ID' }}</span>
                                <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="localeOpen"
                                 x-transition
                                 x-cloak
                                 class="absolute right-0 z-50 w-48 mt-2 origin-top-right bg-white border border-gray-200 rounded-lg shadow-lg ring-1 ring-black/5"
                                 role="menu">
                                <div class="py-1">
                                    <a href="{{ route('locale.set', 'id') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'id' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}" role="menuitem">
                                        <span class="text-lg">🇮🇩</span><span>Bahasa Indonesia</span>
                                        @if($currentLocale === 'id')<i class="fas fa-check ml-auto text-blue-600 text-xs"></i>@endif
                                    </a>
                                    <a href="{{ route('locale.set', 'en') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors {{ $currentLocale === 'en' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}" role="menuitem">
                                        <span class="text-lg">🇬🇧</span><span>English</span>
                                        @if($currentLocale === 'en')<i class="fas fa-check ml-auto text-blue-600 text-xs"></i>@endif
                                    </a>
                                </div>
                            </div>
                        </div>
                        <a href="{{ $landingUrl }}#contact" class="ml-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_25px_rgba(10,102,194,0.25)]">
                            {{ __('landing.nav.get_started') }}
                        </a>
                    </div>
                    <!-- Mobile hamburger -->
                    <button @click="mobileOpen = !mobileOpen"
                            class="md:hidden p-2.5 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-blue-600 transition min-w-[44px] min-h-[44px] flex items-center justify-center"
                            :aria-expanded="mobileOpen"
                            aria-label="{{ $currentLocale === 'id' ? 'Buka menu navigasi' : 'Open navigation menu' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileOpen"
             x-transition.opacity
             x-cloak
             class="fixed inset-0 bg-black/30 z-[55]"
             @click="mobileOpen = false"
             aria-hidden="true"></div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 z-[60] w-80 max-w-[85vw] bg-white shadow-xl"
             role="dialog"
             aria-label="{{ $currentLocale === 'id' ? 'Menu navigasi' : 'Navigation menu' }}"
             aria-modal="true">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <span class="text-lg font-bold text-gray-900"><i class="fas fa-certificate mr-2 text-blue-600"></i>Menu</span>
                <button @click="mobileOpen = false"
                        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition min-w-[44px] min-h-[44px] flex items-center justify-center"
                        aria-label="{{ $currentLocale === 'id' ? 'Tutup menu' : 'Close menu' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex flex-col h-[calc(100%-65px)] overflow-y-auto">
                <div class="p-4 space-y-1 flex-1">
                    <a href="{{ $landingUrl }}" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition min-h-[44px]"><i class="fas fa-home w-5 text-center text-gray-400"></i>{{ __('landing.nav.home') }}</a>
                    <a href="{{ $landingUrl }}#services" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition min-h-[44px]"><i class="fas fa-briefcase w-5 text-center text-gray-400"></i>{{ __('landing.nav.services') }}</a>
                    <a href="{{ $landingUrl }}#process" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition min-h-[44px]"><i class="fas fa-tasks w-5 text-center text-gray-400"></i>{{ __('landing.nav.process') }}</a>
                    <a href="{{ $landingUrl }}#about" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition min-h-[44px]"><i class="fas fa-info-circle w-5 text-center text-gray-400"></i>{{ __('landing.nav.about') }}</a>
                    <a href="{{ $blogUrl }}" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition min-h-[44px]"><i class="fas fa-newspaper w-5 text-center text-gray-400"></i>{{ __('landing.nav.blog') }}</a>
                    <a href="{{ route('landing.service-inquiry.create') }}" @click="mobileOpen = false" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg min-h-[44px]"><i class="fas fa-robot w-5 text-center"></i>Analisis AI</a>
                </div>
                <div class="px-4 py-3 border-t border-gray-100">
                    <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-3">{{ __('landing.footer.language') }}</p>
                    <div class="flex gap-2">
                        <a href="{{ route('locale.set', 'id') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ $currentLocale === 'id' ? 'bg-blue-50 font-semibold text-blue-700' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                            <span>🇮🇩</span><span class="text-sm">Indonesia</span>
                        </a>
                        <a href="{{ route('locale.set', 'en') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg transition min-h-[44px] {{ $currentLocale === 'en' ? 'bg-blue-50 font-semibold text-blue-700' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }}">
                            <span>🇬🇧</span><span class="text-sm">English</span>
                        </a>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100">
                    <a href="{{ $landingUrl }}#contact" @click="mobileOpen = false" class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition min-h-[44px]"><i class="fas fa-paper-plane"></i>{{ __('landing.nav.get_started') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <main class="flex-1 flex items-center justify-center px-4 pt-24 pb-12 bg-gradient-to-b from-[#FDFBF8] via-[#FDFBF8] to-[#F5F3F8] bg-mesh relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-blue-100/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-orange-400/10 rounded-full blur-3xl"></div>

        <div class="max-w-md w-full text-center relative z-10 animate-[fadeIn_0.6s_ease_forwards]">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sm:p-12">
                <div class="w-20 h-20 mx-auto mb-6 bg-blue-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-blue-400"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 mb-3">Hasil Tidak Ditemukan</h1>
                <p class="text-gray-500 mb-2">
                    Nomor inquiry <strong class="text-gray-700 font-mono">{{ $inquiryNumber }}</strong> tidak ditemukan dalam sistem kami.
                </p>
                <p class="text-gray-400 text-sm mb-8">
                    Pastikan Anda menggunakan link yang benar dari email konfirmasi, atau coba analisis baru.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('landing.service-inquiry.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-[0_10px_25px_rgba(10,102,194,0.25)]">
                        <i class="fas fa-plus-circle"></i> Analisis Baru
                    </a>
                    <a href="{{ route('landing.id') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        <i class="fas fa-home"></i> Beranda
                    </a>
                </div>
            </div>

            <p class="mt-6 text-xs text-gray-400">
                Butuh bantuan? <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline"><i class="fab fa-whatsapp"></i> Hubungi kami</a>
            </p>
        </div>
    </main>

    <!-- Footer -->
    @php
        $phoneNumber = $contact['phone'] ?? '+62 838 7960 2855';
        $whatsappNumber = $contact['whatsapp'] ?? '6283879602855';
        $whatsappLink = $contact['whatsapp_link'] ?? ('https://wa.me/' . $whatsappNumber);
        $emailAddress = $contact['email'] ?? 'info@bizmark.id';
    @endphp
    <footer class="bg-gray-900 text-gray-300 py-8 mt-auto no-print">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="text-xl font-bold text-white mb-4"><i class="fas fa-certificate mr-2 text-blue-400"></i>Bizmark.ID</div>
                    <p class="text-sm text-gray-300">{{ __('landing.footer.tagline') }}</p>
                    <div class="mt-4 flex gap-4">
                        <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-400 transition" aria-label="LinkedIn"><i class="fab fa-linkedin text-2xl"></i></a>
                        <a href="https://www.facebook.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-400 transition" aria-label="Facebook"><i class="fab fa-facebook text-2xl"></i></a>
                        <a href="https://www.instagram.com/bizmark.id" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-blue-400 transition" aria-label="Instagram"><i class="fab fa-instagram text-2xl"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.navigation') }}</h4>
                    @php
                        $footerLandingRoute = app()->getLocale() === 'en' ? route('landing.en') : route('landing.id');
                        $footerBlogRoute = app()->getLocale() === 'en' ? route('blog.index.en') : route('blog.index.id');
                    @endphp
                    <ul class="space-y-1 text-sm">
                        <li><a href="{{ $footerLandingRoute }}#services" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.services') }}</a></li>
                        <li><a href="{{ $footerLandingRoute }}#process" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.process') }}</a></li>
                        <li><a href="{{ $footerLandingRoute }}#about" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.about') }}</a></li>
                        <li><a href="{{ $footerBlogRoute }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.nav.blog') }}</a></li>
                        <li><a href="{{ route('career.index') }}" class="text-gray-300 hover:text-white transition inline-block py-1.5 min-h-[44px] flex items-center">{{ __('landing.footer.careers') }}</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.contact_us') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="mailto:{{ $emailAddress }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-envelope mr-2"></i>{{ $emailAddress }}</a></li>
                        <li><a href="tel:{{ str_replace(' ', '', $phoneNumber) }}" class="text-gray-300 hover:text-white transition"><i class="fas fa-phone mr-2"></i>{{ $phoneNumber }}</a></li>
                        <li><a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition inline-block py-1.5"><i class="fab fa-whatsapp mr-2"></i>{{ __('landing.footer.whatsapp') }}</a></li>
                        <li class="text-gray-300 py-1.5"><i class="fas fa-map-marker-alt mr-2"></i>{{ __('landing.footer.location') }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('landing.footer.legal') }}</h4>
                    <ul class="space-y-2 text-sm">
                        @php
                            $privacyRoute = app()->getLocale() === 'en' ? route('privacy.policy.en') : route('privacy.policy.id');
                            $termsRoute = app()->getLocale() === 'en' ? route('terms.conditions.en') : route('terms.conditions.id');
                        @endphp
                        <li><a href="{{ $privacyRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.privacy_policy') }}</a></li>
                        <li><a href="{{ $termsRoute }}" class="text-gray-300 hover:text-white transition">{{ __('landing.footer.terms_conditions') }}</a></li>
                    </ul>
                    <h4 class="text-white font-semibold mb-4 mt-6">{{ __('landing.footer.language') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('locale.set', 'id') }}" class="hover:text-white transition {{ app()->getLocale() == 'id' ? 'font-semibold text-blue-400' : 'text-gray-300' }}">🇮🇩 Indonesia</a></li>
                        <li><a href="{{ route('locale.set', 'en') }}" class="hover:text-white transition {{ app()->getLocale() == 'en' ? 'font-semibold text-blue-400' : 'text-gray-300' }}">🇬🇧 English</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 pb-0 text-center text-sm text-gray-400">
                <p class="mb-1">{{ __('landing.footer.copyright', ['year' => date('Y')]) }}</p>
                <p class="text-gray-400 text-xs mb-0">{{ __('landing.footer.tagline') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
