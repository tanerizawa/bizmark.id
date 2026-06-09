{{-- Consultation Leads Tab --}}
@php
    $stats = $consultationLeadsStats ?? [
        'total' => 0, 'new' => 0, 'contacted' => 0, 'converted' => 0,
        'pending_review' => 0, 'high_value' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $consultations = $consultations ?? collect();

    $statusMap = [
        'auto_estimated' => ['variant' => 'info',    'label' => 'Auto Estimated'],
        'reviewed'       => ['variant' => 'warning', 'label' => 'Reviewed'],
        'approved'       => ['variant' => 'success', 'label' => 'Approved'],
        'quoted'         => ['variant' => 'primary', 'label' => 'Quoted'],
        'rejected'       => ['variant' => 'danger',  'label' => 'Rejected'],
    ];
    $sizeMap = [
        'large'  => ['variant' => 'danger',  'label' => 'Large'],
        'medium' => ['variant' => 'warning', 'label' => 'Medium'],
        'small'  => ['variant' => 'success', 'label' => 'Small'],
        'micro'  => ['variant' => 'neutral', 'label' => 'Micro'],
    ];

    $cellRenderers = [
        'lead_id' => function ($row) {
            return '<span class="font-mono text-sm font-bold text-dark-text-primary">#' . e($row->id) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span class="text-sm text-dark-text-primary">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span class="text-xs text-dark-text-secondary">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'perusahaan' => function ($row) use ($sizeMap) {
            $html = '<span class="text-sm font-semibold text-dark-text-primary block">' . e($row->company_name ?: $row->name) . '</span>';
            if ($row->kbli) {
                $html .= '<span class="text-xs text-dark-text-secondary block max-w-[160px] truncate">' . e(optional($row->kbli)->description) . '</span>';
            }
            if ($row->business_size && isset($sizeMap[$row->business_size])) {
                $s = $sizeMap[$row->business_size];
                $html .= '<span class="inline-block mt-0.5">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v" size="sm">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]) . '</span>';
            }
            return $html;
        },
        'kontak' => function ($row) {
            return '<span class="text-sm font-medium text-dark-text-primary block">' . e($row->name) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary block">' . e($row->email) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary">' . e($row->phone) . '</span>';
        },
        'estimasi' => function ($row) {
            $formatted = $row->auto_estimate['cost_summary']['formatted']['grand_total'] ?? null;
            $total = $row->auto_estimate['cost_summary']['grand_total'] ?? null;
            $confidence = number_format(($row->confidence_score ?? 0.5) * 100, 0);
            $html = $formatted
                ? '<span class="text-lg font-bold text-apple-green">' . e($formatted) . '</span>'
                : '<span class="text-sm text-dark-text-secondary">—</span>';
            $html .= '<br><span class="text-xs text-dark-text-secondary">Confidence: ' . $confidence . '%</span>';
            if ($total && $total >= 10000000) {
                $html .= '<br>' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="danger" size="sm">High Value</x-ui.badge>');
            }
            return $html;
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->estimate_status] ?? ['variant' => 'neutral', 'label' => ucfirst(str_replace('_', ' ', $row->estimate_status))];
            $html = \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $s['variant'], 'l' => $s['label']]);
            if ($row->contacted) {
                $html .= '<span class="block mt-0.5">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="success" size="sm">Contacted</x-ui.badge>') . '</span>';
            }
            if ($row->converted_to_client) {
                $html .= '<span class="block mt-0.5">' . \Illuminate\Support\Facades\Blade::render('<x-ui.badge variant="primary" size="sm">Converted</x-ui.badge>') . '</span>';
            }
            return $html;
        },
        'actions' => function ($row) {
            $showUrl    = route('admin.consultation-leads.show', $row);
            $convertUrl = route('admin.consultation-leads.convert-to-client', $row);
            $html = '<div class="flex items-center gap-2 justify-end">';
            $html .= '<a href="' . e($showUrl) . '" class="lead-action-link"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a>';
            if (!$row->converted_to_client) {
                $html .= '<button onclick="showConvertModal(\'' . e($convertUrl) . '\')" class="lead-action-link lead-action-convert"><i class="fas fa-user-plus" aria-hidden="true"></i>Konversi</button>';
            }
            $html .= '</div>';
            return $html;
        },
    ];
@endphp

{{-- Stats Strip --}}
<div class="lead-stats-grid">
    <x-leads.stat-card :label="'Total'" :value="$stats['total']" :sub="'semua leads'" color="var(--dark-text-primary)" bg="transparent" icon="fa-users" />
    <x-leads.stat-card :label="'Baru'" :value="$stats['new']" :sub="'belum ditindak'" color="var(--apple-blue)" bg="var(--apple-blue)" icon="fa-user-plus" />
    <x-leads.stat-card :label="'Dihubungi'" :value="$stats['contacted']" :sub="'sudah follow-up'" color="var(--apple-green)" bg="var(--apple-green)" icon="fa-phone" />
    <x-leads.stat-card :label="'Konversi'" :value="$stats['converted']" :sub="$stats['total'] > 0 ? round(($stats['converted']/$stats['total'])*100).'% rate' : '—'" color="var(--apple-purple)" bg="var(--apple-purple)" icon="fa-trophy" />
