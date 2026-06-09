@extends('layouts.admin')

@section('title', $vacancy->title . ' - Applications')

@section('content')
@php
    $statusLabels = ['pending'=>'Pending','reviewed'=>'Direview','interview'=>'Interview','accepted'=>'Diterima','rejected'=>'Ditolak'];
    $statusColors = ['pending'=>'var(--apple-yellow)','reviewed'=>'var(--apple-blue)','interview'=>'var(--apple-purple)','accepted'=>'var(--apple-green)','rejected'=>'var(--apple-red)'];
@endphp

<div style="display:flex;flex-direction:column;gap:16px">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title, 'url' => route('admin.jobs.show', $vacancy->id)],
        ['label' => 'Applications']
    ]" />

    {{-- Header with Tabs --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px">
            <div>
                <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px">{{ $vacancy->title }}</h1>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">{{ $vacancy->applications_count }} Total Applications</p>
            </div>
            <a href="{{ route('admin.jobs.create') }}" style="display:inline-flex;align-items:center;gap:7px;padding:8px 16px;background:var(--apple-blue);color:#fff;border-radius:10px;font-size:0.78rem;font-weight:700;text-decoration:none" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1"><i class="fas fa-plus"></i>New Job</a>
        </div>
        <x-job-tabs :vacancy="$vacancy" active-tab="applications" />
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
        <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
        <span style="font-size:0.85rem;font-weight:600;color:var(--apple-green)">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Filters --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px 18px">
        <form method="GET" action="{{ route('admin.jobs.applications', $vacancy->id) }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            <div style="flex:1;min-width:200px;position:relative">
                <i class="fas fa-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--dark-text-tertiary);font-size:0.75rem"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, telepon..."
                       style="width:100%;padding:8px 12px 8px 32px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
            </div>
            <div style="position:relative;min-width:150px">
                <select name="status" style="width:100%;padding:8px 30px 8px 10px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;appearance:none;cursor:pointer">
                    <option value="">All Status</option>
                    @foreach($statusLabels as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);pointer-events:none;font-size:0.65rem"></i>
            </div>
            <button type="submit" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:9px;font-size:0.78rem;font-weight:600;cursor:pointer"><i class="fas fa-filter"></i>Filter</button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:color-mix(in srgb,var(--dark-text-tertiary) 15%,transparent);color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:9px;font-size:0.78rem;font-weight:600;text-decoration:none"><i class="fas fa-times"></i>Clear</a>
            @endif
        </form>
    </div>

    {{-- Applications Table --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        @if($applications->isNotEmpty())
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-secondary);border-bottom:1px solid var(--dark-separator)">
                        @foreach(['Candidate','Contact','Applied','Status','Actions'] as $h)
                        <th style="padding:11px 16px;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);text-align:{{ $loop->last ? 'right' : 'left' }}">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $application)
                    @php $col = $statusColors[$application->status] ?? 'var(--dark-text-secondary)'; @endphp
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:10px 16px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:36px;height:36px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.88rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);color:var(--apple-blue)">{{ substr($application->full_name, 0, 1) }}</div>
                                <div>
                                    <p style="font-size:0.83rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $application->full_name }}</p>
                                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">{{ $application->education_level ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:10px 16px">
                            <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0 0 2px">{{ $application->email }}</p>
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $application->phone }}</p>
                        </td>
                        <td style="padding:10px 16px">
                            <p style="font-size:0.8rem;color:var(--dark-text-primary);margin:0 0 2px">{{ $application->created_at->format('d M Y') }}</p>
                            <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0">{{ $application->created_at->diffForHumans() }}</p>
                        </td>
                        <td style="padding:10px 16px"><span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $col }} 15%,transparent);color:{{ $col }}">{{ $statusLabels[$application->status] ?? ucfirst($application->status) }}</span></td>
                        <td style="padding:10px 16px;text-align:right">
                            <div style="display:flex;justify-content:flex-end;gap:6px">
                                <a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none"><i class="fas fa-stream" style="font-size:0.6rem"></i>Pipeline</a>
                                <a href="{{ route('admin.applications.show', $application->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-secondary);padding:4px 9px;border-radius:6px;border:1px solid var(--dark-separator);text-decoration:none"><i class="fas fa-file-alt" style="font-size:0.6rem"></i>Details</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:12px 16px;border-top:1px solid var(--dark-separator)">{{ $applications->links() }}</div>
        @else
        <div style="padding:40px;text-align:center">
            <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:8px;color:var(--dark-text-tertiary);opacity:.5"></i>
            <p style="font-size:0.85rem;color:var(--dark-text-secondary);margin:0 0 6px">No applications found</p>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.jobs.applications', $vacancy->id) }}" style="font-size:0.75rem;color:var(--apple-blue);text-decoration:none">Clear filters</a>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
