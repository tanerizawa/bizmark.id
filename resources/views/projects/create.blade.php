@extends('layouts.admin')

@section('title', 'Tambah Proyek')
@section('page-title', 'Tambah Proyek Baru')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Error Alert --}}
    @if($errors->any())
        <div style="background:color-mix(in srgb,var(--apple-red) 10%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:14px;padding:16px 20px;display:flex;align-items:flex-start;gap:12px">
            <i class="fas fa-exclamation-triangle" style="color:var(--apple-red);font-size:1rem;margin-top:2px;flex-shrink:0"></i>
            <div>
                <p style="font-size:0.85rem;font-weight:700;color:var(--apple-red);margin:0 0 6px">Terdapat {{ $errors->count() }} kesalahan pada form</p>
                <ul style="margin:0;padding-left:16px">
                    @foreach($errors->all() as $error)
                        <li style="font-size:0.8rem;color:var(--dark-text-secondary);margin-bottom:3px">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('projects.store') }}" method="POST" id="create-project-form">
        @csrf

        {{-- Section 1 --}}
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden;margin-bottom:16px">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
                <div>
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Form</p>
                    <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Informasi Proyek</h3>
                </div>
                <span style="font-size:0.72rem;color:var(--dark-text-secondary)"><span style="color:var(--apple-red)">*</span> wajib diisi</span>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:18px">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Nama Proyek <span style="color:var(--apple-red)">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               placeholder="Contoh: Perizinan AMDAL PT. XYZ"
                               style="width:100%;padding:9px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                               onfocus="this.style.borderColor='var(--apple-blue)'"
                               onblur="this.style.borderColor='var(--dark-separator)'">
                        @error('name')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Klien <span style="color:var(--apple-red)">*</span>
                        </label>
                        <div style="position:relative">
                            <select name="client_id" id="client_id" required
                                    style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                    onfocus="this.style.borderColor='var(--apple-blue)'"
                                    onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">Pilih Klien</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company_name ?? $client->name }}{{ ($client->company_name && $client->name != $client->company_name) ? ' ('.$client->name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                        </div>
                        @error('client_id')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @else
                            <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:5px 0 0">
                                <i class="fas fa-info-circle" style="margin-right:4px;color:var(--apple-blue)"></i>
                                Belum ada?
                                <a href="{{ route('clients.create') }}" target="_blank"
                                   style="color:var(--apple-blue);text-decoration:none"
                                   onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">Tambah Klien Baru</a>
                            </p>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Status Awal <span style="color:var(--apple-red)">*</span>
                        </label>
                        <div style="position:relative">
                            <select name="status_id" id="status_id" required
                                    style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                    onfocus="this.style.borderColor='var(--apple-blue)'"
                                    onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">Pilih Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                        </div>
                        @error('status_id')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Institusi Terkait
                        </label>
                        <div style="position:relative">
                            <select name="institution_id" id="institution_id"
                                    style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .2s"
                                    onfocus="this.style.borderColor='var(--apple-blue)'"
                                    onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">Pilih Institusi</option>
                                @foreach($institutions as $institution)
                                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>{{ $institution->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);font-size:0.6rem;color:var(--dark-text-tertiary);pointer-events:none"></i>
                        </div>
                        @error('institution_id')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                        Deskripsi Proyek
                    </label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Jelaskan detail proyek perizinan ini..."
                              style="width:100%;padding:9px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s;font-family:inherit"
                              onfocus="this.style.borderColor='var(--apple-blue)'"
                              onblur="this.style.borderColor='var(--dark-separator)'">{{ old('description') }}</textarea>
                    @error('description')
                        <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Section 2 --}}
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden;margin-bottom:16px">
            <div style="padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Form</p>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Konfigurasi Proyek</h3>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:18px">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                               style="width:100%;padding:9px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;transition:border-color .2s;color-scheme:dark"
                               onfocus="this.style.borderColor='var(--apple-blue)'"
                               onblur="this.style.borderColor='var(--dark-separator)'">
                        @error('start_date')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                            Target Selesai (Deadline)
                        </label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                               style="width:100%;padding:9px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;transition:border-color .2s;color-scheme:dark"
                               onfocus="this.style.borderColor='var(--apple-blue)'"
                               onblur="this.style.borderColor='var(--dark-separator)'">
                        @error('deadline')
                            <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                        Budget (Rp)
                    </label>
                    <div style="position:relative">
                        <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.8rem;color:var(--dark-text-secondary);pointer-events:none;font-weight:600">Rp</span>
                        <input type="number" name="budget" id="budget" value="{{ old('budget') }}" step="1000" min="0"
                               placeholder="0"
                               style="width:100%;padding:9px 14px 9px 38px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                               onfocus="this.style.borderColor='var(--apple-blue)'"
                               onblur="this.style.borderColor='var(--dark-separator)'">
                    </div>
                    @error('budget')
                        <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:10px">
                        Prioritas Proyek
                    </label>
                    @php
                    $priorities = [
                        'low'    => ['label' => 'Rendah',  'color' => 'var(--apple-teal)',   'icon' => 'fa-arrow-down'],
                        'medium' => ['label' => 'Sedang',  'color' => 'var(--apple-orange)', 'icon' => 'fa-minus'],
                        'high'   => ['label' => 'Tinggi',  'color' => 'var(--apple-red)',    'icon' => 'fa-arrow-up'],
                    ];
                    $selectedPriority = old('priority', 'medium');
                    @endphp
                    <div style="display:flex;gap:10px">
                        @foreach($priorities as $val => $p)
                            @php $isSelected = $selectedPriority === $val; @endphp
                            <label style="display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:10px;cursor:pointer;border:1px solid {{ $isSelected ? 'color-mix(in srgb,'.$p['color'].' 50%,var(--dark-separator))' : 'var(--dark-separator)' }};background:{{ $isSelected ? 'color-mix(in srgb,'.$p['color'].' 12%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};transition:all .15s"
                                   id="priority-label-{{ $val }}" onclick="setPriority('{{ $val }}')">
                                <input type="radio" name="priority" value="{{ $val }}"
                                       {{ $isSelected ? 'checked' : '' }}
                                       style="display:none">
                                <i class="fas {{ $p['icon'] }}" style="font-size:0.7rem;color:{{ $isSelected ? $p['color'] : 'var(--dark-text-secondary)' }}" id="priority-icon-{{ $val }}"></i>
                                <span style="font-size:0.8rem;font-weight:600;color:{{ $isSelected ? $p['color'] : 'var(--dark-text-secondary)' }}" id="priority-text-{{ $val }}">{{ $p['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('priority')
                        <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                        Catatan Tambahan
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Catatan internal atau informasi penting lainnya..."
                              style="width:100%;padding:9px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s;font-family:inherit"
                              onfocus="this.style.borderColor='var(--apple-blue)'"
                              onblur="this.style.borderColor='var(--dark-separator)'">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p style="font-size:0.72rem;color:var(--apple-red);margin:5px 0 0"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px">
            <a href="{{ route('projects.index') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;font-size:0.8rem;font-weight:600;color:var(--dark-text-secondary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;text-decoration:none"
               onmouseover="this.style.color='var(--dark-text-primary)';this.style.borderColor='var(--dark-text-secondary)'"
               onmouseout="this.style.color='var(--dark-text-secondary)';this.style.borderColor='var(--dark-separator)'">
                <i class="fas fa-times"></i>Batal
            </a>
            <button type="submit" id="submitBtn"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 24px;font-size:0.8rem;font-weight:700;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;cursor:pointer"
                    onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                <i class="fas fa-plus"></i>Buat Proyek
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
const _priorityCfg = {
    low:    { color: 'var(--apple-teal)',   bg: 'color-mix(in srgb,var(--apple-teal) 12%,var(--dark-bg-tertiary))',    border: 'color-mix(in srgb,var(--apple-teal) 50%,var(--dark-separator))' },
    medium: { color: 'var(--apple-orange)', bg: 'color-mix(in srgb,var(--apple-orange) 12%,var(--dark-bg-tertiary))', border: 'color-mix(in srgb,var(--apple-orange) 50%,var(--dark-separator))' },
    high:   { color: 'var(--apple-red)',    bg: 'color-mix(in srgb,var(--apple-red) 12%,var(--dark-bg-tertiary))',    border: 'color-mix(in srgb,var(--apple-red) 50%,var(--dark-separator))' },
};

function setPriority(val) {
    const radio = document.querySelector('input[name="priority"][value="' + val + '"]');
    if (radio) radio.checked = true;
    Object.keys(_priorityCfg).forEach(k => {
        const label = document.getElementById('priority-label-' + k);
        const icon  = document.getElementById('priority-icon-' + k);
        const text  = document.getElementById('priority-text-' + k);
        const cfg   = _priorityCfg[k];
        const active = k === val;
        label.style.background  = active ? cfg.bg     : 'var(--dark-bg-tertiary)';
        label.style.borderColor = active ? cfg.border : 'var(--dark-separator)';
        icon.style.color        = active ? cfg.color  : 'var(--dark-text-secondary)';
        text.style.color        = active ? cfg.color  : 'var(--dark-text-secondary)';
    });
}

document.getElementById('create-project-form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
    btn.style.opacity = '.7';
});
</script>
@endpush
