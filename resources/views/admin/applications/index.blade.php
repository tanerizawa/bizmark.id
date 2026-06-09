@extends('layouts.admin')

@section('title', 'Lamaran Masuk')
@section('page-title', 'Lamaran Masuk')

@section('content')
@php
    use App\Models\JobApplication;

    $statusLabels = [
        'pending' => 'Pending',
        'reviewed' => 'Direview',
        'interview' => 'Interview',
        'accepted' => 'Diterima',
        'rejected' => 'Ditolak',
    ];

    $statusColors = [
        'pending' => ['bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
        'reviewed' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)'],
        'interview' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)'],
        'accepted' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)'],
        'rejected' => ['bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
    ];

    $totalApplications = JobApplication::count();
    $todayApplications = JobApplication::whereDate('created_at', today())->count();
    $interviewPipeline = JobApplication::whereIn('status', ['reviewed', 'interview'])->count();
    $lastReview = JobApplication::whereNotNull('reviewed_at')->latest('reviewed_at')->first();
@endphp

{{-- Hero --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
        <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    </div>
    <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-3 max-w-3xl">
            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Manajemen Talenta</p>
            <h1 class="text-2xl md:text-xl font-bold text-white">Daftar Lamaran Kerja</h1>
            <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                Kelola proses rekrutmen kandidat dari tahap peninjauan hingga keputusan penerimaan secara terpusat.
            </p>
            <div class="text-xs flex flex-wrap gap-3" style="color: rgba(235,235,245,0.6);">
                <span><i class="fas fa-users mr-2"></i>{{ $totalApplications }} lamaran tersimpan</span>
                <span><i class="fas fa-calendar-plus mr-2"></i>{{ $todayApplications }} masuk hari ini</span>
                @if($lastReview)
                    <span><i class="fas fa-user-check mr-2"></i>Ditinjau {{ $lastReview->reviewed_at->diffForHumans() }}</span>
                @endif
            </div>
        </div>
        <div class="flex flex-col items-start gap-3">
            <a href="{{ route('admin.jobs.index') }}" class="btn-secondary-sm">
                <i class="fas fa-briefcase mr-2"></i>Kelola Lowongan
            </a>
            <a href="{{ route('admin.jobs.create') }}" class="btn-primary-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Lowongan
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
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Lamaran</p>
        <p class="text-xl font-bold text-white">{{ $totalApplications }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $todayApplications }} hari ini</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Proses Wawancara</p>
        <p class="text-xl font-bold text-white">{{ $interviewPipeline }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Ditinjau + wawancara</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Menunggu</p>
        <p class="text-xl font-bold text-white">{{ $statusCounts['pending'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Perlu peninjauan</p>
    </div>
    <div class="card-elevated rounded-apple-lg p-4 space-y-1">
        <p class="text-xs uppercase tracking-widest" style="color: rgba(255,69,58,0.9);">Ditolak</p>
        <p class="text-xl font-bold text-white">{{ $statusCounts['rejected'] ?? 0 }}</p>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">Akan dihubungi ulang</p>
    </div>
</section>

{{-- Status pills --}}
<section class="card-elevated rounded-apple-xl p-4 mb-5">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.applications.index') }}"
           class="status-pill {{ !request('status') ? 'active' : '' }}">
            Semua <span>{{ $totalApplications }}</span>
        </a>
        @foreach($statusLabels as $status => $label)
            <a href="{{ route('admin.applications.index', ['status' => $status]) }}"
               class="status-pill {{ request('status') === $status ? 'active' : '' }}">
                {{ $label }} <span>{{ $statusCounts[$status] ?? 0 }}</span>
            </a>
        @endforeach
    </div>
</section>

{{-- Filters --}}
<section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
            <h2 class="text-sm font-semibold text-white">Temukan Kandidat</h2>
        </div>
        <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $applications->total() }} lamaran ditemukan</p>
    </div>
    <form method="GET" action="{{ route('admin.applications.index') }}" class="flex flex-col gap-3 md:flex-row md:items-end">
        <div class="flex-1">
            <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
            <div class="flex">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atau email"
                       class="w-full px-4 py-2.5 rounded-r-apple text-sm text-white placeholder-gray-500"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35); border-left: none;">
            </div>
        </div>
        <div class="flex-1">
            <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Lowongan</label>
            <select name="job_vacancy_id" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                    style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                <option value="">Semua Lowongan</option>
                @foreach($vacancies as $vacancy)
                    <option value="{{ $vacancy->id }}" {{ request('job_vacancy_id') == $vacancy->id ? 'selected' : '' }}>
                        {{ $vacancy->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary-sm">
                <i class="fas fa-search mr-2"></i>
            </button>
            <a href="{{ route('admin.applications.index') }}" class="btn-secondary-sm text-center">
                Reset
            </a>
        </div>
    </form>
</section>

    {{-- Applications table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($applications->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kandidat</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Lowongan</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Pendidikan</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal Lamar</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $application)
                            @php $color = $statusColors[$application->status] ?? $statusColors['pending']; @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold" style="background: rgba(255,255,255,0.1); color:#FFFFFF;">
                                            {{ strtoupper(substr($application->full_name, 0, 1)) }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $application->full_name }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);"><i class="fas fa-envelope mr-2"></i>{{ $application->email }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);"><i class="fas fa-phone mr-2"></i>{{ $application->phone }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $application?->jobVacancy?->title ?? 'Position Deleted' ?? '-' }}</p>
                                    @if($application->has_experience_ukl_upl)
                                        <span class="inline-flex px-2 py-0.5 text-[10px] rounded-apple" style="background: rgba(52,199,89,0.18); color: rgba(52,199,89,1);">
                                            UKL-UPL Exp
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color:#FFFFFF;">{{ $application->education_level }} {{ $application->major }}</p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $application->institution }}</p>
                                    @if($application->gpa)
                                        <p class="text-xs" style="color: rgba(235,235,245,0.55);">IPK: {{ number_format($application->gpa, 2) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                        {{ $statusLabels[$application->status] ?? ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $application->created_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.applications.show', $application->id) }}" 
                                           class="btn-primary-sm"
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.applications.destroy', $application->id) }}"
                                              method="POST"
                                              class="inline"
                                              x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus kandidat {{ $application->full_name }}? Data test, interview, dan file CV/Portfolio akan terhapus.')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                                    style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1);"
                                                    onmouseover="this.style.background='rgba(255,59,48,0.25)'"
                                                    onmouseout="this.style.background='rgba(255,59,48,0.15)'"
                                                    title="Delete Application">
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
            @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $applications->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12 space-y-4">
                <i class="fas fa-inbox text-5xl" style="color: rgba(235,235,245,0.3);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada lamaran masuk.</p>
            </div>
        @endif
    </section>

<style>
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    border: 1px solid rgba(84,84,88,0.35);
    background: rgba(28,28,30,0.4);
    color: rgba(235,235,245,0.65);
    font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.2s;
}
.status-pill span {
    padding: 0.15rem 0.6rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    font-size: 0.7rem;
}
.status-pill.active {
    border-color: rgba(10,132,255,0.4);
    background: rgba(10,132,255,0.15);
    color: rgba(10,132,255,0.95);
}
</style>
@endsection
