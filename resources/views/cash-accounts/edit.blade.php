@extends('layouts.admin')
@section('title', 'Edit Akun - ' . $cashAccount->account_name)
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;top:-55px;right:-25px;background:color-mix(in srgb,var(--apple-orange) 16%,transparent);filter:blur(52px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Cash Accounts</p>
                <h1 style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-edit" style="color:var(--apple-orange);font-size:0.82rem"></i></span>
                    Edit: {{ $cashAccount->account_name }}
                </h1>
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Update informasi akun kas atau rekening bank</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <a href="{{ route('cash-accounts.show', $cashAccount) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-eye" style="font-size:0.75rem"></i>Lihat Mutasi
                </a>
                <a href="{{ route('cash-accounts.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
                </a>
            </div>
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
        <form action="{{ route('cash-accounts.update', $cashAccount) }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
            @csrf
            @method('PUT')
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-university" style="color:var(--apple-orange);font-size:0.72rem"></i></span>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Akun</h2>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama Akun <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="account_name" required maxlength="255" value="{{ old('account_name', $cashAccount->account_name) }}"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('account_name') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        @error('account_name')<p style="font-size:0.75rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tipe Akun <span style="color:var(--apple-red)">*</span></label>
                        <select name="account_type" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none" onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <option value="bank" {{ old('account_type', $cashAccount->account_type) == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="cash" {{ old('account_type', $cashAccount->account_type) == 'cash' ? 'selected' : '' }}>Kas Tunai</option>
                            <option value="receivable" {{ old('account_type', $cashAccount->account_type) == 'receivable' ? 'selected' : '' }}>Piutang</option>
                            <option value="payable" {{ old('account_type', $cashAccount->account_type) == 'payable' ? 'selected' : '' }}>Hutang</option>
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama Bank</label>
                            <input type="text" name="bank_name" maxlength="255" value="{{ old('bank_name', $cashAccount->bank_name) }}"
                                   placeholder="Bank Tabungan Negara"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nomor Rekening</label>
                            <input type="text" name="account_number" maxlength="100" value="{{ old('account_number', $cashAccount->account_number) }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Saldo Saat Ini</label>
                        <input type="text" value="Rp {{ number_format($cashAccount->current_balance ?? 0, 0, ',', '.') }}" disabled
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.85rem;outline:none;box-sizing:border-box;opacity:.7;cursor:not-allowed">
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Saldo saat ini tidak bisa diubah secara manual. Saldo diupdate otomatis dari transaksi.</p>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Status Akun</label>
                        <div style="display:flex;gap:20px">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.85rem;color:var(--dark-text-primary)">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $cashAccount->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                       style="accent-color:var(--apple-green);width:16px;height:16px">Aktif
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.85rem;color:var(--dark-text-primary)">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $cashAccount->is_active ? '1' : '0') == '0' ? 'checked' : '' }}
                                       style="accent-color:var(--apple-red);width:16px;height:16px">Non-aktif
                            </label>
                        </div>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0">Akun non-aktif tidak dapat menerima transaksi baru</p>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Catatan</label>
                        <textarea name="notes" rows="3" style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box" onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes', $cashAccount->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between">
                <a href="{{ route('cash-accounts.index') }}"
                   style="padding:9px 20px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-times" style="margin-right:6px"></i>Batal
                </a>
                <button type="submit" style="padding:9px 24px;border:none;border-radius:10px;background:var(--apple-orange);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save" style="margin-right:6px"></i>Simpan Perubahan
                </button>
            </div>
        </form>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:16px">
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-chart-bar" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Ringkasan Transaksi</h3>
                </div>
                <div style="padding:16px 18px;display:flex;flex-direction:column;gap:10px">
                    @php
                        $transactionStats = $cashAccount->transactionStats ?? ['total' => 0, 'income' => 0, 'expense' => 0];
                    @endphp
                    @foreach([['fa-list','var(--apple-blue)','Total Transaksi',$transactionStats['total'] ?? 0],['fa-arrow-down','var(--apple-green)','Pemasukan',$transactionStats['income'] ?? 0],['fa-arrow-up','var(--apple-red)','Pengeluaran',$transactionStats['expense'] ?? 0]] as $stat)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 12px;border-radius:9px;background:var(--dark-bg-tertiary)">
                        <div style="display:flex;align-items:center;gap:8px">
                            <i class="fas {{ $stat[0] }}" style="color:{{ $stat[1] }};font-size:0.78rem"></i>
                            <span style="font-size:0.8rem;color:var(--dark-text-secondary)">{{ $stat[2] }}</span>
                        </div>
                        <span style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary)">{{ is_numeric($stat[3]) && $stat[3] > 99 ? 'Rp ' . number_format($stat[3], 0, ',', '.') : $stat[3] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="padding:14px 16px;border-radius:12px;background:color-mix(in srgb,var(--apple-orange) 8%,transparent);border:1px solid color-mix(in srgb,var(--apple-orange) 18%,transparent)">
                <p style="font-size:0.82rem;font-weight:600;color:var(--apple-orange);margin:0 0 4px;display:flex;align-items:center;gap:6px"><i class="fas fa-exclamation-triangle"></i>Peringatan</p>
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0;line-height:1.4">Tipe akun tidak sebaiknya diubah jika sudah ada transaksi untuk akun ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
