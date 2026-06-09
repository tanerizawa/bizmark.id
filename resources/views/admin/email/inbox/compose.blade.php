@extends('layouts.admin')
@section('title', 'Compose Email')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px;max-width:860px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Inbox</p>
            <h1 style="font-size:1.2rem;font-weight:700;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:8px">
                <i class="fas fa-edit" style="color:var(--apple-blue);font-size:1rem"></i>Compose Email
            </h1>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Kirim email baru</p>
        </div>
        <a href="{{ route('admin.inbox.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Back to Inbox
        </a>
    </div>

    @if(session('success'))
    <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:10px;padding:12px 16px;color:var(--apple-green)">
        <i class="fas fa-check-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:10px;padding:12px 16px;color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Compose Form --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <form action="{{ route('admin.inbox.send') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- From Account --}}
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                        <i class="fas fa-at" style="margin-right:4px"></i>From Account
                    </label>
                    <select name="from_account_id" id="from_account_id"
                            style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">Default ({{ config('mail.from.address') }})</option>
                        @foreach(($fromAccounts ?? collect()) as $account)
                        <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} ({{ $account->email }})
                        </option>
                        @endforeach
                    </select>
                    @error('from_account_id')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                        <i class="fas fa-info-circle" style="margin-right:3px"></i>Pilih akun pengirim untuk korespondensi klien.
                    </p>
                </div>

                {{-- To --}}
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                        <i class="fas fa-envelope" style="margin-right:4px"></i>To <span style="color:var(--apple-red)">*</span>
                    </label>
                    <input type="email" name="to_email" id="to_email" value="{{ old('to_email') }}"
                           placeholder="recipient@example.com" required
                           style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    @error('to_email')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                </div>

                {{-- Subject --}}
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                        <i class="fas fa-tag" style="margin-right:4px"></i>Subject <span style="color:var(--apple-red)">*</span>
                    </label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                           placeholder="Email subject" required
                           style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    @error('subject')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                </div>

                {{-- Message --}}
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                        <i class="fas fa-align-left" style="margin-right:4px"></i>Message <span style="color:var(--apple-red)">*</span>
                    </label>
                    <div class="ckeditor-wrapper">
                        <textarea name="body_html" id="body_html" required style="display:none">{{ old('body_html') }}</textarea>
                    </div>
                    @error('body_html')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                        <i class="fas fa-info-circle" style="margin-right:3px"></i>Gunakan toolbar untuk formatting teks (bold, italic, link, list, dll)
                    </p>
                </div>
                    @error('body_html')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                        <i class="fas fa-magic" style="margin-right:3px"></i>Gunakan toolbar untuk memformat teks, menambah link, dan styling lainnya
                    </p>
                </div>
                    @error('body_html')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                        <i class="fas fa-magic" style="margin-right:3px"></i>Gunakan toolbar untuk formatting teks, link, dan lainnya
                    </p>
                </div>

                {{-- Attachments --}}
                <div>
                    <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                        <i class="fas fa-paperclip" style="margin-right:4px"></i>Attachments
                    </label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                           style="font-size:0.82rem;color:var(--dark-text-secondary)" />
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">Optional. Max 10MB per file.</p>
                </div>

                {{-- Actions --}}
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;padding-top:6px;border-top:1px solid var(--dark-separator)">
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:9px;font-size:0.82rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-paper-plane"></i>Send Email
                        </button>
                        <button type="button" id="generateAiBtn"
                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);background:none;font-size:0.82rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-robot"></i>Generate with AI
                        </button>
                        <button type="button" id="saveDraftBtn" onclick="saveDraft()"
                                style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);background:none;font-size:0.82rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-save"></i>Save Draft
                        </button>
                    </div>
                    <a href="{{ route('admin.inbox.index') }}"
                       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                        <i class="fas fa-times"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Quick Tips --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 22px">
        <h6 style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 10px;display:flex;align-items:center;gap:6px">
            <i class="fas fa-lightbulb" style="color:var(--apple-yellow)"></i>Email Tips
        </h6>
        <ul style="color:var(--dark-text-secondary);font-size:0.8rem;margin:0;padding-left:18px;display:flex;flex-direction:column;gap:4px">
            <li>Pastikan email penerima valid dan aktif</li>
            <li>Tulis subject yang jelas dan deskriptif</li>
            <li>Gunakan toolbar editor untuk format teks, link, dan list</li>
            <li>Email akan tersimpan di folder "Sent" setelah terkirim</li>
        </ul>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
