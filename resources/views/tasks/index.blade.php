@extends('layouts.admin')

@section('title', 'Tugas')
@section('page-title', 'Manajemen Tugas')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- KPI Cards --}}
    @php
    $totalTasks     = $tasks->total();
    $pendingCount   = $tasks->getCollection()->whereIn('status', ['todo','in_progress'])->count();
    $completedCount = $tasks->getCollection()->where('status','done')->count();
    $overdueCount   = $tasks->getCollection()->filter(fn($t) => $t->isOverdue() && $t->status !== 'done')->count();

    $statsData = [
        ['label'=>'Total Tugas',   'value'=>$totalTasks,     'sub'=>'semua tugas',         'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',          'icon'=>'fa-list-check'],
        ['label'=>'Dalam Proses',  'value'=>$pendingCount,   'sub'=>'belum selesai',        'color'=>'var(--apple-orange)',      'bg'=>'var(--apple-orange)',   'icon'=>'fa-hourglass-half'],
        ['label'=>'Selesai',       'value'=>$completedCount, 'sub'=>'sudah dikerjakan',     'color'=>'var(--apple-green)',       'bg'=>'var(--apple-green)',    'icon'=>'fa-check-circle'],
        ['label'=>'Terlambat',     'value'=>$overdueCount,   'sub'=>'melewati tenggat',     'color'=>'var(--apple-red)',         'bg'=>'var(--apple-red)',      'icon'=>'fa-exclamation-triangle'],
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
            'search'     => request('search'),
            'status'     => request('status'),
            'priority'   => request('priority'),
            'project_id' => request('project_id'),
        ])->filter()->count();
    @endphp
    <form method="GET" action="{{ route('tasks.index') }}" id="tasks-filter-form">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:220px">
                <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
                <input type="text" name="search" id="tf-search" value="{{ request('search') }}"
                       placeholder="Cari tugas, deskripsi…"
                       style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
                <button type="button" id="tf-clear-search"
                        style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                        onclick="document.getElementById('tf-search').value='';this.style.display='none';document.getElementById('tasks-filter-form').submit()">
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
                            style="padding:6px 28px 6px 10px;background:{{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('status') ? 'color-mix(in srgb,var(--apple-blue) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('status') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Status</option>
                        <option value="todo"        {{ request('status')=='todo'        ? 'selected':'' }}>Belum Dikerjakan</option>
                        <option value="in_progress" {{ request('status')=='in_progress' ? 'selected':'' }}>Sedang Dikerjakan</option>
                        <option value="done"        {{ request('status')=='done'        ? 'selected':'' }}>Selesai</option>
                        <option value="blocked"     {{ request('status')=='blocked'     ? 'selected':'' }}>Terblokir</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('status') ? 'var(--apple-blue)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Priority pill --}}
                <div style="position:relative">
                    <select name="priority"
                            style="padding:6px 28px 6px 10px;background:{{ request('priority') ? 'color-mix(in srgb,var(--apple-orange) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('priority') ? 'color-mix(in srgb,var(--apple-orange) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('priority') ? 'var(--apple-orange)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('priority') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Prioritas</option>
                        <option value="urgent" {{ request('priority')=='urgent' ? 'selected':'' }}>Mendesak</option>
                        <option value="high"   {{ request('priority')=='high'   ? 'selected':'' }}>Tinggi</option>
                        <option value="normal" {{ request('priority')=='normal' ? 'selected':'' }}>Normal</option>
                        <option value="low"    {{ request('priority')=='low'    ? 'selected':'' }}>Rendah</option>
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('priority') ? 'var(--apple-orange)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Project pill --}}
                <div style="position:relative">
                    <select name="project_id"
                            style="padding:6px 28px 6px 10px;background:{{ request('project_id') ? 'color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('project_id') ? 'color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('project_id') ? 'var(--apple-purple)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('project_id') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id')==$project->id ? 'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('project_id') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Active filter badge + reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('tasks.index') }}"
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
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Tugas</h3>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                @php $isEmptyTasks = ($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tasks->total() : $tasks->count()) === 0; @endphp
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                    @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($tasks->total() === 0)
                            0 tugas
                        @else
                            {{ $tasks->firstItem() }}–{{ $tasks->lastItem() }} dari {{ $tasks->total() }}
                        @endif
                    @endif
                </span>
                @unless($isEmptyTasks)
                <a href="{{ route('tasks.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <i class="fas fa-plus"></i>Tambah Tugas
                </a>
                @endunless
            </div>
        </div>

        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary)">
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Tugas</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Status</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Prioritas</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Penanggung Jawab</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Tenggat</th>
                        <th style="padding:10px 16px;text-align:center;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Kemajuan</th>
                        <th style="padding:10px 16px;text-align:right;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $i => $task)
                        @php
                            $statusCfg = [
                                'todo'        => ['label'=>'Belum Dikerjakan', 'color'=>'var(--apple-orange)', 'icon'=>'fa-circle'],
                                'in_progress' => ['label'=>'Sedang Dikerjakan','color'=>'var(--apple-blue)',   'icon'=>'fa-rotate'],
                                'done'        => ['label'=>'Selesai',           'color'=>'var(--apple-green)', 'icon'=>'fa-check-circle'],
                                'blocked'     => ['label'=>'Terblokir',         'color'=>'var(--apple-red)',   'icon'=>'fa-ban'],
                            ];
                            $priorityCfg = [
                                'urgent' => ['label'=>'Mendesak', 'color'=>'var(--apple-red)',    'icon'=>'fa-exclamation-circle'],
                                'high'   => ['label'=>'Tinggi',   'color'=>'var(--apple-orange)', 'icon'=>'fa-arrow-up'],
                                'normal' => ['label'=>'Normal',   'color'=>'var(--apple-blue)',   'icon'=>'fa-minus'],
                                'low'    => ['label'=>'Rendah',   'color'=>'var(--apple-teal)',   'icon'=>'fa-arrow-down'],
                            ];
                            $sc       = $statusCfg[$task->status]   ?? $statusCfg['todo'];
                            $pc       = $priorityCfg[$task->priority] ?? $priorityCfg['normal'];
                            $isOverdue = $task->isOverdue() && $task->status !== 'done';
                            $progress  = $task->getProgress();
                            $progressColor = $progress >= 80 ? 'var(--apple-green)' : ($progress >= 50 ? 'var(--apple-blue)' : 'var(--apple-orange)');
                            $rowBg = $i % 2 === 1 ? 'rgba(255,255,255,0.02)' : 'transparent';
                        @endphp
                        <tr style="border-top:1px solid var(--dark-separator);{{ $isOverdue ? 'border-left:3px solid var(--apple-red);' : '' }}background:{{ $rowBg }};cursor:pointer;transition:background .15s"
                            onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.background='{{ $rowBg }}'"
                            onclick="window.location='{{ route('tasks.show', $task) }}'">

                            <td style="padding:12px 16px">
                                <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $task->title }}</span>
                                @if($task->project)
                                    <a href="{{ route('projects.show', $task->project) }}"
                                       onclick="event.stopPropagation()"
                                       style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;color:var(--apple-blue);text-decoration:none;margin-top:3px"
                                       onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-folder" style="font-size:0.6rem"></i>{{ $task->project->name }}
                                    </a>
                                @endif
                                @if($task->description)
                                    <span style="display:block;font-size:0.7rem;color:var(--dark-text-secondary);margin-top:2px">{{ Str::limit($task->description, 70) }}</span>
                                @endif
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $sc['color'] }} 14%,transparent);color:{{ $sc['color'] }}">
                                    <i class="fas {{ $sc['icon'] }}" style="font-size:0.65rem"></i>{{ $sc['label'] }}
                                </span>
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $pc['color'] }} 14%,transparent);color:{{ $pc['color'] }}">
                                    <i class="fas {{ $pc['icon'] }}" style="font-size:0.65rem"></i>{{ $pc['label'] }}
                                </span>
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                @if($task->assignedUser)
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);font-size:0.65rem;font-weight:700;flex-shrink:0">
                                            {{ strtoupper(substr($task->assignedUser->name, 0, 2)) }}
                                        </span>
                                        <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $task->assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">—</span>
                                @endif
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                @if($task->due_date)
                                    <span style="display:block;font-size:0.8rem;color:{{ $isOverdue ? 'var(--apple-red)' : 'var(--dark-text-primary)' }}">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                    </span>
                                    <span style="font-size:0.68rem;color:{{ $isOverdue ? 'var(--apple-red)' : 'var(--dark-text-secondary)' }}">
                                        @if($isOverdue)<i class="fas fa-exclamation-circle" style="margin-right:3px"></i>@endif{{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                                    </span>
                                @else
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">—</span>
                                @endif
                            </td>

                            <td style="padding:12px 16px;text-align:center">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;min-width:72px;margin:0 auto">
                                    <span style="font-size:0.72rem;font-weight:700;color:{{ $progressColor }}">{{ $progress }}%</span>
                                    <div style="width:72px;height:5px;border-radius:3px;background:rgba(255,255,255,0.08);overflow:hidden">
                                        <div style="height:100%;border-radius:3px;background:{{ $progressColor }};width:{{ $progress }}%;transition:width .3s"></div>
                                    </div>
                                </div>
                            </td>

                            <td style="padding:12px 16px;text-align:right;white-space:nowrap" onclick="event.stopPropagation()">
                                <div style="display:inline-flex;align-items:center;gap:6px">
                                    <a href="{{ route('tasks.show', $task) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);text-decoration:none;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('tasks.edit', $task) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-orange);text-decoration:none;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:48px 20px;text-align:center">
                                <div style="width:52px;height:52px;border-radius:14px;background:color-mix(in srgb,var(--dark-text-secondary) 10%,var(--dark-bg-tertiary));display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                                    <i class="fas fa-inbox" style="font-size:1.4rem;color:var(--dark-text-tertiary)"></i>
                                </div>
                                <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">
                                    @if($activeFilters > 0) Tidak Ada Hasil @else Belum Ada Tugas @endif
                                </p>
                                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 18px">
                                    @if($activeFilters > 0) Coba ubah atau reset filter pencarian @else Buat tugas pertama untuk memulai @endif
                                </p>
                                @if($activeFilters > 0)
                                <a href="{{ route('tasks.index') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.78rem;font-weight:600;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-xmark"></i>Reset Filter
                                </a>
                                @else
                                <a href="{{ route('tasks.create') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.8rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-plus"></i>Tambah Tugas
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->hasPages())
            <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <x-ui.pagination :paginator="$tasks->appends(request()->all())" variant="full" :show-info="true" />
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('tasks-filter-form');
    if (!form) return;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

    // Submit on Enter, show/hide clear button
    const searchInput = form.querySelector('#tf-search');
    const clearBtn    = form.querySelector('#tf-clear-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); } });
        searchInput.addEventListener('input', () => {
            if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        });
    }
});
</script>
@endpush
