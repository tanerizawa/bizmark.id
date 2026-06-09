<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="client">
<head>
    @include('partials.head')
    <title>@yield('title', 'Portal Klien') - Bizmark.ID</title>
    @vite(['resources/css/client.css', 'resources/js/client.js'])
</head>
<body class="bg-[var(--surface)] text-[var(--text-primary)] antialiased">
    @if(session()->hasAny(['success', 'error', 'warning', 'info']))
        <div class="max-w-7xl mx-auto px-4 pt-4">
            @if(session('success'))
                <div class="rounded-xl bg-[var(--color-success-bg)] border border-[var(--color-success)]/20 text-[var(--color-success)] px-4 py-3 text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rounded-xl bg-[var(--color-error-bg)] border border-[var(--color-error)]/20 text-[var(--color-error)] px-4 py-3 text-sm">{{ session('error') }}</div>
            @endif
        </div>
    @endif

    <div class="min-h-screen flex flex-col">
        @yield('content')
    </div>

    @include('partials.scripts')
</body>
</html>
