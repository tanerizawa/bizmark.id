@extends('layouts.admin')

@section('title', 'Upload Dokumen Baru')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;gap:14px">
        <a href="{{ route('documents.index') }}"
           style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none;flex-shrink:0"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.8rem"></i>
        </a>
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Upload Baru</p>
            <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Upload Dokumen</h1>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
            <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:1rem;flex-shrink:0"></i>
            <span style="font-size:0.85rem;color:var(--apple-green);font-weight:500">{{ session('success') }}</span>
        </div>
    @endif
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

    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="docForm">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- === LEFT COLUMN === --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- File Upload Area --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-cloud-upload-alt" style="color:var(--apple-blue);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);opacity:.8;margin:0">Langkah 1</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">File Dokumen</h3>
                        </div>
                    </div>

                    {{-- Drop Zone --}}
                    <div id="dropZone"
                         style="border:2px dashed var(--dark-separator);border-radius:12px;padding:32px 20px;text-align:center;cursor:pointer;transition:all .2s;position:relative"
                         onclick="document.getElementById('document_file').click()"
                         ondragover="event.preventDefault();this.style.borderColor='var(--apple-blue)';this.style.background='color-mix(in srgb,var(--apple-blue) 5%,transparent)'"
                         ondragleave="this.style.borderColor='var(--dark-separator)';this.style.background='transparent'"
                         ondrop="handleDrop(event)">
                        <i id="dropIcon" class="fas fa-cloud-upload-alt" style="font-size:2rem;color:var(--dark-text-tertiary);margin-bottom:10px;display:block"></i>
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Klik atau seret file ke sini</p>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">PDF, DOC, XLS, JPG, PNG, ZIP — maks 10 MB</p>
                        <input id="document_file" name="document_file" type="file" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip,.rar"
                               style="display:none" onchange="handleFileSelect(this.files[0])">
                    </div>

                    @error('document_file')
                        <p style="font-size:0.72rem;color:var(--apple-red);margin-top:6px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                    @enderror

                    {{-- File Preview --}}
                    <div id="filePreview" style="display:none;margin-top:12px;padding:12px 14px;background:var(--dark-bg-tertiary);border-radius:10px;display:none;align-items:center;gap:10px">
                        <i id="previewIcon" class="fas fa-file-alt" style="font-size:1.4rem;color:var(--dark-text-secondary);flex-shrink:0"></i>
                        <div style="flex:1;min-width:0">
                            <p id="previewName" style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></p>
                            <p id="previewSize" style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0"></p>
                        </div>
                        <button type="button" onclick="clearFile()"
                                style="padding:4px 8px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:none;border-radius:6px;color:var(--apple-red);cursor:pointer;font-size:0.72rem">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Document Info --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-info-circle" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);opacity:.8;margin:0">Langkah 2</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Dokumen</h3>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px">

                        {{-- Project --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Proyek <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="project_id" id="project_id" required onchange="loadTasks(this.value)"
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('project_id') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('project_id') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                    <option value="">Pilih Proyek</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }} — {{ $project->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('project_id')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Task --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Tugas Terkait <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span></label>
                            <div style="position:relative">
                                <select name="task_id" id="task_id"
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">Pilih Tugas</option>
                                    @if($selectedProject && $tasks->count() > 0)
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" {{ old('task_id', $selectedTask?->id ?? null) == $task->id ? 'selected' : '' }}>{{ $task->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('task_id')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Title --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Judul Dokumen <span style="color:var(--apple-red)">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   placeholder="Masukkan judul dokumen..."
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('title')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Deskripsi <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span></label>
                            <textarea name="description" id="description" rows="3"
                                      placeholder="Deskripsi singkat dokumen..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">{{ old('description') }}</textarea>
                            @error('description')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Catatan <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span></label>
                            <textarea name="notes" id="notes" rows="2"
                                      placeholder="Catatan tambahan..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- === RIGHT SIDEBAR === --}}
            <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:16px">

                {{-- Klasifikasi --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-tags" style="color:var(--apple-orange);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);opacity:.8;margin:0">Pengaturan</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Klasifikasi</h3>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:14px">

                        {{-- Category --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Kategori <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="category" id="category" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('category') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('category') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                    <option value="">Pilih Kategori</option>
                                    @foreach(['proposal'=>'Proposal','kontrak'=>'Kontrak','kajian'=>'Kajian','surat'=>'Surat','sk'=>'SK/Izin','laporan'=>'Laporan','gambar'=>'Gambar/Desain','lainnya'=>'Lainnya'] as $v=>$l)
                                        <option value="{{ $v }}" {{ old('category')==$v ? 'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('category')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Status <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="status" id="status" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('status') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('status') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                    @foreach(['draft'=>'Draft','review'=>'Review','approved'=>'Approved','submitted'=>'Submitted','final'=>'Final'] as $v=>$l)
                                        <option value="{{ $v }}" {{ old('status','draft')==$v ? 'selected':'' }}>{{ $l }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('status')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Document Date --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Tanggal Dokumen</label>
                            <input type="date" name="document_date" id="document_date" value="{{ old('document_date') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;color-scheme:dark;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('document_date')<p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>

                        {{-- Confidential toggle --}}
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;padding:10px 12px;background:var(--dark-bg-tertiary);border-radius:10px;border:1px solid var(--dark-separator)">
                            <input type="checkbox" name="is_confidential" value="1" id="is_confidential"
                                   {{ old('is_confidential') ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--apple-red);cursor:pointer;flex-shrink:0">
                            <div>
                                <span style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);display:block">Dokumen Rahasia</span>
                                <span style="font-size:0.68rem;color:var(--dark-text-secondary)">Hanya bisa diakses oleh admin</span>
                            </div>
                            <i class="fas fa-lock" style="color:var(--apple-red);font-size:0.8rem;margin-left:auto;opacity:.7"></i>
                        </label>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" id="submitBtn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-upload" id="submitIcon"></i>
                        <span id="submitText">Upload Dokumen</span>
                        <i class="fas fa-spinner fa-spin" id="submitSpinner" style="display:none"></i>
                    </button>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <button type="reset"
                                style="padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-undo" style="margin-right:5px"></i>Reset
                        </button>
                        <a href="{{ route('documents.index') }}"
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
                        <h4 style="font-size:0.82rem;font-weight:700;color:var(--apple-blue);margin:0">Format yang Didukung</h4>
                    </div>
                    <ul style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:5px">
                        @foreach(['PDF, DOC, DOCX — dokumen teks','XLS, XLSX — spreadsheet','JPG, PNG — gambar','ZIP, RAR — arsip','Maks. ukuran: 10 MB'] as $tip)
                            <li style="display:flex;align-items:center;gap:6px">
                                <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.65rem;flex-shrink:0"></i>
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
const fileExtIcons = {
    pdf:'fa-file-pdf',doc:'fa-file-word',docx:'fa-file-word',
    xls:'fa-file-excel',xlsx:'fa-file-excel',
    jpg:'fa-file-image',jpeg:'fa-file-image',png:'fa-file-image',
    zip:'fa-file-archive',rar:'fa-file-archive'
};
const fileExtColors = {
    pdf:'var(--apple-red)',doc:'var(--apple-blue)',docx:'var(--apple-blue)',
    xls:'var(--apple-green)',xlsx:'var(--apple-green)',
    jpg:'var(--apple-purple)',jpeg:'var(--apple-purple)',png:'var(--apple-purple)',
    zip:'var(--apple-orange)',rar:'var(--apple-orange)'
};

function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const units = ['B','KB','MB','GB'];
    let i = 0;
    while (bytes >= 1024 && i < units.length - 1) { bytes /= 1024; i++; }
    return bytes.toFixed(2) + ' ' + units[i];
}

function handleFileSelect(file) {
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) { alert('Ukuran file terlalu besar! Maksimal 10 MB.'); clearFile(); return; }
    const ext = file.name.split('.').pop().toLowerCase();
    const allowed = ['pdf','doc','docx','xls','xlsx','jpg','jpeg','png','zip','rar'];
    if (!allowed.includes(ext)) { alert('Tipe file tidak diizinkan.'); clearFile(); return; }

    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = formatFileSize(file.size);
    const icon = document.getElementById('previewIcon');
    icon.className = 'fas ' + (fileExtIcons[ext] || 'fa-file-alt');
    icon.style.color = fileExtColors[ext] || 'var(--dark-text-secondary)';
    const fp = document.getElementById('filePreview');
    fp.style.display = 'flex';

    const dropZone = document.getElementById('dropZone');
    dropZone.style.borderColor = fileExtColors[ext] || 'var(--apple-blue)';
    document.getElementById('dropIcon').style.color = fileExtColors[ext] || 'var(--dark-text-tertiary)';

    const titleInput = document.getElementById('title');
    if (!titleInput.value) titleInput.value = file.name.replace(/\.[^/.]+$/, '');
}

function clearFile() {
    document.getElementById('document_file').value = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('dropZone').style.borderColor = 'var(--dark-separator)';
    document.getElementById('dropIcon').style.color = 'var(--dark-text-tertiary)';
}

function handleDrop(e) {
    e.preventDefault();
    const files = e.dataTransfer.files;
    if (files.length) {
        document.getElementById('document_file').files = files;
        handleFileSelect(files[0]);
    }
    e.currentTarget.style.borderColor = 'var(--dark-separator)';
    e.currentTarget.style.background = 'transparent';
}

// Load tasks by project via API
window.loadTasks = function(projectId) {
    const select = document.getElementById('task_id');
    select.innerHTML = '<option value="">Pilih Tugas</option>';
    if (!projectId) return;
    fetch(`{{ route('api.tasks-by-project') }}?project_id=${projectId}`)
        .then(r => r.json())
        .then(tasks => {
            tasks.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t.id; opt.textContent = t.title;
                select.appendChild(opt);
            });
        })
        .catch(() => {});
};

// Submit guard
document.getElementById('docForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('submitIcon').style.display = 'none';
    document.getElementById('submitText').textContent = 'Mengupload...';
    document.getElementById('submitSpinner').style.display = 'inline-block';
});
</script>
@endpush
