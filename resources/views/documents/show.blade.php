@extends('layouts.admin')

@section('title', 'Detail Dokumen - ' . $document->title)

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
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
                        <li class="text-dark-text-secondary">{{ Str::limit($document->title, 50) }}</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-white">{{ $document->title }}</h1>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('documents.download', $document) }}" 
                   class="btn-success px-4 py-2 rounded-apple-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-download mr-2"></i>
                    Download
                </a>
                @auth
                    @if(auth()->user()->id === $document->uploaded_by || auth()->user()->hasRole('admin'))
                        <a href="{{ route('documents.edit', $document) }}" 
                           class="btn-primary px-4 py-2 rounded-apple-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Document Info Card -->
            <div class="card-elevated rounded-apple-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white">Informasi Dokumen</h2>
                    
                    <!-- Status Badge -->
                    @php
                        $statusStyles = [
                            'draft' => 'bg-[rgba(142,142,147,0.4)] text-dark-text-primary/80',
                            'review' => 'bg-[rgba(255,204,0,0.3)] text-apple-yellow',
                            'approved' => 'bg-apple-green/30 text-apple-green',
                            'submitted' => 'bg-apple-blue/30 text-apple-blue',
                            'default' => 'bg-[rgba(191,90,242,0.3)] text-[rgba(191,90,242,1)]',
                        ];
                    @endphp
                    <div class="mb-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusStyles[$document->status] ?? $statusStyles['default'] }}">
                            <i class="fas fa-circle mr-1 text-xs"></i>
                            {{ ucfirst($document->status) }}
                        </span>
                    </div>

                    <!-- Description -->
                    @if($document->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">Deskripsi</h3>
                            <p class="leading-relaxed text-dark-text-secondary">{{ $document->description }}</p>
                        </div>
                    @endif

                    <!-- File Preview -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium mb-3 text-dark-text-primary/80">Preview File</h3>
                        <div class="rounded-lg p-4 border border-white/10 bg-[rgba(58,58,60,0.5)]">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4 bg-apple-blue/20">
                                    @if(str_contains($document->mime_type, 'pdf'))
                                        <i class="fas fa-file-pdf text-apple-red text-xl"></i>
                                    @elseif(str_contains($document->mime_type, 'image'))
                                        <i class="fas fa-file-image text-apple-green text-xl"></i>
                                    @elseif(str_contains($document->mime_type, 'word'))
                                        <i class="fas fa-file-word text-apple-blue text-xl"></i>
                                    @elseif(str_contains($document->mime_type, 'excel') || str_contains($document->mime_type, 'spreadsheet'))
                                        <i class="fas fa-file-excel text-apple-green text-xl"></i>
                                    @else
                                        <i class="fas fa-file text-xl text-[rgba(142,142,147,0.8)]"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-dark-text-primary/80">{{ $document->file_name }}</h4>
                                    <p class="text-sm text-dark-text-secondary">
                                        {{ number_format($document->file_size / 1024, 1) }} KB • {{ $document->mime_type }}
                                    </p>
                                </div>
                                <a href="{{ route('documents.download', $document) }}" 
                                   class="text-apple-blue-dark hover:text-apple-blue ml-4">
                                    <i class="fas fa-download text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Version Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-medium mb-1 text-dark-text-primary/80">Versi</h3>
                            <p class="text-dark-text-primary/80">{{ $document->version }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium mb-1 text-dark-text-primary/80">Kategori</h3>
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-apple-blue/30 text-apple-blue">
                                {{ ucfirst($document->category) }}
                            </span>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($document->notes)
                        <div>
                            <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">Catatan</h3>
                            <div class="rounded-lg p-3 bg-[rgba(255,204,0,0.2)] border border-[rgba(255,214,10,0.3)]">
                                <p class="text-sm text-apple-yellow">{{ $document->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card-elevated rounded-apple-lg">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4 text-white">Riwayat Aktivitas</h2>
                    <div class="space-y-4">
                        <!-- Upload Activity -->
                        <div class="flex items-start">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 mt-1 bg-apple-blue/20">
                                <i class="fas fa-upload text-apple-blue text-xs"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-dark-text-primary/80">
                                    <span class="font-medium">{{ $document->uploader->name }}</span> 
                                    mengunggah dokumen
                                </p>
                                <p class="text-xs text-dark-text-secondary">
                                    {{ $document->created_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        </div>

                        @if($document->updated_at != $document->created_at)
                            <!-- Update Activity -->
                            <div class="flex items-start">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 mt-1 bg-apple-green/20">
                                    <i class="fas fa-edit text-apple-green text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-dark-text-primary/80">
                                        Dokumen diperbarui
                                    </p>
                                    <p class="text-xs text-dark-text-secondary">
                                        {{ $document->updated_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if($document->last_accessed_at)
                            <!-- Last Access Activity -->
                            <div class="flex items-start">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 mt-1 bg-[rgba(142,142,147,0.4)]">
                                    <i class="fas fa-eye text-xs text-[rgba(142,142,147,0.8)]"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-dark-text-primary/80">
                                        Terakhir diakses
                                    </p>
                                    <p class="text-xs text-dark-text-secondary">
                                        {{ \Carbon\Carbon::parse($document->last_accessed_at)->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Project Info -->
            <div class="card-elevated rounded-apple-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-white">Informasi Proyek</h3>
                    <div class="space-y-4">
                        <div>
                            <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Nama Proyek</h4>
                            <a href="{{ route('projects.show', $document->project) }}" 
                               class="text-apple-blue-dark hover:text-apple-blue">
                                {{ $document->project->name }}
                            </a>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Klien</h4>
                            <p class="text-dark-text-primary/80">{{ $document->project->client_name }}</p>
                        </div>
                        @if($document->project->institution)
                            <div>
                                <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Institusi</h4>
                                <p class="text-dark-text-primary/80">{{ $document->project->institution->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Task Info -->
            @if($document->task)
                <div class="card-elevated rounded-apple-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-white">Tugas Terkait</h3>
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Nama Tugas</h4>
                                <a href="{{ route('tasks.show', $document->task) }}" 
                                   class="text-apple-blue-dark hover:text-apple-blue">
                                    {{ $document->task->title }}
                                </a>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Status Tugas</h4>
                                <span class="px-2 py-1 text-xs font-medium rounded bg-apple-blue/30 text-apple-blue">
                                    {{ ucfirst($document->task->status) }}
                                </span>
                            </div>
                            @if($document->task->deadline)
                                <div>
                                    <h4 class="text-sm font-medium mb-1 text-dark-text-primary/80">Deadline</h4>
                                    <p class="text-dark-text-primary/80">{{ $document->task->deadline->format('d M Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Document Stats -->
            <div class="card-elevated rounded-apple-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-white">Statistik Dokumen</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-dark-text-secondary">Total Download</span>
                            <span class="text-sm font-medium text-dark-text-primary/80">{{ $document->download_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-dark-text-secondary">Diunggah oleh</span>
                            <span class="text-sm font-medium text-dark-text-primary/80">{{ $document->uploader->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-dark-text-secondary">Tanggal Upload</span>
                            <span class="text-sm font-medium text-dark-text-primary/80">{{ $document->created_at->format('d M Y') }}</span>
                        </div>
                        @if($document->is_confidential)
                            <div class="flex justify-between">
                                <span class="text-sm text-dark-text-secondary">Keamanan</span>
                                <span class="text-sm font-medium text-apple-red-dark">
                                    <i class="fas fa-lock mr-1"></i>Rahasia
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection