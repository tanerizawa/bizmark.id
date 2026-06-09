@extends('layouts.admin')
@section('title', 'Tambah Layanan')
@section('page-title', 'Tambah Layanan')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- AI Generator Panel --}}
    <div id="aiPanel" style="background:linear-gradient(135deg,rgba(0,122,255,0.08) 0%,rgba(90,200,250,0.05) 100%);border:1px solid rgba(0,122,255,0.25);border-radius:16px;padding:20px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
            <div style="width:34px;height:34px;border-radius:10px;background:rgba(0,122,255,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-wand-magic-sparkles" style="color:var(--apple-blue)"></i>
            </div>
            <div>
                <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">AI Service Generator</h3>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">Masukkan topik layanan, AI akan mengisi form secara otomatis</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            <div id="ai-input-wrapper" style="flex:1;min-width:220px;display:flex;align-items:center;background:var(--dark-bg-secondary);border:1px solid rgba(0,122,255,0.25);border-radius:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s">
                <span style="padding:10px 12px;color:var(--dark-text-tertiary);flex-shrink:0"><i class="fas fa-lightbulb" style="font-size:0.75rem"></i></span>
                <input type="text" id="ai-topic-input" placeholder="Contoh: Izin Lingkungan, Sertifikasi ISO 14001..."
                       style="flex:1;border:none;background:transparent;padding:10px 12px 10px 0;font-size:0.85rem;color:var(--dark-text-primary);outline:none">
            </div>
            <button type="button" id="ai-generate-btn"
                    onclick="aiGenerate()"
                    style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;font-size:0.82rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;cursor:pointer;white-space:nowrap">
                <span id="ai-btn-text"><i class="fas fa-wand-magic-sparkles" style="margin-right:4px"></i>Generate</span>
                <span id="ai-btn-loading" style="display:none;align-items:center;gap:6px">
                    <i class="fas fa-spinner" style="animation:spin .8s linear infinite"></i>Generating...
                </span>
            </button>
        </div>
        <div id="ai-error-div" style="display:none;margin-top:10px;padding:10px 14px;background:rgba(255,59,48,0.1);border:1px solid rgba(255,59,48,0.3);border-radius:8px;font-size:0.78rem;color:var(--apple-red)"></div>
        <div id="ai-success-div" style="display:none;margin-top:10px;padding:10px 14px;background:rgba(52,199,89,0.1);border:1px solid rgba(52,199,89,0.3);border-radius:8px;font-size:0.78rem;color:var(--apple-green)">
            <i class="fas fa-check-circle" style="margin-right:6px"></i>Form berhasil diisi oleh AI. Silakan periksa dan sesuaikan isian sebelum menyimpan.
        </div>
    </div>

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:10px">
        <a href="{{ route('admin.services.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;padding:7px 14px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left"></i>Kembali
        </a>
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Layanan Baru</p>
            <h2 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Tambah Layanan</h2>
        </div>
    </div>

    {{-- Form --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:24px">
        <form method="POST" action="{{ route('admin.services.store') }}">
            @csrf

            @if($errors->any())
            <div style="padding:12px 16px;background:rgba(255,59,48,.1);border:1px solid rgba(255,59,48,.3);border-radius:10px;margin-bottom:18px">
                <p style="font-size:0.82rem;font-weight:600;color:var(--apple-red);margin:0 0 6px"><i class="fas fa-exclamation-circle" style="margin-right:6px"></i>Terdapat kesalahan:</p>
                <ul style="margin:0;padding-left:18px">
                    @foreach($errors->all() as $err)
                    <li style="font-size:0.82rem;color:var(--apple-red)">{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @include('admin.services._form')

            <div style="display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid var(--dark-separator)">
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;font-size:0.85rem;font-weight:600;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;cursor:pointer"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save"></i>Simpan Layanan
                </button>
                <a href="{{ route('admin.services.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:10px 20px;font-size:0.85rem;font-weight:600;color:var(--dark-text-secondary);background:rgba(255,255,255,.04);border:1px solid var(--dark-separator);border-radius:10px;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection

@push('styles')
<style>
@keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
@endpush

@push('scripts')
<script>
(function() {
    // Focus/blur effect on AI input wrapper
    var inp = document.getElementById('ai-topic-input');
    var wrapper = document.getElementById('ai-input-wrapper');
    if (inp && wrapper) {
        inp.addEventListener('focus', function() {
            wrapper.style.borderColor = 'var(--apple-blue)';
            wrapper.style.boxShadow = '0 0 0 3px rgba(0,122,255,.12)';
        });
        inp.addEventListener('blur', function() {
            wrapper.style.borderColor = 'rgba(0,122,255,0.25)';
            wrapper.style.boxShadow = 'none';
        });
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); window.aiGenerate(); }
        });
    }

    window.aiGenerate = async function() {
        var topicEl   = document.getElementById('ai-topic-input');
        var btnText   = document.getElementById('ai-btn-text');
        var btnLoad   = document.getElementById('ai-btn-loading');
        var errDiv    = document.getElementById('ai-error-div');
        var succDiv   = document.getElementById('ai-success-div');
        var btn       = document.getElementById('ai-generate-btn');

        var topic = topicEl ? topicEl.value.trim() : '';
        if (!topic) return;

        // Show loading state
        if (btnText) btnText.style.display = 'none';
        if (btnLoad) btnLoad.style.display = 'inline-flex';
        if (btn)     btn.disabled = true;
        if (errDiv)  { errDiv.style.display = 'none'; errDiv.textContent = ''; }
        if (succDiv) succDiv.style.display = 'none';

        try {
            var res = await fetch('{{ route('admin.services.ai-generate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ topic: topic }),
            });

            var json = await res.json();

            if (!res.ok || json.error) {
                if (errDiv) {
                    errDiv.textContent = json.error || 'Terjadi kesalahan pada server AI.';
                    errDiv.style.display = 'block';
                }
                return;
            }

            fillForm(json.data);
            if (succDiv) succDiv.style.display = 'block';

            // Switch to basic tab
            if (typeof switchServiceTab === 'function') switchServiceTab('basic');

        } catch (e) {
            if (errDiv) {
                errDiv.textContent = 'Koneksi gagal: ' + e.message;
                errDiv.style.display = 'block';
            }
        } finally {
            if (btnText) btnText.style.display = 'inline';
            if (btnLoad) btnLoad.style.display = 'none';
            if (btn)     btn.disabled = false;
        }
    };

    function setVal(name, val) {
        var el = document.querySelector('[name="' + name + '"]');
        if (el) { el.value = val || ''; el.dispatchEvent(new Event('input')); }
    }
    function setTA(name, val) {
        var el = document.querySelector('textarea[name="' + name + '"]');
        if (el) el.value = val || '';
    }
    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    function slugify(str) {
        return (str||'').toLowerCase().replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-');
    }

    function fillForm(d) {
        setVal('title', d.title);
        setVal('slug',  d.slug || slugify(d.title));
        setVal('icon',  d.icon || 'fa-layer-group');
        setVal('badge', d.badge || '');
        setVal('tagline', d.tagline || '');

        // Color
        var colorEl = document.querySelector('input[type="color"][name="color"]');
        if (colorEl) colorEl.value = d.color || '#B45309';
        var colorText = document.getElementById('color-text-preview');
        if (colorText) colorText.value = d.color || '#B45309';

        // Category select
        var catEl = document.querySelector('select[name="category"]');
        if (catEl && d.category) {
            for (var i = 0; i < catEl.options.length; i++) {
                if (catEl.options[i].value.toUpperCase() === d.category.toUpperCase()) {
                    catEl.value = catEl.options[i].value;
                    catEl.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }

        // Descriptions
        setTA('short_description', d.short_description);
        setTA('long_description',  d.long_description);

        // Detail
        setVal('price_range',    d.price_range);
        setVal('starting_price', d.starting_price || '');
        setVal('process_time',   d.process_time);
        setTA('requirements', Array.isArray(d.requirements) ? d.requirements.join('\n') : (d.documents_required && Array.isArray(d.documents_required) ? d.documents_required.join('\n') : ''));
        setTA('outputs', Array.isArray(d.outputs) ? d.outputs.join('\n') : (d.key_features && Array.isArray(d.key_features) ? d.key_features.join('\n') : ''));

        // Process steps
        var stepsEl = document.getElementById('steps-container');
        if (stepsEl && Array.isArray(d.process_steps_detail)) {
            stepsEl.innerHTML = '';
            var inpStyle = 'width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid rgba(84,84,88,0.35);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
            d.process_steps_detail.forEach(function(step, i) {
                var div = document.createElement('div');
                div.className = 'step-item';
                div.style.cssText = 'display:flex;gap:8px;align-items:flex-start;background:var(--dark-bg-tertiary);border:1px solid rgba(84,84,88,0.35);border-radius:10px;padding:12px';
                div.innerHTML = '<div style="flex-shrink:0;width:24px;height:24px;border-radius:50%;background:var(--apple-blue);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;margin-top:1px">' + (i+1) + '</div>'
                    + '<div style="flex:1;display:grid;grid-template-columns:1fr 2fr;gap:10px">'
                    + '<input type="text" name="steps[' + i + '][title]" value="' + esc(step.title||'') + '" placeholder="Nama tahapan" style="' + inpStyle + '">'
                    + '<input type="text" name="steps[' + i + '][description]" value="' + esc(step.desc||step.description||'') + '" placeholder="Deskripsi singkat..." style="' + inpStyle + '">'
                    + '</div>'
                    + '<button type="button" onclick="this.closest(\'.step-item\').remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;margin-top:2px"><i class="fas fa-times"></i></button>';
                stepsEl.appendChild(div);
            });
        }

        // FAQ
        var faqEl = document.getElementById('faq-container');
        if (faqEl && Array.isArray(d.faq)) {
            faqEl.innerHTML = '';
            var inpStyle2 = 'width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid rgba(84,84,88,0.35);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box';
            d.faq.forEach(function(faq, i) {
                var div = document.createElement('div');
                div.className = 'faq-item';
                div.style.cssText = 'background:var(--dark-bg-tertiary);border:1px solid rgba(84,84,88,0.35);border-radius:10px;padding:12px';
                div.innerHTML = '<div style="display:flex;gap:8px;margin-bottom:8px">'
                    + '<div style="flex:1"><input type="text" name="faqs[' + i + '][question]" value="' + esc(faq.q||'') + '" placeholder="Pertanyaan..." style="' + inpStyle2 + '"></div>'
                    + '<button type="button" onclick="this.closest(\'.faq-item\').remove()" style="background:none;border:none;cursor:pointer;color:var(--apple-red);padding:4px;flex-shrink:0"><i class="fas fa-times"></i></button>'
                    + '</div>'
                    + '<textarea name="faqs[' + i + '][answer]" rows="2" placeholder="Jawaban..." style="' + inpStyle2 + ';resize:vertical">' + esc(faq.a||'') + '</textarea>';
                faqEl.appendChild(div);
            });
        }

        // SEO
        setTA('meta_keywords', d.meta_keywords || '');
    }
})();
</script>
@endpush
