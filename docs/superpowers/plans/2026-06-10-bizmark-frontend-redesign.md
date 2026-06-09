# Bizmark.ID Frontend Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a redesigned frontend for Bizmark.ID served at `/new` path, using Warm Professional design (teal accent, warm off-white backgrounds, Plus Jakarta Sans typography).

**Architecture:** New `NewLandingController` aggregates data (articles, services, testimonials, FAQ from existing config/models) and renders new Blade views in `resources/views/new/`. New CSS entry point `new.css` with independent design tokens. Routes under `/new` prefix with locale middleware.

**Tech Stack:** Laravel 12, Tailwind CSS v4, Vite, Alpine.js, Blade Components, Plus Jakarta Sans, Heroicons

---

### Task 1: Route + Controller + Layout Foundation

**Files:**
- Create: `app/Http/Controllers/NewLandingController.php`
- Create: `resources/views/new/layouts/app.blade.php`
- Create: `resources/views/new/partials/head.blade.php`
- Create: `resources/views/new/partials/navbar.blade.php`
- Create: `resources/views/new/partials/footer.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create NewLandingController**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewLandingController extends Controller
{
    public function home()
    {
        $locale = app()->getLocale();

        try {
            $latestArticles = cache()->remember("new.landing.articles.{$locale}", 600, function () {
                return \App\Models\Article::published()
                    ->orderBy('published_at', 'desc')
                    ->take(5)
                    ->get();
            });
        } catch (Throwable $e) {
            Log::warning('New landing articles cache unavailable', [
                'locale' => $locale, 'error' => $e->getMessage(),
            ]);
            $latestArticles = \App\Models\Article::published()
                ->orderBy('published_at', 'desc')
                ->take(5)
                ->get();
        }

        $services = config('services_data', []);
        $testimonials = config('landing.testimonials', []);
        $faq = config('landing.faq', []);
        $clients = config('landing.clients', []);
        $contact = config('landing_metrics.contact', []);
        $stats = config('landing_metrics.stats', []);

        return view('new.pages.home', compact(
            'latestArticles', 'services', 'testimonials', 'faq',
            'clients', 'contact', 'stats', 'locale'
        ));
    }

    public function services()
    {
        $services = config('services_data', []);
        return view('new.pages.services', compact('services'));
    }

    public function process()
    {
        return view('new.pages.process');
    }

    public function pricing()
    {
        return view('new.pages.pricing');
    }

    public function about()
    {
        return view('new.pages.about');
    }

    public function blog()
    {
        $articles = \App\Models\Article::published()
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        return view('new.pages.blog', compact('articles'));
    }

    public function contact()
    {
        return view('new.pages.contact');
    }
}
```

- [ ] **Step 2: Create head partial**

```blade
{{-- resources/views/new/partials/head.blade.php --}}
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="theme-color" content="#F8F6F3">

<title>@yield('title', 'Bizmark.ID — Perizinan Usaha Indonesia')</title>
<meta name="description" content="@yield('description', 'Platform legal-tech perizinan usaha di Indonesia. Cek kebutuhan izin usaha Anda dengan AI, gratis.')">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

@vite('resources/css/new.css')
@stack('styles')
```

- [ ] **Step 3: Create layout**

```blade
{{-- resources/views/new/layouts/app.blade.php --}}
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

    {{-- Back to top --}}
    <button id="backToTop"
        class="fixed bottom-6 right-6 z-50 w-10 h-10 rounded-full bg-white border border-[#E5E0DB] shadow-sm flex items-center justify-center text-[#6B6560] opacity-0 invisible transition-all duration-300 hover:bg-[#0D9488] hover:text-white hover:border-[#0D9488]"
        onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Kembali ke atas">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
    </button>

    <script>
        // Back to top visibility
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

        // Navbar scroll effect
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
```

- [ ] **Step 4: Create navbar**

```blade
{{-- resources/views/new/partials/navbar.blade.php --}}
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/92 backdrop-blur-lg border-b transition-all duration-300 border-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/new" class="flex items-center gap-2.5 flex-shrink-0">
                <img src="{{ asset('images/logo-mark.svg') }}" alt="Bizmark.ID" class="w-7 h-7">
                <span class="text-lg font-extrabold tracking-tight text-[#2D2A24]">Bizmark.ID</span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden lg:flex items-center gap-1">
                <a href="/new/layanan" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Layanan</a>
                <a href="/new/proses" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Proses</a>
                <a href="/new/harga" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Harga</a>
                <a href="/new/blog" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Blog</a>
                <a href="/new/tentang" class="px-3 py-2 text-sm font-medium text-[#6B6560] hover:text-[#2D2A24] rounded-lg hover:bg-[#F0ECE6] transition-colors">Tentang</a>
            </div>

            {{-- CTA + Mobile toggle --}}
            <div class="flex items-center gap-3">
                <a href="/konsultasi-gratis"
                   class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cek Kebutuhan Izin
                </a>

                {{-- Mobile hamburger --}}
                <button id="mobileMenuToggle" class="lg:hidden p-2 rounded-lg text-[#6B6560] hover:bg-[#F0ECE6] transition-colors" aria-label="Buka menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menuIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="closeIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobileMenu" class="lg:hidden hidden border-t border-[#E5E0DB] bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="/new/layanan" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Layanan</a>
            <a href="/new/proses" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Proses</a>
            <a href="/new/harga" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Harga</a>
            <a href="/new/blog" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Blog</a>
            <a href="/new/tentang" class="block px-3 py-2.5 text-sm font-medium text-[#6B6560] rounded-lg hover:bg-[#F0ECE6]">Tentang</a>
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
```

- [ ] **Step 5: Create footer**