@push('styles')
<style>
    .ckeditor-wrapper .ck-editor__editable { min-height:400px;background-color:#1c1c1e !important;color:#f5f5f7 !important; }
    .ck.ck-editor__main > .ck-editor__editable { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck.ck-toolbar { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-button:not(.ck-disabled):hover,.ck.ck-button:not(.ck-disabled):active { background-color:#3a3a3c !important; }
    .ck.ck-button.ck-on { background-color:#0a84ff !important;color:white !important; }
    .ck.ck-dropdown__panel { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-list__item:hover { background-color:#3a3a3c !important; }
    .ck.ck-labeled-field-view>.ck-labeled-field-view__input-wrapper>.ck-input { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck-content h1,.ck-content h2,.ck-content h3,.ck-content h4,.ck-content h5,.ck-content h6 { color:#f5f5f7 !important; }
    .ck-content a { color:#0a84ff !important; }
    .ck-content blockquote { border-left-color:#0a84ff !important; }
    .ck-content code { background-color:#2c2c2e !important;color:#ff453a !important; }
    .ck-content pre { background-color:#2c2c2e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
</style>
@endpush

<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
@push('styles')
<style>
    .ckeditor-wrapper .ck-editor__editable { min-height:300px;background-color:#1c1c1e !important;color:#f5f5f7 !important; }
    .ck.ck-editor__main > .ck-editor__editable { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck.ck-toolbar { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-button:not(.ck-disabled):hover,.ck.ck-button:not(.ck-disabled):active { background-color:#3a3a3c !important; }
    .ck.ck-button.ck-on { background-color:#0a84ff !important;color:white !important; }
    .ck.ck-dropdown__panel { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-list__item:hover { background-color:#3a3a3c !important; }
    .ck.ck-labeled-field-view>.ck-labeled-field-view__input-wrapper>.ck-input { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck-content h1,.ck-content h2,.ck-content h3,.ck-content h4,.ck-content h5,.ck-content h6 { color:#f5f5f7 !important; }
    .ck-content a { color:#0a84ff !important; }
    .ck-content blockquote { border-left-color:#0a84ff !important; }
    .ck-content code { background-color:#2c2c2e !important;color:#ff453a !important; }
    .ck-content pre { background-color:#2c2c2e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
</style>
@endpush

@push('scripts')
<script>
let editorInstance;

// Initialize CKEditor
ClassicEditor.create(document.querySelector('#body_html'), {
    toolbar: ['heading', '|', 'bold', 'italic', 'underline', '|', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', '|', 'undo', 'redo'],
    link: { addTargetToExternalLinks: true }
}).then(editor => {
    editorInstance = editor;
    
    // Sync editor content to textarea on form submit
    document.querySelector('form').addEventListener('submit', function() {
        document.querySelector('#body_html').value = editorInstance.getData();
    });
    
    // Load draft if exists
    const draft = localStorage.getItem('email_draft');
    if (draft) {
        const data = JSON.parse(draft);
        if (confirm('Found saved draft from ' + new Date(data.saved_at).toLocaleString() + '. Restore?')) {
            document.getElementById('to_email').value = data.to || '';
            document.getElementById('subject').value = data.subject || '';
            editorInstance.setData(data.body || '');
        }
    }
}).catch(err => console.error(err));

function saveDraft() {
    const btn = document.getElementById('saveDraftBtn');
    localStorage.setItem('email_draft', JSON.stringify({
        to: document.getElementById('to_email').value,
        subject: document.getElementById('subject').value,
        body: editorInstance.getData(),
        saved_at: new Date().toISOString()
    }));
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check" style="margin-right:6px"></i>Draft tersimpan';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
}

@if(session('success'))
    localStorage.removeItem('email_draft');
@endif

document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('generateAiBtn');
    if (!btn) return;
    btn.addEventListener('click', async function () {
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:6px"></i>Menghasilkan...';
        const payload = new FormData();
        payload.append('to_email', document.getElementById('to_email').value || '');
        payload.append('subject', document.getElementById('subject').value || '');
        payload.append('body_html', editorInstance.getData() || '');
        try {
            const res = await fetch('{{ route('admin.inbox.generate') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                body: payload,
            });
            const data = await res.json();
            if (data.success && data.data) {
                if (data.data.email_subject) document.getElementById('subject').value = data.data.email_subject;
                if (data.data.email_html) editorInstance.setData(data.data.email_html);
                alert('Konten AI berhasil dimasukkan. Periksa dan sesuaikan sebelum mengirim.');
            } else {
                alert('Gagal menghasilkan konten: ' + (data.error || 'unknown'));
            }
        } catch (e) {
            alert('Gagal menghubungi layanan AI.');
        }
        btn.innerHTML = orig;
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection
