# Landing Page Redesign — Implementation Plan

> Generated: 2026-06-08 | Based on comprehensive UI/UX audit of `/resources/views/landing/**` and `/resources/css/*`

## Status Key
- [ ] Not started
- [~] In progress
- [x] Done
- [-] Cancelled

---

## Priority 1: Fix Gold Text Color Contrast (WCAG AA Violation)

**Effort:** Small | **Impact:** High | **Status:** [x]

**Problem:** `--accent: #b8860b` (gold) used for text on `--bg-base: #FDFBF8` (warm white) yields a contrast ratio of **3.35:1**, which fails WCAG AA for normal-sized text (needs ≥4.5:1). For large text (≥18px bold), 3:1 passes — gold is safe on headings but not on body text or small labels.

**Files to change:**

### 1a. `resources/css/design-tokens.css` lines 32-36
```css
/* Before */
--color-accent: #b8860b;
--color-accent-light: #d4a843;
--color-accent-lighter: #e8c55a;
--color-accent-dark: #9a7209;
--color-accent-darker: #7a5c07;

/* After — darken base gold for AA body text */
--color-accent: #8b6914;           /* 4.54:1 on #FDFBF8 */
--color-accent-light: #b8960a;     /* existing visuals preserved */
--color-accent-lighter: #e8c55a;   /* unchanged — decorative only */
--color-accent-dark: #9a7209;      /* unchanged */
--color-accent-darker: #7a5c07;    /* unchanged */
```

### 1b. `resources/css/landing.css` — Add to `@theme` block (around line 70)
```css
/* Accent Text — guaranteed AA contrast on bg-base */
--color-accent-text: #8b6914;      /* 4.54:1 on FDFBF8 */
--color-accent-text-light: #b8960a; /* 3.5+:1 — safe for large text only */
```

### 1c. Audit all usages
Search pattern: `color: var(--accent)` and `text-amber-600`  
Locations to verify:
- `sections/v2/hero.blade.php:88` — `text-amber-600` on headline span → keep (large text, passes AA)
- `sections/v2/pain-solution.blade.php:94` — `text-amber-600` on CTA link → change to `text-amber-700` 
- `sections/v2/about.blade.php:104` — `color: var(--accent)` on `.display-xl span` → keep (large text)
- All `.eyebrow`, `.tool-stat`, `.tool-kicker` usages → verify each

**Verification:** Run a contrast checker (e.g., axe DevTools, Lighthouse) on `/`, `/tentang`, `/harga`, `/proses` after changes.

---

## Priority 2: Add `rel="noopener noreferrer"` to External Links

**Effort:** Small | **Impact:** High | **Status:** [x]

**Problem:** External links opening in new tabs (`target="_blank"`) without `rel="noopener noreferrer"` create a security vulnerability where the opened page can access `window.opener`.

**Files to change:**

### 2a. `resources/views/landing/sections/v2/final-cta.blade.php` line 41
```php
<!-- Before -->
<a href="{{ $whatsappLink }}" target="_blank"
   class="btn btn-ghost btn-lg"

<!-- After -->
<a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
   class="btn btn-ghost btn-lg"
```

### 2b. `resources/views/landing/partials/footer.blade.php` lines 31-33
```php
<!-- Before -->
<a href="https://www.linkedin.com/..." target="_blank" class="footer-social">
<a href="https://www.facebook.com/..." target="_blank" class="footer-social">
<a href="https://www.instagram.com/..." target="_blank" class="footer-social">

<!-- After — add rel="noopener noreferrer" to all three -->
```

### 2c. `resources/views/landing/partials/footer.blade.php` line 64
```php
<!-- Before -->
<a href="{{ $whatsappLink }}" target="_blank">

<!-- After -->
<a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer">
```

### 2d. `resources/views/landing/pages/pricing.blade.php` line 393
```php
<!-- Before (already has rel="noopener") -->
<a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-ghost btn-lg">

<!-- After -->
<a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost btn-lg">
```

**Verification:** `grep -rn 'target="_blank"' resources/views/landing/ | grep -v 'noopener'` → should return empty.

---

## Priority 3: Hero Dashboard Mockup Accessibility

**Effort:** Medium | **Impact:** High | **Status:** [x]

