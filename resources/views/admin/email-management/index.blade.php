@extends('layouts.admin')

@section('title', 'Email Management')
@section('page-title', 'Email Management')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:260px;height:260px;border-radius:50%;top:-80px;right:-40px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);filter:blur(64px);pointer-events:none"></div>
        <div style="position:absolute;width:180px;height:180px;border-radius:50%;bottom:-60px;left:80px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);filter:blur(64px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Komunikasi</p>
                <h1 style="font-size:1.25rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 6px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-envelope" style="color:var(--apple-orange);font-size:0.85rem"></i>
                    </span>
                    Email Management
                </h1>
                <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Kelola kotak surat, campaign marketing, subscriber, template, dan akun email operasional.</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('admin.campaigns.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-paper-plane" style="font-size:0.75rem"></i>Campaign Baru
                </a>
                <a href="{{ route('admin.email-management.index', ['tab' => 'inbox']) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.09);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-inbox" style="font-size:0.75rem"></i>Buka Mailbox
                </a>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    @php
    $unread = $unreadEmails ?? 0;
    $kpiCards = [
        ['label'=>'Total Email',  'value'=>$totalEmails??0,     'sub'=>'semua akun',        'color'=>'var(--apple-blue)',   'icon'=>'fa-envelope'],
        ['label'=>'Belum Dibaca', 'value'=>$unread,             'sub'=>$unread>0?'perlu diperhatikan':'semua terbaca', 'color'=>$unread>0?'var(--apple-orange)':'var(--apple-green)', 'icon'=>'fa-envelope-open'],
        ['label'=>'Campaign',     'value'=>$totalCampaigns??0,  'sub'=>'total campaign',    'color'=>'var(--apple-green)',  'icon'=>'fa-bullhorn'],
        ['label'=>'Subscriber',   'value'=>$totalSubscribers??0,'sub'=>'terdaftar',         'color'=>'var(--apple-purple)', 'icon'=>'fa-users'],
        ['label'=>'Template',     'value'=>$totalTemplates??0,  'sub'=>'template aktif',    'color'=>'var(--apple-red)',    'icon'=>'fa-file-code'],
        ['label'=>'Accounts',     'value'=>$totalAccounts??0,   'sub'=>'akun email',        'color'=>'var(--apple-teal)',   'icon'=>'fa-at'],
    ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:10px">
        @foreach($kpiCards as $k)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $k['color'] }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $k['color'] }} 28%,var(--dark-separator));border-radius:16px;padding:18px 20px;position:relative;overflow:hidden">
            <div style="position:absolute;top:12px;right:14px;font-size:1.4rem;opacity:.15;color:{{ $k['color'] }}">
                <i class="fas {{ $k['icon'] }}"></i>
            </div>
            <p style="font-size:0.58rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $k['color'] }};opacity:.85;margin:0">{{ $k['label'] }}</p>
            <p style="font-size:2rem;font-weight:800;color:{{ $k['color'] }};margin:5px 0 2px;line-height:1">{{ number_format($k['value']) }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $k['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Main Tab Container --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;overflow:hidden">

        {{-- Tab Bar --}}
        <div style="display:flex;align-items:stretch;padding:0 20px;border-bottom:1px solid var(--dark-separator);overflow-x:auto;overflow-y:hidden">
            <div style="display:flex;align-items:stretch">
                @php
                $tabs = [
                    'inbox'       => ['icon'=>'fa-inbox',     'label'=>'Mailbox',     'color'=>'var(--apple-orange)', 'count'=>$notifications['inbox']??0],
                    'campaigns'   => ['icon'=>'fa-bullhorn',  'label'=>'Campaigns',   'color'=>'var(--apple-green)',  'count'=>$notifications['campaigns']??0],
                    'subscribers' => ['icon'=>'fa-users',     'label'=>'Subscribers', 'color'=>'var(--apple-purple)','count'=>$notifications['subscribers']??0],
                    'templates'   => ['icon'=>'fa-file-code', 'label'=>'Templates',   'color'=>'var(--apple-red)',    'count'=>0],
                    'accounts'    => ['icon'=>'fa-at',        'label'=>'Accounts',    'color'=>'var(--apple-teal)',   'count'=>0],
                    'settings'    => ['icon'=>'fa-cog',       'label'=>'Settings',    'color'=>'var(--apple-blue)',   'count'=>0],
                ];
                @endphp
                @foreach($tabs as $tabKey => $tab)
                    @php $isActive = $activeTab === $tabKey; @endphp
                    <a href="{{ route('admin.email-management.index', ['tab' => $tabKey]) }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:14px 6px;margin-right:24px;font-size:0.85rem;font-weight:{{ $isActive ? '700' : '500' }};color:{{ $isActive ? $tab['color'] : 'var(--dark-text-secondary)' }};text-decoration:none;border-bottom:2px solid {{ $isActive ? $tab['color'] : 'transparent' }};margin-bottom:-1px;white-space:nowrap"
                       @unless($isActive)onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'"@endunless>
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:{{ $isActive ? 'color-mix(in srgb,'.$tab['color'].' 18%,transparent)' : 'var(--dark-bg-tertiary)' }};color:{{ $isActive ? $tab['color'] : 'var(--dark-text-secondary)' }};font-size:0.75rem">
                            <i class="fas {{ $tab['icon'] }}"></i>
                        </span>
                        {{ $tab['label'] }}
                        @if($tab['count'] > 0)
                            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 5px;border-radius:10px;font-size:0.7rem;font-weight:700;background:{{ $isActive ? $tab['color'] : 'var(--dark-bg-tertiary)' }};color:{{ $isActive ? '#fff' : 'var(--dark-text-secondary)' }}">
                                {{ $tab['count'] > 99 ? '99+' : $tab['count'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tab Content --}}
        <div style="padding:20px">
            @if($activeTab === 'inbox')
                <div id="content-inbox">
                @include('admin.email-management.tabs.inbox')
                </div>
            @elseif($activeTab === 'campaigns')
                @include('admin.email-management.tabs.campaigns')
            @elseif($activeTab === 'subscribers')
                @include('admin.email-management.tabs.subscribers')
            @elseif($activeTab === 'templates')
                @include('admin.email-management.tabs.templates')
            @elseif($activeTab === 'accounts')
                @include('admin.email-management.tabs.accounts')
            @elseif($activeTab === 'settings')
                @include('admin.email-management.tabs.settings')
            @endif
        </div>
    </div>

</div>
@endsection
