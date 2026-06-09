@extends('layouts.admin')

@section('title', 'Manajemen Interview')

@php
    $completedCount = $metrics['completed'] ?? \App\Models\InterviewSchedule::where('status', 'completed')->count();
    $cancelledCount = $metrics['cancelled'] ?? \App\Models\InterviewSchedule::where('status', 'cancelled')->count();
@endphp

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Rekrutmen
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Manajemen Talenta</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Manajemen Interview</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Jadwalkan dan pantau interview kandidat dalam satu panel</p>
        </div>
        <a href="{{ route('admin.recruitment.interviews.create') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.85rem;font-weight:700;text-decoration:none;transition:opacity .2s;align-self:flex-end"
           onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>Jadwalkan Interview
        </a>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @php $interviewStats = [
            ['label'=>'Hari Ini',       'value'=>$todayInterviews->count(),   'sub'=>'Terlaksana hari ini', 'color'=>'var(--apple-blue)'],
            ['label'=>'Akan Datang',    'value'=>$upcomingInterviews->count(),'sub'=>'Terjadwal',           'color'=>'var(--apple-green)'],
            ['label'=>'Selesai',        'value'=>$completedCount,             'sub'=>'Status selesai',      'color'=>'var(--apple-teal)'],
            ['label'=>'Dibatalkan',     'value'=>$cancelledCount,             'sub'=>'Total batal',         'color'=>'var(--apple-red)'],
        ]; @endphp
        @foreach($interviewStats as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['color'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['color'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.85;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:1.8rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Main Grid: Calendar + Sidebar --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">

        {{-- Calendar --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
            <div style="padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Kalender</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Jadwal Interview</h3>
            </div>
            <div style="padding:16px">
                <div id="interviewCalendar" style="min-height:520px"></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:12px">
            {{-- Hari Ini --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Jadwal Hari Ini</h3>
                </div>
                @forelse($todayInterviews as $interview)
                <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator)">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px">
                        <div>
                            <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $interview->jobApplication->full_name }}</p>
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $interview->jobApplication?->jobVacancy?->title ?? '—' }}</p>
                        </div>
                        <span style="display:inline-flex;padding:2px 8px;border-radius:20px;font-size:0.65rem;font-weight:600;flex-shrink:0;background:color-mix(in srgb,{{ $interview->status === 'scheduled' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }} 15%,transparent);color:{{ $interview->status === 'scheduled' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }}">{{ ucfirst($interview->status) }}</span>
                    </div>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 3px">
                        <i class="fas fa-clock" style="margin-right:4px;opacity:.6"></i>{{ $interview->scheduled_at->format('H:i') }} ({{ $interview->duration_minutes }} mnt)
                    </p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 8px">
                        <i class="fas fa-{{ $interview->interview_type === 'video' ? 'video' : ($interview->interview_type === 'phone' ? 'phone-alt' : 'map-marker-alt') }}" style="margin-right:4px;opacity:.6"></i>{{ $interview->getMeetingTypeLabel() }}
                    </p>
                    <a href="{{ route('admin.recruitment.interviews.show', $interview) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:4px 9px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none">
                        <i class="fas fa-eye" style="font-size:0.65rem"></i>Detail
                    </a>
                </div>
                @empty
                <div style="padding:24px;text-align:center">
                    <i class="fas fa-calendar-times" style="font-size:1.5rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:8px"></i>
                    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Tidak ada interview hari ini</p>
                </div>
                @endforelse
            </div>

            {{-- Akan Datang --}}
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator)">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Akan Datang</h3>
                </div>
                @forelse($upcomingInterviews->take(5) as $interview)
                <div style="padding:10px 16px;border-bottom:1px solid var(--dark-separator)">
                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0 0 2px">{{ $interview->scheduled_at->format('D, d M H:i') }}</p>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 1px">{{ $interview->jobApplication->full_name }}</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $interview->jobApplication?->jobVacancy?->title ?? '—' }}</p>
                </div>
                @empty
                <div style="padding:20px;text-align:center">
                    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Tidak ada jadwal minggu ini</p>
                </div>
                @endforelse
                @if($upcomingInterviews->count() > 5)
                <div style="padding:10px 16px;text-align:center">
                    <span style="font-size:0.75rem;color:var(--dark-text-secondary)">+{{ $upcomingInterviews->count() - 5 }} lainnya</span>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
#interviewCalendar { max-width:100%; }
.fc-theme-standard td, .fc-theme-standard th { border-color: var(--dark-separator); }
.fc .fc-toolbar-title { color:#fff; font-size:1rem; font-weight:700; }
.fc .fc-button-primary { background:var(--dark-bg-secondary); border:1px solid var(--dark-separator); color:var(--dark-text-primary); }
.fc .fc-button-primary:hover { background:var(--dark-bg-tertiary); border-color:var(--apple-blue); }
.fc .fc-button-primary:not(:disabled).fc-button-active { background:color-mix(in srgb,var(--apple-blue) 20%,var(--dark-bg-secondary)); border-color:var(--apple-blue); color:var(--apple-blue); }
.fc .fc-col-header-cell-cushion, .fc .fc-daygrid-day-number { color: var(--dark-text-secondary); text-decoration:none; }
.fc-event { border:none; padding:2px 6px; background:color-mix(in srgb,var(--apple-blue) 80%,transparent); }
.fc .fc-daygrid-day.fc-day-today { background: color-mix(in srgb,var(--apple-blue) 8%,transparent); }
.fc .fc-timegrid-now-indicator-line { border-color:var(--apple-red); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('interviewCalendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        allDaySlot: false,
        nowIndicator: true,
        selectable: true,
        selectMirror: true,
        events: { url: '{{ route("admin.recruitment.interviews.index") }}', method: 'GET', extraParams: () => ({ json: 1 }) },
        eventClick: function(info) { window.location.href = '/admin/recruitment/interviews/' + info.event.id; },
        select: function(info) { window.location.href = '{{ route("admin.recruitment.interviews.create") }}?date=' + info.startStr; }
    });
    calendar.render();
});
</script>
@endpush
