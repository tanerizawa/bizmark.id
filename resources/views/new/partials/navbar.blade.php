@php
    $currentLocale = app()->getLocale();
    $otherLocale = $currentLocale === 'en' ? 'id' : 'en';
    $currentPath = request()->path();
    $currentSlug = $currentPath === '/' ? null : $currentPath;
    $currentSlug = Str::after($currentSlug ?? '', 'en/');

    // ID ↔ EN path mapping
    $localeMap = [
        '/'               => ['id' => '/', 'en' => '/en/'],
        'layanan'         => ['id' => '/layanan', 'en' => '/en/services'],
        'alat'            => ['id' => '/alat', 'en' => '/en/tools'],
        'proses'          => ['id' => '/proses', 'en' => '/en/process'],
        'harga'           => ['id' => '/harga', 'en' => '/en/pricing'],
        'tentang'         => ['id' => '/tentang', 'en' => '/en/about'],
        'blog'            => ['id' => '/blog', 'en' => '/en/blog'],
        'kontak'          => ['id' => '/kontak', 'en' => '/en/contact'],
        'kebijakan-privasi'  => ['id' => '/kebijakan-privasi', 'en' => '/en/privacy-policy'],
        'syarat-ketentuan'   => ['id' => '/syarat-ketentuan', 'en' => '/en/terms-conditions'],
        'status'          => ['id' => '/status', 'en' => '/en/status'],
        'konsultasi-gratis'  => ['id' => '/konsultasi-gratis', 'en' => '/en/inquiry'],
    ];

    // Strip locale prefix for EN paths
    $lookupKey = $currentPath;
    if (Str::startsWith($currentPath, 'en/')) {
        $lookupKey = Str::after($currentPath, 'en/');
    }
    if ($lookupKey === '' || $lookupKey === '/') $lookupKey = '/';

    // Handle blog article paths: /blog/{slug} or /en/blog/{slug}
    if (preg_match('#^(?:en/)?blog/(.+)$#', $currentPath, $m)) {
        $otherSlug = $m[1];
        $otherUrl = $otherLocale === 'en' ? "/en/blog/{$otherSlug}" : "/blog/{$otherSlug}";
    } else {
        $otherUrl = $localeMap[$lookupKey][$otherLocale] ?? ($otherLocale === 'en' ? '/en/' : '/');
    }
@endphp

<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/92 backdrop-blur-lg border-b transition-all duration-300 border-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="{{ $currentLocale === 'en' ? '/en/' : '/' }}" class="flex items-center gap-2.5 flex-shrink-0">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="Bizmark.ID" class="w-7 h-7">
                <span class="text-lg font-extrabold tracking-tight text-[#2D2A24]">Bizmark.ID</span>
            </a>

            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ $currentLocale === 'en' ? '/en/services' : '/layanan' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'Services' : 'Layanan' }}</a>
                <a href="{{ $currentLocale === 'en' ? '/en/tools' : '/alat' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'Tools' : 'Alat' }}</a>
                <a href="{{ $currentLocale === 'en' ? '/en/process' : '/proses' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'Process' : 'Proses' }}</a>
                <a href="{{ $currentLocale === 'en' ? '/en/pricing' : '/harga' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'Pricing' : 'Harga' }}</a>
                <a href="{{ $currentLocale === 'en' ? '/en/blog' : '/blog' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'Blog' : 'Blog' }}</a>
                <a href="{{ $currentLocale === 'en' ? '/en/about' : '/tentang' }}" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">{{ $currentLocale === 'en' ? 'About' : 'Tentang' }}</a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Language Toggle --}}
                <a href="{{ $otherUrl }}"
                   class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold uppercase tracking-wider rounded-lg border border-[#E5E0DB] text-[#6B6560] hover:border-[#0D9488] hover:text-[#0D9488] transition-all duration-200 bg-white"
                   aria-label="{{ $otherLocale === 'en' ? 'Switch to English' : 'Ganti ke Bahasa Indonesia' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ strtoupper($otherLocale) }}
                </a>

                <a href="/konsultasi-gratis"
                   class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $currentLocale === 'en' ? 'Check Permit Need' : 'Cek Kebutuhan Izin' }}
                </a>

                <button id="mobileMenuToggle" class="lg:hidden p-2 rounded-lg text-[#6B6560] hover:bg-[#F0ECE6] transition-colors" aria-label="{{ $currentLocale === 'en' ? 'Open menu' : 'Buka menu' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menuIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="closeIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="lg:hidden hidden border-t border-[#E5E0DB] bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ $currentLocale === 'en' ? '/en/services' : '/layanan' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'Services' : 'Layanan' }}</a>
            <a href="{{ $currentLocale === 'en' ? '/en/tools' : '/alat' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'Tools' : 'Alat' }}</a>
            <a href="{{ $currentLocale === 'en' ? '/en/process' : '/proses' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'Process' : 'Proses' }}</a>
            <a href="{{ $currentLocale === 'en' ? '/en/pricing' : '/harga' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'Pricing' : 'Harga' }}</a>
            <a href="{{ $currentLocale === 'en' ? '/en/blog' : '/blog' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'Blog' : 'Blog' }}</a>
            <a href="{{ $currentLocale === 'en' ? '/en/about' : '/tentang' }}" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">{{ $currentLocale === 'en' ? 'About' : 'Tentang' }}</a>
            <hr class="border-[#E5E0DB] my-2">
            <div class="px-3 py-2">
                <a href="{{ $otherUrl }}"
                   class="flex items-center gap-2 text-sm font-semibold text-[#0D9488]"
                   aria-label="{{ $otherLocale === 'en' ? 'Switch to English' : 'Ganti ke Bahasa Indonesia' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ strtoupper($otherLocale) }}
                </a>
            </div>
            <a href="/konsultasi-gratis" class="block px-3 py-2.5 text-sm font-semibold text-[#0D9488]">{{ $currentLocale === 'en' ? 'Check Permit Need' : 'Cek Kebutuhan Izin' }}</a>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobileMenuToggle')?.addEventListener('click', function() {
        var menu = document.getElementById('mobileMenu');
        var menuIcon = document.getElementById('menuIcon');
        var closeIcon = document.getElementById('closeIcon');
        menu.classList.toggle('hidden');
        menuIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });
</script>