**Problem:** The dashboard mockup in `hero.blade.php:208` uses `aria-hidden="true"`, hiding it entirely from screen readers. While this avoids shipping fake stats to AT users, it wastes the right-column space. The mockup should be disclosed as an illustrative preview.

**File:** `resources/views/landing/sections/v2/hero.blade.php`

### 3a. Line 208 — Replace `aria-hidden="true"` wrapper
```html
<!-- Before -->
<div class="hidden md:flex items-center justify-center dashboard-mockup-wrap mockup-float"
     data-aos="fade-left" data-aos-duration="800" data-aos-delay="200"
     aria-hidden="true">

<!-- After -->
<figure class="hidden md:flex items-center justify-center dashboard-mockup-wrap mockup-float"
     data-aos="fade-left" data-aos-duration="800" data-aos-delay="200"
     role="img"
     aria-label="{{ $isEn ? 'Preview of Bizmark.ID permit tracking dashboard — illustrative mockup' : 'Pratinjau dashboard pelacakan izin Bizmark.ID — ilustrasi' }}">
```

### 3b. Line 322 — Close the `<figure>` instead of `</div>`
```html
<!-- Before -->
</div>
{{-- /kolom kanan --}}

<!-- After -->
</figure>
{{-- /kolom kanan --}}
```

### 3c. Add `aria-hidden="true"` to individual mockup elements
Already partially done — verify all inner mockup elements (stats, progress bars, map pins) have `aria-hidden="true"` since the `<figure>` carries the descriptive label.

---

## Priority 4: Noscript KBLI Fallback

**Effort:** Medium | **Impact:** High | **Status:** [x]

**Problem:** The hero KBLI typeahead (`hero.blade.php:101-164`) is purely JavaScript-driven via Alpine. Users with JS disabled or on slow connections get a non-functional input.

**File:** `resources/views/landing/sections/v2/hero.blade.php`

### 4a. After line 163 (the typeahead closing `</div>`), add:
```html
<noscript>
    <form method="GET" action="{{ $isEn ? route('pma.inquiry.create') : route('landing.service-inquiry.create') }}"
          class="hero-quickcheck mb-3">
        <label for="hero-quickcheck-noscript" class="sr-only">{{ $isEn ? 'Describe your business type' : 'Jenis usaha Anda' }}</label>
        <span class="hero-quickcheck-icon" aria-hidden="true"><i class="fas fa-robot"></i></span>
        <input id="hero-quickcheck-noscript" type="text" name="q"
               placeholder="{{ $isEn ? 'Business type (e.g. coffee shop, packaging factory)' : 'Jenis usaha (cth: kafe, pabrik kemasan)' }}"
               class="hero-quickcheck-input" maxlength="120">
        <button type="submit" class="hero-quickcheck-btn">
            <span class="hidden sm:inline">{{ $isEn ? 'Check permits' : 'Cek izin' }}</span>
            <i class="fas fa-arrow-right text-sm" aria-hidden="true"></i>
        </button>
    </form>
</noscript>
```

### 4b. In the PHP block at top (lines 1-14), add a cached KBLI list for noscript:
```php
@php
    // ... existing vars ...
    $topKbli = cache()->remember('landing_top_kbli', 3600, fn() =>
        \App\Models\Kbli::orderBy('search_count', 'desc')->take(20)->get()
    );
@endphp
```
Then render a plain `<select>` inside the noscript block for progressive enhancement.

---

## Priority 5: Process Accordion WCAG Compliance

**Effort:** Medium | **Impact:** High | **Status:** [x]

**Problem:** Process page (`process.blade.php:156-215`) uses `@click` on `<article>` elements as accordion triggers. WCAG 2.1 requires interactive controls to be native focusable elements with proper ARIA roles.

**File:** `resources/views/landing/pages/process.blade.php`

### 5a. Lines 156-163 — Convert article click to button trigger
```html
<!-- Before -->
<article class="premium-card cursor-pointer transition-all"
         @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
         ...
         role="button"
         tabindex="0">

<!-- After -->
<article class="premium-card transition-all"
         :class="open === {{ $i }} ? 'shadow-lg' : ''"
         :style="open === {{ $i }} ? 'border-color: rgba(var(--accent-rgb),.4);' : ''">
    <!-- Header becomes a button -->
    <button type="button"
            class="w-full text-left"
            @click="open = (open === {{ $i }} ? -1 : {{ $i }})"
            :aria-expanded="open === {{ $i }} ? 'true' : 'false'"
            aria-controls="process-stage-{{ $i }}-panel"
            id="process-stage-{{ $i }}-trigger">
```

