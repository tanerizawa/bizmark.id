@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-white"><i class="fas fa-chart-line mr-1.5" style="color: rgba(10,132,255,1);"></i>Search Console Data</h1>
            <p class="mt-0.5 text-xs" style="color: rgba(235,235,245,0.6);">Performa pencarian dari Google Search Console
                @if($lastImport)
                    <span class="ml-1" style="color: rgba(235,235,245,0.4);">&middot; Last sync: {{ \Carbon\Carbon::parse($lastImport)->diffForHumans() }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-1.5">
            <form method="GET" class="flex items-center">
                <select name="days" onchange="this.form.submit()" class="rounded-apple text-xs px-2 py-1.5" style="background: var(--dark-bg-elevated); color: rgba(235,235,245,0.8); border: 1px solid var(--dark-separator);">
                    @foreach([7, 14, 28, 90] as $d)
                        <option value="{{ $d }}" {{ $days == $d ? 'selected' : '' }}>{{ $d }} hari</option>
                    @endforeach
                </select>
            </form>
            <form action="{{ route('admin.seo.search-console.import') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="days" value="7">
                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-semibold transition" style="background: rgba(52,199,89,0.9); color: #fff;" title="Import/sync data dari GSC (atau simulasi)">
                    <i class="fas fa-arrows-rotate"></i> Sync Data
                </button>
            </form>
            <form action="{{ route('admin.seo.search-console.clear') }}" method="POST" class="inline" x-data @submit.prevent="if(confirm('Clear semua data Search Console?')) $el.submit()">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-semibold transition" style="background: rgba(255,69,58,0.15); color: rgba(255,69,58,1); border: 1px solid rgba(255,69,58,0.3);" title="Hapus semua data">
                    <i class="fas fa-trash-can"></i> Clear
                </button>
            </form>
            <a href="{{ route('admin.seo.dashboard') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-medium transition" style="background: rgba(142,142,147,0.15); color: rgba(235,235,245,0.7); border: 1px solid rgba(84,84,88,0.35);"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-3 p-3 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <p class="text-xs font-sans" style="color: rgba(52,199,89,1);">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-3 p-3 rounded-apple-lg" style="background: rgba(255,69,58,0.1); border: 1px solid rgba(255,69,58,0.3);">
        <p class="text-xs font-sans" style="color: rgba(255,69,58,1);">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Summary -->
    <div class="grid grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total Clicks</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(10,132,255,1);">{{ number_format($summary['total_clicks']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Total Impressions</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(175,82,222,1);">{{ number_format($summary['total_impressions']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Avg CTR</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(52,199,89,1);">{{ number_format($summary['avg_ctr'], 1) }}%</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Avg Position</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(255,149,0,1);">{{ number_format($summary['avg_position'], 1) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Unique Queries</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ $summary['unique_queries'] }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Unique Pages</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ $summary['unique_pages'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- Top Queries -->
        <div class="card-elevated rounded-apple-xl p-4">
            <h3 class="text-sm font-semibold text-white mb-3"><i class="fas fa-magnifying-glass mr-1" style="color: rgba(10,132,255,0.8);"></i>Top Queries</h3>
            @if($topQueries->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Query</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[50px]" style="color: rgba(235,235,245,0.6);">Clicks</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[50px]" style="color: rgba(235,235,245,0.6);">Imp</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[45px]" style="color: rgba(235,235,245,0.6);">CTR</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[45px]" style="color: rgba(235,235,245,0.6);">Pos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topQueries as $q)
                        @php
                            $posBg = $q->avg_position <= 3 ? 'rgba(52,199,89,0.15)' : ($q->avg_position <= 10 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                            $posColor = $q->avg_position <= 3 ? 'rgba(52,199,89,1)' : ($q->avg_position <= 10 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="px-2 py-1.5 text-white truncate max-w-[180px]">{{ $q->query }}</td>
                            <td class="px-2 py-1.5 text-right font-medium" style="color: rgba(10,132,255,1);">{{ $q->total_clicks }}</td>
                            <td class="px-2 py-1.5 text-right" style="color: rgba(235,235,245,0.5);">{{ $q->total_impressions }}</td>
                            <td class="px-2 py-1.5 text-right" style="color: rgba(52,199,89,1);">{{ number_format($q->total_impressions > 0 ? ($q->total_clicks / $q->total_impressions) * 100 : 0, 1) }}%</td>
                            <td class="px-2 py-1.5 text-right">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-apple text-[10px] font-semibold"
                                      style="background: {{ $posBg }}; color: {{ $posColor }};">
                                    {{ number_format($q->avg_position, 1) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-6 text-center">
                <i class="fas fa-magnifying-glass text-xl mb-2" style="color: rgba(235,235,245,0.2);"></i>
                <p class="text-xs" style="color: rgba(235,235,245,0.45);">Belum ada data queries. Klik "Sync Data" untuk import.</p>
            </div>
            @endif
        </div>

        <!-- Top Pages -->
        <div class="card-elevated rounded-apple-xl p-4">
            <h3 class="text-sm font-semibold text-white mb-3"><i class="fas fa-file-lines mr-1" style="color: rgba(175,82,222,0.8);"></i>Top Pages</h3>
            @if($topPages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs">
                    <thead style="background: rgba(28,28,30,0.45);">
                        <tr>
                            <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Page</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[50px]" style="color: rgba(235,235,245,0.6);">Clicks</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[50px]" style="color: rgba(235,235,245,0.6);">Imp</th>
                            <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[45px]" style="color: rgba(235,235,245,0.6);">Pos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topPages as $p)
                        @php
                            $ppBg = $p->avg_position <= 3 ? 'rgba(52,199,89,0.15)' : ($p->avg_position <= 10 ? 'rgba(255,214,10,0.15)' : 'rgba(255,59,48,0.15)');
                            $ppColor = $p->avg_position <= 3 ? 'rgba(52,199,89,1)' : ($p->avg_position <= 10 ? 'rgba(255,214,10,1)' : 'rgba(255,59,48,1)');
                            $pagePath = str_replace(url('/'), '', $p->page_url) ?: '/';
                        @endphp
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            <td class="px-2 py-1.5">
                                <a href="{{ $p->page_url }}" target="_blank" class="truncate max-w-[200px] block hover:underline" style="color: rgba(10,132,255,0.8);" title="{{ $p->page_url }}">{{ $pagePath }}</a>
                            </td>
                            <td class="px-2 py-1.5 text-right font-medium" style="color: rgba(10,132,255,1);">{{ $p->total_clicks }}</td>
                            <td class="px-2 py-1.5 text-right" style="color: rgba(235,235,245,0.5);">{{ $p->total_impressions }}</td>
                            <td class="px-2 py-1.5 text-right">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-apple text-[10px] font-semibold"
                                      style="background: {{ $ppBg }}; color: {{ $ppColor }};">
                                    {{ number_format($p->avg_position, 1) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="py-6 text-center">
                <i class="fas fa-file-lines text-xl mb-2" style="color: rgba(235,235,245,0.2);"></i>
                <p class="text-xs" style="color: rgba(235,235,245,0.45);">Belum ada data pages. Klik "Sync Data" untuk import.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Opportunities -->
    <div class="card-elevated rounded-apple-xl p-4">
        <h3 class="text-sm font-semibold text-white mb-1"><i class="fas fa-bullseye mr-1" style="color: rgba(255,159,10,0.8);"></i>Opportunities</h3>
        <p class="text-[10px] mb-3" style="color: rgba(235,235,245,0.45);">High impressions + low CTR (pos 5-20) — easy wins for meta tag optimization</p>
        @if($opportunities->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead style="background: rgba(28,28,30,0.45);">
                    <tr>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Page</th>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Query</th>
                        <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[55px]" style="color: rgba(235,235,245,0.6);">Imp</th>
                        <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[50px]" style="color: rgba(235,235,245,0.6);">Clicks</th>
                        <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[45px]" style="color: rgba(235,235,245,0.6);">CTR</th>
                        <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[45px]" style="color: rgba(235,235,245,0.6);">Pos</th>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($opportunities as $opp)
                    @php
                        $oppPath = str_replace(url('/'), '', $opp->page_url) ?: '/';
                        $avgCtr = round($opp->avg_ctr, 1);
                        $avgPos = round($opp->avg_position, 1);
                        $suggestion = $avgPos <= 10 ? 'Optimize meta title & description' : 'Improve content + add internal links';
                    @endphp
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-2 py-1.5">
                            <a href="{{ $opp->page_url }}" target="_blank" class="truncate max-w-[160px] block hover:underline" style="color: rgba(10,132,255,0.8);" title="{{ $opp->page_url }}">{{ $oppPath }}</a>
                        </td>
                        <td class="px-2 py-1.5" style="color: rgba(235,235,245,0.6);">{{ $opp->query }}</td>
                        <td class="px-2 py-1.5 text-right font-medium" style="color: rgba(175,82,222,1);">{{ number_format($opp->total_impressions) }}</td>
                        <td class="px-2 py-1.5 text-right" style="color: rgba(10,132,255,1);">{{ $opp->total_clicks }}</td>
                        <td class="px-2 py-1.5 text-right" style="color: rgba(255,69,58,1);">{{ $avgCtr }}%</td>
                        <td class="px-2 py-1.5 text-right" style="color: rgba(235,235,245,0.6);">{{ $avgPos }}</td>
                        <td class="px-2 py-1.5">
                            <span class="text-[10px]" style="color: rgba(255,159,10,0.8);">{{ $suggestion }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-5 text-center">
            <i class="fas fa-bullseye text-lg mb-2" style="color: rgba(235,235,245,0.15);"></i>
            <p class="text-xs" style="color: rgba(235,235,245,0.45);">Belum ada peluang terdeteksi. Seiring data bertambah, peluang optimasi CTR akan muncul di sini.</p>
        </div>
        @endif
    </div>
</div>
@endsection
