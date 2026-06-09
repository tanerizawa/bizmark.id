@extends('layouts.admin')

@section('title', 'Edit Dokumen - ' . $document->title)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
                <nav class="text-sm breadcrumbs mb-4">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-apple-blue-dark hover:text-apple-blue">Dashboard</a>
                    <svg class="w-3 h-3 mx-3 text-[rgba(142,142,147,0.8)]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('documents.index') }}" class="text-apple-blue-dark hover:text-apple-blue">Dokumen</a>
                    <svg class="w-3 h-3 mx-3 text-[rgba(142,142,147,0.8)]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('documents.show', $document) }}" class="text-apple-blue-dark hover:text-apple-blue">{{ Str::limit($document->title, 30) }}</a>
                    <svg class="w-3 h-3 mx-3 text-[rgba(142,142,147,0.8)]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                </li>
                <li class="text-dark-text-secondary">Edit</li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-white">Edit Dokumen</h1>
        <p class="mt-2 text-dark-text-secondary">Perbarui informasi dokumen {{ $document->title }}</p>
    </div>

    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <!-- Basic Information -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-6 text-white">Informasi Dasar</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Judul Dokumen <span class="text-apple-red-dark">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $document->title) }}"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('title') ring-2 ring-apple-red @enderror"
                                   required>
                            @error('title')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Kategori <span class="text-apple-red-dark">*</span>
                            </label>
                            <select id="category" 
                                    name="category" 
                                    class="input-dark w-full px-3 py-2 rounded-lg @error('category') ring-2 ring-apple-red @enderror"
                                    required>
                                <option value="">Pilih Kategori</option>
                                <option value="administrasi" {{ old('category', $document->category) === 'administrasi' ? 'selected' : '' }}>Administrasi</option>
                                <option value="teknis" {{ old('category', $document->category) === 'teknis' ? 'selected' : '' }}>Teknis</option>
                                <option value="lingkungan" {{ old('category', $document->category) === 'lingkungan' ? 'selected' : '' }}>Lingkungan</option>
                                <option value="transportasi" {{ old('category', $document->category) === 'transportasi' ? 'selected' : '' }}>Transportasi</option>
                                <option value="legal" {{ old('category', $document->category) === 'legal' ? 'selected' : '' }}>Legal</option>
                                <option value="keuangan" {{ old('category', $document->category) === 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                                <option value="lainnya" {{ old('category', $document->category) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('category')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Version -->
                        <div>
                            <label for="version" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Versi
                            </label>
                            <input type="text" 
                                   id="version" 
                                   name="version" 
                                   value="{{ old('version', $document->version) }}"
                                   placeholder="1.0"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('version') ring-2 ring-apple-red @enderror">
                            @error('version')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Status <span class="text-apple-red-dark">*</span>
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="input-dark w-full px-3 py-2 rounded-lg @error('status') ring-2 ring-apple-red @enderror"
                                    required>
                                <option value="draft" {{ old('status', $document->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="review" {{ old('status', $document->status) === 'review' ? 'selected' : '' }}>Review</option>
                                <option value="approved" {{ old('status', $document->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="submitted" {{ old('status', $document->status) === 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="final" {{ old('status', $document->status) === 'final' ? 'selected' : '' }}>Final</option>
                            </select>
                            @error('status')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Document Type -->
                        <div>
                            <label for="document_type" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Tipe Dokumen
                            </label>
                            <input type="text" 
                                   id="document_type" 
                                   name="document_type" 
                                   value="{{ old('document_type', $document->document_type) }}"
                                   placeholder="Contoh: Surat, Laporan, Formulir"
                                   class="input-dark w-full px-3 py-2 rounded-lg @error('document_type') ring-2 ring-apple-red @enderror">
                            @error('document_type')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Deskripsi
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Deskripsi detail tentang dokumen ini..."
                                      class="input-dark w-full px-3 py-2 rounded-lg @error('description') ring-2 ring-apple-red @enderror">{{ old('description', $document->description) }}</textarea>
                            @error('description')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                Catatan
                            </label>
                            <textarea id="notes" 
                                      name="notes" 
                                      rows="3"
                                      placeholder="Catatan tambahan atau instruksi khusus..."
                                      class="input-dark w-full px-3 py-2 rounded-lg @error('notes') ring-2 ring-apple-red @enderror">{{ old('notes', $document->notes) }}</textarea>
                            @error('notes')
                                <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- File Upload -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-6 text-white">File Dokumen</h2>
                    
                    <!-- Current File Info -->
                    <div class="mb-6 p-4 rounded-lg bg-[rgba(58,58,60,0.5)] border border-white/10">
                        <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">File Saat Ini</h3>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 bg-apple-blue/20">
                                @if(str_contains($document->mime_type, 'pdf'))
                                    <i class="fas fa-file-pdf text-apple-red"></i>
                                @elseif(str_contains($document->mime_type, 'image'))
                                    <i class="fas fa-file-image text-apple-green"></i>
                                @elseif(str_contains($document->mime_type, 'word'))
                                    <i class="fas fa-file-word text-apple-blue"></i>
                                @elseif(str_contains($document->mime_type, 'excel') || str_contains($document->mime_type, 'spreadsheet'))
                                    <i class="fas fa-file-excel text-apple-green"></i>
                                @else
                                    <i class="fas fa-file text-[rgba(142,142,147,0.8)]"></i>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-dark-text-primary/80">{{ $document->file_name }}</p>
                                <p class="text-xs text-dark-text-secondary">
                                    {{ number_format($document->file_size / 1024, 1) }} KB • {{ $document->mime_type }}
                                </p>
                            </div>
                            <a href="{{ route('documents.download', $document) }}" 
                               class="text-apple-blue-dark hover:text-apple-blue ml-4">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>

                    <!-- New File Upload -->
                    <div>
                        <label for="file" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Upload File Baru (Opsional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-lg hover:border-gray-400 transition-colors duration-200 border-white/10">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-[rgba(142,142,147,0.8)]" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-dark-text-secondary">
                                    <label for="file" class="relative cursor-pointer rounded-md font-medium text-apple-blue-dark hover:text-apple-blue focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-apple-blue">
                                        <span>Upload file baru</span>
                                        <input id="file" name="file" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-dark-text-secondary">PDF, DOC, XLS, JPG sampai 10MB</p>
                            </div>
                        </div>
                        @error('file')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm mt-2 text-dark-text-secondary">
                            <i class="fas fa-info-circle mr-1"></i>
                            Jika tidak upload file baru, file yang ada sekarang akan tetap digunakan.
                        </p>
                    </div>
                </div>

                <!-- Security & Access -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <h2 class="text-xl font-semibold mb-6 text-white">Keamanan & Akses</h2>
                    
                    <div class="space-y-4">
                        <!-- Confidential -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_confidential" 
                                   name="is_confidential" 
                                   value="1"
                                   {{ old('is_confidential', $document->is_confidential) ? 'checked' : '' }}
                                   class="h-4 w-4 text-apple-blue border-gray-300 rounded focus:ring-apple-blue">
                            <label for="is_confidential" class="ml-2 block text-sm text-dark-text-primary/80">
                                Dokumen rahasia (akses terbatas)
                            </label>
                        </div>

                        <!-- Document Date -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="document_date" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                    Tanggal Dokumen
                                </label>
                                <input type="date" 
                                       id="document_date" 
                                       name="document_date" 
                                       value="{{ old('document_date', $document->document_date ? $document->document_date->format('Y-m-d') : '') }}"
                                       class="input-dark w-full px-3 py-2 rounded-lg">
                            </div>

                            <div>
                                <label for="submission_date" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                    Tanggal Pengajuan
                                </label>
                                <input type="date" 
                                       id="submission_date" 
                                       name="submission_date" 
                                       value="{{ old('submission_date', $document->submission_date ? $document->submission_date->format('Y-m-d') : '') }}"
                                       class="input-dark w-full px-3 py-2 rounded-lg">
                            </div>

                            <div>
                                <label for="approval_date" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                                    Tanggal Persetujuan
                                </label>
                                <input type="date" 
                                       id="approval_date" 
                                       name="approval_date" 
                                       value="{{ old('approval_date', $document->approval_date ? $document->approval_date->format('Y-m-d') : '') }}"
                                       class="input-dark w-full px-3 py-2 rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Project & Task Selection -->
                <div class="card-elevated rounded-apple-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-white">Proyek & Tugas</h3>
                    
                    <!-- Project -->
                    <div class="mb-4">
                        <label for="project_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Proyek <span class="text-apple-red-dark">*</span>
                        </label>
                        <select id="project_id" 
                                name="project_id" 
                                class="input-dark w-full px-3 py-2 rounded-lg @error('project_id') ring-2 ring-apple-red @enderror"
                                required>
                            <option value="">Pilih Proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id', $document->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Task -->
                    <div>
                        <label for="task_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Tugas (Opsional)
                        </label>
                        <select id="task_id" 
                                name="task_id" 
                                class="input-dark w-full px-3 py-2 rounded-lg">
                            <option value="">Pilih Tugas</option>
                            @if($document->task)
                                <option value="{{ $document->task->id }}" selected>
                                    {{ $document->task->title }}
                                </option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card-elevated rounded-apple-lg p-6">
                    <div class="space-y-3">
                        <button type="submit" 
                                class="btn-primary w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Perubahan
                        </button>
                        
                        <a href="{{ route('documents.show', $document) }}" 
                           class="w-full py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center bg-[rgba(142,142,147,0.6)] text-dark-text-primary/80">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const taskSelect = document.getElementById('task_id');
    
    projectSelect.addEventListener('change', function() {
        const projectId = this.value;
        
        // Clear task options
        taskSelect.innerHTML = '<option value="">Loading...</option>';
        
        if (projectId) {
            fetch(`{{ route('api.tasks-by-project') }}?project_id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    taskSelect.innerHTML = '<option value="">Pilih Tugas</option>';
                    data.forEach(task => {
                        const option = document.createElement('option');
                        option.value = task.id;
                        option.textContent = task.title;
                        taskSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading tasks:', error);
                    taskSelect.innerHTML = '<option value="">Error loading tasks</option>';
                });
        } else {
            taskSelect.innerHTML = '<option value="">Pilih Tugas</option>';
        }
    });
});
</script>
@endsection