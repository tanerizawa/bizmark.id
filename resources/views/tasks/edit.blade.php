@extends('layouts.admin')

@section('title', 'Edit Tugas: ' . $task->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4 mb-4">
            <a href="{{ route('tasks.show', $task) }}" 
               class="text-apple-blue-dark hover:text-apple-blue">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-white">Edit Tugas</h1>
        </div>
        <p class="text-dark-text-secondary">{{ $task->title }}</p>
    </div>

    <!-- Form -->
    <div class="card-elevated rounded-apple-lg">
        <form action="{{ route('tasks.update', $task) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="pb-6 border-b border-white/10">
                <h3 class="text-lg font-medium mb-4 text-white">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Project -->
                    <div class="md:col-span-2">
                        <label for="project_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Proyek <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="project_id" 
                                id="project_id" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('project_id') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Proyek</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" 
                                        {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} - {{ $project->client_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Judul Tugas <span class="text-apple-red-dark">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               id="title"
                               value="{{ old('title', $task->title) }}"
                               required
                               class="input-dark w-full px-3 py-2 rounded-md @error('title') ring-2 ring-apple-red @enderror">
                        @error('title')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Deskripsi</label>
                        <textarea name="description" 
                                  id="description"
                                  rows="4"
                                  class="input-dark w-full px-3 py-2 rounded-md @error('description') ring-2 ring-apple-red @enderror">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SOP Notes -->
                    <div class="md:col-span-2">
                        <label for="sop_notes" class="block text-sm font-medium mb-2 text-dark-text-primary/80">SOP / Checklist</label>
                        <textarea name="sop_notes" 
                                  id="sop_notes"
                                  rows="3"
                                  class="input-dark w-full px-3 py-2 rounded-md @error('sop_notes') ring-2 ring-apple-red @enderror">{{ old('sop_notes', $task->sop_notes) }}</textarea>
                        @error('sop_notes')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Assignment & Timeline -->
            <div class="pb-6 border-b border-white/10">
                <h3 class="text-lg font-medium mb-4 text-white">Penugasan & Timeline</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Assigned User -->
                    <div>
                        <label for="assigned_user_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Ditugaskan Kepada</label>
                        <select name="assigned_user_id" 
                                id="assigned_user_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('assigned_user_id') ring-2 ring-apple-red @enderror">
                            <option value="">Belum ditugaskan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $task->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_user_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Tanggal Jatuh Tempo</label>
                        <input type="date" 
                               name="due_date" 
                               id="due_date"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                               class="input-dark w-full px-3 py-2 rounded-md @error('due_date') ring-2 ring-apple-red @enderror">
                        @error('due_date')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estimated Hours -->
                    <div>
                        <label for="estimated_hours" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Estimasi Jam Kerja</label>
                        <input type="number" 
                               name="estimated_hours" 
                               id="estimated_hours"
                               value="{{ old('estimated_hours', $task->estimated_hours) }}"
                               min="1"
                               class="input-dark w-full px-3 py-2 rounded-md @error('estimated_hours') ring-2 ring-apple-red @enderror">
                        @error('estimated_hours')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actual Hours -->
                    <div>
                        <label for="actual_hours" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Jam Kerja Aktual</label>
                        <input type="number" 
                               name="actual_hours" 
                               id="actual_hours"
                               value="{{ old('actual_hours', $task->actual_hours) }}"
                               min="0"
                               class="input-dark w-full px-3 py-2 rounded-md @error('actual_hours') ring-2 ring-apple-red @enderror">
                        @error('actual_hours')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Institution -->
                    <div>
                        <label for="institution_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Institusi Terkait</label>
                        <select name="institution_id" 
                                id="institution_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('institution_id') ring-2 ring-apple-red @enderror">
                            <option value="">Pilih Institusi</option>
                            @foreach($institutions as $institution)
                                <option value="{{ $institution->id }}" {{ old('institution_id', $task->institution_id) == $institution->id ? 'selected' : '' }}>
                                    {{ $institution->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dependencies -->
                    <div>
                        <label for="depends_on_task_id" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Bergantung pada Tugas</label>
                        <select name="depends_on_task_id" 
                                id="depends_on_task_id"
                                class="input-dark w-full px-3 py-2 rounded-md @error('depends_on_task_id') ring-2 ring-apple-red @enderror">
                            <option value="">Tidak ada</option>
                            @foreach($availableTasks as $availableTask)
                                <option value="{{ $availableTask->id }}" {{ old('depends_on_task_id', $task->depends_on_task_id) == $availableTask->id ? 'selected' : '' }}>
                                    {{ $availableTask->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('depends_on_task_id')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status & Priority -->
            <div class="pb-6 border-b border-white/10">
                <h3 class="text-lg font-medium mb-4 text-white">Status & Prioritas</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Status <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('status') ring-2 ring-apple-red @enderror">
                            <option value="todo" {{ old('status', $task->status) == 'todo' ? 'selected' : '' }}>To Do</option>
                            <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="done" {{ old('status', $task->status) == 'done' ? 'selected' : '' }}>Done</option>
                            <option value="blocked" {{ old('status', $task->status) == 'blocked' ? 'selected' : '' }}>Blocked</option>
                        </select>
                        @error('status')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium mb-2 text-dark-text-primary/80">
                            Prioritas <span class="text-apple-red-dark">*</span>
                        </label>
                        <select name="priority" 
                                id="priority" 
                                required
                                class="input-dark w-full px-3 py-2 rounded-md @error('priority') ring-2 ring-apple-red @enderror">
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority', $task->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $task->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Urutan</label>
                        <input type="number" 
                               name="sort_order" 
                               id="sort_order"
                               value="{{ old('sort_order', $task->sort_order) }}"
                               min="0"
                               class="input-dark w-full px-3 py-2 rounded-md @error('sort_order') ring-2 ring-apple-red @enderror">
                        @error('sort_order')
                            <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Completion Notes -->
            @if($task->status === 'done' || $task->completion_notes)
            <div>
                <h3 class="text-lg font-medium mb-4 text-white">Catatan Penyelesaian</h3>
                <div>
                    <label for="completion_notes" class="block text-sm font-medium mb-2 text-dark-text-primary/80">Catatan</label>
                    <textarea name="completion_notes" 
                              id="completion_notes"
                              rows="4"
                              placeholder="Catatan tentang penyelesaian tugas..."
                              class="input-dark w-full px-3 py-2 rounded-md @error('completion_notes') ring-2 ring-apple-red @enderror">{{ old('completion_notes', $task->completion_notes) }}</textarea>
                    @error('completion_notes')
                        <p class="text-apple-red-dark text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-white/10">
                <a href="{{ route('tasks.show', $task) }}"
                   class="px-6 py-2 rounded-md font-medium transition-colors border border-white/10 text-dark-text-primary/80 bg-[rgba(58,58,60,0.6)]">
                    Batal
                </a>
                <button type="submit" 
                        class="btn-primary px-6 py-2 rounded-md font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection