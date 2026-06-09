@extends('layouts.admin')

@section('title', 'Proyek')
@section('page-title', 'Manajemen Proyek')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    @if(session('success'))
        <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
    @endif
    @if(session('error'))
        <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
    @endif

    {{-- KPI Stat Cards --}}
    @php
    $statsData = [
        ['label'=>'Total Proyek',   'value'=>$totalProjects,      'sub'=>'semua proyek',        'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',          'icon'=>'fa-folder-open'],
        ['label'=>'Berjalan',       'value'=>$inProgressProjects, 'sub'=>'sedang dikerjakan',   'color'=>'var(--apple-orange)',      'bg'=>'var(--apple-orange)',   'icon'=>'fa-tasks'],
        ['label'=>'Selesai',        'value'=>$completedProjects,  'sub'=>'berhasil diselesaikan','color'=>'var(--apple-green)',      'bg'=>'var(--apple-green)',    'icon'=>'fa-check-circle'],
        ['label'=>'Terlambat',      'value'=>$overdueProjects,    'sub'=>'melewati deadline',   'color'=>'var(--apple-red)',         'bg'=>'var(--apple-red)',      'icon'=>'fa-exclamation-triangle'],
    ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach($statsData as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px;position:relative;overflow:hidden">
            <div style="position:absolute;top:10px;right:14px;font-size:1rem;opacity:.2;color:{{ $s['color'] }}">
                <i class="fas {{ $s['icon'] }}"></i>
            </div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.8;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:2rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Smart Search & Filter Toolbar --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'status' => request('status'),
            'client' => request('client'),
            'year'   => request('year'),
        ])->filter()->count();
    @endphp
    <form method="GET" action="{{ route('projects.index') }}" id="projects-filter-form">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:220px">
                <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
                <input type="text" name="search" id="pf-search" value="{{ request('search') }}"
                       placeholder="Cari proyek, klien, deskripsi…"
                       style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
                {{-- Clear search button --}}
                <button type="button" id="pf-clear-search"
                        style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                        onclick="document.getElementById('pf-search').value='';this.style.display='none';document.getElementById('projects-filter-form').submit()">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- Separator --}}
            <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

            {{-- Filter Pills --}}
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

                {{-- Status pill --}}
                <div style="position:relative">
                    <select name="status" class="pf-pill {{ request('status') ? 'pf-pill--active' : '' }}"
                            style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Client pill --}}
                <div style="position:relative">
                    <select name="client" class="pf-pill {{ request('client') ? 'pf-pill--active' : '' }}"
                            style="padding:6px 28px 6px 10px;background:{{ request('client') ? 'color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('client') ? 'color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('client') ? 'var(--apple-purple)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('client') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Klien</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client') == $client->id ? 'selected' : '' }}>{{ $client->company_name ?? $client->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('client') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Year pill --}}
                <div style="position:relative">
                    <select name="year" class="pf-pill {{ request('year') ? 'pf-pill--active' : '' }}"
                            style="padding:6px 28px 6px 10px;background:{{ request('year') ? 'color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('year') ? 'color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('year') ? 'var(--apple-teal)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('year') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Tahun</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('year') ? 'var(--apple-teal)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Active filter badge + reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('projects.index') }}"
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
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Proyek</h3>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                @php $isEmpty = ($projects instanceof \Illuminate\Pagination\LengthAwarePaginator ? $projects->total() : $projects->count()) === 0; @endphp
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                    @if($projects instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($projects->total() === 0)
                            0 proyek
                        @else
                            {{ $projects->firstItem() }}–{{ $projects->lastItem() }} dari {{ $projects->total() }}
                        @endif
                    @else
                        {{ $projects->count() }} entri
                    @endif
                </span>
                @unless($isEmpty)
                <a href="{{ route('projects.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;text-decoration:none"
                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <i class="fas fa-plus"></i>Tambah Proyek
                </a>
                @endunless
            </div>
        </div>

        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary)">
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Proyek</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Klien</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Status</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Deadline</th>
                        <th style="padding:10px 16px;text-align:right;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $i => $project)
                        <tr style="border-top:1px solid var(--dark-separator);background:{{ $i % 2 === 1 ? 'rgba(255,255,255,0.02)' : 'transparent' }};cursor:pointer;transition:background .15s"
                            onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.background='{{ $i % 2 === 1 ? 'rgba(255,255,255,0.02)' : 'transparent' }}'"
                            onclick="window.location='{{ route('projects.show', $project) }}'">
                            <td style="padding:12px 16px">
                                <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $project->name }}</span>
                                @if($project->description)
                                    <span style="font-size:0.7rem;color:var(--dark-text-secondary)">{{ Str::limit($project->description, 80) }}</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;white-space:nowrap">
                                @if($project->client)
                                    <span style="font-size:0.85rem;font-weight:500;color:var(--dark-text-primary);display:block">{{ $project->client->company_name ?? $project->client->name }}</span>
                                    <a href="{{ route('clients.show', $project->client) }}"
                                       onclick="event.stopPropagation()"
                                       style="font-size:0.7rem;color:var(--apple-blue);text-decoration:none"
                                       onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-external-link-alt" style="margin-right:3px"></i>Lihat klien
                                    </a>
                                @else
                                    <span style="font-size:0.85rem;color:var(--dark-text-secondary)">{{ $project->client_name ?? '—' }}</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;white-space:nowrap">
                                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:{{ ($project->status->color ?? '#6B7280') }}22;color:{{ $project->status->color ?? '#6B7280' }}">
                                    {{ $project->status->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td style="padding:12px 16px;white-space:nowrap">
                                @if($project->deadline)
                                    @php
                                        if ($project->completed_at) {
                                            $completionStatus = $project->getCompletionStatus();
                                            $deadlineColor = match($completionStatus) {
                                                'early'   => 'var(--apple-teal)',
                                                'on-time' => 'var(--apple-green)',
                                                'late'    => 'var(--apple-orange)',
                                                default   => 'var(--dark-text-primary)'
                                            };
                                        } else {
                                            $deadlineColor = $project->deadline->isPast() ? 'var(--apple-red)' : 'var(--dark-text-primary)';
                                        }
                                    @endphp
                                    <span style="font-size:0.8rem;color:{{ $deadlineColor }}">{{ $project->deadline->format('d M Y') }}</span>
                                @else
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;white-space:nowrap;text-align:right" onclick="event.stopPropagation()">
                                <div style="display:inline-flex;align-items:center;gap:6px">
                                    <a href="{{ route('projects.show', $project) }}"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);text-decoration:none;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.edit', $project) }}"
                                       style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-orange);text-decoration:none;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                    <button onclick="deleteProject({{ $project->id }})"
                                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:7px;cursor:pointer"
                                            onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:48px 20px;text-align:center">
                                <i class="fas fa-folder-open" style="font-size:2rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                                <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum Ada Proyek</p>
                                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 16px">Mulai dengan membuat proyek pertama</p>
                                <a href="{{ route('projects.create') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.8rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-plus"></i>Tambah Proyek
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects instanceof \Illuminate\Pagination\LengthAwarePaginator && $projects->hasPages())
            <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <x-ui.pagination :paginator="$projects->withQueryString()" variant="full" :show-info="true" />
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('projects-filter-form');
    if (!form) return;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

    // Submit on Enter in search
    const searchInput = form.querySelector('#pf-search');
    const clearBtn    = form.querySelector('#pf-clear-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); } });
        // Show/hide clear button as user types
        searchInput.addEventListener('input', () => {
            if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        });
    }
});

function deleteProject(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus proyek ini?')) return;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/projects/${id}`;
    form.innerHTML = `<input type="hidden" name="_method" value="DELETE"><input type="hidden" name="_token" value="${csrfToken}">`;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
