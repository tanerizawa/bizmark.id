@extends('layouts.admin')

@section('title', 'Jadwalkan Interview')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.interviews.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Interviews
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Manajemen Talenta</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Jadwalkan Interview</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Atur jadwal, format, dan tim pewawancara dalam satu langkah</p>
        </div>
    </div>

    <form action="{{ route('admin.recruitment.interviews.store') }}" method="POST" onsubmit="handleSubmit(this)">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">

            {{-- Left --}}
            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Formulir</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Interview</h3>
                    </div>

                    {{-- Kandidat --}}
                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Kandidat <span style="color:var(--apple-red)">*</span></label>
                        @if($application)
                            <input type="hidden" name="job_application_id" value="{{ $application->id }}">
                            <div style="padding:12px 14px;background:color-mix(in srgb,var(--apple-blue) 8%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));border-radius:10px">
                                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $application->full_name }}</p>
                                <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $application?->jobVacancy?->title ?? 'Position Deleted' }} · {{ $application->email }}</p>
                            </div>
                        @else
                            @php
                                $applications = App\Models\JobApplication::with('jobVacancy')
                                    ->whereIn('status', ['reviewed', 'shortlisted', 'interview-scheduled'])
                                    ->orderBy('created_at', 'desc')->get();
                            @endphp
                            <div style="position:relative">
                                <select name="job_application_id" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">Pilih kandidat...</option>
                                    @foreach($applications as $app)
                                    <option value="{{ $app->id }}" {{ old('job_application_id') == $app->id ? 'selected' : '' }}>{{ $app->full_name }} — {{ $app?->jobVacancy?->title ?? 'Deleted' }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                            </div>
                            @error('job_application_id')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        @endif
                    </div>

                    {{-- Tanggal & Durasi --}}
                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Tanggal & Waktu <span style="color:var(--apple-red)">*</span></label>
                            @php
                                $defaultDate = old('scheduled_at');
                                if (!$defaultDate && request('date')) {
                                    try { $defaultDate = (new \DateTime(request('date')))->format('Y-m-d\TH:i'); } catch (\Exception $e) {}
                                }
                            @endphp
                            <input type="datetime-local" name="scheduled_at" value="{{ $defaultDate }}" required min="{{ now()->format('Y-m-d\TH:i') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;color-scheme:dark"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('scheduled_at')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Durasi <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="duration_minutes" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    @foreach([30=>'30 menit',45=>'45 menit',60=>'60 menit',90=>'90 menit',120=>'2 jam'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('duration_minutes', 45) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Tipe & Format --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Tipe Interview <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="interview_type" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    @foreach(['preliminary'=>'Preliminary','technical'=>'Technical','hr'=>'HR','final'=>'Final'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('interview_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Format Meeting <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="meeting_type" id="meeting_type" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    @foreach(['video-call'=>'Video Conference','phone'=>'Phone Call','in-person'=>'In-Person'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('meeting_type', 'video-call') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div id="location-field" style="display:none;margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Lokasi</label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="Alamat kantor atau ruang meeting"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>

                    {{-- Meeting Link --}}
                    <div id="meeting-link-field" style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Meeting Link</label>
                        <input type="url" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="Kosongkan untuk auto-generate Jitsi"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0">Kosongkan untuk auto-generate Jitsi, atau tempel link Zoom/Google Meet.</p>
                    </div>

                    {{-- Interviewer --}}
                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Interviewer <span style="color:var(--apple-red)">*</span></label>
                        <select name="interviewer_ids[]" multiple required
                                style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;height:130px;box-sizing:border-box"
                                onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @foreach($interviewers as $interviewer)
                            <option value="{{ $interviewer->id }}">{{ $interviewer->name }} ({{ $interviewer->email }})</option>
                            @endforeach
                        </select>
                        <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0">Gunakan Ctrl/Cmd untuk memilih beberapa.</p>
                        @error('interviewer_ids')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Catatan Internal</label>
                        <textarea name="notes" rows="3" placeholder="Catatan persiapan, fokus penilaian, dll."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div style="position:sticky;top:16px;display:flex;flex-direction:column;gap:14px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px;display:flex;flex-direction:column;gap:10px">
                    <button type="submit" id="submit-btn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-calendar-check" id="submit-icon"></i>
                        <span id="submit-label">Jadwalkan Interview</span>
                    </button>
                    <a href="{{ route('admin.recruitment.interviews.index') }}"
                       style="display:flex;align-items:center;justify-content:center;padding:10px 20px;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.85rem;font-weight:600;text-decoration:none"
                       onmouseover="this.style.color='var(--dark-text-primary)';this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.color='var(--dark-text-secondary)';this.style.borderColor='var(--dark-separator)'">Batal</a>
                </div>
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Tips Penjadwalan</h3>
                    <ul style="margin:0;padding-left:16px;display:flex;flex-direction:column;gap:5px">
                        @foreach(['Jadwalkan minimal 24 jam sebelumnya','Pastikan ketersediaan pewawancara','Video: Jitsi auto-generate','Ctrl/Cmd untuk multi-pilih pewawancara','Notifikasi email dikirim ke kandidat'] as $tip)
                        <li style="font-size:0.78rem;color:var(--dark-text-secondary)">{{ $tip }}</li>
                        @endforeach
                    </ul>
                </div>
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Jenis Interview</h3>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach([['fa-clipboard-list','var(--apple-blue)','Preliminary','Screening awal kandidat'],['fa-code','var(--apple-green)','Technical','Tes kemampuan teknis'],['fa-user-tie','var(--apple-purple)','HR','Interview dengan HR'],['fa-users','var(--apple-orange)','Final','Interview akhir pimpinan']] as $t)
                        <div style="display:flex;gap:10px;align-items:flex-start">
                            <i class="fas {{ $t[0] }}" style="color:{{ $t[1] }};margin-top:2px;width:14px;text-align:center;flex-shrink:0"></i>
                            <div><p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 1px">{{ $t[2] }}</p><p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $t[3] }}</p></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mt = document.getElementById('meeting_type');
    const lf = document.getElementById('location-field');
    const mf = document.getElementById('meeting-link-field');
    function upd() { lf.style.display = mt.value === 'in-person' ? '' : 'none'; mf.style.display = mt.value === 'phone' ? 'none' : ''; }
    mt.addEventListener('change', upd); upd();
});
function handleSubmit() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('submit-icon').className = 'fas fa-spinner fa-spin';
    document.getElementById('submit-label').textContent = 'Menyimpan...';
}
</script>
@endpush
@endsection