```blade
{{-- resources/views/new/partials/footer.blade.php --}}
<footer class="bg-white border-t border-[#E5E0DB] mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8 lg:gap-12">
            {{-- Brand --}}
            <div class="col-span-2 md:col-span-3 lg:col-span-1">
                <a href="/new" class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="Bizmark.ID" class="w-6 h-6">
                    <span class="text-base font-extrabold tracking-tight text-[#2D2A24]">Bizmark.ID</span>
                </a>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Perizinan Industri Terpercaya.</p>
                <div class="flex gap-2">
                    <a href="https://www.linkedin.com/company/bizmark-id" target="_blank" rel="noopener" class="w-8 h-8 rounded-full border border-[#E5E0DB] flex items-center justify-center text-[#6B6560] hover:border-[#0D9488] hover:text-[#0D9488] transition-colors" aria-label="LinkedIn">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    <a href="https://www.facebook.com/bizmark.id" target="_blank" rel="noopener" class="w-8 h-8 rounded-full border border-[#E5E0DB] flex items-center justify-center text-[#6B6560] hover:border-[#0D9488] hover:text-[#0D9488] transition-colors" aria-label="Facebook">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385h-3.047v-3.47h3.047v-2.642c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953h-1.514c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385c5.737-.9 10.125-5.864 10.125-11.854z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/bizmark.id" target="_blank" rel="noopener" class="w-8 h-8 rounded-full border border-[#E5E0DB] flex items-center justify-center text-[#6B6560] hover:border-[#0D9488] hover:text-[#0D9488] transition-colors" aria-label="Instagram">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Navigasi --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6560] mb-4">Navigasi</h4>
                <ul class="space-y-2.5">
                    <li><a href="/new" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Beranda</a></li>
                    <li><a href="/new/layanan" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Layanan</a></li>
                    <li><a href="/new/proses" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Proses</a></li>
                    <li><a href="/new/harga" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Harga</a></li>
                    <li><a href="/new/tentang" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Tentang</a></li>
                </ul>
            </div>

            {{-- Platform --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6560] mb-4">Platform</h4>
                <ul class="space-y-2.5">
                    <li><a href="/konsultasi-gratis" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Cek Perizinan AI</a></li>
                    <li><a href="/permohonan" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Permohonan</a></li>
                    <li><a href="/login" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Portal Klien</a></li>
                    <li><a href="/new/blog" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Blog</a></li>
                </ul>
            </div>

            {{-- Hubungi Kami --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6560] mb-4">Hubungi Kami</h4>
                <ul class="space-y-2.5">
                    <li><a href="/cdn-cgi/l/email-protection#dbb2b5bdb49bb9b2a1b6baa9b0f5b2bf" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">hello@bizmark.id</a></li>
                    <li><a href="tel:+6283879602855" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">+62 838 7960 2855</a></li>
                    <li><a href="https://wa.me/6283879602855" target="_blank" rel="noopener" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">WhatsApp</a></li>
                    <li class="text-sm text-[#6B6560]">Karawang, Jawa Barat</li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-[#6B6560] mb-4">Legal</h4>
                <ul class="space-y-2.5">
                    <li><a href="/kebijakan-privasi" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="/syarat-ketentuan" class="text-sm text-[#6B6560] hover:text-[#0D9488] transition-colors">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>

        {{-- City coverage --}}
        <div class="mt-10 pt-6 border-t border-[#E5E0DB]">
            <div class="flex flex-wrap gap-2">
                <span class="text-xs font-medium text-[#6B6560]">Jangkauan:</span>
                @php
                    $cities = ['Karawang','Bekasi','Jakarta','Tangerang','Bogor','Surabaya','Semarang','Bandung','Cikarang','Purwakarta','Subang','Serang','Depok','Cirebon','Sukabumi','Solo','Yogyakarta','Malang','Gresik','Sidoarjo'];
                @endphp
                @foreach($cities as $city)
                    <a href="/layanan/kota/{{ Str::slug($city) }}" class="text-xs text-[#9C9690] hover:text-[#0D9488] transition-colors">{{ $city }}</a>
                    @if(!$loop->last)<span class="text-xs text-[#E5E0DB]">·</span>@endif
                @endforeach
            </div>
        </div>

        {{-- Bottom bar --}}
        <div class="mt-8 pt-6 border-t border-[#E5E0DB] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap gap-3">
                <span class="text-xs text-[#9C9690]">Terhubung dengan:</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F0ECE6] text-xs font-medium text-[#6B6560]">OSS-RBA</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F0ECE6] text-xs font-medium text-[#6B6560]">BKPM</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F0ECE6] text-xs font-medium text-[#6B6560]">KBLI 2020</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F0ECE6] text-xs font-medium text-[#6B6560]">KLHK</span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-[#F0ECE6] text-xs font-medium text-[#6B6560]">Kemenkumham</span>
            </div>
            <p class="text-xs text-[#9C9690]">© {{ date('Y') }} PT CANGAH PAJARATAN MANDIRI (Bizmark.ID)</p>
        </div>
    </div>
</footer>
```

- [ ] **Step 6: Add routes in web.php**

```php
// Before the main landing route, add:
Route::prefix('new')->middleware('locale:id')->name('new.')->group(function () {
    Route::get('/', [App\Http\Controllers\NewLandingController::class, 'home'])->name('home');
    Route::get('/layanan', [App\Http\Controllers\NewLandingController::class, 'services'])->name('services');
    Route::get('/proses', [App\Http\Controllers\NewLandingController::class, 'process'])->name('process');
    Route::get('/harga', [App\Http\Controllers\NewLandingController::class, 'pricing'])->name('pricing');
    Route::get('/tentang', [App\Http\Controllers\NewLandingController::class, 'about'])->name('about');
    Route::get('/blog', [App\Http\Controllers\NewLandingController::class, 'blog'])->name('blog');
    Route::get('/kontak', [App\Http\Controllers\NewLandingController::class, 'contact'])->name('contact');
});
```

- [ ] **Step 7: Verify route works**

```bash
cd /home/bizmark/bizmark.id && php artisan route:list --path=new
```

Expected output: 7 routes listed under `new.` prefix.

---

### Task 2: CSS Design Tokens + Entry Point

**Files:**
- Create: `resources/css/new-theme.css`
- Create: `resources/css/new.css`
- Modify: `vite.config.js`

- [ ] **Step 1: Create new-theme.css**

```css
/* resources/css/new-theme.css */
/* Warm Professional — Design Tokens for Bizmark.ID Redesign */

@theme {
    --font-sans: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

    /* Backgrounds */
    --color-bg-base: #F8F6F3;
    --color-bg-surface: #FFFFFF;
    --color-bg-raised: #FCFAF8;

    /* Text */
    --color-text-primary: #2D2A24;
    --color-text-secondary: #6B6560;
    --color-text-muted: #9C9690;

    /* Accent */
    --color-accent: #0D9488;
    --color-accent-dark: #0F766E;
    --color-accent-soft: #CCFBF1;
    --color-accent-text: #115E59;

    /* Borders */
    --color-border: #E5E0DB;
    --color-border-light: #F0ECE6;

    /* Status */
    --color-success: #10B981;
    --color-warning: #F59E0B;
    --color-error: #EF4444;
    --color-info: #3B82F6;

    /* Shadows */
    --shadow-soft: 0 1px 3px rgba(0,0,0,0.04);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
    --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);
}
```

- [ ] **Step 2: Create new.css**

```css
/* resources/css/new.css */
@import 'tailwindcss';
@import './new-theme.css';

@source '../views/new/**/*.blade.php';

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Skip link */
.skip-link {
    position: absolute;
    top: -100%;
    left: 50%;
    z-index: 9999;
    padding: 0.75rem 1.5rem;
    background: #0D9488;
    color: white;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: top 0.2s;
}
.skip-link:focus {
    top: 1rem;
}

/* Focus visible */
*:focus-visible {
    outline: 2px solid #0D9488;
    outline-offset: 3px;
    border-radius: 4px;
}
:focus:not(:focus-visible) {
    outline: none;
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}

/* Print */
@media print {
    nav, footer { display: none !important; }
}

/* Alpine cloak */
[x-cloak] { display: none !important; }
```

- [ ] **Step 3: Update vite.config.js**

```js
// Add 'resources/css/new.css' to the input array
laravel({
    input: [
        'resources/css/app.css',
        'resources/css/public.css',
        'resources/css/admin.css',
        'resources/css/landing.css',
        'resources/css/landing-theme.css',
        'resources/css/client.css',
        'resources/css/new.css',
        'resources/js/app.js',
        'resources/js/client.js',
    ],
    refresh: true,
}),
```

- [ ] **Step 4: Build to verify**

```bash
cd /home/bizmark/bizmark.id && npm run build 2>&1 | tail -5
```

Expected: Build succeeds, `public/build/assets/new-*.css` is created.

---

### Task 3: Hero Section

**Files:**
- Create: `resources/views/new/sections/hero.blade.php`

- [ ] **Step 1: Create hero section**

