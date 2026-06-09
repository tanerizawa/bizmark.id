{{-- Service Inquiries Tab --}}
@php
    $stats = $serviceInquiriesStats ?? [
        'total' => 0, 'new' => 0, 'analyzed' => 0, 'contacted' => 0,
        'converted' => 0, 'high_priority' => 0, 'this_week' => 0, 'this_month' => 0,
    ];
    $inquiries = $inquiries ?? collect();

    $statusMap = [
        'new'        => ['color' => 'var(--apple-blue)',   'label' => 'Baru'],
        'processing' => ['color' => 'var(--apple-teal)',   'label' => 'Diproses'],
        'analyzed'   => ['color' => 'var(--apple-yellow)', 'label' => 'Dianalisis'],
        'contacted'  => ['color' => 'var(--apple-green)',  'label' => 'Dihubungi'],
        'qualified'  => ['color' => 'var(--apple-purple)', 'label' => 'Qualified'],
        'converted'  => ['color' => 'var(--apple-green)',  'label' => 'Konversi'],
        'registered' => ['color' => 'var(--dark-text-secondary)', 'label' => 'Terdaftar'],
        'lost'       => ['color' => 'var(--apple-red)',    'label' => 'Lost'],
    ];
    $priorityColors = ['high' => 'var(--apple-red)', 'medium' => 'var(--apple-orange)', 'low' => 'var(--dark-text-secondary)'];

    $cellRenderers = [
        'inquiry_number' => function ($row) {
            return '<span class="font-mono text-sm font-bold text-dark-text-primary tracking-wide">' . e($row->inquiry_number) . '</span>';
        },
        'tanggal' => function ($row) {
            return '<span class="text-sm text-dark-text-primary">' . e($row->created_at->format('d M Y')) . '</span>'
                 . '<br><span class="text-xs text-dark-text-secondary">' . e($row->created_at->format('H:i')) . '</span>';
        },
        'perusahaan' => function ($row) {
            return '<span class="text-sm font-semibold text-dark-text-primary block">' . e($row->company_name) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary">' . e($row->company_type ?? '-') . '</span>';
        },
        'kontak' => function ($row) {
            return '<span class="text-sm font-medium text-dark-text-primary block">' . e($row->contact_person) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary block">' . e($row->email) . '</span>'
                 . '<span class="text-xs text-dark-text-secondary">' . e($row->phone ?? '-') . '</span>';
        },
        'status' => function ($row) use ($statusMap) {
            $s = $statusMap[$row->status] ?? ['color' => 'var(--dark-text-secondary)', 'label' => ucfirst($row->status)];
            $variantMap = ['var(--apple-blue)'=>'info','var(--apple-teal)'=>'info','var(--apple-yellow)'=>'warning','var(--apple-green)'=>'success','var(--apple-purple)'=>'primary','var(--apple-red)'=>'danger','var(--dark-text-secondary)'=>'neutral'];
            $v = $variantMap[$s['color']] ?? 'neutral';
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $v, 'l' => $s['label']]);
        },
        'priority' => function ($row) use ($priorityColors) {
            if (!$row->priority) return '<span class="text-sm text-dark-text-secondary">—</span>';
            $labels = ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
            $v = ['high' => 'danger', 'medium' => 'warning', 'low' => 'neutral'][$row->priority] ?? 'neutral';
            return \Illuminate\Support\Facades\Blade::render('<x-ui.badge :variant="$v">{{ $l }}</x-ui.badge>', ['v' => $v, 'l' => $labels[$row->priority] ?? ucfirst($row->priority)]);
        },
        'est_value' => function ($row) {
            if ($row->estimated_value) {
                $val = (float) $row->estimated_value;
                if ($val >= 1_000_000_000) {
                    return '<span class="text-sm font-bold text-apple-green">Rp ' . e(number_format($val / 1_000_000_000, 1)) . ' M</span>'
                         . '<span class="block text-xs text-dark-text-tertiary">Miliar</span>';
                }
                return '<span class="text-sm font-bold text-apple-green">Rp ' . e(number_format($val / 1_000_000, 0)) . ' Jt</span>'
                     . '<span class="block text-xs text-dark-text-tertiary">Juta</span>';
            }
            return '<span class="text-sm text-dark-text-secondary">—</span>';
        },
        'actions' => function ($row) {
            $url = route('admin.service-inquiries.show', $row);
            return '<a href="' . e($url) . '" class="lead-action-link"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a>';
        },
    ];
