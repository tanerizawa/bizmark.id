@extends('layouts.admin')

@section('title', 'Rekrutmen')

@section('content')
@php
    $pendingCount = $notifications['applications'] ?? 0;
    $tabs = [
        ['key' => 'jobs',         'label' => 'Lowongan Kerja', 'icon' => 'fa-briefcase', 'badge' => null],
        ['key' => 'applications', 'label' => 'Lamaran Masuk',  'icon' => 'fa-user-tie',  'badge' => $pendingCount],
    ];
    $currentTab = $activeTab ?? 'jobs';
@endphp

<div style="display:flex;flex-direction:column;gap:16px">

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen Talenta</p>
        <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Rekrutmen & Lamaran</h1>
        <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:4px 0 0">Kelola lowongan pekerjaan dan proses rekrutmen kandidat.</p>
    </div>
    <a href="{{ route('admin.jobs.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:700;text-decoration:none;white-space:nowrap;transition:opacity .2s"
       onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
        <i class="fas fa-plus" style="font-size:0.72rem"></i>Tambah Lowongan
    </a>
</div>

{{-- Session Alerts --}}
@if(session('success'))
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
        <div style="display:flex;align-items:center;gap:10px">
            <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
            <span style="font-size:0.82rem;color:var(--apple-green);font-weight:600">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-green);opacity:.7"><i class="fas fa-times"></i></button>
    </div>
@endif
@if(session('error'))
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
        <div style="display:flex;align-items:center;gap:10px">
            <i class="fas fa-exclamation-circle" style="color:var(--apple-red)"></i>
            <span style="font-size:0.82rem;color:var(--apple-red);font-weight:600">{{ session('error') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);opacity:.7"><i class="fas fa-times"></i></button>
    </div>
@endif

{{-- KPI Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
    @php
    $kpis = [
        ['label'=>'Total Lowongan', 'value'=>$totalJobs,         'sub'=>'semua posisi',     'color'=>'var(--apple-blue)',   'bg'=>'var(--apple-blue)',   'icon'=>'fa-briefcase'],
        ['label'=>'Aktif',          'value'=>$activeJobs,        'sub'=>'sedang tayang',    'color'=>'var(--apple-green)',  'bg'=>'var(--apple-green)',  'icon'=>'fa-check-circle'],
        ['label'=>'Total Lamaran',  'value'=>$totalApplications, 'sub'=>'semua kandidat',   'color'=>'var(--apple-purple)', 'bg'=>'var(--apple-purple)', 'icon'=>'fa-users'],
        ['label'=>'Pending',        'value'=>$pendingCount,      'sub'=>'perlu peninjauan', 'color'=>$pendingCount > 0 ? 'var(--apple-orange)' : 'var(--apple-green)', 'bg'=>$pendingCount > 0 ? 'var(--apple-orange)' : 'var(--apple-green)', 'icon'=>'fa-clock'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $k['bg'] }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $k['bg'] }} 28%,var(--dark-separator));border-radius:16px;padding:18px 20px;position:relative;overflow:hidden">
        <div style="position:absolute;top:12px;right:16px;font-size:1.1rem;opacity:.18;color:{{ $k['color'] }}"><i class="fas {{ $k['icon'] }}"></i></div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $k['color'] }};opacity:.85;margin:0">{{ $k['label'] }}</p>
        <p style="font-size:2rem;font-weight:800;color:{{ $k['color'] }};margin:5px 0 3px;line-height:1">{{ $k['value'] }}</p>
        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $k['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Quick Actions --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
    @php
    $actions = [
        ['href' => route('admin.recruitment.pipeline.index'),   'title' => 'Pipeline',  'sub' => 'Pergerakan kandidat',  'icon' => 'fa-stream',         'color1' => 'var(--apple-blue)',   'color2' => 'var(--apple-purple)'],
        ['href' => route('admin.recruitment.interviews.index'), 'title' => 'Interview', 'sub' => 'Jadwal wawancara',     'icon' => 'fa-calendar-alt',   'color1' => 'var(--apple-green)',  'color2' => 'var(--apple-teal)'],
        ['href' => route('admin.recruitment.tests.index'),      'title' => 'Testing',   'sub' => 'Template & sesi tes', 'icon' => 'fa-clipboard-list', 'color1' => 'var(--apple-orange)', 'color2' => 'var(--apple-red)'],
    ];
    @endphp
    @foreach($actions as $a)
    <a href="{{ $a['href'] }}"
       style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;text-decoration:none;transition:border-color .15s,background .15s"
       onmouseover="this.style.borderColor='color-mix(in srgb,var(--apple-blue) 40%,var(--dark-separator))';this.style.background='var(--dark-bg-tertiary)'"
       onmouseout="this.style.borderColor='var(--dark-separator)';this.style.background='var(--dark-bg-secondary)'">
        <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,{{ $a['color1'] }},{{ $a['color2'] }})">
            <i class="fas {{ $a['icon'] }}" style="color:#fff;font-size:0.85rem"></i>
        </div>
        <div style="flex:1;min-width:0">
            <p style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $a['title'] }}</p>
            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:2px 0 0">{{ $a['sub'] }}</p>
        </div>
        <i class="fas fa-chevron-right" style="font-size:0.65rem;color:var(--dark-text-secondary)"></i>
    </a>
    @endforeach
</div>


{{-- Tab Container --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

    {{-- Tab Bar --}}
    <div style="display:flex;border-bottom:1px solid var(--dark-separator);overflow-x:auto;scrollbar-width:none">
        @foreach($tabs as $tab)
        @php $isActive = $currentTab === $tab['key']; @endphp
        <a href="{{ route('admin.recruitment.index', ['tab' => $tab['key']]) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:14px 20px;font-size:0.82rem;font-weight:{{ $isActive ? '700' : '500' }};color:{{ $isActive ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)' }};text-decoration:none;white-space:nowrap;border-bottom:2px solid {{ $isActive ? 'var(--apple-blue)' : 'transparent' }};margin-bottom:-1px;transition:color .15s"
           onmouseover="if({{ $isActive ? 'false' : 'true' }})this.style.color='var(--dark-text-primary)'"
           onmouseout="if({{ $isActive ? 'false' : 'true' }})this.style.color='var(--dark-text-secondary)'">
            <i class="fas {{ $tab['icon'] }}" style="font-size:0.75rem"></i>
            {{ $tab['label'] }}
            @if(!empty($tab['badge']) && $tab['badge'] > 0)
            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;border-radius:9px;font-size:0.65rem;font-weight:700;background:color-mix(in srgb,var(--apple-orange) 25%,transparent);color:var(--apple-orange)">{{ $tab['badge'] }}</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Tab Content --}}
    <div style="padding:20px">
        @if($currentTab === 'jobs')
            @include('admin.recruitment.tabs.jobs')
        @elseif($currentTab === 'applications')
            @include('admin.recruitment.tabs.applications')
        @endif
    </div>
</div>

</div>
@endsection