```blade
{{-- resources/views/new/sections/hero.blade.php --}}
<section class="relative overflow-hidden pt-24 pb-12 sm:pt-32 sm:pb-16 lg:pt-36 lg:pb-20">
    {{-- Subtle background gradient --}}
    <div class="absolute inset-0 bg-gradient-to-b from-[#F0ECE6] to-[#F8F6F3] pointer-events-none"></div>

    {{-- Decorative geometric pattern --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image: linear-gradient(rgba(45,42,36,.08) 1px, transparent 1px), linear-gradient(90deg, rgba(45,42,36,.08) 1px, transparent 1px); background-size: 48px 48px;">
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-12 xl:gap-16 items-center">
            {{-- Left: Text --}}
            <div class="max-w-xl">
                {{-- Eyebrow --}}
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#0D9488]"></span>
                    <span class="text-xs font-semibold text-[#115E59] tracking-wide">Gratis Selamanya · Tanpa Daftar</span>
                </div>

                {{-- Headline --}}
                <h1 class="text-[clamp(2.25rem,4vw,3.5rem)] font-extrabold leading-[1.08] tracking-tight text-[#2D2A24] mb-5">
                    Peta lengkap perizinan usaha Anda.<br>
                    <span class="text-[#0D9488]">Dalam 2 menit. Gratis.</span>
                </h1>

                {{-- Sub --}}
                <p class="text-base sm:text-lg text-[#6B6560] leading-relaxed mb-6 max-w-lg">
                    Bizmark.ID adalah operating system perizinan usaha di Indonesia. Pakai alat AI kami sendiri — atau serahkan kepada tim spesialis saat Anda butuh eksekusi lapangan.
                </p>

                {{-- AI Search --}}
                <form action="/konsultasi-gratis" method="GET" class="flex items-center gap-2 bg-white border border-[#E5E0DB] rounded-xl p-1.5 focus-within:border-[#0D9488] focus-within:ring-2 focus-within:ring-[#0D9488]/20 transition-all duration-200 shadow-sm mb-4 max-w-lg">
                    <div class="flex items-center gap-2 pl-3 flex-shrink-0">
                        <svg class="w-5 h-5 text-[#9C9690]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="q" placeholder="Jenis usaha Anda atau kode KBLI..."
                           class="flex-1 border-none outline-none bg-transparent px-1 py-2.5 text-sm text-[#2D2A24] placeholder:text-[#9C9690]" autocomplete="off">
                    <button type="submit" class="flex-shrink-0 px-5 py-2.5 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md">
                        Cek Izin
                    </button>
                </form>

                {{-- Hint --}}
                <div class="flex items-center gap-2 text-xs text-[#9C9690]">
                    <svg class="w-3.5 h-3.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    AI petakan syarat, instansi & timeline · Gratis · Cukup info kontak (~30 detik)
                </div>
            </div>

            {{-- Right: Dashboard Mockup / Illustration --}}
            <div class="hidden lg:block relative">
                <div class="relative">
                    {{-- Soft decorative blob --}}
                    <div class="absolute -top-10 -right-10 w-64 h-64 rounded-full bg-[#0D9488]/5 blur-3xl"></div>
                    <div class="absolute -bottom-8 -left-8 w-48 h-48 rounded-full bg-[#CCFBF1] blur-3xl"></div>

                    {{-- Mockup card --}}
                    <div class="relative bg-white rounded-2xl border border-[#E5E0DB] shadow-lg overflow-hidden">
                        {{-- Top bar --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-[#F0ECE6]">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-[#0D9488]/10 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <span class="text-xs font-bold text-[#2D2A24]">Bizmark.ID</span>
                            </div>
                            <div class="flex items-center gap-2 px-2.5 py-1 bg-[#F8F6F3] rounded-full text-[10px] text-[#9C9690]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Cari...
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            {{-- Stats --}}
                            <div class="grid grid-cols-4 gap-2 mb-4">
                                <div class="text-center p-2 rounded-lg bg-[#FCFAF8] border border-[#F0ECE6]">
                                    <div class="text-sm font-bold text-[#2D2A24]">128</div>
                                    <div class="text-[10px] text-[#9C9690]">Semua</div>
                                </div>
                                <div class="text-center p-2 rounded-lg bg-[#FCFAF8] border border-[#F0ECE6]">
                                    <div class="text-sm font-bold text-[#F59E0B]">72</div>
                                    <div class="text-[10px] text-[#9C9690]">Diproses</div>
                                </div>
                                <div class="text-center p-2 rounded-lg bg-[#FCFAF8] border border-[#F0ECE6]">
                                    <div class="text-sm font-bold text-[#10B981]">45</div>
                                    <div class="text-[10px] text-[#9C9690]">Disetujui</div>
                                </div>
                                <div class="text-center p-2 rounded-lg bg-[#FCFAF8] border border-[#F0ECE6]">
                                    <div class="text-sm font-bold text-[#EF4444]">11</div>
                                    <div class="text-[10px] text-[#9C9690]">Ditolak</div>
                                </div>
                            </div>

                            {{-- Progress list --}}
                            <div class="text-[11px] font-semibold uppercase tracking-wider text-[#9C9690] mb-3">Progres Permohonan</div>
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-[#2D2A24]">Izin Mendirikan Bangunan</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#92400E]">Diproses</span>
                                </div>
                                <div class="h-1 rounded-full bg-[#F0ECE6] overflow-hidden">
                                    <div class="h-full w-3/5 rounded-full bg-[#F59E0B]"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-[#2D2A24]">Izin Lingkungan</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#92400E]">Diproses</span>
                                </div>
                                <div class="h-1 rounded-full bg-[#F0ECE6] overflow-hidden">
                                    <div class="h-full w-2/5 rounded-full bg-[#F59E0B]"></div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-[#2D2A24]">Izin Usaha (NIB)</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#D1FAE5] text-[#065F46]">Terbit</span>
                                </div>
                                <div class="h-1 rounded-full bg-[#F0ECE6] overflow-hidden">
                                    <div class="h-full w-full rounded-full bg-[#10B981]"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trust bar --}}
        <div class="mt-10 lg:mt-14 pt-6 border-t border-[#E5E0DB]/60">
            <div class="flex flex-wrap items-center gap-5 sm:gap-8">
                <span class="text-xs font-medium text-[#9C9690] uppercase tracking-wider">Beroperasi sejak 2014</span>
                <div class="hidden sm:block w-px h-4 bg-[#E5E0DB]"></div>
                <span class="text-xs font-medium text-[#9C9690]">12+ Tahun pengalaman</span>
                <div class="hidden sm:block w-px h-4 bg-[#E5E0DB]"></div>
                <span class="text-xs font-medium text-[#9C9690]">Cakupan se-Indonesia</span>
                <div class="hidden sm:block w-px h-4 bg-[#E5E0DB]"></div>
                <span class="text-xs font-medium text-[#9C9690]">Bilingual ID / EN</span>
            </div>
        </div>
    </div>
</section>
```

---

### Task 4: Problem/Solution Section

**Files:**
- Create: `resources/views/new/sections/problem-solution.blade.php`

- [ ] **Step 1: Create problem-solution section**

```blade
{{-- resources/views/new/sections/problem-solution.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="max-w-2xl mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Masalah Umum</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24]">
                Kami atasi hambatan yang memperlambat usaha Anda.
            </h2>
        </div>

        {{-- Cards grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            {{-- Card 1: SHP --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#FEE2E2] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#EF4444] mb-1">Masalah</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed">File SHP ditolak OSS-RBA karena format berkas atau sistem proyeksi tidak sesuai.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 mt-auto">
                    <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0D9488] mb-1">Solusi</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed mb-3">Buat file SHP standar OSS-RBA dalam hitungan menit menggunakan alat pemetaan interaktif kami.</p>
                        <a href="/polygon-shp-maker" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                            Pelajari
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 2: Bingung Izin --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#FEE2E2] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#EF4444] mb-1">Masalah</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed">Bingung izin apa saja yang benar-benar dibutuhkan oleh usaha Anda?</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 mt-auto">
                    <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0D9488] mb-1">Solusi</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed mb-3">AI kami memetakan lebih dari 1.000 kode KBLI ke kebutuhan izin spesifik usaha Anda — langsung dan gratis.</p>
                        <a href="/konsultasi-gratis" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                            Cek kebutuhan izin saya
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Card 3: Progress --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200 sm:col-span-2 lg:col-span-1">
                <div class="flex items-start gap-4 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-[#FEE2E2] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#EF4444]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#EF4444] mb-1">Masalah</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed">Berbulan-bulan berlalu tanpa ada kejelasan mengenai perkembangan izin Anda.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 mt-auto">
                    <div class="w-10 h-10 rounded-xl bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-[#0D9488] mb-1">Solusi</p>
                        <p class="text-sm text-[#6B6560] leading-relaxed mb-3">Laporan perkembangan setiap minggu disertai tindak lanjut lapangan, lengkap dengan notifikasi setiap kali izin maju ke tahap berikutnya.</p>
                        <a href="/proses" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                            Pelajari cara kerja SLA kami
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom CTA --}}
        <div class="mt-8 text-center">
            <a href="/konsultasi-gratis" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Cek kebutuhan perizinan saya — gratis
            </a>
        </div>
    </div>
</section>
```

