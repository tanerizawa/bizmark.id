@extends('layouts.admin')

@section('title', $permitType->name)

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('permit-types.index') }}" 
           class="inline-flex items-center text-sm mb-4 hover:text-apple-blue transition-colors" 
           style="color: rgba(235, 235, 245, 0.6);">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar
        </a>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">{{ $permitType->name }}</h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">{{ $permitType->code }}</p>
            </div>

            <div class="flex space-x-2">
                <a href="{{ route('permit-types.edit', $permitType) }}" 
                   class="px-4 py-2 rounded-lg transition-colors" 
                   style="background: rgba(255, 149, 0, 0.2); color: rgba(255, 149, 0, 1);">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('permit-types.destroy', $permitType) }}"
                      method="POST"
                      class="inline"
                      x-data @submit.prevent="if(confirm('Yakin ingin menghapus jenis izin ini? Tindakan ini tidak dapat dibatalkan.')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg transition-colors" 
                            style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-lg" style="background: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);">
        <p style="color: rgba(52, 199, 89, 1);">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-lg" style="background: rgba(255, 59, 48, 0.1); border: 1px solid rgba(255, 59, 48, 0.3);">
        <p style="color: rgba(255, 59, 48, 1);">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </p>
    </div>
    @endif

    <!-- Status Badge -->
    <div class="mb-6">
        @if($permitType->is_active)
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                  style="background: rgba(52, 199, 89, 0.2); color: rgba(52, 199, 89, 1);">
                <i class="fas fa-check-circle mr-2"></i>Aktif
            </span>
        @else
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full"
                  style="background: rgba(142, 142, 147, 0.2); color: rgba(142, 142, 147, 1);">
                <i class="fas fa-times-circle mr-2"></i>Tidak Aktif
            </span>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Informasi Dasar</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm mb-1" style="color: rgba(235, 235, 245, 0.6);">Kategori</p>
                        <span class="inline-flex px-2 py-1 text-sm font-semibold rounded-full"
                              style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                            {{ ucfirst($permitType->category) }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm mb-1" style="color: rgba(235, 235, 245, 0.6);">Institusi Penerbit</p>
                        <p class="text-base font-medium" style="color: #FFFFFF;">
                            {{ $permitType->institution?->name ?? 'Tidak ada' }}
                        </p>
                    </div>

                    @if($permitType->description)
                    <div>
                        <p class="text-sm mb-1" style="color: rgba(235, 235, 245, 0.6);">Deskripsi</p>
                        <p class="text-base" style="color: rgba(235, 235, 245, 0.8);">
                            {{ $permitType->description }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Processing & Cost -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Estimasi Waktu & Biaya</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-clock text-2xl mr-3" style="color: rgba(10, 132, 255, 1);"></i>
                            <div>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Waktu Proses</p>
                                <p class="text-xl font-bold" style="color: #FFFFFF;">
                                    {{ $permitType->avg_processing_days ?? '-' }}
                                    @if($permitType->avg_processing_days)
                                        <span class="text-sm font-normal" style="color: rgba(235, 235, 245, 0.6);">hari</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                        <div class="flex items-center mb-2">
                            <i class="fas fa-money-bill-wave text-2xl mr-3" style="color: rgba(52, 199, 89, 1);"></i>
                            <div>
                                <p class="text-xs" style="color: rgba(235, 235, 245, 0.6);">Estimasi Biaya</p>
                                <p class="text-base font-bold" style="color: #FFFFFF;">
                                    @if($permitType->estimated_cost_min && $permitType->estimated_cost_max)
                                        {{ $permitType->estimated_cost_range }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Required Documents -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Dokumen yang Diperlukan</h3>
                
                @if($permitType->required_documents && count($permitType->required_documents) > 0)
                    <ul class="space-y-2">
                        @foreach($permitType->required_documents as $doc)
                            <li class="flex items-start">
                                <i class="fas fa-file-alt mt-1 mr-3" style="color: rgba(10, 132, 255, 1);"></i>
                                <span style="color: rgba(235, 235, 245, 0.8);">{{ $doc }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="color: rgba(235, 235, 245, 0.5);">
                        <i class="fas fa-info-circle mr-2"></i>Tidak ada dokumen yang didefinisikan
                    </p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Usage Stats -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Statistik Penggunaan</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                        <div class="flex items-center">
                            <i class="fas fa-layer-group mr-2" style="color: rgba(10, 132, 255, 1);"></i>
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);">Template</span>
                        </div>
                        <span class="text-lg font-bold" style="color: #FFFFFF;">
                            {{ $permitType->templateItems()->count() }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                        <div class="flex items-center">
                            <i class="fas fa-project-diagram mr-2" style="color: rgba(255, 149, 0, 1);"></i>
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.8);">Proyek</span>
                        </div>
                        <span class="text-lg font-bold" style="color: #FFFFFF;">
                            {{ $permitType->projectPermits()->count() }}
                        </span>
                    </div>
                </div>

                @if($permitType->templateItems()->count() > 0 || $permitType->projectPermits()->count() > 0)
                    <div class="mt-4 p-3 rounded-lg" style="background: rgba(255, 149, 0, 0.1); border: 1px solid rgba(255, 149, 0, 0.3);">
                        <p class="text-xs" style="color: rgba(255, 149, 0, 1);">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Jenis izin ini sedang digunakan dan tidak dapat dihapus
                        </p>
                    </div>
                @endif
            </div>

            <!-- Templates Using This -->
            @if($permitType->templateItems()->count() > 0)
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Template Menggunakan</h3>
                
                <div class="space-y-2">
                    @foreach($permitType->templateItems()->with('template')->get() as $item)
                        <div class="p-3 rounded-lg" style="background: rgba(58, 58, 60, 0.3);">
                            <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">
                                {{ $item->template->name }}
                            </p>
                            <p class="text-xs mt-1" style="color: rgba(235, 235, 245, 0.5);">
                                @if($item->is_goal_permit)
                                    <i class="fas fa-flag mr-1" style="color: rgba(255, 149, 0, 1);"></i>
                                    Izin Tujuan
                                @else
                                    Prasyarat
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Projects Using This -->
            @if($permitType->projectPermits()->count() > 0)
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Proyek Menggunakan</h3>
                
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($permitType->projectPermits()->with('project')->get() as $projectPermit)
                        <a href="{{ route('projects.show', $projectPermit->project) }}" 
                           class="block p-3 rounded-lg hover:bg-opacity-50 transition-colors" 
                           style="background: rgba(58, 58, 60, 0.3);">
                            <p class="text-sm font-medium" style="color: rgba(235, 235, 245, 0.8);">
                                {{ $projectPermit->project->name }}
                            </p>
                            <p class="text-xs mt-1" style="color: {{ $projectPermit->statusColor }}">
                                {{ $projectPermit->statusLabel }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Metadata</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p style="color: rgba(235, 235, 245, 0.6);">Dibuat</p>
                        <p style="color: rgba(235, 235, 245, 0.8);">
                            {{ $permitType->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p style="color: rgba(235, 235, 245, 0.6);">Terakhir Diperbarui</p>
                        <p style="color: rgba(235, 235, 245, 0.8);">
                            {{ $permitType->updated_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
