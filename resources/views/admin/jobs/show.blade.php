@extends('layouts.admin')

@section('title', $vacancy->title . ' - Detail Lowongan')

@section('content')
@php
    $statusColors = ['open'=>'var(--apple-green)','draft'=>'var(--apple-yellow)','closed'=>'var(--dark-text-tertiary)'];
    $statusLabels = ['open'=>'Aktif','draft'=>'Draft','closed'=>'Ditutup'];
    $sCol = $statusColors[$vacancy->status] ?? 'var(--dark-text-tertiary)';
    $sLbl = $statusLabels[$vacancy->status] ?? ucfirst($vacancy->status);
@endphp

<div style="display:flex;flex-direction:column;gap:16px">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title]
    ]" />

    {{-- Header with Tabs --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px">
            <div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                    <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $vacancy->title }}</h1>
                    <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sCol }} 15%,transparent);color:{{ $sCol }}">{{ $sLbl }}</span>
                </div>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Dipublikasikan {{ $vacancy->created_at->format('d M Y') }} · Lokasi {{ $vacancy->location }}</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ route('career.show', $vacancy->slug) }}" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:var(--dark-bg-secondary);color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:9px;font-size:0.78rem;font-weight:600;text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'"><i class="fas fa-external-link-alt" style="font-size:0.7rem"></i>View Public</a>
                <a href="{{ route('admin.jobs.edit', $vacancy->id) }}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:9px;font-size:0.78rem;font-weight:600;text-decoration:none"><i class="fas fa-edit" style="font-size:0.7rem"></i>Edit</a>
            </div>
        </div>
        <x-job-tabs :vacancy="$vacancy" active-tab="overview" />
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach([['Total Pelamar','var(--apple-blue)','fa-users',$vacancy->applications_count,'Akumulasi lamaran'],['Status',''.($sCol).'','fa-circle',$sLbl,'Update: '.$vacancy->updated_at->diffForHumans()],['Dipublikasikan','var(--apple-green)','fa-calendar',$vacancy->created_at->format('d M Y'),'Tanggal tayang'],['Deadline','var(--apple-orange)','fa-clock',$vacancy->deadline ? $vacancy->deadline->format('d M Y') : 'Tidak ada','Batas lamaran']] as [$l,$col,$ico,$val,$sub])
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $col }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $col }} 25%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px"><p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $col }};margin:0">{{ $l }}</p><i class="fas {{ $ico }}" style="color:color-mix(in srgb,{{ $col }} 50%,transparent);font-size:0.9rem"></i></div>
            <p style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 3px">{{ $val }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div style="grid-column:span 2;display:flex;flex-direction:column;gap:14px">
            {{-- Detail Lowongan --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-info-circle" style="color:var(--apple-blue);margin-right:7px"></i>Detail Lowongan</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                    @foreach([['Tipe Pekerjaan','fa-briefcase',ucfirst($vacancy->employment_type)],['Lokasi','fa-map-marker-alt',$vacancy->location],['Rentang Gaji','fa-money-bill-wave',($vacancy->salary_min && $vacancy->salary_max ? 'Rp '.number_format($vacancy->salary_min,0,',','.').' - Rp '.number_format($vacancy->salary_max,0,',','.') : 'Negosiasi')],['Pengalaman','fa-graduation-cap',$vacancy->experience_years.' tahun']] as [$l,$ico,$v])
                    <div style="padding:10px;background:var(--dark-bg-secondary);border-radius:9px;border:1px solid var(--dark-separator)">
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 4px">{{ $l }}</p>
                        <p style="font-size:0.83rem;font-weight:600;color:var(--dark-text-primary);margin:0"><i class="fas {{ $ico }}" style="color:var(--apple-blue);margin-right:5px"></i>{{ $v }}</p>
                    </div>
                    @endforeach
                </div>
                <div style="border-top:1px solid var(--dark-separator);padding-top:14px;margin-bottom:14px">
                    <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 8px">Deskripsi Pekerjaan</p>
                    <div style="font-size:0.83rem;line-height:1.6;color:var(--dark-text-secondary)">{!! nl2br(e($vacancy->description)) !!}</div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                    <div>
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 8px">Tanggung Jawab</p>
                        <ul style="margin:0;padding-left:16px;display:flex;flex-direction:column;gap:4px">
                            @foreach((is_array($vacancy->responsibilities) ? $vacancy->responsibilities : (json_decode($vacancy->responsibilities, true) ?? [])) as $r)
                            <li style="font-size:0.8rem;color:var(--dark-text-primary)">{{ $r }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 8px">Kualifikasi</p>
                        <ul style="margin:0;padding-left:16px;display:flex;flex-direction:column;gap:4px">
                            @foreach((is_array($vacancy->qualifications) ? $vacancy->qualifications : (json_decode($vacancy->qualifications, true) ?? [])) as $q)
                            <li style="font-size:0.8rem;color:var(--dark-text-primary)">{{ $q }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Recent Applications --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
                    <div>
                        <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-tertiary);margin:0 0 2px">Pelamar</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pelamar Terbaru</h3>
                    </div>
                    <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none">Lihat Semua</a>
                </div>
                @if($recentApplications->count() > 0)
                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse">
                        <thead>
                            <tr style="background:var(--dark-bg-secondary);border-bottom:1px solid var(--dark-separator)">
                                @foreach(['Nama','Email','Status','Tanggal','Aksi'] as $h)
                                <th style="padding:10px 16px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);text-align:{{ $loop->last ? 'center' : 'left' }}">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentApplications as $application)
                            @php
                                $aColors=['pending'=>'var(--apple-yellow)','reviewed'=>'var(--apple-blue)','interview'=>'var(--apple-purple)','accepted'=>'var(--apple-green)','rejected'=>'var(--apple-red)'];
                                $aCol = $aColors[$application->status] ?? 'var(--dark-text-secondary)';
                            @endphp
                            <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:10px 16px;font-size:0.83rem;font-weight:600;color:var(--dark-text-primary)">{{ $application->full_name }}</td>
                                <td style="padding:10px 16px;font-size:0.8rem;color:var(--dark-text-secondary)">{{ $application->email }}</td>
                                <td style="padding:10px 16px"><span style="display:inline-flex;padding:2px 9px;border-radius:12px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $aCol }} 15%,transparent);color:{{ $aCol }}">{{ ucfirst($application->status) }}</span></td>
                                <td style="padding:10px 16px;font-size:0.78rem;color:var(--dark-text-secondary)">{{ $application->created_at->format('d M Y') }}</td>
                                <td style="padding:10px 16px;text-align:center"><a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none"><i class="fas fa-stream" style="font-size:0.65rem"></i>Pipeline</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="padding:30px;text-align:center">
                    <i class="fas fa-inbox" style="font-size:1.8rem;display:block;margin-bottom:8px;color:var(--dark-text-tertiary);opacity:.5"></i>
                    <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Belum ada pelamar untuk lowongan ini</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px">
            {{-- Quick Actions --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator)"><h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Aksi Cepat</h3></div>
                @foreach([['admin.jobs.applications',$vacancy->id,'fa-users','var(--apple-blue)','All Applications'],['admin.jobs.pipeline',$vacancy->id,'fa-stream','var(--apple-purple)','Recruitment Pipeline'],['admin.jobs.tests',$vacancy->id,'fa-clipboard-check','var(--apple-orange)','Assign & Track Tests'],['admin.jobs.interviews',$vacancy->id,'fa-calendar-alt','var(--apple-green)','View Interviews']] as [$routeName,$param,$ico,$col,$label])
                <a href="{{ route($routeName, $param) }}" style="display:flex;align-items:center;gap:10px;padding:12px 18px;border-bottom:1px solid var(--dark-separator);text-decoration:none" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                    <div style="width:28px;height:28px;border-radius:50%;background:color-mix(in srgb,{{ $col }} 15%,transparent);display:flex;align-items:center;justify-content:center;color:{{ $col }};flex-shrink:0;font-size:0.78rem"><i class="fas {{ $ico }}"></i></div>
                    <span style="font-size:0.83rem;color:var(--dark-text-secondary)">{{ $label }}</span>
                </a>
                @endforeach
                <a href="{{ route('admin.recruitment.interviews.create', ['vacancy_id' => $vacancy->id]) }}" style="display:flex;align-items:center;gap:10px;padding:12px 18px;text-decoration:none" onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='transparent'">
                    <div style="width:28px;height:28px;border-radius:50%;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-teal);flex-shrink:0;font-size:0.78rem"><i class="fas fa-calendar-plus"></i></div>
                    <span style="font-size:0.83rem;color:var(--dark-text-secondary)">Schedule New Interview</span>
                </a>
            </div>

            {{-- Status Update --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Status Lowongan</h3>
                    <span style="display:inline-flex;padding:3px 9px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,{{ $sCol }} 15%,transparent);color:{{ $sCol }}">{{ $sLbl }}</span>
                </div>
                <form action="{{ route('admin.jobs.update', $vacancy->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="title" value="{{ $vacancy->title }}">
                    <input type="hidden" name="description" value="{{ $vacancy->description }}">
                    <input type="hidden" name="employment_type" value="{{ $vacancy->employment_type }}">
                    <input type="hidden" name="location" value="{{ $vacancy->location }}">
                    <div style="position:relative;margin-bottom:6px">
                        <select name="status" onchange="this.form.submit()" style="width:100%;padding:8px 32px 8px 10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;appearance:none;cursor:pointer">
                            <option value="draft" {{ $vacancy->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="open" {{ $vacancy->status === 'open' ? 'selected' : '' }}>Aktif (Buka)</option>
                            <option value="closed" {{ $vacancy->status === 'closed' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                        <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);pointer-events:none;font-size:0.65rem"></i>
                    </div>
                    <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0">Perubahan disimpan otomatis.</p>
                </form>
                <div style="border-top:1px solid var(--dark-separator);margin-top:10px;padding-top:10px;display:flex;flex-direction:column;gap:4px">
                    <div style="display:flex;justify-content:space-between"><span style="font-size:0.7rem;color:var(--dark-text-secondary)">Dibuat</span><span style="font-size:0.7rem;color:var(--dark-text-tertiary)">{{ $vacancy->created_at->format('d M Y H:i') }}</span></div>
                    <div style="display:flex;justify-content:space-between"><span style="font-size:0.7rem;color:var(--dark-text-secondary)">Terakhir Update</span><span style="font-size:0.7rem;color:var(--dark-text-tertiary)">{{ $vacancy->updated_at->format('d M Y H:i') }}</span></div>
                </div>
            </div>

            {{-- Application Stats --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Statistik</h3>
                @php
                    $pendingCount = $vacancy->applications()->where('status', 'pending')->count();
                    $reviewedCount = $vacancy->applications()->where('status', 'reviewed')->count();
                    $acceptedCount = $vacancy->applications()->where('status', 'accepted')->count();
                    $rejectedCount = $vacancy->applications()->where('status', 'rejected')->count();
                    $total = $vacancy->applications_count ?: 1;
                @endphp
                <div style="display:flex;flex-direction:column;gap:9px">
                    @foreach([['Total Pelamar','var(--apple-blue)',$vacancy->applications_count,100],['Pending','var(--apple-yellow)',$pendingCount,round($pendingCount/$total*100)],['Reviewed','var(--apple-blue)',$reviewedCount,round($reviewedCount/$total*100)],['Diterima','var(--apple-green)',$acceptedCount,round($acceptedCount/$total*100)],['Ditolak','var(--apple-red)',$rejectedCount,round($rejectedCount/$total*100)]] as [$l,$col,$cnt,$pct])
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $l }}</span>
                            <span style="font-size:0.75rem;font-weight:700;color:var(--dark-text-primary)">{{ $cnt }}</span>
                        </div>
                        <div style="height:5px;border-radius:3px;background:rgba(255,255,255,0.08)">
                            <div style="height:100%;border-radius:3px;background:{{ $col }};width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