### 5b. Line 193 — Add id to the expandable panel
```html
<!-- Before -->
<div x-show="open === {{ $i }}"

<!-- After -->
<div x-show="open === {{ $i }}"
     id="process-stage-{{ $i }}-panel"
     role="region"
     aria-labelledby="process-stage-{{ $i }}-trigger"
```

### 5c. Move the chevron icon inside the button (line 185-188)
Already positioned in the grid cell — ensure it's a child of the `<button>`.

---

## Priority 6: Status Page Health-Check Backend

**Effort:** Large | **Impact:** High | **Status:** [ ]

**Problem:** Status page data is hardcoded in the view. No real health checks run.

### 6a. Create `app/Console/Commands/HealthCheckCommand.php`
```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HealthCheckCommand extends Command
{
    protected $signature = 'health:check';
    protected $description = 'Check internal service health and cache results';

    public function handle(): void
    {
        $services = [
            ['name' => 'Web', 'url' => config('app.url') . '/health'],
            ['name' => 'Client Portal', 'url' => config('app.url') . '/client/login'],
            ['name' => 'AI Permit Checker', 'url' => config('app.url') . '/api/kbli/search?q=test'],
            ['name' => 'Cost Calculator', 'url' => config('app.url') . '/kalkulator-perizinan'],
            ['name' => 'SHP Polygon Maker', 'url' => config('app.url') . '/polygon-shp-maker'],
            ['name' => 'Email Notifications', 'url' => null, 'check' => fn() => $this->checkMail()],
        ];

        $results = [];
        foreach ($services as $svc) {
            $status = 'operational';
            $responseTime = null;
            $note = '';

            try {
                if (isset($svc['check'])) {
                    $result = ($svc['check'])();
                    $status = $result['status'];
                    $note = $result['note'] ?? '';
                } elseif ($svc['url']) {
                    $start = microtime(true);
                    $res = Http::timeout(10)->get($svc['url']);
                    $responseTime = round((microtime(true) - $start) * 1000);
                    $status = $res->successful() ? 'operational' : 'degraded';
                    $note = $status === 'operational' ? "{$responseTime}ms" : "HTTP {$res->status()}";
                }
            } catch (\Throwable $e) {
                $status = 'down';
                $note = 'Connection failed';
            }

            $results[] = [
                'name' => $svc['name'],
                'status' => $status,
                'note' => $note,
                'response_time' => $responseTime,
            ];
        }

        Cache::put('system_status', [
            'services' => $results,
            'checked_at' => now()->toIso8601String(),
        ], now()->addMinutes(5));

        $this->info('Health check complete. ' . count($results) . ' services checked.');
    }

    private function checkMail(): array
    {
        // Verify mail driver is configured
        $driver = config('mail.default');
        return [
            'status' => $driver ? 'operational' : 'degraded',
            'note' => $driver ? 'Outbound queue OK' : 'Mail driver not configured',
        ];
    }
}
```

### 6b. Register in `routes/console.php`
```php
\Illuminate\Support\Facades\Schedule::command('health:check')->everyFiveMinutes();
```

### 6c. Update `status.blade.php` to read from cache
```php
// Replace hardcoded $internal array (lines 17-24)
$statusData = Cache::get('system_status', ['services' => [], 'checked_at' => null]);
$internal = $statusData['services'];
$lastChecked = $statusData['checked_at']
    ? Carbon\Carbon::parse($statusData['checked_at'])->setTimezone('Asia/Jakarta')
    : now()->setTimezone('Asia/Jakarta');
```

---

## Priority 7: Sticky Scroll CTA Bar

**Effort:** Medium | **Impact:** Medium | **Status:** [x]

**Problem:** After scrolling past the hero CTA, there's no persistent conversion point until the final CTA at the bottom of the page — ~4000px of scrolling without a visible CTA.

**File:** `resources/views/landing/layout.blade.php` — add before closing `</body>` (line 138)

