@extends('layouts.admin')
@section('title', 'Sub-Layanan: ' . ($parent['title'] ?? ''))
@section('page-title', 'Sub-Layanan')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

@if(session('success'))
    <div style="padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px;display:flex;align-items:center;gap:10px"><i class="fas fa-check-circle" style="color:var(--apple-green)"></i><span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ session('success') }}</span></div>
@endif

<div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
    <a href="{{ route('admin.services.edit', $parentSlug) }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;padding:7px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px"
       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
        <i class="fas fa-arrow-left"></i>Kembali ke Edit
    </a>
    <div style="flex:1">
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Sub-Layanan dari</p>
        <h2 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">{{ $parent['title'] ?? $parentSlug }}</h2>
    </div>
    <a href="{{ route('admin.services.sub.create', $parentSlug) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:8px;text-decoration:none"
       onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
        <i class="fas fa-plus"></i>Tambah Sub-Layanan
    </a>
</div>

<div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
    <div style="padding:14px 20px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;justify-content:space-between">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Data</p>
            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">{{ count($subServices) }} Sub-Layanan</h3>
        </div>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:separate;border-spacing:0">
            <thead>
                <tr>
                    <th style="padding:10px 16px;text-align:left;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--dark-separator);background:var(--dark-bg-tertiary);width:40px"></th>
                    <th style="padding:10px 16px;text-align:left;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--dark-separator);background:var(--dark-bg-tertiary)">Judul</th>
                    <th style="padding:10px 16px;text-align:left;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--dark-separator);background:var(--dark-bg-tertiary)">Durasi</th>
                    <th style="padding:10px 16px;text-align:right;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--dark-separator);background:var(--dark-bg-tertiary);width:100px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subServices as $subSlug => $sub)
                <tr style="transition:background .15s" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:12px 16px;border-bottom:1px solid rgba(84,84,88,.12);text-align:center">
                        <span style="color:var(--dark-text-secondary);font-size:0.9rem"><i class="fas {{ $sub['icon'] ?? 'fa-layer-group' }}"></i></span>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid rgba(84,84,88,.12)">
                        <div style="font-weight:600;font-size:0.875rem;color:var(--dark-text-primary)">{{ $sub['title'] ?? '-' }}</div>
                        <div style="font-family:monospace;font-size:0.72rem;color:var(--dark-text-secondary)">{{ $subSlug }}</div>
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid rgba(84,84,88,.12);font-size:0.8rem;color:var(--dark-text-secondary)">
                        {{ $sub['duration'] ?? '-' }}
                    </td>
                    <td style="padding:12px 16px;border-bottom:1px solid rgba(84,84,88,.12);text-align:right">
                        <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center">
                            <a href="{{ route('admin.services.sub.edit', [$parentSlug, $subSlug]) }}"
                               style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);text-decoration:none"
                               onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                <i class="fas fa-pencil-alt"></i>Edit
                            </a>
                            <form method="POST" action="{{ route('admin.services.sub.destroy', [$parentSlug, $subSlug]) }}" style="display:inline"
                                  onsubmit="return confirm('Hapus sub-layanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="display:inline-flex;align-items:center;gap:4px;font-size:0.75rem;font-weight:600;color:var(--apple-red);background:none;border:none;cursor:pointer;padding:0"
                                        onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:56px 20px;text-align:center;color:var(--dark-text-secondary)">
                        <i class="fas fa-sitemap" style="font-size:2rem;display:block;margin-bottom:12px;opacity:0.25"></i>
                        <span style="font-size:0.875rem">Belum ada sub-layanan.</span>
                        <a href="{{ route('admin.services.sub.create', $parentSlug) }}"
                           style="display:inline-flex;align-items:center;gap:5px;font-size:0.875rem;font-weight:600;color:var(--apple-blue);text-decoration:none;margin-left:6px">Tambah sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
