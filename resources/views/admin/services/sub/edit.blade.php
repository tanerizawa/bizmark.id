@extends('layouts.admin')
@section('title', 'Edit Sub-Layanan')
@section('page-title', 'Edit Sub-Layanan')

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
    <div style="display:flex;align-items:center;gap:10px">
        <a href="{{ route('admin.services.sub.index', $parentSlug) }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;padding:7px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Edit Sub-Layanan</p>
            <h2 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">{{ $sub['title'] ?? $subSlug }}</h2>
        </div>
    </div>

    {{-- Form --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:24px">
        <form method="POST" action="{{ route('admin.services.sub.update', [$parentSlug, $subSlug]) }}">
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

            @include('admin.services.sub._form')

            <div style="display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid var(--dark-separator)">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;font-size:0.85rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;cursor:pointer"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save"></i>Perbarui Sub-Layanan
                </button>
                <a href="{{ route('admin.services.sub.index', $parentSlug) }}"
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
        <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 14px">Hapus sub-layanan ini secara permanen.</p>
        {{-- Delete form terpisah — HTML tidak bisa nested form --}}
        <form method="POST" action="{{ route('admin.services.sub.destroy', [$parentSlug, $subSlug]) }}"
              onsubmit="return confirm('Hapus sub-layanan ini?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.82rem;font-weight:600;background:rgba(255,59,48,.12);color:var(--apple-red);border:1px solid rgba(255,59,48,.3);border-radius:9px;cursor:pointer"
                    onmouseover="this.style.background='rgba(255,59,48,.2)'" onmouseout="this.style.background='rgba(255,59,48,.12)'">
                <i class="fas fa-trash"></i>Hapus Sub-Layanan Ini
            </button>
        </form>
    </div>

</div>
@endsection
