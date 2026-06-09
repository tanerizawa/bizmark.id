# BizMark.ID — Comprehensive UX/UI Audit & Redesign Proposal

> **Date:** 10 June 2026  
> **Scope:** Landing page (homepage + all subpages)  
> **Stack:** Laravel 12 · Blade · Alpine.js 3.15 · Tailwind CSS v4 · Vite  
> **Existing Design Tokens:** `resources/css/design-tokens.css`

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [UX/UI Audit — Current Inconsistencies](#2-uxui-audit--current-inconsistencies)
3. [Proposed Design System](#3-proposed-design-system)
4. [Creative Direction Strategy](#4-creative-direction-strategy)
5. [Implementation Roadmap (Per Page)](#5-implementation-roadmap-per-page)
6. [Success Metrics](#6-success-metrics)

---

## 1. Executive Summary

BizMark.ID operates **3 distinct design systems** across admin, client portal, and landing pages — each with conflicting color palettes, typography treatments, and component patterns. The landing page has the strongest identity (navy + gold editorial), but subpages (services, pricing, process, about, blog) show visual drift from this core system.

**Goal:** Unify all public-facing pages under a single, cohesive design language that communicates authority, transparency, and modern legal-tech capability, while preserving the existing editorial-navy identity as the primary brand direction for public surfaces.

---

## 2. UX/UI Audit — Current Inconsistencies

### 2.1 Brand Identity Fragmentation

| Surface | Primary Color | Secondary Color | Accent | Typography | Theme |
|---------|-------------|----------------|--------|-----------|-------|
| Design Tokens (`:root`) | `#5B8DBE` (blue) | `#E8956F` (coral) | `#8b6914` (gold) | Inter + Fraunces | Warm editorial |
| Landing Page | `#0f172a` (navy) | `#b8860b` (gold) | `#b8860b` (gold) | Fraunces + Inter | Editorial premium |
| Admin Panel | Apple HIG colors | `#007AFF` blue | `#34C759` green | Inter only | Apple dark |
| Client Portal | `#0a66c2` (LinkedIn blue) | `#004182` | — | Inter only | Professional blue |

**Problem:** A user moving from landing to client portal to admin sees 3 completely different products. No consistent brand DNA.

### 2.2 CSS Architecture Conflicts

**Current file map:**
```
resources/css/
├── design-tokens.css   (247 lines — base tokens, semantic light/dark)
├── app.css             (306 lines — globals + Tailwind v4 import)
├── landing.css         (2,116 lines — custom component classes + mockups)
├── landing-theme.css   (880 lines — LEGACY, marked "DO NOT MODIFY")
├── admin.css           (1,296 lines — Apple HIG dark theme)
├── client.css          (34K+ — LinkedIn blue portal)
└── fa-client-subset.css (19.9K — Font Awesome subset)
```

**Key issues:**
- `landing-theme.css` is explicitly marked legacy but still has 880 lines of active styling
- `landing.css` has 2,116 lines including custom mockup classes that should be Blade components
- Token conflicts: `--color-primary` = `#5B8DBE` in design-tokens but `#0f172a` in landing-theme
- Landing redefines `--color-primary` to navy in its own `:root`, overriding design-tokens
- Admin's `--color-primary` defaults to `#5B8DBE` (blue) from design-tokens, which doesn't match the Apple HIG theme at all

### 2.3 Landing Page UX Issues

| Issue | Location | Severity | Description |
|-------|----------|----------|-------------|
| **Length** | Homepage | Medium | 13 sections = very long scroll. Some sections (segmentation, pain-solution) could merge. |
| **Hero CTA hierarchy** | Hero | Medium | Two primary CTAs + AI input field + "Lihat 4 alat gratis" competing for attention |
| **Emoji as icons** | Language switcher | High | `🇮🇩` `🇬🇧` used as toggle icons - violates design guidelines |
| **FA icon bloat** | All pages | Medium | Font Awesome 7.1 adds unnecessary weight; SVG sprites would be leaner |
| **Button style inconsistency** | Cross-page | Medium | Home uses pill `.btn` class; subpages sometimes use Tailwind `rounded-lg` |
| **Footer overload** | All pages | Low-Medium | 5 columns + city grid (20 cities) + integration strip = 7 visual rows |
| **Mobile nav** | All pages | Medium | Hamburger menu with long list; CTA buttons could be more prominent on mobile |
| **Dark mode** | Landing | Low | `prefers-color-scheme` supported but landing forces light mode — no user toggle |

### 2.4 Subpage-Specific Issues

**Services (`/layanan`):**
- 11 category tabs (anchor links) + 28 service cards = very high information density
- No visual distinction between categories (all white cards on white bg)
- Category tab bar is text-only, scrolls horizontally with no scroll indicator
- "Terfavorit" and "Wajib 2024" badges blend into the layout

**Process (`/proses`):**
- 6-step accordion works well but could be more visually engaging
- Guarantee metrics bar (SLA / 7d / 1PM / 6/6) uses small text

**Pricing (`/harga`):**
- Clean tier system, but pricing table is dense and hard to scan
- "Paling diminati" badge on tier 2 is good; table below lacks visual rhythm

**About (`/tentang`):**
- Horizontal timeline carousel needs better scroll affordance on mobile
- Values section (4 cards) is clean
- Team section has good role descriptions but no photos yet

**Blog:**
- Article body uses basic styling diverging from editorial design system
- Sidebar "Coba tools-nya" section could be visually cleaner
- Heavy internal linking throughout article body

### 2.5 Accessibility Gaps

| Check | Status | Notes |
|-------|--------|-------|
| Color contrast | Partially | Gold accent on white fails WCAG AA (3.27:1 for `#b8860b`); documented as decorative-only |
| Focus indicators | Partial | `focus-visible` set globally but needs verification across all interactive elements |
| Reduced motion | Implemented | Via `prefers-reduced-motion` in design-tokens |
| Screen reader labels | Partial | Skip-to-content exists; some interactive elements lack aria labels |
| Touch targets | Needs audit | Nav links and small UI elements need >=44px verification |
| Keyboard navigation | Needs audit | Process accordion, FAQ, tool interactions |

---

## 3. Proposed Design System

### 3.1 Brand Identity Architecture

Keep the existing editorial-navy identity for public pages (navy + gold is distinctive and effective) but enforce strict consistency across ALL public subpages. Admin and client portal themes remain separate (dark functional / LinkedIn blue) since they serve different user contexts.

### 3.2 Unified Color Palette (Public Surfaces)

```
BRAND CORE
Navy        #0f172a    --color-navy           Primary brand
Deep Navy   #0a0f1e    --color-navy-dark      Hero bg, dark sections
Gold        #b8860b    --color-gold           Accent (decorative only)
Gold Dark   #7a5908    --color-gold-dark      Body accent text (passes AA)
Gold Text   #946708    --color-gold-text      Small text labels (passes AA)
Gold Glow   rgba(184,134,11,.08)              Background wash

NEUTRAL
White       #ffffff
Off-white   #faf8f3    --surface-warm         Section background alt
Gray 100    #f1f5f9
Gray 200    #e2e8f0    --border-subtle
Gray 400    #94a3b8    --text-muted
Gray 600    #475569    --text-secondary
Gray 900    #0f172a    --text-primary

SEMANTIC
Success     #22c55e
Danger      #ef4444
Warning     #f59e0b
Info        #3b82f6

TOOLS ACCENT (Free tools ecosystem)
Emerald     #047857    --color-tools
Emerald Glow rgba(4,120,87,.08)
```

**Token override resolution:** All public page CSS MUST source `--color-primary` from the landing theme (`#0f172a` navy), not the design-tokens base (`#5B8DBE` blue). This means importing landing variables or establishing a new `public-theme.css`.

### 3.3 Typography Scale (Public Surfaces)

Preserve Fraunces (display/serif) + Inter (body/sans) — this is a strength of the current design.

```
DISPLAY (Fraunces)
Hero       clamp(2.5rem, 5.5vw, 4rem)    w800 lh1.04
Display    clamp(1.75rem, 3vw, 2.5rem)   w800 lh1.1
H1         1.75rem / 28px                w700 lh1.2
H2         1.375rem / 22px               w700 lh1.25
H3         1.125rem / 18px               w600 lh1.3

BODY (Inter)
Body-lg    1.125rem / 18px               w400 lh1.6
Body       1rem / 16px                   w400 lh1.6
Body-sm    0.875rem / 14px               w400 lh1.5
Caption    0.75rem / 12px                w500 lh1.4
Eyebrow    0.7rem / 11px                 w700 lh1.2 uppercase ls0.1em
```

### 3.4 Component Design Tokens

```
SPACING (4px base)
xs   0.25rem   sm   0.5rem    md   1rem
lg   1.5rem    xl   2rem      2xl  3rem      3xl  4rem

BORDER RADIUS
sm   6px   md   8px   lg   12px   xl   16px   2xl   24px   full   9999px

SHADOWS (Light mode)
soft   0 2px 15px rgba(0,0,0,.05)
md     0 4px 16px rgba(0,0,0,.08)
lg     0 8px 32px rgba(0,0,0,.1)
xl     0 16px 48px rgba(0,0,0,.12)
gold   0 0 0 1px rgba(184,134,11,.35), 0 4px 24px rgba(184,134,11,.12)

TRANSITIONS
fast   200ms cubic-bezier(0.4,0,0.2,1)
base   300ms cubic-bezier(0.4,0,0.2,1)
```

### 3.5 Component Library Standardization

| Component | Current State | Action |
|-----------|-------------|--------|
| **Button** | 3 CSS classes + inline styles | Unify under `x-button` with `variant`, `size` props. Remove legacy CSS. |
| **Card** | `.card` CSS + custom variants | Create `x-card` with `variant="elevated/bordered/plain"` |
| **Section** | Inline per-section code | Create `x-section` wrapper with consistent padding, bg options, heading slot |
| **Badge** | Exists for admin only | Extend to public with `variant="gold/green/neutral"` |
| **Accordion** | Hand-coded per page | Create `x-accordion` with Alpine `x-collapse` |
| **Tab/Nav** | Services anchor-link tabs | Create `x-tabs` with scroll + underline indicator |
| **Pricing Card** | Custom per tier | Create `x-pricing-card` with tier prop |
| **Timeline** | About page custom | Create `x-timeline` with year + content slots |
| **Testimonial** | Inline per case study | Create `x-testimonial-card` |
| **Article Card** | Inline in blog | Create `x-article-card` |

### 3.6 Unified CSS File Structure (Target)

```
resources/css/
├── design-tokens.css       (247 lines — KEEP as base, FIX primary color)
├── public-theme.css        (NEW — shared tokens for ALL public pages)
│   ├── Navy + Gold palette
│   ├── Typography scale
│   ├── Shared utility classes
│   └── @theme declarations
├── landing.css             (REDUCE to ~800 lines, move mockups to components)
├── landing-theme.css       (REMOVE — delete 880 lines after migration)
├── admin.css               (KEEP — separate theme)
└── client.css              (KEEP — separate theme)
```

**What `public-theme.css` solves:**
- Single source of truth for `--color-primary` = `#0f172a` (navy)
- All subpages import `public-theme.css` directly
- Landing imports `public-theme.css` + its own `landing.css`
- Eliminates the `design-tokens.css` vs `landing-theme.css` token conflict

---

## 4. Creative Direction Strategy

### 4.1 Brand Personality Framework

| Dimension | Current | Target |
|-----------|---------|--------|
| **Voice** | "Konsultan perizinan" | "Legal-tech platform" |
| **Tone** | Professional, helpful | Authoritative, empowering |
| **Visual metaphor** | Gold + navy = prestige | Gold + navy + data dashboards = modern authority |
| **User positioning** | "We handle permits" | "You manage your compliance ecosystem" |
| **Trust signals** | Testimonials, years | +Live progress, AI tools, transparent SLA |

### 4.2 Visual Design Principles

1. **Authority through restraint** — Use generous whitespace. Apply gold accent sparingly (CTAs, key highlights, decorative borders). Gold loses impact when overused.

2. **Data as decoration** — Show real metrics, dashboard previews, progress bars as visual elements. The live dashboard mockup on homepage is strong — extend this pattern.

3. **Editorial sophistication** — Fraunces serif for headlines creates premium/trustworthy feel. Maintain for all subpage H1s. Gold pull-quotes and borders for emphasis.

4. **Tool-first, service-second** — Free AI tools get emerald accent (`#047857`) to visually distinguish from paid services (gold). Semantic color coding helps navigate.

5. **Consistent section rhythm** — Every section: eyebrow label (gold) → Fraunces heading → body copy → optional CTA. Standardize vertical spacing at 4rem/6rem.

### 4.3 Page-by-Page Direction

**Homepage:**
- Keep 13-section arc but merge segmentation into ecosystem hub
- Make hero AI input more prominent (primary conversion tool)
- Reduce competing CTAs — keep one primary + one secondary link
- Standardize all section headers to `eyebrow (gold) → h2 (Fraunces) → body`

**Services (`/layanan`):**
- Category tabs need gold active indicator and icon support
- Service cards need consistent height, hover lift effect, gold left border accent
- Alternating warm off-white / white per category
- Sticky bottom "Perlu Rekomendasi" bar on mobile

**Process (`/proses`):**
- Step number circles with gold stroke
- Estimated timeline badges per step
- Guarantee metrics bar more prominent — use Fraunces for numbers

**Pricing (`/harga`):**
- Tier visual hierarchy: Free (emerald) → Self-service (gold) → Managed (navy)
- Alternating row backgrounds in pricing table
- FAQ accordion matching process pattern

**About (`/tentang`):**
- Horizontal scroll affordance on mobile timeline
- Values cards with gold decorative top borders
- Team section matching card pattern

**Blog:**
- Fraunces drop-caps or gold pull-quotes in article body
- Emerald accent for free tools sidebar
- Cleaner author bio
- Related articles matching homepage card pattern

### 4.4 Dark Mode & Icon Strategy

**Dark mode:** Do NOT add a user toggle to public pages. Landing already forces light mode by design for readability. Admin portal handles dark mode separately.

**Icons:** Replace Font Awesome with inline SVG icons over time. Immediate actions:
- Strip FA to only used icons
- Replace language switcher `🇮🇩`/`🇬🇧` with text labels "ID" / "EN"
- Never use emoji for UI controls

---

## 5. Implementation Roadmap

### Phase 1 — Foundation (Week 1)
- [ ] Create `public-theme.css` as single source of truth for all public pages
- [ ] Fix `--color-primary` conflict between design-tokens and landing-theme
- [ ] Migrate essential styles from `landing-theme.css` into `public-theme.css`
- [ ] Move reusable landing.css custom classes into Blade components
- [ ] Standardize `x-button` with variant/size props

### Phase 2 — Homepage Refinement (Week 1-2)
- [ ] Reduce hero competing CTAs
- [ ] Standardize all section headers to eyebrow + Fraunces h2
- [ ] Replace FA icons with SVG in ecosystem hub
- [ ] Replace emoji flags with text labels
- [ ] Create `x-section` component and migrate each section

### Phase 3 — Subpage Migration (Week 2-3)
- [ ] Services: Gold tab indicator, consistent cards, alternating bg
- [ ] Process: `x-accordion` component, enhanced step visuals
- [ ] Pricing: `x-pricing-card`, improved table scanability
- [ ] About: `x-timeline` component, enhanced values section

### Phase 4 — Blog & Long Tail (Week 3-4)
- [ ] Blog: Editorial typography, clean sidebar, consistent cards
- [ ] City landing pages: Apply component system
- [ ] Tool pages: Consistent emerald accent
- [ ] Legal pages: Minimal styling via public-theme.css

### Phase 5 — QA & Polish (Week 4)
- [ ] WCAG color contrast verification
- [ ] Keyboard navigation audit
- [ ] Responsive testing (375px / 768px / 1024px / 1440px)
- [ ] Lighthouse performance audit
- [ ] Remove `landing-theme.css` (target: -880 lines)

---

## 6. Success Metrics

| Metric | Current (Est.) | Target |
|--------|---------------|--------|
| Public CSS bundle size | ~200KB (3 files + FA) | <80KB (2 files) |
| Color palette systems | 3 conflicting | 1 unified |
| Pages using standard components | ~30% | >90% |
| Layout CSS in CSS files | ~3,000 lines | <1,000 lines (rest in Blade) |
| Accessibility (Lighthouse) | Unknown | >=90 |
| `landing-theme.css` | 880 lines | 0 lines (deleted) |
