@extends('layouts.admin')

@section('title', 'Detail Inquiry - ' . $serviceInquiry->inquiry_number)

@php
    $statusMap = [
        'new'        => ['color'=>'var(--apple-blue)',   'label'=>'Baru'],
        'processing' => ['color'=>'var(--apple-teal)',   'label'=>'Diproses'],
        'analyzed'   => ['color'=>'var(--apple-green)',  'label'=>'Dianalisis'],
        'contacted'  => ['color'=>'var(--apple-purple)',  'label'=>'Dihubungi'],
        'qualified'  => ['color'=>'var(--apple-teal)',   'label'=>'Qualified'],
        'converted'  => ['color'=>'var(--brand-gold)',   'label'=>'Konversi'],
        'registered' => ['color'=>'var(--apple-green)',  'label'=>'Terdaftar'],
        'lost'       => ['color'=>'var(--apple-red)',    'label'=>'Lost'],
    ];
    $sm = $statusMap[$serviceInquiry->status] ?? ['color'=>'var(--dark-text-secondary)', 'label'=>ucfirst($serviceInquiry->status)];

    $priorityColors = [
        'high'   => ['color'=>'var(--apple-red)',    'icon'=>'fa-arrow-up',   'label'=>'High'],
        'medium' => ['color'=>'var(--apple-orange)', 'icon'=>'fa-minus',     'label'=>'Medium'],
        'low'    => ['color'=>'var(--dark-text-tertiary)', 'icon'=>'fa-arrow-down','label'=>'Low'],
    ];
    $pc = $priorityColors[$serviceInquiry->priority] ?? $priorityColors['low'];
@endphp

