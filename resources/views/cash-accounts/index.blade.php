@extends('layouts.admin')
@section('title', 'Keuangan')
@section('page-title', 'Manajemen Keuangan')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:240px;height:240px;border-radius:50%;top:-70px;right:-40px;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);filter:blur(60px);pointer-events:none"></div>
        <div style="position:absolute;width:160px;height:160px;border-radius:50%;bottom:-30px;left:30px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);filter:blur(50px);pointer-events:none"></div>
        <div style="position:relative">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:16px">
                <div>
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Kendali Keuangan Terintegrasi</p>
                    <h1 style="font-size:1.25rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 6px">Kendali Kas & Rekening Terpadu</h1>
                    <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Pantau arus kas, piutang, dan tren finansial dalam satu panel.</p>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
                    <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0"><i class="fas fa-sync-alt" style="margin-right:5px"></i>Sync: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0"><i class="fas fa-shield-alt" style="margin-right:5px"></i>Akses Tim Keuangan</p>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <a href="{{ route('cash-accounts.create') }}"
                           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-plus" style="font-size:0.75rem"></i>Tambah Akun
                        </a>
                        <button onclick="openPeriodModal()"
                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:rgba(255,255,255,.06);cursor:pointer"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-calendar-alt" style="font-size:0.75rem"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
                <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-blue) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-blue) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-blue) 22%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-wallet" style="color:var(--apple-blue);font-size:0.85rem"></i></div>
                        <div>
                            <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">Rp {{ number_format($financialSummary['liquid_assets'] / 1000000, 1) }}M</p>
                            <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Aset Likuid</p>
                        </div>
                    </div>
                </div>
                <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-orange) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-orange) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-orange) 22%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-file-invoice" style="color:var(--apple-orange);font-size:0.85rem"></i></div>
                        <div>
                            <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">Rp {{ number_format($financialSummary['total_receivables'] / 1000000, 1) }}M</p>
                            <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Piutang</p>
                        </div>
                    </div>
                </div>
                <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-green) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-green) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 22%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-arrow-down" style="color:var(--apple-green);font-size:0.85rem"></i></div>
                        <div>
                            <p style="font-size:1rem;font-weight:800;color:var(--apple-green);margin:0">Rp {{ number_format($financialSummary['cash_inflow_this_month'] / 1000000, 1) }}M</p>
                            <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Kas Masuk</p>
                        </div>
                    </div>
                </div>
                @php $isPositive = $financialSummary['net_cash_flow'] >= 0; @endphp
                <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $isPositive ? 'var(--apple-green)' : 'var(--apple-red)' }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $isPositive ? 'var(--apple-green)' : 'var(--apple-red)' }} 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,{{ $isPositive ? 'var(--apple-green)' : 'var(--apple-red)' }} 22%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-chart-line" style="color:{{ $isPositive ? 'var(--apple-green)' : 'var(--apple-red)' }};font-size:0.85rem"></i></div>
                        <div>
                            <p style="font-size:1rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $isPositive ? '+' : '' }}Rp {{ number_format($financialSummary['net_cash_flow'] / 1000000, 1) }}M</p>
                            <p style="font-size:0.65rem;color:var(--dark-text-secondary);margin:2px 0 0;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Arus Bersih</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div style="display:flex;align-items:center;gap:10px;border-radius:10px;padding:10px 16px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);color:var(--apple-green)">
        <i class="fas fa-check-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;border-radius:10px;padding:10px 16px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Insight Cards --}}
    <div style="display:flex;flex-direction:column;gap:10px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 2px">Periode Aktif</p>
                <h2 style="font-size:0.96rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 2px">{{ $startDate->isoFormat('D MMM Y') }} – {{ $endDate->isoFormat('D MMM Y') }}</h2>
                @php $daysDiff = $startDate->diffInDays($endDate) + 1; @endphp
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">{{ $daysDiff }} hari • {{ count($recentTransactions) }} transaksi</p>
            </div>
            <button onclick="openPeriodModal()"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:9px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;background:rgba(255,255,255,.04);cursor:pointer"
                    onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-calendar-alt" style="font-size:0.72rem"></i>Ubah Periode
            </button>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:14px 16px;display:flex;flex-direction:column;gap:6px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Kas Keluar</h3>
                    <span style="padding:2px 8px;border-radius:8px;font-size:0.68rem;font-weight:700;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red)">Expense</span>
                </div>
                <p style="font-size:1.1rem;font-weight:800;color:var(--dark-text-primary);margin:0">Rp {{ number_format($financialSummary['cash_outflow_this_month'] / 1000000, 1) }}M</p>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">Pengeluaran periode berjalan</p>
            </div>
            @php $isPositiveTrend = $financialSummary['is_positive_trend'] ?? false; @endphp
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:14px 16px;display:flex;flex-direction:column;gap:6px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Arus Kas Bersih</h3>
                    <span style="padding:2px 8px;border-radius:8px;font-size:0.68rem;font-weight:700;background:color-mix(in srgb,{{ $isPositiveTrend ? 'var(--apple-green)' : 'var(--apple-red)' }} 15%,transparent);color:{{ $isPositiveTrend ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $isPositiveTrend ? 'Positif' : 'Negatif' }}</span>
                </div>
                <p style="font-size:1.1rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $isPositiveTrend ? '+' : '' }}{{ $financialSummary['cash_flow_trend'] }}%</p>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">vs bulan lalu</p>
            </div>
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:14px 16px;display:flex;flex-direction:column;gap:6px">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Rekening Aktif</h3>
                    <span style="padding:2px 8px;border-radius:8px;font-size:0.68rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">Live</span>
                </div>
                <p style="font-size:1.1rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $accounts->count() }}</p>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">Rekening dan kas</p>
            </div>
        </div>
    </div>

    {{-- Tab Container --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
        <div style="border-bottom:1px solid var(--dark-separator);padding:8px 8px 0;overflow-x:auto;overflow-y:hidden;white-space:nowrap">
            <div style="display:inline-flex;gap:4px;padding-bottom:8px">
                @php
                    $tabs = [
                        ['id'=>'cash-flow','icon'=>'fa-chart-line','label'=>'Laporan Arus Kas'],
                        ['id'=>'accounts','icon'=>'fa-university','label'=>'Rekening dan Kas'],
                        ['id'=>'general','icon'=>'fa-briefcase','label'=>'Keuangan Umum'],
                        ['id'=>'reconciliations','icon'=>'fa-balance-scale','label'=>'Rekonsiliasi Bank'],
                        ['id'=>'transactions','icon'=>'fa-project-diagram','label'=>'Transaksi Proyek'],
                    ];
                    $activeTab = request('tab', 'cash-flow');
                @endphp
                @foreach($tabs as $tab)
                <button onclick="switchCashTab('{{ $tab['id'] }}')" id="tabBtn-{{ $tab['id'] }}"
                        style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:9px;font-size:0.82rem;font-weight:600;border:1px solid {{ $activeTab === $tab['id'] ? 'color-mix(in srgb,var(--apple-blue) 30%,transparent)' : 'transparent' }};cursor:pointer;white-space:nowrap;background:{{ $activeTab === $tab['id'] ? 'color-mix(in srgb,var(--apple-blue) 15%,transparent)' : 'transparent' }};color:{{ $activeTab === $tab['id'] ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)' }}">
                    <i class="fas {{ $tab['icon'] }}" style="font-size:0.78rem"></i>{{ $tab['label'] }}
                    @if($tab['id'] === 'general')
                        @php $generalCount = ($generalTransactions['income']->count() + $generalTransactions['expenses']->count()); @endphp
                        @if($generalCount > 0)
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border-radius:9px;font-size:0.68rem;font-weight:700;background:var(--apple-blue);color:#fff">{{ $generalCount }}</span>
                        @endif
                    @elseif($tab['id'] === 'reconciliations')
                        @if(isset($pendingReconciliations) && $pendingReconciliations > 0)
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 4px;border-radius:9px;font-size:0.68rem;font-weight:700;background:var(--apple-orange);color:#fff">{{ $pendingReconciliations }}</span>
                        @endif
                    @endif
                </button>
                @endforeach
            </div>
        </div>
        <div style="padding:20px">
            <div id="tabContent-cash-flow" style="display:{{ $activeTab === 'cash-flow' ? 'block' : 'none' }}">
                @include('cash-accounts.tabs.cash-flow', ['cashFlowStatement' => $cashFlowStatement])
            </div>
            <div id="tabContent-accounts" style="display:{{ $activeTab === 'accounts' ? 'block' : 'none' }}">
                @include('cash-accounts.tabs.accounts', ['accounts' => $accounts])
            </div>
            <div id="tabContent-general" style="display:{{ $activeTab === 'general' ? 'block' : 'none' }}">
                @include('cash-accounts.tabs.general-transactions', ['generalTransactions' => $generalTransactions])
            </div>
            <div id="tabContent-reconciliations" style="display:{{ $activeTab === 'reconciliations' ? 'block' : 'none' }}">
                @include('cash-accounts.tabs.reconciliations')
            </div>
            <div id="tabContent-transactions" style="display:{{ $activeTab === 'transactions' ? 'block' : 'none' }}">
                @include('cash-accounts.tabs.transactions', ['recentTransactions' => $recentTransactions])
            </div>
        </div>
    </div>
</div>

{{-- Period Filter Modal --}}
<div id="periodModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.75)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;width:100%;max-width:640px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <div>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:0">Filter Periode Laporan</h3>
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:3px 0 0">Pilih rentang waktu untuk analisis finansial</p>
            </div>
            <button onclick="closePeriodModal()" style="width:32px;height:32px;border-radius:50%;border:none;background:rgba(255,255,255,.06);color:var(--dark-text-secondary);cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center">&times;</button>
        </div>
        <div style="padding:20px">
            <form method="GET" action="{{ route('cash-accounts.index') }}" id="periodFilterForm" style="display:flex;flex-direction:column;gap:14px">
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">
                    @foreach(['month'=>['fa-calendar','Bulan'],'quarter'=>['fa-calendar-week','Kuartal'],'year'=>['fa-calendar-alt','Tahun'],'custom'=>['fa-sliders-h','Custom']] as $ftype => $fmeta)
                    <button type="button" id="ftBtn-{{ $ftype }}" onclick="setPeriodFilterType('{{ $ftype }}')"
                            style="padding:8px 10px;border-radius:9px;font-size:0.8rem;font-weight:600;cursor:pointer;border:1px solid {{ request('filter_type', 'month') === $ftype ? 'var(--apple-blue)' : 'var(--dark-separator)' }};background:{{ request('filter_type', 'month') === $ftype ? 'color-mix(in srgb,var(--apple-blue) 15%,transparent)' : 'var(--dark-bg-tertiary)' }};color:{{ request('filter_type', 'month') === $ftype ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)' }}">
                        <i class="fas {{ $fmeta[0] }}" style="margin-right:5px"></i>{{ $fmeta[1] }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="filter_type" id="periodFilterTypeInput" value="{{ request('filter_type', 'month') }}">

                <div id="pfSection-month" style="display:{{ request('filter_type', 'month') === 'month' ? 'block' : 'none' }}">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Bulan</label>
                            <select name="month" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m, 1)->isoFormat('MMMM') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tahun</label>
                            <select name="year" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                                @for($y = 2020; $y <= date('Y'); $y++)<option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div id="pfSection-quarter" style="display:{{ request('filter_type') === 'quarter' ? 'block' : 'none' }}">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Kuartal</label>
                            <select name="quarter" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                                <option value="1" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 1 ? 'selected' : '' }}>Q1 (Jan-Mar)</option>
                                <option value="2" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 2 ? 'selected' : '' }}>Q2 (Apr-Jun)</option>
                                <option value="3" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 3 ? 'selected' : '' }}>Q3 (Jul-Sep)</option>
                                <option value="4" {{ (request('quarter') ?? ceil($selectedMonth / 3)) == 4 ? 'selected' : '' }}>Q4 (Okt-Des)</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tahun</label>
                            <select name="year" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                                @for($y = 2020; $y <= date('Y'); $y++)<option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div id="pfSection-year" style="display:{{ request('filter_type') === 'year' ? 'block' : 'none' }}">
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tahun</label>
                    <select name="year" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                        @for($y = 2020; $y <= date('Y'); $y++)<option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor
                    </select>
                </div>
                <div id="pfSection-custom" style="display:{{ request('filter_type') === 'custom' ? 'block' : 'none' }}">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tanggal Mulai</label>
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tanggal Akhir</label>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                        </div>
                    </div>
                </div>

                @if(count($availablePeriods) > 0)
                <div style="padding-top:12px;border-top:1px solid var(--dark-separator)">
                    <p style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);margin:0 0 8px;text-transform:uppercase;letter-spacing:.08em">Shortcut Periode</p>
                    <div style="display:flex;flex-wrap:wrap;gap:6px">
                        @foreach(array_slice($availablePeriods, 0, 6) as $period)
                        <a href="{{ route('cash-accounts.index', ['filter_type' => 'month', 'month' => $period['month'], 'year' => $period['year']]) }}"
                           style="font-size:0.75rem;padding:5px 12px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 10%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 20%,transparent);text-decoration:none"
                           onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                            {{ \Carbon\Carbon::create($period['year'], $period['month'], 1)->locale('id')->isoFormat('MMM YYYY') }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div style="display:flex;align-items:center;gap:10px;padding-top:6px">
                    <button type="submit" style="flex:1;padding:9px 16px;border:none;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-filter" style="margin-right:7px"></i>Terapkan Filter
                    </button>
                    <button type="button" onclick="closePeriodModal()" style="padding:9px 16px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;background:none;cursor:pointer">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchCashTab(tab) {
    ['cash-flow','accounts','general','reconciliations','transactions'].forEach(function(t) {
        document.getElementById('tabContent-' + t).style.display = 'none';
        var btn = document.getElementById('tabBtn-' + t);
        btn.style.background = 'transparent';
        btn.style.color = 'var(--dark-text-secondary)';
        btn.style.borderColor = 'transparent';
    });
    document.getElementById('tabContent-' + tab).style.display = 'block';
    var b = document.getElementById('tabBtn-' + tab);
    b.style.background = 'color-mix(in srgb,var(--apple-blue) 15%,transparent)';
    b.style.color = 'var(--dark-text-primary)';
    b.style.borderColor = 'color-mix(in srgb,var(--apple-blue) 30%,transparent)';
}
function openPeriodModal() { document.getElementById('periodModal').style.display = 'flex'; }
function closePeriodModal() { document.getElementById('periodModal').style.display = 'none'; }
function setPeriodFilterType(type) {
    ['month','quarter','year','custom'].forEach(function(t) {
        var btn = document.getElementById('ftBtn-' + t);
        var sec = document.getElementById('pfSection-' + t);
        var active = t === type;
        btn.style.background = active ? 'color-mix(in srgb,var(--apple-blue) 15%,transparent)' : 'var(--dark-bg-tertiary)';
        btn.style.borderColor = active ? 'var(--apple-blue)' : 'var(--dark-separator)';
        btn.style.color = active ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)';
        if (sec) sec.style.display = active ? 'block' : 'none';
    });
    document.getElementById('periodFilterTypeInput').value = type;
}
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePeriodModal(); });
document.getElementById('periodModal').addEventListener('click', function(e) { if (e.target === this) closePeriodModal(); });
</script>
@endpush
@endsection
