@extends('layouts.admin')

@section('title', 'Template Email')
@section('page-title', 'Template Email')

@section('content')
{{-- Hero --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
        <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    </div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-3 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Konten</p>
            <h1 class="text-2xl md:text-xl font-bold text-white">Template Email</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola template email yang dapat digunakan kembali untuk kampanye dan komunikasi.
            </p>
            <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-file-alt mr-2"></i>{{ $stats['total'] }} total template</span>
                <span><i class="fas fa-check-circle mr-2"></i>{{ $stats['active'] }} aktif</span>
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.templates.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Buat Template
            </a>
        </div>
    </div>
</section>

{{-- Flash messages --}}
@if(session('success'))
    <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3 mb-5" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
        <i class="fas fa-check-circle"></i>
        <span class="text-sm">{{ session('success') }}</span>
    </div>
@endif

{{-- Stats --}}
<section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Template</p>
        <p class="text-xl font-bold text-white">{{ $stats['total'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Semua template</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Aktif</p>
        <p class="text-xl font-bold text-white">{{ $stats['active'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Siap digunakan</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Newsletter</p>
        <p class="text-xl font-bold text-white">{{ $stats['newsletter'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Buletin</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,159,10,0.9);">Promosi</p>
        <p class="text-xl font-bold text-white">{{ $stats['promotional'] }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Pemasaran</p>
    </div>
    </div>
</section>

{{-- Templates List --}}
<section class="card-elevated rounded-apple-xl overflow-hidden">
    @if($templates->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead style="background: rgba(28,28,30,0.45);">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Nama</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Subjek</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                        <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Dibuat</th>
                        <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-alt" style="color: rgba(10,132,255,0.9);"></i>
                                    <span class="text-sm font-semibold text-white">{{ $template->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($template->category === 'newsletter')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);">Newsletter</span>
                                @elseif($template->category === 'promotional')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(255,159,10,0.15); color: rgba(255,159,10,1);">Promosi</span>
                                @elseif($template->category === 'transactional')
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">Transaksional</span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.7);">{{ ucfirst($template->category) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm" style="color: rgba(235,235,245,0.7);">{{ \Illuminate\Support\Str::limit($template->subject, 50) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($template->is_active)
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">Aktif</span>
                                @else
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.5);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm" style="color: rgba(235,235,245,0.6);">{{ $template->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex gap-2">
                                    <a href="{{ route('admin.templates.show', $template) }}" class="btn-secondary-sm" title="Lihat">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.templates.edit', $template) }}" class="btn-primary-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Hapus template ini?')) $el.submit()">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9); border: 1px solid rgba(255,59,48,0.3);" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="space-y-3">
                                    <i class="fas fa-file-alt text-4xl" style="color: rgba(235,235,245,0.3);"></i>
                                    <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada template. Buat template pertama Anda!</p>
                                    <a href="{{ route('admin.templates.create') }}" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>Buat Template
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $templates->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16 space-y-3">
            <i class="fas fa-file-alt text-4xl" style="color: rgba(235,235,245,0.3);"></i>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada template. Buat template pertama Anda!</p>
            <a href="{{ route('admin.templates.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Buat Template
            </a>
        </div>
    @endif
</section>
@endsection
