{{-- Service Cost Requests Tab --}}
@php
    $stats = $serviceCostRequestsStats ?? [
        'total' => 0, 'pending' => 0, 'reviewing' => 0, 'quoted' => 0,
        'accepted' => 0, 'rejected' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $serviceCostRequests = $serviceCostRequests ?? collect();

    $statusMap = [
        'pending'   => ['variant' => 'warning', 'label' => 'Pending'],
        'reviewing' => ['variant' => 'info',    'label' => 'Reviewing'],
        'quoted'    => ['variant' => 'primary', 'label' => 'Quoted'],
        'accepted'  => ['variant' => 'success', 'label' => 'Accepted'],
        'rejected'  => ['variant' => 'danger',  'label' => 'Rejected'],
        'cancelled' => ['variant' => 'neutral', 'label' => 'Cancelled'],
    ];

    $cellRenderers = [
        'request_number' => function ($row) {
            return '<span class="font-mono text-sm font-bold text-dark-text-primary tracking-wide">' . e($row->request_number) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span class="text-sm text-dark-text-primary">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span class="text-xs text-dark-text-secondary">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'pemohon' => function ($row) {
            $type = $row->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan';
            $typeColor = $row->applicant_type === 'badan' ? 'var(--apple-purple)' : 'var(--apple-teal)';
            return '<span class="text-sm font-semibold text-dark-text-primary block">' . e($row->display_name) . '</span>'
                 . '<span class="text-xs font-semibold" style="color:' . $typeColor . '">' . e($type) . '</span>';
        },
        'kontak' => function ($row) {
            return '<span class="text-sm text-dark-text-primary block">' . e($row->email) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary">' . e($row->phone) . '</span>';
        },
        'kategori' => function ($row) {
            $cats = \App\Models\ServiceCostRequest::getServiceCategories();
            $label = $cats[$row->service_category] ?? $row->service_category;
            return '<span class="text-sm text-dark-text-primary">' . e($label) . '</span>';
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->status] ?? ['variant' => 'neutral', 'label' => ucfirst($row->status)];
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]);
        },
        'actions' => function ($row) {
            $url = route('admin.service-cost-requests.show', $row->request_number);
            return '<a href="' . e($url) . '" class="lead-action-link"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a>';
        },
    ];
@endphp

{{-- Stats Strip --}}
<div class="lead-stats-grid">
    <x-leads.stat-card :label="'Total'" :value="$stats['total']" :sub="'semua permohonan'" color="var(--dark-text-primary)" bg="transparent" icon="fa-file-alt" />
    <x-leads.stat-card :label="'Pending'" :value="$stats['pending']" :sub="'perlu ditinjau'" color="var(--apple-orange)" bg="var(--apple-orange)" icon="fa-clock" />
    <x-leads.stat-card :label="'Reviewing'" :value="$stats['reviewing']" :sub="'sedang diproses'" color="var(--apple-blue)" bg="var(--apple-blue)" icon="fa-search" />
    <x-leads.stat-card :label="'Accepted'" :value="$stats['accepted']" :sub="$stats['total'] > 0 ? round(($stats['accepted']/$stats['total'])*100).'% rate' : '—'" color="var(--apple-green)" bg="var(--apple-green)" icon="fa-check-circle" />
</div>
<div class="lead-stats-grid-secondary">
    <x-leads.stat-card variant="secondary" :label="'Quoted'" :value="$stats['quoted']" color="var(--apple-indigo)" bg="var(--apple-indigo)" icon="fa-file-invoice" />
    <x-leads.stat-card variant="secondary" :label="'Rejected'" :value="$stats['rejected']" color="var(--apple-red)" bg="var(--apple-red)" icon="fa-times-circle" />
    <x-leads.stat-card variant="secondary" :label="'Minggu Ini'" :value="$stats['this_week']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar-week" />
    <x-leads.stat-card variant="secondary" :label="'Bulan Ini'" :value="$stats['this_month']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar" />
</div>

