@extends('layouts.admin')
@section('title', 'SEO Command Center')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Hero --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:22px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:220px;height:220px;border-radius:50%;top:-70px;right:-40px;background:color-mix(in srgb,var(--apple-green) 14%,transparent);filter:blur(60px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">SEO & Analytics</p>
                <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px"><i class="fas fa-rocket" style="margin-right:8px;color:var(--apple-blue)"></i>SEO Command Center</h1>
                <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 12px">Monitor performa SEO, scoring artikel, dan optimasi konten</p>
                <div style="display:flex;flex-wrap:wrap;gap:12px">
                    <span style="font-size:0.78rem;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:5px">
                        <i class="fas fa-file-alt" style="color:var(--apple-blue);font-size:0.72rem"></i>{{ number_format($stats['published_count']) }} artikel
                    </span>
                    <span style="font-size:0.78rem;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:5px">
                        <i class="fas fa-chart-line" style="color:var(--apple-green);font-size:0.72rem"></i>{{ number_format($stats['total_views']) }} views
                    </span>
                    <span style="font-size:0.78rem;color:var(--dark-text-secondary);display:inline-flex;align-items:center;gap:5px">
                        <i class="fas fa-star" style="color:var(--apple-orange);font-size:0.72rem"></i>{{ $stats['avg_seo_score'] }} avg score
                    </span>
                </div>
            </div>
            {{-- Period switcher --}}
            <div style="display:inline-flex;border-radius:10px;overflow:hidden;border:1px solid var(--dark-separator);flex-shrink:0">
                @foreach(['7days' => '7D', '30days' => '30D', '90days' => '90D'] as $key => $label)
                <a href="{{ route('admin.seo.command-center', ['period' => $key]) }}"
                   style="padding:7px 14px;font-size:0.78rem;font-weight:600;text-decoration:none;transition:all .15s;{{ $period === $key ? 'background:var(--apple-blue);color:#fff' : 'background:transparent;color:var(--dark-text-secondary)' }}"
                   onmouseover="if('{{ $period }}'!=='{{ $key }}')this.style.color='var(--dark-text-primary)'"
                   onmouseout="if('{{ $period }}'!=='{{ $key }}')this.style.color='var(--dark-text-secondary)'">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div style="padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px;display:flex;align-items:center;justify-content:space-between;gap:10px">
        <div style="display:flex;align-items:center;gap:8px">
            <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
            <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--dark-text-secondary);padding:2px 4px">
            <i class="fas fa-times" style="font-size:0.75rem"></i>
        </button>
    </div>
    @endif

    {{-- Score Distribution Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
        @php
        $statCards = [
            ['label'=>'Excellent','value'=>$scoreDistribution['excellent']??0,'color'=>'var(--apple-green)','icon'=>'fa-trophy'],
            ['label'=>'Good',     'value'=>$scoreDistribution['good']??0,     'color'=>'var(--apple-blue)', 'icon'=>'fa-thumbs-up'],
            ['label'=>'Average',  'value'=>$scoreDistribution['average']??0,  'color'=>'var(--apple-orange)','icon'=>'fa-minus'],
            ['label'=>'Needs Work','value'=>$scoreDistribution['poor']??0,    'color'=>'var(--apple-red)',  'icon'=>'fa-exclamation-triangle'],
        ];
        @endphp
        @foreach($statCards as $sc)
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:14px;display:flex;align-items:center;gap:12px">
            <div style="width:36px;height:36px;border-radius:10px;background:color-mix(in srgb,{{ $sc['color'] }} 15%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas {{ $sc['icon'] }}" style="color:{{ $sc['color'] }};font-size:0.8rem"></i>
            </div>
            <div>
                <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:{{ $sc['color'] }};margin:0 0 2px">{{ $sc['label'] }}</p>
                <p style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1">{{ $sc['value'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- SEO Modules Grid --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
        <h2 style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px;display:flex;align-items:center;gap:7px">
            <i class="fas fa-compass" style="color:var(--apple-blue);font-size:0.78rem"></i>SEO Modules
        </h2>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">

            @php
            $modules = [
                ['route'=>'admin.seo.scores',         'label'=>'SEO Scores',    'desc'=>'Analisis skor artikel',   'icon'=>'fa-chart-bar',        'color'=>'var(--apple-blue)',   'badge'=>($moduleCounts['low_scores']??0)>0 ? $moduleCounts['low_scores'].' fix' : null,  'badge_red'=>true],
                ['route'=>'admin.seo.competitors',     'label'=>'Competitor',    'desc'=>'Benchmark kompetitor',   'icon'=>'fa-search',           'color'=>'var(--apple-purple)', 'badge'=>($moduleCounts['competitors']??0)>0 ? $moduleCounts['competitors'] : null],
                ['route'=>'admin.seo.ab-tests',        'label'=>'A/B Tests',     'desc'=>'Title & meta testing',   'icon'=>'fa-flask',            'color'=>'var(--apple-green)',  'badge'=>($moduleCounts['active_tests']??0)>0 ? $moduleCounts['active_tests'] : null],
                ['route'=>'admin.seo.search-console',  'label'=>'Search Console','desc'=>'Data GSC',               'icon'=>'fab fa-google',       'color'=>'var(--apple-orange)', 'badge'=>($moduleCounts['search_console']??0)>0 ? number_format($moduleCounts['search_console']) : null],
                ['route'=>'admin.seo.refresh-logs',    'label'=>'Refresh',       'desc'=>'Update konten lama',     'icon'=>'fa-sync-alt',         'color'=>'var(--apple-red)',    'badge'=>($moduleCounts['pending_refresh']??0)>0 ? $moduleCounts['pending_refresh'] : null],
                ['route'=>'admin.seo.programmatic',    'label'=>'Programmatic',  'desc'=>'Landing pages',          'icon'=>'fa-code',             'color'=>'#818CF8',             'badge'=>null],
                ['route'=>'admin.seo.reports',         'label'=>'Reports',       'desc'=>'Laporan SEO',            'icon'=>'fa-file-alt',         'color'=>'var(--dark-text-secondary)', 'badge'=>($moduleCounts['reports']??0)>0 ? $moduleCounts['reports'] : null],
                ['route'=>'admin.seo.dashboard',       'label'=>'Dashboard',     'desc'=>'Charts & trends',        'icon'=>'fa-tachometer-alt',   'color'=>'var(--apple-orange)', 'badge'=>null],
            ];
            @endphp

            @foreach($modules as $mod)
            <a href="{{ route($mod['route']) }}"
               style="position:relative;display:block;padding:14px;border-radius:12px;background:color-mix(in srgb,{{ $mod['color'] }} 10%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,{{ $mod['color'] }} 25%,transparent);text-decoration:none;transition:transform .15s,box-shadow .15s"
               onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 4px 20px rgba(0,0,0,.25)'"
               onmouseout="this.style.transform='scale(1)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                    <div style="width:32px;height:32px;border-radius:9px;background:color-mix(in srgb,{{ $mod['color'] }} 20%,transparent);display:flex;align-items:center;justify-content:center">
                        <i class="{{ str_starts_with($mod['icon'],'fab ') ? $mod['icon'] : 'fas '.$mod['icon'] }}" style="color:{{ $mod['color'] }};font-size:0.8rem"></i>
                    </div>
                    @if($mod['badge'])
                    <span style="padding:2px 7px;border-radius:20px;font-size:0.65rem;font-weight:700;background:{{ ($mod['badge_red']??false) ? 'var(--apple-red)' : 'color-mix(in srgb,'.$mod['color'].' 25%,transparent)' }};color:{{ ($mod['badge_red']??false) ? '#fff' : $mod['color'] }}">{{ $mod['badge'] }}</span>
                    @endif
                </div>
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 3px">{{ $mod['label'] }}</h3>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">{{ $mod['desc'] }}</p>
                <div style="position:absolute;bottom:10px;right:10px;color:color-mix(in srgb,{{ $mod['color'] }} 50%,transparent)">
                    <i class="fas fa-arrow-right" style="font-size:0.6rem"></i>
                </div>
            </a>
            @endforeach

        </div>
    </div>

    {{-- Low Score Articles --}}
    @if($lowScoreArticles->count() > 0)
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <h2 style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary);margin:0;display:flex;align-items:center;gap:7px">
                <i class="fas fa-exclamation-circle" style="color:var(--apple-red);font-size:0.78rem"></i>Perlu Perhatian
                <span style="padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:700;background:rgba(255,59,48,.15);color:var(--apple-red)">{{ $lowScoreArticles->count() }}</span>
            </h2>
            <a href="{{ route('admin.seo.scores', ['sort' => 'score_asc']) }}"
               style="font-size:0.78rem;font-weight:600;color:var(--apple-blue);text-decoration:none;display:inline-flex;align-items:center;gap:5px"
               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                Lihat Semua <i class="fas fa-arrow-right" style="font-size:0.65rem"></i>
            </a>
        </div>
        <div style="display:flex;flex-direction:column;gap:6px">
            @foreach($lowScoreArticles as $score)
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px">
                <a href="{{ route('admin.seo.score-detail', $score->article_id) }}"
                   style="flex:1;min-width:0;font-size:0.85rem;font-weight:500;color:var(--dark-text-primary);text-decoration:none;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block"
                   onmouseover="this.style.color='var(--apple-blue)'" onmouseout="this.style.color='var(--dark-text-primary)'">
                    {{ $score->article->title ?? 'Unknown Article' }}
                </a>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
                    <span style="padding:3px 9px;border-radius:20px;font-size:0.75rem;font-weight:700;background:{{ $score->total_score < 40 ? 'rgba(255,59,48,.2)' : 'rgba(255,149,0,.2)' }};color:{{ $score->total_score < 40 ? 'var(--apple-red)' : 'var(--apple-orange)' }}">
                        {{ round($score->total_score) }}
                    </span>
                    <form action="{{ route('admin.seo.fix-single', $score->article_id) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:0.75rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:7px;cursor:pointer"
                                onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-wrench" style="font-size:0.65rem"></i>Fix
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
