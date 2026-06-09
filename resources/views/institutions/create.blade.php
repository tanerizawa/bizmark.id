@extends('layouts.admin')

@section('title', 'Tambah Institusi Baru')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;gap:14px">
        <a href="{{ route('institutions.index') }}"
           style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none;flex-shrink:0"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.8rem"></i>
        </a>
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Tambah Baru</p>
            <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Tambah Institusi</h1>
        </div>
    </div>

    {{-- Alerts --}}
    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <i class="fas fa-exclamation-circle" style="color:var(--apple-red);font-size:1rem;flex-shrink:0;margin-top:2px"></i>
            <div>
                <p style="font-size:0.85rem;color:var(--apple-red);font-weight:600;margin:0 0 6px">Terdapat kesalahan pada form:</p>
                <ul style="font-size:0.78rem;color:var(--apple-red);opacity:.85;margin:0;padding-left:16px">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('institutions.store') }}" method="POST" id="instForm">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- === LEFT COLUMN === --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Informasi Dasar --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-building" style="color:var(--apple-blue);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);opacity:.8;margin:0">Langkah 1</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Dasar</h3>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px">

                        {{-- Name --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Nama Institusi <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                   placeholder="Contoh: Dinas Lingkungan Hidup DKI Jakarta"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('name') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('name') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Type --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">
                                Tipe Institusi <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="hidden" name="type" id="typeInput" value="{{ old('type', '') }}">
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px" id="typeBtns">
                                @foreach([
                                    ['Pemerintah','fa-landmark','var(--apple-red)'],
                                    ['BUMN','fa-city','var(--apple-orange)'],
                                    ['Swasta','fa-briefcase','var(--apple-green)'],
                                    ['Lainnya','fa-building','var(--dark-text-secondary)'],
                                ] as [$val,$icon,$col])
                                    <button type="button" data-type="{{ $val }}" data-color="{{ $col }}"
                                            onclick="setType('{{ $val }}')"
                                            style="padding:8px 4px;border-radius:8px;font-size:0.72rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:center;border:1px solid color-mix(in srgb,{{ $col }} 30%,transparent);background:{{ old('type')==$val ? "color-mix(in srgb,{$col} 18%,transparent)" : 'transparent' }};color:{{ $col }};display:flex;flex-direction:column;align-items:center;gap:4px">
                                        <i class="fas {{ $icon }}" style="font-size:1rem"></i>
                                        <span>{{ $val }}</span>
                                    </button>
                                @endforeach
                            </div>
                            @error('type')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Alamat <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                            </label>
                            <textarea name="address" id="address" rows="3"
                                      placeholder="Alamat lengkap institusi..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('address') }}</textarea>
                            @error('address')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Informasi Kontak --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-address-book" style="color:var(--apple-green);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-green);opacity:.8;margin:0">Langkah 2</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Kontak</h3>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        {{-- Contact Person --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Nama Kontak</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                   placeholder="Nama person in charge"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('contact_person')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Contact Position --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Jabatan</label>
                            <input type="text" name="contact_position" value="{{ old('contact_position') }}"
                                   placeholder="Kepala Bidang, Manager..."
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('contact_position')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Nomor Telepon</label>
                            <div style="position:relative">
                                <i class="fas fa-phone" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:0.68rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       placeholder="021-12345678"
                                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('phone') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('phone') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            </div>
                            @error('phone')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Email</label>
                            <div style="position:relative">
                                <i class="fas fa-envelope" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:0.68rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="contact@institusi.com"
                                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('email') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('email') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            </div>
                            @error('email')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Informasi Tambahan --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-sticky-note" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);opacity:.8;margin:0">Langkah 3</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Tambahan</h3>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Catatan <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                        </label>
                        <textarea name="notes" rows="4"
                                  placeholder="Catatan khusus tentang institusi ini..."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                        @error('notes')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- === RIGHT SIDEBAR === --}}
            <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:16px">

                {{-- Status --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-toggle-on" style="color:var(--apple-orange);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);opacity:.8;margin:0">Pengaturan</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Status Institusi</h3>
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Status <span style="color:var(--apple-red)">*</span></label>
                        <input type="hidden" name="is_active" id="statusInput" value="{{ old('is_active', '1') }}">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px" id="statusBtns">
                            <button type="button" data-status="1"
                                    onclick="setStatus('1')"
                                    style="padding:10px 8px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:center;border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);background:{{ old('is_active','1')==='1' ? 'color-mix(in srgb,var(--apple-green) 18%,transparent)' : 'transparent' }};color:var(--apple-green);display:flex;flex-direction:column;align-items:center;gap:4px">
                                <i class="fas fa-check-circle" style="font-size:1.1rem"></i>
                                <span>Aktif</span>
                            </button>
                            <button type="button" data-status="0"
                                    onclick="setStatus('0')"
                                    style="padding:10px 8px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:center;border:1px solid color-mix(in srgb,var(--dark-text-secondary) 30%,transparent);background:{{ old('is_active')==='0' ? 'color-mix(in srgb,var(--dark-text-secondary) 18%,transparent)' : 'transparent' }};color:var(--dark-text-secondary);display:flex;flex-direction:column;align-items:center;gap:4px">
                                <i class="fas fa-times-circle" style="font-size:1.1rem"></i>
                                <span>Nonaktif</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" id="submitBtn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save" id="submitIcon"></i>
                        <span id="submitText">Simpan Institusi</span>
                        <i class="fas fa-spinner fa-spin" id="submitSpinner" style="display:none"></i>
                    </button>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <button type="reset"
                                style="padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-undo" style="margin-right:5px"></i>Reset
                        </button>
                        <a href="{{ route('institutions.index') }}"
                           style="display:flex;align-items:center;justify-content:center;padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-times" style="margin-right:5px"></i>Batal
                        </a>
                    </div>
                </div>

                {{-- Info Card --}}
                <div style="background:color-mix(in srgb,var(--apple-blue) 6%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));border-radius:16px;padding:16px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                        <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.85rem"></i>
                        <h4 style="font-size:0.82rem;font-weight:700;color:var(--apple-blue);margin:0">Informasi</h4>
                    </div>
                    <ul style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px">
                        @foreach(['Nama institusi harus unik dalam sistem','Tipe membantu kategorisasi dan filter','Info kontak digunakan untuk komunikasi proyek','Status dapat diubah sewaktu-waktu'] as $tip)
                            <li style="display:flex;align-items:flex-start;gap:6px">
                                <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.65rem;margin-top:2px;flex-shrink:0"></i>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function setType(val) {
    document.getElementById('typeInput').value = val;
    document.querySelectorAll('#typeBtns button').forEach(btn => {
        const active = btn.dataset.type === val;
        const col = btn.dataset.color;
        btn.style.background = active ? `color-mix(in srgb,${col} 18%,transparent)` : 'transparent';
        btn.style.borderColor = active ? `color-mix(in srgb,${col} 50%,transparent)` : `color-mix(in srgb,${col} 30%,transparent)`;
    });
}

function setStatus(val) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('#statusBtns button').forEach(btn => {
        const active = btn.dataset.status === val;
        const isActive = btn.dataset.status === '1';
        const col = isActive ? 'var(--apple-green)' : 'var(--dark-text-secondary)';
        btn.style.background = active ? `color-mix(in srgb,${col} 18%,transparent)` : 'transparent';
    });
}

// Init from old() values
const initType = "{{ old('type', '') }}";
const initStatus = "{{ old('is_active', '1') }}";
if (initType) setType(initType);
setStatus(initStatus);

// Submit guard
document.getElementById('instForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('submitIcon').style.display = 'none';
    document.getElementById('submitText').textContent = 'Menyimpan...';
    document.getElementById('submitSpinner').style.display = 'inline-block';
});
</script>
@endpush
