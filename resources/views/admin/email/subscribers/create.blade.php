@extends('layouts.admin')
@section('title', 'Tambah Pelanggan Newsletter')
@section('page-title', 'Tambah Pelanggan')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px;max-width:720px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen Audiens</p>
            <h1 style="font-size:1.2rem;font-weight:700;color:var(--dark-text-primary);margin:4px 0 2px">Tambah Pelanggan Baru</h1>
        </div>
        <a href="{{ route('admin.subscribers.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
        </a>
    </div>

    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:10px;padding:12px 16px;color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
        <span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.subscribers.store') }}" method="POST">
        @csrf

        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px;display:flex;flex-direction:column;gap:16px">

            {{-- Email --}}
            <div>
                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                    Email <span style="color:var(--apple-red)">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="pelanggan@email.com"
                       style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                @error('email')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
            </div>

            {{-- Name --}}
            <div>
                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap"
                       style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
            </div>

            {{-- Phone --}}
            <div>
                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nomor HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xx-xxxx-xxxx"
                       style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                @error('phone')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
            </div>

            {{-- Status --}}
            <div>
                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                    Status <span style="color:var(--apple-red)">*</span>
                </label>
                <select name="status" required
                        style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                        onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }} style="background:#1c1c1e">Aktif</option>
                    <option value="unsubscribed" {{ old('status') === 'unsubscribed' ? 'selected' : '' }} style="background:#1c1c1e">Berhenti Berlangganan</option>
                    <option value="bounced" {{ old('status') === 'bounced' ? 'selected' : '' }} style="background:#1c1c1e">Email Gagal</option>
                </select>
                @error('status')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
            </div>

            {{-- Tags --}}
            <div>
                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                    Tags <span style="font-size:0.65rem;font-weight:400;opacity:.6">(pisahkan dengan koma)</span>
                </label>
                <input type="text" name="tags_input" value="{{ old('tags_input') }}" placeholder="newsletter, promo, pelanggan-baru"
                       style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-purple)'" onblur="this.style.borderColor='var(--dark-separator)'">
                @error('tags')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;gap:10px;margin-top:14px">
            <button type="submit"
                    style="flex:1;padding:10px 18px;background:var(--apple-purple);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:600;cursor:pointer"
                    onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-user-plus" style="margin-right:6px"></i>Simpan Pelanggan
            </button>
            <a href="{{ route('admin.subscribers.index') }}"
               style="padding:10px 18px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Batal</a>
        </div>
    </form>
</div>
@endsection
