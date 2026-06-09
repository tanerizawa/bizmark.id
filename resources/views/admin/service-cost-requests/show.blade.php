@extends('layouts.admin')

@section('title', 'Detail Permohonan - ' . $serviceRequest->request_number)

@section('content')
<div class="space-y-4" style="max-width:1200px;margin:0 auto;padding:0 4px">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div style="display:flex;align-items:center;gap:14px">
            <a href="{{ route('admin.leads.index', ['tab' => 'service-cost-requests']) }}"
               style="display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);flex-shrink:0"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:.85rem"></i>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <h1 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:0;font-family:monospace">{{ $serviceRequest->request_number }}</h1>
                    @php
                        $statusConfig = [
                            'pending'   => ['color'=>'var(--apple-yellow)', 'label'=>'Pending'],
                            'reviewing' => ['color'=>'var(--apple-blue)',   'label'=>'Reviewing'],
                            'quoted'    => ['color'=>'var(--apple-indigo)', 'label'=>'Quoted'],
                            'accepted'  => ['color'=>'var(--apple-green)',  'label'=>'Accepted'],
                            'rejected'  => ['color'=>'var(--apple-red)',    'label'=>'Rejected'],
                            'cancelled' => ['color'=>'var(--dark-text-tertiary)', 'label'=>'Cancelled'],
                        ];
                        $sc = $statusConfig[$serviceRequest->status] ?? ['color'=>'var(--dark-text-secondary)', 'label'=>ucfirst($serviceRequest->status)];
                    @endphp
                    <span style="padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:600;background:color-mix(in srgb,{{ $sc['color'] }} 15%,transparent);color:{{ $sc['color'] }}">{{ $sc['label'] }}</span>
                </div>
                <p style="font-size:.78rem;color:var(--dark-text-secondary);margin:3px 0 0;display:flex;align-items:center;gap:10px">
                    <span><i class="fas fa-calendar-alt" style="margin-right:4px"></i>{{ $serviceRequest->created_at->format('d M Y H:i') }}</span>
                    <span><i class="fas fa-tag" style="margin-right:4px"></i>{{ $serviceRequest->service_category ?? 'Layanan' }}</span>
                </p>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            @if($serviceRequest->status !== 'cancelled')
                <button onclick="document.getElementById('archiveModal').classList.remove('hidden')"
                        style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid var(--dark-separator);border-radius:10px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.82rem;font-weight:600;cursor:pointer"
                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-archive" style="font-size:.75rem"></i>Arsip
                </button>
            @endif
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid var(--apple-green);border-radius:12px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
                <span style="font-size:.85rem;font-weight:500;color:var(--apple-green)">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;color:var(--apple-green);opacity:.6;cursor:pointer;padding:0"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div style="background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid var(--apple-red);border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
            <i class="fas fa-exclamation-circle" style="color:var(--apple-red)"></i>
            <span style="font-size:.85rem;font-weight:500;color:var(--apple-red)">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div style="background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid var(--apple-red);border-radius:12px;padding:12px 16px">
            <div style="display:flex;align-items:flex-start;gap:10px">
                <i class="fas fa-exclamation-circle" style="color:var(--apple-red);margin-top:2px"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <p style="font-size:.83rem;color:var(--apple-red);margin:0 0 3px">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- KPI Strip --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-secondary);margin:0 0 4px">Status</p>
            <p style="font-size:.88rem;font-weight:700;color:{{ $sc['color'] }};margin:0">{{ $sc['label'] }}</p>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-secondary);margin:0 0 4px">Nilai Quote</p>
            <p style="font-size:.88rem;font-weight:700;color:{{ $serviceRequest->quoted_price ? 'var(--apple-green)' : 'var(--dark-text-tertiary)' }};margin:0">
                {{ $serviceRequest->quoted_price ? 'Rp '.number_format($serviceRequest->quoted_price,0,',','.') : 'Belum tersedia' }}
            </p>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-secondary);margin:0 0 4px">Email</p>
            <p style="font-size:.78rem;font-weight:600;color:var(--dark-text-primary);margin:0;word-break:break-all">{{ $serviceRequest->email }}</p>
        </div>
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px">
            <p style="font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-secondary);margin:0 0 4px">Dikirim Pada</p>
            <p style="font-size:.78rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->responded_at ? $serviceRequest->responded_at->format('d M Y H:i') : 'Belum dikirim' }}</p>
        </div>
    </div>

    {{-- 2-col Layout --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:start">

        {{-- LEFT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Informasi Kontak --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-user" style="font-size:.82rem;color:var(--apple-blue)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Kontak</h3>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
                    @php
                        $contactFields = [
                            ['label'=>'Tipe Pemohon', 'value'=> $serviceRequest->applicant_type === 'badan' ? 'Badan Usaha' : 'Perorangan'],
                            ['label'=>'Nama Pemohon', 'value'=> $serviceRequest->display_name],
                        ];
                        if ($serviceRequest->applicant_type === 'badan' && $serviceRequest->company_name)
                            $contactFields[] = ['label'=>'Nama Perusahaan', 'value'=> $serviceRequest->company_name];
                    @endphp
                    @foreach($contactFields as $f)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">{{ $f['label'] }}</p>
                            <p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ $f['value'] }}</p>
                        </div>
                    @endforeach
                    <div>
                        <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Email</p>
                        <a href="mailto:{{ $serviceRequest->email }}" style="font-size:.85rem;color:var(--apple-blue)">{{ $serviceRequest->email }}</a>
                    </div>
                    <div>
                        <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Telepon</p>
                        <a href="tel:{{ $serviceRequest->phone }}" style="font-size:.85rem;color:var(--apple-blue)">{{ $serviceRequest->phone }}</a>
                    </div>
                    @if($serviceRequest->address || $serviceRequest->city || $serviceRequest->province)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Alamat</p>
                            <p style="font-size:.85rem;color:var(--dark-text-primary);margin:0;line-height:1.5">
                                @if($serviceRequest->address){{ $serviceRequest->address }}@endif
                                @if($serviceRequest->city || $serviceRequest->province)
                                    @if($serviceRequest->address)<br>@endif
                                    {{ implode(', ', array_filter([$serviceRequest->city, $serviceRequest->province])) }}
                                @endif
                            </p>
                        </div>
                    @endif
                    @if($serviceRequest->applicant_type === 'badan')
                        @if($serviceRequest->npwp)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">NPWP</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0;font-family:monospace">{{ $serviceRequest->npwp }}</p></div>
                        @endif
                        @if($serviceRequest->nib)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">NIB</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0;font-family:monospace">{{ $serviceRequest->nib }}</p></div>
                        @endif
                        @if($serviceRequest->business_type)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Jenis Badan Usaha</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->business_type_label ?? $serviceRequest->business_type }}</p></div>
                        @endif
                        @if($serviceRequest->business_sector)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Bidang Usaha</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->business_sector }}</p></div>
                        @endif
                        @if($serviceRequest->pic_name)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Contact Person (PIC)</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->pic_name }}{{ $serviceRequest->pic_position ? ' — '.$serviceRequest->pic_position : '' }}</p></div>
                        @endif
                    @else
                        @if($serviceRequest->nik)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">NIK</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0;font-family:monospace">{{ $serviceRequest->nik }}</p></div>
                        @endif
                        @if($serviceRequest->occupation)
                            <div><p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Pekerjaan</p><p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->occupation }}</p></div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Informasi Layanan --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-briefcase" style="font-size:.82rem;color:var(--apple-orange)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Layanan</h3>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
                    <div>
                        <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Kategori Layanan</p>
                        <p style="font-size:.85rem;color:var(--dark-text-primary);margin:0">{{ \App\Models\ServiceCostRequest::getServiceCategories()[$serviceRequest->service_category] ?? $serviceRequest->service_category }}</p>
                    </div>
                    @if(!empty($serviceRequest->services_requested) && is_array($serviceRequest->services_requested))
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 6px">Layanan Dipilih</p>
                            @php
                                $servicesByCategory = \App\Models\ServiceCostRequest::getServicesByCategory();
                                $serviceMap = $servicesByCategory[$serviceRequest->service_category] ?? [];
                            @endphp
                            <div style="display:flex;flex-direction:column;gap:4px">
                                @foreach($serviceRequest->services_requested as $service)
                                    <div style="display:flex;align-items:flex-start;gap:8px">
                                        <i class="fas fa-check" style="font-size:.7rem;color:var(--apple-green);margin-top:3px;flex-shrink:0"></i>
                                        <span style="font-size:.84rem;color:var(--dark-text-primary)">{{ $serviceMap[$service] ?? $service }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($serviceRequest->ai_letter_body || $serviceRequest->project_description)
                        <div>
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px">
                                <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0">Deskripsi Kebutuhan</p>
                                @if($serviceRequest->ai_letter_body)
                                    <span style="font-size:.6rem;padding:1px 6px;border-radius:4px;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 35%,transparent);color:var(--apple-blue)">Generated by AI</span>
                                @endif
                            </div>
                            @if($serviceRequest->ai_letter_body)
                                <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0;white-space:pre-wrap;line-height:1.6">{{ $serviceRequest->ai_letter_body }}</p>
                                @if($serviceRequest->project_description)
                                    <details style="margin-top:8px">
                                        <summary style="font-size:.7rem;color:var(--dark-text-secondary);cursor:pointer">Lihat input asli pemohon</summary>
                                        <p style="font-size:.8rem;color:var(--dark-text-secondary);margin:4px 0 0;white-space:pre-wrap;line-height:1.5;padding:8px;border-radius:6px;background:var(--dark-bg-tertiary)">{{ $serviceRequest->project_description }}</p>
                                    </details>
                                @endif
                            @else
                                <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0;white-space:pre-wrap;line-height:1.5">{{ $serviceRequest->project_description }}</p>
                            @endif
                        </div>
                    @endif
                    @if($serviceRequest->project_location)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Lokasi Proyek</p>
                            <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->project_location }}</p>
                        </div>
                    @endif
                    @if($serviceRequest->estimated_budget)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Estimasi Anggaran</p>
                            <p style="font-size:.84rem;color:var(--apple-teal);font-weight:600;margin:0">{{ $serviceRequest->formatted_budget ?? ('Rp '.number_format($serviceRequest->estimated_budget,0,',','.')) }}</p>
                        </div>
                    @endif
                    @if($serviceRequest->timeline_expectation)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Ekspektasi Timeline</p>
                            <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->timeline_expectation }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quote Management --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-receipt" style="font-size:.82rem;color:var(--apple-green)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Manajemen Quote</h3>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:16px">
                    @if($serviceRequest->quoted_at)
                        @php
                            $defaultEmailSubject = $serviceRequest->quote_details['email_subject']
                                ?? ('Penawaran Jasa - ' . $serviceRequest->request_number . ' - Bizmark.ID');
                            $defaultEmailBody = $serviceRequest->quote_details['email_body']
                                ?? ('Yth. Bapak/Ibu ' . $serviceRequest->display_name . "\n\n" .
                                    'Terima kasih atas kepercayaan Anda kepada Bizmark.ID. Bersama ini kami sampaikan penawaran jasa untuk permohonan ' . $serviceRequest->request_number . '. ' .
                                    'Nilai penawaran saat ini sebesar Rp ' . number_format((float) $serviceRequest->quoted_price, 0, ',', '.') .
                                    ($serviceRequest->quoted_timeline ? ' dengan estimasi timeline ' . $serviceRequest->quoted_timeline . '.' : '.') . "\n\n" .
                                    'Apabila diperlukan penyesuaian ruang lingkup atau klarifikasi tambahan, silakan balas email ini dan tim kami akan menindaklanjuti.\n\n' .
                                    'Hormat kami,\nTim Konsultan\ninfo@bizmark.id');
                            $defaultEmailHtmlBody = $serviceRequest->quote_details['email_html_body'] ?? '';
                            $signatureMeta = $serviceRequest->quote_details['digital_signature'] ?? [];
                        @endphp

                        {{-- Quote Active Card --}}
                        <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-indigo) 25%,var(--dark-bg-secondary)),var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-indigo) 35%,var(--dark-separator));border-radius:12px;padding:16px">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap">
                                <div>
                                    <p style="font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--dark-text-secondary);margin:0 0 4px">Quote Aktif</p>
                                    <p style="font-size:1.6rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px">Rp {{ number_format($serviceRequest->quoted_price,0,',','.') }}</p>
                                    <p style="font-size:.72rem;color:var(--dark-text-secondary);margin:0">Dikutip {{ $serviceRequest->quoted_at->format('d M Y H:i') }}</p>
                                </div>
                                <form method="POST" action="{{ route('admin.service-cost-requests.regenerate-content', $serviceRequest->request_number) }}" style="display:flex;flex-direction:column;gap:6px;min-width:200px">
                                    @csrf
                                    <textarea name="regen_notes" rows="2"
                                              placeholder="Arahan regenerate (opsional)"
                                              style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:6px 10px;font-size:.78rem;resize:none;width:100%"></textarea>
                                    <button type="submit"
                                            style="background:var(--apple-indigo);color:#fff;border:none;border-radius:8px;padding:7px 12px;font-size:.78rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px"
                                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-rotate-right"></i>Regenerate AI
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($serviceRequest->quoted_timeline)
                            <div>
                                <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Timeline Proses</p>
                                <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0">{{ $serviceRequest->quoted_timeline }}</p>
                            </div>
                        @endif

                        @if(!empty($serviceRequest->quote_details['offer_text']))
                            <div>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                                    <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0">Naskah Penawaran Formal</p>
                                    @if(!empty($serviceRequest->quote_details['generated_by_ai']))
                                        <span style="font-size:.68rem;padding:2px 8px;border-radius:20px;border:1px solid var(--apple-indigo);color:var(--apple-indigo)">AI Generated</span>
                                    @endif
                                </div>
                                <textarea id="offerText" rows="8" readonly
                                          style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:10px 12px;font-size:.82rem;resize:vertical">{{ $serviceRequest->quote_details['offer_text'] }}</textarea>
                                <button type="button" onclick="copyField('offerText')"
                                        style="margin-top:6px;padding:6px 14px;border:1px solid var(--dark-separator);border-radius:8px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.78rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                    <i class="fas fa-copy"></i>Copy Naskah
                                </button>
                            </div>
                        @endif

                        @if($defaultEmailHtmlBody !== '')
                            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                                    <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0">Preview Email (Rich Text)</p>
                                    <span style="font-size:.65rem;color:var(--apple-teal)">HTML</span>
                                </div>
                                <div style="background:#fff;color:#0f172a;border-radius:6px;padding:12px;font-size:.82rem;max-height:240px;overflow-y:auto;border:1px solid #dbe2ef">
                                    {!! $defaultEmailHtmlBody !!}
                                </div>
                            </div>
                        @endif

                        @if(!empty($signatureMeta))
                            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                                <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 8px">Digital Signature</p>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:.78rem">
                                    <div><span style="color:var(--dark-text-secondary)">Signer: </span><span style="color:var(--dark-text-primary)">{{ $signatureMeta['signer_name'] ?? 'Tim Konsultan' }}</span></div>
                                    <div><span style="color:var(--dark-text-secondary)">Issued: </span><span style="color:var(--dark-text-primary)">{{ $signatureMeta['issued_at'] ?? '-' }}</span></div>
                                    <div style="grid-column:span 2"><span style="color:var(--dark-text-secondary)">Signature ID: </span><span style="color:var(--dark-text-primary);font-family:monospace">{{ $signatureMeta['signature_id'] ?? '-' }}</span></div>
                                    <div style="grid-column:span 2"><span style="color:var(--dark-text-secondary)">Hash: </span><span style="color:var(--dark-text-primary);font-family:monospace;font-size:.7rem;word-break:break-all">{{ $signatureMeta['signature_hash'] ?? '-' }}</span></div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($serviceRequest->quote_details['attachments']) && is_array($serviceRequest->quote_details['attachments']))
                            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px">
                                <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 8px">Lampiran Terkirim</p>
                                <div style="display:flex;flex-direction:column;gap:6px">
                                    @foreach($serviceRequest->quote_details['attachments'] as $att)
                                        <div style="background:var(--dark-bg);border:1px solid var(--dark-separator);border-radius:8px;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px">
                                            <div style="flex:1;min-width:0">
                                                <a href="{{ asset('storage/'.$att['path']) }}" target="_blank" style="font-size:.82rem;color:var(--apple-blue);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $att['name'] }}</a>
                                                <p style="font-size:.7rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ number_format(($att['size'] ?? 0)/1024,2) }} KB · {{ $att['uploaded_at'] ?? '-' }}</p>
                                            </div>
                                            <a href="{{ asset('storage/'.$att['path']) }}" download
                                               style="flex-shrink:0;padding:5px 10px;border:1px solid var(--dark-separator);border-radius:6px;font-size:.75rem;color:var(--dark-text-secondary)"
                                               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Download</a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Send Email Form --}}
                        <form method="POST" action="{{ route('admin.service-cost-requests.send-email', $serviceRequest->request_number) }}"
                              enctype="multipart/form-data"
                              style="padding-top:14px;border-top:1px solid var(--dark-separator);display:flex;flex-direction:column;gap:10px">
                            @csrf
                            <p style="font-size:.78rem;font-weight:600;color:var(--dark-text-secondary);margin:0">Kirim Email Manual</p>
                            <div>
                                <label style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Subject</label>
                                <input id="emailSubject" name="email_subject" type="text" value="{{ $defaultEmailSubject }}"
                                       style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:.83rem"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                                <label style="display:flex;align-items:center;gap:6px;font-size:.82rem;color:var(--dark-text-primary);cursor:pointer">
                                    <input id="useAIGenerated" type="checkbox" checked>
                                    Gunakan konten AI
                                </label>
                                <button type="button" id="regenBtn"
                                        style="padding:5px 12px;border:1px solid var(--dark-separator);border-radius:7px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.78rem;font-weight:600;cursor:pointer"
                                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                    Regenerate AI
                                </button>
                            </div>
                            <div>
                                <label style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Body</label>
                                <textarea id="emailBody" name="email_body" rows="10"
                                          style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:.82rem;resize:vertical"
                                          onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ $defaultEmailBody }}</textarea>
                            </div>
                            <div>
                                <label style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:4px">Lampiran (opsional)</label>
                                <input type="file" name="attachments[]" multiple style="font-size:.82rem;color:var(--dark-text-secondary)">
                                <p style="font-size:.7rem;color:var(--dark-text-tertiary);margin:4px 0 0">PDF, DOC, DOCX, JPG, PNG · Maks 5MB/file</p>
                            </div>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <button type="button" onclick="copyField('emailSubject')"
                                        style="padding:7px 14px;border:1px solid var(--dark-separator);border-radius:8px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.78rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                    <i class="fas fa-copy"></i>Copy Subject
                                </button>
                                <button type="button" onclick="copyField('emailBody')"
                                        style="padding:7px 14px;border:1px solid var(--dark-separator);border-radius:8px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.78rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                    <i class="fas fa-copy"></i>Copy Body
                                </button>
                                <button type="submit"
                                        style="padding:7px 16px;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;font-size:.82rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-paper-plane"></i>Kirim via info@bizmark.id
                                </button>
                            </div>
                            <p style="font-size:.72rem;color:var(--dark-text-tertiary);margin:0">Email dikirim melalui <strong style="color:var(--dark-text-secondary)">Tim Konsultan &lt;info@bizmark.id&gt;</strong></p>
                        </form>
                    @else
                        {{-- Form Buat Quote --}}
                        <form method="POST" action="{{ route('admin.service-cost-requests.generate-quote', $serviceRequest->request_number) }}"
                              style="display:flex;flex-direction:column;gap:12px">
                            @csrf
                            <div>
                                <label style="font-size:.78rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                    Estimasi Biaya (Rp) <span style="color:var(--apple-red)">*</span>
                                </label>
                                <input type="number" name="quoted_price" placeholder="Contoh: 5000000"
                                       min="0" step="1000" required
                                       style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:9px 12px;font-size:.85rem"
                                       onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                            <div>
                                <label style="font-size:.78rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Timeline Proses</label>
                                <input type="text" name="quoted_timeline" placeholder="Contoh: 5-7 hari kerja"
                                       style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:9px 12px;font-size:.85rem"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            </div>
                            <div>
                                <label style="font-size:.78rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Catatan Quote</label>
                                <textarea name="quote_notes" rows="3" placeholder="Catatan tambahan untuk quote..."
                                          style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:9px 12px;font-size:.84rem;resize:vertical"
                                          onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                            </div>
                            <label style="display:flex;align-items:center;gap:8px;font-size:.84rem;color:var(--dark-text-primary);cursor:pointer">
                                <input type="checkbox" name="generate_ai_content" value="1" checked>
                                Generate naskah penawaran + template email formal dengan AI
                            </label>
                            <button type="submit"
                                    style="width:100%;padding:10px;background:var(--apple-green);color:#fff;border:none;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-file-invoice-dollar"></i>Buat Quote
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Status Management --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-tasks" style="font-size:.82rem;color:var(--apple-purple)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Status Management</h3>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:14px">
                    <form method="POST" action="{{ route('admin.service-cost-requests.update-status', $serviceRequest->request_number) }}">
                        @csrf
                        @method('PATCH')
                        <p style="font-size:.78rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 6px">Update Status</p>
                        <div style="display:flex;gap:8px">
                            <select name="status"
                                    style="flex:1;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:8px 12px;font-size:.84rem">
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $serviceRequest->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    style="padding:8px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;font-size:.83rem;font-weight:600;cursor:pointer"
                                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                Update
                            </button>
                        </div>
                    </form>

                    @if($serviceRequest->reviewed_at)
                        <div>
                            <p style="font-size:.68rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 3px">Direview Pada</p>
                            <p style="font-size:.84rem;color:var(--dark-text-primary);margin:0">
                                {{ $serviceRequest->reviewed_at->format('d M Y H:i') }}
                                @if($serviceRequest->reviewer)
                                    <span style="color:var(--dark-text-secondary)">oleh {{ $serviceRequest->reviewer->name ?? 'Admin' }}</span>
                                @endif
                            </p>
                        </div>
                    @endif

                    @if(in_array($serviceRequest->status, ['accepted','quoted']))
                        @if(!$serviceRequest->completed_at)
                            <form method="POST" action="{{ route('admin.service-cost-requests.complete', $serviceRequest->request_number) }}">
                                @csrf
                                <button type="submit"
                                        style="width:100%;padding:9px;background:var(--apple-green);color:#fff;border:none;border-radius:10px;font-size:.84rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                    <i class="fas fa-check"></i>Tandai Selesai
                                </button>
                            </form>
                        @else
                            <div style="background:color-mix(in srgb,var(--apple-green) 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-green) 30%,var(--dark-separator));border-radius:10px;padding:10px;text-align:center">
                                <p style="font-size:.84rem;font-weight:600;color:var(--apple-green);margin:0">
                                    <i class="fas fa-check-circle" style="margin-right:6px"></i>Selesai pada {{ $serviceRequest->completed_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

        </div>{{-- /LEFT --}}

        {{-- RIGHT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Dokumen --}}
            @if(!empty($serviceRequest->documents) && is_array($serviceRequest->documents))
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                    <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-file-upload" style="font-size:.82rem;color:var(--apple-teal)"></i>
                        <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Dokumen ({{ count($serviceRequest->documents) }})</h3>
                    </div>
                    <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px">
                        @foreach($serviceRequest->documents as $doc)
                            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:10px">
                                <i class="fas fa-file-pdf" style="font-size:1.2rem;color:var(--apple-red);flex-shrink:0"></i>
                                <div style="flex:1;min-width:0">
                                    <p style="font-size:.83rem;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $doc['name'] ?? 'Document' }}</p>
                                    <p style="font-size:.7rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ round(($doc['size'] ?? 0) / 1024, 2) }} KB</p>
                                </div>
                                @if(isset($doc['path']))
                                    <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank"
                                       style="flex-shrink:0;padding:5px 10px;border:1px solid var(--dark-separator);border-radius:6px;font-size:.75rem;color:var(--apple-blue)"
                                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                        <i class="fas fa-download" style="margin-right:4px"></i>Download
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Catatan Admin --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-sticky-note" style="font-size:.82rem;color:var(--apple-yellow)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Catatan Admin</h3>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px">
                    <form method="POST" action="{{ route('admin.service-cost-requests.add-note', $serviceRequest->request_number) }}"
                          style="display:flex;flex-direction:column;gap:8px">
                        @csrf
                        <textarea name="note" rows="3" placeholder="Tambahkan catatan..."
                                  style="width:100%;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);border-radius:8px;padding:9px 12px;font-size:.83rem;resize:vertical"
                                  onfocus="this.style.borderColor='var(--apple-yellow)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                        <button type="submit"
                                style="align-self:flex-start;padding:7px 18px;background:var(--apple-yellow);color:#000;border:none;border-radius:8px;font-size:.82rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-plus"></i>Tambah Catatan
                        </button>
                    </form>
                    @if($serviceRequest->admin_notes)
                        <div style="display:flex;flex-direction:column;gap:8px">
                            @foreach(array_filter(explode("\n\n", $serviceRequest->admin_notes)) as $note)
                                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px;padding:10px 12px">
                                    <p style="font-size:.83rem;color:var(--dark-text-primary);margin:0;white-space:pre-wrap;line-height:1.5">{{ $note }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:.84rem;color:var(--dark-text-tertiary);text-align:center;padding:12px 0;margin:0">Belum ada catatan</p>
                    @endif
                </div>
            </div>

            {{-- Informasi Teknis --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:13px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-info-circle" style="font-size:.82rem;color:var(--dark-text-secondary)"></i>
                    <h3 style="font-size:.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Teknis</h3>
                </div>
                <div style="padding:14px 16px;display:flex;flex-direction:column;gap:8px">
                    @php
                        $techFields = [
                            ['label'=>'Nomor Permohonan', 'value'=> $serviceRequest->request_number, 'mono'=>true],
                            ['label'=>'IP Address',        'value'=> $serviceRequest->ip_address ?? '-', 'mono'=>true],
                            ['label'=>'Source',            'value'=> $serviceRequest->source ?? 'website', 'mono'=>false],
                            ['label'=>'Dibuat',            'value'=> $serviceRequest->created_at->format('d M Y H:i'), 'mono'=>false],
                        ];
                    @endphp
                    @foreach($techFields as $f)
                        <div style="display:flex;justify-content:space-between;align-items:baseline;gap:12px;font-size:.82rem">
                            <span style="color:var(--dark-text-secondary);flex-shrink:0">{{ $f['label'] }}</span>
                            <span style="color:var(--dark-text-primary);{{ $f['mono'] ? 'font-family:monospace' : '' }}">{{ $f['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /RIGHT --}}
    </div>
</div>

{{-- Archive Modal --}}
<div id="archiveModal" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:50;display:none;align-items:center;justify-content:center;padding:16px">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:24px;max-width:420px;width:100%">
        <h3 style="font-size:.95rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 10px;display:flex;align-items:center;gap:8px">
            <i class="fas fa-archive" style="color:var(--apple-orange)"></i>Arsipkan Permohonan
        </h3>
        <p style="font-size:.84rem;color:var(--dark-text-secondary);margin:0 0 18px;line-height:1.5">
            Permohonan yang diarsipkan akan tersembunyi dari daftar utama. Apakah Anda yakin?
        </p>
        <form method="POST" action="{{ route('admin.service-cost-requests.archive', $serviceRequest->request_number) }}">
            @csrf
            <div style="display:flex;gap:10px">
                <button type="button" onclick="document.getElementById('archiveModal').style.display='none'"
                        style="flex:1;padding:9px;border:1px solid var(--dark-separator);border-radius:10px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:.84rem;font-weight:600;cursor:pointer"
                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1;padding:9px;background:var(--apple-orange);color:#fff;border:none;border-radius:10px;font-size:.84rem;font-weight:700;cursor:pointer"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    Arsipkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Open archive modal (fix: hidden class + inline display)
document.querySelectorAll('[onclick*="archiveModal"]').forEach(function(btn) {
    btn.onclick = function() {
        var m = document.getElementById('archiveModal');
        m.classList.remove('hidden');
        m.style.display = 'flex';
    };
});

// Copy field to clipboard
function copyField(fieldId) {
    const el = document.getElementById(fieldId);
    if (!el) return;
    const value = el.value || el.textContent || '';
    navigator.clipboard.writeText(value).then(function() {
        let btn = document.activeElement?.tagName === 'BUTTON' ? document.activeElement : null;
        if (!btn) {
            document.querySelectorAll('button').forEach(function(b) {
                if (b.innerText?.toLowerCase().includes('copy')) btn = b;
            });
        }
        if (btn) {
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check" style="margin-right:4px"></i>Tersalin';
            setTimeout(function() { btn.innerHTML = orig; }, 2000);
        }
    }).catch(function() { alert('Gagal copy. Silakan copy manual.'); });
}

// AI lock/unlock toggle
document.addEventListener('DOMContentLoaded', function() {
    const useAi = document.getElementById('useAIGenerated');
    const subj = document.getElementById('emailSubject');
    const body = document.getElementById('emailBody');
    if (!useAi || !subj || !body) return;

    const genSubject = subj.value;
    const genBody = body.value;

    function setLocked(locked) {
        subj.readOnly = locked;
        body.readOnly = locked;
        subj.style.opacity = locked ? '.8' : '1';
        body.style.opacity = locked ? '.8' : '1';
    }
    setLocked(useAi.checked);
    useAi.addEventListener('change', function() {
        if (this.checked) { subj.value = genSubject; body.value = genBody; }
        setLocked(this.checked);
    });

    // Regen button
    const regenBtn = document.getElementById('regenBtn');
    if (regenBtn) {
        regenBtn.addEventListener('click', function() {
            const note = prompt('Arahan regenerate (opsional)') || '';
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.service-cost-requests.regenerate-content', $serviceRequest->request_number) }}';
            form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}">'
                           + '<input type="hidden" name="regen_notes" value="' + note.replace(/"/g,'&quot;') + '">';
            document.body.appendChild(form);
            form.submit();
        });
    }
});
</script>

@if(($serviceRequest->ai_quote_status ?? '') === 'pending')
<script>
(function() {
    const requestNumber = '{{ $serviceRequest->request_number }}';
    let attempts = 0;
    const maxAttempts = 120;

    const statusEl = document.createElement('div');
    statusEl.style = 'position:fixed;bottom:16px;right:16px;z-index:60;background:var(--dark-bg-elevated);border:1px solid var(--dark-separator);color:var(--dark-text-primary);padding:10px 14px;border-radius:10px;font-size:.8rem;box-shadow:var(--shadow-soft-lg)';
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:8px;color:var(--apple-blue)"></i>AI sedang memproses quote...';
    document.body.appendChild(statusEl);

    const timer = setInterval(async function() {
        attempts++;
        try {
            const res = await fetch('/api/status/' + encodeURIComponent(requestNumber), {cache: 'no-store'});
            if (!res.ok) throw new Error('network');
            const json = await res.json();
            if (json.success && json.data && json.data.status === 'quoted') {
                clearInterval(timer);
                statusEl.innerHTML = '<i class="fas fa-check-circle" style="margin-right:8px;color:var(--apple-green)"></i>AI selesai. Memuat ulang...';
                setTimeout(function() { location.reload(); }, 600);
                return;
            }
        } catch(e) {}
        if (attempts >= maxAttempts) {
            clearInterval(timer);
            statusEl.innerHTML = '<i class="fas fa-exclamation-triangle" style="margin-right:8px;color:var(--apple-orange)"></i>Refresh manual jika diperlukan.';
        }
    }, 5000);
})();
</script>
@endif
@endpush
@endsection
