@extends('layouts.admin')

@section('title', 'Tambah Tugas Baru')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px">
        <div style="display:flex;align-items:center;gap:14px">
            <a href="{{ route('tasks.index') }}"
               style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-secondary);text-decoration:none;flex-shrink:0"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.8rem"></i>
            </a>
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Tambah Baru</p>
                <h1 style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Buat Tugas Baru</h1>
            </div>
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

    <form action="{{ route('tasks.store') }}" method="POST" id="taskForm">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- === LEFT COLUMN === --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Informasi Dasar --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);opacity:.8;margin:0">Langkah 1</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Informasi Dasar</h3>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:16px">

                        {{-- Project --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                Proyek <span style="color:var(--apple-red)">*</span>
                            </label>
                            <div style="position:relative">
                                <select name="project_id" id="project_id" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('project_id') ? 'var(--apple-red)' : 'var(--dark-separator))' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('project_id') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                    <option value="">Pilih Proyek</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                                data-client="{{ $project->client_name }}"
                                                data-status="{{ $project->status->name ?? 'N/A' }}"
                                                {{ old('project_id', $selectedProject?->id) == $project->id ? 'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('project_id')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                            <div id="projectInfo" style="display:none;margin-top:10px;padding:10px 12px;background:var(--dark-bg-tertiary);border-radius:8px;font-size:0.75rem;color:var(--dark-text-secondary)">
                                <i class="fas fa-briefcase" style="margin-right:6px;color:var(--dark-text-tertiary)"></i>
                                Client: <strong id="clientName" style="color:var(--dark-text-primary)">-</strong>
                                &nbsp;·&nbsp; Status: <strong id="projectStatus" style="color:var(--dark-text-primary)">-</strong>
                            </div>
                        </div>

                        {{-- Title --}}
                        <div>
                            <label style="display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                <span>Judul Tugas <span style="color:var(--apple-red)">*</span></span>
                                <span id="titleCount" style="font-weight:400;color:var(--dark-text-tertiary)">0/255</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255"
                                   placeholder="Misal: Mengurus izin IMB ke Dinas PU"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('title')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px">Gunakan judul yang jelas dan deskriptif</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label style="display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                <span>Deskripsi <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span></span>
                                <span id="descCount" style="font-weight:400;color:var(--dark-text-tertiary)">0/1000</span>
                            </label>
                            <textarea name="description" id="description" rows="4" maxlength="1000"
                                      placeholder="Jelaskan detail tugas, langkah-langkah yang perlu dilakukan, atau informasi penting lainnya..."
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('description') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">{{ old('description') }}</textarea>
                            @error('description')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SOP Notes --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                                SOP / Checklist <span style="font-weight:400;color:var(--dark-text-tertiary)">(Opsional)</span>
                            </label>
                            <textarea name="sop_notes" id="sop_notes" rows="5"
                                      placeholder="Contoh:&#10;☐ Siapkan dokumen persyaratan&#10;☐ Fotokopi KTP dan NPWP&#10;☐ Upload ke sistem online&#10;☐ Tunggu verifikasi (3-5 hari kerja)&#10;☐ Ambil dokumen fisik"
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('sop_notes') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.8rem;font-family:monospace;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('sop_notes') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">{{ old('sop_notes') }}</textarea>
                            @error('sop_notes')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <div style="display:flex;align-items:center;gap:6px;margin-top:8px;padding:8px 10px;background:var(--dark-bg-tertiary);border-radius:8px">
                                    <i class="fas fa-lightbulb" style="color:var(--apple-yellow);font-size:0.75rem;flex-shrink:0"></i>
                                    <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0"><strong>Tips:</strong> Gunakan ☐ untuk checkbox, - untuk bullet, atau 1. 2. 3. untuk langkah berurutan</p>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Penugasan & Timeline --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-calendar-check" style="color:var(--apple-purple);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);opacity:.8;margin:0">Langkah 2</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Penugasan & Timeline</h3>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">

                        {{-- Assigned User --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Ditugaskan Kepada</label>
                            <div style="position:relative">
                                <select name="assigned_user_id" id="assigned_user_id"
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">Belum ditugaskan</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('assigned_user_id')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px">Bisa diisi nanti setelah task dibuat</p>
                            @enderror
                        </div>

                        {{-- Due Date --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Tanggal Jatuh Tempo</label>
                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('due_date') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;color-scheme:dark;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('due_date') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('due_date')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p id="daysUntilDue" style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px"></p>
                            @enderror
                        </div>

                        {{-- Estimated Hours --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Estimasi Jam Kerja</label>
                            <div style="position:relative">
                                <input type="number" name="estimated_hours" id="estimated_hours" value="{{ old('estimated_hours') }}"
                                       min="1" max="999" placeholder="8"
                                       style="width:100%;padding:9px 44px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('estimated_hours') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('estimated_hours') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none">jam</span>
                            </div>
                            @error('estimated_hours')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px">Perkiraan waktu pengerjaan</p>
                            @enderror
                        </div>

                        {{-- Institution --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Institusi Terkait</label>
                            <div style="position:relative">
                                <select name="institution_id" id="institution_id"
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">Tidak ada</option>
                                    @foreach($institutions as $institution)
                                        <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>{{ $institution->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('institution_id')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px">Misal: Dinas PU, BPN, dll</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- === RIGHT SIDEBAR === --}}
            <div style="display:flex;flex-direction:column;gap:16px;position:sticky;top:16px">

                {{-- Status & Priority --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-sliders-h" style="color:var(--apple-orange);font-size:0.85rem"></i>
                        </div>
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);opacity:.8;margin:0">Pengaturan</p>
                            <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Status & Prioritas</h3>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:14px">
                        {{-- Status --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Status <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="status" id="status" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('status') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('status') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                                    <option value="todo"        {{ old('status','todo')=='todo'        ? 'selected':'' }}>Belum Dikerjakan (To Do)</option>
                                    <option value="in_progress" {{ old('status')=='in_progress'        ? 'selected':'' }}>Sedang Dikerjakan (In Progress)</option>
                                    <option value="done"        {{ old('status')=='done'               ? 'selected':'' }}>Selesai (Done)</option>
                                    <option value="blocked"     {{ old('status')=='blocked'            ? 'selected':'' }}>Terblokir (Blocked)</option>
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                            </div>
                            @error('status')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Prioritas <span style="color:var(--apple-red)">*</span></label>
                            <input type="hidden" name="priority" id="priorityInput" value="{{ old('priority','normal') }}">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px" id="priorityBtns">
                                @foreach([['low','Rendah','var(--apple-teal)'],['normal','Normal','var(--apple-blue)'],['high','Tinggi','var(--apple-orange)'],['urgent','Mendesak','var(--apple-red)']] as [$val,$lbl,$col])
                                    <button type="button" data-priority="{{ $val }}"
                                            onclick="setPriority('{{ $val }}')"
                                            style="padding:7px 10px;border-radius:8px;font-size:0.75rem;font-weight:600;cursor:pointer;transition:all .15s;text-align:center;border:1px solid color-mix(in srgb,{{ $col }} 30%,transparent);background:{{ old('priority','normal')==$val ? "color-mix(in srgb,{$col} 18%,transparent)" : 'transparent' }};color:{{ $col }}">
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                            @error('priority')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Sort Order --}}
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Urutan</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   placeholder="0 (otomatis di akhir)"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('sort_order') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='{{ $errors->has('sort_order') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'">
                            @error('sort_order')
                                <p style="font-size:0.72rem;color:var(--apple-red);margin-top:5px;display:flex;align-items:center;gap:4px"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>
                            @else
                                <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:4px">Kosongkan untuk posisi terakhir</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Tips Card --}}
                <div style="background:color-mix(in srgb,var(--apple-blue) 6%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));border-radius:16px;padding:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
                        <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.9rem"></i>
                        <h4 style="font-size:0.82rem;font-weight:700;color:var(--apple-blue);margin:0">Tips Membuat Tugas</h4>
                    </div>
                    <ul style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:8px">
                        @foreach(['Gunakan judul yang spesifik dan actionable','Pecah tugas besar menjadi sub-tugas kecil','Set prioritas berdasarkan deadline dan impact','Tambahkan SOP untuk tugas yang berulang'] as $tip)
                            <li style="display:flex;align-items:flex-start;gap:7px">
                                <i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.68rem;margin-top:2px;flex-shrink:0"></i>
                                <span>{{ $tip }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;flex-direction:column;gap:8px">
                    <button type="submit" id="submitBtn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save" id="submitIcon"></i>
                        <span id="submitText">Simpan Tugas</span>
                        <i class="fas fa-spinner fa-spin" id="submitSpinner" style="display:none"></i>
                    </button>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <button type="reset"
                                style="padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;cursor:pointer;transition:all .15s"
                                onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-undo" style="margin-right:5px"></i>Reset
                        </button>
                        <a href="{{ route('tasks.index') }}"
                           style="display:flex;align-items:center;justify-content:center;padding:9px;background:transparent;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:10px;font-size:0.78rem;font-weight:600;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <i class="fas fa-times" style="margin-right:5px"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Character counters
const titleInput = document.getElementById('title');
const titleCount = document.getElementById('titleCount');
const descInput  = document.getElementById('description');
const descCount  = document.getElementById('descCount');

function updateCount(input, countEl, max) {
    countEl.textContent = input.value.length + '/' + max;
}
titleInput.addEventListener('input', () => updateCount(titleInput, titleCount, 255));
descInput.addEventListener('input',  () => updateCount(descInput, descCount, 1000));
updateCount(titleInput, titleCount, 255);
updateCount(descInput, descCount, 1000);

// Priority toggle
function setPriority(val) {
    document.getElementById('priorityInput').value = val;
    document.querySelectorAll('#priorityBtns button').forEach(btn => {
        const active = btn.dataset.priority === val;
        btn.style.fontWeight = active ? '700' : '600';
        const col = btn.style.color;
        btn.style.background = active ? `color-mix(in srgb,${col} 18%,transparent)` : 'transparent';
    });
}
// Init priority visual on load
setPriority(document.getElementById('priorityInput').value || 'normal');

// Project info preview
const projectSelect = document.getElementById('project_id');
const projectInfo   = document.getElementById('projectInfo');

projectSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (this.value) {
        document.getElementById('clientName').textContent  = opt.dataset.client  || '-';
        document.getElementById('projectStatus').textContent = opt.dataset.status || '-';
        projectInfo.style.display = 'block';
    } else {
        projectInfo.style.display = 'none';
    }
});
if (projectSelect.value) projectSelect.dispatchEvent(new Event('change'));

// Due date helper
const dueDateInput = document.getElementById('due_date');
const daysUntilDue = document.getElementById('daysUntilDue');

dueDateInput.addEventListener('change', function () {
    if (!this.value || !daysUntilDue) return;
    const due   = new Date(this.value);
    const today = new Date(); today.setHours(0,0,0,0);
    const diff  = Math.ceil((due - today) / 86400000);

    if (diff < 0) {
        daysUntilDue.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:var(--apple-red);margin-right:4px"></i><span style="color:var(--apple-red)">Tanggal sudah lewat!</span>';
    } else if (diff === 0) {
        daysUntilDue.innerHTML = '<i class="fas fa-clock" style="color:var(--apple-yellow);margin-right:4px"></i><span style="color:var(--apple-yellow)">Jatuh tempo hari ini</span>';
    } else if (diff <= 3) {
        daysUntilDue.innerHTML = `<i class="fas fa-clock" style="color:var(--apple-orange);margin-right:4px"></i><span style="color:var(--apple-orange)">${diff} hari lagi (segera!)</span>`;
    } else {
        daysUntilDue.innerHTML = `<i class="fas fa-clock" style="color:var(--dark-text-tertiary);margin-right:4px"></i>${diff} hari lagi`;
    }
});

// Submit guard
document.getElementById('taskForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.style.opacity = '0.6';
    btn.style.cursor  = 'not-allowed';
    document.getElementById('submitIcon').style.display    = 'none';
    document.getElementById('submitText').textContent       = 'Menyimpan...';
    document.getElementById('submitSpinner').style.display = 'inline-block';
});
</script>
@endpush