@section('content')
<div style="display:flex;flex-direction:column;gap:20px">

    {{-- Page Header --}}
    <div style="display:flex;flex-direction:column;gap:4px">
        <div style="display:flex;align-items:center;gap:10px">
            <a href="{{ route('admin.leads.index', ['tab' => 'service-inquiries']) }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:10px;color:var(--apple-blue);text-decoration:none;transition:background .15s;flex-shrink:0" onmouseover="this.style.background='rgba(10,132,255,0.12)'" onmouseout="this.style.background='transparent'">
                <i class="fas fa-arrow-left" style="font-size:0.85rem"></i>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary)">Detail Inquiry</span>
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $sm['color'] }} 15%,transparent);color:{{ $sm['color'] }};border:1px solid color-mix(in srgb,{{ $sm['color'] }} 25%,transparent)">
                        <i class="fas fa-circle" style="font-size:6px"></i>
                        {{ $sm['label'] }}
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;color:{{ $pc['color'] }}">
                        <i class="fas {{ $pc['icon'] }}" style="font-size:0.6rem"></i>
                        {{ $pc['label'] }}
                    </span>
                </div>
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:2px 0 0;line-height:1.2">{{ $serviceInquiry->inquiry_number }}</h1>
                <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:4px">
                    <span style="font-size:0.75rem;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:4px">
                        <i class="far fa-calendar" style="font-size:0.7rem"></i>{{ $serviceInquiry->created_at->format('d M Y H:i') }}
                    </span>
                    <span style="font-size:0.75rem;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:4px">
                        <i class="far fa-building" style="font-size:0.7rem"></i>{{ $serviceInquiry->company_name }}
                    </span>
                    @if($serviceInquiry->client)
                        <span style="font-size:0.75rem;color:var(--apple-purple);font-weight:600;display:inline-flex;align-items:center;gap:4px">
                            <i class="fas fa-user-check" style="font-size:0.7rem"></i>Klien terdaftar
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons Bar --}}
    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        @if($serviceInquiry->ai_analysis)
            <form method="POST" action="{{ route('admin.service-inquiries.send-result', $serviceInquiry) }}" style="display:inline"
                  x-data @submit.prevent="if(confirm('Kirim email hasil analisis AI ke {{ $serviceInquiry->email }}?')) $el.submit()">
                @csrf
                <button type="submit" style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 30%,transparent);border-radius:10px;cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 25%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 15%,transparent)'">
                    <i class="fas fa-paper-plane" style="font-size:0.7rem"></i>Kirim Email Hasil AI
                </button>
            </form>
        @endif
        @if(!$serviceInquiry->client_id)
            <button onclick="document.getElementById('convertModal').style.display='flex'"
                    style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:#fff;background:var(--brand-gold);border:none;border-radius:10px;cursor:pointer;transition:all .2s"
                    onmouseover="this.style.background='#a67c00';this.style.boxShadow='0 4px 12px rgba(184,134,11,0.35)'" onmouseout="this.style.background='var(--brand-gold)';this.style.boxShadow='none'">
                <i class="fas fa-user-plus" style="font-size:0.7rem"></i>Konversi ke Klien
            </button>
        @endif
        <button onclick="if(confirm('Yakin ingin menghapus inquiry ini?')) document.getElementById('deleteForm').submit()"
                style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:#fff;background:var(--apple-red);border:none;border-radius:10px;cursor:pointer;transition:all .2s"
                onmouseover="this.style.background='#d63031';this.style.boxShadow='0 4px 12px rgba(255,59,48,0.35)'" onmouseout="this.style.background='var(--apple-red)';this.style.boxShadow='none'">
            <i class="fas fa-trash" style="font-size:0.7rem"></i>Hapus
        </button>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
                <span style="font-size:0.82rem;color:var(--apple-green);font-weight:600">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-green);opacity:.7;padding:0"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-exclamation-circle" style="color:var(--apple-red)"></i>
                <span style="font-size:0.82rem;color:var(--apple-red);font-weight:600">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);opacity:.7;padding:0"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Main Grid 2-col --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

        {{-- ================= LEFT COLUMN ================= --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Contact Information --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <i class="fas fa-user" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Informasi Kontak</span>
                </div>
                <div style="padding:18px 20px;display:grid;grid-template-columns:1fr 1fr;gap:14px 24px">
                    <div>
                        <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Nama Perusahaan</span>
                        <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $serviceInquiry->company_name }}</span>
                    </div>
                    @if($serviceInquiry->company_type)
                        <div>
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Tipe Perusahaan</span>
                            <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $serviceInquiry->company_type }}</span>
                        </div>
                    @endif
                    <div>
                        <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Nama Kontak</span>
                        <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $serviceInquiry->contact_person }}</span>
                    </div>
                    @if($serviceInquiry->position)
                        <div>
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Posisi</span>
                            <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $serviceInquiry->position }}</span>
                        </div>
                    @endif
                    <div>
                        <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Email</span>
                        <a href="mailto:{{ $serviceInquiry->email }}" style="font-size:0.82rem;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">{{ $serviceInquiry->email }}</a>
                    </div>
                    @if($serviceInquiry->phone)
                        <div>
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Telepon</span>
                            <a href="tel:{{ $serviceInquiry->phone }}" style="font-size:0.82rem;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">{{ $serviceInquiry->phone }}</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Business Information --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <i class="fas fa-briefcase" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Informasi Bisnis</span>
                </div>
                <div style="padding:18px 20px;display:flex;flex-direction:column;gap:12px">
                    <div>
                        <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Aktivitas Bisnis</span>
                        <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0;line-height:1.5">{{ $serviceInquiry->business_activity }}</p>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 24px">
                        @if($serviceInquiry->kbli_code)
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Kode KBLI</span>
                                <span style="font-size:0.85rem;color:var(--dark-text-primary);font-family:monospace;font-weight:600">{{ $serviceInquiry->kbli_code }}</span>
                            </div>
                        @endif
                        @if(isset($serviceInquiry->form_data['business_scale']))
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Skala Bisnis</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);text-transform:capitalize">{{ $serviceInquiry->form_data['business_scale'] }}</span>
                            </div>
                        @endif
                        @if($serviceInquiry->form_data['location_province'] ?? false)
                            <div style="{{ isset($kbli_code) || isset($serviceInquiry->form_data['business_scale']) ? 'grid-column:1/-1' : '' }}">
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Lokasi</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ $serviceInquiry->form_data['location_city'] ?? '' }}{{ ($serviceInquiry->form_data['location_city'] ?? '') && $serviceInquiry->form_data['location_province'] ? ', ' : '' }}{{ $serviceInquiry->form_data['location_province'] }}</span>
                            </div>
                        @endif
                        @if(isset($serviceInquiry->form_data['location_category']))
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Kategori Lokasi</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);text-transform:capitalize">{{ $serviceInquiry->form_data['location_category'] }}</span>
                            </div>
                        @endif
                        @if(isset($serviceInquiry->form_data['estimated_investment']))
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Estimasi Investasi</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ str_replace('_', ' ', $serviceInquiry->form_data['estimated_investment']) }}</span>
                            </div>
                        @endif
                        @if(isset($serviceInquiry->form_data['timeline']))
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Timeline</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary)">{{ str_replace('_', ' ', $serviceInquiry->form_data['timeline']) }}</span>
                            </div>
                        @endif
                    </div>
                    @if(isset($serviceInquiry->form_data['additional_notes']))
                        <div>
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Catatan Tambahan</span>
                            <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0;white-space:pre-wrap;line-height:1.5">{{ $serviceInquiry->form_data['additional_notes'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Status & Priority --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <i class="fas fa-tasks" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Status & Prioritas</span>
                </div>
                <div style="padding:18px 20px;display:flex;flex-direction:column;gap:16px">
                    {{-- Status & Priority forms --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <form method="POST" action="{{ route('admin.service-inquiries.update-status', $serviceInquiry) }}" style="display:flex;flex-direction:column;gap:6px">
                            @csrf @method('PATCH')
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary)">Status</span>
                            <div style="display:flex;gap:6px">
                                <select name="status" style="flex:1;padding:7px 10px;font-size:0.78rem;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);outline:none">
                                    @foreach(['new'=>'Baru','processing'=>'Diproses','analyzed'=>'Dianalisis','contacted'=>'Dihubungi','qualified'=>'Qualified','converted'=>'Konversi','registered'=>'Terdaftar','lost'=>'Lost'] as $v=>$l)
                                        <option value="{{ $v }}" {{ $serviceInquiry->status==$v ? 'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" style="padding:7px 14px;font-size:0.72rem;font-weight:700;color:#fff;background:var(--apple-blue);border:none;border-radius:10px;cursor:pointer;transition:opacity .15s" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">Update</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.service-inquiries.update-priority', $serviceInquiry) }}" style="display:flex;flex-direction:column;gap:6px">
                            @csrf @method('PATCH')
                            <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary)">Prioritas</span>
                            <div style="display:flex;gap:6px">
                                <select name="priority" style="flex:1;padding:7px 10px;font-size:0.78rem;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);outline:none">
                                    @foreach(['low'=>'Low','medium'=>'Medium','high'=>'High'] as $v=>$l)
                                        <option value="{{ $v }}" {{ $serviceInquiry->priority==$v ? 'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" style="padding:7px 14px;font-size:0.72rem;font-weight:700;color:#fff;background:var(--apple-blue);border:none;border-radius:10px;cursor:pointer;transition:opacity .15s" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">Update</button>
                            </div>
                        </form>
                    </div>

                    {{-- Additional info --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;padding-top:12px;border-top:1px solid var(--dark-separator)">
                        @if($serviceInquiry->last_contacted_at)
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Terakhir Dihubungi</span>
                                <span style="font-size:0.78rem;color:var(--dark-text-primary)">
                                    {{ $serviceInquiry->last_contacted_at->format('d M Y H:i') }}
                                    @if($serviceInquiry->contactedBy)
                                        <span style="color:var(--dark-text-secondary)">oleh {{ $serviceInquiry->contactedBy->name }}</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                        @if($serviceInquiry->source && $serviceInquiry->source !== 'landing_page')
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Sumber</span>
                                <span style="font-size:0.78rem;color:var(--dark-text-primary);text-transform:capitalize">{{ str_replace('_', ' ', $serviceInquiry->source) }}</span>
                            </div>
                        @endif
                        @if($serviceInquiry->converted_at)
                            <div style="grid-column:1/-1">
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Dikonversi Pada</span>
                                <span style="font-size:0.78rem;color:var(--dark-text-primary)">{{ $serviceInquiry->converted_at->format('d M Y H:i') }}</span>
                                <div style="display:flex;gap:12px;margin-top:4px">
                                    @if($serviceInquiry->client)
                                        <a href="{{ route('clients.show', $serviceInquiry->client) }}" style="font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Lihat Klien &rarr;</a>
                                    @endif
                                    @if($serviceInquiry->convertedToApplication)
                                        <a href="{{ route('projects.show', $serviceInquiry->convertedToApplication->project_id) }}" style="font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none;transition:opacity .15s" onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">Lihat Proyek &rarr;</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= RIGHT COLUMN ================= --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- AI Analysis --}}
            @if($serviceInquiry->ai_analysis)
                @php $analysis = $serviceInquiry->ai_analysis; @endphp
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                        <div style="display:flex;align-items:center;gap:8px">
                            <i class="fas fa-robot" style="color:var(--apple-blue);font-size:0.85rem"></i>
                            <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Hasil Analisis AI</span>
                        </div>
                        <a href="{{ route('landing.service-inquiry.result', $serviceInquiry->inquiry_number) }}" target="_blank" rel="noopener noreferrer"
                           style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:8px;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 25%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 15%,transparent)'">
                            <i class="fas fa-external-link-alt" style="font-size:0.65rem"></i>Lihat Hasil User
                        </a>
                    </div>
                    <div style="padding:18px 20px;display:flex;flex-direction:column;gap:18px">

                        {{-- Summary Stats --}}
                        @php
                            $hasStats = isset($analysis['total_estimated_cost']['grand_total']) || isset($analysis['total_estimated_timeline']) || (isset($analysis['estimated_timeline']) && is_string($analysis['estimated_timeline'])) || isset($analysis['estimated_timeline']['summary']) || isset($analysis['complexity_score']);
                        @endphp
                        @if($hasStats)
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px">
                                @if(isset($analysis['total_estimated_cost']['grand_total']))
                                    <div style="text-align:center;padding:12px 8px;background:var(--dark-bg-tertiary);border-radius:12px">
                                        <div style="font-size:0.6rem;font-weight:700;color:var(--dark-text-secondary);letter-spacing:.08em;text-transform:uppercase;margin-bottom:4px">Total Biaya</div>
                                        <div style="font-size:0.85rem;font-weight:800;color:var(--apple-green)">
                                            Rp{{ number_format($analysis['total_estimated_cost']['grand_total']['min'] / 1000000, 0) }}jt – Rp{{ number_format($analysis['total_estimated_cost']['grand_total']['max'] / 1000000, 0) }}jt
                                        </div>
                                    </div>
                                @endif
                                @php
                                    $tlText = $analysis['total_estimated_timeline'] ?? (is_string($analysis['estimated_timeline'] ?? null) ? $analysis['estimated_timeline'] : null) ?? ($analysis['estimated_timeline']['summary'] ?? null);
                                @endphp
                                @if($tlText)
                                    <div style="text-align:center;padding:12px 8px;background:var(--dark-bg-tertiary);border-radius:12px">
                                        <div style="font-size:0.6rem;font-weight:700;color:var(--dark-text-secondary);letter-spacing:.08em;text-transform:uppercase;margin-bottom:4px">Timeline</div>
                                        <div style="font-size:0.85rem;font-weight:800;color:var(--apple-blue)">{{ $tlText }}</div>
                                    </div>
                                @endif
                                @if(isset($analysis['complexity_score']))
                                    <div style="text-align:center;padding:12px 8px;background:var(--dark-bg-tertiary);border-radius:12px">
                                        <div style="font-size:0.6rem;font-weight:700;color:var(--dark-text-secondary);letter-spacing:.08em;text-transform:uppercase;margin-bottom:4px">Kompleksitas</div>
                                        <div style="font-size:0.85rem;font-weight:800;color:var(--apple-orange)">{{ $analysis['complexity_score'] }}/10</div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Model & Tokens --}}
                        @if(($analysis['ai_model_used'] ?? false) || ($analysis['ai_tokens_used'] ?? false))
                            <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:0.72rem;color:var(--dark-text-secondary);padding-top:12px;border-top:1px solid var(--dark-separator)">
                                @if($analysis['ai_model_used'] ?? false)
                                    <span style="display:inline-flex;align-items:center;gap:4px"><i class="fas fa-microchip"></i>Model: <strong style="color:var(--dark-text-primary)">{{ $analysis['ai_model_used'] }}</strong></span>
                                @endif
                                @if($analysis['ai_tokens_used'] ?? false)
                                    <span style="display:inline-flex;align-items:center;gap:4px"><i class="fas fa-tachometer-alt"></i>Token: <strong style="color:var(--dark-text-primary)">{{ number_format($analysis['ai_tokens_used']) }}</strong></span>
                                @endif
                                @if($analysis['ai_processing_time'] ?? false)
                                    <span style="display:inline-flex;align-items:center;gap:4px"><i class="fas fa-clock"></i>Waktu: <strong style="color:var(--dark-text-primary)">{{ number_format($analysis['ai_processing_time'] / 1000, 1) }}s</strong></span>
                                @endif
                            </div>
                        @endif

                        {{-- KBLI Suggestion --}}
                        @if(isset($analysis['kbli_suggestion']['code']))
                            <div style="padding:12px 14px;border-radius:12px;background:color-mix(in srgb,var(--apple-blue) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 20%,transparent)">
                                <span style="font-size:0.65rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:4px;letter-spacing:.05em">KBLI yang Disarankan</span>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span style="font-size:1rem;font-weight:800;font-family:monospace;color:var(--apple-blue)">{{ $analysis['kbli_suggestion']['code'] }}</span>
                                    @if(isset($analysis['kbli_suggestion']['description']))
                                        <span style="font-size:0.78rem;color:var(--dark-text-primary)">{{ $analysis['kbli_suggestion']['description'] }}</span>
                                    @endif
                                    @if(isset($analysis['kbli_suggestion']['confidence']))
                                        <span style="padding:2px 8px;border-radius:6px;font-size:0.65rem;font-weight:700;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary)">{{ $analysis['kbli_suggestion']['confidence'] }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Recommended Permits --}}
                        @if(isset($analysis['recommended_permits']) && count($analysis['recommended_permits']) > 0)
                            <div>
                                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:8px">Izin yang Direkomendasikan ({{ count($analysis['recommended_permits']) }})</span>
                                <div style="display:flex;flex-direction:column;gap:6px">
                                    @foreach($analysis['recommended_permits'] as $permit)
                                        @php
                                            $pm = $permit['priority'] ?? '';
                                            $bc = match($pm) {'critical'=>'var(--apple-red)','high'=>'var(--apple-orange)','medium'=>'var(--apple-blue)',default=>'var(--dark-separator)'};
                                            $bgc = match($pm) {'critical'=>'color-mix(in srgb,var(--apple-red) 5%,transparent)','high'=>'color-mix(in srgb,var(--apple-orange) 5%,transparent)','medium'=>'color-mix(in srgb,var(--apple-blue) 5%,transparent)',default=>'transparent'};
                                            $bcBadge = match($pm) {'critical'=>'color-mix(in srgb,var(--apple-red) 15%,transparent)','high'=>'color-mix(in srgb,var(--apple-orange) 15%,transparent)','medium'=>'color-mix(in srgb,var(--apple-blue) 15%,transparent)',default=>'var(--dark-bg-tertiary)'};
                                            $tcBadge = match($pm) {'critical'=>'var(--apple-red)','high'=>'var(--apple-orange)','medium'=>'var(--apple-blue)',default=>'var(--dark-text-secondary)'};
                                        @endphp
                                        <div style="padding:12px 14px;border-radius:12px;border:1px solid var(--dark-separator);border-left:3px solid {{ $bc }};background:{{ $bgc }}">
                                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:4px">
                                                <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">{{ $permit['name'] ?? 'N/A' }}</span>
                                                @if($pm)
                                                    <span style="padding:1px 8px;border-radius:20px;font-size:0.62rem;font-weight:700;border:1px solid {{ $bcBadge }};background:{{ $bcBadge }};color:{{ $tcBadge }};white-space:nowrap">{{ ucfirst($pm) }}</span>
                                                @endif
                                            </div>
                                            @if(isset($permit['description']))
                                                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 6px;line-height:1.4">{{ $permit['description'] }}</p>
                                            @endif
                                            @if(isset($permit['government_fee']['min']) || isset($permit['consultant_fee']['min']) || isset($permit['total_cost_range']) || isset($permit['estimated_timeline']))
                                                <div style="display:flex;flex-wrap:wrap;gap:8px;font-size:0.7rem;color:var(--dark-text-secondary)">
                                                    @if(isset($permit['government_fee']['min']))
                                                        <span>Pemerintah: <strong style="color:var(--apple-green)">Rp{{ number_format($permit['government_fee']['min'] / 1000, 0) }}K</strong></span>
                                                    @endif
                                                    @if(isset($permit['consultant_fee']['min']))
                                                        <span>Konsultan: <strong style="color:var(--apple-blue)">Rp{{ number_format($permit['consultant_fee']['min'] / 1000, 0) }}K</strong></span>
                                                    @endif
                                                    @if(isset($permit['total_cost_range']))
                                                        <span>Total: <strong style="color:var(--dark-text-primary)">{{ $permit['total_cost_range'] }}</strong></span>
                                                    @endif
                                                    @if(isset($permit['estimated_timeline']))
                                                        <span><i class="far fa-clock" style="font-size:0.65rem"></i> {{ $permit['estimated_timeline'] }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Risk Assessment --}}
                        @if(isset($analysis['risk_assessment']))
                            <div>
                                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:6px">Penilaian Risiko</span>
                                @if(isset($analysis['risk_assessment']['level']))
                                    @php
                                        $rl = $analysis['risk_assessment']['level'];
                                        $rc = match($rl) {'low'=>'var(--apple-green)','medium'=>'var(--apple-orange)','high'=>'var(--apple-red)',default=>'var(--dark-text-secondary)'};
                                    @endphp
                                    <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:20px;font-size:0.7rem;font-weight:700;border:1px solid color-mix(in srgb,{{ $rc }} 25%,transparent);background:color-mix(in srgb,{{ $rc }} 12%,transparent);color:{{ $rc }};margin-bottom:6px">{{ ucfirst($rl) }}</span>
                                @endif
                                @if(isset($analysis['risk_assessment']['factors']) && count($analysis['risk_assessment']['factors']) > 0)
                                    <ul style="margin:4px 0 0;padding:0;list-style:none;display:flex;flex-direction:column;gap:4px">
                                        @foreach($analysis['risk_assessment']['factors'] as $factor)
                                            <li style="display:flex;align-items:flex-start;gap:6px;font-size:0.75rem;color:var(--dark-text-secondary)">
                                                <i class="fas fa-exclamation-triangle" style="color:var(--apple-yellow);font-size:0.65rem;margin-top:2px;flex-shrink:0"></i>
                                                <span>{{ $factor }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @elseif(isset($analysis['risk_factors']) && count($analysis['risk_factors']) > 0)
                            <div>
                                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:6px">Faktor Risiko</span>
                                <ul style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:4px">
                                    @foreach($analysis['risk_factors'] as $risk)
                                        <li style="display:flex;align-items:flex-start;gap:6px;font-size:0.75rem;color:var(--dark-text-secondary)">
                                            <i class="fas fa-exclamation-triangle" style="color:var(--apple-yellow);font-size:0.65rem;margin-top:2px;flex-shrink:0"></i>
                                            <span>{{ $risk }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Next Steps --}}
                        @if(isset($analysis['next_steps']) && count($analysis['next_steps']) > 0)
                            <div>
                                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:6px">Langkah Selanjutnya</span>
                                <ol style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px">
                                    @foreach($analysis['next_steps'] as $step)
                                        <li style="display:flex;align-items:flex-start;gap:8px;font-size:0.78rem;color:var(--dark-text-primary)">
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);font-size:0.6rem;font-weight:800;flex-shrink:0;margin-top:1px">{{ $loop->iteration }}</span>
                                            <span>{{ $step }}</span>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden;padding:40px 20px;text-align:center">
                    <i class="fas fa-robot" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.2;display:block;margin-bottom:12px"></i>
                    <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 12px">Analisis AI belum tersedia</p>
                    <a href="{{ route('landing.service-inquiry.result', $serviceInquiry->inquiry_number) }}" target="_blank" rel="noopener noreferrer"
                       style="display:inline-flex;align-items:center;gap:5px;padding:7px 14px;font-size:0.78rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:10px;text-decoration:none;transition:all .15s"
                       onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 22%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 12%,transparent)'">
                        <i class="fas fa-external-link-alt" style="font-size:0.7rem"></i>Buka Halaman Hasil
                    </a>
                </div>
            @endif

            {{-- Admin Notes --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <i class="fas fa-sticky-note" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Catatan Admin</span>
                    @if($serviceInquiry->admin_notes)
                        <span style="margin-left:auto;font-size:0.72rem;color:var(--dark-text-secondary)">{{ count(array_filter(explode("\n\n", $serviceInquiry->admin_notes))) }} catatan</span>
                    @endif
                </div>
                <div style="padding:18px 20px">
                    <form method="POST" action="{{ route('admin.service-inquiries.add-note', $serviceInquiry) }}" style="display:flex;flex-direction:column;gap:8px">
                        @csrf
                        <textarea name="note" rows="3" placeholder="Tambahkan catatan..."
                                  style="width:100%;padding:10px 14px;font-size:0.82rem;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;color:var(--dark-text-primary);outline:none;resize:vertical;box-sizing:border-box;transition:border-color .15s"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                        <button type="submit" style="width:100%;padding:9px 16px;font-size:0.78rem;font-weight:700;color:#fff;background:var(--apple-blue);border:none;border-radius:10px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:6px;transition:opacity .15s" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-plus" style="font-size:0.7rem"></i>Tambah Catatan
                        </button>
                    </form>
                    @if($serviceInquiry->admin_notes)
                        <div style="display:flex;flex-direction:column;gap:8px;margin-top:16px">
                            @foreach(array_filter(explode("\n\n", $serviceInquiry->admin_notes)) as $note)
                                <div style="padding:12px 14px;border-radius:12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator)">
                                    <p style="font-size:0.78rem;color:var(--dark-text-primary);margin:0;white-space:pre-wrap;line-height:1.5">{{ $note }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align:center;padding:24px 0">
                            <i class="far fa-file-alt" style="font-size:1.2rem;color:var(--dark-text-secondary);opacity:.2;display:block;margin-bottom:8px"></i>
                            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Belum ada catatan</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Technical Info --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Informasi Teknis</span>
                </div>
                <div style="padding:14px 20px;display:flex;flex-direction:column;gap:0">
                    @php $techItems = [ ['label'=>'Inquiry Number',   'value'=>$serviceInquiry->inquiry_number, 'mono'=>true],
                                       ['label'=>'IP Address',       'value'=>$serviceInquiry->ip_address ?? '-', 'mono'=>true],
                                       ['label'=>'User Agent',       'value'=>$serviceInquiry->user_agent ?? '-', 'mono'=>true, 'truncate'=>true],
                                     ];
                        if(isset($serviceInquiry->form_data['utm_source']))   $techItems[] = ['label'=>'UTM Source',   'value'=>$serviceInquiry->form_data['utm_source']];
                        if(isset($serviceInquiry->form_data['utm_medium']))   $techItems[] = ['label'=>'UTM Medium',   'value'=>$serviceInquiry->form_data['utm_medium']];
                        if(isset($serviceInquiry->form_data['utm_campaign'])) $techItems[] = ['label'=>'UTM Campaign',   'value'=>$serviceInquiry->form_data['utm_campaign']];
                    @endphp
                    @foreach($techItems as $i => $item)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;{{ $i > 0 ? 'border-top:1px solid var(--dark-separator)' : '' }}">
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary)">{{ $item['label'] }}</span>
                            @if(isset($item['truncate']) && $item['truncate'])
                                <span style="font-size:0.72rem;{{ isset($item['mono']) && $item['mono'] ? 'font-family:monospace;' : '' }}color:var(--dark-text-primary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:right" title="{{ $item['value'] }}">{{ $item['value'] }}</span>
                            @else
                                <span style="font-size:0.72rem;{{ isset($item['mono']) && $item['mono'] ? 'font-family:monospace;font-weight:600;' : '' }}color:var(--dark-text-primary)">{{ $item['value'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Zoning Info (RTRW) --}}
            @if($serviceInquiry->shapefileProject && ($serviceInquiry->shapefileProject->rtrw_zona || $serviceInquiry->shapefileProject->rtrw_perda))
                <div style="background:var(--dark-bg-secondary);border:1px solid color-mix(in srgb,var(--apple-green) 30%,var(--dark-separator));border-radius:18px;overflow:hidden">
                    <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                        <i class="fas fa-draw-polygon" style="color:var(--apple-green);font-size:0.85rem"></i>
                        <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Informasi Zonasi (RTRW)</span>
                    </div>
                    <div style="padding:18px 20px;display:flex;flex-direction:column;gap:12px">
                        @if($serviceInquiry->shapefileProject->rtrw_zona)
                            <div>
                                <span style="font-size:0.68rem;font-weight:700;color:var(--apple-green);display:inline-flex;align-items:center;gap:4px;margin-bottom:2px"><i class="fas fa-tag"></i>Zona RTRW</span>
                                <p style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $serviceInquiry->shapefileProject->rtrw_zona }}</p>
                            </div>
                        @endif
                        @if($serviceInquiry->shapefileProject->rtrw_perda)
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:4px;margin-bottom:2px"><i class="fas fa-gavel"></i>Dasar Hukum (Perda)</span>
                                <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">{{ $serviceInquiry->shapefileProject->rtrw_perda }}</p>
                            </div>
                        @endif
                        @if($serviceInquiry->shapefileProject->rtrw_remark)
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:4px;margin-bottom:2px"><i class="fas fa-sticky-note"></i>Catatan</span>
                                <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">{{ $serviceInquiry->shapefileProject->rtrw_remark }}</p>
                            </div>
                        @endif
                        <div style="padding:10px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 20%,transparent)">
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;display:inline-flex;align-items:flex-start;gap:6px">
                                <i class="fas fa-info-circle" style="color:var(--apple-green);margin-top:1px;flex-shrink:0"></i>
                                Data zonasi bersumber dari GISTARU ATR/BPN. Verifikasi data ke instansi terkait untuk memastikan kesesuaian peruntukan lahan.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Shapefile Project (no RTRW) --}}
            @if($serviceInquiry->shapefileProject && !$serviceInquiry->shapefileProject->rtrw_zona && !$serviceInquiry->shapefileProject->rtrw_perda)
                @php $coords = $serviceInquiry->shapefileProject->geojson['coordinates'][0] ?? []; @endphp
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">
                    <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                        <i class="fas fa-draw-polygon" style="color:var(--apple-green);font-size:0.85rem"></i>
                        <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">Proyek SHP</span>
                    </div>
                    <div style="padding:18px 20px">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Nama Proyek</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);font-weight:600;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $serviceInquiry->shapefileProject->name }}">{{ $serviceInquiry->shapefileProject->name }}</span>
                            </div>
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Jumlah Titik</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);font-family:monospace">{{ count($coords) }}</span>
                            </div>
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Luas (m²)</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);font-family:monospace">{{ number_format($serviceInquiry->shapefileProject->area_m2, 2) }}</span>
                            </div>
                            <div>
                                <span style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:2px">Luas (Ha)</span>
                                <span style="font-size:0.82rem;color:var(--dark-text-primary);font-family:monospace">{{ number_format($serviceInquiry->shapefileProject->area_ha, 6) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Convert to Client Modal --}}