{{-- Smart Search & Filter Toolbar --}}
@php
    $scrActiveFilters = collect([
        'search'         => request('search'),
        'status'         => request('status'),
        'applicant_type' => request('applicant_type'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" class="mb-4" x-data="{ searchQuery: '' }" x-init="searchQuery = new URLSearchParams(window.location.search).get('search') || ''"
      @submit="$dispatch('form-submit', { tab: 'service-cost-requests' })">
    <input type="hidden" name="tab" value="service-cost-requests">
    <div class="lead-filter-bar">

        {{-- Search --}}
        <div class="lead-search-wrap">
            <i class="fas fa-magnifying-glass lead-search-icon"></i>
            <input type="text" name="search" id="scr-search" x-model="searchQuery"
                   placeholder="Nomor request, email, nama pemohon…"
                   class="lead-search-input"
                   @focus="$el.style.borderColor='var(--apple-orange)'"
                   @blur="$el.style.borderColor='var(--dark-separator)'"
                   @input.debounce.300ms="$el.closest('form').submit()">
            <button type="button"
                    class="lead-search-clear"
                    :class="{ visible: searchQuery }"
                    @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit())"
                    aria-label="Hapus pencarian">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="lead-filter-divider"></div>

        <div class="lead-filter-group">

            {{-- Status pill --}}
            <div class="relative">
                <select name="status" @change="$el.closest('form').submit()"
                        class="lead-filter-pill @if(request('status')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                        @if(request('status')) style="background:color-mix(in srgb,var(--apple-orange) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-orange) 45%,var(--dark-separator));color:var(--apple-orange)" @endif>
                    <option value="">Status</option>
                    <option value="pending"   {{ request('status')=='pending'   ? 'selected':'' }}>Pending</option>
                    <option value="reviewing" {{ request('status')=='reviewing' ? 'selected':'' }}>Reviewing</option>
                    <option value="quoted"    {{ request('status')=='quoted'    ? 'selected':'' }}>Quoted</option>
                    <option value="accepted"  {{ request('status')=='accepted'  ? 'selected':'' }}>Accepted</option>
                    <option value="rejected"  {{ request('status')=='rejected'  ? 'selected':'' }}>Rejected</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected':'' }}>Cancelled</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('status') ? 'var(--apple-orange)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            {{-- Pemohon pill --}}
            <div class="relative">
                <select name="applicant_type" @change="$el.closest('form').submit()"
                        class="lead-filter-pill @if(request('applicant_type')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                        @if(request('applicant_type')) style="background:color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator));color:var(--apple-purple)" @endif>
                    <option value="">Pemohon</option>
                    <option value="perorangan" {{ request('applicant_type')=='perorangan' ? 'selected':'' }}>Perorangan</option>
                    <option value="badan"      {{ request('applicant_type')=='badan'      ? 'selected':'' }}>Badan Usaha</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('applicant_type') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            @if($scrActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}" class="lead-filter-reset">
                <i class="fas fa-xmark"></i>Reset
                <span class="lead-filter-reset-count">{{ $scrActiveFilters }}</span>
            </a>
            @endif
        </div>
    </div>
</form>

{{-- Data Table --}}
<div x-data="{ loading: false }"
     x-on:form-submit.window="if($event.detail.tab === 'service-cost-requests') loading = true"
     x-init="$watch('loading', val => { if(val) setTimeout(() => loading = false, 8000) })"
     class="lead-table-wrapper">
    @php
        $scrCount = '';
        if ($serviceCostRequests instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $scrCount = $serviceCostRequests->total() > 0
                ? $serviceCostRequests->firstItem().'–'.$serviceCostRequests->lastItem().' dari '.$serviceCostRequests->total()
                : '0 permohonan';
        } else {
            $scrCount = $serviceCostRequests->count().' entri';
        }
    @endphp
    <x-leads.section-header eyebrow="Data" title="Daftar Permohonan Biaya" :count="$scrCount" />
    {{-- Skeleton loading --}}
    <div x-show="loading" class="lead-table-scroll">
        @for($i = 0; $i < 5; $i++)
        <div class="lead-skeleton-row">
            <div class="lead-skeleton lead-skeleton-cell" style="width:60%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:70%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:80%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:65%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:50%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:40%"></div>
            <div class="lead-skeleton lead-skeleton-cell" style="width:30%"></div>
        </div>
        @endfor
    </div>
    {{-- Actual table --}}
    <div x-show="!loading" class="lead-table-scroll">
    <x-ui.table
        :columns="[
            ['key' => 'request_number', 'label' => 'Request #'],
            ['key' => 'tanggal',        'label' => 'Tanggal'],
            ['key' => 'pemohon',        'label' => 'Pemohon'],
            ['key' => 'kontak',         'label' => 'Kontak'],
            ['key' => 'kategori',       'label' => 'Kategori'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'actions',        'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$serviceCostRequests"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message='<div class="lead-empty-state"><i class="fas fa-file-invoice lead-empty-icon"></i><p class="lead-empty-title">Belum ada Permohonan Biaya</p><p class="lead-empty-desc">Permohonan akan muncul di sini ketika calon klien mengirimkan permohonan biaya melalui formulir website.</p></div>'
    />
    </div>
    @if($serviceCostRequests instanceof \Illuminate\Pagination\LengthAwarePaginator && $serviceCostRequests->hasPages())
        <div class="lead-table-pagination">
            <x-ui.pagination :paginator="$serviceCostRequests->appends(array_merge(request()->all(), ['tab'=>'service-cost-requests']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>

<x-ui.alert variant="warning" :icon="true">
    <strong>Tentang Permohonan Biaya:</strong>
    Data dari formulir <code>/permohonan</code> masuk ke tab ini untuk review tim admin sebelum diteruskan ke proses quotation.
</x-ui.alert>
