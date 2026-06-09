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
