@extends('layouts.admin')

@section('title', 'AI Settings')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Konfigurasi</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">AI Settings</h1>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:4px 0 0">Kelola konfigurasi layanan AI, pricing multiplier, dan parameter sistem.</p>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            <a href="{{ route('admin.ai-settings.recent-changes') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:9px;font-size:0.78rem;font-weight:700;text-decoration:none;transition:background .15s"
               onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 20%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 12%,transparent)'">
                <i class="fas fa-history" style="font-size:0.72rem"></i>Riwayat Perubahan
            </a>
            <form action="{{ route('admin.ai-settings.clear-cache') }}" method="POST" class="inline"
                  onsubmit="return confirm('Ini akan menghapus cache kalkulasi harga & parameter AI di server (bukan browser). Lanjutkan?')">
                @csrf
                <button type="submit"
                        title="Hapus cache kalkulasi harga & parameter AI di server. Browser cache tidak terpengaruh."
                        style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);color:var(--apple-orange);border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent);border-radius:9px;font-size:0.78rem;font-weight:700;cursor:pointer;transition:background .15s"
                        onmouseover="this.style.background='color-mix(in srgb,var(--apple-orange) 20%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-orange) 12%,transparent)'">
                    <i class="fas fa-sync-alt" style="font-size:0.72rem"></i>Clear Cache
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:15px;height:15px;border-radius:50%;background:color-mix(in srgb,var(--apple-orange) 30%,transparent);font-size:0.55rem;font-weight:700">?</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px">
            <div style="display:flex;align-items:center;gap:10px">
                <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
                <span style="font-size:0.82rem;color:var(--apple-green);font-weight:600">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-green);opacity:.7"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <i class="fas fa-exclamation-triangle" style="color:var(--apple-red);font-size:1rem;flex-shrink:0;margin-top:2px"></i>
            <ul style="font-size:0.78rem;color:var(--apple-red);margin:0;padding-left:16px">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Category Tabs --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:8px">
        <nav style="display:flex;flex-wrap:wrap;gap:4px">
            @php
                $catIcons = ['pricing'=>'fa-dollar-sign','global'=>'fa-cog','model'=>'fa-brain','service'=>'fa-server','cache'=>'fa-database'];
                $catColors = ['pricing'=>'var(--apple-green)','global'=>'var(--apple-blue)','model'=>'var(--apple-purple)','service'=>'var(--apple-orange)','cache'=>'var(--apple-teal)'];
            @endphp
            @foreach($categories as $cat)
            @php
                $isActive = $category === $cat;
                $catColor = $catColors[$cat] ?? 'var(--apple-blue)';
                $catIcon = $catIcons[$cat] ?? 'fa-cog';
            @endphp
            <a href="{{ route('admin.ai-settings.index', ['category' => $cat]) }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:9px;font-size:0.78rem;font-weight:600;text-decoration:none;transition:all .15s;
                      {{ $isActive ? "background:color-mix(in srgb,{$catColor} 16%,transparent);color:{$catColor};border:1px solid color-mix(in srgb,{$catColor} 35%,transparent)" : 'background:transparent;color:var(--dark-text-secondary);border:1px solid transparent' }}">
                <i class="fas {{ $catIcon }}" style="font-size:0.72rem"></i>
                <span>{{ ucfirst($cat) }}</span>
            </a>
            @endforeach
        </nav>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('admin.ai-settings.update') }}" method="POST" id="settingsForm">
        @csrf
        <div style="display:flex;flex-direction:column;gap:16px">
            @forelse($settings as $groupName => $groupSettings)
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                {{-- Group Header --}}
                <div style="padding:14px 20px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:10px">
                    <div style="width:8px;height:8px;border-radius:50%;background:var(--apple-blue)"></div>
                    <h4 style="font-size:0.7rem;font-weight:700;color:rgba(235,235,245,0.85);text-transform:uppercase;letter-spacing:.1em;margin:0">
                        {{ $groupName ?: 'General' }}
                    </h4>
                    <span style="font-size:0.65rem;color:var(--dark-text-tertiary);background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:6px;padding:2px 8px">
                        {{ count($groupSettings) }} setting
                    </span>
                </div>

                {{-- Group Settings --}}
                <div style="padding:16px 20px">
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
                        @foreach($groupSettings as $setting)
                        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:10px">

                            {{-- Setting Header --}}
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px">
                                <div style="flex:1;min-width:0">
                                    <div style="display:flex;align-items:center;gap:6px">
                                        <span style="font-size:0.8rem;font-weight:700;color:var(--dark-text-primary)">
                                            {{ ucwords(str_replace(['.', '_'], ' ', last(explode('.', $setting->key)))) }}
                                        </span>
                                        @if($setting->is_encrypted)
                                            <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:5px;font-size:0.62rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);color:var(--apple-orange)">
                                                <i class="fas fa-lock" style="font-size:0.55rem"></i>Encrypted
                                            </span>
                                        @endif
                                        @if($setting->requires_restart)
                                            <span style="display:inline-flex;align-items:center;gap:3px;padding:2px 6px;border-radius:5px;font-size:0.62rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue)">
                                                <i class="fas fa-power-off" style="font-size:0.55rem"></i>Restart
                                            </span>
                                        @endif
                                    </div>
                                    @if($setting->description)
                                    <p style="font-size:0.7rem;color:var(--dark-text-tertiary);margin:3px 0 0;line-height:1.4">{{ $setting->description }}</p>
                                    @endif
                                </div>
                                <form action="{{ route('admin.ai-settings.reset', $setting->key) }}" method="POST"
                                      onsubmit="return confirm('Reset ke default: {{ addslashes($setting->default_value) }}?')" style="flex-shrink:0">
                                    @csrf
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:7px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 20%,transparent);cursor:pointer;color:var(--apple-red);transition:background .15s"
                                            onmouseover="this.style.background='color-mix(in srgb,var(--apple-red) 20%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-red) 10%,transparent)'"
                                            title="Reset ke default: {{ $setting->default_value }}">
                                        <i class="fas fa-undo" style="font-size:0.62rem"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Setting Input --}}
                            @php
                                $inputPrefix = '';
                                $inputSuffix = '';
                                $keyLower = strtolower($setting->key);
                                if (str_contains($keyLower, 'percentage') || str_contains($keyLower, '_rate') || str_contains($keyLower, 'overhead')) {
                                    $inputSuffix = '%';
                                } elseif (str_contains($keyLower, 'total') || str_contains($keyLower, 'minimum') || str_contains($keyLower, 'price') || str_contains($keyLower, 'fee') || str_contains($keyLower, 'amount') || str_contains($keyLower, 'cost')) {
                                    $inputPrefix = 'Rp';
                                }
                                $isLargeNumber = $setting->data_type === 'number' && abs((float)($setting->default_value ?? 0)) >= 100000;
                            @endphp
                            @if($setting->data_type === 'boolean')
                                <div style="display:flex;align-items:center;gap:10px">
                                    <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer">
                                        <input type="checkbox"
                                               id="setting_{{ $setting->id }}"
                                               name="settings[{{ $setting->key }}]"
                                               value="1"
                                               class="sr-only peer"
                                               {{ $setting->value ? 'checked' : '' }}>
                                        <div style="width:38px;height:21px;background:var(--dark-separator);border-radius:99px;position:relative;transition:background .2s"
                                             id="toggle_{{ $setting->id }}"
                                             onclick="document.getElementById('setting_{{ $setting->id }}').click()">
                                        </div>
                                    </label>
                                    <span style="font-size:0.75rem;color:var(--dark-text-secondary)" id="toggleLabel_{{ $setting->id }}">
                                        {{ $setting->value ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            @elseif($setting->data_type === 'number')
                                @if($inputPrefix || $inputSuffix)
                                <div style="position:relative;display:flex;align-items:center">
                                    @if($inputPrefix)
                                    <span style="position:absolute;left:10px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);pointer-events:none;z-index:1">{{ $inputPrefix }}</span>
                                    @endif
                                    <input type="number"
                                           id="setting_{{ $setting->id }}"
                                           name="settings[{{ $setting->key }}]"
                                           value="{{ $setting->value }}"
                                           step="any"
                                           data-large-number="{{ $isLargeNumber ? '1' : '0' }}"
                                           @if(isset($setting->validation_rules['min'])) min="{{ $setting->validation_rules['min'] }}" @endif
                                           @if(isset($setting->validation_rules['max'])) max="{{ $setting->validation_rules['max'] }}" @endif
                                           style="width:100%;padding:8px {{ $inputSuffix ? '32px' : '12px' }} 8px {{ $inputPrefix ? '32px' : '12px' }};background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)';formatLargeNumber(this)">
                                    @if($inputSuffix)
                                    <span style="position:absolute;right:10px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);pointer-events:none">{{ $inputSuffix }}</span>
                                    @endif
                                </div>
                                @else
                                <input type="number"
                                       id="setting_{{ $setting->id }}"
                                       name="settings[{{ $setting->key }}]"
                                       value="{{ $setting->value }}"
                                       step="any"
                                       data-large-number="{{ $isLargeNumber ? '1' : '0' }}"
                                       @if(isset($setting->validation_rules['min'])) min="{{ $setting->validation_rules['min'] }}" @endif
                                       @if(isset($setting->validation_rules['max'])) max="{{ $setting->validation_rules['max'] }}" @endif
                                       style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)';formatLargeNumber(this)">
                                @endif
                                @if($isLargeNumber)
                                <p class="num-preview" data-for="setting_{{ $setting->id }}" style="font-size:0.68rem;color:var(--apple-teal);margin:3px 0 0"></p>
                                @endif
                            @elseif($setting->data_type === 'json' || $setting->data_type === 'array')
                                <textarea id="setting_{{ $setting->id }}"
                                          name="settings[{{ $setting->key }}]"
                                          rows="3"
                                          style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.75rem;font-family:monospace;outline:none;resize:vertical;box-sizing:border-box;transition:border-color .2s"
                                          onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ is_array($setting->value) ? json_encode($setting->value, JSON_PRETTY_PRINT) : $setting->value }}</textarea>
                            @else
                                <input type="{{ $setting->is_encrypted ? 'password' : 'text' }}"
                                       id="setting_{{ $setting->id }}"
                                       name="settings[{{ $setting->key }}]"
                                       value="{{ $setting->display_value }}"
                                       style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box;transition:border-color .2s"
                                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @endif

                            {{-- Default Value (only shown when different from current) --}}
                            @php $defaultDiffers = (string)$setting->value !== (string)$setting->default_value; @endphp
                            @if($defaultDiffers)
                            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap">
                                <span style="font-size:0.65rem;color:var(--apple-orange);font-weight:600">Default:</span>
                                <code style="font-size:0.65rem;padding:2px 6px;background:color-mix(in srgb,var(--apple-orange) 10%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-orange) 25%,var(--dark-separator));border-radius:5px;color:var(--apple-orange);font-family:monospace">{{ $setting->default_value }}</code>
                                <span style="font-size:0.6rem;color:var(--dark-text-tertiary)">— nilai saat ini berbeda</span>
                            </div>
                            @else
                            <div style="display:flex;align-items:center;gap:5px;flex-wrap:wrap">
                                <span style="font-size:0.65rem;color:var(--dark-text-tertiary)">Default:</span>
                                <code style="font-size:0.65rem;padding:2px 6px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:5px;color:var(--dark-text-tertiary);font-family:monospace">{{ $setting->default_value }}</code>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:48px 24px;text-align:center">
                <i class="fas fa-cog" style="font-size:2.5rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                <p style="font-size:0.85rem;color:var(--dark-text-secondary);margin:0">Tidak ada setting untuk kategori <strong>{{ $category }}</strong></p>
            </div>
            @endforelse

            @if($settings->isNotEmpty())
            <div style="display:flex;justify-content:center;padding:4px 0">
                <button type="submit" id="saveBtn"
                        style="padding:12px 40px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.9rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:opacity .15s"
                        onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <i class="fas fa-save" id="saveBtnIcon"></i>
                    <span id="saveBtnText">Simpan Semua Perubahan</span>
                    <i class="fas fa-spinner fa-spin" id="saveBtnSpinner" style="display:none"></i>
                </button>
            </div>
            @endif
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Format large number preview
function formatLargeNumber(input) {
    const val = parseFloat(input.value);
    if (!input.dataset.largeNumber || input.dataset.largeNumber === '0') return;
    const preview = document.querySelector('.num-preview[data-for="' + input.id + '"]');
    if (!preview) return;
    if (isNaN(val) || input.value === '') { preview.textContent = ''; return; }
    preview.textContent = '≈ ' + new Intl.NumberFormat('id-ID').format(val);
}

