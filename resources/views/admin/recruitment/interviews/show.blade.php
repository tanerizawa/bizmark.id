@extends('layouts.admin')

@section('title', 'Interview - ' . $interview->jobApplication->full_name)

@section('content')
@php
    $application = $interview->jobApplication;
    $vacancy = $application->jobVacancy;
    $sc = $interview->status === 'completed' ? 'var(--apple-green)' : ($interview->status === 'scheduled' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)');
@endphp
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.interviews.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Interviews
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Manajemen Talenta</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $application->full_name }}</h1>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sc }} 15%,transparent);color:{{ $sc }}">{{ ucfirst($interview->status) }}</span>
            </div>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">{{ $vacancy?->title }} · {{ $interview->scheduled_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-self:flex-end">
            <a href="{{ route('admin.recruitment.interviews.edit', $interview) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--apple-blue);color:#fff;border-radius:10px;font-size:0.82rem;font-weight:600;text-decoration:none"
               onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                <i class="fas fa-edit" style="font-size:0.7rem"></i>Edit
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach([
            ['label'=>'Tanggal','value'=>$interview->scheduled_at->format('d M Y'),'sub'=>$interview->scheduled_at->diffForHumans(),'color'=>'var(--apple-blue)'],
            ['label'=>'Durasi','value'=>$interview->duration_minutes.' mnt','sub'=>ucfirst($interview->interview_type),'color'=>'var(--apple-green)'],
            ['label'=>'Meeting','value'=>ucfirst(str_replace('-',' ',$interview->meeting_type)),'sub'=>'Tipe pertemuan','color'=>'var(--apple-teal)'],
            ['label'=>'Status','value'=>ucfirst($interview->status),'sub'=>'Update '.$interview->updated_at->diffForHumans(),'color'=>$sc],
        ] as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['color'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['color'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};margin:0;opacity:.85">{{ $s['label'] }}</p>
            <p style="font-size:1.5rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">
        {{-- Left --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Detail --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Informasi</p>
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Interview</h3>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                    <div>
                        <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 3px">Kandidat</p>
                        <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $application->full_name }}</p>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $application->email }}{{ $application->phone ? ' · '.$application->phone : '' }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 3px">Posisi</p>
                        <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $vacancy?->title }}</p>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $vacancy?->location }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 3px">Jadwal</p>
                        <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $interview->scheduled_at->format('d M Y, H:i') }} WIB</p>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $interview->scheduled_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 3px">Pertemuan</p>
                        <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $interview->getMeetingTypeLabel() }}</p>
                        @if($interview->meeting_link)
                        <a href="{{ $interview->meeting_link }}" target="_blank" style="font-size:0.72rem;color:var(--apple-blue);text-decoration:none;word-break:break-all">{{ $interview->meeting_link }}</a>
                        @elseif($interview->location)
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $interview->location }}</p>
                        @endif
                    </div>
                </div>

                {{-- Interviewer --}}
                <div style="margin-bottom:16px;padding-top:16px;border-top:1px solid var(--dark-separator)">
                    <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 10px">Interviewer</p>
                    @php $ivrs = $interview->interviewers(); @endphp
                    @forelse($ivrs as $ivr)
                    <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;margin:0 6px 6px 0">
                        <div style="width:32px;height:32px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;flex-shrink:0">
                            {{ strtoupper(substr($ivr->full_name ?? $ivr->name, 0, 2)) }}
                        </div>
                        <div>
                            <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $ivr->full_name ?? $ivr->name }}</p>
                            <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">{{ $ivr->email }}</p>
                        </div>
                    </div>
                    @empty
                    <p style="font-size:0.78rem;color:var(--dark-text-secondary)">Belum ada interviewer ditetapkan.</p>
                    @endforelse
                </div>

                @if($interview->notes)
                <div style="padding-top:16px;border-top:1px solid var(--dark-separator);margin-bottom:16px">
                    <p style="font-size:0.7rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 8px">Catatan Internal</p>
                    <div style="padding:12px 14px;background:color-mix(in srgb,var(--apple-blue) 8%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));border-radius:10px">
                        <p style="font-size:0.83rem;color:var(--dark-text-primary);margin:0">{{ $interview->notes }}</p>
                    </div>
                </div>
                @endif

                {{-- Aksi --}}
                <div style="padding-top:16px;border-top:1px solid var(--dark-separator);display:flex;flex-wrap:wrap;gap:8px">
                    @if($interview->status === 'scheduled')
                    <form action="{{ route('admin.recruitment.interviews.update', $interview) }}" method="POST" onsubmit="return confirm('Tandai interview selesai?')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);border-radius:8px;cursor:pointer">
                            <i class="fas fa-check" style="font-size:0.65rem"></i>Tandai Selesai
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.recruitment.interviews.destroy', $interview) }}" method="POST" onsubmit="return confirm('Hapus interview ini?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:8px;cursor:pointer">
                            <i class="fas fa-trash" style="font-size:0.65rem"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>

            {{-- Feedback --}}
            @if($interview->relationLoaded('feedback') && $interview->feedback->count() > 0)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                    <div>
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Feedback</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Ringkasan Penilai</h3>
                    </div>
                    <a href="{{ route('admin.recruitment.interviews.feedback.show', $interview) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:5px 12px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none">
                        Lihat Semua
                    </a>
                </div>
                @foreach($interview->feedback as $fb)
                @php
                    $rc = $fb->recommendation === 'strong-hire' ? 'var(--apple-green)' : ($fb->recommendation === 'hire' ? 'var(--apple-blue)' : ($fb->recommendation === 'maybe' ? 'var(--apple-orange)' : 'var(--apple-red)'));
                @endphp
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;padding:14px;margin-bottom:10px">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                        <div>
                            <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $fb->interviewer->name }}</p>
                            <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">{{ $fb->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $rc }} 15%,transparent);color:{{ $rc }}">{{ $fb->getRecommendationLabel() }}</span>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:6px">
                        @foreach(['technical_score'=>'Teknis','communication_score'=>'Komunikasi'] as $field=>$lbl)
                        <div style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $lbl }}: <span style="font-weight:700;color:var(--dark-text-primary)">{{ $fb->$field }}/10</span></div>
                        @endforeach
                        <div style="font-size:0.75rem;color:var(--dark-text-secondary)">Overall: <span style="font-weight:700;color:var(--dark-text-primary)">{{ $fb->calculateOverallRating() }}/10</span></div>
                    </div>
                    @if($fb->notes)<p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">{{ $fb->notes }}</p>@endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px">
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Aksi Cepat</h3>
                <div style="display:flex;flex-direction:column;gap:4px">
                    @foreach([
                        [route('admin.recruitment.interviews.create', ['application_id'=>$application->id]),'fa-calendar-plus','var(--apple-blue)','Jadwalkan Interview Lanjutan'],
                        [route('admin.recruitment.tests.create', ['application_id'=>$application->id]),'fa-clipboard-check','var(--apple-green)','Assign Test'],
                        ['mailto:'.$application->email,'fa-envelope','var(--apple-orange)','Kirim Email'],
                    ] as $a)
                    <a href="{{ $a[0] }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:9px;text-decoration:none;color:var(--dark-text-primary)" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                        <i class="fas {{ $a[1] }}" style="color:{{ $a[2] }};width:16px;text-align:center"></i>
                        <span style="font-size:0.82rem">{{ $a[3] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Timeline</h3>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <i class="fas fa-clock" style="color:var(--apple-blue);width:14px;text-align:center"></i>
                        <span style="font-size:0.8rem;color:var(--dark-text-secondary)">Dijadwalkan {{ $interview->created_at->diffForHumans() }}</span>
                    </div>
                    @if($interview->candidate_joined_at)
                    <div style="display:flex;align-items:center;gap:8px">
                        <i class="fas fa-sign-in-alt" style="color:var(--apple-green);width:14px;text-align:center"></i>
                        <span style="font-size:0.8rem;color:var(--dark-text-secondary)">Kandidat bergabung {{ $interview->candidate_joined_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
