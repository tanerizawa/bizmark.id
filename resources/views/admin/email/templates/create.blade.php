@extends('layouts.admin')
@section('title', 'Create Template')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Design</p>
            <h1 style="font-size:1.2rem;font-weight:700;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:8px">
                <i class="fas fa-plus-circle" style="color:var(--apple-red);font-size:1rem"></i>Create Email Template
            </h1>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Create a new reusable email template</p>
        </div>
        <a href="{{ route('admin.templates.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Back to Templates
        </a>
    </div>

    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:10px;padding:12px 16px;color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('admin.templates.store') }}" method="POST">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- Left: Content --}}
            <div style="display:flex;flex-direction:column;gap:16px">
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-info-circle" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Template Information</h5>
                    </div>
                    <div style="padding:18px;display:flex;flex-direction:column;gap:16px">

                        {{-- Name --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Template Name <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g., Monthly Newsletter Template" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Default Subject <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                   placeholder="e.g., Newsletter Bulanan - @{{month}} @{{year}}" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('subject')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                                <i class="fas fa-info-circle" style="margin-right:3px"></i>Use variables like @{{month}}, @{{year}}, @{{name}} in subject
                            </p>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Category <span style="color:var(--apple-red)">*</span>
                            </label>
                            <select id="category" name="category" required
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">-- Select Category --</option>
                                <option value="newsletter" {{ old('category') === 'newsletter' ? 'selected' : '' }}>Newsletter</option>
                                <option value="promotional" {{ old('category') === 'promotional' ? 'selected' : '' }}>Promotional</option>
                                <option value="transactional" {{ old('category') === 'transactional' ? 'selected' : '' }}>Transactional</option>
                                <option value="announcement" {{ old('category') === 'announcement' ? 'selected' : '' }}>Announcement</option>
                            </select>
                            @error('category')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        {{-- HTML Content --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                HTML Content <span style="color:var(--apple-red)">*</span>
                            </label>
@php
$defaultEmailContent = '<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello {{name}}!</h1>
        <p>Your content here...</p>
    </div>
</body>
</html>';
@endphp
                            <textarea id="content" name="content" rows="20" required
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.8rem;outline:none;font-family:'Courier New',Consolas,monospace;line-height:1.6;resize:vertical;min-height:380px;box-sizing:border-box"
                                      onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('content', $defaultEmailContent) }}</textarea>
                            @error('content')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                                <i class="fas fa-code" style="margin-right:3px"></i>Full HTML email template with inline CSS
                            </p>
                        </div>

                        {{-- Plain Content --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Plain Text Version <span style="font-size:0.65rem;font-weight:400;opacity:.6">(Optional)</span>
                            </label>
                            <textarea id="plain_content" name="plain_content" rows="8"
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;resize:vertical;box-sizing:border-box"
                                      onfocus="this.style.borderColor='var(--apple-red)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('plain_content') }}</textarea>
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">Plain text fallback for email clients that don't support HTML</p>
                        </div>

                        <button type="button" onclick="openPreview()"
                                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid color-mix(in srgb,var(--apple-teal) 40%,var(--dark-separator));border-radius:9px;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 10%,transparent);font-size:0.82rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-eye"></i>Preview Template
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Settings + Actions --}}
            <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:16px">

                {{-- Status --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-toggle-on" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Status</h5>
                    </div>
                    <div style="padding:14px 16px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">Active Template</span>
                        </label>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:6px 0 0;opacity:.7">Only active templates can be used in campaigns</p>
                    </div>
                </div>

                {{-- Variables --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-code" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Available Variables</h5>
                    </div>
                    <div style="padding:14px 16px">
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0 0 8px">Click to insert into template:</p>
                        <div style="display:flex;flex-wrap:wrap;gap:6px">
                            @foreach(['name','email','phone','unsubscribe_url','month','year'] as $var)
                            <button type="button" onclick="insertVariable('{{ $var }}')"
                                    style="padding:3px 8px;background:color-mix(in srgb,var(--apple-teal) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-teal) 25%,var(--dark-separator));border-radius:6px;color:var(--apple-teal);font-size:0.72rem;font-family:'Courier New',monospace;cursor:pointer;font-weight:600"
                                    onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                                @{{{{ '{{' . $var . '}}' }}}}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Quick Insert --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-magic" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Quick Insert</h5>
                    </div>
                    <div style="padding:14px 16px;display:flex;flex-direction:column;gap:8px">
                        @foreach(['Header' => 'insertHeader', 'Button' => 'insertButton', 'Footer' => 'insertFooter', 'Unsubscribe' => 'insertUnsubscribe'] as $label => $fn)
                        <button type="button" onclick="{{ $fn }}()"
                                style="width:100%;padding:7px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);background:none;font-size:0.82rem;cursor:pointer;font-weight:600"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            Insert {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Actions --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px;display:flex;flex-direction:column;gap:8px">
                    <button type="submit"
                            style="width:100%;padding:10px;background:var(--apple-red);color:#fff;border:none;border-radius:10px;font-size:0.85rem;font-weight:600;cursor:pointer"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-save" style="margin-right:6px"></i>Create Template
                    </button>
                    <a href="{{ route('admin.templates.index') }}"
                       style="display:block;text-align:center;padding:10px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.85rem;font-weight:600;text-decoration:none"
                       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                        <i class="fas fa-times" style="margin-right:6px"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Preview Modal (pure JS) --}}
    <div id="previewModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
        <div style="position:relative;width:100%;max-width:900px;border-radius:14px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.6);background:var(--dark-bg-secondary);border:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-eye" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                    <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Template Preview</h5>
                </div>
                <button onclick="closePreview()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.2rem;cursor:pointer;padding:0;line-height:1"
                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">&times;</button>
            </div>
            <div style="padding:12px 18px;border-bottom:1px solid var(--dark-separator);font-size:0.82rem;color:var(--dark-text-secondary)">
                <strong style="color:var(--dark-text-primary)">Subject:</strong> <span id="preview_subject"></span>
            </div>
            <div style="padding:0;max-height:70vh;overflow-y:auto;background:#fff">
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

function openPreview() {
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
    document.getElementById('preview_content').innerHTML = previewContent || '<p style="color:#999; padding:2rem; text-align:center">No content</p>';
    const modal = document.getElementById('previewModal');
    modal.style.display = 'flex';
    document.addEventListener('keydown', handlePreviewEsc);
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    document.removeEventListener('keydown', handlePreviewEsc);
}

function handlePreviewEsc(e) { if (e.key === 'Escape') closePreview(); }

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

function insertAtCursor(text) {
    const textarea = document.getElementById('content');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    textarea.value = textBefore + text + textAfter;
    textarea.focus();
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
</script>
@endverbatim
@endsection
