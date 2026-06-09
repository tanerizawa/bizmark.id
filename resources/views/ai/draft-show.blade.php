@extends('layouts.admin')

@section('title', $draft->title)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center">
            <a href="{{ route('ai.drafts.index', $project) }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold" style="color: #FFFFFF;">{{ $draft->title }}</h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">
                    Draft Dokumen AI • {{ $project->name }}
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            @if($draft->status === 'draft' || $draft->status === 'reviewed')
            <form action="{{ route('ai.drafts.approve', [$project, $draft]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menyetujui draft ini?')) $el.submit()">
                @csrf
                @method('POST')
                <button type="submit" class="px-4 py-2 rounded-lg font-medium transition-colors"
                        style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                    <i class="fas fa-check-circle mr-2"></i>Approve
                </button>
            </form>

            <form action="{{ route('ai.drafts.reject', [$project, $draft]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menolak draft ini?')) $el.submit()">
                @csrf
                @method('POST')
                <button type="submit" class="px-4 py-2 rounded-lg font-medium transition-colors"
                        style="background: rgba(255, 59, 48, 0.9); color: #FFFFFF;">
                    <i class="fas fa-times-circle mr-2"></i>Reject
                </button>
            </form>
            @endif

            @if($draft->status !== 'approved')
            <!-- Delete Button (only for non-approved drafts) -->
            <form action="{{ route('ai.drafts.destroy', [$project, $draft]) }}" method="POST" class="inline"
                  x-data @submit.prevent="if(confirm('⚠️ PERHATIAN: Draft akan dihapus permanen!\n\nApakah Anda yakin ingin menghapus draft ini?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg font-medium transition-colors"
                        style="background: rgba(255, 59, 48, 0.7); color: #FFFFFF;">
                    <i class="fas fa-trash-alt mr-2"></i>Delete
                </button>
            </form>
            @endif

            <!-- Export Dropdown -->
            <div class="relative inline-block" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="px-4 py-2 rounded-lg font-medium transition-colors flex items-center"
                        style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;">
                    <i class="fas fa-download mr-2"></i>Export
                    <i class="fas fa-chevron-down ml-2 text-xs"></i>
                </button>
                
                <div x-show="open" @click.away="open = false"
                     class="absolute right-0 mt-2 w-56 rounded-lg shadow-xl z-50"
                     style="background: rgba(28, 28, 30, 0.95); border: 1px solid rgba(142, 142, 147, 0.3);">
                    <a href="{{ route('ai.drafts.export', [$project, $draft]) }}?format=docx" 
                       class="block px-4 py-3 hover:bg-opacity-80 transition-colors border-b"
                       style="color: #FFFFFF; border-color: rgba(142, 142, 147, 0.2);">
                        <i class="fas fa-file-word mr-3 text-blue-500"></i>
                        <span class="font-medium">Export as DOCX</span>
                        <span class="block text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                            Format profesional dengan heading, TOC, table
                        </span>
                    </a>
                    <a href="{{ route('ai.drafts.export', [$project, $draft]) }}?format=pdf" 
                       class="block px-4 py-3 hover:bg-opacity-80 transition-colors"
                       style="color: #FFFFFF;">
                        <i class="fas fa-file-pdf mr-3 text-red-500"></i>
                        <span class="font-medium">Export as PDF</span>
                        <span class="block text-xs mt-1" style="color: rgba(235, 235, 245, 0.6);">
                            Format read-only untuk distribusi
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Banner -->
            @php
                $statusConfig = [
                    'draft' => ['icon' => 'fa-edit', 'bg' => 'rgba(255, 149, 0, 0.1)', 'border' => '#FF9500', 'text' => 'Draft - Dapat Diedit'],
                    'reviewed' => ['icon' => 'fa-search', 'bg' => 'rgba(0, 122, 255, 0.1)', 'border' => '#007AFF', 'text' => 'Reviewed - Menunggu Persetujuan'],
                    'approved' => ['icon' => 'fa-check-circle', 'bg' => 'rgba(52, 199, 89, 0.1)', 'border' => '#34C759', 'text' => 'Approved - Siap Digunakan'],
                    'rejected' => ['icon' => 'fa-times-circle', 'bg' => 'rgba(255, 59, 48, 0.1)', 'border' => '#FF3B30', 'text' => 'Rejected - Ditolak'],
                    'exported' => ['icon' => 'fa-file-export', 'bg' => 'rgba(175, 82, 222, 0.1)', 'border' => '#AF52DE', 'text' => 'Exported - Sudah Diexport'],
                ];
                $config = $statusConfig[$draft->status] ?? $statusConfig['draft'];
            @endphp

            <div class="card-elevated rounded-apple-lg p-4" style="background: {{ $config['bg'] }}; border-left: 4px solid {{ $config['border'] }};">
                <div class="flex items-center">
                    <i class="fas {{ $config['icon'] }} text-xl mr-3" style="color: {{ $config['border'] }};"></i>
                    <span class="font-medium" style="color: #FFFFFF;">{{ $config['text'] }}</span>
                </div>
            </div>

            <!-- Compliance Dashboard -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-clipboard-check mr-2 text-blue-500"></i>
                    UKL-UPL Compliance Check
                </h3>
                @include('ai.partials.compliance-dashboard')
            </div>

            <!-- Edit Form -->
            <form action="{{ route('ai.drafts.update', [$project, $draft]) }}" method="POST" id="editForm">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <label class="block text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.8);">
                        Judul Dokumen
                    </label>
                    <input type="text" name="title" value="{{ $draft->title }}" 
                           class="input-dark w-full px-4 py-3 rounded-lg text-lg font-semibold"
                           required>
                </div>

                <!-- Content Editor -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <label class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">
                            Konten Dokumen
                        </label>
                        <div class="flex space-x-2">
                            <button type="button" onclick="formatText('bold')" 
                                    class="px-3 py-1 rounded text-sm" style="background: rgba(142, 142, 147, 0.2); color: #FFFFFF;"
                                    title="Bold">
                                <i class="fas fa-bold"></i>
                            </button>
                            <button type="button" onclick="formatText('italic')" 
                                    class="px-3 py-1 rounded text-sm" style="background: rgba(142, 142, 147, 0.2); color: #FFFFFF;"
                                    title="Italic">
                                <i class="fas fa-italic"></i>
                            </button>
                            <button type="button" onclick="formatText('underline')" 
                                    class="px-3 py-1 rounded text-sm" style="background: rgba(142, 142, 147, 0.2); color: #FFFFFF;"
                                    title="Underline">
                                <i class="fas fa-underline"></i>
                            </button>
                        </div>
                    </div>
                    
                    <textarea name="content" id="contentEditor" rows="25" 
                              class="input-dark w-full px-4 py-3 rounded-lg font-mono text-sm"
                              style="line-height: 1.8;"
                              required>{{ $draft->content }}</textarea>

                    <div class="mt-3 flex justify-between items-center text-sm" style="color: rgba(235, 235, 245, 0.6);">
                        <span>
                            <i class="fas fa-info-circle mr-1"></i>
                            Gunakan plain text atau Markdown untuk formatting
                        </span>
                        <span id="charCount">
                            {{ number_format(strlen($draft->content)) }} karakter
                        </span>
                    </div>
                </div>

                <!-- Save Buttons -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('ai.drafts.index', $project) }}" 
                       class="px-6 py-3 rounded-lg font-medium transition-colors"
                       style="background: rgba(142, 142, 147, 0.2); color: rgba(235, 235, 245, 0.8);">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>

                    <div class="flex space-x-3">
                        <button type="submit" name="status" value="draft"
                                class="px-6 py-3 rounded-lg font-medium transition-colors"
                                style="background: rgba(255, 149, 0, 0.9); color: #FFFFFF;">
                            <i class="fas fa-save mr-2"></i>Simpan sebagai Draft
                        </button>
                        <button type="submit" name="status" value="reviewed"
                                class="px-6 py-3 rounded-lg font-medium transition-colors"
                                style="background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%); color: #FFFFFF;">
                            <i class="fas fa-check mr-2"></i>Simpan & Tandai Reviewed
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Document Info -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-info-circle mr-2 text-apple-blue-dark"></i>Informasi
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span style="color: rgba(235, 235, 245, 0.6);">Template:</span>
                        <div class="font-medium mt-1" style="color: #FFFFFF;">
                            {{ $draft->template->name }}
                        </div>
                    </div>

                    <div>
                        <span style="color: rgba(235, 235, 245, 0.6);">Jenis Izin:</span>
                        <div class="font-medium mt-1" style="color: #FFFFFF;">
                            {{ strtoupper(str_replace('_', ' ', $draft->template->permit_type)) }}
                        </div>
                    </div>

                    <div>
                        <span style="color: rgba(235, 235, 245, 0.6);">Dibuat oleh:</span>
                        <div class="font-medium mt-1" style="color: #FFFFFF;">
                            {{ $draft->creator->name }}
                        </div>
                    </div>

                    <div>
                        <span style="color: rgba(235, 235, 245, 0.6);">Tanggal:</span>
                        <div class="font-medium mt-1" style="color: #FFFFFF;">
                            {{ $draft->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    @if($draft->status === 'approved' && $draft->approver)
                    <div>
                        <span style="color: rgba(235, 235, 245, 0.6);">Disetujui oleh:</span>
                        <div class="font-medium mt-1" style="color: #34C759;">
                            {{ $draft->approver->name }}
                            <div class="text-xs mt-1" style="color: rgba(52, 199, 89, 0.7);">
                                {{ $draft->approved_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-chart-bar mr-2 text-apple-blue-dark"></i>Statistik
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Jumlah Kata:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ number_format($draft->word_count) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Jumlah Karakter:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ number_format($draft->char_count) }}
                        </span>
                    </div>

                    @if($draft->sections && count($draft->sections) > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Jumlah Bagian:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ count($draft->sections) }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- AI Processing Info -->
            @if($draft->aiLog)
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-robot mr-2 text-apple-blue-dark"></i>AI Processing
                </h3>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Status:</span>
                        <span class="px-2 py-1 rounded font-medium" 
                              style="background: rgba(52, 199, 89, 0.2); color: #34C759;">
                            {{ strtoupper($draft->aiLog->status) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Total Tokens:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ number_format($draft->aiLog->total_tokens) }}
                        </span>
                    </div>

                    @if($draft->aiLog->metadata && isset($draft->aiLog->metadata['duration_seconds']))
                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Durasi:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ round($draft->aiLog->metadata['duration_seconds'], 1) }}s
                        </span>
                    </div>
                    @endif

                    @if($draft->aiLog->metadata && isset($draft->aiLog->metadata['chunks_count']))
                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Chunks:</span>
                        <span class="font-semibold" style="color: #FFFFFF;">
                            {{ $draft->aiLog->metadata['chunks_count'] }}
                        </span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Cost:</span>
                        <span class="font-semibold" style="color: #34C759;">
                            @if($draft->aiLog->cost > 0)
                                ${{ number_format($draft->aiLog->cost, 6) }}
                            @else
                                FREE
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span style="color: rgba(235, 235, 245, 0.6);">Model:</span>
                        <span class="font-medium text-xs" style="color: #FFFFFF;">
                            {{ $draft->aiLog->metadata['model'] ?? config('services.openrouter.model') }}
                        </span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Character counter
