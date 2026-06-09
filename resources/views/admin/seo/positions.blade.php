@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-white">📊 Position Tracking</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Monitor peringkat keyword di Google</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.seo.positions.track') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⏳ Tracking...';">
                @csrf
                <input type="hidden" name="limit" value="30">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.15); color: rgba(52,199,89,1); border: 1px solid rgba(52,199,89,0.3);">
                    🔄 Track Now
                </button>
            </form>
            <a href="{{ route('admin.seo.alerts') }}" class="btn-secondary-sm">🔔 Alerts</a>
            <a href="{{ route('admin.seo.command-center') }}" class="btn-secondary-sm">← Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</pre>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(255,59,48,0.1); border: 1px solid rgba(255,59,48,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(255,59,48,1);">{{ session('error') }}</pre>
    </div>
    @endif

    <!-- Track Custom Keyword -->
    <div class="mb-6 card-elevated rounded-apple-xl p-4">
        <form action="{{ route('admin.seo.positions.track') }}" method="POST" class="flex flex-wrap items-end gap-3" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').textContent='⏳ Tracking...';">
            @csrf
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-semibold mb-1.5" style="color: rgba(235,235,245,0.55);">Track Keyword Spesifik</label>
                <input type="text" name="keyword" required minlength="3" maxlength="200"
                       placeholder="contoh: jasa pengurusan AMDAL"
                       class="w-full px-3 py-2 rounded-apple text-sm text-white placeholder-white/30"
                       style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1);">
            </div>
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-apple text-sm font-semibold transition" style="background: rgba(10,132,255,0.9); color: #fff;">
                📊 Track
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Tracked Today</p>
            <p class="text-lg font-bold text-white mt-1">{{ $recentTracking['today'] }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Top 3</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(52,199,89,1);">{{ $tierDistribution['top3'] ?? 0 }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Page 1 (4-10)</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(10,132,255,1);">{{ $tierDistribution['page1'] ?? 0 }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Page 2 (11-20)</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,214,10,1);">{{ $tierDistribution['page2'] ?? 0 }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Page 3+</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,159,10,1);">{{ ($tierDistribution['page3'] ?? 0) + ($tierDistribution['beyond'] ?? 0) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-5">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Not Ranking</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,59,48,1);">{{ $tierDistribution['notranking'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Alert Summary -->
    @if(!empty($summary['alert_summary']) && $summary['alert_summary']['unread'] > 0)
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(255,214,10,0.1); border: 1px solid rgba(255,214,10,0.3);">
        <div class="flex items-center justify-between">
            <div>
                <span class="font-semibold" style="color: rgba(255,214,10,1);">🔔 {{ $summary['alert_summary']['unread'] }} Unread Alerts</span>
                @if($summary['alert_summary']['critical'] > 0)
                <span class="ml-2 px-2 py-0.5 rounded text-xs font-semibold" style="background: rgba(255,59,48,0.2); color: rgba(255,59,48,1);">
                    {{ $summary['alert_summary']['critical'] }} Critical
                </span>
                @endif
            </div>
            <a href="{{ route('admin.seo.alerts') }}" class="text-sm font-medium" style="color: rgba(10,132,255,1);">View All →</a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Big Movers -->
        <div class="lg:col-span-1">
            <div class="card-elevated rounded-apple-xl p-4">
                <h3 class="font-semibold text-white mb-4">🚀 Big Movers (Last {{ $days }} Days)</h3>
                @if($bigMovers->isEmpty())
                <p class="text-sm" style="color: rgba(235,235,245,0.55);">Belum ada perubahan signifikan.</p>
                @else
                <div class="space-y-3">
                    @foreach($bigMovers as $mover)
                    <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,255,255,0.04);">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('admin.seo.positions.trend', urlencode($mover->keyword)) }}" class="text-sm font-medium text-white hover:underline truncate block">
                                {{ Str::limit($mover->keyword, 35) }}
                            </a>
                            <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.55);">
                                #{{ $mover->position ?? 'N/A' }} • {{ $mover->getDataSourceLabel() }}
                            </p>
                        </div>
                        <div class="ml-3 text-right">
                            @if($mover->position_change > 0)
                            <span class="text-sm font-bold" style="color: rgba(52,199,89,1);">↑ +{{ $mover->position_change }}</span>
                            @else
                            <span class="text-sm font-bold" style="color: rgba(255,59,48,1);">↓ {{ $mover->position_change }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- At-Risk Keywords -->
            @if(!empty($summary['at_risk_keywords']))
            <div class="card-elevated rounded-apple-xl p-4 mt-4">
                <h3 class="font-semibold mb-4" style="color: rgba(255,59,48,1);">⚠️ Keywords At Risk</h3>
                <div class="space-y-2">
                    @foreach(array_slice($summary['at_risk_keywords'], 0, 5) as $risk)
                    <div class="flex items-center justify-between p-2 rounded-apple" style="background: rgba(255,59,48,0.08);">
                        <span class="text-sm text-white truncate">{{ Str::limit($risk['keyword'], 30) }}</span>
                        <span class="text-xs font-medium" style="color: rgba(255,59,48,1);">
                            {{ $risk['position_change'] > 0 ? '+' : '' }}{{ $risk['position_change'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Position List -->
        <div class="lg:col-span-2">
            <div class="card-elevated rounded-apple-xl overflow-hidden">
                <div class="p-4 border-b" style="border-color: rgba(255,255,255,0.08);">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-white">All Tracked Keywords</h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.seo.positions', ['period' => '7days']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-medium {{ $period === '7days' ? 'bg-blue-500/20 text-blue-400' : 'text-white/50 hover:text-white' }}">
                                7 Days
                            </a>
                            <a href="{{ route('admin.seo.positions', ['period' => '30days']) }}" 
                               class="px-3 py-1 rounded-full text-xs font-medium {{ $period === '30days' ? 'bg-blue-500/20 text-blue-400' : 'text-white/50 hover:text-white' }}">
                                30 Days
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02);">
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Keyword</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Position</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Change</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Tier</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Tracked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: rgba(255,255,255,0.05);">
                            @forelse($latestPositions as $pos)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.seo.positions.trend', urlencode($pos->keyword)) }}" class="text-sm font-medium text-white hover:underline">
                                        {{ Str::limit($pos->keyword, 50) }}
                                    </a>
                                    @if($pos->our_url)
                                    <p class="text-xs mt-0.5 truncate" style="color: rgba(235,235,245,0.4);">{{ Str::limit($pos->our_url, 60) }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($pos->position)
                                    <span class="text-sm font-bold text-white">#{{ $pos->position }}</span>
                                    @else
                                    <span class="text-xs" style="color: rgba(235,235,245,0.4);">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($pos->position_change > 0)
                                    <span class="inline-flex items-center gap-0.5 text-xs font-semibold" style="color: rgba(52,199,89,1);">
                                        ↑ +{{ $pos->position_change }}
                                    </span>
                                    @elseif($pos->position_change < 0)
                                    <span class="inline-flex items-center gap-0.5 text-xs font-semibold" style="color: rgba(255,59,48,1);">
                                        ↓ {{ $pos->position_change }}
                                    </span>
                                    @else
                                    <span class="text-xs" style="color: rgba(235,235,245,0.4);">→ 0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $tierColor = match($pos->rank_tier) {
                                            'Top 3' => 'rgba(52,199,89,1)',
                                            'Page 1' => 'rgba(10,132,255,1)',
                                            'Page 2' => 'rgba(255,214,10,1)',
                                            'Page 3' => 'rgba(255,159,10,1)',
                                            default => 'rgba(255,59,48,1)',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background: {{ $tierColor }}20; color: {{ $tierColor }};">
                                        {{ $pos->rank_tier }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-xs" style="color: rgba(235,235,245,0.55);">
                                    {{ $pos->tracked_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm" style="color: rgba(235,235,245,0.55);">
                                    Belum ada data position tracking. Klik "Track Now" untuk mulai.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($latestPositions->hasPages())
                <div class="px-4 py-3 border-t" style="border-color: rgba(255,255,255,0.08);">
                    {{ $latestPositions->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Position Trend Chart -->
    @if(!empty($trendData))
    <div class="card-elevated rounded-apple-xl p-4 mt-6">
        <h3 class="font-semibold text-white mb-4">📈 Average Position Trend (Last {{ $days }} Days)</h3>
        <div class="h-64" id="positionTrendChart"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        const trendData = @json($trendData);
        
        new ApexCharts(document.querySelector('#positionTrendChart'), {
            chart: {
                type: 'line',
                height: 256,
                background: 'transparent',
                toolbar: { show: false },
            },
            series: [{
                name: 'Avg Position',
                data: trendData.map(d => d.avg_position)
            }],
            xaxis: {
                categories: trendData.map(d => d.date),
                labels: { style: { colors: 'rgba(235,235,245,0.55)', fontSize: '10px' } }
            },
            yaxis: {
                reversed: true, // Lower position is better
                min: 1,
                labels: { 
                    style: { colors: 'rgba(235,235,245,0.55)' },
                    formatter: v => '#' + Math.round(v)
                }
            },
            stroke: { curve: 'smooth', width: 2 },
            colors: ['#34c759'],
            grid: { borderColor: 'rgba(255,255,255,0.05)' },
            tooltip: {
                theme: 'dark',
                y: { formatter: v => '#' + v.toFixed(1) }
            }
        }).render();
    </script>
    @endif
</div>
@endsection
