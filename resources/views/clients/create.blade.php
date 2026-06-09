@extends('layouts.admin')

@section('title', 'Tambah Klien Baru')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;gap:14px">
        <a href="{{ route('clients.index') }}"
           style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none;flex-shrink:0"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.8rem"></i>
        </a>
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Manajemen Klien</p>
            <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Tambah Klien Baru</h1>
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

    <form action="{{ route('clients.store') }}" method="POST" id="clientForm">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- === LEFT COLUMN === --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Step 1: Informasi Dasar --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-id-card" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);opacity:.8;margin:0">Langkah 1</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Dasar</h3>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

                        {{-- Name --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Nama Klien <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Nama lengkap klien"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('name') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('name') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Company Name --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Nama Perusahaan <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name') }}"
                                   placeholder="PT / CV / UD / Yayasan..."
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('company_name')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Industry --}}
                        <div style="grid-column:1/-1">
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Industri <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                            </label>
                            <input type="text" name="industry" value="{{ old('industry') }}"
                                   placeholder="Konstruksi, Perdagangan, Teknologi, Jasa..."
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('industry')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Step 2: Informasi Kontak --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-address-book" style="color:var(--apple-blue);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);opacity:.8;margin:0">Langkah 2</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Kontak</h3>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

                        {{-- Contact Person --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Nama Contact Person</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                   placeholder="Nama PIC"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('contact_person')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Email</label>
                            <div style="position:relative">
                                <i class="fas fa-envelope" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:0.68rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="email@domain.com"
                                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('email') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('email') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            </div>
                            @error('email')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Telepon</label>
                            <div style="position:relative">
                                <i class="fas fa-phone" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:0.68rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       placeholder="021-12345678"
                                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('phone') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('phone') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            </div>
                            @error('phone')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Mobile / WA --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Handphone / WhatsApp</label>
                            <div style="position:relative">
                                <i class="fab fa-whatsapp" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:0.68rem;color:var(--apple-green);pointer-events:none"></i>
                                <input type="text" name="mobile" value="{{ old('mobile') }}"
                                       placeholder="0812-3456-7890"
                                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('mobile') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('mobile') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            </div>
                            @error('mobile')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Step 3: Alamat --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-map-marker-alt" style="color:var(--apple-green);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-green);opacity:.8;margin:0">Langkah 3</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Alamat</h3>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Alamat Lengkap</label>
                            <textarea name="address" rows="3"
                                      placeholder="Jalan, nomor, RT/RW, kelurahan..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('address') }}</textarea>
                            @error('address')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px">
                            @foreach([['city','Kota'],['province','Provinsi'],['postal_code','Kode Pos']] as [$field,$label])
                            <div>
                                <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">{{ $label }}</label>
                                <input type="text" name="{{ $field }}" value="{{ old($field) }}"
                                       style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has($field) ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has($field) ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                @error($field)<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Step 4: Informasi Pajak --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-file-invoice" style="color:var(--apple-orange);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);opacity:.8;margin:0">Langkah 4</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Pajak</h3>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">NPWP</label>
                            <input type="text" name="npwp" value="{{ old('npwp') }}"
                                   placeholder="12.345.678.9-012.345"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s;font-family:monospace"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('npwp')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Nama di NPWP</label>
                            <input type="text" name="tax_name" value="{{ old('tax_name') }}"
                                   placeholder="Nama sesuai NPWP"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('tax_name')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div style="grid-column:1/-1">
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Alamat NPWP</label>
                            <textarea name="tax_address" rows="2"
                                      placeholder="Alamat sesuai NPWP..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('tax_address') }}</textarea>
                            @error('tax_address')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Step 5: Catatan --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-teal) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-sticky-note" style="color:var(--apple-teal);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-teal);opacity:.8;margin:0">Langkah 5</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Catatan Internal</h3>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Catatan <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                        </label>
                        <textarea name="notes" rows="4"
                                  placeholder="Catatan internal tentang klien ini..."
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                        @error('notes')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:4px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- === RIGHT SIDEBAR === --}}
            <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:16px">

                {{-- Tipe Klien --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-layer-group" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);opacity:.8;margin:0">Klasifikasi</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Tipe Klien</h3>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Tipe <span style="color:var(--apple-red)">*</span></label>
                        <input type="hidden" name="client_type" id="typeInput" value="{{ old('client_type', 'individual') }}">
                        <div style="display:flex;flex-direction:column;gap:7px" id="typeBtns">
                            @foreach([
                                ['individual','Individual','fa-user','var(--apple-blue)'],
                                ['company','Perusahaan','fa-building','var(--apple-purple)'],
                                ['government','Pemerintah','fa-landmark','var(--apple-red)'],
                            ] as [$val,$label,$icon,$col])
                            <button type="button" data-type="{{ $val }}" data-color="{{ $col }}"
                                    onclick="setType('{{ $val }}')"
                                    style="padding:9px 12px;border-radius:9px;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:left;border:1px solid color-mix(in srgb,{{ $col }} 25%,transparent);background:{{ old('client_type','individual')===$val ? "color-mix(in srgb,{$col} 16%,transparent)" : 'transparent' }};color:{{ $col }};display:flex;align-items:center;gap:8px">
                                <i class="fas {{ $icon }}" style="font-size:0.82rem;width:16px;text-align:center"></i>
                                <span>{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                        @error('client_type')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:6px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Status --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-toggle-on" style="color:var(--apple-green);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-green);opacity:.8;margin:0">Pengaturan</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Status Klien</h3>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Status <span style="color:var(--apple-red)">*</span></label>
                        <input type="hidden" name="status" id="statusInput" value="{{ old('status', 'active') }}">
                        <div style="display:flex;flex-direction:column;gap:7px" id="statusBtns">
                            @foreach([
                                ['active','Aktif','fa-check-circle','var(--apple-green)'],
                                ['inactive','Tidak Aktif','fa-times-circle','var(--apple-red)'],
                                ['potential','Potensial','fa-star','var(--apple-orange)'],
                            ] as [$val,$label,$icon,$col])
                            <button type="button" data-status="{{ $val }}" data-color="{{ $col }}"
                                    onclick="setStatus('{{ $val }}')"
                                    style="padding:9px 12px;border-radius:9px;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:left;border:1px solid color-mix(in srgb,{{ $col }} 25%,transparent);background:{{ old('status','active')===$val ? "color-mix(in srgb,{$col} 16%,transparent)" : 'transparent' }};color:{{ $col }};display:flex;align-items:center;gap:8px">
                                <i class="fas {{ $icon }}" style="font-size:0.82rem;width:16px;text-align:center"></i>
                                <span>{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                        @error('status')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:6px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" id="submitBtn"
                            style="width:100%;padding:11px 20px;background:var(--apple-purple);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save" id="submitIcon"></i>
                        <span id="submitText">Simpan Klien</span>
                        <i class="fas fa-spinner fa-spin" id="submitSpinner" style="display:none"></i>
                    </button>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <button type="reset"
                                style="padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-undo" style="margin-right:5px"></i>Reset
                        </button>
                        <a href="{{ route('clients.index') }}"
                           style="display:flex;align-items:center;justify-content:center;padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;text-decoration:none"
                           onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-times" style="margin-right:5px"></i>Batal
                        </a>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div style="background:color-mix(in srgb,var(--apple-purple) 6%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-purple) 20%,var(--dark-separator));border-radius:16px;padding:16px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                        <i class="fas fa-info-circle" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        <h4 style="font-size:0.82rem;font-weight:700;color:var(--apple-purple);margin:0">Tips</h4>
                    </div>
                    <ul style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px">
                        @foreach(['Nama klien akan tampil di daftar proyek','Email digunakan untuk notifikasi otomatis','NPWP diperlukan untuk penerbitan faktur pajak','Status dapat diubah sewaktu-waktu dari halaman edit'] as $tip)
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
        btn.style.background = active ? `color-mix(in srgb,${col} 16%,transparent)` : 'transparent';
        btn.style.borderColor = active ? `color-mix(in srgb,${col} 40%,transparent)` : `color-mix(in srgb,${col} 25%,transparent)`;
    });
}

function setStatus(val) {
    document.getElementById('statusInput').value = val;
    document.querySelectorAll('#statusBtns button').forEach(btn => {
        const active = btn.dataset.status === val;
        const col = btn.dataset.color;
        btn.style.background = active ? `color-mix(in srgb,${col} 16%,transparent)` : 'transparent';
        btn.style.borderColor = active ? `color-mix(in srgb,${col} 40%,transparent)` : `color-mix(in srgb,${col} 25%,transparent)`;
    });
}

// Init from old() values
setType("{{ old('client_type', 'individual') }}");
setStatus("{{ old('status', 'active') }}");

// Submit guard
document.getElementById('clientForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('submitIcon').style.display = 'none';
    document.getElementById('submitText').textContent = 'Menyimpan...';
    document.getElementById('submitSpinner').style.display = 'inline-block';
});
</script>
@endpush
