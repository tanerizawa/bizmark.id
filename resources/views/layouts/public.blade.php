<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700;800&display=swap" rel="stylesheet">

    <title>@yield('title', config('app.name', 'Bizmark.ID'))</title>

    @vite(['resources/css/public.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>
    @include('partials.navbar.public')

    <main>
        @yield('content')
    </main>

    @include('partials.footer.public')

    @stack('scripts')
    <script src="{{ asset('js/currency-helper.js') }}"></script>
</body>
</html>
