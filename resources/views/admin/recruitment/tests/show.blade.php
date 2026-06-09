@extends('layouts.admin')

@section('title', 'Test Template - ' . $test->title)

@section('content')
@php
    $typeColor = match($test->test_type) {
        'psychology' => 'var(--apple-blue)',
        'psychometric' => 'var(--apple-yellow)',
        'technical' => 'var(--apple-red)',
        'aptitude' => 'var(--apple-green)',
        'personality' => 'var(--apple-purple)',
        default => 'var(--dark-text-secondary)',
    };
@endphp
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);margin:0 0 4px">Manajemen Tes</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $test->title }}</h1>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $typeColor }} 15%,transparent);color:{{ $typeColor }}">{{ ucfirst($test->test_type) }}</span>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $test->is_active ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }} 15%,transparent);color:{{ $test->is_active ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }}">{{ $test->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">{{ $test->description ?? 'Template tes untuk proses rekrutmen' }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-self:flex-end">
            <a href="{{ route('admin.recruitment.tests.edit', $test) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:0.8rem;font-weight:700;color:#fff;background:var(--apple-blue);border:none;border-radius:9px;text-decoration:none"
               onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                <i class="fas fa-edit" style="font-size:0.7rem"></i>Edit Template
            </a>
            <a href="{{ route('admin.recruitment.tests.index') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:0.8rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.7rem"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach([
            ['Total Sesi','var(--apple-blue)','fa-users',$test->test_sessions_count,'Kandidat mengikuti'],
            ['Avg. Score','var(--apple-green)','fa-chart-line',number_format($statistics['average_score'] ?? 0, 1),'Skor rata-rata'],
            ['Pass Rate','var(--apple-orange)','fa-percentage',number_format($statistics['pass_rate'] ?? 0, 1).'%','Tingkat kelulusan'],
            ['Avg. Duration','var(--apple-purple)','fa-clock',number_format($statistics['average_duration'] ?? 0, 0),'Menit rata-rata'],
        ] as [$label,$col,$icon,$val,$sub])
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $col }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $col }} 25%,var(--dark-separator));border-radius:14px;padding:18px 20px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $col }};margin:0">{{ $label }}</p>
                <i class="fas {{ $icon }}" style="font-size:1rem;color:color-mix(in srgb,{{ $col }} 50%,transparent)"></i>
            </div>
            <p style="font-size:1.5rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px">{{ $val }}</p>
            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:flex-start">

        {{-- Info + Assign --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Template Info --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                <div style="margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Template</h3>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px">
                    @foreach(['Durasi Tes'=>$test->duration_minutes.' menit','Passing Score'=>$test->passing_score.'%','Total Pertanyaan'=>($test->total_questions ?? 0).' pertanyaan','Dibuat'=>$test->created_at->format('d M Y H:i'),'Terakhir Update'=>$test->updated_at->format('d M Y H:i'),'Sesi Selesai'=>$test->completed_sessions_count.' sesi'] as $k=>$v)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid color-mix(in srgb,var(--dark-separator) 50%,transparent)">
                        <span style="font-size:0.8rem;color:var(--dark-text-secondary)">{{ $k }}</span>
                        <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary)">{{ $v }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Template File --}}
            @if($test->test_type === 'document-editing' && $test->template_file_path)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Template Dokumen</h3>
                </div>
                <div style="padding:14px 18px">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:var(--dark-bg-secondary);border-radius:10px;border:1px solid var(--dark-separator)">
                        <div style="display:flex;align-items:center;gap:10px">
                            <div style="width:38px;height:38px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-blue);font-size:1rem">
                                <i class="fas fa-file-word"></i>
                            </div>
                            <div>
                                <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ basename($test->template_file_path) }}</p>
                                <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">Template Dokumen</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.recruitment.tests.download-template', $test) }}"
                           style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none">
                            <i class="fas fa-download" style="font-size:0.6rem"></i>Download
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Instructions --}}
            @if($test->instructions)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Instruksi Tes</h3>
                </div>
                <div style="padding:16px 18px;font-size:0.85rem;color:var(--dark-text-secondary);line-height:1.7">
                    {!! nl2br(e($test->instructions)) !!}
                </div>
            </div>
            @endif

            {{-- Assign Test --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0"><i class="fas fa-user-plus" style="color:var(--apple-green);margin-right:6px"></i>Assign Test ke Kandidat</h3>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:3px 0 0">Berikan tes ini kepada kandidat yang belum memiliki sesi aktif</p>
                </div>
                <div style="padding:18px">
                    @if(isset($availableCandidates) && $availableCandidates->count() > 0)
                    <form action="{{ route('admin.recruitment.tests.assign') }}" method="POST" style="display:flex;flex-direction:column;gap:12px">
                        @csrf
                        <input type="hidden" name="test_template_id" value="{{ $test->id }}">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:5px">Kandidat <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="job_application_id" required
                                        style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;appearance:none;outline:none;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">-- Pilih Kandidat --</option>
                                    @foreach($availableCandidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->full_name }} - {{ $candidate->jobVacancy->title ?? 'No Position' }} ({{ $candidate->email }})</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;font-size:0.65rem;color:var(--dark-text-secondary)"></i>
                            </div>
                            @error('job_application_id')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:5px">Expired At (opsional)</label>
                            <input type="datetime-local" name="expires_at"
                                   min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                   value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;box-sizing:border-box;color-scheme:dark"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div style="display:flex;justify-content:flex-end">
                            <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.83rem;font-weight:700;cursor:pointer"
                                    onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                <i class="fas fa-paper-plane"></i>Assign & Send Email
                            </button>
                        </div>
                    </form>
                    @else
                    <div style="text-align:center;padding:24px 0;color:var(--dark-text-secondary)">
                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.5"></i>
                        <p style="font-size:0.85rem;margin:0">Tidak ada kandidat tersedia. Semua sudah memiliki sesi aktif.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sessions Table --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">All Test Sessions</h3>
                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:3px 0 0">{{ $recentSessions->count() }} total sesi</p>
            </div>
            @if($recentSessions->count())
            <div style="overflow-x:auto">
                <table style="min-width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="background:var(--dark-bg-secondary)">
                            @foreach(['Kandidat','Status','Score','Durasi','Tanggal',''] as $h)
                            <th style="padding:8px 14px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);text-align:{{ $loop->last ? 'right' : 'left' }};white-space:nowrap">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                        @php
                            $sc2 = match($session->status) {
                                'completed' => 'var(--apple-green)',
                                'in_progress','in-progress' => 'var(--apple-blue)',
                                'pending' => 'var(--apple-yellow)',
                                default => 'var(--dark-text-tertiary)',
                            };
                            $dur = ($session->started_at && $session->completed_at)
                                ? $session->started_at->diffInMinutes($session->completed_at)
                                : null;
                        @endphp
                        <tr style="border-top:1px solid var(--dark-separator)">
                            <td style="padding:10px 14px">
                                @if($session->jobApplication)
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="width:32px;height:32px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 20%,var(--dark-bg-secondary));display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;color:var(--apple-blue);flex-shrink:0">{{ strtoupper(substr($session->jobApplication->full_name, 0, 1)) }}</div>
                                    <div>
                                        <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 1px">{{ $session->jobApplication->full_name }}</p>
                                        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $session->jobApplication->email }}</p>
                                    </div>
                                </div>
                                @else
                                <span style="font-size:0.8rem;color:var(--dark-text-tertiary)">Data dihapus</span>
                                @endif
                            </td>
                            <td style="padding:10px 14px">
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sc2 }} 15%,transparent);color:{{ $sc2 }}">{{ ucfirst(str_replace('_',' ',$session->status)) }}</span>
                            </td>
                            <td style="padding:10px 14px">
                                @if($session->status === 'completed' && $session->score !== null)
                                <span style="font-size:0.88rem;font-weight:700;color:var(--apple-blue)">{{ number_format($session->score, 1) }}</span>
                                @else
                                <span style="color:var(--dark-text-tertiary)">-</span>
                                @endif
                            </td>
                            <td style="padding:10px 14px;font-size:0.8rem;color:var(--dark-text-secondary)">{{ $dur ? $dur.' mnt' : '-' }}</td>
                            <td style="padding:10px 14px;font-size:0.78rem;color:var(--dark-text-secondary)">{{ $session->created_at->format('d M Y') }}</td>
                            <td style="padding:10px 14px;text-align:right">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px">
                                    @if($session->status === 'completed')
                                    <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-secondary);padding:4px 9px;border-radius:6px;border:1px solid var(--dark-separator);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                        <i class="fas fa-chart-bar" style="font-size:0.65rem"></i>Hasil
                                    </a>
                                    @elseif($session->session_token)
                                    <a href="{{ route('candidate.test.show', $session->session_token) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none">
                                        <i class="fas fa-external-link-alt" style="font-size:0.65rem"></i>Buka
                                    </a>
                                    @endif
                                    @if(in_array($session->status, ['pending','not-started','expired']))
                                    <form action="{{ route('admin.recruitment.tests.sessions.cancel', $session) }}" method="POST" style="display:inline" onsubmit="return confirm('Batalkan test session ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer">
                                            <i class="fas fa-times" style="font-size:0.65rem"></i>Batalkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="text-align:center;padding:40px 20px;color:var(--dark-text-secondary)">
                <i class="fas fa-clipboard-check" style="font-size:2.5rem;display:block;margin-bottom:10px;opacity:.4"></i>
                <p style="font-size:0.85rem;margin:0">Belum ada sesi tes dengan template ini.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
