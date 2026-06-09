@extends('layouts.admin')

@section('title', 'Reply Email')

@section('content')
<div class="px-4 py-6 max-w-4xl mx-auto"
     x-data="{ showOriginal: false }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-reply text-blue-400"></i>Reply Email
            </h1>
            <p class="text-gray-400 mt-1">Reply to: {{ $email->from_name ?? $email->from_email }}</p>
        </div>
        <a href="{{ route('admin.inbox.show', $email->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-5">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Original Email (collapsible) --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow mb-4">
        <button type="button" @click="showOriginal = !showOriginal"
                class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-gray-700/40 transition">
            <span class="text-white font-medium flex items-center gap-2">
                <i class="fas fa-chevron-down text-gray-400 transition-transform" :class="showOriginal ? 'rotate-180' : ''"></i>
                Original Message
            </span>
            <span class="text-gray-400 text-sm">from {{ $email->from_email }}</span>
        </button>
        <div x-show="showOriginal" x-cloak class="border-t border-gray-700 p-5">
            <div class="space-y-2 text-sm mb-3">
                <p><span class="text-white font-medium">Subject:</span> <span class="text-gray-300">{{ $email->subject }}</span></p>
                <p><span class="text-white font-medium">Date:</span> <span class="text-gray-300">{{ $email->received_at->format('d M Y, H:i') }}</span></p>
            </div>
            <hr class="border-gray-700 mb-3">
            <div class="text-gray-400 text-sm max-h-72 overflow-y-auto whitespace-pre-wrap">{{ $email->body_text ?: strip_tags($email->body_html ?? '') }}</div>
        </div>
    </div>

    {{-- Reply Form --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
        <div class="p-6">
            <form action="{{ route('admin.inbox.send-reply', $email->id) }}" method="POST">
                @csrf
                <div class="space-y-4">

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-at mr-2 text-gray-400"></i>From Account
                        </label>
                        <select name="from_account_id"
                                class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('from_account_id') border-red-500 @enderror">
                            <option value="">Default ({{ config('mail.from.address') }})</option>
                            @foreach(($fromAccounts ?? collect()) as $account)
                            <option value="{{ $account->id }}"
                                {{ (string) old('from_account_id', $email->email_account_id) === (string) $account->id ? 'selected' : '' }}>
                                {{ $account->name }} ({{ $account->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('from_account_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-envelope mr-2 text-gray-400"></i>To
                        </label>
                        <input type="text" readonly
                               value="{{ $email->from_name ?? $email->from_email }} <{{ $email->from_email }}>"
                               class="w-full bg-gray-900/50 text-gray-400 border border-gray-700 rounded-lg px-3 py-2.5 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-tag mr-2 text-gray-400"></i>Subject
                        </label>
                        <input type="text" readonly value="Re: {{ $email->subject }}"
                               class="w-full bg-gray-900/50 text-gray-400 border border-gray-700 rounded-lg px-3 py-2.5 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-1">
                            <i class="fas fa-align-left mr-2 text-gray-400"></i>Your Reply <span class="text-red-400">*</span>
                        </label>
                        <div class="ckeditor-wrapper">
                            <textarea name="body_html" id="body_html" required>{{ old('body_html') }}</textarea>
                        </div>
                        @error('body_html')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Use the toolbar above for rich text formatting</p>
                    </div>

                    {{-- Quick Responses --}}
                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-bolt mr-2 text-gray-400"></i>Quick Responses
                        </label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['thanks' => 'Thank You', 'received' => 'Received', 'follow' => 'Will Follow Up'] as $key => $label)
                            <button type="button" onclick="insertQuickResponse('{{ $key }}')" id="quick-{{ $key }}"
                                    class="px-3 py-1.5 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm rounded-lg transition">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-paper-plane mr-2"></i>Send Reply
                            </button>
                            <button type="button" id="saveDraftBtn" onclick="saveDraft()"
                                    class="inline-flex items-center px-4 py-2.5 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-save mr-2"></i>Save Draft
                            </button>
                        </div>
                        <a href="{{ route('admin.inbox.show', $email->id) }}"
                           class="inline-flex items-center px-4 py-2.5 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
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
<script>
const quickResponses = {
    thanks: `<p>Terima kasih atas email Anda.</p><p>Kami sangat menghargai waktu Anda untuk menghubungi kami.</p><p>Salam,<br>Tim Bizmark.ID</p>`,
    received: `<p>Email Anda telah kami terima.</p><p>Kami akan meninjau pesan Anda dan segera memberikan respon.</p><p>Terima kasih,<br>Tim Bizmark.ID</p>`,
    follow: `<p>Terima kasih atas email Anda.</p><p>Kami akan menindaklanjuti hal ini dan segera menghubungi Anda kembali.</p><p>Hormat kami,<br>Tim Bizmark.ID</p>`
};

let replyEditorInstance;

function insertQuickResponse(type) {
    if (replyEditorInstance) {
        replyEditorInstance.setData(quickResponses[type]);
    }
}

const DRAFT_KEY = 'reply_draft_{{ $email->id }}';

function saveDraft() {
    const btn = document.getElementById('saveDraftBtn');
    if (replyEditorInstance) {
        localStorage.setItem(DRAFT_KEY, JSON.stringify({
            body: replyEditorInstance.getData(),
            saved_at: new Date().toISOString()
        }));
    }
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Draft tersimpan';
    btn.disabled = true;
    setTimeout(() => { btn.innerHTML = orig; btn.disabled = false; }, 2000);
}

document.addEventListener('DOMContentLoaded', function () {
    ClassicEditor.create(document.querySelector('#body_html'), {
        toolbar:['heading','|','bold','italic','|','link','blockQuote','|','bulletedList','numberedList','|','undo','redo'],
        link:{addTargetToExternalLinks:true}
    }).then(editor => {
        replyEditorInstance = editor;
        
        // Restore draft if exists
        const draft = localStorage.getItem(DRAFT_KEY);
        if (draft) {
            const data = JSON.parse(draft);
            if (confirm('Found saved draft from ' + new Date(data.saved_at).toLocaleString() + '. Restore?')) {
                editor.setData(data.body || '');
            }
        }
        
        // Save editor content to textarea before submit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('#body_html').value = replyEditorInstance.getData();
        });
    }).catch(err => console.error(err));
});
</script>
@endsection
