@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-white"><i class="fas fa-globe mr-1.5" style="color: rgba(10,132,255,1);"></i>Programmatic SEO</h1>
            <p class="mt-0.5 text-xs" style="color: rgba(235,235,245,0.6);">{{ number_format($stats['total_pages']) }} halaman dari {{ $stats['total_cities'] }} kota &times; {{ $stats['total_services'] }} layanan</p>
        </div>
        <div class="flex items-center gap-1.5">
            <form action="{{ route('admin.seo.programmatic.clear-cache') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-semibold transition" style="background: rgba(255,149,0,0.15); color: rgba(255,149,0,1); border: 1px solid rgba(255,149,0,0.3);" title="Clear cache semua halaman programmatic">
                    <i class="fas fa-broom"></i> Clear Cache
                </button>
            </form>
            <a href="{{ route('admin.seo.dashboard') }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-apple text-xs font-medium transition" style="background: rgba(142,142,147,0.15); color: rgba(235,235,245,0.7); border: 1px solid rgba(84,84,88,0.35);"><i class="fas fa-arrow-left"></i> Dashboard</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-3 p-3 rounded-apple-lg" style="background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3);">
        <p class="text-xs" style="color: rgba(52,199,89,1);"><i class="fas fa-check-circle mr-1"></i>{{ session('success') }}</p>
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Kota Industri</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(10,132,255,1);">{{ $stats['total_cities'] }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Jenis Layanan</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(52,199,89,1);">{{ $stats['total_services'] }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Halaman Layanan</p>
            <p class="text-xl font-bold text-white mt-0.5">{{ number_format($stats['service_location_pages']) }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Halaman Index Kota</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(255,149,0,1);">{{ $stats['city_index_pages'] }}</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <p class="text-[10px] uppercase tracking-wide" style="color: rgba(235,235,245,0.55);">Provinsi</p>
            <p class="text-xl font-bold mt-0.5" style="color: rgba(175,82,222,1);">{{ count($byProvince) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- By Province -->
        <div class="card-elevated rounded-apple-xl p-4">
            <h3 class="text-sm font-semibold text-white mb-3"><i class="fas fa-map-location-dot mr-1" style="color: rgba(10,132,255,0.8);"></i>Kota per Provinsi</h3>
            @if(count($byProvince) > 0)
            @php $maxProv = max(array_values($byProvince->toArray())); @endphp
            <div class="space-y-1.5 max-h-[400px] overflow-y-auto">
                @foreach($byProvince as $province => $count)
                <a href="{{ route('admin.seo.programmatic', ['province' => $province]) }}" class="flex items-center justify-between py-1 px-1 rounded-apple hover:bg-white/5 transition group">
                    <span class="text-xs {{ $provinceFilter === $province ? 'text-white font-semibold' : '' }}" style="{{ $provinceFilter !== $province ? 'color: rgba(235,235,245,0.6);' : '' }}">{{ $province }}</span>
                    <div class="flex items-center gap-1.5">
                        <div class="w-20 h-1.5 rounded-full bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full" style="width: {{ ($count / max(1, $maxProv)) * 100 }}%; background: rgba(10,132,255,1);"></div>
                        </div>
                        <span class="text-[10px] font-medium text-white w-5 text-right">{{ $count }}</span>
                    </div>
                </a>
                @endforeach
            </div>
            @if($provinceFilter)
            <a href="{{ route('admin.seo.programmatic') }}" class="mt-2 block text-center text-[10px] font-medium" style="color: rgba(10,132,255,0.8);"><i class="fas fa-xmark mr-0.5"></i>Reset filter provinsi</a>
            @endif
            @else
            <div class="py-5 text-center">
                <i class="fas fa-map text-lg mb-2" style="color: rgba(235,235,245,0.15);"></i>
                <p class="text-xs" style="color: rgba(235,235,245,0.45);">Belum ada data provinsi.</p>
            </div>
            @endif
        </div>

        <!-- Services List -->
        <div class="card-elevated rounded-apple-xl p-4">
            <h3 class="text-sm font-semibold text-white mb-3"><i class="fas fa-layer-group mr-1" style="color: rgba(52,199,89,0.8);"></i>Layanan ({{ $services->count() }})</h3>
            @if($services->count() > 0)
            <div class="space-y-1.5 max-h-[400px] overflow-y-auto">
                @foreach($services as $svc)
                <div class="flex items-center justify-between p-2 rounded-apple-lg hover:bg-white/5 transition" style="background: rgba(255,255,255,0.02);">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-apple flex items-center justify-center" style="background: {{ $svc['color'] }}20;">
                            <i class="fas {{ $svc['icon'] }}" style="color: {{ $svc['color'] }}; font-size: 10px;"></i>
                        </div>
                        <div>
                            <p class="font-medium text-xs text-white">{{ $svc['title'] }}</p>
                            <p class="text-[10px]" style="color: rgba(235,235,245,0.4);">{{ $stats['total_cities'] }} halaman &middot; {{ $svc['category'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('programmatic.service-location.id', ['serviceSlug' => $svc['slug'], 'citySlug' => 'karawang']) }}" target="_blank" class="w-6 h-6 inline-flex items-center justify-center rounded-apple transition" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);" title="Preview halaman {{ $svc['title'] }}">
                        <i class="fas fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="py-5 text-center">
                <i class="fas fa-layer-group text-lg mb-2" style="color: rgba(235,235,245,0.15);"></i>
                <p class="text-xs" style="color: rgba(235,235,245,0.45);">Belum ada layanan dikonfigurasi.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- All Cities Table -->
    <div class="card-elevated rounded-apple-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-white">
                <i class="fas fa-city mr-1" style="color: rgba(175,82,222,0.8);"></i>
                @if($provinceFilter)
                    Kota di {{ $provinceFilter }} ({{ count($filteredCities) }})
                @else
                    Semua Kota ({{ $stats['total_cities'] }})
                @endif
            </h3>
        </div>
        @if(count($filteredCities) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead style="background: rgba(28,28,30,0.45);">
                    <tr>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Kota</th>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Provinsi</th>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Populasi</th>
                        <th class="px-2 py-1.5 text-left text-[10px] uppercase tracking-widest" style="color: rgba(235,235,245,0.6);">Industri</th>
                        <th class="px-2 py-1.5 text-right text-[10px] uppercase tracking-widest w-[55px]" style="color: rgba(235,235,245,0.6);">Pages</th>
                        <th class="px-2 py-1.5 text-center text-[10px] uppercase tracking-widest w-[55px]" style="color: rgba(235,235,245,0.6);">Preview</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredCities as $city)
                    <tr class="border-b border-white/5 hover:bg-white/5 transition">
                        <td class="px-2 py-1.5">
                            <span class="font-medium text-white">{{ $city['name'] }}</span>
                        </td>
                        <td class="px-2 py-1.5" style="color: rgba(235,235,245,0.5);">{{ $city['province'] }}</td>
                        <td class="px-2 py-1.5" style="color: rgba(10,132,255,0.8);">{{ $city['population'] ?? '—' }}</td>
                        <td class="px-2 py-1.5">
                            <div class="flex flex-wrap gap-0.5">
                                @foreach(array_slice($city['industries'] ?? [], 0, 3) as $ind)
                                    <span class="px-1 py-0.5 rounded text-[9px]" style="background: rgba(52,199,89,0.1); color: rgba(52,199,89,0.8);">{{ $ind }}</span>
                                @endforeach
                                @if(count($city['industries'] ?? []) > 3)
                                    <span class="text-[9px]" style="color: rgba(235,235,245,0.35);">+{{ count($city['industries']) - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-2 py-1.5 text-right font-medium" style="color: rgba(175,82,222,1);">{{ $services->count() + 1 }}</td>
                        <td class="px-2 py-1.5 text-center">
                            <a href="{{ route('programmatic.city.id', ['citySlug' => $city['slug']]) }}" target="_blank" class="w-6 h-6 inline-flex items-center justify-center rounded-apple transition" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,1);" title="Preview index {{ $city['name'] }}">
                                <i class="fas fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-6 text-center">
            <i class="fas fa-city text-lg mb-2" style="color: rgba(235,235,245,0.15);"></i>
            <p class="text-xs" style="color: rgba(235,235,245,0.45);">
                @if($provinceFilter)
                    Tidak ada kota untuk provinsi "{{ $provinceFilter }}".
                    <a href="{{ route('admin.seo.programmatic') }}" class="ml-1" style="color: rgba(10,132,255,0.8);">Reset filter</a>
                @else
                    Belum ada kota dikonfigurasi di programmatic_seo.php.
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endsection
