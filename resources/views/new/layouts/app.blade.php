<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    @include('new.partials.head')
</head>
<body class="font-sans antialiased bg-[#F8F6F3] text-[#2D2A24] min-h-screen flex flex-col">
    <script>
        (function() {
            var theme = 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    @include('new.partials.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('new.partials.footer')

    <button id="backToTop"
        class="fixed bottom-6 right-6 z-50 w-10 h-10 rounded-full bg-white border border-[#E5E0DB] shadow-sm flex items-center justify-center text-[#6B6560] opacity-0 invisible transition-all duration-300 hover:bg-[#0D9488] hover:text-white hover:border-[#0D9488]"
        onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Kembali ke atas">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>

    <script>
        window.addEventListener('scroll', function() {
            var btn = document.getElementById('backToTop');
            if (window.scrollY > 400) {
                btn.classList.remove('opacity-0', 'invisible');
                btn.classList.add('opacity-100', 'visible');
            } else {
                btn.classList.remove('opacity-100', 'visible');
                btn.classList.add('opacity-0', 'invisible');
            }
        });

        window.addEventListener('scroll', function() {
            var nav = document.getElementById('navbar');
            if (window.scrollY > 20) {
                nav.classList.add('shadow-sm');
                nav.classList.remove('border-transparent');
                nav.classList.add('border-[#E5E0DB]');
            } else {
                nav.classList.remove('shadow-sm');
                nav.classList.remove('border-[#E5E0DB]');
                nav.classList.add('border-transparent');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
