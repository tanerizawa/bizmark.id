@extends('layouts.admin')

@section('title', 'AI SDK Status')

@section('content')
<div class="space-y-4">
    {{-- Header --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden">
        <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <p class="admin-hero-subtitle">Monitoring</p>
                <h1 class="admin-hero-title text-white">AI SDK Status</h1>
                <p class="admin-hero-desc">Current configuration and connectivity of all AI providers</p>
            </div>
            <a href="{{ route('admin.ai-settings.index') }}"
               class="admin-btn admin-btn-sm rounded" style="background: rgba(142,142,147,0.25); color: #fff;">
                <i class="fas fa-arrow-left mr-1.5"></i>Back to Settings
            </a>
        </div>
    </section>

    {{-- Provider Cards --}}
    <section class="card-elevated rounded-apple-lg p-4">
        @if(empty($status))
            <div class="text-center py-8">
                <i class="fas fa-cloud" style="font-size:2.5rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                <p style="font-size:0.85rem;color:var(--dark-text-secondary);margin:0">No AI providers configured</p>
            </div>
        @else
            <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
                @foreach($status as $name => $info)
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:10px">
                    {{-- Header --}}
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $info['configured'] ? 'var(--apple-green)' : 'var(--apple-red)' }}"></div>
                            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $name }}</h3>
                        </div>
                        @if($info['is_default'])
                        <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:5px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue)">
                            <i class="fas fa-star" style="font-size:0.55rem"></i>Default
                        </span>
                        @endif
                    </div>

                    {{-- Details --}}
                    <div style="display:flex;flex-direction:column;gap:6px">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:0.72rem;color:var(--dark-text-tertiary)">Driver</span>
                            <span style="font-size:0.75rem;font-weight:600;color:var(--dark-text-primary)">{{ $info['driver'] }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:0.72rem;color:var(--dark-text-tertiary)">Status</span>
                            <span style="display:inline-flex;align-items:center;gap:4px;font-size:0.72rem;font-weight:600;color:{{ $info['configured'] ? 'var(--apple-green)' : 'var(--apple-red)' }}">
                                <i class="fas fa-{{ $info['configured'] ? 'check-circle' : 'times-circle' }}" style="font-size:0.62rem"></i>
                                {{ $info['configured'] ? 'Configured' : 'Missing Key' }}
                            </span>
                        </div>
                        @if($info['url'])
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:0.72rem;color:var(--dark-text-tertiary)">URL</span>
                            <span style="font-size:0.68rem;font-family:monospace;color:var(--dark-text-secondary);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $info['url'] }}">{{ $info['url'] }}</span>
                        </div>
                        @endif
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:0.72rem;color:var(--dark-text-tertiary)">Default Model</span>
                            <span style="font-size:0.72rem;font-weight:600;color:var(--dark-text-primary)">{{ $info['default_model'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
