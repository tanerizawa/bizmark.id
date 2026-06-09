@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-white">📋 SEO Reports</h1>
            <p class="mt-1 text-sm" style="color: rgba(235,235,245,0.6);">Laporan performa SEO otomatis (weekly/monthly)</p>
        </div>
        <a href="{{ route('admin.seo.dashboard') }}" class="btn-secondary-sm">← Dashboard</a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <pre class="text-sm whitespace-pre-wrap font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</pre>
    </div>
    @endif

    <div class="card-elevated rounded-apple-xl overflow-x-auto">
        <table class="min-w-full">
            <thead style="background: rgba(28,28,30,0.45);">
                <tr>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Period</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Views</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Growth</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Artikel Baru</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Alerts</th>
                    <th class="px-4 py-3 text-left text-xs uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                    <td class="px-4 py-3">
                        @php
                            $pBg = $report->period === 'weekly' ? 'rgba(10,132,255,0.15)' : 'rgba(175,82,222,0.15)';
                            $pColor = $report->period === 'weekly' ? 'rgba(10,132,255,1)' : 'rgba(175,82,222,1)';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-apple" style="background: {{ $pBg }}; color: {{ $pColor }};">
                            {{ ucfirst($report->period) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235,235,245,0.6);">
                        {{ $report->period_start->format('d M') }} — {{ $report->period_end->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-white">
                        {{ number_format($report->metrics['period_views'] ?? 0) }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @php $growth = $report->metrics['views_growth_pct'] ?? 0; @endphp
                        <span class="font-medium" style="color: {{ $growth >= 0 ? 'rgba(52,199,89,1)' : 'rgba(255,59,48,1)' }};">
                            {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm" style="color: rgba(235,235,245,0.6);">
                        {{ $report->metrics['new_articles'] ?? 0 }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                        @if(!empty($report->alerts))
                            <span class="font-medium" style="color: rgba(255,214,10,1);">{{ count($report->alerts) }}</span>
                        @else
                            <span style="color: rgba(52,199,89,1);">✓</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.seo.report-detail', $report->id) }}" class="text-sm" style="color: rgba(10,132,255,1);">Detail →</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center" style="background: rgba(10,132,255,0.1);">
                                <span class="text-2xl">📋</span>
                            </div>
                            <p class="text-sm font-medium text-white">Belum ada report</p>
                            <p class="text-xs" style="color: rgba(235,235,245,0.55);">Generate laporan performa SEO mingguan atau bulanan.</p>
                            <div class="flex gap-2">
                                <form action="{{ route('admin.seo.run-generate-report') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="weekly">
                                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-apple text-sm font-semibold transition" style="background: rgba(10,132,255,0.9); color: #fff;">
                                        📋 Generate Weekly Report
                                    </button>
                                </form>
                                <form action="{{ route('admin.seo.run-generate-report') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="monthly">
                                    <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 rounded-apple text-sm font-semibold transition" style="background: rgba(175,82,222,0.15); color: rgba(175,82,222,1); border: 1px solid rgba(175,82,222,0.3);">
                                        📊 Generate Monthly Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $reports->links() }}</div>
</div>
@endsection