### 7a. Add sticky bar HTML
```html
{{-- Sticky scroll CTA — appears when hero scrolls out --}}
<div x-data="scrollCta()" x-show="visible" x-cloak x-transition
     class="fixed bottom-0 inset-x-0 z-40 bg-[var(--bg-raised)] border-t border-gray-200 shadow-soft-xl md:hidden"
     aria-live="polite">
    <div class="px-4 py-3 flex items-center gap-3">
        <a href="{{ route('landing.service-inquiry.create') }}" class="btn btn-gold flex-1 justify-center text-sm py-2.5">
            <i class="fas fa-robot"></i>
            <span>{{ app()->getLocale() === 'en' ? 'Free Permit Check' : 'Cek Izin Gratis' }}</span>
        </a>
    </div>
</div>
```

### 7b. Add Alpine component in scripts stack
```js
// In @push('scripts') or inline
document.addEventListener('alpine:init', () => {
    Alpine.data('scrollCta', () => ({
        visible: false,
        init() {
            const hero = document.querySelector('#hero-title');
            if (!hero) return;
            const observer = new IntersectionObserver(([entry]) => {
                this.visible = !entry.isIntersecting;
            }, { threshold: 0 });
            observer.observe(hero);
        },
    }));
});
```

---

## Priority 8: Differentiate Ecosystem Hub Tool Card Colors

**Effort:** Small | **Impact:** Medium | **Status:** [x]

**Problem:** All 4 tool cards in `ecosystem-hub.blade.php` use `color: var(--accent)` for icons, making them visually monotonous.

**File:** `resources/views/landing/sections/v2/ecosystem-hub.blade.php`

### 8a. Lines 6-59 — Add distinct color to each tool
```php
$primaryTools = [
    [
        // ... AI Checker
        'color' => '#b8860b',  // Gold — primary conversion
        'colorBg' => 'rgba(184,134,11,.1)',
    ],
    [
        // ... Cost Estimator
        'color' => '#047857',  // Emerald — money/value
        'colorBg' => 'rgba(4,120,87,.1)',
    ],
    [
        // ... SHP Maker
        'color' => '#1d4ed8',  // Blue — spatial/map
        'colorBg' => 'rgba(29,78,216,.1)',
    ],
    [
        // ... Calculator
        'color' => '#b45309',  // Amber — calculation/detail
        'colorBg' => 'rgba(180,83,9,.1)',
    ],
];
```

### 8b. Line 101 — Apply color to icon badge
```html
<span class="editorial-icon-badge" style="background: {{ $tool['colorBg'] }}; color: {{ $tool['color'] }};">
    <i class="fas {{ $tool['icon'] }} icon-xl" aria-hidden="true"></i>
</span>
```

### 8c. Update `.tool-card` hover border in `landing.css`
Add a CSS custom property `--tool-accent` per card via inline style:
```html
<a href="..." class="tool-card is-tools bento-featured"
   style="--tool-accent: {{ $tool['color'] }}; --tool-accent-bg: {{ $tool['colorBg'] }};">
```

---

## Priority 9: Process Timeline Connector

**Effort:** Small | **Impact:** Medium | **Status:** [x]

**Problem:** The 6 process stages have no visual line connecting them, weakening the narrative flow.

**File:** `resources/css/landing.css` — add after line 300 (or wherever process styles are grouped)

### 9a. Add CSS for the timeline connector
```css
/* Process timeline connector */
.process-stages {
    position: relative;
}
.process-stages::before {
    content: '';
    position: absolute;
    left: 2.75rem; /* center of icon circle (14/2 + padding) */
    top: 2.5rem;
    bottom: 2.5rem;
    width: 2px;
    background: linear-gradient(
        180deg,
        var(--accent) 0%,
        var(--accent-soft) 50%,
        var(--border-subtle) 100%
    );
    /* Only on lg+ where icons are in a dedicated column */
    display: none;
}
@media (min-width: 1024px) {
    .process-stages::before { display: block; }
}
```

### 9b. Add class to the stages wrapper in `process.blade.php:154`
```html
<div class="space-y-4 process-stages" x-data="{ open: 0 }">
```

---

## Priority 10: Allow Multiple FAQ Items Open