const contentEditor = document.getElementById('contentEditor');
const charCount = document.getElementById('charCount');

contentEditor.addEventListener('input', function() {
    const count = this.value.length;
    charCount.textContent = count.toLocaleString() + ' karakter';
});

// Simple text formatting (for demo)
function formatText(command) {
    const textarea = document.getElementById('contentEditor');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    if (!selectedText) {
        alert('Pilih teks terlebih dahulu');
        return;
    }
    
    let formattedText = selectedText;
    
    switch(command) {
        case 'bold':
            formattedText = '**' + selectedText + '**';
            break;
        case 'italic':
            formattedText = '*' + selectedText + '*';
            break;
        case 'underline':
            formattedText = '__' + selectedText + '__';
            break;
    }
    
    textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
    textarea.focus();
}

// Auto-save draft (every 2 minutes)
let autoSaveTimer;
const form = document.getElementById('editForm');

function getCsrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

function autoSave() {
    try {
        const formData = new FormData(form);
        // Force status to remain draft during auto-save
        formData.set('status', 'draft');
        // Method spoofing for Laravel route expecting PUT
        formData.set('_method', 'PUT');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(res => {
            // If not JSON (e.g., validation redirect), treat as failure silently
            const contentType = res.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) return null;
            return res.json();
        })
        .then(data => {
            if (data && data.success) {
                console.log('Auto-saved at', new Date().toLocaleTimeString());
            }
        })
        .catch(() => {/* swallow auto-save errors */});
    } catch (e) {
        // No-op if browser blocks
    }
}

// Auto-save every 2 minutes
autoSaveTimer = setInterval(autoSave, 120000);

// Clear timer on form submit
form.addEventListener('submit', function() {
    clearInterval(autoSaveTimer);
});
</script>

<!-- Alpine.js CDN for dropdown -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection
