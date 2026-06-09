@extends('layouts.admin')

@section('title', 'Edit Template')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto" x-data="{ previewOpen: false }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-400"></i>Edit Template
            </h1>
            <p class="text-gray-400 mt-1">{{ $template->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.templates.show', $template->id) }}"
               class="inline-flex items-center px-4 py-2 border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 text-sm font-medium rounded-lg transition">
                <i class="fas fa-eye mr-2"></i>View
            </a>
            <a href="{{ route('admin.templates.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    @if($template->campaigns->count() > 0)
    <div class="flex items-center gap-3 bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-exclamation-triangle"></i>
        <span><strong>Note:</strong> This template is used by {{ $template->campaigns->count() }} campaign(s). Changes will not affect existing campaigns.</span>
    </div>
    @endif

    <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Content --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i>Template Information
                        </h5>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Name --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-white mb-1">
                                Template Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $template->name) }}"
                                   placeholder="e.g., Monthly Newsletter Template" required
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-white mb-1">
                                Default Subject <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject', $template->subject) }}"
                                   placeholder="e.g., Newsletter Bulanan" required
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                            @error('subject')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Use variables like @{{month}}, @{{year}}, @{{name}} in subject</p>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-white mb-1">
                                Category <span class="text-red-400">*</span>
                            </label>
                            <select id="category" name="category" required
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror">
                                <option value="">-- Select Category --</option>
                                @foreach(['newsletter' => 'Newsletter', 'promotional' => 'Promotional', 'transactional' => 'Transactional', 'announcement' => 'Announcement'] as $val => $label)
                                <option value="{{ $val }}" {{ old('category', $template->category) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- HTML Content --}}
                        <div>
                            <label for="content" class="block text-sm font-medium text-white mb-1">
                                HTML Content <span class="text-red-400">*</span>
                            </label>
                            <textarea id="content" name="content" rows="20" required
                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror">{{ old('content', $template->content) }}</textarea>
                            @error('content')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-code mr-1"></i>Full HTML email template with inline CSS</p>
                        </div>

                        {{-- Plain Content --}}
                        <div>
                            <label for="plain_content" class="block text-sm font-medium text-white mb-1">
                                Plain Text Version <span class="text-gray-400 font-normal">(Optional)</span>
                            </label>
                            <textarea id="plain_content" name="plain_content" rows="8"
                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('plain_content', $template->plain_content) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Plain text fallback for email clients that don't support HTML</p>
                        </div>

                        <button type="button" @click="previewOpen = true"
                            class="inline-flex items-center px-4 py-2 border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 text-sm rounded-lg transition">
                            <i class="fas fa-eye mr-2"></i>Preview Template
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Settings + Actions --}}
            <div class="space-y-5">

                {{-- Status --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-toggle-on text-gray-400"></i>Status
                        </h5>
                    </div>
                    <div class="p-5">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 accent-blue-500">
                            <span class="text-white text-sm">Active Template</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Only active templates can be used in campaigns</p>
                    </div>
                </div>

                {{-- Variables --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-code text-gray-400"></i>Available Variables
                        </h5>
                    </div>
                    <div class="p-5">
                        <p class="text-xs text-gray-400 mb-3">Click to insert into template:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['name','email','phone','unsubscribe_url','month','year'] as $var)
                            <button type="button" onclick="insertVariable('{{ $var }}')"
                                class="px-2.5 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 text-xs rounded cursor-pointer transition">
                                @{{{{ '{{' . $var . '}}' }}}}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Quick Insert --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-magic text-gray-400"></i>Quick Insert
                        </h5>
                    </div>
                    <div class="p-5 space-y-2">
                        @foreach(['Header' => 'insertHeader', 'Button' => 'insertButton', 'Footer' => 'insertFooter', 'Unsubscribe' => 'insertUnsubscribe'] as $label => $fn)
                        <button type="button" onclick="{{ $fn }}()"
                            class="w-full border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 py-2 rounded-lg text-sm transition">
                            Insert {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                    <a href="{{ route('admin.templates.show', $template->id) }}"
                       class="block w-full text-center border border-gray-600 text-gray-300 hover:text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Preview Modal --}}
    <div x-show="previewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="previewOpen = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
             @click.outside="previewOpen = false">
            <div class="flex items-center justify-between px-5 py-4 bg-gray-800 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-eye text-gray-400"></i>Template Preview
                </h5>
                <button @click="previewOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="bg-gray-100 px-5 py-3 border-b text-sm">
                <strong>Subject:</strong> <span id="preview_subject"></span>
            </div>
            <div class="p-6 overflow-y-auto bg-white flex-1">
                <div id="preview_content"></div>
            </div>
        </div>
    </div>
</div>

@verbatim
<script>
function insertVariable(varName) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    textarea.value = textBefore + '{{' + varName + '}}' + textAfter;
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = cursorPos + varName.length + 4;
}

function insertHeader() {
    insertAtCursor(`
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 20px; text-align: center;">
    <h1 style="color: white; margin: 0; font-size: 32px;">Bizmark.ID</h1>
</div>
`);
}

function insertButton() {
    insertAtCursor(`
<div style="text-align: center; margin: 30px 0;">
    <a href="https://bizmark.id" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
        Kunjungi Website
    </a>
</div>
`);
}

function insertFooter() {
    insertAtCursor(`
<div style="background: #f8f9fa; padding: 30px 20px; text-align: center; border-top: 1px solid #dee2e6;">
    <p style="color: #6c757d; margin: 0 0 10px 0;">© ${new Date().getFullYear()} Bizmark.ID. All rights reserved.</p>
</div>
`);
}

function insertUnsubscribe() {
    insertAtCursor(`
<div style="text-align: center; margin-top: 20px; padding: 20px; background: #f8f9fa;">
    <p style="color: #6c757d; font-size: 12px; margin: 0;">
        Tidak ingin menerima email ini? 
        <a href="{{unsubscribe_url}}" style="color: #667eea; text-decoration: none;">Unsubscribe</a>
    </p>
</div>
`);
}

function insertAtCursor(text) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    textarea.value = textBefore + text + textAfter;
    textarea.focus();
}

document.addEventListener('DOMContentLoaded', function() {
    const xData = document.querySelector('[x-data]');
    if (xData) {
        xData.addEventListener('click', function() {
            if (document.getElementById('preview_subject')) updatePreview();
        });
    }
});

function updatePreview() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    let previewContent = content
        .replace(/\{\{name\}\}/g, 'John Doe')
        .replace(/\{\{email\}\}/g, 'john@example.com')
        .replace(/\{\{phone\}\}/g, '6283879602855')
        .replace(/\{\{month\}\}/g, 'November')
        .replace(/\{\{year\}\}/g, '2025')
        .replace(/\{\{unsubscribe_url\}\}/g, '#unsubscribe');
    let previewSubject = subject
        .replace(/\{\{name\}\}/g, 'John Doe')
        .replace(/\{\{month\}\}/g, 'November')
        .replace(/\{\{year\}\}/g, '2025');
    document.getElementById('preview_subject').textContent = previewSubject || '(No subject)';
    document.getElementById('preview_content').innerHTML = previewContent || '<p style="color:#999">No content</p>';
}
</script>
@endverbatim
@endsection
