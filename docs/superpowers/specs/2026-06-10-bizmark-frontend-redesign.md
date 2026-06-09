# Bizmark.ID — Frontend Redesign Spec

> **Prepared:** 2026-06-10
> **Design Direction:** Warm Professional
> **Stack:** Laravel 12 + Tailwind CSS v4 + Vite + Alpine.js + Blade Components
> **Staging Path:** `/new` (before migration to primary domain)

---

## 1. Design Philosophy

**"Tenang, berwibawa, tapi ramah"** — warm professional approach.

The current design uses Navy + Gold (editorial/authoritative). The redesign moves to a softer, more comfortable visual system:
- Warm off-white backgrounds (not pure white — reduces eye strain)
- Teal accent (calming, trustworthy, modern) instead of gold
- Single font family (Plus Jakarta Sans) for consistency
- Generous whitespace, soft borders, gentle shadows
- Reduced contrast without sacrificing WCAG AA compliance

## 2. Color Palette

| Role | Hex | CSS Variable | Usage |
|------|-----|-------------|-------|
| Background | `#F8F6F3` | `--bg-base` | Page background |
| Surface | `#FFFFFF` | `--bg-surface` | Cards, panels, modals |
| Surface Raised | `#FCFAF8` | `--bg-raised` | Section alt backgrounds |
| Text Primary | `#2D2A24` | `--text-primary` | Body text, headings |
| Text Secondary | `#6B6560` | `--text-secondary` | Secondary text, metadata |
| Text Muted | `#9C9690` | `--text-muted` | Placeholder, disabled |
| Accent (Teal) | `#0D9488` | `--accent` | Primary CTA, links, focus rings |
| Accent Dark | `#0F766E` | `--accent-dark` | Hover states |
| Accent Soft | `#CCFBF1` | `--accent-soft` | Badge backgrounds, glow |
| Accent Text | `#115E59` | `--accent-text` | Text on accent bg |
| Border | `#E5E0DB` | `--border` | Card borders, dividers |
| Border Light | `#F0ECE6` | `--border-light` | Subtle separators |
| Muted | `#F0ECE6` | `--muted` | Muted backgrounds |
| Success | `#10B981` | `--success` | Approved status |
| Warning | `#F59E0B` | `--warning` | In progress status |
| Error | `#EF4444` | `--error` | Rejected status |

### WCAG Compliance

| Token | Light BG | Ratio | AA? |
|-------|----------|-------|-----|
| Text Primary #2D2A24 | #F8F6F3 | 11.5:1 | ✅ AAA |
| Text Secondary #6B6560 | #F8F6F3 | 5.2:1 | ✅ AA |
| Accent #0D9488 | #FFFFFF | 3.5:1 | ⚠️ (decorative/large only) |
| Accent Text #115E59 | #CCFBF1 | 7.1:1 | ✅ AA |
| White text #FFFFFF | #0D9488 | 4.8:1 | ✅ AA |

## 3. Typography

- **Font:** Plus Jakarta Sans (single font family across all surfaces)
- **Weights:** 400 (regular), 500 (medium), 600 (semibold), 700 (bold), 800 (extrabold)

### Type Scale

| Token | Size | Weight | Usage |
|-------|------|--------|-------|
| `--text-hero` | clamp(2.25rem, 4vw, 3.5rem) | 800 | Hero headline |
| `--text-display` | clamp(1.75rem, 3vw, 2.5rem) | 700 | Section titles |
| `--text-title` | clamp(1.25rem, 2vw, 1.5rem) | 600 | Card titles |
| `--text-body` | 1rem / 16px | 400 | Paragraphs |
| `--text-body-sm` | 0.875rem / 14px | 400 | Compact text |
| `--text-caption` | 0.75rem / 12px | 500 | Labels, metadata |
| `--text-micro` | 0.6875rem / 11px | 600 | Eyebrow, badges |
| `--text-hero` line-height: 1.08 | Body line-height: 1.65 |

## 4. Layout Architecture

### Routing (`/new` prefix)

```
GET /new                    → new.pages.home
GET /new/layanan            → new.pages.services
GET /new/proses             → new.pages.process
GET /new/harga              → new.pages.pricing
GET /new/tentang            → new.pages.about
GET /new/blog               → new.pages.blog
GET /new/kontak             → new.pages.contact
```

