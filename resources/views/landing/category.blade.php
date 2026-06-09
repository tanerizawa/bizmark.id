<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $categoryLabel }} - Bizmark.ID</title>
    
    @vite(['resources/css/public.css', 'resources/css/landing-theme.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --apple-blue: #007AFF;
            --dark-bg: #000000;
            --dark-bg-secondary: #1C1C1E;
            --dark-bg-tertiary: #2C2C2E;
            --dark-separator: rgba(84, 84, 88, 0.35);
        }
        
        body {
            background: var(--dark-bg);
            color: white;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(28, 28, 30, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--dark-separator);
            z-index: 1000;
        }
        
        .article-card {
            background: var(--dark-bg-tertiary);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid var(--dark-separator);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .article-card:hover {
            transform: translateY(-8px);
            border-color: var(--apple-blue);
            box-shadow: 0 15px 35px rgba(0, 122, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="{{ route('landing.id') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <span class="text-xl font-bold">Bizmark.ID</span>
                </a>
                
                <div class="flex items-center space-x-8">
                    <a href="{{ route('landing.id') }}" class="hover:text-blue-400 transition">Beranda</a>
                    <a href="{{ route('blog.index.id') }}" class="hover:text-blue-400 transition">Artikel</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
    <!-- Header -->
    <section class="pt-32 pb-12 px-4 bg-gradient-to-br from-gray-900 via-gray-900 to-gray-800">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-4 text-sm text-gray-400">
                <a href="{{ route('landing.id') }}" class="hover:text-blue-400">Beranda</a>
                <span class="mx-2">/</span>
                <a href="{{ route('blog.index.id') }}" class="hover:text-blue-400">Artikel</a>
                <span class="mx-2">/</span>
                <span>{{ $categoryLabel }}</span>
            </div>
            
            <h1 class="text-5xl font-bold mb-4">{{ $categoryLabel }}</h1>
            <p class="text-xl text-gray-400">{{ $articles->total() }} artikel tersedia</p>
        </div>
    </section>

    <!-- Articles Grid -->
    <section class="py-12 px-4">
        <div class="container mx-auto max-w-7xl">
            @if($articles->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($articles as $article)
                <div class="article-card">
                    @if($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" loading="lazy" decoding="async" class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-newspaper text-white text-4xl"></i>
                    </div>
                    @endif
                    
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="mb-3">
                            <span class="inline-block px-3 py-1 bg-blue-500/20 text-blue-400 rounded-full text-xs font-semibold">
                                {{ $article->category_label }}
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold mb-3 line-clamp-2">
                            <a href="{{ route('blog.article.id', $article->slug) }}" class="hover:text-blue-400 transition">
                                {{ $article->title }}
                            </a>
                        </h3>
                        
                        <p class="text-gray-400 mb-4 line-clamp-3 text-sm flex-1">
                            {{ $article->excerpt }}
                        </p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t border-gray-700 mt-auto">
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $article->published_at->format('d M Y') }}
                            </span>
                            <span>
                                <i class="fas fa-eye mr-1"></i>
                                {{ number_format($article->views_count) }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($articles->hasPages())
            <div class="mt-12">
                {{ $articles->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-20">
                <i class="fas fa-folder-open text-6xl text-gray-600 mb-4"></i>
                <p class="text-xl text-gray-400">Belum ada artikel di kategori ini</p>
                <a href="{{ route('blog.index.id') }}" class="inline-block mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                    Lihat Semua Artikel
                </a>
            </div>
            @endif
        </div>
    </section>
    </div>

    <!-- Footer -->
    <footer class="py-8 px-4 border-t border-gray-800 mt-auto">
        <div class="container mx-auto max-w-7xl text-center text-gray-500">
            <p>&copy; 2025 Bizmark.ID - PT Cangah Pajaratan Mandiri. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
