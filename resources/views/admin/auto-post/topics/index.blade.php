@extends('layouts.admin')

@section('title', 'Topic Pool')
@section('page-title', 'Topic Pool')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-purple opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-blue opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Content Management</p>
                    <h1 class="text-2xl md:text-xl font-bold" style="color: #FFFFFF;">
                        Topic Pool Management
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Kelola topic untuk artikel auto-generated AI dengan mudah.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('auto-post.topics.create') }}" class="inline-flex items-center px-4 py-2.5 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                        <i class="fas fa-plus mr-2"></i>Tambah Topic
                    </a>
                    <p class="text-xs" style="color: rgba(235,235,245,0.65);">
                        <i class="fas fa-sync-alt mr-2"></i>Diperbarui: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                    </p>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Topics</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($stats['total']) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua topic</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Available</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color: rgba(52,199,89,1);">
                        {{ number_format($stats['available']) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Siap digunakan</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,159,10,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Scheduled</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color: rgba(255,159,10,1);">
                        {{ number_format($stats['scheduled']) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Terjadwal</p>
                </div>

                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(175,82,222,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Used</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color: #FFFFFF;">
                        {{ number_format($stats['used']) }}
                    </h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sudah dipakai</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Success Message --}}
    @if(session('success'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
    <div class="mb-5 p-4 rounded-apple-lg" style="background: rgba(255,69,58,0.12); border: 1px solid rgba(255,69,58,0.3); color: rgba(255,69,58,1);">
        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Filters --}}
    <div class="card-elevated rounded-apple-xl p-5 md:p-6 mb-6">
        <h3 class="text-lg font-semibold text-dark-text-primary mb-4">
            <i class="fas fa-filter mr-2 text-apple-blue"></i>Filter & Pencarian
        </h3>
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari topic..."
                    class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary" 
                    style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary" 
                        style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
            <div>
                <select name="category" class="w-full px-4 py-3 rounded-apple text-sm text-dark-text-primary" 
                        style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    <option value="">Semua Kategori</option>
                    <option value="tips" {{ request('category') === 'tips' ? 'selected' : '' }}>Tips</option>
                    <option value="guide" {{ request('category') === 'guide' ? 'selected' : '' }}>Guide</option>
                    <option value="case-study" {{ request('category') === 'case-study' ? 'selected' : '' }}>Case Study</option>
                    <option value="news" {{ request('category') === 'news' ? 'selected' : '' }}>News</option>
                    <option value="regulation" {{ request('category') === 'regulation' ? 'selected' : '' }}>Regulation</option>
                    <option value="general" {{ request('category') === 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-apple-blue rounded-apple hover:bg-apple-blue-dark transition-apple">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('auto-post.topics.index') }}" class="px-4 py-3 text-sm font-medium text-dark-text-primary rounded-apple transition-apple" 
                   style="background: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator);">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Topics Table --}}
    <div class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead style="background: var(--dark-bg-tertiary);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Topic
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Category
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Priority
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Used
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody style="background: var(--dark-bg-primary);">
                    @forelse($topics as $topic)
                        <tr class="hover-apple border-b" style="border-color: var(--dark-separator);">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-dark-text-primary">
                                    {{ $topic->title }}
                                </div>
                                @if($topic->description)
                                    <div class="text-xs text-dark-text-tertiary mt-1">
                                        {{ Str::limit($topic->description, 100) }}
                                    </div>
                                @endif
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach($topic->keywords as $keyword)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium" 
                                              style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.8);">
                                            {{ $keyword }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-apple text-xs font-medium 
                                    @if($topic->category === 'tips')
                                        " style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);
                                    @elseif($topic->category === 'guide')
                                        " style="background: rgba(175,82,222,0.2); color: rgba(175,82,222,1);
                                    @elseif($topic->category === 'case-study')
                                        " style="background: rgba(88,86,214,0.2); color: rgba(88,86,214,1);
                                    @elseif($topic->category === 'news')
                                        " style="background: rgba(255,69,58,0.2); color: rgba(255,69,58,1);
                                    @elseif($topic->category === 'regulation')
                                        " style="background: rgba(255,159,10,0.2); color: rgba(255,159,10,1);
                                    @else
                                        " style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.8);
                                    @endif">
                                    {{ ucfirst($topic->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-dark-text-primary">{{ $topic->priority }}</span>
                                    <span class="ml-2 text-xs text-dark-text-tertiary">/10</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-apple text-xs font-medium 
                                    @if($topic->status === 'pending') 
                                        " style="background: rgba(52,199,89,0.2); color: rgba(52,199,89,1);
                                    @elseif($topic->status === 'processing') 
                                        " style="background: rgba(255,159,10,0.2); color: rgba(255,159,10,1);
                                    @elseif($topic->status === 'published') 
                                        " style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);
                                    @elseif($topic->status === 'failed') 
                                        " style="background: rgba(255,69,58,0.2); color: rgba(255,69,58,1);
                                    @else 
                                        " style="background: rgba(255,255,255,0.1); color: rgba(235,235,245,0.8);
                                    @endif">
                                    {{ ucfirst($topic->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-dark-text-tertiary">
                                {{ $topic->views_count }}x
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('auto-post.topics.edit', $topic) }}" class="text-apple-blue hover:text-apple-blue-dark transition-apple">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    @if($topic->status !== 'processing')
                                        <form action="{{ route('auto-post.topics.destroy', $topic) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Yakin hapus topic ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-apple-red hover:text-red-400 transition-apple">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="space-y-3">
                                    <i class="fas fa-lightbulb text-4xl text-dark-text-tertiary"></i>
                                    <p class="text-sm text-dark-text-secondary">Tidak ada topic yang ditemukan.</p>
                                    <a href="{{ route('auto-post.topics.create') }}" class="inline-flex items-center px-4 py-2 bg-apple-blue text-white rounded-apple text-sm font-medium hover:bg-apple-blue-dark transition-apple">
                                        <i class="fas fa-plus mr-2"></i>Tambah Topic Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($topics->hasPages())
        <div class="px-6 py-4 border-t" style="border-color: var(--dark-separator);">
            {{ $topics->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
