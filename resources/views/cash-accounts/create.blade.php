@extends('layouts.admin')
@section('title', 'Tambah Akun Kas')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;top:-55px;right:-25px;background:color-mix(in srgb,var(--apple-green) 16%,transparent);filter:blur(52px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Cash Accounts</p>
                <h1 style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-plus" style="color:var(--apple-green);font-size:0.82rem"></i></span>
                    Tambah Akun Kas
                </h1>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Buat akun bank atau kas baru</p>
            </div>
            <a href="{{ route('cash-accounts.index') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div style="border-radius:10px;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent)">
        <p style="font-size:0.85rem;font-weight:600;color:var(--apple-red);margin:0 0 6px;display:flex;align-items:center;gap:6px"><i class="fas fa-exclamation-circle"></i>Terdapat kesalahan:</p>
        <ul style="margin:0;padding-left:18px">@foreach($errors->all() as $err)<li style="font-size:0.82rem;color:var(--apple-red)">{{ $err }}</li>@endforeach</ul>
    </div>
    @endif

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">
        {{-- Form --}}
        <form action="{{ route('cash-accounts.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-university" style="color:var(--apple-green);font-size:0.72rem"></i></span>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Akun</h2>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama Akun <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="account_name" required maxlength="255" value="{{ old('account_name') }}"
                               placeholder="Contoh: Bank BTN Operasional"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('account_name') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        @error('account_name')<p style="font-size:0.75rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tipe Akun <span style="color:var(--apple-red)">*</span></label>
                        <select name="account_type" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none" onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <option value="">Pilih tipe akun...</option>
                            <option value="bank" {{ old('account_type') == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="cash" {{ old('account_type') == 'cash' ? 'selected' : '' }}>Kas Tunai</option>
                            <option value="receivable" {{ old('account_type') == 'receivable' ? 'selected' : '' }}>Piutang</option>
                            <option value="payable" {{ old('account_type') == 'payable' ? 'selected' : '' }}>Hutang</option>
                        </select>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Bank: Rekening bank | Kas: Uang tunai | Piutang: Uang yang akan diterima | Hutang: Kewajiban</p>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama Bank</label>
                            <input type="text" name="bank_name" maxlength="255" value="{{ old('bank_name') }}"
                                   placeholder="Bank Tabungan Negara"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Hanya untuk tipe Bank</p>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nomor Rekening</label>
                            <input type="text" name="account_number" maxlength="100" value="{{ old('account_number') }}"
                                   placeholder="1234567890"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Saldo Awal (Rp) <span style="color:var(--apple-red)">*</span></label>
                        <input type="number" name="initial_balance" required min="0" step="0.01" value="{{ old('initial_balance', 0) }}"
                               placeholder="0"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Saldo awal akan menjadi saldo saat ini. Transaksi selanjutnya akan mengubah saldo saat ini.</p>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Catatan</label>
                        <textarea name="notes" rows="3" placeholder="Catatan atau deskripsi akun..."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
                <a href="{{ route('cash-accounts.index') }}"
                   style="padding:9px 20px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-times" style="margin-right:6px"></i>Batal
                </a>
                <button type="submit" style="padding:9px 24px;border:none;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save" style="margin-right:6px"></i>Simpan Akun
                </button>
            </div>
        </form>

        {{-- Sidebar --}}
        <div style="background:var(--dark-bg-secondary);border:1px solid color-mix(in srgb,var(--apple-blue) 22%,var(--dark-separator));border-radius:16px;overflow:hidden;position:sticky;top:16px">
            <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
                <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Penting</h3>
            </div>
            <div style="padding:16px 18px;display:flex;flex-direction:column;gap:10px">
                <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0">Akun akan otomatis diatur sebagai aktif. Saldo akan berubah otomatis saat ada transaksi pembayaran atau pengeluaran.</p>
                @foreach([['fa-building-columns','var(--apple-blue)','Gunakan nama deskriptif agar mudah dikenali'],['fa-coins','var(--apple-yellow)','Isi saldo awal sesuai saldo rekening saat ini'],['fa-lock','var(--apple-green)','Saldo diupdate otomatis dari setiap transaksi']] as $tip)
                <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                    <i class="fas {{ $tip[0] }}" style="color:{{ $tip[1] }};flex-shrink:0;font-size:0.78rem;margin-top:2px"></i>
                    <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0;line-height:1.4">{{ $tip[2] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
