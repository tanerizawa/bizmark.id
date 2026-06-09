<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('meta_title', 'Estimasi Biaya Perizinan - Bizmark.ID')</title>
    <meta name="description" content="@yield('meta_description', 'Hitung estimasi biaya perizinan usaha Anda dengan AI. Gratis, cepat, dan akurat.')">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('meta_title', 'Estimasi Biaya Perizinan - Bizmark.ID')">
    <meta property="og:description" content="@yield('meta_description', 'Hitung estimasi biaya perizinan usaha Anda dengan AI.')">
    <meta property="og:image" content="https://bizmark.id/images/og-consultation.jpg">
    
    <!-- Organization Structured Data -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "Bizmark.ID",
        "legalName": "PT Cangah Pajaratan Mandiri",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "description": "Konsultan perizinan lingkungan dan bisnis Indonesia.",
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "+62-838-7960-2855",
            "contactType": "customer service",
            "availableLanguage": ["Indonesian", "English"]
        },
        "address": {
            "@@type": "PostalAddress",
            "addressCountry": "ID",
            "addressRegion": "West Java",
            "addressLocality": "Karawang"
        }
    }
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Performance: Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    
    <!-- Tailwind CSS (compiled) -->
    @vite('resources/css/public.css')
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome (non-render-blocking) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Landing Page Styles (for consistent theming) -->
    @include('landing.partials.critical-css')
    @vite('resources/css/landing-theme.css')
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Consultation-specific styles */
        .consultation-section {
            background: linear-gradient(180deg, var(--surface-warm) 0%, var(--surface) 100%);
        }
        
        .form-card {
            background: var(--surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 0.9375rem;
            border: 1px solid var(--border-medium);
            border-radius: var(--radius-md);
            transition: all 0.2s;
            background: var(--surface);
            color: var(--text-primary);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }
        
        .form-input::placeholder {
            color: var(--text-tertiary);
        }
        
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.25rem;
            padding-right: 2.5rem;
        }
        
        .option-card {
            position: relative;
            cursor: pointer;
            padding: 1rem;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-lg);
            transition: all 0.2s;
            background: var(--surface);
        }
        
        .option-card:hover {
            border-color: var(--color-accent);
            background: rgba(14, 165, 233, 0.02);
        }
        
        .option-card.selected {
            border-color: var(--color-accent);
            background: rgba(14, 165, 233, 0.05);
        }
        
        .option-card.selected::after {
            content: '✓';
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 1.25rem;
            height: 1.25rem;
            background: var(--color-accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
        }
        
        /* Quick Estimate Preview Card */
        .estimate-preview {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
            border-radius: var(--radius-xl);
            color: white;
            padding: 1.5rem;
        }
        
        .estimate-preview .label {
            font-size: 0.75rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .estimate-preview .value {
            font-size: 1.75rem;
            font-weight: 800;
        }
        
        /* Validation Error Styles */
        .validation-error {
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .validation-error-title {
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .validation-error-list {
            list-style: disc;
            list-style-position: inside;
            color: #dc2626;
            font-size: 0.875rem;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white text-gray-900">

<!-- Skip to main content link for accessibility -->
<a href="#main-content" class="skip-link">Lewati ke konten utama</a>

<!-- Landing Page Navbar (Consistent) -->
@include('landing.partials.navbar')
@include('landing.partials.mobile-menu')

<!-- Main Content -->
<main id="main-content">
    @yield('content')
</main>

<!-- Landing Page Footer (Consistent) -->
@include('landing.partials.footer')

<!-- Scripts for dropdown handling (vanilla JS - same as landing) -->
<script>
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = ['localeDropdown', 'toolsMenu', 'profileMenu'];
        dropdowns.forEach(function(id) {
            const dropdown = document.getElementById(id);
            const wrapper = document.getElementById(id.replace('Dropdown', 'Switcher').replace('Menu', 'Dropdown'));
            if (dropdown && wrapper && !wrapper.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
