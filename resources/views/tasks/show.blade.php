@extends('layouts.admin')

@section('title', $task->title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('tasks.index') }}" 
                   class="text-apple-blue-dark hover:text-apple-blue">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $task->title }}</h1>
                    <p class="mt-1 text-dark-text-secondary">
                        Proyek: <a href="{{ route('projects.show', $task->project) }}" class="text-apple-blue-dark hover:text-apple-blue">{{ $task->project->name }}</a>
                    </p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('tasks.edit', $task) }}" 
                   class="btn-warning px-4 py-2 rounded-apple-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('tasks.destroy', $task) }}"
                      method="POST"
                      class="inline"
                      x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menghapus tugas ini?')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn-danger px-4 py-2 rounded-apple-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Task Details -->
            <div class="card-elevated rounded-apple-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-white">Detail Tugas</h2>
                    <div class="flex items-center space-x-3">
                        <!-- Status Badge -->
                        @php
                            $statusColors = [
                                'todo' => 'text-dark-text-secondary',
                                'in_progress' => 'text-apple-blue',
                                'done' => 'text-apple-green',
                                'blocked' => 'text-apple-red'
                            ];
                            $statusBgColors = [
                                'todo' => 'bg-dark-text-tertiary/15',
                                'in_progress' => 'bg-apple-blue/15',
                                'done' => 'bg-apple-green/15',
                                'blocked' => 'bg-apple-red/15'
                            ];
                            $statusLabels = [
                                'todo' => 'To Do',
                                'in_progress' => 'In Progress',
                                'done' => 'Done',
                                'blocked' => 'Blocked'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusBgColors[$task->status] }} {{ $statusColors[$task->status] }}">
                            {{ $statusLabels[$task->status] }}
                        </span>

                        <!-- Priority Badge -->
                        @php
                            $priorityColors = [
                                'low' => 'text-dark-text-secondary',
                                'normal' => 'text-apple-blue',
                                'high' => 'text-apple-orange',
                                'urgent' => 'text-apple-red'
                            ];
                            $priorityBgColors = [
                                'low' => 'bg-dark-text-tertiary/15',
                                'normal' => 'bg-apple-blue/15',
                                'high' => 'bg-apple-orange/15',
                                'urgent' => 'bg-apple-red/15'
                            ];
                            $priorityLabels = [
                                'low' => 'Low Priority',
                                'normal' => 'Normal Priority',
                                'high' => 'High Priority',
                                'urgent' => 'Urgent'
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $priorityBgColors[$task->priority] }} {{ $priorityColors[$task->priority] }}">
                            {{ $priorityLabels[$task->priority] }}
                        </span>
                    </div>
                </div>

                <!-- Description -->
                @if($task->description)
                <div class="mb-6">
                    <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">Deskripsi</h3>
                    <div class="prose max-w-none text-dark-text-secondary">
                        {!! nl2br(e($task->description)) !!}
                    </div>
                </div>
                @endif

                <!-- SOP Notes -->
                @if($task->sop_notes)
                <div class="mb-6">
                    <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">SOP / Checklist</h3>
                    <div class="rounded-md p-4 bg-[rgba(58,58,60,0.5)]">
                        <div class="prose max-w-none text-dark-text-secondary">
                            {!! nl2br(e($task->sop_notes)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Completion Notes -->
                @if($task->completion_notes)
                <div class="mb-6">
                    <h3 class="text-sm font-medium mb-2 text-dark-text-primary/80">Catatan Penyelesaian</h3>
                    <div class="rounded-md p-4 bg-apple-green/20">
                        <div class="prose max-w-none text-dark-text-secondary">
                            {!! nl2br(e($task->completion_notes)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Status Update -->
                <div class="pt-6 border-t border-white/10">
                    <h3 class="text-sm font-medium mb-3 text-dark-text-primary/80">Update Status</h3>
                    <div class="flex space-x-2">
                        @foreach(['todo', 'in_progress', 'done', 'blocked'] as $status)
                            <button onclick="updateStatus('{{ $status }}')"
                                    @if($task->status === $status)
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-colors {{ $statusBgColors[$status] }} {{ $statusColors[$status] }}"
                                    @else
                                        class="px-3 py-1 rounded-md text-sm font-medium transition-colors bg-[rgba(58,58,60,0.5)] text-dark-text-secondary"
                                    @endif>
                                {{ $statusLabels[$status] }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Related Tasks -->
            @if($relatedTasks->count() > 0)
            <div class="card-elevated rounded-apple-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-white">Tugas Terkait</h2>
                <div class="space-y-3">
                    @foreach($relatedTasks as $relatedTask)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-[rgba(58,58,60,0.5)]">
                        <div>
                            <h3 class="font-medium">
                                <a href="{{ route('tasks.show', $relatedTask) }}" class="text-apple-blue-dark hover:text-apple-blue">
                                    {{ $relatedTask->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-dark-text-secondary">{{ $relatedTask->assignedUser->name ?? 'Belum ditugaskan' }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusBgColors[$relatedTask->status] }} {{ $statusColors[$relatedTask->status] }}">
                            {{ $statusLabels[$relatedTask->status] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Task Info -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Informasi Tugas</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Ditugaskan Kepada</dt>
                        <dd class="text-sm text-dark-text-primary/80">
                            {{ $task->assignedUser->name ?? 'Belum ditugaskan' }}
                        </dd>
                    </div>
                    
                    @if($task->due_date)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Tanggal Jatuh Tempo</dt>
                        <dd class="text-sm {{ $task->due_date->isPast() && $task->status !== 'done' ? 'text-apple-red-dark font-medium' : 'text-dark-text-primary/80' }}">
                            {{ $task->due_date->format('d M Y') }}
                            @if($task->due_date->isPast() && $task->status !== 'done')
                                <span class="text-xs text-apple-red-dark block">Terlambat {{ $task->due_date->diffForHumans() }}</span>
                            @endif
                        </dd>
                    </div>
                    @endif

                    @if($task->institution)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Institusi</dt>
                        <dd class="text-sm text-dark-text-primary/80">{{ $task->institution->name }}</dd>
                    </div>
                    @endif

                    @if($task->estimated_hours)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Estimasi Waktu</dt>
                        <dd class="text-sm text-dark-text-primary/80">{{ $task->estimated_hours }} jam</dd>
                    </div>
                    @endif

                    @if($task->actual_hours)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Waktu Aktual</dt>
                        <dd class="text-sm text-dark-text-primary/80">{{ $task->actual_hours }} jam</dd>
                    </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Dibuat</dt>
                        <dd class="text-sm text-dark-text-primary/80">{{ $task->created_at->format('d M Y H:i') }}</dd>
                    </div>

                    @if($task->started_at)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Dimulai</dt>
                        <dd class="text-sm text-dark-text-primary/80">
                            {{ is_string($task->started_at) ? \Carbon\Carbon::parse($task->started_at)->format('d M Y H:i') : $task->started_at->format('d M Y H:i') }}
                        </dd>
                    </div>
                    @endif

                    @if($task->completed_at)
                    <div>
                        <dt class="text-sm font-medium text-dark-text-secondary">Selesai</dt>
                        <dd class="text-sm text-dark-text-primary/80">
                            {{ is_string($task->completed_at) ? \Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') : $task->completed_at->format('d M Y H:i') }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Dependent Tasks -->
            @if($dependentTasks->count() > 0)
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Tugas yang Bergantung</h3>
                <div class="space-y-2">
                    @foreach($dependentTasks as $dependentTask)
                    <div class="p-2 rounded bg-[rgba(58,58,60,0.5)]">
                        <a href="{{ route('tasks.show', $dependentTask) }}" class="text-sm text-apple-blue-dark hover:text-apple-blue">
                            {{ $dependentTask->title }}
                        </a>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-2 {{ $statusBgColors[$dependentTask->status] }} {{ $statusColors[$dependentTask->status] }}">
                            {{ $statusLabels[$dependentTask->status] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card-elevated rounded-apple-lg p-6">
                <h3 class="text-lg font-semibold mb-4 text-white">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('tasks.edit', $task) }}" 
                       class="btn-primary w-full px-4 py-2 rounded-md font-medium transition-colors text-center block">
                        <i class="fas fa-edit mr-2"></i>Edit Tugas
                    </a>
                    <a href="{{ route('projects.show', $task->project) }}" 
                       class="btn-success w-full px-4 py-2 rounded-md font-medium transition-colors text-center block">
                        <i class="fas fa-project-diagram mr-2"></i>Lihat Proyek
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(status) {
    fetch(`{{ route('tasks.update-status', $task) }}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Gagal memperbarui status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}
</script>
@endsection