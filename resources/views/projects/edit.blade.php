@extends('layouts.admin')

@section('title', 'Edit Proyek - ' . $project->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('projects.show', $project) }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Proyek</h1>
            <p class="mt-1 text-dark-text-secondary">{{ $project->name }}</p>
        </div>
    </div>

    <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-white">
                <i class="fas fa-info-circle mr-2 text-apple-blue-dark"></i>Informasi Proyek
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Nama Proyek <span class="text-apple-red-dark">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $project->name) }}" required
                           class="input-dark w-full px-3 py-2 rounded-md @error('name') ring-2 ring-apple-red @enderror"
                           placeholder="Contoh: Perizinan IMB Gedung Perkantoran">
                    @error('name')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="client_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Klien <span class="text-apple-red-dark">*</span>
                    </label>
                    <select id="client_id" name="client_id" required
                            class="input-dark w-full px-3 py-2 rounded-md @error('client_id') ring-2 ring-apple-red @enderror">
                        <option value="">Pilih Klien</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                {{ $client->company_name ?? $client->name }}
                                @if($client->company_name && $client->name != $client->company_name)
                                    ({{ $client->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-dark-text-tertiary">
                        <i class="fas fa-info-circle mr-1"></i>
                        Belum ada di list? 
                        <a href="{{ route('clients.create') }}" target="_blank" class="text-apple-blue hover:text-apple-blue-dark">
                            Tambah Klien Baru
                        </a>
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Deskripsi Proyek <span class="text-apple-red-dark">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required
                              class="input-dark w-full px-3 py-2 rounded-md @error('description') ring-2 ring-apple-red @enderror"
                              placeholder="Jelaskan detail proyek perizinan...">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="institution_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Institusi Tujuan <span class="text-apple-red-dark">*</span>
                    </label>
                    <select id="institution_id" name="institution_id" required
                            class="input-dark w-full px-3 py-2 rounded-md @error('institution_id') ring-2 ring-apple-red @enderror">
                        <option value="">Pilih Institusi</option>
                        @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}" 
                                {{ old('institution_id', $project->institution_id) == $institution->id ? 'selected' : '' }}>
                            {{ $institution->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('institution_id')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Status <span class="text-apple-red-dark">*</span>
                    </label>
                    <select id="status_id" name="status_id" required
                            class="input-dark w-full px-3 py-2 rounded-md @error('status_id') ring-2 ring-apple-red @enderror">
                        <option value="">Pilih Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->id }}" 
                                {{ old('status_id', $project->status_id) == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('status_id')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Project Schedule -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-white">
                <i class="fas fa-calendar mr-2 text-apple-blue-dark"></i>Jadwal Proyek
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Tanggal Mulai <span class="text-apple-red-dark">*</span>
                    </label>
                    <input type="date" id="start_date" name="start_date" 
                           value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" required
                           class="input-dark w-full px-3 py-2 rounded-md @error('start_date') ring-2 ring-apple-red @enderror">
                    @error('start_date')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="deadline" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Target Selesai
                    </label>
                    <input type="date" id="deadline" name="deadline" 
                           value="{{ old('deadline', $project->deadline ? $project->deadline->format('Y-m-d') : '') }}"
                           class="input-dark w-full px-3 py-2 rounded-md @error('deadline') ring-2 ring-apple-red @enderror">
                    @error('deadline')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="completed_at" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Tanggal Selesai Aktual
                        <span class="text-xs text-dark-text-tertiary">(opsional)</span>
                    </label>
                    <input type="date" id="completed_at" name="completed_at" 
                           value="{{ old('completed_at', $project->completed_at ? $project->completed_at->format('Y-m-d') : ($project->actual_completion_date ? $project->actual_completion_date->format('Y-m-d') : '')) }}"
                           class="input-dark w-full px-3 py-2 rounded-md @error('completed_at') ring-2 ring-apple-red @enderror">
                    @error('completed_at')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                    
                    @if(($project->completed_at || $project->actual_completion_date) && $project->deadline)
                        @php
                            $completedDate = $project->completed_at ?? $project->actual_completion_date;
                            $status = $project->getCompletionStatus();
                            $statusColors = [
                                'on-time' => 'rgba(52, 199, 89, 1)',
                                'early' => 'rgba(10, 132, 255, 1)',
                                'late' => 'rgba(255, 59, 48, 1)'
                            ];
                            $statusIcons = [
                                'on-time' => 'fa-check-circle',
                                'early' => 'fa-bolt',
                                'late' => 'fa-exclamation-triangle'
                            ];
                        @endphp
                        <p class="mt-2 text-xs flex items-center gap-1" style="color: {{ $statusColors[$status] ?? 'rgba(235,235,245,0.6)' }}">
                            <i class="fas {{ $statusIcons[$status] ?? 'fa-info-circle' }}"></i>
                            {{ $project->getCompletionStatusMessage() }}
                        </p>
                    @endif
                </div>

                <div>
                    <label for="progress_percentage" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Progress (%)
                    </label>
                    <input type="number" id="progress_percentage" name="progress_percentage" 
                           value="{{ old('progress_percentage', $project->progress_percentage) }}" min="0" max="100"
                           class="input-dark w-full px-3 py-2 rounded-md @error('progress_percentage') ring-2 ring-apple-red @enderror">
                    @error('progress_percentage')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Completion Notes -->
            @if($project->completed_at || old('completed_at'))
            <div class="mt-6">
                <label for="completion_notes" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                    Catatan Penyelesaian
                    <span class="text-xs text-dark-text-tertiary">(opsional - muncul saat ada tanggal selesai)</span>
                </label>
                <textarea id="completion_notes" name="completion_notes" rows="3"
                          class="input-dark w-full px-3 py-2 rounded-md @error('completion_notes') ring-2 ring-apple-red @enderror"
                          placeholder="Contoh: Semua dokumen izin sudah diterbitkan. Client puas dengan hasil pekerjaan.">{{ old('completion_notes', $project->completion_notes) }}</textarea>
                @error('completion_notes')
                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        <!-- Financial Information -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-white">
                <i class="fas fa-dollar-sign mr-2 text-apple-blue-dark"></i>Informasi Keuangan
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="budget" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Budget Proyek
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-dark-text-secondary">Rp</span>
                        <input type="number" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" step="0.01"
                               class="input-dark w-full pl-10 pr-3 py-2 rounded-md @error('budget') ring-2 ring-apple-red @enderror"
                               placeholder="0">
                    </div>
                    @error('budget')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="actual_cost" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                        Biaya Aktual
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-dark-text-secondary">Rp</span>
                        <input type="number" id="actual_cost" name="actual_cost" value="{{ old('actual_cost', $project->actual_cost) }}" step="0.01"
                               class="input-dark w-full pl-10 pr-3 py-2 rounded-md @error('actual_cost') ring-2 ring-apple-red @enderror"
                               placeholder="0">
                    </div>
                    @error('actual_cost')
                    <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-white">
                <i class="fas fa-sticky-note mr-2 text-apple-blue-dark"></i>Catatan Tambahan
            </h3>
            
            <div>
                <label for="notes" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                    Catatan
                </label>
                <textarea id="notes" name="notes" rows="4"
                          class="input-dark w-full px-3 py-2 rounded-md @error('notes') ring-2 ring-apple-red @enderror"
                          placeholder="Catatan khusus, persyaratan, atau informasi penting lainnya...">{{ old('notes', $project->notes) }}</textarea>
                @error('notes')
                <p class="mt-1 text-sm text-apple-red-dark">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status Change Log (if status changes) -->
        @if($project->logs->count() > 0)
        <div class="card-elevated rounded-apple-lg p-6">
            <h3 class="text-lg font-semibold mb-4 text-white">
                <i class="fas fa-history mr-2 text-apple-blue-dark"></i>Riwayat Perubahan
            </h3>
            
            <div class="space-y-3 max-h-60 overflow-y-auto">
                @foreach($project->logs->take(5) as $log)
                <div class="flex items-start space-x-3 p-3 rounded-lg bg-[rgba(58,58,60,0.6)]">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-sm mt-1 text-dark-text-secondary"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-dark-text-primary/80">{{ $log->description }}</p>
                        <p class="text-xs mt-1 text-dark-text-secondary">{{ $log->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                @endforeach
                
                @if($project->logs->count() > 5)
                <div class="text-center">
                    <a href="{{ route('projects.show', $project) }}" class="text-apple-blue-dark hover:text-apple-blue text-sm">
                        Lihat semua riwayat
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="card-elevated rounded-apple-lg p-6">
            <div class="flex justify-end space-x-4">
                <a href="{{ route('projects.show', $project) }}" 
                   class="px-6 py-3 rounded-lg font-medium transition-colors bg-[rgba(58,58,60,0.8)] text-dark-text-primary/80">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" 
                        class="btn-primary px-6 py-3 rounded-lg font-medium">
                    <i class="fas fa-save mr-2"></i>Update Proyek
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Track status changes to show warning
    const originalStatus = '{{ $project->status_id }}';
    const statusSelect = document.getElementById('status_id');
    
    statusSelect.addEventListener('change', function() {
        if (this.value !== originalStatus && this.value !== '') {
            // Show confirmation for status changes
            if (!confirm('Anda akan mengubah status proyek. Perubahan ini akan dicatat dalam log. Lanjutkan?')) {
                this.value = originalStatus;
            }
        }
    });

    // Progress validation
    const progressInput = document.getElementById('progress_percentage');
    const statusField = document.getElementById('status_id');
    
    progressInput.addEventListener('change', function() {
        const progress = parseInt(this.value);
        const currentStatus = statusField.value;
        
        // Auto-suggest status based on progress
        if (progress === 100) {
            const completedOption = statusField.querySelector('option[value]');
            // Find completed status (adjust based on your status IDs)
            for (let option of statusField.options) {
                if (option.text.toLowerCase().includes('selesai') || option.text.toLowerCase().includes('completed')) {
                    if (confirm('Progress 100%, apakah ingin mengubah status menjadi "' + option.text + '"?')) {
                        statusField.value = option.value;
                    }
                    break;
                }
            }
        }
    });
</script>
@endpush
@endsection