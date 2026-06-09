@extends('layouts.admin')

@section('title', $permitTemplate->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('permit-templates.index') }}" 
           class="inline-flex items-center text-sm mb-4 hover:text-apple-blue transition-colors" 
           style="color: rgba(235, 235, 245, 0.6);">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Daftar Template
        </a>
        
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">{{ $permitTemplate->name }}</h1>
                @if($permitTemplate->description)
                    <p class="mt-2" style="color: rgba(235, 235, 245, 0.6);">{{ $permitTemplate->description }}</p>
                @endif
            </div>

            <div class="flex space-x-2">
                <form action="{{ route('permit-templates.destroy', $permitTemplate) }}"
                      method="POST"
                      class="inline"
                      x-data @submit.prevent="if(confirm('Yakin ingin menghapus template ini?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 rounded-lg transition-colors" 
                            style="background: rgba(255, 59, 48, 0.2); color: rgba(255, 59, 48, 1);">
                        <i class="fas fa-trash mr-2"></i>Hapus Template
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-4">
                <div class="card-elevated rounded-apple-lg p-4">
                    <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Total Izin</p>
                    <p class="text-2xl font-bold" style="color: rgba(10, 132, 255, 1);">
                        {{ $permitTemplate->items->count() }}
                    </p>
                </div>

                <div class="card-elevated rounded-apple-lg p-4">
                    <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Estimasi Waktu</p>
                    <p class="text-2xl font-bold" style="color: rgba(255, 149, 0, 1);">
                        {{ $permitTemplate->estimated_days ?? 0 }}
                        <span class="text-sm font-normal" style="color: rgba(235, 235, 245, 0.6);">hari</span>
                    </p>
                </div>

                <div class="card-elevated rounded-apple-lg p-4">
                    <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Estimasi Biaya</p>
                    <p class="text-lg font-bold" style="color: rgba(52, 199, 89, 1);">
                        {{ $permitTemplate->estimated_cost ? 'Rp ' . number_format($permitTemplate->estimated_cost / 1000000, 0) . 'M' : '-' }}
                    </p>
                </div>
            </div>

            <!-- Permit Flow Diagram -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-6" style="color: #FFFFFF;">
                    <i class="fas fa-sitemap mr-2"></i>Alur Izin & Dependensi
                </h3>

                <div class="space-y-4">
                    @foreach($permitTemplate->items->sortBy('sequence_order') as $index => $item)
                        <div class="relative">
                            <!-- Permit Card -->
                            <div class="flex items-start gap-4 p-4 rounded-lg {{ $item->is_goal_permit ? 'ring-2' : '' }}" 
                                 style="background: rgba(58, 58, 60, 0.5); {{ $item->is_goal_permit ? 'border-color: rgba(10, 132, 255, 1);' : '' }}">
                                
                                <!-- Sequence Number -->
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center font-bold" 
                                     style="background: rgba(10, 132, 255, 0.3); color: rgba(10, 132, 255, 1);">
                                    {{ $item->sequence_order }}
                                </div>

                                <div class="flex-1">
                                    <!-- Permit Info -->
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h4 class="font-semibold" style="color: #FFFFFF;">
                                                {{ $item->permitType->name }}
                                                @if($item->is_goal_permit)
                                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full" 
                                                          style="background: rgba(10, 132, 255, 0.2); color: rgba(10, 132, 255, 1);">
                                                        <i class="fas fa-flag mr-1"></i>TUJUAN
                                                    </span>
                                                @endif
                                            </h4>
                                            <p class="text-sm mt-1" style="color: rgba(235, 235, 245, 0.6);">
                                                {{ $item->permitType->code }} • {{ $item->permitType->institution?->name ?? '-' }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-sm font-semibold" style="color: rgba(255, 149, 0, 1);">
                                                <i class="fas fa-clock mr-1"></i>{{ $item->permitType->avg_processing_days ?? 0 }} hari
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Dependencies -->
                                    @if($item->dependencies->count() > 0)
                                        <div class="mt-3 pt-3" style="border-top: 1px solid rgba(58, 58, 60, 0.8);">
                                            <p class="text-xs font-semibold mb-2" style="color: rgba(235, 235, 245, 0.6);">
                                                <i class="fas fa-link mr-1"></i>PRASYARAT:
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($item->dependencies as $dep)
                                                    <span class="inline-flex items-center px-2 py-1 text-xs rounded-full" 
                                                          style="background: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 0.2)' : 'rgba(255, 59, 48, 0.2)' }}; 
                                                                 color: {{ $dep->can_proceed_without ? 'rgba(255, 149, 0, 1)' : 'rgba(255, 59, 48, 1)' }};">
                                                        @if($dep->can_proceed_without)
                                                            <i class="fas fa-info-circle mr-1"></i>
                                                        @else
                                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                                        @endif
                                                        {{ $dep->dependsOnItem->permitType->code }}
                                                        @if($dep->can_proceed_without)
                                                            (Opsional)
                                                        @else
                                                            (Wajib)
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Arrow to next -->
                            @if(!$loop->last)
                                <div class="flex justify-center my-2">
                                    <i class="fas fa-arrow-down text-2xl" style="color: rgba(235, 235, 245, 0.3);"></i>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Permit Types List -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-list mr-2"></i>Daftar Izin dalam Template
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="border-b" style="border-color: rgba(58, 58, 60, 0.8);">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: rgba(235, 235, 245, 0.6);">
                                    #
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: rgba(235, 235, 245, 0.6);">
                                    Nama Izin
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: rgba(235, 235, 245, 0.6);">
                                    Institusi
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: rgba(235, 235, 245, 0.6);">
                                    Waktu
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase" style="color: rgba(235, 235, 245, 0.6);">
                                    Dependensi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: rgba(58, 58, 60, 0.8);">
                            @foreach($permitTemplate->items->sortBy('sequence_order') as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium" style="color: rgba(10, 132, 255, 1);">
                                        {{ $item->sequence_order }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium" style="color: #FFFFFF;">
                                            {{ $item->permitType->name }}
                                            @if($item->is_goal_permit)
                                                <i class="fas fa-flag ml-2" style="color: rgba(10, 132, 255, 1);"></i>
                                            @endif
                                        </div>
                                        <div class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                                            {{ $item->permitType->code }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(235, 235, 245, 0.8);">
                                        {{ $item->permitType->institution?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(255, 149, 0, 1);">
                                        {{ $item->permitType->avg_processing_days ?? 0 }} hari
                                    </td>
                                    <td class="px-4 py-3 text-sm" style="color: rgba(235, 235, 245, 0.8);">
                                        {{ $item->dependencies->count() }} prasyarat
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Apply to Project -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">
                    <i class="fas fa-rocket mr-2"></i>Terapkan ke Proyek
                </h3>
                
                <p class="text-sm mb-4" style="color: rgba(235, 235, 245, 0.6);">
                    Gunakan template ini untuk mempercepat setup izin pada proyek Anda
                </p>

                <form action="{{ route('permit-templates.apply', $permitTemplate) }}" method="POST">
                    @csrf
                    <select name="project_id" 
                            class="input-dark w-full px-3 py-2 rounded-md mb-3"
                            required>
                        <option value="">Pilih Proyek...</option>
                        @foreach(\App\Models\Project::orderBy('name')->get() as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>

                    <button type="submit" 
                            class="w-full px-4 py-2 rounded-lg font-medium transition-colors" 
                            style="background: rgba(10, 132, 255, 0.9); color: #FFFFFF;">
                        <i class="fas fa-check mr-2"></i>Terapkan Template
                    </button>
                </form>
            </div>

            <!-- Template Stats -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Statistik Template</h3>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Total Dependensi</p>
                        <p class="text-xl font-bold" style="color: #FFFFFF;">
                            {{ $permitTemplate->items->sum(function($item) { return $item->dependencies->count(); }) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Izin Wajib</p>
                        <p class="text-xl font-bold" style="color: rgba(255, 59, 48, 1);">
                            {{ $permitTemplate->items->sum(function($item) { 
                                return $item->dependencies->where('can_proceed_without', false)->count(); 
                            }) }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs mb-1" style="color: rgba(235, 235, 245, 0.6);">Izin Opsional</p>
                        <p class="text-xl font-bold" style="color: rgba(255, 149, 0, 1);">
                            {{ $permitTemplate->items->sum(function($item) { 
                                return $item->dependencies->where('can_proceed_without', true)->count(); 
                            }) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4" style="color: #FFFFFF;">Metadata</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <p style="color: rgba(235, 235, 245, 0.6);">Dibuat</p>
                        <p style="color: rgba(235, 235, 245, 0.8);">
                            {{ $permitTemplate->created_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p style="color: rgba(235, 235, 245, 0.6);">Terakhir Diperbarui</p>
                        <p style="color: rgba(235, 235, 245, 0.8);">
                            {{ $permitTemplate->updated_at->format('d M Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
