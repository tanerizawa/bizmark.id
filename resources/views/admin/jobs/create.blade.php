@extends('layouts.admin')

@section('title', 'Tambah Lowongan Kerja')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.index', ['tab'=>'jobs']) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Kembali ke Rekrutmen
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);margin:0 0 4px">Rekrutmen</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Tambah Lowongan Kerja</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Buat lowongan pekerjaan baru untuk tim Anda</p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.jobs.store') }}" method="POST" id="create-job-form" onsubmit="handleSubmit(this)">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">

            {{-- Left: Main Info --}}
            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Informasi Utama</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Dasar</h3>
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Judul Lowongan <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid {{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'"
                               placeholder="Contoh: Senior Environmental Consultant">
                        @error('title')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Posisi <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="position" value="{{ old('position') }}" required
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid {{ $errors->has('position') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('position') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'"
                               placeholder="Contoh: Environmental Specialist">
                        @error('position')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:6px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Deskripsi Pekerjaan <span style="color:var(--apple-red)">*</span></label>
                        <textarea name="description" rows="6" required
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid {{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box;line-height:1.5"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'"
                                  placeholder="Jelaskan tanggung jawab dan konteks pekerjaan...">{{ old('description') }}</textarea>
                        @error('description')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>
                </div>

                @foreach(['responsibilities' => ['label'=>'Tanggung Jawab','required'=>true,'placeholder'=>'Contoh: Menyusun dokumen AMDAL'], 'qualifications' => ['label'=>'Kualifikasi','required'=>true,'placeholder'=>'Contoh: S1 Teknik Lingkungan'], 'benefits' => ['label'=>'Benefit / Keuntungan','required'=>false,'placeholder'=>'Contoh: BPJS Kesehatan + Ketenagakerjaan']] as $field => $meta)
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Daftar Item</p>
                            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $meta['label'] }} @if($meta['required'])<span style="color:var(--apple-red)">*</span>@endif</h3>
                        </div>
                        <button type="button" onclick="addField('{{ $field }}', '{{ $meta['placeholder'] }}')"
                                style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:6px 12px;border-radius:8px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);cursor:pointer"
                                onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                            <i class="fas fa-plus" style="font-size:0.65rem"></i>Tambah
                        </button>
                    </div>
                    <div id="{{ $field }}-list" style="display:flex;flex-direction:column;gap:8px">
                        <div style="display:flex;gap:8px;align-items:center">
                            <input type="text" name="{{ $field }}[]"
                                   style="flex:1;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                   placeholder="{{ $meta['placeholder'] }}">
                            <button type="button" onclick="this.parentElement.remove()"
                                    style="width:32px;height:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:8px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer;font-size:0.75rem">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Right: Details + Submit --}}
            <div style="position:sticky;top:16px;display:flex;flex-direction:column;gap:16px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Konfigurasi</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Pekerjaan</h3>
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Tipe Kerja <span style="color:var(--apple-red)">*</span></label>
                        <div style="position:relative">
                            <select name="employment_type" required
                                    style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="full-time" {{ old('employment_type') === 'full-time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part-time" {{ old('employment_type') === 'part-time' ? 'selected' : '' }}>Part Time</option>
                                <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Kontrak</option>
                                <option value="internship" {{ old('employment_type') === 'internship' ? 'selected' : '' }}>Magang</option>
                                <option value="remote" {{ old('employment_type') === 'remote' ? 'selected' : '' }}>Remote</option>
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                        </div>
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Lokasi <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="location" value="{{ old('location', 'Jakarta') }}" required
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Gaji Min (Rp)</label>
                            <input type="number" name="salary_min" value="{{ old('salary_min') }}" min="0"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                   placeholder="5000000">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Gaji Max (Rp)</label>
                            <input type="number" name="salary_max" value="{{ old('salary_max') }}" min="0"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                   placeholder="10000000">
                        </div>
                    </div>

                    <div style="margin-bottom:14px;padding:10px 14px;background:var(--dark-bg-secondary);border-radius:10px;display:flex;align-items:center;gap:10px">
                        <input type="checkbox" name="salary_negotiable" value="1" id="salary_negotiable"
                               {{ old('salary_negotiable', '1') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--apple-blue);flex-shrink:0;cursor:pointer">
                        <label for="salary_negotiable" style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);cursor:pointer;margin:0">Gaji bisa dinegosiasi</label>
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Deadline Lamaran</label>
                        <input type="date" name="deadline" value="{{ old('deadline') }}"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Google Form URL <span style="font-size:0.7rem;color:var(--dark-text-secondary)">(Backup)</span></label>
                        <input type="url" name="google_form_url" value="{{ old('google_form_url') }}"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                               placeholder="https://forms.gle/...">
                    </div>

                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Status <span style="color:var(--apple-red)">*</span></label>
                        <div style="position:relative">
                            <select name="status" required
                                    style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Aktif / Terbuka</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                        </div>
                    </div>
                </div>

                {{-- Submit Actions --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px;display:flex;flex-direction:column;gap:10px">
                    <button type="submit" id="submit-btn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .2s"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save" id="submit-icon"></i>
                        <span id="submit-label">Simpan Lowongan</span>
                    </button>
                    <a href="{{ route('admin.recruitment.index', ['tab'=>'jobs']) }}"
                       style="display:flex;align-items:center;justify-content:center;padding:10px 20px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.85rem;font-weight:600;text-decoration:none;transition:all .2s"
                       onmouseover="this.style.borderColor='var(--dark-text-secondary)';this.style.color='var(--dark-text-primary)'"
                       onmouseout="this.style.borderColor='var(--dark-separator)';this.style.color='var(--dark-text-secondary)'">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>
function addField(type, placeholder) {
    const list = document.getElementById(type + '-list');
    const div = document.createElement('div');
    div.style.cssText = 'display:flex;gap:8px;align-items:center';
    div.innerHTML = `
        <input type="text" name="${type}[]"
               style="flex:1;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
               placeholder="${placeholder}">
        <button type="button" onclick="this.parentElement.remove()"
                style="width:32px;height:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:8px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer;font-size:0.75rem">
            <i class="fas fa-times"></i>
        </button>
    `;
    list.appendChild(div);
    div.querySelector('input').focus();
}

function handleSubmit(form) {
    const btn = document.getElementById('submit-btn');
    const icon = document.getElementById('submit-icon');
    const label = document.getElementById('submit-label');
    btn.disabled = true;
    btn.style.opacity = '0.6';
    btn.style.cursor = 'not-allowed';
    icon.className = 'fas fa-spinner fa-spin';
    label.textContent = 'Menyimpan...';
}
</script>
@endpush
@endsection