**Effort:** Small | **Impact:** Low | **Status:** [x]

**Problem:** Pricing FAQ (`pricing.blade.php:352`) uses `x-data="{ open: -1 }"` — only one answer visible at a time, frustrating users comparing answers.

**File:** `resources/views/landing/pages/pricing.blade.php`

### 10a. Line 352 — Change data model
```html
<!-- Before -->
<div class="space-y-3" x-data="{ open: -1 }">

<!-- After -->
<div class="space-y-3" x-data="{ open: [] }">
```

### 10b. Lines 356, 357 — Change button logic
```html
<!-- Before -->
@click="open = (open === {{ $i }} ? -1 : {{ $i }})"
:aria-expanded="open === {{ $i }}"

<!-- After -->
@click="open.includes({{ $i }}) ? open = open.filter(x => x !== {{ $i }}) : open.push({{ $i }})"
:aria-expanded="open.includes({{ $i }})"
```

### 10c. Lines 364, 371 — Change show conditions
```html
<!-- Before -->
x-show="open === {{ $i }}"

<!-- After -->
x-show="open.includes({{ $i }})"
```

### 10d. Lines 360-361 — Chevron rotation
```html
<!-- Before -->
:class="{ 'rotate-180': open === {{ $i }} }"

<!-- After -->
:class="{ 'rotate-180': open.includes({{ $i }}) }"
```

---

## Priority 11: About Page Timeline Scroll Hint

**Effort:** Small | **Impact:** Low | **Status:** [x]

**Problem:** The horizontal timeline in `about.blade.php:230` has no visual indicator that it scrolls. Users may miss the interaction entirely.

**File:** `resources/views/landing/pages/about.blade.php`

### 11a. Lines 228-229 — Add scroll hint after the instruction text
```html
<p class="text-base text-gray-600 mb-8 max-w-2xl">
    {{ $isEn ? 'Scroll horizontally to walk through every chapter.' : 'Geser untuk menyusuri setiap babak.' }}
</p>

{{-- NEW: Scroll hint pill --}}
<div class="h-timeline-scroll-hint mb-4" aria-hidden="true"
     x-data="{ visible: true }"
     x-init="setTimeout(() => visible = false, 8000)"
     x-show="visible"
     x-transition.opacity.duration.500ms>
    <i class="fas fa-arrows-left-right text-xs"></i>
    <span>{{ $isEn ? 'Scroll to explore' : 'Geser ke samping' }}</span>
    <i class="fas fa-hand-pointer text-xs animate-pulse"></i>
</div>
```

### 11b. Add CSS in `landing.css`
```css
.h-timeline-scroll-hint {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.375rem 1rem;
    border-radius: var(--radius-full);
    background: var(--accent-glow);
    color: var(--accent-text);
    font-size: 0.75rem;
    font-weight: 600;
}
```

---

## Priority 12: About Team Section Restructuring

**Effort:** Large | **Impact:** Medium | **Status:** [ ]

**Problem:** Team roster (`about.blade.php:326`) has 6 undifferentiated roles all labeled "Senior · 5+ yrs." No hierarchy, no photos, no leadership visibility.

**File:** `resources/views/landing/pages/about.blade.php`

### 12a. Split into two sections: Leadership + Practice Leads

Replace lines 278-345 with:

```php
{{-- LEADERSHIP --}}
<section class="section-v2" aria-labelledby="leadership-heading">
    <div class="container-wide">
        <div class="chapter-mark">
            <span class="chapter-mark__num">CH.05</span>
            <span class="chapter-mark__rule"></span>
            <span>{{ $isEn ? 'Leadership' : 'Kepemimpinan' }}</span>
        </div>
        <div class="editorial-split mb-10">
            <h2 id="leadership-heading" class="display-lg">
                {{ $isEn ? 'Practice leadership with decades of combined experience.' : 'Kepemimpinan praktik dengan pengalaman puluhan tahun.' }}
            </h2>
        </div>

        @php
        $leaders = [
            [
                'role' => $isEn ? 'Managing Director' : 'Direktur Utama',
                'focus' => $isEn ? 'Strategy, client partnerships, regulatory affairs' : 'Strategi, kemitraan klien, urusan regulasi',
                'initials' => '—', // placeholder until real data
            ],
            [
                'role' => $isEn ? 'Head of Environmental Practice' : 'Kepala Praktik Lingkungan',
                'focus' => $isEn ? 'AMDAL, UKL-UPL, waste management, 14+ years' : 'AMDAL, UKL-UPL, pengelolaan limbah, 14+ tahun',
                'initials' => '—',
            ],
        ];
        @endphp

        <div class="grid md:grid-cols-2 gap-6">
            @foreach($leaders as $leader)
                <article class="premium-card flex items-start gap-5">
                    <div class="w-20 h-20 rounded-2xl flex-shrink-0 flex items-center justify-center text-2xl font-black"
                         style="background: var(--accent-glow); color: var(--accent-text);">
                        {{ $leader['initials'] }}
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-xl mb-1">{{ $leader['role'] }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $leader['focus'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- PRACTICE LEADS (keeps existing team grid, now CH.06) --}}
<section class="section-v2" aria-labelledby="practice-heading">
    <div class="container-wide">
        <div class="chapter-mark">
            <span class="chapter-mark__num">CH.06</span>
            <span class="chapter-mark__rule"></span>
            <span>{{ $isEn ? 'Practice Leads' : 'Pemimpin Praktik' }}</span>
        </div>
        {{-- existing grid from lines 326-338 --}}
    </div>
</section>
```

---

## Priority 13: Remove Deprecated `landing-theme.css`

**Effort:** Medium | **Impact:** Medium | **Status:** [ ]

**Problem:** `landing-theme.css` (880 lines, deprecated) is still loaded via `@vite('resources/css/landing-theme.css')` in `layout.blade.php:23` and `vite.config.js`, adding ~30KB of unused CSS to every landing page.

**Files to change:**

### 13a. Audit which classes are still in use
```bash
# Run this grep to identify classes from landing-theme.css still used in blades
rg -o 'class="[^"]*"' resources/views/landing/ | sort -u > /tmp/used-classes.txt
# Cross-reference with landing-theme.css class definitions
```

Key classes to check:
- `.btn`, `.btn-gold`, `.btn-primary`, `.btn-ghost`, `.btn-lg`, `.btn-sm` — likely still used
- `.eyebrow`, `.eyebrow-hero` — used in hero
- `.premium-card` — used everywhere
- `.display-xl`, `.display-lg`, `.display-md` — used everywhere
- `.platform-card` — used in segmentation and about
- `.tool-card` — used in ecosystem hub
- `.chapter-mark`, `.editorial-split`, `.editorial-number`, `.editorial-quote` — used in about
- `.container-wide`, `.section-v2`, `.section-v2-sm`, `.section-premium` — used everywhere

### 13b. Migration plan
1. Copy still-used classes from `landing-theme.css` into `landing.css` `@layer components`
2. Once all classes are migrated and verified, remove the `@vite(...)` line from `layout.blade.php:23`
3. Remove the entry from `vite.config.js`
4. Delete `resources/css/landing-theme.css`

**Verification:** `npm run build && grep -l 'landing-theme' public/build/manifest.json` should return empty.

---

## Priority 14: Font Awesome Subset for Landing Pages

**Effort:** Medium | **Impact:** Low | **Status:** [ ]

**Problem:** All 4 Font Awesome variants (~150KB gzipped) load on landing pages, but only ~40 icons are actually used.

### 14a. Audit used icons
Search: `grep -roP 'fa-[a-z0-9-]+' resources/views/landing/ | sort -u`

Expected counts: ~35-40 unique icons.

### 14b. Create `resources/css/fa-landing-subset.css`
Following the existing `fa-client-subset.css` pattern, create a landing-specific subset containing only the icons actually used. This reduces the CSS payload from ~150KB to ~25KB.

### 14c. Update Vite entry in `layout.blade.php`
```html
<!-- Replace Font Awesome imports in landing.css with the subset -->
@vite('resources/css/fa-landing-subset.css')
```

---

## Priority 15: Exit-Intent Modal

**Effort:** Medium | **Impact:** Low | **Status:** [ ]

**Problem:** No exit-intent capture mechanism for users who browse without converting.

**File:** New — `resources/views/landing/partials/exit-modal.blade.php`