// Init previews on load
document.querySelectorAll('input[type="number"][data-large-number="1"]').forEach(input => {
    formatLargeNumber(input);
    input.addEventListener('input', () => formatLargeNumber(input));
});

// Auto-format JSON textareas
document.querySelectorAll('textarea').forEach(t => {
    t.addEventListener('blur', function() {
        try {
            const json = JSON.parse(this.value);
            this.value = JSON.stringify(json, null, 2);
        } catch(e) {}
    });
});

// Submit guard
document.getElementById('settingsForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true; btn.style.opacity = '0.6'; btn.style.cursor = 'not-allowed';
    document.getElementById('saveBtnIcon').style.display = 'none';
    document.getElementById('saveBtnText').textContent = 'Menyimpan...';
    document.getElementById('saveBtnSpinner').style.display = 'inline-block';
});

// Boolean toggle visual
document.querySelectorAll('input[type="checkbox"].sr-only').forEach(cb => {
    const id = cb.id.replace('setting_', '');
    const toggle = document.getElementById('toggle_' + id);
    const label = document.getElementById('toggleLabel_' + id);
    function update() {
        if (toggle) toggle.style.background = cb.checked ? 'var(--apple-blue)' : 'var(--dark-separator)';
        if (label) label.textContent = cb.checked ? 'Aktif' : 'Nonaktif';
        if (label) label.style.color = cb.checked ? 'var(--apple-blue)' : 'var(--dark-text-secondary)';
    }
    update();
    cb.addEventListener('change', update);
    if (toggle) toggle.addEventListener('click', function() { cb.checked = !cb.checked; cb.dispatchEvent(new Event('change')); });
});
</script>
@endpush