---

### Task 5: Free Tools Section (Bento Grid)

**Files:**
- Create: `resources/views/new/sections/tools.blade.php`

- [ ] **Step 1: Create tools section**

```blade
{{-- resources/views/new/sections/tools.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="max-w-2xl mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Toolkit Mandiri</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Pakai platformnya sendiri. <span class="text-[#0D9488]">Tanpa biaya.</span>
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Dibangun dari 12+ tahun pengalaman izin nyata. Jalankan AI checker, estimasi biaya, gambar poligon — sendiri. Saat Anda butuh eksekusi lapangan, tim spesialis kami turun tangan.</p>
        </div>

        {{-- Bento Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            {{-- AI Checker (Featured - spans 2 cols on lg) --}}
            <a href="/konsultasi-gratis" class="group lg:col-span-2 lg:row-span-2 bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200 flex flex-col">
                <div class="flex items-start gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] flex items-center justify-center">
                            <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs font-semibold tracking-wider uppercase text-[#0D9488]">AI · Gratis</span>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-1">Cek Kebutuhan Izin (AI)</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Ceritakan jenis usaha Anda, dan AI kami langsung memetakan semua izin yang perlu Anda urus — tanpa biaya.</p>
                <div class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Cek sekarang
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Estimasi Biaya --}}
            <a href="/estimasi-biaya" class="group bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200 flex flex-col">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-[#0D9488]">Real-time · AI</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-1">Estimasi Biaya Perizinan</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Pilih kode KBLI dan dapatkan perkiraan biaya serta estimasi durasi pengurusan izin secara langsung.</p>
                <div class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Hitung Biaya
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Polygon SHP Maker --}}
            <a href="/polygon-shp-maker" class="group bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200 flex flex-col">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    </div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-[#0D9488]">Siap OSS-RBA</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-1">Polygon SHP Maker</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Gambar poligon lokasi usaha Anda di peta interaktif dan ekspor file SHP siap pakai untuk OSS-RBA.</p>
                <div class="mt-auto flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Buat SHP
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>

            {{-- Kalkulator Biaya (Wide on mobile, spans bottom) --}}
            <a href="/kalkulator-perizinan" class="group lg:col-span-3 bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex items-start gap-3 sm:flex-shrink-0">
                    <div class="w-8 h-8 rounded-lg bg-[#CCFBF1] flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-semibold tracking-wider uppercase text-[#0D9488]">50+ jenis izin</span>
                </div>
                <div class="sm:flex-1">
                    <h3 class="text-base font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-1">Kalkulator Biaya Perizinan</h3>
                    <p class="text-sm text-[#6B6560] leading-relaxed">Rincian biaya lengkap berdasarkan jenis izin, dokumen yang diperlukan, dan estimasi waktu pengurusan.</p>
                </div>
                <div class="flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors sm:flex-shrink-0">
                    Hitung Biaya
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
        </div>
    </div>
</section>
```

---

### Task 6: Services Section

**Files:**
- Create: `resources/views/new/sections/services.blade.php`

- [ ] **Step 1: Create services section**

```blade
{{-- resources/views/new/sections/services.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="max-w-2xl mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Layanan Utama</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Perizinan dikelola penuh. Dijamin para ahli.
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Enam kategori perizinan tersedia. Satu tim khusus untuk setiap proyek Anda. Komitmen SLA yang jelas sejak hari pertama.</p>
        </div>

        {{-- Services Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @php
                $servicesList = [
                    ['title' => 'Izin Limbah B3', 'slug' => 'perizinan-lb3', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'items' => ['Izin TPS LB3', 'Manifest angkut', 'Pemulihan / pembuangan']],
                    ['title' => 'Lingkungan', 'slug' => 'amdal', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'items' => ['Studi AMDAL lengkap', 'Dokumen UKL-UPL', 'SPPL']],
                    ['title' => 'Perizinan Gedung', 'slug' => 'pbg-slf', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'items' => ['Penerbitan PBG', 'Sertifikasi SLF', 'Konversi IMB']],
                    ['title' => 'Izin Usaha', 'slug' => 'oss-nib', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'items' => ['Registrasi NIB', 'Setup OSS-RBA', 'SIUP · API']],
                    ['title' => 'PMA / Investasi Asing', 'slug' => 'pma-investasi-asing', 'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9', 'items' => ['Registrasi BKPM', 'Izin sektoral', 'Dukungan bilingual']],
                    ['title' => 'Operasional', 'slug' => 'izin-operasional', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'items' => ['Izin industri', 'Logistik · K3', 'Audit kepatuhan']],
                ];
            @endphp

            @foreach($servicesList as $svc)
            <a href="/layanan/{{ $svc['slug'] }}" class="group bg-white rounded-xl border border-[#E5E0DB] p-5 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-lg bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svc['icon'] }}"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59]">SLA aktif</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-2">{{ $svc['title'] }}</h3>
                <ul class="space-y-1.5 mb-3">
                    @foreach($svc['items'] as $item)
                    <li class="flex items-center gap-2 text-xs text-[#6B6560]">
                        <svg class="w-3 h-3 text-[#0D9488] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Detail layanan
                    <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>

        {{-- View all link --}}
        <div class="mt-8 text-center">
            <a href="/layanan" class="inline-flex items-center gap-2 px-5 py-2.5 border border-[#E5E0DB] text-sm font-semibold text-[#6B6560] rounded-xl hover:border-[#0D9488]/30 hover:text-[#0D9488] transition-all duration-200">
                Lihat semua 20+ layanan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
```

---

### Task 7: Business Types Section

**Files:**
- Create: `resources/views/new/sections/business-types.blade.php`

- [ ] **Step 1: Create business types section**

