@extends('layouts.admin')

@section('title', 'Template Izin')
@section('page-title', 'Template Izin')

@section('content')
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 space-y-3 sm:space-y-0">
        <div>
            <p class="text-sm" style="color: rgba(235, 235, 245, 0.6);">Template siap pakai untuk mempercepat setup proyek perizinan</p>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" class="px-4 py-2 rounded-apple text-sm font-medium transition-apple" 
                    style="background-color: #2C2C2E; color: rgba(235, 235, 245, 0.6); border: 1px solid rgba(84, 84, 88, 0.65); box-shadow: 0 2px 8px rgba(0, 0, 0, 0.48);" 
                    onmouseover="this.style.backgroundColor='#3A3A3C'; this.style.color='#FFFFFF'" 
                    onmouseout="this.style.backgroundColor='#2C2C2E'; this.style.color='rgba(235, 235, 245, 0.6)'">
                <i class="fas fa-filter mr-2"></i>
                Filter
            </button>
            <a href="{{ route('permit-templates.create') }}" 
               class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>
                Buat Template
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-apple-lg" style="background-color: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);">
            <p style="color: rgba(52, 199, 89, 1);">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </p>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        @php
            $totalTemplates = $templates->count();
            $totalItems = $templates->sum('items_count');
            $totalDependencies = $templates->sum('dependencies_count');
            $avgDuration = $templates->avg('total_estimated_days') ? round($templates->avg('total_estimated_days')) : 0;
        @endphp

        <!-- Total Template -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Template</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $totalTemplates }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(0, 122, 255, 0.15);">
                    <i class="fas fa-layer-group text-xl" style="color: rgba(0, 122, 255, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Total Izin -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Total Jenis Izin</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $totalItems }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(52, 199, 89, 0.15);">
                    <i class="fas fa-certificate text-xl" style="color: rgba(52, 199, 89, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Dependencies -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Dependencies</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">{{ $totalDependencies }}</div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(255, 149, 0, 0.15);">
                    <i class="fas fa-project-diagram text-xl" style="color: rgba(255, 149, 0, 1);"></i>
                </div>
            </div>
        </div>

        <!-- Rata-rata Durasi -->
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">Rata-rata Durasi</div>
                    <div class="text-2xl font-bold mt-1" style="color: #FFFFFF;">
                        {{ $avgDuration }}
                        <span class="text-sm font-normal" style="color: rgba(235, 235, 245, 0.6);">hari</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: rgba(175, 82, 222, 0.15);">
                    <i class="fas fa-clock text-xl" style="color: rgba(175, 82, 222, 1);"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-4 overflow-hidden">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold" style="color: #FFFFFF;">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('permit-templates.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Search -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Pencarian</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nama atau deskripsi template..." 
                                   class="input-dark w-full pl-9 pr-3 py-2 rounded-apple text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-sm" style="color: rgba(235, 235, 245, 0.3);"></i>
                        </div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Kategori</label>
                        <select name="category" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Kategori</option>
                            <option value="industrial" {{ request('category') == 'industrial' ? 'selected' : '' }}>Industrial</option>
                            <option value="strategic" {{ request('category') == 'strategic' ? 'selected' : '' }}>Strategic</option>
                            <option value="business" {{ request('category') == 'business' ? 'selected' : '' }}>Business</option>
                            <option value="commercial" {{ request('category') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: rgba(235, 235, 245, 0.6);">Urutkan</label>
                        <select name="sort" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="usage" {{ request('sort') == 'usage' ? 'selected' : '' }}>Paling Banyak Digunakan</option>
                            <option value="items" {{ request('sort') == 'items' ? 'selected' : '' }}>Jumlah Izin</option>
                            <option value="duration" {{ request('sort') == 'duration' ? 'selected' : '' }}>Durasi Estimasi</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                    <div class="text-xs" style="color: rgba(235, 235, 245, 0.6);">
                        Menampilkan {{ $templates->count() }} template
                    </div>
                    <div class="flex items-center space-x-3">
                        <button type="button" 
                                onclick="document.querySelector('form').reset(); window.location.href='{{ route('permit-templates.index') }}'"
                                class="px-4 py-2 rounded-apple text-sm font-medium transition-apple" 
                                style="background-color: #2C2C2E; color: rgba(235, 235, 245, 0.6); border: 1px solid rgba(84, 84, 88, 0.65);">
                            Reset
                        </button>
                        <button type="submit" 
                                class="btn-primary px-4 py-2 text-white rounded-apple text-sm font-medium">
                            <i class="fas fa-search mr-2"></i>
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead style="background-color: var(--dark-bg-secondary);">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Template</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Jenis Izin</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Dependencies</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Estimasi</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Penggunaan</th>
                        <th scope="col" class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" style="background-color: var(--dark-bg-secondary);">
                    @forelse($templates as $template)
                        @php
                            // Category configuration
                            $categoryConfig = [
                                'industrial' => ['icon' => 'fa-industry', 'color' => 'rgba(255, 149, 0, 1)', 'bg' => 'rgba(255, 149, 0, 0.15)', 'label' => 'Industrial'],
                                'strategic' => ['icon' => 'fa-flag', 'color' => 'rgba(255, 59, 48, 1)', 'bg' => 'rgba(255, 59, 48, 0.15)', 'label' => 'Strategic'],
                                'business' => ['icon' => 'fa-briefcase', 'color' => 'rgba(52, 199, 89, 1)', 'bg' => 'rgba(52, 199, 89, 0.15)', 'label' => 'Business'],
                                'commercial' => ['icon' => 'fa-building', 'color' => 'rgba(0, 122, 255, 1)', 'bg' => 'rgba(0, 122, 255, 0.15)', 'label' => 'Commercial'],
                                'general' => ['icon' => 'fa-layer-group', 'color' => 'rgba(142, 142, 147, 1)', 'bg' => 'rgba(142, 142, 147, 0.15)', 'label' => 'General'],
                            ];
                            $category = $categoryConfig[$template->category] ?? $categoryConfig['general'];
                            
                            // Check if popular (used 5+ times)
                            $isPopular = $template->usage_count >= 5;
                            
                            // Format cost
                            $formattedCost = $template->total_estimated_cost > 0 ? 
                                'Rp ' . number_format($template->total_estimated_cost, 0, ',', '.') : '-';
                        @endphp

                        <tr class="hover-lift transition-apple" style="cursor: pointer;" onclick="window.location='{{ route('permit-templates.show', $template) }}'">
                            <!-- Template Info -->
                            <td class="px-4 py-3">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3 flex-shrink-0" style="background-color: {{ $category['bg'] }};">
                                        <i class="fas {{ $category['icon'] }} text-lg" style="color: {{ $category['color'] }};"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="font-semibold text-sm text-dark-text-primary">{{ $template->name }}</div>
                                            @if($isPopular)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold" 
                                                      style="background-color: rgba(255, 59, 48, 0.15); color: rgba(255, 59, 48, 1);" 
                                                      title="Popular Template">
                                                    <i class="fas fa-fire mr-1"></i>Popular
                                                </span>
                                            @endif
                                            @if($template->is_public)
                                                <i class="fas fa-globe text-xs" style="color: rgba(0, 122, 255, 1);" title="Public Template"></i>
                                            @endif
                                        </div>
                                        @if($template->description)
                                            <div class="text-xs text-dark-text-secondary line-clamp-2">
                                                {{ Str::limit($template->description, 100) }}
                                            </div>
                                        @endif
                                        @if($template->goal_permit_type)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" 
                                                      style="background-color: rgba(175, 82, 222, 0.15); color: rgba(175, 82, 222, 1);">
                                                    <i class="fas fa-bullseye mr-1"></i>Goal: {{ $template->goal_permit_type }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Jenis Izin -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-semibold" 
                                      style="background-color: rgba(52, 199, 89, 0.15); color: rgba(52, 199, 89, 1);">
                                    <i class="fas fa-certificate mr-1.5"></i>
                                    {{ $template->items_count }}
                                </span>
                            </td>

                            <!-- Dependencies -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if($template->dependencies_count > 0)
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-semibold" 
                                          style="background-color: rgba(255, 149, 0, 0.15); color: rgba(255, 149, 0, 1);">
                                        <i class="fas fa-link mr-1.5"></i>
                                        {{ $template->dependencies_count }}
                                    </span>
                                @else
                                    <span class="text-sm text-dark-text-secondary">-</span>
                                @endif
                            </td>

                            <!-- Estimasi -->
                            <td class="px-6 py-3" style="min-width: 150px;">
                                <div class="text-sm">
                                    <div class="flex items-center mb-1 whitespace-nowrap">
                                        <i class="fas fa-clock text-xs mr-1.5" style="color: rgba(175, 82, 222, 1);"></i>
                                        <span class="text-dark-text-primary font-medium">{{ $template->total_estimated_days }} hari</span>
                                    </div>
                                    @if($template->total_estimated_cost > 0)
                                        <div class="flex items-center whitespace-nowrap">
                                            <i class="fas fa-wallet text-xs mr-1.5" style="color: rgba(52, 199, 89, 1);"></i>
                                            <span class="text-dark-text-secondary text-xs">{{ $formattedCost }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Penggunaan -->
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-sm font-semibold" 
                                      style="background-color: rgba(0, 122, 255, 0.15); color: rgba(0, 122, 255, 1);">
                                    <i class="fas fa-chart-line mr-1.5"></i>
                                    {{ $template->usage_count }}x
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2" onclick="event.stopPropagation();">
                                    <a href="{{ route('permit-templates.show', $template) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #0A84FF; background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#0A84FF'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(10, 132, 255, 0.1)'; this.style.color='#0A84FF'"
                                       title="View Details">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <button type="button"
                                            onclick="applyTemplate({{ $template->id }})" 
                                            class="p-2 rounded-apple transition-apple" 
                                            style="color: #34C759; background-color: rgba(52, 199, 89, 0.1); border: 1px solid rgba(52, 199, 89, 0.3);" 
                                            onmouseover="this.style.backgroundColor='#34C759'; this.style.color='#FFFFFF'" 
                                            onmouseout="this.style.backgroundColor='rgba(52, 199, 89, 0.1)'; this.style.color='#34C759'"
                                            title="Apply to Project">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                    <a href="{{ route('permit-templates.edit', $template) }}" 
                                       class="p-2 rounded-apple transition-apple" 
                                       style="color: #FF9F0A; background-color: rgba(255, 159, 10, 0.1); border: 1px solid rgba(255, 159, 10, 0.3);" 
                                       onmouseover="this.style.backgroundColor='#FF9F0A'; this.style.color='#FFFFFF'" 
                                       onmouseout="this.style.backgroundColor='rgba(255, 159, 10, 0.1)'; this.style.color='#FF9F0A'"
                                       title="Edit Template">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div class="flex flex-col items-center justify-center" style="color: rgba(235, 235, 245, 0.6);">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="text-sm font-medium">Tidak ada template ditemukan</p>
                                    <p class="text-xs mt-1">Buat template baru untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function applyTemplate(templateId) {
    // Redirect to projects with template parameter
    window.location.href = `/projects/create?template_id=${templateId}`;
}
</script>
@endpush
