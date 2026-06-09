@extends('layouts.admin')
@section('title', 'Edit Email Account: ' . $emailAccount->email)
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;top:-55px;right:-25px;background:color-mix(in srgb,var(--apple-blue) 16%,transparent);filter:blur(52px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Accounts</p>
                <h1 style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 4px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-edit" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    </span>
                    Edit: {{ $emailAccount->email }}
                </h1>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <a href="{{ route('admin.email-accounts.show', $emailAccount) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Errors --}}
    @if($errors->any())
    <div style="border-radius:10px;padding:12px 16px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent)">
        <p style="font-size:0.85rem;font-weight:600;color:var(--apple-red);margin:0 0 6px;display:flex;align-items:center;gap:6px"><i class="fas fa-exclamation-circle"></i>Terdapat kesalahan:</p>
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $err)
            <li style="font-size:0.82rem;color:var(--apple-red)">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 2-col Grid --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

        {{-- LEFT: Form --}}
        <form action="{{ route('admin.email-accounts.update', $emailAccount) }}" method="POST" style="display:flex;flex-direction:column;gap:16px">
            @csrf @method('PUT')

            {{-- Informasi Dasar --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Dasar</h2>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Email <span style="color:var(--apple-red)">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $emailAccount->email) }}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Nama Tampilan <span style="color:var(--apple-red)">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $emailAccount->name) }}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tipe</label>
                            <select name="type" onchange="updateTypeHelp(this.value)"
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="shared" {{ old('type', $emailAccount->type) === 'shared' ? 'selected' : '' }}>Shared (Tim)</option>
                                <option value="personal" {{ old('type', $emailAccount->type) === 'personal' ? 'selected' : '' }}>Personal</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Departemen</label>
                            <select name="department"
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                @foreach(['general','sales','support','marketing','hr','finance'] as $dep)
                                <option value="{{ $dep }}" {{ old('department', $emailAccount->department) === $dep ? 'selected' : '' }}>{{ ucfirst($dep) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('description', $emailAccount->description) }}</textarea>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Forward To (Opsional)</label>
                        <input type="email" name="forward_to" value="{{ old('forward_to', $emailAccount->forward_to) }}"
                               placeholder="email@domain.com"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $emailAccount->is_active) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--apple-green)">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary)">Akun Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Pengaturan Email --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-cog" style="color:var(--apple-teal);font-size:0.72rem"></i></span>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pengaturan Email</h2>
                </div>
                <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Maks Email per Hari</label>
                        <input type="number" name="max_daily_emails" value="{{ old('max_daily_emails', $emailAccount->max_daily_emails ?? 100) }}"
                               min="1" max="10000"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Signature (Opsional)</label>
                        <textarea name="signature" rows="3"
                                  placeholder="Nama Anda&#10;Jabatan - Perusahaan&#10;Telepon: ..."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('signature', $emailAccount->signature) }}</textarea>
                    </div>
                    <div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:10px">
                            <input type="checkbox" id="autoReplyCheckbox" name="auto_reply_enabled" value="1"
                                   {{ old('auto_reply_enabled', $emailAccount->auto_reply_enabled) ? 'checked' : '' }}
                                   onchange="toggleAutoReply(this.checked)"
                                   style="width:16px;height:16px;accent-color:var(--apple-blue)">
                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary)">Aktifkan Auto-Reply</span>
                        </label>
                        <div id="autoReplySection" style="display:{{ old('auto_reply_enabled', $emailAccount->auto_reply_enabled) ? 'block' : 'none' }}">
                            <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Pesan Auto-Reply</label>
                            <textarea name="auto_reply_message" rows="3"
                                      placeholder="Terima kasih, kami akan segera membalas..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('auto_reply_message', $emailAccount->auto_reply_message) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                <a href="{{ route('admin.email-accounts.show', $emailAccount) }}"
                   style="padding:9px 20px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    Batal
                </a>
                <button type="submit"
                        style="padding:9px 24px;border:none;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.85rem;font-weight:600;cursor:pointer"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save" style="margin-right:6px"></i>Simpan Perubahan
                </button>
            </div>
        </form>

        {{-- RIGHT: Help Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:16px">
            {{-- Type Help --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-yellow) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-lightbulb" style="color:var(--apple-yellow);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Panduan Tipe</h3>
                </div>
                <div style="padding:14px 18px">
                    <p id="typeHelpText" style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0;line-height:1.5">
                        @if(old('type', $emailAccount->type) === 'personal')
                            Akun personal hanya dapat memiliki satu user yang ditetapkan.
                        @else
                            Pilih shared untuk email tim (cs@, sales@). Dapat ditetapkan ke banyak user.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Tips --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Tips</h3>
                </div>
                <div style="padding:14px 18px;display:flex;flex-direction:column;gap:8px">
                    @foreach([
                        ['icon'=>'fa-at','color'=>'var(--apple-blue)','text'=>'Gunakan email resmi domain bisnis Anda'],
                        ['icon'=>'fa-robot','color'=>'var(--apple-teal)','text'=>'Auto-reply berguna untuk notifikasi otomatis'],
                        ['icon'=>'fa-tachometer-alt','color'=>'var(--apple-orange)','text'=>'Batasi pengiriman untuk menghindari spam'],
                    ] as $tip)
                    <div style="display:flex;align-items:flex-start;gap:8px">
                        <i class="fas {{ $tip['icon'] }}" style="color:{{ $tip['color'] }};font-size:0.78rem;margin-top:3px;flex-shrink:0"></i>
                        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0;line-height:1.4">{{ $tip['text'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleAutoReply(checked) {
    document.getElementById('autoReplySection').style.display = checked ? 'block' : 'none';
}
function updateTypeHelp(val) {
    document.getElementById('typeHelpText').textContent = val === 'personal'
        ? 'Akun personal hanya dapat memiliki satu user yang ditetapkan.'
        : 'Pilih shared untuk email tim (cs@, sales@). Dapat ditetapkan ke banyak user.';
}
</script>
@endpush
@endsection
