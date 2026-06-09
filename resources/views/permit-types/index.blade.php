@extends('layouts.admin')

@section('title', 'Jenis Izin')

@section('content')
@php
    $totalPermitTypes = \App\Models\PermitType::count();
    $activePermitTypes = \App\Models\PermitType::where('is_active', true)->count();
    $inactivePermitTypes = max(0, $totalPermitTypes - $activePermitTypes);
    $institutionCount = $institutions->count();
    $categoryDistribution = \App\Models\PermitType::select('category', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('category')
        ->pluck('total', 'category');
    $topCategory = $categoryDistribution->sortDesc()->keys()->first();
    $categoryStyles = [
        'environmental' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'icon' => 'fa-leaf', 'label' => 'Environmental'],
        'land' => ['bg' => 'rgba(175,82,222,0.15)', 'text' => 'rgba(175,82,222,1)', 'icon' => 'fa-map', 'label' => 'Land'],
        'building' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)', 'icon' => 'fa-building', 'label' => 'Building'],
        'transportation' => ['bg' => 'rgba(90,200,250,0.15)', 'text' => 'rgba(90,200,250,1)', 'icon' => 'fa-truck', 'label' => 'Transportation'],
        'business' => ['bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)', 'icon' => 'fa-briefcase', 'label' => 'Business'],
        'other' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'icon' => 'fa-layer-group', 'label' => 'Other'],
        'default' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'icon' => 'fa-file-alt', 'label' => 'General'],
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
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Taksonomi Perizinan</p>
                <h1 class="text-2xl md:text-3xl font-bold" style="color:#FFFFFF;">Katalog Jenis Izin</h1>
                <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.7);">
                    Kelola seluruh jenis izin, kategori, dan institusi penanggung jawab dalam satu tampilan terpadu.
                </p>
                <div class="flex flex-wrap gap-3 text-xs" style="color: rgba(235,235,245,0.6);">
                    <span><i class="fas fa-database mr-2"></i>{{ $totalPermitTypes }} jenis izin tercatat</span>
                    <span><i class="fas fa-clock mr-2"></i>Diperbarui {{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>
            <div class="flex flex-col items-start gap-3">
                <a href="{{ route('permit-types.create') }}" class="btn-primary-sm">
                    <i class="fas fa-plus mr-2"></i>Tambah Jenis Izin
                </a>
                <a href="{{ route('permit-types.create') }}" class="text-xs font-semibold" style="color: rgba(235,235,245,0.7);">
                    atau impor massal →
                </a>
            </div>
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('success') || session('error'))
        <div class="space-y-3 mb-5">
            @if(session('success'))
                <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-sm">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(255,59,48,0.12); border: 1px solid rgba(255,59,48,0.3); color: rgba(255,59,48,1);">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    {{-- Stats --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.55);">Total Jenis Izin</p>
            <p class="text-3xl font-bold" style="color:#FFFFFF;">{{ $totalPermitTypes }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Termasuk draft & inactive</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Aktif</p>
            <p class="text-3xl font-bold" style="color: rgba(52,199,89,1);">{{ $activePermitTypes }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $inactivePermitTypes }} nonaktif</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Institusi Mitra</p>
            <p class="text-3xl font-bold" style="color: rgba(10,132,255,1);">{{ $institutionCount }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Memiliki data penanggung jawab</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Kategori Teratas</p>
            <p class="text-3xl font-bold" style="color: rgba(255,149,0,1);">
                {{ ucfirst($topCategory ?? 'N/A') }}
            </p>
            <p class="text-xs" style="color: rgba(235,235,245,0.55);">{{ $topCategory ? $categoryDistribution[$topCategory] : 0 }} jenis izin</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-6 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Filter</p>
                <h2 class="text-lg font-semibold text-white">Susun Daftar Sesuai Kebutuhan</h2>
            </div>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $permitTypes->total() }} hasil ditemukan</p>
        </div>
        <form method="GET" action="{{ route('permit-types.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Pencarian</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, kode, deskripsi"
                           class="w-full px-4 py-2.5 rounded-apple text-sm text-white placeholder-gray-500"
                           style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Kategori</label>
                    <select name="category" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Institusi</label>
                    <select name="institution" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Institusi</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ request('institution') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Urutkan berdasarkan</label>
                    @php
                        $sortOptions = [
                            'name' => 'Nama',
                            'code' => 'Kode',
                            'category' => 'Kategori',
                            'avg_processing_days' => 'Durasi proses',
                        ];
                    @endphp
                    <select name="sort_by" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                            style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                        @foreach($sortOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('sort_by', 'name') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs uppercase tracking-widest mb-2 block" style="color: rgba(235,235,245,0.6);">Arah</label>
                        <select name="sort_order" class="w-full px-4 py-2.5 rounded-apple text-sm text-white"
                                style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
                            <option value="asc" {{ request('sort_order', 'asc') === 'asc' ? 'selected' : '' }}>A-Z</option>
                            <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Z-A</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="btn-primary-sm flex-1">
                            <i class="fas fa-search mr-2"></i>Terapkan
                        </button>
                        <a href="{{ route('permit-types.index') }}" class="btn-secondary-sm flex-1 text-center">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Permit types table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($permitTypes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Jenis izin</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Institusi</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Waktu & biaya</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Status</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permitTypes as $permit)
                            @php
                                $style = $categoryStyles[$permit->category] ?? $categoryStyles['default'];
                            @endphp
                            <tr class="border-b border-white/5 hover:bg-white/5 transition cursor-pointer" onclick="toggleDescription('desc-{{ $permit->id }}')">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-apple flex items-center justify-center" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }};">
                                            <i class="fas {{ $style['icon'] }}"></i>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $permit->name }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $permit->code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: {{ $style['bg'] }}; color: {{ $style['text'] }};">
                                        {{ ucfirst($permit->category) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm" style="color: rgba(235,235,245,0.85);">{{ $permit->institution?->name ?? '-' }}</p>
                                    @if($permit->institution)
                                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">{{ $permit->institution->type }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="space-y-1 text-xs" style="color: rgba(235,235,245,0.7);">
                                        <div><i class="fas fa-clock mr-2 text-apple-blue"></i>{{ $permit->avg_processing_days ? $permit->avg_processing_days . ' hari' : 'Durasi belum ditentukan' }}</div>
                                        <div><i class="fas fa-money-bill-wave mr-2 text-apple-green"></i>{{ $permit->estimated_cost_min && $permit->estimated_cost_max ? $permit->estimated_cost_range : 'Biaya belum ditentukan' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($permit->is_active)
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1);">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple" style="background: rgba(142,142,147,0.15); color: rgba(142,142,147,1);">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation();">
                                        <a href="{{ route('permit-types.show', $permit) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                        <a href="{{ route('permit-types.edit', $permit) }}" class="btn-primary-sm">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <form action="{{ route('permit-types.destroy', $permit) }}" method="POST" x-data @submit.prevent="if(confirm('Hapus jenis izin ini?')) $el.submit()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary-sm" style="background: rgba(255,59,48,0.12); color: rgba(255,59,48,0.9); border: 1px solid rgba(255,59,48,0.3);">
                                                <i class="fas fa-trash mr-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @if($permit->description || $permit->required_documents)
                                <tr id="desc-{{ $permit->id }}" class="hidden border-b border-white/5">
                                    <td colspan="6" class="px-6 pb-6">
                                        <div class="rounded-apple p-4" style="background: rgba(255,255,255,0.02);">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div>
                                                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.5);">Deskripsi</p>
                                                    <p class="text-sm" style="color: rgba(235,235,245,0.8);">
                                                        {{ $permit->description ?? 'Belum ada deskripsi tambahan.' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-xs uppercase tracking-widest mb-2" style="color: rgba(235,235,245,0.5);">Dokumen wajib</p>
                                                    @if(is_array($permit->required_documents) && count($permit->required_documents))
                                                        <ul class="list-disc pl-4 space-y-1 text-sm" style="color: rgba(235,235,245,0.75);">
                                                            @foreach($permit->required_documents as $doc)
                                                                <li>{{ $doc }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p class="text-sm" style="color: rgba(235,235,245,0.55);">Belum ditentukan.</p>
                                                    @endif
                                                </div>
                                                <div class="space-y-3">
                                                    <div>
                                                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.5);">Durasi rata-rata</p>
                                                        <p class="text-sm font-semibold" style="color:#FFFFFF;">{{ $permit->avg_processing_days ? $permit->avg_processing_days . ' hari' : '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.5);">Estimasi biaya</p>
                                                        <p class="text-sm font-semibold" style="color:#FFFFFF;">{{ $permit->estimated_cost_min && $permit->estimated_cost_max ? $permit->estimated_cost_range : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-white/5">
                {{ $permitTypes->links() }}
            </div>
        @else
            <div class="text-center py-12 space-y-4">
                <i class="fas fa-file-alt text-5xl" style="color: rgba(235,235,245,0.3);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada data jenis izin yang cocok dengan filter Anda.</p>
                <a href="{{ route('permit-types.create') }}" class="btn-primary-sm">
                    <i class="fas fa-plus mr-2"></i>Tambah Jenis Izin Pertama
                </a>
            </div>
        @endif
    </section>

<script>
function toggleDescription(id) {
    const row = document.getElementById(id);
    if (row) {
        row.classList.toggle('hidden');
    }
}
</script>
@endsection