<div id="convertModal" style="display:none;position:fixed;inset:0;z-index:100;background:rgba(0,0,0,0.75);align-items:center;justify-content:center;padding:16px">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;max-width:480px;width:100%;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid var(--dark-separator)">
            <div>
                <span style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);display:block">Lead Management</span>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Konversi ke Klien</h3>
            </div>
            <button onclick="document.getElementById('convertModal').style.display='none'"
                    style="padding:8px;border-radius:10px;background:none;border:none;color:var(--dark-text-secondary);cursor:pointer;transition:all .15s"
                    onmouseover="this.style.background='var(--dark-bg-tertiary)';this.style.color='var(--dark-text-primary)'"
                    onmouseout="this.style.background='transparent';this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-times" style="font-size:0.85rem"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.service-inquiries.convert', $serviceInquiry) }}" style="padding:20px 24px">
            @csrf
            <label style="display:flex;align-items:center;gap:8px;margin-bottom:16px;cursor:pointer">
                <input type="checkbox" name="create_client_account" value="1" checked id="createAccountCheckbox"
                       style="width:16px;height:16px;border-radius:4px;accent-color:var(--brand-gold);cursor:pointer">
                <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary)">Buat akun klien baru</span>
            </label>
            <div id="passwordField" style="margin-bottom:16px">
                <label style="font-size:0.68rem;font-weight:700;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Password untuk Akun Klien</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter"
                       style="width:100%;padding:10px 14px;border-radius:12px;font-size:0.82rem;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);outline:none;box-sizing:border-box;transition:border-color .15s"
                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
            </div>
            <div style="padding:12px 14px;border-radius:12px;margin-bottom:16px;background:color-mix(in srgb,var(--brand-gold) 10%,transparent);border:1px solid color-mix(in srgb,var(--brand-gold) 25%,transparent)">
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0;display:inline-flex;align-items:flex-start;gap:6px">
                    <i class="fas fa-info-circle" style="color:var(--brand-gold);margin-top:1px;flex-shrink:0"></i>
                    Akan dibuat klien baru dan aplikasi permit dari data inquiry ini.
                </p>
            </div>
            <div style="display:flex;gap:8px">
                <button type="button" onclick="document.getElementById('convertModal').style.display='none'"
                        style="flex:1;padding:10px 16px;border-radius:12px;font-size:0.82rem;font-weight:700;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);border:1px solid var(--dark-separator);cursor:pointer;transition:all .15s"
                        onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='var(--dark-bg-tertiary)'">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1;padding:10px 16px;border-radius:12px;font-size:0.82rem;font-weight:700;color:#fff;background:var(--brand-gold);border:none;cursor:pointer;transition:all .2s"
                        onmouseover="this.style.background='#a67c00';this.style.boxShadow='0 4px 12px rgba(184,134,11,0.35)'" onmouseout="this.style.background='var(--brand-gold)';this.style.boxShadow='none'">
                    Konversi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Form --}}
<form id="deleteForm" method="POST" action="{{ route('admin.service-inquiries.destroy', $serviceInquiry) }}" style="display:none">
    @csrf @method('DELETE')
</form>

<script>
document.getElementById('createAccountCheckbox')?.addEventListener('change', function() {
    var pw = document.getElementById('passwordField');
    if (pw) pw.style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection