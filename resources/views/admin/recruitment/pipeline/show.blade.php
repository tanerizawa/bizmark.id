@extends('layouts.admin')

@section('title', 'Pipeline Detail - ' . $application->full_name)

@section('content')
@php
    $sc = match($application->status) {
        'hired' => 'var(--apple-green)',
        'rejected' => 'var(--apple-red)',
        default => 'var(--apple-blue)',
    };
@endphp
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Breadcrumb --}}
    @if($application->jobVacancy)
        <x-breadcrumb :items="[
            ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
            ['label' => $application->jobVacancy->title, 'url' => route('admin.jobs.show', $application->jobVacancy->id)],
            ['label' => 'Pipeline', 'url' => route('admin.jobs.pipeline', $application->jobVacancy->id)],
            ['label' => $application->full_name]
        ]" />
    @else
        <x-breadcrumb :items="[
            ['label' => 'Pipeline', 'url' => route('admin.recruitment.pipeline.index')],
            ['label' => $application->full_name]
        ]" />
    @endif

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Pipeline Rekrutmen</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $application->full_name }}</h1>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sc }} 15%,transparent);color:{{ $sc }}">{{ ucfirst($application->status) }}</span>
            </div>
            @if($application->jobVacancy)
                <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0"><i class="fas fa-briefcase" style="font-size:0.72rem;margin-right:5px"></i>{{ $application->jobVacancy->title }}</p>
            @endif
        </div>
        <a href="{{ route('admin.recruitment.pipeline.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:0.8rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.7rem"></i>Pipeline
        </a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;align-items:flex-start">

        {{-- Left --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Candidate Card --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px;text-align:center">
                <div style="width:72px;height:72px;border-radius:50%;background:color-mix(in srgb,var(--apple-purple) 20%,var(--dark-bg-secondary));border:2px solid color-mix(in srgb,var(--apple-purple) 40%,var(--dark-separator));display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:700;color:var(--apple-purple);margin:0 auto 14px">
                    {{ strtoupper(substr($application->full_name, 0, 2)) }}
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 4px">{{ $application->full_name }}</h3>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 12px">{{ $application->email }}</p>
                <span style="display:inline-flex;padding:3px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $sc }} 15%,transparent);color:{{ $sc }}">{{ ucfirst($application->status) }}</span>
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--dark-separator);display:grid;grid-template-columns:1fr 1fr;gap:10px;text-align:left">
                    <div>
                        <p style="font-size:0.68rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;margin:0 0 3px">Melamar</p>
                        <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $application->created_at->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.68rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;margin:0 0 3px">Telepon</p>
                        <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $application->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Pipeline Stages --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Progress</p>
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Tahap Rekrutmen</h3>
                </div>
                <div style="padding:16px 18px">
                    @if($application->recruitmentStages->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:10px">
                        @foreach($application->recruitmentStages->sortBy('stage_order') as $stage)
                        @php
                            $stageColor = match($stage->status) {
                                'passed' => 'var(--apple-green)',
                                'in-progress' => 'var(--apple-blue)',
                                'failed' => 'var(--apple-red)',
                                'pending' => 'var(--apple-yellow)',
                                default => 'var(--dark-text-tertiary)',
                            };
                            $stageIcon = match($stage->status) {
                                'passed' => 'fa-check',
                                'in-progress' => 'fa-play',
                                'failed' => 'fa-times',
                                default => 'fa-circle',
                            };
                            $stageLinks = [
                                'screening' => ['fa-file-download','Download CV', route('admin.applications.download-cv', $application->id), true],
                                'testing' => ['fa-clipboard-check','Kelola Test', route('admin.recruitment.tests.index'), false],
                                'interview' => ['fa-calendar-alt','Jadwalkan Interview', route('admin.recruitment.interviews.create', ['application_id'=>$application->id]), false],
                            ];
                            $sl = $stageLinks[$stage->stage_name] ?? null;
                        @endphp
                        <div style="border:1px solid var(--dark-separator);border-radius:10px;padding:12px 14px;background:var(--dark-bg-secondary)">
                            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
                                <div style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:color-mix(in srgb,{{ $stageColor }} 18%,transparent);color:{{ $stageColor }};font-size:0.75rem">
                                    <i class="fas {{ $stageIcon }}"></i>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ ucfirst($stage->stage_name) }}</p>
                                    @if($stage->started_at || $stage->completed_at)
                                    <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">
                                        @if($stage->started_at)Mulai: {{ $stage->started_at->format('d M Y') }}@endif
                                        @if($stage->completed_at) · Selesai: {{ $stage->completed_at->format('d M Y') }}@endif
                                    </p>
                                    @endif
                                </div>
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,{{ $stageColor }} 15%,transparent);color:{{ $stageColor }}">{{ ucfirst($stage->status) }}</span>
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px">
                                @if($sl)
                                <a href="{{ $sl[2] }}" @if($sl[3]) target="_blank" @endif style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--apple-yellow);background:color-mix(in srgb,var(--apple-yellow) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-yellow) 25%,transparent);text-decoration:none">
                                    <i class="fas {{ $sl[0] }}" style="font-size:0.6rem"></i>{{ $sl[1] }}
                                </a>
                                @if($stage->stage_name === 'screening' && $application->portfolio_path)
                                <a href="{{ route('admin.applications.download-portfolio', $application->id) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--apple-yellow);background:color-mix(in srgb,var(--apple-yellow) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-yellow) 25%,transparent);text-decoration:none">
                                    <i class="fas fa-folder" style="font-size:0.6rem"></i>Portfolio
                                </a>
                                @endif
                                @endif
                                @if($stage->status === 'pending')
                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="in-progress">
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);cursor:pointer">
                                        <i class="fas fa-play" style="font-size:0.6rem"></i>Mulai
                                    </button>
                                </form>
                                @endif
                                @if($stage->status === 'in-progress')
                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="passed">
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);cursor:pointer">
                                        <i class="fas fa-check" style="font-size:0.6rem"></i>Lulus
                                    </button>
                                </form>
                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="failed">
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer">
                                        <i class="fas fa-times" style="font-size:0.6rem"></i>Gagal
                                    </button>
                                </form>
                                @endif
                                @if($stage->status === 'pending' || $stage->status === 'in-progress')
                                <form action="{{ route('admin.recruitment.pipeline.stages.update', $stage->id) }}" method="POST" class="inline-block">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="skipped">
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:4px;font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);background:color-mix(in srgb,var(--dark-text-secondary) 12%,transparent);padding:4px 8px;border-radius:6px;border:1px solid var(--dark-separator);cursor:pointer">
                                        <i class="fas fa-forward" style="font-size:0.6rem"></i>Skip
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:20px 0">
                        <p style="font-size:0.85rem;color:var(--dark-text-secondary);margin:0 0 12px">Belum ada tahap rekrutmen</p>
                        <form action="{{ route('admin.recruitment.pipeline.initialize', $application) }}" method="POST" style="display:inline-block">
                            @csrf
                            <input type="hidden" name="stages[0][stage_name]" value="screening">
                            <input type="hidden" name="stages[0][stage_order]" value="1">
                            <input type="hidden" name="stages[1][stage_name]" value="testing">
                            <input type="hidden" name="stages[1][stage_order]" value="2">
                            <input type="hidden" name="stages[2][stage_name]" value="interview">
                            <input type="hidden" name="stages[2][stage_order]" value="3">
                            <input type="hidden" name="stages[3][stage_name]" value="offer">
                            <input type="hidden" name="stages[3][stage_order]" value="4">
                            <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:9px;font-size:0.82rem;font-weight:700;cursor:pointer">
                                <i class="fas fa-play-circle"></i>Inisialisasi Tahap
                            </button>
                        </form>
                        <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:8px 0 0">Screening → Testing → Interview → Offer</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Timeline + Interview + Test --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Timeline --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Aktivitas</p>
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Timeline Kandidat</h3>
                </div>
                <div style="padding:16px 18px">
                    @if(isset($timeline) && count($timeline) > 0)
                    <div style="display:flex;flex-direction:column;gap:16px">
                        @foreach($timeline as $item)
                        @php
                            $tc = match($item['color'] ?? 'secondary') {
                                'primary' => 'var(--apple-blue)',
                                'success' => 'var(--apple-green)',
                                'danger' => 'var(--apple-red)',
                                'warning' => 'var(--apple-yellow)',
                                'info' => 'var(--apple-teal)',
                                default => 'var(--dark-text-tertiary)',
                            };
                        @endphp
                        <div style="display:flex;gap:12px">
                            <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:color-mix(in srgb,{{ $tc }} 18%,transparent);color:{{ $tc }};font-size:0.78rem">
                                <i class="fas fa-{{ $item['icon'] ?? 'circle' }}"></i>
                            </div>
                            <div style="flex:1;min-width:0;padding-top:4px">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:3px">
                                    <h4 style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $item['title'] }}</h4>
                                    <span style="font-size:0.68rem;color:var(--dark-text-tertiary);flex-shrink:0">{{ $item['timestamp'] }}</span>
                                </div>
                                @if($item['description'])
                                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 4px">{{ $item['description'] }}</p>
                                @endif
                                @if(isset($item['score']))
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">Skor: {{ $item['score'] }}{{ is_numeric($item['score']) ? '%' : '' }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:30px 0;color:var(--dark-text-secondary)">
                        <i class="fas fa-clock" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.5"></i>
                        <p style="font-size:0.85rem;margin:0">Belum ada aktivitas</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Interviews --}}
            @if($application->interviewSchedules->count() > 0)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;justify-content:space-between;align-items:center">
                    <div>
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Jadwal</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Interview</h3>
                    </div>
                    <a href="{{ route('admin.recruitment.interviews.create', ['application_id' => $application->id]) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none">
                        <i class="fas fa-plus" style="font-size:0.6rem"></i>Jadwalkan
                    </a>
                </div>
                <div style="overflow-x:auto">
                    <table style="min-width:100%;border-collapse:collapse">
                        <thead>
                            <tr style="background:var(--dark-bg-secondary)">
                                @foreach(['Tanggal','Tipe','Durasi','Status',''] as $h)
                                <th style="padding:8px 14px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);text-align:{{ $loop->last ? 'right' : 'left' }};white-space:nowrap">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($application->interviewSchedules as $interview)
                            @php
                                $ic = match($interview->status) {
                                    'completed' => 'var(--apple-green)',
                                    'scheduled' => 'var(--apple-yellow)',
                                    default => 'var(--dark-text-tertiary)',
                                };
                            @endphp
                            <tr style="border-top:1px solid var(--dark-separator)">
                                <td style="padding:10px 14px">
                                    <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0 0 1px;font-weight:500">{{ $interview->scheduled_at->format('d M Y') }}</p>
                                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">{{ $interview->scheduled_at->format('H:i') }}</p>
                                </td>
                                <td style="padding:10px 14px">
                                    <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal)">{{ $interview->getMeetingTypeLabel() }}</span>
                                </td>
                                <td style="padding:10px 14px;font-size:0.82rem;color:var(--dark-text-primary)">{{ $interview->duration_minutes }} mnt</td>
                                <td style="padding:10px 14px">
                                    <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $ic }} 15%,transparent);color:{{ $ic }}">{{ ucfirst($interview->status) }}</span>
                                </td>
                                <td style="padding:10px 14px;text-align:right">
                                    <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-secondary);padding:4px 9px;border-radius:6px;border:1px solid var(--dark-separator);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                        <i class="fas fa-eye" style="font-size:0.65rem"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Test Sessions --}}
            @if($application->testSessions->count() > 0)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Hasil Tes</p>
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Sesi Tes</h3>
                </div>
                <div style="overflow-x:auto">
                    <table style="min-width:100%;border-collapse:collapse">
                        <thead>
                            <tr style="background:var(--dark-bg-secondary)">
                                @foreach(['Nama Tes','Status','Skor','Selesai',''] as $h)
                                <th style="padding:8px 14px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);text-align:{{ $loop->last ? 'right' : 'left' }};white-space:nowrap">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($application->testSessions as $session)
                            @php
                                $tc2 = match($session->status) {
                                    'completed' => 'var(--apple-green)',
                                    'in-progress' => 'var(--apple-yellow)',
                                    default => 'var(--dark-text-tertiary)',
                                };
                            @endphp
                            <tr style="border-top:1px solid var(--dark-separator)">
                                <td style="padding:10px 14px;font-size:0.82rem;font-weight:500;color:var(--dark-text-primary)">{{ $session->testTemplate->title ?? 'N/A' }}</td>
                                <td style="padding:10px 14px">
                                    <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $tc2 }} 15%,transparent);color:{{ $tc2 }}">{{ ucfirst($session->status) }}</span>
                                </td>
                                <td style="padding:10px 14px">
                                    @if($session->final_score)
                                        <span style="font-size:0.88rem;font-weight:700;color:var(--apple-blue)">{{ $session->final_score }}%</span>
                                    @else
                                        <span style="color:var(--dark-text-tertiary)">-</span>
                                    @endif
                                </td>
                                <td style="padding:10px 14px;font-size:0.8rem;color:var(--dark-text-secondary)">
                                    {{ $session->completed_at ? $session->completed_at->format('d M Y') : '-' }}
                                </td>
                                <td style="padding:10px 14px;text-align:right">
                                    @if($session->status == 'completed')
                                        <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-secondary);padding:4px 9px;border-radius:6px;border:1px solid var(--dark-separator);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                            <i class="fas fa-eye" style="font-size:0.65rem"></i>Hasil
                                        </a>
                                    @elseif(($session->status == 'in-progress' || $session->status == 'pending') && $session->session_token)
                                        <a href="{{ route('candidate.test.show', $session->session_token) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none">
                                            <i class="fas fa-external-link-alt" style="font-size:0.65rem"></i>Buka
                                        </a>
                                    @else
                                        <span style="font-size:0.75rem;color:var(--dark-text-tertiary)">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
