@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-white">📈 Position Trend</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Histori peringkat untuk: <strong class="text-white">{{ $keyword }}</strong></p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.seo.positions.track') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="keyword" value="{{ $keyword }}">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    🔄 Track Now
                </button>
            </form>
            <a href="{{ route('admin.seo.positions') }}" class="btn-secondary-sm">← Back to Positions</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <p class="text-sm" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Current Status Card -->
        <div class="lg:col-span-1">
            @if($trend['current'])
            <div class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-sm font-semibold mb-4" style="color: rgba(235,235,245,0.55);">Current Status</h3>
                
                <div class="text-center mb-6">
                    @if($trend['current']->position)
                    <p class="text-4xl font-bold text-white">#{{ $trend['current']->position }}</p>
                    <p class="mt-1 text-sm {{ $trend['current']->isOnPageOne() ? 'text-green-400' : 'text-yellow-400' }}">
                        {{ $trend['current']->rank_tier }}
                    </p>
                    @else
                    <p class="text-2xl font-bold" style="color: rgba(255,59,48,1);">Not Ranking</p>
                    @endif
                </div>

                <div class="space-y-3 border-t pt-4" style="border-color: rgba(255,255,255,0.08);">
                    <div class="flex justify-between">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Previous Position</span>
                        <span class="text-sm text-white">{{ $trend['current']->previous_position ? '#'.$trend['current']->previous_position : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Change</span>
                        <span class="text-sm font-semibold {{ $trend['current']->position_change > 0 ? 'text-green-400' : ($trend['current']->position_change < 0 ? 'text-red-400' : 'text-white/50') }}">
                            {{ $trend['current']->change_description }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Data Source</span>
                        <span class="text-sm text-white">{{ $trend['current']->getDataSourceLabel() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Last Tracked</span>
                        <span class="text-sm text-white">{{ $trend['current']->tracked_at->format('d M Y') }}</span>
                    </div>
                    @if($trend['current']->our_url)
                    <div class="pt-2 border-t" style="border-color: rgba(255,255,255,0.05);">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Ranking URL</span>
                        <a href="{{ $trend['current']->our_url }}" target="_blank" class="block text-xs mt-1 truncate" style="color: rgba(10,132,255,1);">
                            {{ $trend['current']->our_url }}
                        </a>
                    </div>
                    @endif
                </div>

                @if($trend['current']->search_volume)
                <div class="mt-4 pt-4 border-t" style="border-color: rgba(255,255,255,0.08);">
                    <div class="flex justify-between">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Search Volume (est.)</span>
                        <span class="text-sm text-white">{{ number_format($trend['current']->search_volume) }}/mo</span>
                    </div>
                    @if($trend['current']->search_intent)
                    <div class="flex justify-between mt-2">
                        <span class="text-xs" style="color: rgba(235,235,245,0.55);">Intent</span>
                        <span class="text-sm text-white capitalize">{{ $trend['current']->search_intent }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Competitors -->
            @if(!empty($trend['current']->top_competitors))
            <div class="card-elevated rounded-apple-xl p-4 mt-4">
                <h3 class="text-sm font-semibold mb-3" style="color: rgba(235,235,245,0.55);">Current Competitors</h3>
                <div class="space-y-2">
                    @foreach(array_slice($trend['current']->top_competitors, 0, 5) as $comp)
                    <div class="flex items-center gap-2 p-2 rounded" style="background: rgba(255,255,255,0.03);">
                        <span class="text-xs font-bold text-white/70">#{{ $comp['position'] ?? '-' }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white truncate">{{ $comp['domain'] ?? 'Unknown' }}</p>
                            @if(!empty($comp['title']))
                            <p class="text-xs truncate" style="color: rgba(235,235,245,0.4);">{{ $comp['title'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            <div class="card-elevated rounded-apple-xl p-5">
                <p class="text-center text-sm" style="color: rgba(235,235,245,0.55);">
                    Belum ada data tracking untuk keyword ini.
                </p>
            </div>
            @endif
        </div>

        <!-- Trend Chart & History -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Trend Chart -->
            @if(!empty($trend['trend']))
            <div class="card-elevated rounded-apple-xl p-4">
                <h3 class="font-semibold text-white mb-4">Position History (Last 30 Days)</h3>
                <div class="h-64" id="keywordTrendChart"></div>
            </div>
            @endif

            <!-- Recent Alerts for this keyword -->
            @if(!empty($trend['alerts']) && $trend['alerts']->isNotEmpty())
            <div class="card-elevated rounded-apple-xl p-4">
                <h3 class="font-semibold text-white mb-4">🔔 Recent Alerts</h3>
                <div class="space-y-2">
                    @foreach($trend['alerts']->take(5) as $alert)
                    <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,255,255,0.04);">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $alert->alert_icon }}</span>
                            <div>
                                <p class="text-sm text-white">{{ $alert->message }}</p>
                                <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.55);">{{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @php
                            $severityColor = match($alert->severity) {
                                \App\Models\RankingAlert::SEVERITY_CRITICAL => 'rgba(255,59,48,1)',
                                \App\Models\RankingAlert::SEVERITY_WARNING => 'rgba(255,214,10,1)',
                                default => 'rgba(10,132,255,1)',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background: {{ $severityColor }}15; color: {{ $severityColor }};">
                            {{ $alert->severity_label }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Full History Table -->
            @if(!empty($trend['trend']))
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.08);">
                    <h3 class="font-semibold text-white">Full History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02);">
                                <th class="px-4 py-3 text-left text-xs font-semibold" style="color: rgba(235,235,245,0.55);">Date</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold" style="color: rgba(235,235,245,0.55);">Position</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold" style="color: rgba(235,235,245,0.55);">Change</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: rgba(255,255,255,0.05);">
                            @foreach(array_reverse($trend['trend']) as $point)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-2 text-sm text-white">{{ $point['date'] }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($point['position'])
                                    <span class="text-sm font-bold text-white">#{{ $point['position'] }}</span>
                                    @else
                                    <span class="text-xs" style="color: rgba(235,235,245,0.4);">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if($point['change'] > 0)
                                    <span class="text-xs font-semibold text-green-400">↑ +{{ $point['change'] }}</span>
                                    @elseif($point['change'] < 0)
                                    <span class="text-xs font-semibold text-red-400">↓ {{ $point['change'] }}</span>
                                    @else
                                    <span class="text-xs" style="color: rgba(235,235,245,0.4);">→</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(!empty($trend['trend']))
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const trendData = @json($trend['trend']);
    
    new ApexCharts(document.querySelector('#keywordTrendChart'), {
        chart: {
            type: 'line',
            height: 256,
            background: 'transparent',
            toolbar: { show: false },
        },
        series: [{
            name: 'Position',
            data: trendData.map(d => d.position)
        }],
        xaxis: {
            categories: trendData.map(d => d.date),
            labels: { 
                style: { colors: 'rgba(235,235,245,0.55)', fontSize: '10px' },
                rotate: -45,
                rotateAlways: trendData.length > 10
            }
        },
        yaxis: {
            reversed: true,
            min: 1,
            labels: { 
                style: { colors: 'rgba(235,235,245,0.55)' },
                formatter: v => v ? '#' + Math.round(v) : 'N/A'
            }
        },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#0a84ff'],
        markers: {
            size: 4,
            colors: ['#0a84ff'],
            strokeColors: '#fff',
            strokeWidth: 1
        },
        grid: { borderColor: 'rgba(255,255,255,0.05)' },
        tooltip: {
            theme: 'dark',
            y: { formatter: v => v ? '#' + v : 'Not Ranking' }
        },
        annotations: {
            yaxis: [{
                y: 10,
                borderColor: 'rgba(52,199,89,0.5)',
                label: {
                    borderColor: 'rgba(52,199,89,0.5)',
                    style: { color: '#fff', background: 'rgba(52,199,89,0.3)' },
                    text: 'Page 1 Threshold'
                }
            }]
        }
    }).render();
</script>
@endif
@endsection
