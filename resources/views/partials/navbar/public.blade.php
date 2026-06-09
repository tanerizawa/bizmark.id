<nav class="app-navbar">
    <div class="container-wide app-navbar-inner">
        <a href="{{ url('/') }}" class="brand-mark" aria-label="Bizmark.ID">
            <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-7 w-auto" aria-hidden="true">
            <span>Bizmark.ID</span>
        </a>
        <div class="hidden lg:flex items-center gap-1">
            <a href="{{ route('layanan') }}" class="nav-link {{ request()->routeIs('layanan*') ? 'active' : '' }}">Layanan</a>
            <a href="{{ route('proses') }}" class="nav-link {{ request()->routeIs('proses') ? 'active' : '' }}">Proses</a>
            <a href="{{ route('harga') }}" class="nav-link {{ request()->routeIs('harga') ? 'active' : '' }}">Harga</a>
            <a href="{{ route('tentang') }}" class="nav-link {{ request()->routeIs('tentang') ? 'active' : '' }}">Tentang</a>
            <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog*') ? 'active' : '' }}">Blog</a>
        </div>
        <div class="flex items-center gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Masuk</a>
                <a href="{{ route('konsultasi-gratis') }}" class="btn btn-primary btn-sm">Cek Izin Gratis</a>
            @endauth
        </div>
    </div>
</nav>
