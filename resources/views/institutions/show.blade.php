@extends('layouts.admin')

@section('title', 'Detail Institusi - ' . $institution->name)

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
                            <svg class="w-3 h-3 mx-3" style="color: rgba(142, 142, 147, 0.8);" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </li>
                        <li class="flex items-center">
                            <a href="{{ route('institutions.index') }}" class="text-apple-blue-dark hover:text-apple-blue">Institusi</a>
                            <svg class="w-3 h-3 mx-3" style="color: rgba(142, 142, 147, 0.8);" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </li>
                        <li style="color: rgba(235, 235, 245, 0.6);">{{ Str::limit($institution->name, 50) }}</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">{{ $institution->name }}</h1>
                <p class="mt-2">
                    <span class="px-2 py-1 text-sm font-medium rounded mr-2" style="background: rgba(10, 132, 255, 0.15); color: rgba(10, 132, 255, 1);">
                        {{ ucfirst($institution->type) }}
                    </span>
                    <span class="px-2 py-1 rounded-full text-sm font-medium"
                        style="@if($institution->is_active) background: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1); @else background: rgba(255, 69, 58, 0.15); color: rgba(255, 69, 58, 1); @endif">
                        {{ $institution->is_active ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('institutions.edit', $institution) }}" 
                   class="btn-primary px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                @if($institution->projects->count() === 0)
                    <form action="{{ route('institutions.destroy', $institution) }}"
                          method="POST"
                          class="inline"
                          x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus institusi ini?')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="btn-danger px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card-elevated rounded-apple-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background: rgba(10, 132, 255, 0.15);">
                    <i class="fas fa-project-diagram text-xl" style="color: rgba(10, 132, 255, 1);"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color: #FFFFFF;">{{ $stats['total_projects'] }}</div>
                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Proyek</div>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background: rgba(52, 199, 89, 0.15);">
                    <i class="fas fa-play text-xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color: #FFFFFF;">{{ $stats['active_projects'] }}</div>
                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Proyek Aktif</div>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background: rgba(191, 90, 242, 0.15);">
                    <i class="fas fa-check text-xl" style="color: rgba(191, 90, 242, 1);"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color: #FFFFFF;">{{ $stats['completed_projects'] }}</div>
                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Proyek Selesai</div>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background: rgba(255, 214, 10, 0.15);">
                    <i class="fas fa-money-bill text-xl" style="color: rgba(255, 214, 10, 1);"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold" style="color: #FFFFFF;">Rp {{ number_format($stats['total_budget'], 0, ',', '.') }}</div>
                    <div class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Total Budget</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Institution Details -->
            <div class="card-elevated rounded-apple-lg mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4" style="color: #FFFFFF;">Informasi Institusi</h2>
                    
                    <div class="space-y-4">
                        <!-- Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Nama Institusi</h3>
                                <p style="color: #FFFFFF;">{{ $institution->name }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Tipe</h3>
                                <span class="px-2 py-1 text-sm font-medium rounded" style="background: rgba(10, 132, 255, 0.15); color: rgba(10, 132, 255, 1);">
                                    {{ ucfirst($institution->type) }}
                                </span>
                            </div>
                        </div>

                        @if($institution->address)
                            <div>
                                <h3 class="text-sm font-medium mb-1" style="color: rgba(235, 235, 245, 0.6);">Alamat</h3>
                                <p style="color: #FFFFFF;">{{ $institution->address }}</p>
                            </div>
                        @endif

                        <!-- Contact Info -->
                        @if($institution->contact_person || $institution->phone || $institution->email)
                            <div>
                                <h3 class="text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Informasi Kontak</h3>
                                <div class="rounded-lg p-4" style="background: rgba(118, 118, 128, 0.12);">
                                    @if($institution->contact_person)
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-user w-5" style="color: rgba(142, 142, 147, 0.8);"></i>
                                            <span style="color: #FFFFFF;">{{ $institution->contact_person }}</span>
                                            @if($institution->contact_position)
                                                <span class="ml-2" style="color: rgba(235, 235, 245, 0.6);">({{ $institution->contact_position }})</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if($institution->phone)
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-phone w-5" style="color: rgba(142, 142, 147, 0.8);"></i>
                                            <a href="tel:{{ $institution->phone }}" class="text-apple-blue-dark hover:text-apple-blue">
                                                {{ $institution->phone }}
                                            </a>
                                        </div>
                                    @endif
                                    @if($institution->email)
                                        <div class="flex items-center">
                                            <i class="fas fa-envelope w-5" style="color: rgba(142, 142, 147, 0.8);"></i>
                                            <a href="mailto:{{ $institution->email }}" class="text-apple-blue-dark hover:text-apple-blue">
                                                {{ $institution->email }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($institution->notes)
                            <div>
                                <h3 class="text-sm font-medium mb-2" style="color: rgba(235, 235, 245, 0.6);">Catatan</h3>
                                <div class="rounded-lg p-3" style="background: rgba(255, 214, 10, 0.15); border: 1px solid rgba(255, 214, 10, 0.3);">
                                    <p class="text-sm" style="color: rgba(255, 214, 10, 1);">{{ $institution->notes }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Projects List -->
            <div class="card-elevated rounded-apple-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold" style="color: #FFFFFF;">Proyek Terkait</h2>
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">{{ $institution->projects->count() }} proyek</span>
                    </div>
                    
                    @if($recentProjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentProjects as $project)
                                <div class="rounded-lg p-4 transition-colors duration-200" style="border: 1px solid rgba(142, 142, 147, 0.3);">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium mb-1">
                                                <a href="{{ route('projects.show', $project) }}" class="text-apple-blue-dark hover:text-apple-blue">
                                                    {{ $project->name }}
                                                </a>
                                            </h3>
                                            @if($project->description)
                                                <p class="text-sm mb-2" style="color: rgba(235, 235, 245, 0.6);">{{ Str::limit($project->description, 100) }}</p>
                                            @endif
                                            <div class="flex items-center space-x-4 text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                                <span><i class="fas fa-user mr-1"></i>{{ $project->client_name }}</span>
                                                @if($project->budget)
                                                    <span><i class="fas fa-money-bill mr-1"></i>Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                                                @endif
                                                @if($project->deadline)
                                                    <span><i class="fas fa-calendar mr-1"></i>{{ $project->deadline->format('d M Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            @if($project->status)
                                                <span class="px-2 py-1 text-xs font-medium rounded" style="background: rgba(10, 132, 255, 0.15); color: rgba(10, 132, 255, 1);">
                                                    {{ $project->status->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($institution->projects->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="{{ route('projects.index', ['institution' => $institution->id]) }}" 
                                   class="text-apple-blue-dark hover:text-apple-blue text-sm">
                                    Lihat semua proyek ({{ $institution->projects->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 mx-auto mb-4" style="color: rgba(142, 142, 147, 0.8);">
                                <i class="fas fa-project-diagram text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium mb-2" style="color: #FFFFFF;">Belum ada proyek</h3>
                            <p class="mb-4" style="color: rgba(235, 235, 245, 0.6);">Institusi ini belum memiliki proyek yang terdaftar.</p>
                            <a href="{{ route('projects.create', ['institution_id' => $institution->id]) }}" 
                               class="btn-primary px-4 py-2 rounded-lg transition-colors duration-200 inline-block">
                                Tambah Proyek Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="card-elevated rounded-apple-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('projects.create', ['institution_id' => $institution->id]) }}" 
                       class="btn-primary w-full py-2 px-4 rounded-lg transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Proyek
                    </a>
                    <a href="{{ route('institutions.edit', $institution) }}" 
                       class="w-full py-2 px-4 rounded-lg transition-colors duration-200 flex items-center" style="background: rgba(142, 142, 147, 0.6); color: rgba(235, 235, 245, 0.8);">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Institusi
                    </a>
                </div>
            </div>

            <!-- Institution Stats -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Detail Institusi</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Status</span>
                        <span class="text-sm font-medium" style="color: #FFFFFF;">
                            {{ $institution->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Tipe</span>
                        <span class="text-sm font-medium" style="color: #FFFFFF;">{{ ucfirst($institution->type) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Terdaftar</span>
                        <span class="text-sm font-medium" style="color: #FFFFFF;">{{ $institution->created_at->format('d M Y') }}</span>
                    </div>
                    @if($institution->updated_at != $institution->created_at)
                        <div class="flex justify-between">
                            <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Terakhir Diperbarui</span>
                            <span class="text-sm font-medium" style="color: #FFFFFF;">{{ $institution->updated_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection