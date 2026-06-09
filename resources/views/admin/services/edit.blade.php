@extends('layouts.admin')
@section('title', 'Edit Layanan: ' . ($service['title'] ?? ''))
@section('page-title', 'Edit Layanan')

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
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <a href="{{ route('admin.services.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;padding:7px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
        <div style="flex:1">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Edit Layanan</p>
            <h2 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">{{ $service['title'] ?? $slug }}</h2>
        </div>
        <a href="{{ route('admin.services.sub.index', $slug) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-sitemap"></i>Kelola Sub-Layanan
        </a>
        <a href="{{ url('/layanan/' . $slug) }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:9px;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-external-link-alt"></i>Lihat Publik
        </a>
    </div>

    {{-- Form --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:24px">
        <form method="POST" action="{{ route('admin.services.update', $slug) }}">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div style="padding:12px 16px;background:rgba(255,59,48,.1);border:1px solid rgba(255,59,48,.3);border-radius:10px;margin-bottom:18px">
                <p style="font-size:0.82rem;font-weight:600;color:var(--apple-red);margin:0 0 6px"><i class="fas fa-exclamation-circle" style="margin-right:6px"></i>Terdapat kesalahan:</p>
                <ul style="margin:0;padding-left:18px">
                    @foreach($errors->all() as $err)
                    <li style="font-size:0.82rem;color:var(--apple-red)">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @include('admin.services._form')

            <div style="display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid var(--dark-separator)">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;font-size:0.85rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;cursor:pointer"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save"></i>Perbarui Layanan
                </button>
                <a href="{{ route('admin.services.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;font-size:0.85rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:10px;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Danger zone --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid rgba(255,59,48,.25);border-radius:14px;padding:18px">
        <p style="font-size:0.82rem;font-weight:700;color:var(--apple-red);margin:0 0 6px"><i class="fas fa-exclamation-triangle" style="margin-right:6px"></i>Zona Bahaya</p>
        <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 14px">Menghapus layanan ini akan menghapus semua data terkait, termasuk sub-layanan. Aksi ini <strong style="color:var(--dark-text-primary)">tidak dapat diurungkan</strong>.</p>
        <form method="POST" action="{{ route('admin.services.destroy', $slug) }}"
              onsubmit="return confirm('Hapus layanan \'{{ addslashes($service['title'] ?? $slug) }}\'? Aksi ini tidak dapat diurungkan.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.82rem;font-weight:600;background:rgba(255,59,48,.12);color:var(--apple-red);border:1px solid rgba(255,59,48,.3);border-radius:9px;cursor:pointer"
                    onmouseover="this.style.background='rgba(255,59,48,.2)'" onmouseout="this.style.background='rgba(255,59,48,.12)'">
                <i class="fas fa-trash"></i>Hapus Layanan Ini
            </button>
        </form>
    </div>

</div>
@endsection
