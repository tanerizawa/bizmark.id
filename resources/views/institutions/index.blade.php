@extends('layouts.admin')

@section('title', 'Institusi')
@section('page-title', 'Manajemen Institusi')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- KPI Cards --}}
    @php
    $col = $institutions->getCollection();
    $statsData = [
        ['label'=>'Total Institusi', 'value'=>$institutions->total(),                    'sub'=>'semua institusi',    'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',         'icon'=>'fa-building'],
        ['label'=>'Pemerintah',      'value'=>$col->where('type','Pemerintah')->count(), 'sub'=>'instansi pemerintah','color'=>'var(--apple-red)',         'bg'=>'var(--apple-red)',    'icon'=>'fa-landmark'],
        ['label'=>'BUMN',            'value'=>$col->where('type','BUMN')->count(),       'sub'=>'badan usaha negara', 'color'=>'var(--apple-orange)',      'bg'=>'var(--apple-orange)', 'icon'=>'fa-city'],
        ['label'=>'Swasta',          'value'=>$col->where('type','Swasta')->count(),     'sub'=>'entitas swasta',     'color'=>'var(--apple-green)',       'bg'=>'var(--apple-green)',  'icon'=>'fa-briefcase'],
    ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach($statsData as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px;position:relative;overflow:hidden">
            <div style="position:absolute;top:10px;right:14px;font-size:1rem;opacity:.2;color:{{ $s['color'] }}"><i class="fas {{ $s['icon'] }}"></i></div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.8;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:2rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Smart Search & Filter Toolbar --}}
    @php
        $activeFilters = collect([
            'search'    => request('search'),
            'type'      => request('type'),
            'is_active' => request('is_active'),
        ])->filter(fn($v) => $v !== null && $v !== '')->count();
    @endphp
    <form method="GET" action="{{ route('institutions.index') }}" id="inst-filter-form">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:220px">
                <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
                <input type="text" name="search" id="if-search" value="{{ request('search') }}"
                       placeholder="Cari nama institusi, kontak…"
                       style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
                <button type="button" id="if-clear-search"
                        style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                        onclick="document.getElementById('if-search').value='';this.style.display='none';document.getElementById('inst-filter-form').submit()">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- Separator --}}
            <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

            {{-- Filter Pills --}}
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

                {{-- Tipe pill --}}
                <div style="position:relative">
                    <select name="type"
                            style="padding:6px 28px 6px 10px;background:{{ request('type') ? 'color-mix(in srgb,var(--apple-orange) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('type') ? 'color-mix(in srgb,var(--apple-orange) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('type') ? 'var(--apple-orange)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('type') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Tipe</option>
                        <option value="Pemerintah" {{ request('type')=='Pemerintah' ? 'selected':'' }}>Pemerintah</option>
                        <option value="BUMN"       {{ request('type')=='BUMN'       ? 'selected':'' }}>BUMN</option>
                        <option value="Swasta"     {{ request('type')=='Swasta'     ? 'selected':'' }}>Swasta</option>
                        <option value="Lainnya"    {{ request('type')=='Lainnya'    ? 'selected':'' }}>Lainnya</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('type') ? 'var(--apple-orange)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Status pill --}}
                <div style="position:relative">
                    <select name="is_active"
                            style="padding:6px 28px 6px 10px;background:{{ request('is_active') !== null && request('is_active') !== '' ? 'color-mix(in srgb,var(--apple-green) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('is_active') !== null && request('is_active') !== '' ? 'color-mix(in srgb,var(--apple-green) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('is_active') !== null && request('is_active') !== '' ? 'var(--apple-green)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('is_active') !== null && request('is_active') !== '' ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Status</option>
                        <option value="1" {{ request('is_active')==='1' ? 'selected':'' }}>Aktif</option>
                        <option value="0" {{ request('is_active')==='0' ? 'selected':'' }}>Tidak Aktif</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('is_active') !== null && request('is_active') !== '' ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Active filter badge + reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('institutions.index') }}"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:20px;font-size:0.72rem;font-weight:600;color:var(--apple-red);text-decoration:none;white-space:nowrap;transition:opacity .18s"
                   onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                    <i class="fas fa-xmark"></i>Reset
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;background:var(--apple-red);color:#fff;border-radius:50%;font-size:0.6rem;font-weight:700">{{ $activeFilters }}</span>
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table Card --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Data</p>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Institusi</h3>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                @php $isEmptyInst = ($institutions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $institutions->total() : $institutions->count()) === 0; @endphp
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                    @if($institutions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($institutions->total() === 0)
                            0 institusi
                        @else
                            {{ $institutions->firstItem() }}–{{ $institutions->lastItem() }} dari {{ $institutions->total() }}
                        @endif
                    @endif
                </span>
                @unless($isEmptyInst)
                <a href="{{ route('institutions.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <i class="fas fa-plus"></i>Tambah Institusi
                </a>
                @endunless
            </div>
        </div>

        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary)">
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Institusi</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Tipe</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Kontak</th>
                        <th style="padding:10px 16px;text-align:center;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Jenis Izin</th>
                        <th style="padding:10px 16px;text-align:center;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Status</th>
                        <th style="padding:10px 16px;text-align:right;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($institutions as $i => $institution)
                        @php
                            $typeCfg = [
                                'Pemerintah' => ['icon'=>'fa-landmark',  'color'=>'var(--apple-red)'],
                                'BUMN'       => ['icon'=>'fa-city',      'color'=>'var(--apple-orange)'],
                                'Swasta'     => ['icon'=>'fa-briefcase', 'color'=>'var(--apple-green)'],
                                'Lainnya'    => ['icon'=>'fa-building',  'color'=>'var(--dark-text-secondary)'],
                            ];
                            $tc     = $typeCfg[$institution->type] ?? $typeCfg['Lainnya'];
                            $rowBg  = $i % 2 === 1 ? 'rgba(255,255,255,0.02)' : 'transparent';
                        @endphp
                        <tr style="border-top:1px solid var(--dark-separator);background:{{ $rowBg }};cursor:pointer;transition:background .15s"
                            onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.background='{{ $rowBg }}'"
                            onclick="window.location='{{ route('institutions.show', $institution) }}'">

                            <td style="padding:12px 16px">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:38px;height:38px;border-radius:10px;background:color-mix(in srgb,{{ $tc['color'] }} 15%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <i class="fas {{ $tc['icon'] }}" style="font-size:1rem;color:{{ $tc['color'] }}"></i>
                                    </div>
                                    <div>
                                        <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $institution->name }}</span>
                                        @if($institution->contact_person)
                                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);margin-top:2px;display:block">{{ $institution->contact_person }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $tc['color'] }} 14%,transparent);color:{{ $tc['color'] }}">
                                    <i class="fas {{ $tc['icon'] }}" style="font-size:0.65rem"></i>{{ $institution->type ?? 'Lainnya' }}
                                </span>
                            </td>

                            <td style="padding:12px 16px">
                                <div style="display:flex;flex-direction:column;gap:3px">
                                    @if($institution->email)
                                        <span style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:var(--dark-text-secondary)">
                                            <i class="fas fa-envelope" style="font-size:0.6rem;width:12px;flex-shrink:0"></i>{{ $institution->email }}
                                        </span>
                                    @endif
                                    @if($institution->phone)
                                        <span style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:var(--dark-text-secondary)">
                                            <i class="fas fa-phone" style="font-size:0.6rem;width:12px;flex-shrink:0"></i>{{ $institution->phone }}
                                        </span>
                                    @endif
                                    @if(!$institution->email && !$institution->phone)
                                        <span style="font-size:0.78rem;color:var(--dark-text-tertiary)">—</span>
                                    @endif
                                </div>
                            </td>

                            <td style="padding:12px 16px;text-align:center;white-space:nowrap">
                                <span style="display:inline-flex;align-items:center;justify-content:center;gap:4px;padding:3px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);color:var(--apple-blue)">
                                    {{ $institution->permit_types_count ?? 0 }} Izin
                                </span>
                            </td>

                            <td style="padding:12px 16px;text-align:center;white-space:nowrap">
                                @if($institution->is_active)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 14%,transparent);color:var(--apple-green)">
                                        <i class="fas fa-check-circle" style="font-size:0.65rem"></i>Aktif
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-tertiary) 14%,transparent);color:var(--dark-text-tertiary)">
                                        <i class="fas fa-times-circle" style="font-size:0.65rem"></i>Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td style="padding:12px 16px;text-align:right;white-space:nowrap" onclick="event.stopPropagation()">
                                <div style="display:inline-flex;align-items:center;gap:6px">
                                    <a href="{{ route('institutions.show', $institution) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);text-decoration:none;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('institutions.edit', $institution) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-orange);text-decoration:none;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:48px 20px;text-align:center">
                                <div style="width:52px;height:52px;border-radius:14px;background:color-mix(in srgb,var(--dark-text-secondary) 10%,var(--dark-bg-tertiary));display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                                    <i class="fas fa-building" style="font-size:1.4rem;color:var(--dark-text-tertiary)"></i>
                                </div>
                                <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">
                                    @if($activeFilters > 0) Tidak Ada Hasil @else Belum Ada Institusi @endif
                                </p>
                                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 18px">
                                    @if($activeFilters > 0) Coba ubah atau reset filter pencarian @else Tambahkan institusi pertama untuk memulai @endif
                                </p>
                                @if($activeFilters > 0)
                                <a href="{{ route('institutions.index') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.78rem;font-weight:600;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-xmark"></i>Reset Filter
                                </a>
                                @else
                                <a href="{{ route('institutions.create') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.8rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-plus"></i>Tambah Institusi
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($institutions instanceof \Illuminate\Pagination\LengthAwarePaginator && $institutions->hasPages())
            <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <x-ui.pagination :paginator="$institutions->appends(request()->all())" variant="full" :show-info="true" />
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('inst-filter-form');
    if (!form) return;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

    // Submit on Enter, show/hide clear button
    const searchInput = form.querySelector('#if-search');
    const clearBtn    = form.querySelector('#if-clear-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); } });
        searchInput.addEventListener('input', () => {
            if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        });
    }
});
</script>
@endpush
