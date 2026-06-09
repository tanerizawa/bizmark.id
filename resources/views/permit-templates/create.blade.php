@extends('layouts.admin')

@section('title', 'Buat Template Baru')

@php
    $activePermitTypes = \App\Models\PermitType::where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="card-elevated rounded-apple-xl p-6 md:p-8 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(10, 132, 255, 0.25) 0%, rgba(88, 86, 214, 0.4) 50%, rgba(0, 0, 0, 0.75) 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-12 -right-16"></div>
            <div class="w-40 h-40 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-12"></div>
        </div>
        <div class="relative space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <a href="{{ route('permit-templates.index') }}" class="inline-flex items-center text-xs tracking-widest uppercase hover:text-apple-blue transition-colors" style="color: rgba(235, 235, 245, 0.7);">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke daftar template
                </a>
                <span class="px-4 py-1 rounded-full text-xs font-semibold" style="background: rgba(255, 255, 255, 0.08); color: rgba(235, 235, 245, 0.85);">
                    Referensi: Tim Drafter Dokumen Lingkungan Bizmark
                </span>
            </div>
            <div class="md:flex md:items-center md:justify-between gap-8">
                <div class="space-y-3 max-w-2xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.5);">Template Builder</p>
                    <h1 class="text-3xl md:text-4xl font-bold" style="color: #FFFFFF;">Susun rangkaian izin dengan pengalaman sekelas halaman karir Bizmark</h1>
                    <p class="text-base" style="color: rgba(235, 235, 245, 0.75);">
                        Terinspirasi dari proses rekrutmen Drafter Dokumen Lingkungan Teknis, kami menghadirkan alur kerja yang runtut, human-friendly, dan siap dieksekusi tanpa kebingungan.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 w-full md:w-auto">
                    <div class="rounded-apple-lg p-4" style="background: rgba(58, 58, 60, 0.55);">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(235, 235, 245, 0.55);">Jenis Izin Aktif</p>
                        <p class="text-3xl font-bold mt-2" style="color: #FFFFFF;">{{ $activePermitTypes->count() }}</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.5);">Siap dipakai di template</p>
                    </div>
                    <div class="rounded-apple-lg p-4" style="background: rgba(10, 132, 255, 0.15);">
                        <p class="text-xs uppercase tracking-widest" style="color: rgba(10, 132, 255, 0.9);">Fokus UX</p>
                        <p class="text-3xl font-bold mt-2" style="color: rgba(10, 132, 255, 1);">100%</p>
                        <p class="text-xs" style="color: rgba(235, 235, 245, 0.7);">Minim bug, jelas untuk user</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('permit-templates.store') }}" method="POST" id="template-form" class="space-y-8">
        @csrf
        <div class="lg:grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <section class="card-elevated rounded-apple-lg p-6 space-y-6">
                    <div class="flex flex-wrap items-start gap-3 justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.45);">Langkah 01</p>
                            <h2 class="text-xl font-semibold" style="color: #FFFFFF;">Informasi Template</h2>
                            <p class="text-sm" style="color: rgba(235, 235, 245, 0.7);">Pastikan struktur dasar template relevan dengan proyek yang akan dieksekusi.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 0.9);">Kelengkapan Data</span>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: rgba(255, 159, 10, 0.15); color: rgba(255, 159, 10, 0.9);">Transparansi Tim</span>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Nama Template <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" class="input-dark w-full px-3 py-2 rounded-md @error('name') border-red-500 @enderror" placeholder="Contoh: UKL-UPL Pembangunan Kawasan" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi</label>
                            <textarea name="description" rows="3" class="input-dark w-full px-3 py-2 rounded-md @error('description') border-red-500 @enderror" placeholder="Jelaskan scope template atau departemen pengguna...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Estimasi Total Hari</label>
                            <input type="number" name="estimated_days" value="{{ old('estimated_days') }}" min="1" class="input-dark w-full px-3 py-2 rounded-md @error('estimated_days') border-red-500 @enderror" placeholder="Contoh: 95">
                            @error('estimated_days')
                                <p class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Estimasi Total Biaya (Rp)</label>
                            <input type="number" name="estimated_cost" value="{{ old('estimated_cost') }}" min="0" step="100000" class="input-dark w-full px-3 py-2 rounded-md @error('estimated_cost') border-red-500 @enderror" placeholder="Contoh: 73000000">
                            @error('estimated_cost')
                                <p class="mt-1 text-sm text-red-500" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <section class="card-elevated rounded-apple-lg p-6 space-y-6">
                    <div class="flex flex-wrap items-center gap-4 justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.45);">Langkah 02</p>
                            <h2 class="text-xl font-semibold" style="color: #FFFFFF;">Izin dalam Template</h2>
                            <p class="text-sm" style="color: rgba(235, 235, 245, 0.7);">Susun urutan izin, tandai goal permit, dan tentukan prasyarat antar izin.</p>
                        </div>
                        <button type="button" id="add-permit-item" class="px-4 py-2 rounded-lg transition-colors flex items-center gap-2" style="background: rgba(10, 132, 255, 0.22); color: rgba(10, 132, 255, 1);">
                            <i class="fas fa-plus"></i>
                            Tambah Izin
                        </button>
                    </div>
                    <div class="rounded-apple-lg p-4" style="background: rgba(58, 58, 60, 0.5);">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-lightbulb text-xl" style="color: rgba(255, 214, 10, 1);"></i>
                            <p class="text-sm" style="color: rgba(235, 235, 245, 0.75);">
                                Tips Bizmark: perlakukan setiap izin seperti deliverable rekrutmen—jelas tujuannya, siapa penanggung jawabnya, dan apa dependensinya.
                            </p>
                        </div>
                    </div>
                    <div id="permit-items-container" class="space-y-4"></div>
                    <div id="empty-state" class="rounded-apple-lg border border-dashed border-apple-blue/30 p-8 text-center space-y-3" style="color: rgba(235, 235, 245, 0.6);" aria-live="polite">
                        <i class="fas fa-route text-4xl"></i>
                        <p class="font-semibold">Belum ada izin yang ditambahkan</p>
                        <p class="text-sm">Klik "Tambah Izin" untuk mulai menyusun perjalanan dokumen.</p>
                    </div>
                </section>
            </div>

            <aside class="lg:col-span-1 space-y-6">
                <section class="card-elevated rounded-apple-lg p-6 space-y-4">
                    <h3 class="text-lg font-semibold" style="color: #FFFFFF;">Checklist ala Tim Drafter</h3>
                    <p class="text-sm" style="color: rgba(235, 235, 245, 0.7);">Adopsi budaya kerja halaman karir Bizmark untuk menjaga pengalaman kandidat maupun stakeholder internal.</p>
                    <ul class="space-y-3 text-sm" style="color: rgba(235, 235, 245, 0.75);">
                        <li class="flex gap-3"><span class="w-8 h-8 rounded-full flex items-center justify-center font-semibold" style="background: rgba(10, 132, 255, 0.15); color: rgba(10, 132, 255, 1);">1</span>Kontekstualkan template secara ringkas.</li>
                        <li class="flex gap-3"><span class="w-8 h-8 rounded-full flex items-center justify-center font-semibold" style="background: rgba(90, 200, 250, 0.15); color: rgba(90, 200, 250, 1);">2</span>Selaraskan nama izin dengan basis data.</li>
                        <li class="flex gap-3"><span class="w-8 h-8 rounded-full flex items-center justify-center font-semibold" style="background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">3</span>Tandai goal permit agar target akhir jelas.</li>
                        <li class="flex gap-3"><span class="w-8 h-8 rounded-full flex items-center justify-center font-semibold" style="background: rgba(255, 149, 0, 0.15); color: rgba(255, 149, 0, 1);">4</span>Bedakan dependensi wajib dan opsional.</li>
                    </ul>
                </section>

                <section class="card-elevated rounded-apple-lg p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold" style="color: #FFFFFF;">Ringkasan Template</h3>
                        <span class="text-xs px-2 py-1 rounded-full" style="background: rgba(84, 84, 88, 0.4); color: rgba(235, 235, 245, 0.8);">Live</span>
                    </div>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt style="color: rgba(235, 235, 245, 0.65);">Total Izin</dt>
                            <dd class="font-semibold" style="color: #FFFFFF;" data-summary="total-items">0</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt style="color: rgba(235, 235, 245, 0.65);">Goal Permit</dt>
                            <dd class="font-semibold" style="color: rgba(52, 199, 89, 0.9);" data-summary="goal-items">0</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt style="color: rgba(235, 235, 245, 0.65);">Prasyarat Terdefinisi</dt>
                            <dd class="font-semibold" style="color: rgba(10, 132, 255, 0.9);" data-summary="dependencies">0</dd>
                        </div>
                    </dl>
                    <p class="text-xs" style="color: rgba(235, 235, 245, 0.55);" data-summary-empty>Tambahkan izin untuk melihat metrik kemajuan.</p>
                </section>

                <section class="card-elevated rounded-apple-lg p-6 space-y-3">
                    <h3 class="text-lg font-semibold" style="color: #FFFFFF;">Butuh second opinion?</h3>
                    <p class="text-sm" style="color: rgba(235, 235, 245, 0.7);">Tim Environment Bizmark siap membantu review sebelum template dirilis.</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" style="color: #FFFFFF;">cs@bizmark.id</p>
                            <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">SLA &lt; 1 x 24 jam kerja</p>
                        </div>
                    </div>
                </section>
            </aside>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 md:p-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(235, 235, 245, 0.85);">Siap mempublikasikan template?</p>
                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Pastikan setiap izin memiliki data lengkap dan dependensi yang jelas.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('permit-templates.index') }}" class="px-6 py-3 rounded-lg text-center" style="background: rgba(142, 142, 147, 0.25); color: rgba(235, 235, 245, 0.8);">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-lg text-center flex items-center justify-center gap-2" style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save"></i>
                    Simpan Template
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('permit-items-container');
    const addButton = document.getElementById('add-permit-item');
    const emptyState = document.getElementById('empty-state');
    const summaryTargets = {
        total: document.querySelector('[data-summary="total-items"]'),
        goals: document.querySelector('[data-summary="goal-items"]'),
        dependencies: document.querySelector('[data-summary="dependencies"]'),
        empty: document.querySelector('[data-summary-empty]')
    };

    const permitTypesRaw = @json($activePermitTypes);
    const permitTypes = Array.isArray(permitTypesRaw) ? permitTypesRaw : [];
    let itemCount = 0;

    if (!container) {
        return;
    }

    if (!permitTypes.length) {
        if (addButton) {
            addButton.disabled = true;
            addButton.classList.add('opacity-50', 'cursor-not-allowed');
            addButton.setAttribute('title', 'Tambahkan jenis izin aktif terlebih dahulu');
        }
        if (emptyState) {
            emptyState.innerHTML = `
                <div class="space-y-2">
                    <i class="fas fa-exclamation-triangle text-3xl" style="color: rgba(255, 149, 0, 1);"></i>
                    <p class="font-semibold">Belum ada jenis izin aktif</p>
                    <p class="text-sm">Silakan tambahkan data izin pada menu Master Data sebelum membuat template.</p>
                </div>
            `;
        }
        if (summaryTargets.empty) {
            summaryTargets.empty.textContent = 'Jenis izin belum tersedia. Perbarui master data terlebih dahulu.';
        }
        return;
    }

    function buildDependencies(sequence) {
        const dependencies = [];
        Array.from(container.children).forEach((item) => {
            const itemSequence = Number(item.dataset.sequence);
            if (itemSequence >= sequence) {
                return;
            }
            const select = item.querySelector('.permit-type-select');
            if (select && select.value) {
                const label = select.options[select.selectedIndex].text;
                dependencies.push({ sequence: itemSequence, label });
            }
        });
        return dependencies;
    }

    function captureDependencyState(item) {
        const selections = new Set();
        const types = {};
        item.querySelectorAll('input[data-dependency-sequence]').forEach((checkbox) => {
            if (!checkbox.checked) {
                return;
            }
            const seq = checkbox.dataset.dependencySequence;
            selections.add(seq);
            const typeSelect = item.querySelector(`select[data-dependency-type="${seq}"]`);
            if (typeSelect) {
                types[seq] = typeSelect.value;
            }
        });
        return { selections, types };
    }

    function renderDependencySection(item) {
        const wrapper = item.querySelector('[data-dependency-wrapper]');
        if (!wrapper) {
            return;
        }
        const sequence = Number(item.dataset.sequence);
        const index = sequence - 1;
        const availableDependencies = buildDependencies(sequence);
        const storedState = captureDependencyState(item);

        if (!availableDependencies.length) {
            wrapper.innerHTML = `
                <div class="text-sm p-3 rounded-lg" style="background: rgba(255, 159, 10, 0.1); color: rgba(255, 159, 10, 0.95);">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tidak ada izin sebelumnya untuk dijadikan prasyarat.
                </div>
            `;
            return;
        }

        wrapper.innerHTML = availableDependencies.map((dependency) => {
            const seq = String(dependency.sequence);
            const isChecked = storedState.selections.has(seq);
            const dependencyType = storedState.types[seq] || 'MANDATORY';
            return `
                <label class="flex items-center gap-3 p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.4);">
                    <input type="checkbox" name="items[${index}][dependencies][]" value="${dependency.sequence}" class="w-4 h-4 rounded" data-dependency-sequence="${dependency.sequence}" ${isChecked ? 'checked' : ''}>
                    <span class="text-sm flex-1" style="color: rgba(235, 235, 245, 0.85);">${dependency.sequence}. ${dependency.label}</span>
                    <select name="items[${index}][dependency_types][${dependency.sequence}]" class="text-xs px-2 py-1 rounded-md" style="background: rgba(58, 58, 60, 0.8); color: rgba(235, 235, 245, 0.85);" data-dependency-type="${dependency.sequence}">
                        <option value="MANDATORY" ${dependencyType === 'MANDATORY' ? 'selected' : ''}>Wajib</option>
                        <option value="OPTIONAL" ${dependencyType === 'OPTIONAL' ? 'selected' : ''}>Opsional</option>
                    </select>
                </label>
            `;
        }).join('');
    }

    function updateEmptyState() {
        if (!emptyState) {
            return;
        }
        const hasItems = container.children.length > 0;
        emptyState.style.display = hasItems ? 'none' : 'block';
    }

    function updateSummary() {
        const totalItems = container.children.length;
        const goalItems = container.querySelectorAll('input[name*="[is_goal_permit]"]:checked').length;
        const dependencyCount = container.querySelectorAll('input[data-dependency-sequence]:checked').length;
        if (summaryTargets.total) summaryTargets.total.textContent = totalItems;
        if (summaryTargets.goals) summaryTargets.goals.textContent = goalItems;
        if (summaryTargets.dependencies) summaryTargets.dependencies.textContent = dependencyCount;
        if (summaryTargets.empty) {
            summaryTargets.empty.style.display = totalItems ? 'none' : 'block';
        }
    }

    function createPermitItem(sequence) {
        const index = sequence - 1;
        const item = document.createElement('div');
        item.className = 'permit-item card-elevated rounded-apple-lg p-4 md:p-5 space-y-4';
        item.dataset.sequence = sequence;
        item.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" data-sequence-label style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                    ${sequence}
                </div>
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                            Pilih Jenis Izin <span class="text-red-500">*</span>
                        </label>
                        <select name="items[${index}][permit_type_id]" class="permit-type-select input-dark w-full px-3 py-2 rounded-md" required>
                            <option value="">-- Pilih Izin --</option>
                            ${permitTypes.map((type) => `<option value="${type.id}">${type.name} (${type.code})</option>`).join('')}
                        </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="items[${index}][is_goal_permit]" value="1" class="w-4 h-4 rounded">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);">
                                <i class="fas fa-flag mr-1" style="color: rgba(10, 132, 255, 1);"></i>
                                Tandai sebagai goal permit
                            </span>
                        </label>
                        <button type="button" class="remove-item w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(255, 69, 58, 0.15); color: rgba(255, 69, 58, 1);" aria-label="Hapus izin">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="space-y-2" data-dependency-wrapper>
                        <div class="text-sm p-3 rounded-lg" style="background: rgba(255, 149, 0, 0.08); color: rgba(255, 149, 0, 0.95);">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Menyiapkan daftar prasyarat...
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="items[${index}][sequence_order]" value="${sequence}" data-sequence-order>
        `;
        return item;
    }

    function renumberItems() {
        Array.from(container.children).forEach((item, idx) => {
            const sequence = idx + 1;
            item.dataset.sequence = sequence;
            const badge = item.querySelector('[data-sequence-label]');
            if (badge) {
                badge.textContent = sequence;
            }
            item.querySelectorAll('[name]').forEach((element) => {
                const name = element.getAttribute('name');
                if (!name) {
                    return;
                }
                element.setAttribute('name', name.replace(/items\[\d+\]/, `items[${idx}]`));
            });
            const hiddenSeq = item.querySelector('[data-sequence-order]');
            if (hiddenSeq) {
                hiddenSeq.value = sequence;
            }
            renderDependencySection(item);
        });
        itemCount = container.children.length;
    }

    function refreshDependencies(startSequence = 2) {
        Array.from(container.children).forEach((item) => {
            const sequence = Number(item.dataset.sequence);
            if (sequence >= startSequence) {
                renderDependencySection(item);
            }
        });
        updateSummary();
    }

    if (addButton) {
        addButton.addEventListener('click', () => {
            itemCount += 1;
            const newItem = createPermitItem(itemCount);
            container.appendChild(newItem);
            renderDependencySection(newItem);
            updateEmptyState();
            updateSummary();
        });
    }

    container.addEventListener('click', (event) => {
        const removeButton = event.target.closest('.remove-item');
        if (removeButton) {
            removeButton.closest('.permit-item')?.remove();
            renumberItems();
            updateEmptyState();
            updateSummary();
        }
    });

    container.addEventListener('change', (event) => {
        if (event.target.classList.contains('permit-type-select')) {
            const item = event.target.closest('.permit-item');
            if (!item) {
                return;
            }
            const nextSequence = Number(item.dataset.sequence) + 1;
            refreshDependencies(nextSequence);
        }
        if (event.target.matches('input[name*="[is_goal_permit]"]') || event.target.matches('input[data-dependency-sequence]')) {
            updateSummary();
        }
    });

    updateEmptyState();
    updateSummary();
});
</script>
@endpush
@endsection