All routes under `middleware('locale:id')` group, matching existing app locale handling.

### File Structure

```
resources/views/new/
├── layouts/
│   └── app.blade.php           # Main layout (navbar + footer + yield)
├── pages/
│   ├── home.blade.php          # Landing page
│   ├── services.blade.php      # Layanan overview
│   ├── process.blade.php       # Cara kerja
│   ├── pricing.blade.php       # Harga
│   ├── about.blade.php         # Tentang
│   ├── blog.blade.php          # Blog index
│   └── contact.blade.php       # Kontak
├── partials/
│   ├── navbar.blade.php        # Navigation
│   ├── footer.blade.php        # Footer
│   └── head.blade.php          # <head> metadata
└── sections/
    ├── hero.blade.php
    ├── problem-solution.blade.php
    ├── tools.blade.php
    ├── services.blade.php
    ├── business-types.blade.php
    ├── process.blade.php
    ├── testimonials.blade.php
    ├── blog.blade.php
    ├── newsletter.blade.php
    ├── faq.blade.php
    └── cta.blade.php

resources/css/
├── new.css                     # Entry point
├── new-theme.css               # Design tokens
```

### Layout: Public Pages

```
┌──────────────────────────────────────────┐
│  Navbar (fixed, bg-white/90, blur)       │
├──────────────────────────────────────────┤
│                                          │
│  Hero (full-width, warm gradient bg)     │
│                                          │
├──────────────────────────────────────────┤
│  Sections (container-wide, max-w-7xl)    │
│  — Problem/Solution                     │
│  — Free Tools (bento)                   │
│  — Services (grid)                      │
│  — Business Types                       │
│  — Process (4-step flow)                │
│  — Testimonials                         │
│  — Blog + Newsletter                    │
│  — FAQ                                  │
│  — Final CTA                            │
│                                          │
├──────────────────────────────────────────┤
│  Footer                                 │
├──────────────────────────────────────────┤
│  WhatsApp FAB (fixed, bottom-right)      │
└──────────────────────────────────────────┘
```

## 5. Navbar Design

- Fixed top, height 64px
- Background: `rgba(255,255,255,0.92)` with `backdrop-filter: blur(12px)`
- Border-bottom: 1px `--border-light` (hidden when at top, appears on scroll)
- Logo left, nav links center, CTA right
- Desktop: full nav links visible
- Mobile (≤768px): hamburger → slide-in panel from right
- Nav links: Layanan, Proses, Harga, Blog, About — dengan dropdown untuk Layanan
- CTA: "Cek Kebutuhan Izin" button (teal primary)

## 6. Component Specifications

### Buttons

```
Primary:   bg-[#0D9488] text-white rounded-[10px] px-6 py-3 font-semibold
           hover:bg-[#0F766E] hover:-translate-y-[1px] shadow-sm
Secondary: bg-transparent border border-[#0D9488] text-[#0D9488] rounded-[10px]
           hover:bg-[#0D9488]/5
Ghost:     bg-transparent text-[#6B6560] hover:bg-[#F0ECE6]
Tools:     bg-[#059669] text-white (for free/tools CTAs)
```

All buttons: transition 200ms, focus-visible ring 2px accent, touch target ≥44px.

### Cards

```
Default:   bg-white border border-[#E5E0DB] rounded-xl p-6
Interactive: + hover:border-[#0D9488]/30 hover:shadow-sm hover:-translate-y-[2px]
Highlight: + border-t-2 border-t-[#0D9488] shadow-[0_0_0_1px_rgba(13,148,136,0.08)]
```

### Form Input (Hero Search)

```
Wrapper:   flex items-center gap-2 bg-white border border-[#E5E0DB]
           rounded-xl p-1.5 focus-within:border-[#0D9488]
           focus-within:ring-2 focus-within:ring-[#0D9488]/20
Input:     flex-1 border-none outline-none bg-transparent px-3 py-2.5
Button:    Primary button, flex-shrink-0
```

### Status Badges

```
Approved:  bg-[#D1FAE5] text-[#065F46]
Progress:  bg-[#FEF3C7] text-[#92400E]
Review:    bg-[#DBEAFE] text-[#1E40AF]
Rejected:  bg-[#FEE2E2] text-[#991B1B]
```

### Navigation Dropdown

