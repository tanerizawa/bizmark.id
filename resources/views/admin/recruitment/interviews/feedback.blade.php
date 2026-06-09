@extends('layouts.admin')

@section('title', 'Interview Feedback - ' . $interview->jobApplication->full_name)

@section('content')
@php
    $application = $interview->jobApplication;
    $vacancy = $application->jobVacancy;
    $sc = $interview->status === 'completed' ? 'var(--apple-green)' : 'var(--apple-blue)';
@endphp
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.interviews.show', $interview) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Detail Interview
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Manajemen Talenta</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $application->full_name }}</h1>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sc }} 15%,transparent);color:{{ $sc }}">{{ ucfirst($interview->status) }}</span>
            </div>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">{{ $vacancy?->title }} · {{ $interview->scheduled_at->format('d M Y, H:i') }} WIB</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-self:flex-end">
            <a href="{{ route('admin.recruitment.interviews.show', $interview) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:0.8rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-calendar" style="font-size:0.7rem"></i>Detail Interview
            </a>
        </div>
    </div>

    <form action="{{ route('admin.recruitment.interviews.feedback.store', $interview) }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">

            {{-- Left --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Interview Summary --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Rincian Interview</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Ringkasan Jadwal</h3>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
                        @foreach([
                            ['Tanggal',$interview->scheduled_at->format('d M Y'),$interview->scheduled_at->format('H:i').' WIB'],
                            ['Tipe Interview',ucfirst($interview->interview_type),ucfirst(str_replace('-',' ',$interview->meeting_type))],
                            ['Durasi',$interview->duration_minutes.' menit',''],
                            ['Status',ucfirst($interview->status),''],
                        ] as $col)
                        <div>
                            <p style="font-size:0.68rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em;margin:0 0 3px">{{ $col[0] }}</p>
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 1px">{{ $col[1] }}</p>
                            @if($col[2])<p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $col[2] }}</p>@endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Rating Sections --}}
                @php
                    $ratingLabels = [1=>'Sangat Kurang',2=>'Kurang',3=>'Cukup',4=>'Baik',5=>'Sangat Baik'];
                    $ratingFields = [
                        ['communication_rating','Komunikasi','Kejelasan, bahasa profesional, mendengarkan'],
                        ['technical_rating','Pengetahuan Teknis','Kompetensi teknis dan problem solving'],
                        ['teamwork_rating','Kolaborasi','Kerja tim, kooperatif, interpersonal'],
                        ['culture_fit_rating','Kesesuaian Budaya','Nilai, gaya kerja, adaptasi'],
                        ['overall_rating','Penilaian Akhir','Impresi keseluruhan kandidat'],
                    ];
                @endphp
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Penilaian</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Skor Interview</h3>
                    </div>
                    @foreach($ratingFields as [$field, $label, $helper])
                    <div style="margin-bottom:20px">
                        <div style="margin-bottom:8px">
                            <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $label }} <span style="color:var(--apple-red)">*</span></p>
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $helper }}</p>
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px">
                            @foreach($ratingLabels as $val=>$text)
                            <label style="cursor:pointer">
                                <input type="radio" name="{{ $field }}" value="{{ $val }}" class="rating-input" {{ old($field) == $val ? 'checked' : '' }} required style="display:none">
                                <div class="rating-pill" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:10px 6px;border-radius:10px;border:1px solid var(--dark-separator);background:var(--dark-bg-secondary);text-align:center;transition:all .15s">
                                    <span style="color:#fbbf24;font-size:0.78rem;display:block;margin-bottom:3px">
                                        @for($s=1;$s<=$val;$s++)<i class="fas fa-star" style="font-size:0.65rem"></i>@endfor
                                    </span>
                                    <span style="font-size:0.65rem;color:var(--dark-text-secondary);line-height:1.2">{{ $text }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        @error($field)<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>

                {{-- Comments --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Umpan Balik</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Catatan Detail</h3>
                    </div>
                    @foreach([['strengths','Kekuatan','Hal yang menonjol, kekuatan utama'],['weaknesses','Area Perbaikan','Area yang perlu ditingkatkan'],['additional_notes','Catatan Tambahan','Observasi lain, red flag, atau poin penting']] as [$fname,$flabel,$fhint])
                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:4px">{{ $flabel }}</label>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 6px">{{ $fhint }}</p>
                        <textarea name="{{ $fname }}" rows="3" placeholder="{{ $fhint }}..."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old($fname) }}</textarea>
                        @error($fname)<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                    @endforeach
                </div>

                {{-- Recommendation --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:14px;padding-bottom:12px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Rekomendasi</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Keputusan Hiring</h3>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
                        @foreach(['highly-recommended'=>['Highly Recommended','var(--apple-green)'],'recommended'=>['Recommended','var(--apple-blue)'],'neutral'=>['Neutral','var(--apple-orange)'],'not-recommended'=>['Not Recommended','var(--apple-red)']] as $val=>[$rlabel,$rcolor])
                        <label style="cursor:pointer">
                            <input type="radio" name="recommendation" value="{{ $val }}" class="rec-input" {{ old('recommendation') == $val ? 'checked' : '' }} required style="display:none">
                            <div class="rec-pill" data-color="{{ $rcolor }}" style="padding:12px 10px;border-radius:10px;border:1px solid var(--dark-separator);background:var(--dark-bg-secondary);text-align:center;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);transition:all .15s">
                                {{ $rlabel }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('recommendation')<p style="color:var(--apple-red);font-size:0.72rem;margin:6px 0 0">{{ $message }}</p>@enderror
                </div>

                {{-- Submit --}}
                <div style="display:flex;justify-content:flex-end;gap:10px">
                    <a href="{{ route('admin.recruitment.interviews.show', $interview) }}"
                       style="display:inline-flex;align-items:center;padding:10px 20px;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.85rem;font-weight:600;text-decoration:none"
                       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Batal</a>
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.88rem;font-weight:700;cursor:pointer"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-check"></i>Kirim Feedback
                    </button>
                </div>
            </div>

            {{-- Sidebar --}}
            <div style="position:sticky;top:16px;display:flex;flex-direction:column;gap:14px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Detail Kandidat</h3>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach(['Nama'=>$application->full_name,'Posisi'=>$vacancy?->title,'Lokasi'=>$vacancy?->location,'Tahap'=>ucfirst($application->status)] as $k=>$v)
                        <div style="display:flex;justify-content:space-between;padding:7px 10px;background:var(--dark-bg-secondary);border-radius:8px">
                            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $k }}</span>
                            <span style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary)">{{ $v }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Agenda Interview</h3>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach(['Tanggal'=>$interview->scheduled_at->format('d M Y'),'Waktu'=>$interview->scheduled_at->format('H:i').' WIB','Tipe'=>ucfirst($interview->interview_type),'Meeting'=>ucfirst(str_replace('-',' ',$interview->meeting_type))] as $k=>$v)
                        <div style="display:flex;justify-content:space-between;padding:7px 10px;background:var(--dark-bg-secondary);border-radius:8px">
                            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $k }}</span>
                            <span style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary)">{{ $v }}</span>
                        </div>
                        @endforeach
                        @if($interview->meeting_link)
                        <div style="padding:7px 10px;background:var(--dark-bg-secondary);border-radius:8px;border-top:1px solid var(--dark-separator)">
                            <p style="font-size:0.68rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;margin:0 0 3px">Link</p>
                            <a href="{{ $interview->meeting_link }}" target="_blank" style="font-size:0.72rem;color:var(--apple-blue);text-decoration:none;word-break:break-all">{{ $interview->meeting_link }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
.rating-input:checked + .rating-pill {
    background: color-mix(in srgb, var(--apple-blue) 18%, transparent) !important;
    border-color: color-mix(in srgb, var(--apple-blue) 40%, transparent) !important;
}
.rating-input:checked + .rating-pill span:last-child { color: #fff !important; }
.rec-input:checked + .rec-pill {
    border-color: var(--active-color, var(--apple-blue));
    background: color-mix(in srgb, var(--active-color, var(--apple-blue)) 18%, transparent);
    color: var(--active-color, var(--apple-blue)) !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating pill active state
    document.querySelectorAll('.rating-input').forEach(function(inp) {
        inp.addEventListener('change', function() {
            const name = this.name;
            document.querySelectorAll('input[name="'+name+'"]').forEach(function(r) {
                const pill = r.nextElementSibling;
                pill.style.background = r.checked ? 'color-mix(in srgb,var(--apple-blue) 18%,transparent)' : 'var(--dark-bg-secondary)';
                pill.style.borderColor = r.checked ? 'color-mix(in srgb,var(--apple-blue) 40%,transparent)' : 'var(--dark-separator)';
                pill.querySelector('span:last-child').style.color = r.checked ? '#fff' : 'var(--dark-text-secondary)';
            });
        });
    });
    // Recommendation pill
    document.querySelectorAll('.rec-input').forEach(function(inp) {
        inp.addEventListener('change', function() {
            document.querySelectorAll('.rec-input').forEach(function(r) {
                const pill = r.nextElementSibling;
                const col = pill.dataset.color;
                if (r.checked) {
                    pill.style.background = 'color-mix(in srgb,'+col+' 18%,transparent)';
                    pill.style.borderColor = 'color-mix(in srgb,'+col+' 40%,transparent)';
                    pill.style.color = col;
                } else {
                    pill.style.background = 'var(--dark-bg-secondary)';
                    pill.style.borderColor = 'var(--dark-separator)';
                    pill.style.color = 'var(--dark-text-secondary)';
                }
            });
        });
    });
    // Restore on page load (validation errors)
    document.querySelectorAll('.rating-input:checked').forEach(function(inp) { inp.dispatchEvent(new Event('change')); });
    document.querySelectorAll('.rec-input:checked').forEach(function(inp) { inp.dispatchEvent(new Event('change')); });
});
</script>
@endpush
@endsection
