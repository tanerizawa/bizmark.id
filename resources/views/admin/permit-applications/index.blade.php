@extends('layouts.admin')

@section('title', 'Manajemen Permohonan Izin')
@section('page-title', 'Manajemen Permohonan Izin')

@section('content')
@php
    $totalApplications = $stats['total'] ?? 0;
    $reviewBacklog = ($stats['submitted'] ?? 0) + ($stats['under_review'] ?? 0);
    $quoted = $stats['quoted'] ?? 0;
    $inProgress = $stats['in_progress'] ?? 0;
    $completed = $stats['completed'] ?? 0;
    $activePipeline = max(0, $totalApplications - $completed);
    $completionRate = $totalApplications > 0 ? round(($completed / $totalApplications) * 100) : 0;

    $badgeVariants = [
        'draft' => 'neutral',
        'submitted' => 'info',
        'under_review' => 'warning',
        'document_incomplete' => 'danger',
        'quoted' => 'primary',
        'quotation_accepted' => 'success',
        'payment_pending' => 'warning',
        'payment_verified' => 'success',
        'in_progress' => 'info',
        'completed' => 'success',
        'cancelled' => 'neutral',
    ];
@endphp

    {{-- Hero / overview --}}
    <x-ui.card variant="flat" padding="md" class="relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-[var(--apple-blue)]/30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-[var(--apple-green)]/20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-xs uppercase tracking-[0.4em] text-gray-500 dark:text-gray-400">Manajemen Permohonan</p>
                    <h1 class="text-2xl md:text-xl font-bold text-gray-900 dark:text-white">
                        Database Lengkap Permohonan Izin
                    </h1>
                    <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">
                        Pantau permohonan baru, tindak lanjuti dokumen yang belum lengkap, dan lacak progres setiap pengajuan dalam satu tampilan.
                    </p>
                </div>
                <div class="text-sm space-y-2.5 text-gray-500 dark:text-gray-400">
                    <p><i class="fas fa-database mr-2"></i>{{ $totalApplications }} total permohonan</p>
                    <p><i class="fas fa-percentage mr-2"></i>Tingkat penyelesaian {{ $completionRate }}%</p>
                    <x-ui.button variant="ghost" size="sm" :href="route('admin.permits.index')">
                        <i class="fas fa-chart-network mr-2"></i>Kembali ke Dashboard
                    </x-ui.button>
                </div>
            </div>

            {{-- Stat cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <x-ui.card variant="flat" padding="sm" class="!bg-[var(--apple-red)]/10 !border-[var(--apple-red)]/20">
                    <p class="text-xs uppercase tracking-widest text-[var(--apple-red)]/80">Antrian Tinjauan</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1.5">{{ $reviewBacklog }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Menunggu verifikasi</p>
                </x-ui.card>
                <x-ui.card variant="flat" padding="sm" class="!bg-[var(--apple-purple)]/15 !border-[var(--apple-purple)]/25">
                    <p class="text-xs uppercase tracking-widest text-[var(--apple-purple)]/90">Butuh Penawaran</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1.5">{{ $quoted }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Menunggu tindak lanjut</p>
                </x-ui.card>
                <x-ui.card variant="flat" padding="sm" class="!bg-[var(--apple-blue)]/15 !border-[var(--apple-blue)]/25">
                    <p class="text-xs uppercase tracking-widest text-[var(--apple-blue)]/90">Dalam Proses</p>
                    <p class="text-lg font-bold text-[var(--apple-blue)] mt-1.5">{{ $inProgress }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activePipeline }} permohonan aktif</p>
                </x-ui.card>
                <x-ui.card variant="flat" padding="sm" class="!bg-[var(--apple-green)]/15 !border-[var(--apple-green)]/25">
                    <p class="text-xs uppercase tracking-widest text-[var(--apple-green)]/90">Selesai</p>
                    <p class="text-lg font-bold text-[var(--apple-green)] mt-1.5">{{ $completed }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tingkat penyelesaian {{ $completionRate }}%</p>
                </x-ui.card>
            </div>
        </div>
    </x-ui.card>

    {{-- Session alerts --}}
    @if(session('success') || session('error'))
        <div class="space-y-3 mb-5">
            @if(session('success'))
                <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
            @endif
            @if(session('error'))
                <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
            @endif
        </div>
    @endif

    {{-- Search & Filter --}}
    <x-ui.card variant="flat" padding="md" class="space-y-5 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-gray-500 dark:text-gray-400">Pencarian</p>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Cari Permohonan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Filter berdasarkan nomor permohonan, nama klien, atau status pengajuan.</p>
            </div>
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle"></i>
                Menampilkan {{ $applications->total() }} hasil
            </div>
        </div>
        <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="space-y-4">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'submitted_at') }}">
            <input type="hidden" name="sort_order" value="{{ request('sort_order', 'desc') }}">
            <div class="flex flex-col md:flex-row md:items-end md:gap-4 gap-3">
                <div class="flex-1">
                    <x-ui.input
                        name="search"
                        label="Nomor permohonan atau nama klien"
                        placeholder="Masukkan nomor permohonan atau nama klien"
                        :value="request('search')"
                        leading-icon="fa-solid fa-search"
                    />
                </div>
                <div class="w-full md:w-60">
                    <x-ui.select
                        name="status"
                        label="Filter status"
                        :options="['' => 'Semua Status'] + $statusOptions"
                        :value="request('status')"
                    />
                </div>
                <div class="flex gap-3 w-full md:w-auto">
                    <x-ui.button type="submit" size="sm">
                        <i class="fas fa-search mr-2"></i>Terapkan
                    </x-ui.button>
                    <x-ui.button variant="outline" size="sm" :href="route('admin.permit-applications.index')">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>

    {{-- Applications table --}}
    <x-ui.card variant="flat" padding="none">
        {{-- Table header with sort controls --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 p-5 border-b border-gray-200 dark:border-gray-700">
            <div>
                <p class="text-xs uppercase tracking-[0.35em] text-gray-500 dark:text-gray-400">Data Table</p>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Daftar permohonan</h3>
                <p class="text-xs mt-1 text-gray-500 dark:text-gray-400">
                    {{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }} dari {{ $applications->total() }} entri
                </p>
            </div>
            <form method="GET" action="{{ route('admin.permit-applications.index') }}" class="flex flex-wrap items-center gap-2 text-xs">
                @foreach(request()->except(['sort_by','sort_order','page']) as $param => $value)
                    <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                @endforeach
                <label class="text-gray-500 dark:text-gray-400">Urut:</label>
                <select name="sort_by"
                        class="px-3 py-2 rounded-xl text-xs text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <option value="submitted_at" {{ request('sort_by','submitted_at') === 'submitted_at' ? 'selected' : '' }}>Submit</option>
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Dibuat</option>
                    <option value="application_number" {{ request('sort_by') === 'application_number' ? 'selected' : '' }}>No Aplikasi</option>
                    <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                </select>
                <select name="sort_order"
                        class="px-3 py-2 rounded-xl text-xs text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none">
                    <option value="desc" {{ request('sort_order','desc') === 'desc' ? 'selected' : '' }}>↓ Terbaru</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>↑ Terlama</option>
                </select>
                <x-ui.button type="submit" size="sm">
                    <i class="fas fa-sort mr-2"></i>Terapkan
                </x-ui.button>
            </form>
        </div>

        {{-- Data table --}}
        @php
            $cellRenderers = [
                'application_number' => function ($row) {
                    return '<p class="text-sm font-semibold text-gray-900 dark:text-white">' . e($row->application_number) . '</p>'
                         . '<p class="text-xs mt-0.5 text-gray-500 dark:text-gray-400">ID internal: ' . e($row->id) . '</p>';
                },
                'client' => function ($row) {
                    if (! $row->client) {
                        return '<p class="text-sm text-gray-400">-</p>';
                    }
                    $html = '<p class="text-sm font-semibold text-gray-900 dark:text-white">' . e($row->client->name) . '</p>'
                          . '<p class="text-xs text-gray-500 dark:text-gray-400">' . e($row->client->email) . '</p>';
                    if (! empty($row->client->company_type)) {
                        $html .= '<p class="text-xs mt-0.5 text-gray-500 dark:text-gray-400">' . e(strtoupper($row->client->company_type)) . '</p>';
                    }
                    return $html;
                },
                'permit_type' => function ($row) {
                    $name = $row->permitType->name ?? ($row->form_data['permit_package'] ?? 'Tidak ada data');
                    $html = '<div class="text-sm font-medium text-gray-900 dark:text-white">' . e($name) . '</div>';
                    if (isset($row->business_context['primary_kbli'])) {
                        $html .= '<p class="text-xs mt-0.5 text-[var(--apple-purple)]/90">KBLI ' . e($row->business_context['primary_kbli']) . '</p>';
                    }
                    $html .= '<p class="text-xs mt-0.5 text-gray-500 dark:text-gray-400">Dokumen: ' . $row->documents->count() . ' file</p>';
                    return $html;
                },
                'status' => function ($row) use ($badgeVariants) {
                    $variant = $badgeVariants[$row->status] ?? 'neutral';
                    $label = $row->status_label ?? ucfirst(str_replace('_', ' ', $row->status));
                    $html = \Illuminate\Support\Facades\Blade::render(
                        '<x-ui.badge :variant="$variant">{{ $label }}</x-ui.badge>',
                        ['variant' => $variant, 'label' => $label]
                    );
                    if ($row->reviewedBy) {
                        $html .= '<p class="text-xs mt-1 text-gray-500 dark:text-gray-400">PIC: ' . e($row->reviewedBy->name) . '</p>';
                    }
                    return $html;
                },
                'timeline' => function ($row) {
                    return '<p class="text-xs text-gray-600 dark:text-gray-400">Dibuat: ' . e(optional($row->created_at)->format('d M Y')) . '</p>'
                         . '<p class="text-xs text-gray-600 dark:text-gray-400">Submit: ' . e(optional($row->submitted_at)->format('d M Y') ?? '—') . '</p>'
                         . '<p class="text-xs mt-1 text-gray-500 dark:text-gray-400">Update ' . e($row->updated_at->diffForHumans()) . '</p>';
                },
                'quotation' => function ($row) {
                    if ($row->quotation) {
                        return '<p class="text-sm font-semibold text-gray-900 dark:text-white">Rp ' . e(number_format($row->quotation->total_price, 0, ',', '.')) . '</p>'
                             . '<p class="text-xs text-gray-500 dark:text-gray-400">' . e(ucfirst($row->quotation->status ?? 'draft')) . '</p>';
                    }
                    return '<p class="text-xs text-gray-500 dark:text-gray-400">Belum ada quotation</p>';
                },
                'actions' => function ($row) {
                    $url = route('admin.permit-applications.show', $row->id);
                    return '<a href="' . e($url) . '" class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-1 dark:focus:ring-offset-gray-900"><i class="fas fa-eye mr-2"></i>Detail</a>';
                },
            ];
        @endphp

        <x-ui.table
            :columns="[
                ['key' => 'application_number', 'label' => 'Aplikasi'],
                ['key' => 'client', 'label' => 'Client'],
                ['key' => 'permit_type', 'label' => 'Jenis Izin'],
                ['key' => 'status', 'label' => 'Status'],
                ['key' => 'timeline', 'label' => 'Timeline'],
                ['key' => 'quotation', 'label' => 'Quotation / Nilai'],
                ['key' => 'actions', 'label' => 'Aksi', 'align' => 'right'],
            ]"
            :rows="$applications"
            :cellRenderers="$cellRenderers"
            :striped="true"
            :hoverable="true"
            empty-message="Tidak ada permohonan sesuai filter."
        />

        {{-- Pagination --}}
        @if($applications->hasPages())
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.pagination :paginator="$applications" variant="full" :show-info="true" />
            </div>
        @endif
    </x-ui.card>
@endsection
