@extends('layouts.admin')

@section('title', 'Edit Lowongan Kerja')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div>
        <a href="{{ route('admin.jobs.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'"><i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Kembali</a>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);margin:0 0 4px">Edit Lowongan</p>
        <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px">Edit Lowongan Kerja</h1>
        <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Perbarui informasi lowongan pekerjaan</p>
    </div>

    @if($errors->any())
    <div style="background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px;padding:12px 16px">
        <p style="font-size:0.85rem;font-weight:600;color:var(--apple-red);margin:0 0 4px"><i class="fas fa-exclamation-circle" style="margin-right:5px"></i>Ada kesalahan:</p>
        <ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li style="font-size:0.78rem;color:var(--apple-red);margin-bottom:2px">{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.jobs.update', $vacancy->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
            {{-- Left: Main Info (col-span-2) --}}
            <div style="grid-column:span 2;display:flex;flex-direction:column;gap:14px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-info-circle" style="color:var(--apple-blue);margin-right:7px"></i>Informasi Dasar</h3>

                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Judul Lowongan <span style="color:var(--apple-red)">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $vacancy->title) }}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid {{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('title')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Posisi <span style="color:var(--apple-red)">*</span></label>
                            <input type="text" name="position" value="{{ old('position', $vacancy->position) }}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>

                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Deskripsi Pekerjaan <span style="color:var(--apple-red)">*</span></label>
                            <textarea name="description" rows="6" required
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid {{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('description', $vacancy->description) }}</textarea>
                            @error('description')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        {{-- Responsibilities --}}
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Tanggung Jawab <span style="color:var(--apple-red)">*</span></label>
                            <div id="responsibilities-list" style="display:flex;flex-direction:column;gap:6px">
                                @if(is_array($vacancy->responsibilities) && count($vacancy->responsibilities) > 0)
                                    @foreach($vacancy->responsibilities as $item)
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="responsibilities[]" value="{{ $item }}"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                    @endforeach
                                @else
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="responsibilities[]" placeholder="Tanggung jawab 1"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addField('responsibilities')" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);background:none;border:none;cursor:pointer;margin-top:6px;padding:0"><i class="fas fa-plus"></i>Tambah</button>
                        </div>

                        {{-- Qualifications --}}
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Kualifikasi <span style="color:var(--apple-red)">*</span></label>
                            <div id="qualifications-list" style="display:flex;flex-direction:column;gap:6px">
                                @if(is_array($vacancy->qualifications) && count($vacancy->qualifications) > 0)
                                    @foreach($vacancy->qualifications as $item)
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="qualifications[]" value="{{ $item }}"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                    @endforeach
                                @else
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="qualifications[]" placeholder="Kualifikasi 1"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addField('qualifications')" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);background:none;border:none;cursor:pointer;margin-top:6px;padding:0"><i class="fas fa-plus"></i>Tambah</button>
                        </div>

                        {{-- Benefits --}}
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Benefit / Keuntungan</label>
                            <div id="benefits-list" style="display:flex;flex-direction:column;gap:6px">
                                @if(is_array($vacancy->benefits) && count($vacancy->benefits) > 0)
                                    @foreach($vacancy->benefits as $item)
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="benefits[]" value="{{ $item }}"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                    @endforeach
                                @else
                                    <div style="display:flex;gap:6px">
                                        <input type="text" name="benefits[]" placeholder="Benefit 1"
                                               style="flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none"
                                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                        <button type="button" onclick="this.parentElement.remove()" style="padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addField('benefits')" style="display:inline-flex;align-items:center;gap:5px;font-size:0.75rem;font-weight:600;color:var(--apple-blue);background:none;border:none;cursor:pointer;margin-top:6px;padding:0"><i class="fas fa-plus"></i>Tambah</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Details + Actions --}}
            <div style="display:flex;flex-direction:column;gap:14px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-cog" style="color:var(--apple-orange);margin-right:7px"></i>Detail Pekerjaan</h3>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Tipe Pekerjaan <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="employment_type" required style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none;cursor:pointer">
                                    @foreach(['full-time'=>'Full Time','part-time'=>'Part Time','contract'=>'Kontrak','internship'=>'Magang','remote'=>'Remote'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('employment_type', $vacancy->employment_type) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);pointer-events:none;font-size:0.7rem"></i>
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Lokasi <span style="color:var(--apple-red)">*</span></label>
                            <input type="text" name="location" value="{{ old('location', $vacancy->location) }}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Gaji Minimum (Rp)</label>
                            <input type="number" name="salary_min" value="{{ old('salary_min', $vacancy->salary_min) }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Gaji Maksimum (Rp)</label>
                            <input type="number" name="salary_max" value="{{ old('salary_max', $vacancy->salary_max) }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <input type="checkbox" name="salary_negotiable" value="1" id="salary_negotiable" {{ old('salary_negotiable', $vacancy->salary_negotiable) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--apple-blue);cursor:pointer">
                            <label for="salary_negotiable" style="font-size:0.83rem;color:var(--dark-text-primary);cursor:pointer">Gaji bisa dinegosiasi</label>
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Deadline Lamaran</label>
                            <input type="date" name="deadline" value="{{ old('deadline', $vacancy->deadline ? $vacancy->deadline->format('Y-m-d') : '') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;color-scheme:dark"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Google Form URL (Backup)</label>
                            <input type="url" name="google_form_url" value="{{ old('google_form_url', $vacancy->google_form_url) }}" placeholder="https://forms.gle/..."
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin-bottom:5px">Status <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="status" required style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none;cursor:pointer">
                                    @foreach(['draft'=>'Draft','open'=>'Aktif/Terbuka','closed'=>'Ditutup'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('status', $vacancy->status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);pointer-events:none;font-size:0.7rem"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" style="width:100%;padding:11px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:12px;font-size:0.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save"></i>Update Lowongan
                    </button>
                    <a href="{{ route('admin.jobs.index') }}" style="display:block;width:100%;padding:11px 18px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:12px;font-size:0.88rem;font-weight:700;text-align:center;text-decoration:none;box-sizing:border-box" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function addField(type) {
    const list = document.getElementById(type + '-list');
    const div = document.createElement('div');
    div.style.cssText = 'display:flex;gap:6px';
    const inputStyle = 'flex:1;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.83rem;outline:none';
    const btnStyle = 'padding:8px 12px;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:9px;cursor:pointer';
    const labels = {responsibilities:'Tanggung jawab baru', qualifications:'Kualifikasi baru', benefits:'Benefit baru'};
    div.innerHTML = `<input type="text" name="${type}[]" placeholder="${labels[type] || type+' baru'}" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"><button type="button" onclick="this.parentElement.remove()" style="${btnStyle}"><i class="fas fa-times"></i></button>`;
    list.appendChild(div);
}
</script>
@endpush
@endsection