```blade
{{-- resources/views/new/sections/business-types.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Usaha Anda termasuk yang mana?
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Setiap solusi yang kami tawarkan dirancang sesuai skala usaha, bidang industri, dan kewajiban regulasi Anda.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-4 sm:gap-5">
            {{-- UMKM --}}
            <div class="group bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-semibold text-[#9C9690]">01 / 03</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#F0ECE6] text-[#6B6560]">UMKM</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] mb-2">UMKM / Startup</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Registrasi NIB, pengaturan OSS-RBA, dokumen lingkungan dasar, serta izin usaha tahap awal.</p>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">NIB</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">OSS-RBA</span>
                </div>
                <a href="/layanan/oss-nib" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Telusuri
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- Korporasi (Popular) --}}
            <div class="group bg-white rounded-xl border-2 border-[#0D9488]/20 p-6 hover:border-[#0D9488]/30 hover:shadow-sm transition-all duration-200 relative">
                <div class="absolute -top-2.5 right-4 px-3 py-0.5 bg-[#0D9488] text-white text-[10px] font-semibold rounded-full">Paling diminati</div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-semibold text-[#9C9690]">02 / 03</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59]">Korporasi</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] mb-2">Korporasi Menengah & Besar</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">AMDAL, pengelolaan limbah B3, UKL-UPL, PBG, SLF — kepatuhan lingkungan dan bangunan secara menyeluruh.</p>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">AMDAL</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">PBG</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">SLF</span>
                </div>
                <a href="/layanan/amdal" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Telusuri
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            {{-- PMA --}}
            <div class="group bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[11px] font-semibold text-[#9C9690]">03 / 03</span>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#F0ECE6] text-[#6B6560]">PMA</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] mb-2">Perusahaan PMA / Investasi Asing</h3>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-4">Pendaftaran BKPM, pengurusan izin sektoral, dan pendampingan bilingual di setiap tahap proses.</p>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">BKPM</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#CCFBF1] text-[#115E59]">KPA</span>
                </div>
                <a href="/layanan/pma-investasi-asing" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Telusuri
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
```

---

### Task 8: Process + Testimonials Sections

**Files:**
- Create: `resources/views/new/sections/process.blade.php`
- Create: `resources/views/new/sections/testimonials.blade.php`

- [ ] **Step 1: Create process section**

```blade
{{-- resources/views/new/sections/process.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Cara Kerja Kami</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Proses yang mengutamakan kejelasan dan akuntabilitas.
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Empat langkah yang jelas. Satu tim yang bertanggung jawab. Hasil terukur dengan SLA di setiap tahap.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-4 sm:gap-5">
            @php
                $steps = [
                    ['num' => '01', 'time' => '1-2 hari', 'title' => 'Kajian Awal & Pemetaan', 'desc' => 'Kami telaah konteks usaha, kode KBLI, dan celah perizinan Anda — tanpa biaya.'],
                    ['num' => '02', 'time' => '2-3 hari', 'title' => 'Proposal & Kesepakatan', 'desc' => 'Lingkup pekerjaan, biaya, dan ketentuan SLA yang jelas — semua terdokumentasi sebelum mulai.'],
                    ['num' => '03', 'time' => 'Variatif', 'title' => 'Pelaksanaan & Pelaporan', 'desc' => 'Laporan kemajuan SLA mingguan, manajer proyek khusus, dan tindak lanjut langsung di lapangan.', 'highlight' => true],
                    ['num' => '04', 'time' => '1 minggu', 'title' => 'Terbit & Serah Terima', 'desc' => 'Izin diserahkan lengkap beserta peta jalan kepatuhan dan pilihan dukungan berkelanjutan.'],
                ];
            @endphp

            @foreach($steps as $i => $step)
            <div class="relative">
                {{-- Connector line (desktop) --}}
                @if($i < 3)
                <div class="hidden md:block absolute top-6 left-[calc(50%+1.5rem)] right-0 h-px bg-[#E5E0DB] -z-10"></div>
                @endif

                <div class="bg-white rounded-xl border {{ isset($step['highlight']) ? 'border-[#0D9488]/20 border-2' : 'border-[#E5E0DB]' }} p-5 {{ isset($step['highlight']) ? '' : 'hover:border-[#0D9488]/20' }} transition-all duration-200 h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-[#9C9690]">{{ $step['num'] }}</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ isset($step['highlight']) ? 'bg-[#CCFBF1] text-[#115E59]' : 'bg-[#F0ECE6] text-[#6B6560]' }}">{{ $step['time'] }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#2D2A24] mb-1">{{ $step['title'] }}</h3>
                    <p class="text-xs text-[#6B6560] leading-relaxed">{{ $step['desc'] }}</p>
                    @if(isset($step['highlight']))
                    <div class="mt-3 flex items-center gap-1.5 text-[10px] font-semibold text-[#0D9488]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terpantau via Portal Klien
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="/proses" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                Pelajari proses kami secara lengkap
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
```

- [ ] **Step 2: Create testimonials section**