</div>
<div class="lead-stats-grid-secondary">
    <x-leads.stat-card variant="secondary" :label="'Perlu Review'" :value="$stats['pending_review']" color="var(--apple-orange)" bg="var(--apple-orange)" icon="fa-exclamation-circle" />
    <x-leads.stat-card variant="secondary" :label="'High Value'" :value="$stats['high_value']" color="var(--apple-red)" bg="var(--apple-red)" icon="fa-fire" />
    <x-leads.stat-card variant="secondary" :label="'Minggu Ini'" :value="$stats['this_week']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar-week" />
    <x-leads.stat-card variant="secondary" :label="'Bulan Ini'" :value="$stats['this_month']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar" />
</div>

{{-- Smart Search & Filter Toolbar --}}
@php
    $clActiveFilters = collect([
        'search'    => request('search'),
        'status'    => request('status'),
        'contacted' => request('contacted'),
        'date_from' => request('date_from'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" class="mb-4" x-data="{ searchQuery: '' }" x-init="searchQuery = new URLSearchParams(window.location.search).get('search') || ''"
      @submit="$dispatch('form-submit', { tab: 'consultation-leads' })">
    <input type="hidden" name="tab" value="consultation-leads">
    <div class="lead-filter-bar">

        {{-- Search --}}
        <div class="lead-search-wrap">
            <i class="fas fa-magnifying-glass lead-search-icon"></i>
            <input type="text" name="search" id="cl-search" x-model="searchQuery"
                   placeholder="ID, email, nama, perusahaan…"
                   class="lead-search-input"
                   @focus="$el.style.borderColor='var(--apple-yellow)'"
                   @blur="$el.style.borderColor='var(--dark-separator)'"
                   @input.debounce.300ms="$el.closest('form').submit()">
            <button type="button" id="cl-clear-search"
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
                        @if(request('status')) style="background:color-mix(in srgb,var(--apple-yellow) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-yellow) 45%,var(--dark-separator));color:var(--apple-yellow)" @endif>
                    <option value="">Status</option>
                    <option value="auto_estimated" {{ request('status')=='auto_estimated' ? 'selected':'' }}>Auto Estimated</option>
                    <option value="reviewed"       {{ request('status')=='reviewed'       ? 'selected':'' }}>Reviewed</option>
                    <option value="approved"       {{ request('status')=='approved'       ? 'selected':'' }}>Approved</option>
                    <option value="quoted"         {{ request('status')=='quoted'         ? 'selected':'' }}>Quoted</option>
                    <option value="rejected"       {{ request('status')=='rejected'       ? 'selected':'' }}>Rejected</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('status') ? 'var(--apple-yellow)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            {{-- Kontak pill --}}
            <div class="relative">
                <select name="contacted" @change="$el.closest('form').submit()"
                        class="lead-filter-pill @if(request('contacted')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                        @if(request('contacted')) style="background:color-mix(in srgb,var(--apple-green) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-green) 45%,var(--dark-separator));color:var(--apple-green)" @endif>
                    <option value="">Kontak</option>
                    <option value="yes" {{ request('contacted')=='yes' ? 'selected':'' }}>Dihubungi</option>
                    <option value="no"  {{ request('contacted')=='no'  ? 'selected':'' }}>Belum</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('contacted') ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            {{-- Date from --}}
            <div class="relative">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       @change="$el.closest('form').submit()"
                       class="lead-filter-pill @if(request('date_from')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                       @if(request('date_from')) style="background:color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator));color:var(--apple-teal)" @endif>
            </div>

            @if($clActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'consultation-leads']) }}" class="lead-filter-reset">
                <i class="fas fa-xmark"></i>Reset
                <span class="lead-filter-reset-count">{{ $clActiveFilters }}</span>
            </a>
            @endif
        </div>
    </div>
</form>

{{-- Data Table --}}
<div x-data="{ loading: false }"
     x-on:form-submit.window="if($event.detail.tab === 'consultation-leads') loading = true"
     x-init="$watch('loading', val => { if(val) setTimeout(() => loading = false, 8000) })"
     class="lead-table-wrapper">
    @php
        $clCount = '';
        if ($consultations instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $clCount = $consultations->total() > 0
                ? $consultations->firstItem().'–'.$consultations->lastItem().' dari '.$consultations->total()
                : '0 leads';
        } else {
            $clCount = $consultations->count().' entri';
        }
    @endphp
    <x-leads.section-header eyebrow="Data" title="Daftar Consultation Leads" :count="$clCount" />
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
            ['key' => 'lead_id',    'label' => 'Lead #'],
            ['key' => 'tanggal',    'label' => 'Tanggal'],
            ['key' => 'perusahaan', 'label' => 'Perusahaan'],
            ['key' => 'kontak',     'label' => 'Kontak'],
            ['key' => 'estimasi',   'label' => 'Estimasi'],
            ['key' => 'status',     'label' => 'Status'],
            ['key' => 'actions',    'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$consultations"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message='<div class="lead-empty-state"><i class="fas fa-inbox lead-empty-icon"></i><p class="lead-empty-title">Belum ada Consultation Leads</p><p class="lead-empty-desc">Leads akan muncul di sini ketika calon klien mengirimkan permohonan konsultasi melalui formulir website.</p></div>'
    />
    </div>
    @if($consultations instanceof \Illuminate\Pagination\LengthAwarePaginator && $consultations->hasPages())
        <div class="lead-table-pagination">
            <x-ui.pagination :paginator="$consultations->appends(array_merge(request()->all(), ['tab'=>'consultation-leads']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>
