<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/92 backdrop-blur-lg border-b transition-all duration-300 border-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2.5 flex-shrink-0">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="Bizmark.ID" class="w-7 h-7">
                <span class="text-lg font-extrabold tracking-tight text-[#2D2A24]">Bizmark.ID</span>
            </a>

            <div class="hidden lg:flex items-center gap-1">
                <a href="/layanan" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Layanan</a>
                <a href="/alat" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Alat</a>
                <a href="/proses" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Proses</a>
                <a href="/harga" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Harga</a>
                <a href="/blog" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Blog</a>
                <a href="/tentang" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Tentang</a>
            </div>

            <div class="flex items-center gap-3">
                <a href="/konsultasi-gratis"
                   class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cek Kebutuhan Izin
                </a>

                <button id="mobileMenuToggle" class="lg:hidden p-2 rounded-lg text-[#6B6560] hover:bg-[#F0ECE6] transition-colors" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menuIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="closeIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobileMenu" class="lg:hidden hidden border-t border-[#E5E0DB] bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="/layanan" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Layanan</a>
            <a href="/alat" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Alat</a>
            <a href="/proses" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Proses</a>
            <a href="/harga" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Harga</a>
            <a href="/blog" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Blog</a>
            <a href="/tentang" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Tentang</a>
            <hr class="border-[#E5E0DB] my-2">
            <a href="/konsultasi-gratis" class="block px-3 py-2.5 text-sm font-semibold text-[#0D9488]">Cek Kebutuhan Izin</a>
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