```blade
{{-- resources/views/new/sections/testimonials.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Dipercaya berbagai industri.
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Hasil nyata dari klien kami di bidang manufaktur, logistik, dan perusahaan PMA di seluruh Indonesia.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-4 sm:gap-5">
            {{-- Testimonial 1 --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-[#2D2A24] leading-relaxed mb-4">"Pengurusan PBG untuk infrastruktur data center kami ditangani dengan sangat profesional dan efisien. Prosesnya transparan dan selesai tepat waktu."</p>
                <div class="flex items-center gap-3 pt-3 border-t border-[#F0ECE6]">
                    <div class="w-9 h-9 rounded-full bg-[#0D9488]/10 flex items-center justify-center text-xs font-bold text-[#0D9488]">PM</div>
                    <div>
                        <p class="text-sm font-semibold text-[#2D2A24]">Project Manager</p>
                        <p class="text-xs text-[#6B6560]">PT Biznet Gio Nusantara</p>
                    </div>
                </div>
            </div>

            {{-- Testimonial 2 --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-[#2D2A24] leading-relaxed mb-4">"UKL-UPL untuk pabrik paving block kami diurus dengan sangat detail dan teliti. Tim Bizmark.ID membantu lengkapi semua persyaratan dengan cepat."</p>
                <div class="flex items-center gap-3 pt-3 border-t border-[#F0ECE6]">
                    <div class="w-9 h-9 rounded-full bg-[#0D9488]/10 flex items-center justify-center text-xs font-bold text-[#0D9488]">OM</div>
                    <div>
                        <p class="text-sm font-semibold text-[#2D2A24]">Operational Manager</p>
                        <p class="text-xs text-[#6B6560]">PT Asiacon Cipta Prima</p>
                    </div>
                </div>
            </div>

            {{-- Testimonial 3 --}}
            <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-center gap-1 mb-4">
                    @for($i = 0; $i < 5; $i++)
                    <svg class="w-4 h-4 text-[#F59E0B]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </div>
                <p class="text-sm text-[#2D2A24] leading-relaxed mb-4">"Konsultan inhouse untuk perizinan Limbah B3 sangat membantu operasional kami. Profesional dan selalu update regulasi terbaru."</p>
                <div class="flex items-center gap-3 pt-3 border-t border-[#F0ECE6]">
                    <div class="w-9 h-9 rounded-full bg-[#0D9488]/10 flex items-center justify-center text-xs font-bold text-[#0D9488]">HS</div>
                    <div>
                        <p class="text-sm font-semibold text-[#2D2A24]">HSE Manager</p>
                        <p class="text-xs text-[#6B6560]">PT Rindu Alam Sejahtera</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="mt-6 text-xs text-[#9C9690] text-center">* Studi kasus lengkap tersedia melalui NDA — hubungi tim kami untuk mengatur sesi tinjauan bersama.</p>
    </div>
</section>
```

---

### Task 9: Blog + Newsletter + FAQ Sections

**Files:**
- Create: `resources/views/new/sections/blog.blade.php`
- Create: `resources/views/new/sections/newsletter.blade.php`
- Create: `resources/views/new/sections/faq.blade.php`

- [ ] **Step 1: Create blog section**

```blade
{{-- resources/views/new/sections/blog.blade.php --}}
@if(isset($latestArticles) && $latestArticles->count() > 0)
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24]">
                    Wawasan & Pengetahuan
                </h2>
                <p class="text-base text-[#6B6560] mt-2">Ulasan mendalam regulasi perizinan Indonesia dari para praktisi.</p>
            </div>
            <a href="/blog" class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                Semua artikel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="grid md:grid-cols-3 gap-4 sm:gap-5">
            @foreach($latestArticles as $i => $article)
            <a href="/blog/{{ $article->slug }}" class="group bg-white rounded-xl border border-[#E5E0DB] overflow-hidden hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                @if($article->featured_image)
                <div class="aspect-[16/10] overflow-hidden bg-[#F0ECE6]">
                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                </div>
                @endif
                <div class="p-5">
                    @if($article->category)
                    <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59] mb-2">{{ $article->category }}</span>
                    @endif
                    <h3 class="text-sm font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors leading-snug mb-1">{{ $article->title }}</h3>
                    <p class="text-xs text-[#6B6560]">{{ $article->published_at?->format('d M Y') }} · {{ $article->reading_time ?? '5' }} menit baca</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-6 text-center sm:hidden">
            <a href="/blog" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#0D9488]">Semua artikel <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg></a>
        </div>
    </div>
</section>
@endif
```

- [ ] **Step 2: Create newsletter section**

