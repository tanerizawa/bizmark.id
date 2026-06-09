@extends('layouts.admin')

@section('title', 'Edit Template: ' . $permitTemplate->name)

@php
    $activePermitTypes = \App\Models\PermitType::where('is_active', true)
        ->orderBy('name')
        ->get(['id', 'name', 'code']);

    // Build existing items data for JS initialization
    $existingItems = $permitTemplate->items->map(function ($item) {
        return [
            'permit_type_id' => $item->permit_type_id,
            'sequence_order' => $item->sequence_order,
            'is_goal_permit' => $item->is_goal_permit,
            'dependencies' => $item->dependencies->map(function ($dep) {
                return [
                    'sequence' => $dep->dependsOnItem?->sequence_order,
                    'type' => $dep->dependency_type,
                ];
            })->filter(fn($d) => $d['sequence'] !== null)->values(),
        ];
    });
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-8">
    <div class="card-elevated rounded-apple-xl p-6 md:p-8 relative overflow-hidden" style="background: linear-gradient(135deg, rgba(255, 149, 0, 0.2) 0%, rgba(88, 86, 214, 0.35) 50%, rgba(0, 0, 0, 0.75) 100%);">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-orange opacity-20 blur-3xl rounded-full absolute -top-12 -right-16"></div>
        </div>
        <div class="relative space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <a href="{{ route('permit-templates.show', $permitTemplate) }}" class="inline-flex items-center text-xs tracking-widest uppercase hover:text-apple-blue transition-colors" style="color: rgba(235, 235, 245, 0.7);">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke detail template
                </a>
                <span class="px-4 py-1 rounded-full text-xs font-semibold" style="background: rgba(255, 255, 255, 0.08); color: rgba(235, 235, 245, 0.85);">
                    Edit Mode
                </span>
            </div>
            <div>
                <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.5);">Template Builder</p>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">Edit Template</h1>
                <p class="text-base mt-1" style="color: rgba(235, 235, 245, 0.75);">{{ $permitTemplate->name }}</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="card-elevated rounded-apple-lg p-4" style="background: rgba(255, 69, 58, 0.12); border: 1px solid rgba(255, 69, 58, 0.3);">
            <ul class="space-y-1 text-sm" style="color: rgba(255, 69, 58, 0.9);">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('permit-templates.update', $permitTemplate) }}" method="POST" id="template-form" class="space-y-8">
        @csrf
        @method('PATCH')

        <div class="lg:grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <section class="card-elevated rounded-apple-lg p-6 space-y-6">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.45);">Langkah 01</p>
                        <h2 class="text-xl font-semibold" style="color: #FFFFFF;">Informasi Template</h2>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                                Nama Template <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $permitTemplate->name) }}" class="input-dark w-full px-3 py-2 rounded-md @error('name') border-red-500 @enderror" required>
                            @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Deskripsi</label>
                            <textarea name="description" rows="3" class="input-dark w-full px-3 py-2 rounded-md">{{ old('description', $permitTemplate->description) }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Use Case</label>
                            <textarea name="use_case" rows="2" class="input-dark w-full px-3 py-2 rounded-md">{{ old('use_case', $permitTemplate->use_case) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Kategori</label>
                            <input type="text" name="category" value="{{ old('category', $permitTemplate->category) }}" class="input-dark w-full px-3 py-2 rounded-md" placeholder="Contoh: lingkungan, konstruksi">
                        </div>
                        <div class="flex items-center gap-3 pt-6">
                            <input type="checkbox" name="is_public" value="1" id="is_public" class="w-4 h-4 rounded"
                                {{ old('is_public', $permitTemplate->is_public) ? 'checked' : '' }}>
                            <label for="is_public" class="text-sm" style="color: rgba(235, 235, 245, 0.8);">Template publik (bisa dipakai semua admin)</label>
                        </div>
                    </div>
                </section>

                <section class="card-elevated rounded-apple-lg p-6 space-y-6">
                    <div class="flex flex-wrap items-center gap-4 justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235, 235, 245, 0.45);">Langkah 02</p>
                            <h2 class="text-xl font-semibold" style="color: #FFFFFF;">Izin dalam Template</h2>
                        </div>
                        <button type="button" id="add-permit-item" class="px-4 py-2 rounded-lg transition-colors flex items-center gap-2" style="background: rgba(10, 132, 255, 0.22); color: rgba(10, 132, 255, 1);">
                            <i class="fas fa-plus"></i> Tambah Izin
                        </button>
                    </div>
                    <div id="permit-items-container" class="space-y-4"></div>
                    <div id="empty-state" class="rounded-apple-lg border border-dashed border-apple-blue/30 p-8 text-center space-y-3" style="color: rgba(235, 235, 245, 0.6);" aria-live="polite">
                        <i class="fas fa-route text-4xl"></i>
                        <p class="font-semibold">Belum ada izin yang ditambahkan</p>
                    </div>
                </section>
            </div>

            <aside class="lg:col-span-1 space-y-6">
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
                </section>

                <section class="card-elevated rounded-apple-lg p-6 space-y-3">
                    <h3 class="text-base font-semibold" style="color: rgba(255, 149, 0, 0.9);">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Perhatian
                    </h3>
                    <p class="text-sm" style="color: rgba(235, 235, 245, 0.7);">Menyimpan perubahan akan menghapus dan membuat ulang semua izin dan prasyarat dalam template ini.</p>
                </section>
            </aside>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 md:p-5 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold" style="color: rgba(235, 235, 245, 0.85);">Simpan perubahan template?</p>
                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Perubahan akan menggantikan seluruh konfigurasi izin yang ada.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('permit-templates.show', $permitTemplate) }}" class="px-6 py-3 rounded-lg text-center" style="background: rgba(142, 142, 147, 0.25); color: rgba(235, 235, 245, 0.8);">Batal</a>
                <button type="submit" class="px-6 py-3 rounded-lg text-center flex items-center justify-center gap-2" style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                    <i class="fas fa-save"></i> Simpan Perubahan
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
    };

    const permitTypesRaw = @json($activePermitTypes);
    const permitTypes = Array.isArray(permitTypesRaw) ? permitTypesRaw : [];
    const existingItems = @json($existingItems);
    let itemCount = 0;

    function buildDependencies(sequence) {
        const dependencies = [];
        Array.from(container.children).forEach((item) => {
            const itemSequence = Number(item.dataset.sequence);
            if (itemSequence >= sequence) return;
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
            if (!checkbox.checked) return;
            const seq = checkbox.dataset.dependencySequence;
            selections.add(seq);
            const typeSelect = item.querySelector(`select[data-dependency-type="${seq}"]`);
            if (typeSelect) types[seq] = typeSelect.value;
        });
        return { selections, types };
    }

    function renderDependencySection(item, preselected = null) {
        const wrapper = item.querySelector('[data-dependency-wrapper]');
        if (!wrapper) return;
        const sequence = Number(item.dataset.sequence);
        const index = sequence - 1;
        const availableDependencies = buildDependencies(sequence);
        const storedState = preselected || captureDependencyState(item);

        if (!availableDependencies.length) {
            wrapper.innerHTML = `<div class="text-sm p-3 rounded-lg" style="background: rgba(255, 159, 10, 0.1); color: rgba(255, 159, 10, 0.95);"><i class="fas fa-info-circle mr-2"></i>Tidak ada izin sebelumnya untuk dijadikan prasyarat.</div>`;
            return;
        }

        wrapper.innerHTML = availableDependencies.map((dependency) => {
            const seq = String(dependency.sequence);
            const isChecked = storedState.selections instanceof Set
                ? storedState.selections.has(seq)
                : storedState.selections?.has?.(seq) ?? false;
            const dependencyType = (storedState.types && storedState.types[seq]) || 'MANDATORY';
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
        if (!emptyState) return;
        emptyState.style.display = container.children.length > 0 ? 'none' : 'block';
    }

    function updateSummary() {
        const totalItems = container.children.length;
        const goalItems = container.querySelectorAll('input[name*="[is_goal_permit]"]:checked').length;
        const dependencyCount = container.querySelectorAll('input[data-dependency-sequence]:checked').length;
        if (summaryTargets.total) summaryTargets.total.textContent = totalItems;
        if (summaryTargets.goals) summaryTargets.goals.textContent = goalItems;
        if (summaryTargets.dependencies) summaryTargets.dependencies.textContent = dependencyCount;
    }

    function createPermitItem(sequence) {
        const index = sequence - 1;
        const item = document.createElement('div');
        item.className = 'permit-item card-elevated rounded-apple-lg p-4 md:p-5 space-y-4';
        item.dataset.sequence = sequence;
        item.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg" data-sequence-label style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">${sequence}</div>
                <div class="flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">Pilih Jenis Izin <span class="text-red-500">*</span></label>
                        <select name="items[${index}][permit_type_id]" class="permit-type-select input-dark w-full px-3 py-2 rounded-md" required>
                            <option value="">-- Pilih Izin --</option>
                            ${permitTypes.map((type) => `<option value="${type.id}">${type.name} (${type.code})</option>`).join('')}
                        </select>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="items[${index}][is_goal_permit]" value="1" class="w-4 h-4 rounded">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);"><i class="fas fa-flag mr-1" style="color: rgba(10, 132, 255, 1);"></i>Tandai sebagai goal permit</span>
                        </label>
                        <button type="button" class="remove-item w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(255, 69, 58, 0.15); color: rgba(255, 69, 58, 1);" aria-label="Hapus izin">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="space-y-2" data-dependency-wrapper></div>
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
            if (badge) badge.textContent = sequence;
            item.querySelectorAll('[name]').forEach((element) => {
                const name = element.getAttribute('name');
                if (name) element.setAttribute('name', name.replace(/items\[\d+\]/, `items[${idx}]`));
            });
            const hiddenSeq = item.querySelector('[data-sequence-order]');
            if (hiddenSeq) hiddenSeq.value = sequence;
            renderDependencySection(item);
        });
        itemCount = container.children.length;
    }

    function refreshDependencies(startSequence = 2) {
        Array.from(container.children).forEach((item) => {
            if (Number(item.dataset.sequence) >= startSequence) renderDependencySection(item);
        });
        updateSummary();
    }

    // Initialize from existing template items
    existingItems.forEach((data, idx) => {
        itemCount += 1;
        const item = createPermitItem(itemCount);
        container.appendChild(item);

        // Pre-select permit type
        const select = item.querySelector('.permit-type-select');
        if (select && data.permit_type_id) {
            select.value = data.permit_type_id;
        }

        // Pre-check goal permit
        if (data.is_goal_permit) {
            const checkbox = item.querySelector('input[name*="[is_goal_permit]"]');
            if (checkbox) checkbox.checked = true;
        }
    });

    // Render dependency sections after all items are added, then pre-select
    Array.from(container.children).forEach((item, idx) => {
        const data = existingItems[idx];
        if (!data) return;
        const preselected = {
            selections: new Set(data.dependencies.map((d) => String(d.sequence))),
            types: Object.fromEntries(data.dependencies.map((d) => [String(d.sequence), d.type])),
        };
        renderDependencySection(item, preselected);
    });

    updateEmptyState();
    updateSummary();

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
            if (item) refreshDependencies(Number(item.dataset.sequence) + 1);
        }
        if (event.target.matches('input[name*="[is_goal_permit]"]') || event.target.matches('input[data-dependency-sequence]')) {
            updateSummary();
        }
    });
});
</script>
@endpush
@endsection