@endphp

{{-- Stats Strip --}}
<div class="lead-stats-grid">
    <x-leads.stat-card :label="'Total'" :value="$stats['total']" :sub="'semua inquiry'" color="var(--dark-text-primary)" bg="transparent" icon="fa-inbox" />
    <x-leads.stat-card :label="'Baru'" :value="$stats['new']" :sub="'perlu ditindak'" color="var(--apple-blue)" bg="var(--apple-blue)" icon="fa-star" />
    <x-leads.stat-card :label="'Dihubungi'" :value="$stats['contacted']" :sub="'sudah follow-up'" color="var(--apple-green)" bg="var(--apple-green)" icon="fa-phone" />
    <x-leads.stat-card :label="'Konversi'" :value="$stats['converted']" :sub="$stats['total'] > 0 ? round(($stats['converted']/$stats['total'])*100).'% rate' : '—'" color="var(--apple-purple)" bg="var(--apple-purple)" icon="fa-trophy" />
</div>
<div class="lead-stats-grid-secondary">
    <x-leads.stat-card variant="secondary" :label="'Dianalisis'" :value="$stats['analyzed']" color="var(--apple-teal)" bg="var(--apple-teal)" icon="fa-search" />
    <x-leads.stat-card variant="secondary" :label="'High Priority'" :value="$stats['high_priority']" color="var(--apple-red)" bg="var(--apple-red)" icon="fa-fire" />
    <x-leads.stat-card variant="secondary" :label="'Minggu Ini'" :value="$stats['this_week']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar-week" />
    <x-leads.stat-card variant="secondary" :label="'Bulan Ini'" :value="$stats['this_month']" color="var(--dark-text-primary)" bg="transparent" icon="fa-calendar" />
</div>

{{-- Smart Search & Filter Toolbar --}}
@php
    $siActiveFilters = collect([
        'search'    => request('search'),
        'status'    => request('status'),
        'priority'  => request('priority'),
        'date_from' => request('date_from'),
    ])->filter()->count();
@endphp
<form method="GET" action="{{ route('admin.leads.index') }}" class="mb-4" x-data="{ searchQuery: '' }" x-init="searchQuery = new URLSearchParams(window.location.search).get('search') || ''"
      @submit="$dispatch('form-submit', { tab: 'service-inquiries' })">
    <input type="hidden" name="tab" value="service-inquiries">
    <div class="lead-filter-bar">

        {{-- Search --}}
        <div class="lead-search-wrap">
            <i class="fas fa-magnifying-glass lead-search-icon"></i>
            <input type="text" name="search" id="si-search" x-model="searchQuery"
                   placeholder="Nomor inquiry, email, perusahaan…"
                   class="lead-search-input"
                   @focus="$el.style.borderColor='var(--apple-blue)'"
                   @blur="$el.style.borderColor='var(--dark-separator)'"
                   @input.debounce.300ms="$el.closest('form').submit()">
            <button type="button" id="si-clear-search"
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
                        @if(request('status')) style="background:color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-blue) 45%,var(--dark-separator));color:var(--apple-blue)" @endif>
                    <option value="">Status</option>
                    <option value="new"        {{ request('status')=='new'        ? 'selected':'' }}>Baru</option>
                    <option value="processing" {{ request('status')=='processing' ? 'selected':'' }}>Diproses</option>
                    <option value="analyzed"   {{ request('status')=='analyzed'   ? 'selected':'' }}>Dianalisis</option>
                    <option value="contacted"  {{ request('status')=='contacted'  ? 'selected':'' }}>Dihubungi</option>
                    <option value="qualified"  {{ request('status')=='qualified'  ? 'selected':'' }}>Qualified</option>
                    <option value="converted"  {{ request('status')=='converted'  ? 'selected':'' }}>Konversi</option>
                    <option value="lost"       {{ request('status')=='lost'       ? 'selected':'' }}>Lost</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            {{-- Priority pill --}}
            <div class="relative">
                <select name="priority" @change="$el.closest('form').submit()"
                        class="lead-filter-pill @if(request('priority')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                        @if(request('priority')) style="background:color-mix(in srgb,var(--apple-red) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-red) 45%,var(--dark-separator));color:var(--apple-red)" @endif>
                    <option value="">Prioritas</option>
                    <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>High</option>
                    <option value="medium" {{ request('priority')=='medium' ? 'selected':'' }}>Medium</option>
                    <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>Low</option>
                </select>
                <i class="fas fa-chevron-down lead-filter-pill-chevron" style="color:{{ request('priority') ? 'var(--apple-red)' : 'var(--dark-text-tertiary)' }}"></i>
            </div>

            {{-- Date from --}}
            <div class="relative">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       @change="$el.closest('form').submit()"
                       class="lead-filter-pill @if(request('date_from')) lead-filter-pill-active @else lead-filter-pill-default @endif"
                       @if(request('date_from')) style="background:color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary));border-color:color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator));color:var(--apple-teal)" @endif>
            </div>

            @if($siActiveFilters > 0)
            <a href="{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}" class="lead-filter-reset">
                <i class="fas fa-xmark"></i>Reset
                <span class="lead-filter-reset-count">{{ $siActiveFilters }}</span>
            </a>
            @endif
        </div>
    </div>