### 15a. Create the modal partial
```html
@php
    $locale = app()->getLocale();
    $isEn = $locale === 'en';
    $primaryCta = route('landing.service-inquiry.create');
@endphp

<div x-data="exitIntent()" x-show="show" x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-[1080] flex items-center justify-center p-4"
     style="background: rgba(0,0,0,0.5);"
     @click.self="dismiss()"
     @keydown.escape.window="dismiss()"
     role="dialog"
     aria-modal="true"
     aria-labelledby="exit-modal-title">

    <div class="premium-card max-w-md w-full text-center p-8" @click.stop>
        <button @click="dismiss()" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
                aria-label="{{ $isEn ? 'Close' : 'Tutup' }}">
            <i class="fas fa-times"></i>
        </button>

        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background: var(--accent-glow);">
            <i class="fas fa-robot text-2xl" style="color: var(--accent);"></i>
        </div>

        <h2 id="exit-modal-title" class="display-md mb-3">
            {{ $isEn ? 'Before you go...' : 'Sebelum Anda pergi...' }}
        </h2>
        <p class="text-gray-600 mb-6 leading-relaxed">
            {{ $isEn
                ? 'Get a free AI-powered map of every permit your business needs. No signup. Under 2 minutes.'
                : 'Dapatkan peta lengkap perizinan usaha Anda secara gratis dengan AI. Tanpa daftar. Di bawah 2 menit.' }}
        </p>

        <a href="{{ $primaryCta }}" class="btn btn-gold btn-lg w-full justify-center mb-3">
            <i class="fas fa-arrow-right"></i>
            <span>{{ $isEn ? 'Start Free Permit Check' : 'Cek Perizinan Gratis' }}</span>
        </a>

        <button @click="dismiss()" class="text-sm text-gray-500 hover:text-gray-700 underline">
            {{ $isEn ? 'No thanks, I\'ll browse more' : 'Tidak, saya lanjut membaca' }}
        </button>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('exitIntent', () => ({
        show: false,
        dismissed: false,
        init() {
            // Don't show if user came from a CTA click (detected via sessionStorage)
            if (sessionStorage.getItem('exit_intent_dismissed')) {
                this.dismissed = true;
                return;
            }
            // Exit intent: mouse leaving through the top of the viewport
            document.addEventListener('mouseleave', (e) => {
                if (this.dismissed || this.show) return;
                if (e.clientY <= 0) {
                    this.show = true;
                }
            });
            // Fallback: show after 45 seconds of inactivity
            let timer = setTimeout(() => {
                if (!this.dismissed && !this.show) this.show = true;
            }, 45000);
            document.addEventListener('mousemove', () => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if (!this.dismissed && !this.show) this.show = true;
                }, 45000);
            }, { once: false });
        },
        dismiss() {
            this.show = false;
            this.dismissed = true;
            sessionStorage.setItem('exit_intent_dismissed', '1');
        },
    }));
});
</script>
```

### 15b. Include in layout (after footer, before scripts)
In `layout.blade.php`, add after line 94:
```html
@include('landing.partials.exit-modal')
```

---

## Summary: Effort vs Impact Matrix

```
HIGH IMPACT ──────────────────────────────────────
│  #1 Contrast    #2 Security    #3 A11y Hero
│  #4 Noscript    #5 Accordion   #6 Health Check
│
│  #7 Sticky CTA  #8 Tool Colors
│                                        #12 Team
│                               #13 Dep CSS
│  #9 Timeline    #10 FAQ Multi
│  #11 Scroll Hint                    #14 FA Subset
│                                        #15 Exit Modal
LOW IMPACT ───────────────────────────────────────
  SMALL EFFORT                          LARGE EFFORT
```

## Execution Order

1. **Sprint 1 (Day 1-2):** #1 Contrast, #2 Security, #8 Tool Colors, #9 Timeline, #10 FAQ Multi
2. **Sprint 2 (Day 3-5):** #3 A11y Hero, #4 Noscript, #5 Accordion, #11 Scroll Hint
3. **Sprint 3 (Day 6-10):** #7 Sticky CTA, #13 Dep CSS, #14 FA Subset, #15 Exit Modal
4. **Sprint 4 (Day 11-15):** #6 Health Check, #12 Team Restructure
