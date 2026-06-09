@extends('layouts.admin')

@section('title', 'Buat Template Tes')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.tests.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Test Management
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);margin:0 0 4px">Rekrutmen</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Buat Template Tes</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Susun template tes dengan pertanyaan terstruktur dan nilai lulus yang jelas</p>
        </div>
    </div>

    <form action="{{ route('admin.recruitment.tests.store') }}" method="POST" id="testForm" enctype="multipart/form-data" onsubmit="handleSubmit(this)">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:flex-start">

            {{-- Left: Main Content --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Basic Info --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Informasi Dasar</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Detail Template</h3>
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Judul Tes <span style="color:var(--apple-red)">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                               placeholder="Contoh: Technical Assessment Senior Dev">
                        @error('title')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                    </div>

                    <div style="margin-bottom:14px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Deskripsi</label>
                        <textarea name="description" rows="3"
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                  placeholder="Ringkasan tujuan penilaian">{{ old('description') }}</textarea>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:14px">
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Tipe Tes <span style="color:var(--apple-red)">*</span></label>
                            <div style="position:relative">
                                <select name="test_type" id="test_type" required
                                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <option value="">Pilih tipe...</option>
                                    @foreach(['psychology'=>'Psikologi','psychometric'=>'Psikometrik','technical'=>'Teknis','aptitude'=>'Aptitude','personality'=>'Kepribadian','document_editing'=>'Document Editing'] as $val=>$lbl)
                                    <option value="{{ $val }}" {{ old('test_type') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
                            </div>
                            @error('test_type')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Durasi (menit) <span style="color:var(--apple-red)">*</span></label>
                            <input type="number" name="duration_minutes" id="durationInput" value="{{ old('duration_minutes', 60) }}" required min="5" max="480"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Passing Score (%) <span style="color:var(--apple-red)">*</span></label>
                            <input type="number" name="passing_score" value="{{ old('passing_score', 70) }}" required min="0" max="100"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Instruksi Tes</label>
                        <textarea name="instructions" rows="7"
                                  style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;resize:vertical;box-sizing:border-box;font-family:monospace;line-height:1.5"
                                  onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                  placeholder="Instruksi yang akan dibaca kandidat sebelum memulai tes...">{{ old('instructions') }}</textarea>
                        <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0">Plain text. Simbol ▸ [ ] • akan di-style otomatis oleh sistem.</p>
                    </div>
                </div>

                {{-- Questions Section --}}
                <div id="questionsSection" style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <div>
                            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Pertanyaan</p>
                            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Daftar Pertanyaan</h3>
                        </div>
                        <button type="button" id="addQuestion"
                                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:var(--apple-blue);color:#fff;border:none;border-radius:9px;font-size:0.78rem;font-weight:700;cursor:pointer"
                                onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                            <i class="fas fa-plus" style="font-size:0.65rem"></i>Tambah Pertanyaan
                        </button>
                    </div>
                    <div id="questionsContainer" style="display:flex;flex-direction:column;gap:12px"></div>
                    <div id="emptyState" style="text-align:center;padding:32px">
                        <i class="fas fa-clipboard-list" style="font-size:1.8rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:10px"></i>
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 3px">Belum Ada Pertanyaan</p>
                        <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">Klik "Tambah Pertanyaan" untuk memulai</p>
                    </div>
                </div>

                {{-- Document Editing Section --}}
                <div id="documentEditingSection" style="display:none;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
                    <div style="margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
                        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Document Editing</p>
                        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">Template & Kriteria Penilaian</h3>
                    </div>

                    <div style="margin-bottom:16px">
                        <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);margin-bottom:6px">Template File (Word Document)</label>
                        <input type="file" name="template_file" accept=".doc,.docx"
                               style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box">
                        <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0">Upload file Word yang akan diperbaiki kandidat (Max: 10MB)</p>
                    </div>

                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                            <label style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary)">Kriteria Penilaian</label>
                            <button type="button" id="addCriteria"
                                    style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 12%,transparent);padding:5px 10px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);cursor:pointer"
                                    onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                <i class="fas fa-plus" style="font-size:0.6rem"></i>Tambah Kriteria
                            </button>
                        </div>
                        <div id="criteriaContainer" style="display:flex;flex-direction:column;gap:10px"></div>
                        <div id="criteriaEmptyState" style="text-align:center;padding:20px;color:var(--dark-text-secondary)">
                            <i class="fas fa-check-circle" style="font-size:1.2rem;opacity:.4;display:block;margin-bottom:6px"></i>
                            <p style="font-size:0.75rem;margin:0">Belum ada kriteria. Klik tombol di atas untuk menambah.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Sidebar --}}
            <div style="position:sticky;top:16px;display:flex;flex-direction:column;gap:14px">

                {{-- Status --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Status Template</h3>
                    <div style="padding:10px 14px;background:var(--dark-bg-secondary);border-radius:10px;display:flex;align-items:center;gap:10px">
                        <input type="checkbox" name="is_active" value="1" id="is_active"
                               {{ old('is_active', '1') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:var(--apple-blue);flex-shrink:0;cursor:pointer">
                        <label for="is_active" style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);cursor:pointer;margin:0">Template Aktif</label>
                    </div>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:8px 0 0">Template aktif dapat diberikan ke kandidat.</p>
                </div>

                {{-- Summary --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
                    <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px">Ringkasan</h3>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach(['totalQuestions'=>'Total Pertanyaan','totalPoints'=>'Total Poin','durationDisplay'=>'Durasi'] as $id => $label)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--dark-bg-secondary);border-radius:8px">
                            <span style="font-size:0.78rem;color:var(--dark-text-secondary)">{{ $label }}</span>
                            <span id="{{ $id }}" style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary)">{{ $id === 'durationDisplay' ? '60 menit' : '0' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px;display:flex;flex-direction:column;gap:10px">
                    <button type="submit" id="submit-btn"
                            style="width:100%;padding:11px 20px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                            onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                        <i class="fas fa-save" id="submit-icon"></i>
                        <span id="submit-label">Buat Template</span>
                    </button>
                    <a href="{{ route('admin.recruitment.tests.index') }}"
                       style="display:flex;align-items:center;justify-content:center;padding:10px 20px;color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.85rem;font-weight:600;text-decoration:none"
                       onmouseover="this.style.color='var(--dark-text-primary)';this.style.borderColor='var(--dark-text-secondary)'" onmouseout="this.style.color='var(--dark-text-secondary)';this.style.borderColor='var(--dark-separator)'">
                        Batal
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>
let questionIndex = 0;

const inputStyle = 'width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box';
const selectStyle = inputStyle + ';appearance:none';

function makeInput(name, placeholder, type='text', value='') {
    return `<input type="${type}" name="${name}" value="${value}" placeholder="${placeholder}" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'" required>`;
}

function makeSelect(name, options) {
    const opts = options.map(o => `<option value="${o.v}">${o.l}</option>`).join('');
    return `<div style="position:relative"><select name="${name}" style="${selectStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">${opts}</select><i class="fas fa-chevron-down" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.6rem;pointer-events:none"></i></div>`;
}

document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('addQuestion');
    const container = document.getElementById('questionsContainer');
    const emptyState = document.getElementById('emptyState');
    const durationInput = document.getElementById('durationInput');

    addBtn.addEventListener('click', function() {
        const idx = questionIndex++;
        const item = document.createElement('div');
        item.className = 'question-item';
        item.style.cssText = 'background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px';
        item.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                <span style="font-size:0.78rem;font-weight:700;color:var(--apple-blue)">Pertanyaan <span class="q-num"></span></span>
                <button type="button" class="remove-question" style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer;font-size:0.72rem">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div>
                    <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Teks Pertanyaan *</label>
                    <textarea name="questions[${idx}][question_text]" rows="2" required style="${inputStyle};resize:vertical" placeholder="Tuliskan pertanyaan..." onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div>
                        <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Jenis *</label>
                        ${makeSelect(`questions[${idx}][question_type]`, [{v:'multiple-choice',l:'Multiple Choice'},{v:'true-false',l:'True/False'},{v:'essay',l:'Essay'},{v:'rating',l:'Rating Scale'}])}
                    </div>
                    <div>
                        <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Poin *</label>
                        <input type="number" name="questions[${idx}][points]" value="1" min="0" step="0.5" required class="question-points" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                </div>
                <div class="options-wrap">
                    <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Pilihan Jawaban</label>
                    <div class="options-list" style="display:flex;flex-direction:column;gap:6px">
                        <div style="display:flex;gap:6px;align-items:center">
                            <input type="text" name="questions[${idx}][options][]" placeholder="Pilihan 1" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <button type="button" class="add-option" style="width:30px;height:30px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:7px;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);cursor:pointer;font-size:0.7rem"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="correct-wrap">
                    <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Jawaban Benar</label>
                    <input type="text" name="questions[${idx}][correct_answer]" placeholder="Nomor pilihan benar (mis: 1)" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                </div>
            </div>
        `;
        container.appendChild(item);
        emptyState.style.display = 'none';
        updateSummary();

        // Remove
        item.querySelector('.remove-question').addEventListener('click', function() {
            item.remove();
            updateSummary();
            if (!container.children.length) emptyState.style.display = '';
        });

        // Points change
        item.querySelector('.question-points').addEventListener('input', updateSummary);

        // Type change
        const typeSelect = item.querySelector('select');
        const optionsWrap = item.querySelector('.options-wrap');
        const correctWrap = item.querySelector('.correct-wrap');
        typeSelect.addEventListener('change', function() {
            if (this.value === 'multiple-choice') { optionsWrap.style.display=''; correctWrap.style.display=''; correctWrap.querySelector('input').placeholder='Nomor pilihan benar (mis: 1)'; }
            else if (this.value === 'true-false') { optionsWrap.style.display='none'; correctWrap.style.display=''; correctWrap.querySelector('input').placeholder='"true" atau "false"'; }
            else if (this.value === 'essay') { optionsWrap.style.display='none'; correctWrap.style.display='none'; }
            else { optionsWrap.style.display='none'; correctWrap.style.display=''; correctWrap.querySelector('input').placeholder='Nilai rating'; }
        });

        // Add option
        item.querySelector('.add-option').addEventListener('click', function() {
            const list = item.querySelector('.options-list');
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:6px;align-items:center';
            row.innerHTML = `
                <input type="text" name="questions[${idx}][options][]" placeholder="Pilihan baru" style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                <button type="button" style="width:30px;height:30px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:7px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer;font-size:0.7rem" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            `;
            list.appendChild(row);
        });
    });

    durationInput.addEventListener('input', function() {
        document.getElementById('durationDisplay').textContent = this.value + ' menit';
    });

    // Test type toggle
    const testTypeSelect = document.getElementById('test_type');
    const questionsSection = document.getElementById('questionsSection');
    const docSection = document.getElementById('documentEditingSection');
    testTypeSelect.addEventListener('change', function() {
        if (this.value === 'document_editing') { questionsSection.style.display='none'; docSection.style.display=''; }
        else { questionsSection.style.display=''; docSection.style.display='none'; }
    });

    // Add criteria
    let criteriaIndex = 0;
    const criteriaContainer = document.getElementById('criteriaContainer');
    const criteriaEmpty = document.getElementById('criteriaEmptyState');
    document.getElementById('addCriteria').addEventListener('click', function() {
        criteriaEmpty.style.display = 'none';
        const ci = criteriaIndex++;
        const row = document.createElement('div');
        row.style.cssText = 'background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;padding:14px';
        row.innerHTML = `
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                <span style="font-size:0.72rem;font-weight:700;color:var(--dark-text-secondary);text-transform:uppercase;letter-spacing:.06em">Kriteria #<span class="c-num">${ci+1}</span></span>
                <button type="button" class="rm-criteria" style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:6px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer;font-size:0.7rem"><i class="fas fa-times"></i></button>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <input type="text" name="evaluation_criteria[${ci}][category]" placeholder="Kategori (mis: Formatting, Content)" required style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                <textarea name="evaluation_criteria[${ci}][description]" placeholder="Deskripsi kriteria penilaian" rows="2" required style="${inputStyle};resize:vertical" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                    <input type="number" name="evaluation_criteria[${ci}][points]" placeholder="Poin" min="0" step="0.5" required style="${inputStyle}" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    ${makeSelect(`evaluation_criteria[${ci}][type]`, [{v:'Technical',l:'Technical'},{v:'Analysis',l:'Analysis'},{v:'Quality',l:'Quality'},{v:'checkbox',l:'Checkbox'},{v:'rating',l:'Rating'},{v:'numeric',l:'Numeric'}])}
                </div>
            </div>
        `;
        criteriaContainer.appendChild(row);
        row.querySelector('.rm-criteria').addEventListener('click', function() {
            row.remove();
            if (!criteriaContainer.children.length) criteriaEmpty.style.display = '';
        });
    });
});

function updateSummary() {
    const questions = document.querySelectorAll('.question-item');
    let totalPoints = 0;
    questions.forEach((q, i) => {
        const n = q.querySelector('.q-num'); if (n) n.textContent = i + 1;
        const pts = parseFloat(q.querySelector('.question-points')?.value) || 0;
        totalPoints += pts;
    });
    document.getElementById('totalQuestions').textContent = questions.length;
    document.getElementById('totalPoints').textContent = totalPoints;
}

function handleSubmit(form) {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('submit-icon').className = 'fas fa-spinner fa-spin';
    document.getElementById('submit-label').textContent = 'Menyimpan...';
}
</script>
@endpush
@endsection
