@extends('layouts.admin')

@section('title', 'Recruitment Pipeline')

@section('content')
@php
    $pendingBadge = $stats['screening'] + $stats['testing'] + $stats['interview'] + $stats['offer'];
@endphp
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Rekrutmen
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);margin:0 0 4px">Manajemen Talenta</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Rekrutmen Pipeline</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Pantau pergerakan kandidat di setiap tahap proses</p>
        </div>
        @if($pendingBadge > 0)
        <span style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:20px;font-size:0.78rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 30%,transparent)">
            <i class="fas fa-users" style="margin-right:6px;font-size:0.7rem"></i>{{ $pendingBadge }} kandidat aktif
        </span>
        @endif
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px">
        @php $pipelineStats = [
            ['label'=>'Total Aktif',       'value'=>$stats['total_in_pipeline'], 'sub'=>'Dalam pipeline',     'color'=>'var(--apple-blue)'],
            ['label'=>'Screening',         'value'=>$stats['screening'],          'sub'=>'Tahap awal',         'color'=>'var(--apple-blue)'],
            ['label'=>'Testing',           'value'=>$stats['testing'],            'sub'=>'Tes berlangsung',    'color'=>'var(--apple-orange)'],
            ['label'=>'Interview',         'value'=>$stats['interview'],          'sub'=>'Jadwal aktif',       'color'=>'var(--apple-purple)'],
            ['label'=>'Offer',             'value'=>$stats['offer'],              'sub'=>'Tahap penawaran',    'color'=>'var(--apple-green)'],
            ['label'=>'Minggu Ini',        'value'=>$stats['completed_this_week'],'sub'=>'Selesai minggu ini','color'=>'var(--apple-teal)'],
        ]; @endphp
        @foreach($pipelineStats as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['color'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['color'] }} 25%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <p style="font-size:0.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.85;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:1.6rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.67rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Pencarian & Filter</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Sesuaikan Pipeline</h3>
            </div>
        </div>
        <form method="GET" action="{{ route('admin.recruitment.pipeline.index') }}">
            <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:10px;align-items:flex-end">
                <div>
                    <label style="display:block;font-size:0.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--dark-text-secondary);margin-bottom:5px">Lowongan</label>
                    <div style="position:relative">
                        <select name="vacancy_id"
                                style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                                onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <option value="">Semua Lowongan</option>
                            @foreach(\App\Models\JobVacancy::where('status', 'open')->get() as $vacancy)
                            <option value="{{ $vacancy->id }}" {{ request('vacancy_id') == $vacancy->id ? 'selected' : '' }}>{{ $vacancy->title }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:0.7rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--dark-text-secondary);margin-bottom:5px">Tahap</label>
                    <div style="position:relative">
                        <select name="stage"
                                style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                                onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <option value="">Semua Tahap</option>
                            <option value="screening"  {{ request('stage') == 'screening'  ? 'selected' : '' }}>Screening</option>
                            <option value="testing"    {{ request('stage') == 'testing'    ? 'selected' : '' }}>Testing</option>
                            <option value="interview"  {{ request('stage') == 'interview'  ? 'selected' : '' }}>Interview</option>
                            <option value="offer"      {{ request('stage') == 'offer'      ? 'selected' : '' }}>Offer</option>
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                    </div>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit" style="padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:opacity .2s;white-space:nowrap" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-filter" style="margin-right:5px"></i>Terapkan
                    </button>
                    <a href="{{ route('admin.recruitment.pipeline.index') }}" style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none" title="Reset">
                        <i class="fas fa-undo" style="font-size:0.75rem"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Pipeline Table --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Kandidat</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pipeline Kandidat</h3>
            </div>
        </div>
        <div style="overflow-x:auto">
            @if($applications->count() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--dark-bg-secondary)">
                    <tr>
                        @foreach(['Kandidat','Posisi','Dilamar','Tahap','Progress','Status','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ $col === 'Aksi' ? 'right' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $application)
                    @php
                        $currentStage = $application->recruitmentStages->where('status', 'in-progress')->first();
                        $totalStages  = $application->recruitmentStages->count();
                        $completedStages = $application->recruitmentStages->where('status', 'passed')->count();
                        $progressPercent = $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
                        $stageColorMap = ['screening'=>'var(--apple-blue)','testing'=>'var(--apple-orange)','interview'=>'var(--apple-purple)','offer'=>'var(--apple-green)'];
                        $stageColor = $currentStage ? ($stageColorMap[$currentStage->stage_name] ?? 'var(--dark-text-secondary)') : 'var(--dark-text-secondary)';
                        $statusColorMap = ['hired'=>'var(--apple-green)','rejected'=>'var(--apple-red)'];
                        $statusColor = $statusColorMap[$application->status] ?? 'var(--apple-blue)';
                    @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 18%,var(--dark-bg-secondary));color:var(--apple-blue)">
                                    {{ strtoupper(substr($application->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $application->full_name }}</span>
                                    <span style="font-size:0.72rem;color:var(--dark-text-secondary)">{{ $application->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $application->jobVacancy?->title ?? '—' }}</span>
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary)">{{ $application->jobVacancy?->location ?? '—' }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.82rem;color:var(--dark-text-primary);display:block">{{ $application->created_at->format('d M Y') }}</span>
                            <span style="font-size:0.7rem;color:var(--dark-text-secondary);opacity:.7">{{ $application->created_at->diffForHumans() }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            @if($currentStage)
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $stageColor }} 15%,transparent);color:{{ $stageColor }}">{{ ucfirst($currentStage->stage_name) }}</span>
                            @else
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-secondary) 15%,transparent);color:var(--dark-text-secondary)">Not Started</span>
                            @endif
                        </td>
                        <td style="padding:12px 14px;min-width:100px">
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="flex:1;height:5px;border-radius:3px;background:color-mix(in srgb,var(--apple-blue) 15%,var(--dark-bg-secondary))">
                                    <div style="height:100%;border-radius:3px;background:var(--apple-blue);width:{{ $progressPercent }}%"></div>
                                </div>
                                <span style="font-size:0.7rem;color:var(--dark-text-secondary);white-space:nowrap">{{ $progressPercent }}%</span>
                            </div>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $statusColor }} 15%,transparent);color:{{ $statusColor }}">{{ ucfirst($application->status) }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:right">
                            <a href="{{ route('admin.recruitment.pipeline.show', $application) }}"
                               style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none"
                               onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                <i class="fas fa-stream"></i>Pipeline
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="padding:48px;text-align:center">
                <i class="fas fa-inbox" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
                <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Kandidat</p>
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Belum ada kandidat di pipeline</p>
                <a href="{{ route('admin.recruitment.index', ['tab'=>'jobs']) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;text-decoration:none">
                    <i class="fas fa-briefcase"></i>Lihat Lowongan
                </a>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
