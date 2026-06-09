@extends('layouts.admin')

@section('title', 'Webhook Metrics')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">Webhook Metrics</h1>
            <p class="text-sm mt-0.5" style="color: rgba(235,235,245,0.6);">
                Email webhook replay protection — cache store: <code class="font-mono text-xs px-1.5 py-0.5 rounded" style="background: rgba(255,255,255,0.08);">{{ $store }}</code>
            </p>
        </div>
        <div class="flex items-center gap-2">
            @foreach([6, 24, 48, 168] as $h)
                <a href="{{ route('admin.security.webhook-metrics', ['hours' => $h]) }}"
                   class="px-3 py-1.5 rounded-apple text-xs font-medium transition-all"
                   style="{{ $hours == $h ? 'background: rgba(10,132,255,0.25); color: #0A84FF; border: 1px solid rgba(10,132,255,0.4);' : 'background: rgba(255,255,255,0.06); color: rgba(235,235,245,0.6); border: 1px solid rgba(255,255,255,0.08);' }}">
                    {{ $h == 168 ? '7 hari' : $h . ' jam' }}
                </a>
            @endforeach
            <a href="{{ route('admin.security.audit-logs') }}"
               class="px-3 py-1.5 rounded-apple text-xs font-medium transition-all"
               style="background: rgba(255,255,255,0.06); color: rgba(235,235,245,0.6); border: 1px solid rgba(255,255,255,0.08);">
                ← Audit Logs
            </a>
        </div>
    </div>

    {{-- Totals Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @php
            $securityMetrics = ['missing_headers','invalid_timestamp','timestamp_out_of_window','invalid_nonce','nonce_reused'];
            $cacheMetrics = ['cache_store_failure','cache_latency_high','cache_get_failure','cache_add_failure','cache_put_failure'];
        @endphp

        @foreach($metricKeys as $key => $label)
            @php
                $total = $totals[$key];
                $isSecurity = in_array($key, $securityMetrics);
                $color = $total > 0 ? ($isSecurity ? '#FF453A' : '#FF9F0A') : '#30D158';
                $bg = $total > 0 ? ($isSecurity ? 'rgba(255,69,58,0.12)' : 'rgba(255,159,10,0.12)') : 'rgba(48,209,88,0.08)';
                $border = $total > 0 ? ($isSecurity ? 'rgba(255,69,58,0.3)' : 'rgba(255,159,10,0.3)') : 'rgba(48,209,88,0.2)';
            @endphp
            <div class="rounded-apple-lg p-3" style="background: {{ $bg }}; border: 1px solid {{ $border }};">
                <div class="text-2xl font-bold font-mono" style="color: {{ $color }};">{{ $total }}</div>
                <div class="text-xs mt-0.5 leading-snug" style="color: rgba(235,235,245,0.55);">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    {{-- Security vs Cache grouping --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Security Events --}}
        <div class="card-elevated rounded-apple-lg p-4">
            <h3 class="text-sm font-semibold mb-3" style="color: #FF453A;">
                <i class="fas fa-shield-alt mr-2"></i>Security Events
            </h3>
            @php
                $securityTotal = collect($securityMetrics)->sum(fn($m) => $totals[$m]);
            @endphp
            @if($securityTotal === 0)
                <div class="flex items-center gap-2 text-sm py-2" style="color: rgba(48,209,88,1);">
                    <i class="fas fa-check-circle"></i>
                    <span>Tidak ada security event dalam {{ $hours }} jam terakhir.</span>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($securityMetrics as $m)
                        @if($totals[$m] > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span style="color: rgba(235,235,245,0.7);">{{ $metricKeys[$m] }}</span>
                                <span class="font-mono font-bold" style="color: #FF453A;">{{ $totals[$m] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Cache Events --}}
        <div class="card-elevated rounded-apple-lg p-4">
            <h3 class="text-sm font-semibold mb-3" style="color: #FF9F0A;">
                <i class="fas fa-database mr-2"></i>Cache Events
            </h3>
            @php
                $cacheTotal = collect($cacheMetrics)->sum(fn($m) => $totals[$m]);
            @endphp
            @if($cacheTotal === 0)
                <div class="flex items-center gap-2 text-sm py-2" style="color: rgba(48,209,88,1);">
                    <i class="fas fa-check-circle"></i>
                    <span>Tidak ada cache event dalam {{ $hours }} jam terakhir.</span>
                </div>
            @else
                <div class="space-y-2">
                    @foreach($cacheMetrics as $m)
                        @if($totals[$m] > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span style="color: rgba(235,235,245,0.7);">{{ $metricKeys[$m] }}</span>
                                <span class="font-mono font-bold" style="color: #FF9F0A;">{{ $totals[$m] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Hourly table --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="px-4 py-3 border-b" style="border-color: rgba(255,255,255,0.06);">
            <h3 class="text-sm font-semibold text-white">Detail per Jam ({{ $hours }} jam terakhir)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr style="background: rgba(255,255,255,0.04);">
                        <th class="px-4 py-2 text-left font-medium" style="color: rgba(235,235,245,0.5);">Jam</th>
                        @foreach($metricKeys as $key => $label)
                            <th class="px-3 py-2 text-center font-medium" style="color: rgba(235,235,245,0.5);">{{ Str::limit($label, 12) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_reverse($rows) as $row)
                        @php
                            $rowTotal = collect(array_keys($metricKeys))->sum(fn($m) => $row[$m]);
                        @endphp
                        <tr class="{{ $loop->even ? '' : '' }}" style="{{ $rowTotal > 0 ? 'background: rgba(255,69,58,0.04);' : '' }} border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="px-4 py-2 font-mono font-medium" style="color: rgba(235,235,245,0.8);">{{ $row['label'] }}</td>
                            @foreach(array_keys($metricKeys) as $m)
                                <td class="px-3 py-2 text-center font-mono" style="color: {{ $row[$m] > 0 ? (in_array($m, $securityMetrics) ? '#FF453A' : '#FF9F0A') : 'rgba(235,235,245,0.25)' }};">
                                    {{ $row[$m] > 0 ? $row[$m] : '—' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
