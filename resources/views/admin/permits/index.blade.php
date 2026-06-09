@extends('layouts.admin')

@section('title', 'Manajemen Perizinan')
@section('page-title', 'Manajemen Perizinan')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

{{-- Page Header --}}
<div>
    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Layanan Utama</p>
    <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Manajemen Perizinan</h1>
    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:4px 0 0">Kelola permohonan izin, jenis izin, data KBLI, dan verifikasi pembayaran klien.</p>
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

@php
    $tabs = [
        ['key' => 'dashboard',     'label' => 'Dashboard',    'icon' => 'fa-chart-pie',         'badge' => null],
        ['key' => 'applications',  'label' => 'Permohonan',   'icon' => 'fa-file-signature',    'badge' => $notifications['applications'] ?? 0],
        ['key' => 'types',         'label' => 'Jenis Izin',   'icon' => 'fa-certificate',       'badge' => null],
        ['key' => 'kbli',          'label' => 'KBLI',         'icon' => 'fa-file-invoice',      'badge' => null],
        ['key' => 'payments',      'label' => 'Pembayaran',   'icon' => 'fa-money-check-alt',   'badge' => $notifications['payments'] ?? 0],
    ];
    $currentTab = $activeTab ?? 'dashboard';
@endphp

{{-- KPI Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
    @php
        $kpis = [
            ['label'=>'Total Permohonan', 'value'=>$totalApplications ?? 0,           'sub'=>'semua permohonan',        'color'=>'var(--apple-blue)',   'icon'=>'fa-file-alt'],
            ['label'=>'Perlu Tindakan',   'value'=>$notifications['applications'] ?? 0,'sub'=>'menunggu verifikasi',    'color'=>($notifications['applications'] ?? 0) > 0 ? 'var(--apple-orange)' : 'var(--apple-green)', 'icon'=>'fa-exclamation-circle'],
            ['label'=>'Pending Bayar',    'value'=>$notifications['payments'] ?? 0,    'sub'=>'perlu verifikasi bayar',  'color'=>($notifications['payments'] ?? 0) > 0 ? 'var(--apple-yellow)' : 'var(--apple-green)', 'icon'=>'fa-credit-card'],
            ['label'=>'Proyek Aktif',     'value'=>$activeProjects ?? 0,               'sub'=>'sedang berjalan',         'color'=>'var(--apple-purple)', 'icon'=>'fa-project-diagram'],
        ];
    @endphp
    @foreach($kpis as $k)
    <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $k['color'] }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $k['color'] }} 28%,var(--dark-separator));border-radius:16px;padding:18px 20px;position:relative;overflow:hidden">
        <div style="position:absolute;top:12px;right:16px;font-size:1.1rem;opacity:.18;color:{{ $k['color'] }}"><i class="fas {{ $k['icon'] }}"></i></div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $k['color'] }};opacity:.85;margin:0">{{ $k['label'] }}</p>
        <p style="font-size:2rem;font-weight:800;color:{{ $k['color'] }};margin:5px 0 3px;line-height:1">{{ $k['value'] }}</p>
        <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $k['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Tab Container --}}
<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

    {{-- Tab Bar (underline indicator style) --}}
    <div style="display:flex;border-bottom:1px solid var(--dark-separator);overflow-x:auto;scrollbar-width:none">
        @foreach($tabs as $tab)
        @php $isActive = $currentTab === $tab['key']; @endphp
        <a href="{{ route('admin.permits.index', ['tab' => $tab['key']]) }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:14px 20px;font-size:0.82rem;font-weight:{{ $isActive ? '700' : '500' }};color:{{ $isActive ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)' }};text-decoration:none;white-space:nowrap;border-bottom:2px solid {{ $isActive ? 'var(--apple-blue)' : 'transparent' }};margin-bottom:-1px;transition:color .15s,border-color .15s"
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
        @if($currentTab === 'dashboard')
            @include('admin.permits.tabs.dashboard')
        @elseif($currentTab === 'applications')
            @include('admin.permits.tabs.applications')
        @elseif($currentTab === 'types')
            @include('admin.permits.tabs.types')
        @elseif($currentTab === 'kbli')
            @include('admin.permits.tabs.kbli')
        @elseif($currentTab === 'payments')
            @include('admin.permits.tabs.payments')
        @endif
    </div>
</div>
</div>
@endsection
