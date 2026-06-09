@extends('layouts.admin')

@section('title', 'Klien')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    @php
        $activeFilters = collect([
            'search'      => request('search'),
            'status'      => request('status'),
            'client_type' => request('client_type'),
        ])->filter()->count();
        $isEmptyClients = $clients->total() === 0;
    @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen Klien</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Database Klien</h1>
        </div>
        @unless($isEmptyClients)
        <a href="{{ route('clients.create') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:var(--apple-purple);color:#fff;border-radius:10px;font-size:0.82rem;font-weight:700;text-decoration:none;border:none;transition:opacity .15s"
           onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>Tambah Klien
        </a>
        @endunless
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
                <span style="font-size:0.82rem;color:var(--apple-green)">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-green);opacity:.7"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-exclamation-circle" style="color:var(--apple-red)"></i>
                <span style="font-size:0.82rem;color:var(--apple-red)">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);opacity:.7"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach([
            ['Total Klien','fa-users','var(--apple-blue)',$clients->total(),'Semua klien terdaftar'],
            ['Aktif','fa-check-circle','var(--apple-green)',$stats['active'],'Status aktif'],
            ['Perusahaan','fa-building','var(--apple-purple)',$stats['company'],'Tipe perusahaan'],
            ['Potensial','fa-star','var(--apple-orange)',$stats['potential'],'Peluang baru'],
        ] as [$label,$icon,$col,$val,$sub])
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $col }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $col }} 20%,var(--dark-separator));border-radius:16px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:10px;background:color-mix(in srgb,{{ $col }} 18%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="fas {{ $icon }}" style="color:{{ $col }};font-size:0.9rem"></i>
                </div>
                <div>
                    <p style="font-size:1.4rem;font-weight:800;color:{{ $col }};margin:0;line-height:1.1">{{ $val }}</p>
                    <p style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);margin:2px 0 0">{{ $label }}</p>
                </div>
            </div>
            <p style="font-size:0.65rem;color:var(--dark-text-tertiary);margin:8px 0 0">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    {{-- Smart Search & Filter Toolbar --}}
    <form method="GET" action="{{ route('clients.index') }}" id="filterForm">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:220px">
                <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
                <input type="text" name="search" id="cf-search" value="{{ request('search') }}"
                       placeholder="Cari nama, perusahaan, email…"
                       style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
                <button type="button" id="cf-clear-search"
                        style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                        onclick="document.getElementById('cf-search').value='';this.style.display='none';document.getElementById('filterForm').submit()">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- Separator --}}
            <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

            {{-- Filter Pills --}}
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

                {{-- Status pill --}}
                <div style="position:relative">
                    <select name="status"
                            style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-green) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-green) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-green)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Status</option>
                        <option value="active"    {{ request('status')=='active'    ? 'selected':'' }}>Aktif</option>
                        <option value="inactive"  {{ request('status')=='inactive'  ? 'selected':'' }}>Tidak Aktif</option>
                        <option value="potential" {{ request('status')=='potential' ? 'selected':'' }}>Potensial</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Tipe pill --}}
                <div style="position:relative">
                    <select name="client_type"
                            style="padding:6px 28px 6px 10px;background:{{ request('client_type') ? 'color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('client_type') ? 'color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('client_type') ? 'var(--apple-purple)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('client_type') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Tipe</option>
                        <option value="individual"  {{ request('client_type')=='individual'  ? 'selected':'' }}>Individual</option>
                        <option value="company"     {{ request('client_type')=='company'     ? 'selected':'' }}>Perusahaan</option>
                        <option value="government"  {{ request('client_type')=='government'  ? 'selected':'' }}>Pemerintah</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('client_type') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Active filter badge + reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('clients.index') }}"
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
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary);border-bottom:1px solid var(--dark-separator)">
                        @foreach(['Klien','Kontak','Tipe','Status','Proyek','Aksi'] as $h)
                        <th style="padding:11px 16px;text-align:{{ $loop->last ? 'center' : 'left' }};font-size:0.68rem;font-weight:700;color:rgba(235,235,245,0.85);text-transform:uppercase;letter-spacing:.08em;white-space:nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $i => $client)
                    @php
                        $typeMap = [
                            'individual' => ['Individual','fa-user','var(--apple-blue)'],
                            'company'    => ['Perusahaan','fa-building','var(--apple-purple)'],
                            'government' => ['Pemerintah','fa-landmark','var(--apple-red)'],
                        ];
                        [$typeLabel,$typeIcon,$typeCol] = $typeMap[$client->client_type] ?? ['Lainnya','fa-circle','var(--dark-text-secondary)'];
                        $statusMap = [
                            'active'   => ['Aktif','var(--apple-green)'],
                            'inactive' => ['Tidak Aktif','var(--apple-red)'],
                            'potential'=> ['Potensial','var(--apple-orange)'],
                        ];
                        [$statusLabel,$statusCol] = $statusMap[$client->status] ?? ['—','var(--dark-text-tertiary)'];
                        $rowBg = $i % 2 === 1 ? 'color-mix(in srgb,var(--dark-bg-tertiary) 40%,transparent)' : 'transparent';
                    @endphp
                    <tr style="background:{{ $rowBg }};border-bottom:1px solid var(--dark-separator);cursor:pointer;transition:background .15s"
                        onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 5%,var(--dark-bg-tertiary))'"
                        onmouseout="this.style.background='{{ $rowBg }}'"
                        onclick="window.location='{{ route('clients.show', $client) }}'">

                        {{-- Klien --}}
                        <td style="padding:12px 16px" onclick="event.stopPropagation()">
                            <a href="{{ route('clients.show', $client) }}" style="text-decoration:none">
                                <div style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary)">{{ $client->company_name ?? $client->name }}</div>
                                @if($client->contact_person || $client->industry)
                                <div style="font-size:0.72rem;color:var(--dark-text-secondary);margin-top:2px;display:flex;align-items:center;gap:8px">
                                    @if($client->contact_person)<span>{{ $client->contact_person }}</span>@endif
                                    @if($client->contact_person && $client->industry)<span style="opacity:.4">·</span>@endif
                                    @if($client->industry)<span>{{ $client->industry }}</span>@endif
                                </div>
                                @endif
                            </a>
                        </td>

                        {{-- Kontak --}}
                        <td style="padding:12px 16px">
                            <div style="display:flex;flex-direction:column;gap:3px">
                                @if($client->email)
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.72rem;color:var(--dark-text-secondary)">
                                    <i class="fas fa-envelope" style="color:var(--apple-blue);font-size:0.62rem;width:12px"></i>{{ $client->email }}
                                </div>
                                @endif
                                @if($client->phone)
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.72rem;color:var(--dark-text-secondary)">
                                    <i class="fas fa-phone" style="color:var(--apple-blue);font-size:0.62rem;width:12px"></i>{{ $client->phone }}
                                </div>
                                @endif
                                @if($client->mobile)
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.72rem;color:var(--dark-text-secondary)">
                                    <i class="fab fa-whatsapp" style="color:var(--apple-green);font-size:0.62rem;width:12px"></i>{{ $client->mobile }}
                                </div>
                                @endif
                                @if(!$client->email && !$client->phone && !$client->mobile)
                                <span style="font-size:0.72rem;color:var(--dark-text-tertiary)">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- Tipe --}}
                        <td style="padding:12px 16px;white-space:nowrap">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $typeCol }} 14%,transparent);color:{{ $typeCol }}">
                                <i class="fas {{ $typeIcon }}" style="font-size:0.62rem"></i>{{ $typeLabel }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td style="padding:12px 16px;white-space:nowrap">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $statusCol }} 14%,transparent);color:{{ $statusCol }}">
                                <span style="width:5px;height:5px;border-radius:50%;background:{{ $statusCol }};display:inline-block"></span>{{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Proyek --}}
                        <td style="padding:12px 16px;white-space:nowrap">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue)">
                                <i class="fas fa-folder" style="font-size:0.62rem"></i>{{ $client->projects_count ?? 0 }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td style="padding:12px 16px;white-space:nowrap;text-align:center" onclick="event.stopPropagation()">
                            <div style="display:inline-flex;align-items:center;gap:5px">
                                <a href="{{ route('clients.show', $client) }}"
                                   style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:7px;text-decoration:none;transition:background .15s"
                                   onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 22%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 12%,transparent)'"
                                   title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('clients.edit', $client) }}"
                                   style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);color:var(--apple-orange);border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent);border-radius:7px;text-decoration:none;transition:background .15s"
                                   onmouseover="this.style.background='color-mix(in srgb,var(--apple-orange) 22%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-orange) 12%,transparent)'"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" style="display:inline"
                                      onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-red) 12%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:7px;cursor:pointer;transition:background .15s"
                                            onmouseover="this.style.background='color-mix(in srgb,var(--apple-red) 22%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-red) 12%,transparent)'"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding:48px 24px;text-align:center">
                            <div style="width:52px;height:52px;border-radius:14px;background:color-mix(in srgb,var(--dark-text-secondary) 10%,var(--dark-bg-tertiary));display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                                <i class="fas fa-users" style="font-size:1.4rem;color:var(--dark-text-tertiary)"></i>
                            </div>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">
                                @if($activeFilters > 0) Tidak Ada Hasil @else Belum Ada Klien @endif
                            </p>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 18px">
                                @if($activeFilters > 0) Coba ubah atau reset filter pencarian @else Tambahkan klien pertama untuk memulai @endif
                            </p>
                            @if($activeFilters > 0)
                            <a href="{{ route('clients.index') }}"
                               style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.78rem;font-weight:600;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none"
                               onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                <i class="fas fa-xmark"></i>Reset Filter
                            </a>
                            @else
                            <a href="{{ route('clients.create') }}"
                               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:var(--apple-purple);color:#fff;border-radius:10px;font-size:0.82rem;font-weight:700;text-decoration:none"
                               onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                <i class="fas fa-plus" style="font-size:0.75rem"></i>Tambah Klien Pertama
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
        <div style="padding:14px 20px;background:var(--dark-bg-tertiary);border-top:1px solid var(--dark-separator);display:flex;align-items:center;justify-content:space-between;gap:12px">
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                @if($clients->total() === 0)
                    0 klien
                @else
                    {{ $clients->firstItem() }}–{{ $clients->lastItem() }} dari {{ $clients->total() }}
                @endif
            </span>
            <x-ui.pagination :paginator="$clients->appends(request()->all())" variant="full" :show-info="false" />
        </div>
        @else
        <div style="padding:10px 20px;border-top:1px solid var(--dark-separator)">
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                @if($clients->total() === 0) 0 klien @else {{ $clients->total() }} klien @endif
            </span>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('filterForm');
    if (!form) return;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

    // Submit on Enter, show/hide clear button
    const searchInput = form.querySelector('#cf-search');
    const clearBtn    = form.querySelector('#cf-clear-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); } });
        searchInput.addEventListener('input', () => {
            if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        });
    }
});
</script>
@endpush
@endsection