- Position absolute, top 100% + 8px
- Min-width 240px, rounded-xl, border, shadow-lg
- Grid 2-column for service categories
- Each item: icon + title + description small text
- Smooth fade-in animation

## 7. Section Details (Homepage)

### 7.1 Hero
- Warm gradient background (very subtle: `#F8F6F3` → `#F0ECE6`)
- Left column: eyebrow "Gratis Selamanya · Tanpa Daftar", display-xl headline, body text, AI search input, trust hint text
- Trust bar below: 3 metrics (12+ Tahun, Cakupan Se-Indonesia, Bilingual ID/EN)
- Right column: soft illustration/dashboard mockup
- Decorative: subtle geometric pattern overlay (low opacity)

### 7.2 Problem/Solution (Bento)
- 3 cards in bento layout (featured left, 2 stacked right on desktop)
- Each card: problem statement (red/amber side), solution (teal side)
- Icon per card
- "Cek kebutuhan perizinan saya — gratis" CTA at bottom right

### 7.3 Free Tools (Bento Grid)
- "Pakai platformnya sendiri. Tanpa biaya."
- 4 tool cards in 1.45fr/1fr bento grid
- Featured card (2 rows): AI Checker
- Medium cards: Estimasi Biaya, Polygon SHP Maker
- Wide bottom: Kalkulator Biaya Perizinan
- Each card has preview illustration area + title + description + CTA

### 7.4 Services
- 6 service cards in 3-column grid
- Each: icon, title, SLA badge, features list, "Pelajari" link
- Categories: Limbah B3, Lingkungan, Gedung, Izin Usaha, PMA, Operasional

### 7.5 Business Types
- "Usaha Anda termasuk yang mana?"
- 3 cards: UMKM/Startup, Korporasi, PMA
- Numbered styling (01/03, 02/03, 03/03)
- "Populer" badge on Korporasi
- Link to specific service page

### 7.6 Process
- "Proses yang mengutamakan kejelasan"
- 4 vertical steps with connector line
- Each: number, title, description, timeline badge
- Center step highlighted (Pelaksanaan & Pelaporan)
- "Terpantau via Portal Klien" indicator

### 7.7 Testimonials
- 3 client quote cards
- Each: quote text, "Klien Terverifikasi" badge, name, title, company
- Light background section
- Case study note at bottom

### 7.8 Blog + Newsletter
- Left: featured article (large card with image)
- Right: 2 article list items
- Below: newsletter signup card (email input + submit + meta text)

### 7.9 FAQ
- 6 accordion items
- Click to expand with smooth height transition
- Chevron rotation animation
- "Hubungi tim kami" link at top

### 7.10 Final CTA
- "Siap mengurus perizinan usaha Anda?"
- 2 buttons: "Cek Perizinan AI — Gratis" (primary), "Hubungi via WhatsApp" (secondary)
- Trust badges: 12+ Tahun, Bilingual ID/EN, Cakupan Se-Indonesia

## 8. Footer
- 5 columns: Brand + description, Navigasi, Platform, Hubungi Kami, Legal
- Social media icons (LinkedIn, Facebook, Instagram)
- City coverage list (compact, wrapped)
- System integration badges (OSS-RBA, BKPM, KBLI, etc.)
- Copyright + company name

## 9. Mobile Adaptations
- Navbar: hamburger menu, slide-in panel
- Hero: stack vertically (text top, illustration below)
- Bento grid: single column
- Services grid: 1 column
- Business types: horizontal scroll snap
- Process: vertical with rotated arrows
- Footer: 2 columns stacked

## 10. Accessibility
- Focus-visible rings on all interactive elements
- prefers-reduced-motion: disable all animations
- Touch targets ≥44px
- Semantic heading hierarchy (h1 → h2 → h3)
- All icons: aria-hidden="true"
- Color is never the only indicator
- Form inputs with associated labels
- Skip-to-content link

## 11. Performance
- One CSS entry point (`new.css`) — no unused CSS
- Font: Google Fonts with `display=swap`
- Images: lazy loading
- Alpine.js for interactivity (no jQuery)
- Minimal JavaScript footprint
- Vite code splitting

## 12. Dark Mode (Future Phase)
- Not in initial scope — force light mode for `/new`
- Design tokens structured to support dark mode later
- Use CSS custom properties so dark mode is a one-file change