```blade
{{-- resources/views/new/sections/newsletter.blade.php --}}
<section class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 sm:p-8 lg:p-10 relative overflow-hidden">
            {{-- Decorative --}}
            <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-[#0D9488]/5 blur-3xl"></div>

            <div class="relative max-w-2xl">
                <h2 class="text-xl sm:text-2xl font-bold text-[#2D2A24] mb-2">Pembaruan regulasi langsung ke email Anda.</h2>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-6">Ringkasan bulanan pembaruan regulasi perizinan, perubahan aturan KBLI, dan briefing kepatuhan — dikurasi langsung oleh spesialis regulasi kami.</p>

                <form action="/subscribe" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="email" name="email" required placeholder="Alamat email"
                           class="flex-1 px-4 py-3 bg-[#F8F6F3] border border-[#E5E0DB] rounded-xl text-sm text-[#2D2A24] placeholder:text-[#9C9690] focus:outline-none focus:border-[#0D9488] focus:ring-2 focus:ring-[#0D9488]/20 transition-all duration-200">
                    <button type="submit" class="px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md whitespace-nowrap">
                        Berlangganan
                    </button>
                </form>
                <p class="mt-3 text-xs text-[#9C9690]">Ringkasan bulanan · Bebas spam · Berhenti kapan saja</p>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 3: Create FAQ section**

```blade
{{-- resources/views/new/sections/faq.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Pertanyaan Umum
            </h2>
            <p class="text-base text-[#6B6560]">Masih ragu? <a href="/contact" class="text-[#0D9488] hover:text-[#0F766E] font-semibold">Hubungi tim kami</a> dan kami akan bantu jelaskan.</p>
        </div>

        @php
            $faqItems = [
                ['q' => 'Berapa lama proses pengurusan perizinan?', 'a' => 'Waktu pengurusan bervariasi tergantung jenis izin. OSS (NIB) biasanya 1-3 hari kerja, UKL-UPL 14-30 hari kerja, sedangkan AMDAL dapat memakan waktu 3-6 bulan. Kami memberikan estimasi setelah konsultasi awal.'],
                ['q' => 'Apa saja dokumen yang perlu disiapkan?', 'a' => 'Dokumen dasar meliputi KTP/NPWP Direktur, Akta Pendirian, SK Kemenkumham, NPWP Perusahaan, serta dokumen teknis tambahan sesuai jenis perizinan. Tim kami membantu menyiapkan seluruh kebutuhan tersebut.'],
                ['q' => 'Bagaimana skema pembayaran layanan Bizmark.ID?', 'a' => 'Kami menerapkan pembayaran bertahap: 50% saat pekerjaan dimulai sebagai uang muka dan 50% ketika izin terbit. Untuk proyek besar kami dapat menyesuaikan skema sesuai kesepakatan.'],
                ['q' => 'Apakah ada jaminan jika perizinan tidak berhasil?', 'a' => 'Ada. Jika kegagalan berasal dari sisi kami, biaya akan dikembalikan sebagian sesuai kesepakatan awal. Dengan tingkat keberhasilan di atas 95%, situasi tersebut jarang terjadi.'],
                ['q' => 'Bisakah saya memantau perkembangan perizinan?', 'a' => 'Kami mengirimkan laporan berkala lewat WhatsApp atau email lengkap dengan tonggak kemajuan dan dokumen pendukung sehingga Anda memiliki visibilitas penuh.'],
                ['q' => 'Apakah melayani klien di luar Jawa Barat?', 'a' => 'Ya. Walau kantor pusat di Karawang, kami memiliki jaringan konsultan di berbagai provinsi dan dapat mengurus izin di seluruh Indonesia. Konsultasi awal dapat dilakukan secara daring.'],
            ];
        @endphp

        <div class="space-y-2">
            @foreach($faqItems as $i => $item)
            <div x-data="{ open: false }" class="bg-white rounded-xl border border-[#E5E0DB] overflow-hidden hover:border-[#0D9488]/20 transition-colors duration-200">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left" :class="{ 'border-b border-[#F0ECE6]': open }">
                    <span class="text-sm font-semibold text-[#2D2A24]">{{ $item['q'] }}</span>
                    <svg class="w-4 h-4 text-[#9C9690] flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse.duration.300ms>
                    <div class="px-5 pb-4">
                        <p class="text-sm text-[#6B6560] leading-relaxed">{{ $item['a'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
```

---

### Task 10: Final CTA + Homepage Assembly

**Files:**
- Create: `resources/views/new/sections/cta.blade.php`
- Create: `resources/views/new/pages/home.blade.php`

- [ ] **Step 1: Create final CTA section**

```blade
{{-- resources/views/new/sections/cta.blade.php --}}
<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-[#E5E0DB] p-8 sm:p-10 lg:p-12 text-center relative overflow-hidden">
            {{-- Decorative --}}
            <div class="absolute -bottom-20 -left-20 w-60 h-60 rounded-full bg-[#0D9488]/[0.03] blur-3xl"></div>
            <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-[#CCFBF1] blur-3xl"></div>

            <div class="relative">
                <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">
                    Siap mengurus perizinan<br>usaha Anda?
                </h2>
                <p class="text-base text-[#6B6560] max-w-lg mx-auto mb-8">Jalankan cek perizinan AI secara gratis dalam hitungan detik — atau terhubung langsung dengan tim spesialis kami untuk asesmen yang lebih mendalam.</p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="/konsultasi-gratis" class="inline-flex items-center gap-2 px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Cek Perizinan AI — Gratis
                    </a>
                    <a href="https://wa.me/6283879602855" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 border border-[#E5E0DB] text-sm font-semibold text-[#6B6560] rounded-xl hover:border-[#0D9488]/30 hover:text-[#0D9488] transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Hubungi via WhatsApp
                    </a>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-4 mt-8 text-xs text-[#9C9690]">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Beroperasi sejak 2014
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#0D9488] w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Cakupan se-Indonesia
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m0 4h6m-6 4h6m-6 4h6M5 19a2 2 0 01-2-2V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5z"/></svg>
                        Bilingual ID / EN
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 2: Create homepage**

```blade
{{-- resources/views/new/pages/home.blade.php --}}
@extends('new.layouts.app')

@section('title', 'Bizmark.ID — Perizinan Usaha Indonesia')
@section('description', 'Platform legal-tech perizinan usaha di Indonesia. Cek kebutuhan izin usaha Anda dengan AI, gratis.')

@section('content')
    @include('new.sections.hero')
    @include('new.sections.problem-solution')
    @include('new.sections.tools')
    @include('new.sections.services')
    @include('new.sections.business-types')
    @include('new.sections.process')
    @include('new.sections.testimonials')
    @include('new.sections.blog')
    @include('new.sections.newsletter')
    @include('new.sections.faq')
    @include('new.sections.cta')
@endsection
```

---

### Task 11: Subpages (Services, Process, Pricing, About, Blog, Contact)

**Files:**
- Create: `resources/views/new/pages/services.blade.php`
- Create: `resources/views/new/pages/process.blade.php`
- Create: `resources/views/new/pages/pricing.blade.php`
- Create: `resources/views/new/pages/about.blade.php`
- Create: `resources/views/new/pages/blog.blade.php`
- Create: `resources/views/new/pages/contact.blade.php`

- [ ] **Step 1: Create subpage stubs (each extends the layout, yields content section with page-specific heading and placeholder content)**

Each subpage follows this pattern:

```blade
@extends('new.layouts.app')

@section('title', 'Layanan — Bizmark.ID')
@section('description', 'Layanan perizinan usaha lengkap dari Bizmark.ID')

@section('content')
<section class="pt-28 pb-12 sm:pt-32 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-4">Layanan</h1>
        <p class="text-base text-[#6B6560] max-w-2xl">Layanan perizinan usaha lengkap dari Bizmark.ID.</p>
        {{-- Content --}}
    </div>
</section>
@endsection
```

---

### Task 12: Build Verification

- [ ] **Step 1: Build assets**

```bash
cd /home/bizmark/bizmark.id && npm run build 2>&1 | tail -10
```

Expected: Build successful, `new-*.css` in output.

- [ ] **Step 2: Verify routes**

```bash
cd /home/bizmark/bizmark.id && php artisan route:list --path=new
```

Expected: 7 routes under `new.` prefix.

- [ ] **Step 3: Quick syntax check**

```bash
cd /home/bizmark/bizmark.id && php artisan view:clear && php artisan view:cache 2>&1
```

Expected: Views compile without errors.

- [ ] **Step 4: Access via browser**

Open `https://bizmark.id/new` — should show the redesigned landing page.
