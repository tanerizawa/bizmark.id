@extends('layouts.admin')
@section('title', 'Kelola Layanan')
@section('page-title', 'Kelola Layanan')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Alert --}}
    @if(session('success'))
    <div style="padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px;display:flex;align-items:center;gap:10px">
        <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
        <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;top:-60px;right:-30px;background:color-mix(in srgb,var(--apple-teal) 12%,transparent);filter:blur(55px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Service Management</p>
                <h1 style="font-size:1.25rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px"><i class="fas fa-layer-group" style="margin-right:8px;color:var(--apple-teal)"></i>Kelola Layanan</h1>
                <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">{{ count($services) }} layanan aktif</p>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <a href="{{ url('/layanan') }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-external-link-alt"></i>Halaman Publik
                </a>
                <a href="{{ route('admin.services.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:9px;text-decoration:none"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-plus"></i>Tambah Layanan
                </a>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
        <form method="GET">
            <div style="display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:flex-end">
                <div>
                    <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Cari Layanan</label>
                    <div style="display:flex;align-items:center;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;overflow:hidden">
                        <span style="padding:9px 12px;color:var(--dark-text-tertiary);flex-shrink:0"><i class="fas fa-search" style="font-size:0.75rem"></i></span>
                        <input type="text" name="q" value="{{ $filterQ }}" placeholder="Cari judul layanan..."
                               style="flex:1;border:none;background:transparent;padding:9px 12px 9px 0;font-size:0.85rem;color:var(--dark-text-primary);outline:none">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Kategori</label>
                    <select name="category" style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $filterCat == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit"
                            style="padding:9px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:9px;font-size:0.82rem;font-weight:600;cursor:pointer;white-space:nowrap"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-search" style="margin-right:6px"></i>Filter
                    </button>
                    @if(request()->hasAny(['q', 'category']))
                    <a href="{{ route('admin.services.index') }}"
                       style="display:inline-flex;align-items:center;gap:5px;padding:9px 14px;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none;white-space:nowrap"
                       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                        <i class="fas fa-times"></i>Reset
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary);border-bottom:1px solid var(--dark-separator)">
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary);width:48px"></th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Judul</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Kategori</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Harga</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Durasi</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Sub</th>
                        <th style="padding:10px 16px;text-align:right;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary);width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $slug => $service)
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 16px;text-align:center">
                            <div style="width:34px;height:34px;border-radius:10px;background:color-mix(in srgb,{{ $service['color'] ?? '#FF9500' }} 15%,transparent);display:flex;align-items:center;justify-content:center">
                                <i class="fas {{ $service['icon'] ?? 'fa-layer-group' }}" style="font-size:0.85rem;color:{{ $service['color'] ?? 'var(--apple-orange)' }}"></i>
                            </div>
                        </td>
                        <td style="padding:12px 16px">
                            <span style="font-size:0.875rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $service['title'] ?? '-' }}</span>
                            <span style="font-family:monospace;font-size:0.72rem;color:var(--dark-text-tertiary)">/layanan/{{ $slug }}</span>
                            <div style="display:flex;gap:4px;margin-top:4px;flex-wrap:wrap">
                                @if(!empty($service['badge']))
                                <span style="display:inline-flex;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">{{ $service['badge'] }}</span>
                                @endif
                                @if(!empty($service['featured']))
                                <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue)"><i class="fas fa-star" style="font-size:0.6rem"></i>Featured</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding:12px 16px">
                            <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:rgba(255,255,255,.07);color:var(--dark-text-secondary)">{{ $service['category'] ?? '-' }}</span>
                        </td>
                        <td style="padding:12px 16px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $service['price_range'] ?? '-' }}</td>
                        <td style="padding:12px 16px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $service['process_time'] ?? '-' }}</td>
                        <td style="padding:12px 16px">
                            @php $subCount = count($service['sub_services'] ?? []); @endphp
                            @if($subCount > 0)
                            <a href="{{ route('admin.services.sub.index', $slug) }}"
                               style="display:inline-flex;align-items:center;gap:4px;font-size:0.78rem;font-weight:600;color:var(--apple-blue);text-decoration:none"
                               onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-sitemap" style="font-size:0.65rem"></i>{{ $subCount }}
                            </a>
                            @else
                            <a href="{{ route('admin.services.sub.index', $slug) }}"
                               style="font-size:0.78rem;color:var(--dark-text-tertiary);text-decoration:none"
                               onmouseover="this.style.color='var(--apple-blue)'" onmouseout="this.style.color='var(--dark-text-tertiary)'">+ tambah</a>
                            @endif
                        </td>
                        <td style="padding:12px 16px;text-align:right">
                            <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center">
                                <a href="{{ url('/layanan/' . $slug) }}" target="_blank"
                                   style="padding:6px;border-radius:7px;color:var(--dark-text-secondary);font-size:0.85rem;display:inline-flex;align-items:center;text-decoration:none" title="Lihat publik"
                                   onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.services.edit', $slug) }}"
                                   style="padding:6px;border-radius:7px;color:var(--apple-blue);font-size:0.85rem;display:inline-flex;align-items:center;text-decoration:none" title="Edit"
                                   onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy', $slug) }}" style="display:inline"
                                      onsubmit="return confirm('Hapus layanan ini? Aksi ini tidak dapat diurungkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus"
                                            style="padding:6px;border-radius:7px;background:transparent;border:none;cursor:pointer;color:var(--apple-red);font-size:0.85rem"
                                            onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:56px 20px;text-align:center">
                            <i class="fas fa-layer-group" style="font-size:2.5rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                            <p style="font-size:0.95rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum ada layanan</p>
                            <a href="{{ route('admin.services.create') }}"
                               style="display:inline-flex;align-items:center;gap:5px;font-size:0.85rem;font-weight:600;color:var(--apple-blue);text-decoration:none">
                                <i class="fas fa-plus"></i>Tambah sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