</form>

{{-- Data Table --}}
<div x-data="{ loading: false }"
     x-on:form-submit.window="if($event.detail.tab === 'service-inquiries') loading = true"
     x-init="$watch('loading', val => { if(val) setTimeout(() => loading = false, 8000) })"
     class="lead-table-wrapper">
    @php
        $siCount = '';
        if ($inquiries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $siCount = $inquiries->total() > 0
                ? $inquiries->firstItem().'–'.$inquiries->lastItem().' dari '.$inquiries->total()
                : '0 inquiry';
        } else {
            $siCount = $inquiries->count().' entri';
        }
    @endphp
    <x-leads.section-header eyebrow="Data" title="Daftar Service Inquiry" :count="$siCount" />
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
            <div class="lead-skeleton lead-skeleton-cell" style="width:25%"></div>
        </div>
        @endfor
    </div>
    {{-- Actual table --}}
    <div x-show="!loading" class="lead-table-scroll">
    <x-ui.table
        :columns="[
            ['key' => 'inquiry_number', 'label' => 'Inquiry #'],
            ['key' => 'tanggal',        'label' => 'Tanggal'],
            ['key' => 'perusahaan',     'label' => 'Perusahaan'],
            ['key' => 'kontak',         'label' => 'Kontak'],
            ['key' => 'status',         'label' => 'Status'],
            ['key' => 'priority',       'label' => 'Prioritas'],
            ['key' => 'est_value',      'label' => 'Est. Value'],
            ['key' => 'actions',        'label' => 'Aksi', 'align' => 'right'],
        ]"
        :rows="$inquiries"
        :cellRenderers="$cellRenderers"
        :striped="true"
        :hoverable="true"
        variant="compact"
        empty-message='<div class="lead-empty-state"><i class="fas fa-envelope-open-text lead-empty-icon"></i><p class="lead-empty-title">Belum ada Service Inquiry</p><p class="lead-empty-desc">Inquiry akan muncul di sini ketika calon klien mengirimkan pertanyaan layanan melalui formulir website.</p></div>'
    />
    </div>
    @if($inquiries instanceof \Illuminate\Pagination\LengthAwarePaginator && $inquiries->hasPages())
        <div class="lead-table-pagination">
            <x-ui.pagination :paginator="$inquiries->appends(array_merge(request()->all(), ['tab'=>'service-inquiries']))" variant="full" :show-info="true" />
        </div>
    @endif
</div>
