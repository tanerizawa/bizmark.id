@extends('layouts.admin')

@section('title', 'Manajemen Lowongan Kerja')
@section('page-title', 'Manajemen Lowongan Kerja')

@php
    use App\Models\JobVacancy;

    $openCount = JobVacancy::where('status', 'open')->count();
    $draftCount = JobVacancy::where('status', 'draft')->count();
    $closedCount = JobVacancy::where('status', 'closed')->count();
    $totalApplicants = JobVacancy::sum('applications_count');
    $latestUpdate = JobVacancy::latest('updated_at')->first();

    $employmentOptions = $vacancies->pluck('employment_type')->filter()->unique()->values();
    $locationOptions = $vacancies->pluck('location')->filter()->unique()->values();

    $statusMeta = [
        'open' => ['label' => 'Aktif', 'bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)'],
        'draft' => ['label' => 'Draft', 'bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
        'closed' => ['label' => 'Ditutup', 'bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Talenta</p>
                <h1 class="text-2xl md:text-xl font-bold" style="color:#FFFFFF;">Katalog Lowongan Kerja</h1>
                <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                    Kelola lowongan kerja, pantau status publikasi, dan lacak performa pelamar dalam satu platform terpadu.
                </p>
                <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                    <span><i class="fas fa-briefcase mr-2"></i>{{ $openCount }} lowongan aktif</span>
                    <span><i class="fas fa-users mr-2"></i>{{ $totalApplicants }} pelamar tercatat</span>
                    @if($latestUpdate)
                        <span><i class="fas fa-clock mr-2"></i>Diperbarui {{ $latestUpdate->updated_at->diffForHumans() }}</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-col items-start gap-3">
                <a href="{{ route('admin.jobs.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Lowongan
                </a>
                <a href="{{ route('admin.jobs.create') }}" class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">
                    Lihat panduan rekrutmen →
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
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Lowongan Aktif</p>
            <p class="text-xl font-bold text-white">{{ $openCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sedang tayang untuk publik</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Draft</p>
            <p class="text-xl font-bold text-white">{{ $draftCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Belum dipublikasikan</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Ditutup</p>
            <p class="text-xl font-bold text-white">{{ $closedCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Lowongan selesai</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Total Pelamar</p>
            <p class="text-xl font-bold text-white">{{ $totalApplicants }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Akumulasi seluruh lowongan</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
                <h2 class="text-sm font-semibold text-white">Susun Daftar Lowongan</h2>
            </div>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $vacancies->total() }} hasil ditemukan</p>
        </div>
        <form method="GET" action="{{ route('admin.jobs.index') }}">
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-apple" style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-right: none; color: rgba(235,235,245,0.6);">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, posisi, lokasi"
                               class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                               style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
                    </div>
                </div>
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Tipe Kerja</label>
                    <select name="employment_type" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Tipe</option>
                        @foreach($employmentOptions as $type)
                            <option value="{{ $type }}" {{ request('employment_type') === $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Lokasi</label>
                    <select name="location" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Lokasi</option>
                        @foreach($locationOptions as $location)
                            <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary-sm">
                        <i class="fas fa-search mr-2"></i>Terapkan
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" class="btn-secondary-sm text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    {{-- Job list --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($vacancies->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Posisi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tipe</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Lokasi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Deadline</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pelamar</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Dibuat</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vacancies as $vacancy)
                            @php
                                $meta = $statusMeta[$vacancy->status] ?? ['label' => ucfirst($vacancy->status), 'bg' => 'rgba(255,255,255,0.15)', 'text' => '#FFFFFF'];
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $vacancy->title }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $vacancy->position ?? 'Posisi belum diisi' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs rounded-apple" style="background: rgba(255,255,255,0.08); color: rgba(235,235,245,0.9);">
                                        {{ $vacancy->employment_type ? ucfirst(str_replace('-', ' ', $vacancy->employment_type)) : 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color:#FFFFFF;"><i class="fas fa-map-marker-alt mr-2" style="color: rgba(235,235,245,0.5);"></i>{{ $vacancy->location ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $meta['bg'] }}; color: {{ $meta['text'] }};">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $vacancy->deadline ? \Carbon\Carbon::parse($vacancy->deadline)->format('d M Y') : 'Tidak ditentukan' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $vacancy->applications_count }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">Pelamar</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $vacancy->created_at->format('d M Y') }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $vacancy->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.jobs.show', $vacancy->id) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('admin.jobs.edit', $vacancy->id) }}" class="btn-primary-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('admin.jobs.destroy', $vacancy->id) }}" method="POST" x-data @submit.prevent="if(confirm('Hapus lowongan ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9); border: 1px solid rgba(255,59,48,0.3);">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($vacancies->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $vacancies->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 space-y-4">
                <i class="fas fa-briefcase text-5xl" style="color: rgba(235,235,245,0.3);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada lowongan yang sesuai filter Anda.</p>
                <a href="{{ route('admin.jobs.create') }}" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Tambah Lowongan Pertama
                </a>
            </div>
        @endif
    </section>
</div>
@endsection
