@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-lg font-bold text-white">🔔 Ranking Alerts</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Notifikasi perubahan peringkat keyword</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.seo.alerts.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-apple text-xs font-semibold transition" style="background: rgba(255,255,255,0.06); color: rgba(235,235,245,0.8); border: 1px solid rgba(255,255,255,0.1);">
                    ✓ Mark All Read
                </button>
            </form>
            <a href="{{ route('admin.seo.positions') }}" class="btn-secondary-sm">📊 Positions</a>
            <a href="{{ route('admin.seo.command-center') }}" class="btn-secondary-sm">← Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <p class="text-sm" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <a href="{{ route('admin.seo.alerts') }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ !$filter || $filter === 'all' ? 'ring-1 ring-blue-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total (7 Days)</p>
            <p class="text-lg font-bold text-white mt-1">{{ $summary['total'] }}</p>
        </a>
        <a href="{{ route('admin.seo.alerts', ['filter' => 'unread']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ $filter === 'unread' ? 'ring-1 ring-blue-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Unread</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(10,132,255,1);">{{ $summary['unread'] }}</p>
        </a>
        <a href="{{ route('admin.seo.alerts', ['severity' => 'critical']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ $severity === 'critical' ? 'ring-1 ring-red-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Critical</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,59,48,1);">{{ $summary['critical'] }}</p>
        </a>
        <a href="{{ route('admin.seo.alerts', ['severity' => 'warning']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ $severity === 'warning' ? 'ring-1 ring-yellow-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Warnings</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,214,10,1);">{{ $summary['warnings'] }}</p>
        </a>
        <a href="{{ route('admin.seo.alerts', ['filter' => 'drops']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ $filter === 'drops' ? 'ring-1 ring-red-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Drops</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(255,59,48,1);">{{ $summary['drops'] }}</p>
        </a>
        <a href="{{ route('admin.seo.alerts', ['filter' => 'gains']) }}" class="card-elevated rounded-apple-lg p-5 hover:bg-white/5 transition {{ $filter === 'gains' ? 'ring-1 ring-green-500/50' : '' }}">
            <p class="text-xs uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Gains</p>
            <p class="text-lg font-bold mt-1" style="color: rgba(52,199,89,1);">{{ $summary['gains'] }}</p>
        </a>
    </div>

    <!-- Alerts List -->
    <div class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="background: rgba(255,255,255,0.02);">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Alert</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Severity</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Position</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Time</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.55);">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: rgba(255,255,255,0.05);">
                    @forelse($alerts as $alert)
                    <tr class="hover:bg-white/5 transition {{ !$alert->is_read ? 'bg-blue-500/5' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-start gap-3">
                                <span class="text-lg">{{ $alert->alert_icon }}</span>
                                <div>
                                    <p class="text-sm font-medium text-white">
                                        {{ $alert->message }}
                                    </p>
                                    <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.55);">
                                        {{ $alert->alert_type_label }} • 
                                        <a href="{{ route('admin.seo.positions.trend', urlencode($alert->keyword)) }}" class="hover:underline" style="color: rgba(10,132,255,1);">
                                            View Trend →
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $severityStyles = match($alert->severity) {
                                    \App\Models\RankingAlert::SEVERITY_CRITICAL => ['bg' => 'rgba(255,59,48,0.15)', 'color' => 'rgba(255,59,48,1)'],
                                    \App\Models\RankingAlert::SEVERITY_WARNING => ['bg' => 'rgba(255,214,10,0.15)', 'color' => 'rgba(255,214,10,1)'],
                                    default => ['bg' => 'rgba(10,132,255,0.15)', 'color' => 'rgba(10,132,255,1)'],
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background: {{ $severityStyles['bg'] }}; color: {{ $severityStyles['color'] }};">
                                {{ $alert->severity_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1">
                                @if($alert->old_position)
                                <span class="text-xs" style="color: rgba(235,235,245,0.55);">#{{ $alert->old_position }}</span>
                                <span class="text-xs" style="color: rgba(235,235,245,0.35);">→</span>
                                @endif
                                @if($alert->new_position)
                                <span class="text-sm font-bold text-white">#{{ $alert->new_position }}</span>
                                @else
                                <span class="text-xs" style="color: rgba(255,59,48,1);">Lost</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-xs" style="color: rgba(235,235,245,0.55);">
                            {{ $alert->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(!$alert->is_read)
                            <form action="{{ route('admin.seo.alerts.read', $alert) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 rounded text-xs font-medium transition hover:bg-white/10" style="color: rgba(10,132,255,1);">
                                    Mark Read
                                </button>
                            </form>
                            @else
                            <span class="text-xs" style="color: rgba(235,235,245,0.35);">Read</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm" style="color: rgba(235,235,245,0.55);">
                            @if($filter || $severity !== 'all')
                            Tidak ada alert dengan filter ini.
                            @else
                            Belum ada ranking alerts. Sistem akan membuat alert saat ada perubahan signifikan.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($alerts->hasPages())
        <div class="px-4 py-3 border-t" style="border-color: rgba(255,255,255,0.08);">
            {{ $alerts->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Recent Critical Alerts (if not already filtered) -->
    @if((!$severity || $severity === 'all') && !empty($summary['recent_critical']))
    <div class="card-elevated rounded-apple-xl p-4 mt-4">
        <h3 class="font-semibold mb-3" style="color: rgba(255,59,48,1);">⚠️ Recent Critical Alerts (Unread)</h3>
        <div class="space-y-2">
            @foreach($summary['recent_critical'] as $critical)
            <div class="flex items-center justify-between p-3 rounded-apple" style="background: rgba(255,59,48,0.08);">
                <div>
                    <span class="text-sm text-white">{{ $critical['message'] }}</span>
                    <p class="text-xs mt-0.5" style="color: rgba(235,235,245,0.55);">{{ \Carbon\Carbon::parse($critical['created_at'])->diffForHumans() }}</p>
                </div>
                <form action="{{ route('admin.seo.alerts.read', $critical['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-medium" style="color: rgba(10,132,255,1);">Mark Read</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
