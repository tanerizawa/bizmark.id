@extends('layouts.admin')

@section('title', 'Tests - ' . $vacancy->title)

@section('content')
<div class="max-w-7xl mx-auto space-y-5">
    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title, 'url' => route('admin.jobs.show', $vacancy->id)],
        ['label' => 'Tests']
    ]" />

    <!-- Header with Tabs -->
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 blur-3xl rounded-full absolute -top-10 -right-6" style="background: var(--neuro-primary); opacity: var(--opacity-bg-light);"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl md:text-xl font-bold text-white">{{ $vacancy->title }}</h1>
                    <p class="text-sm mt-1" style="color: rgba(235,235,245,0.7);">
                        Manage test assignments for this position
                    </p>
                </div>
                
                <button type="button" 
                        onclick="document.getElementById('assignTestModal').classList.remove('hidden')"
                        class="btn-primary-sm">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Assign Test
                </button>
            </div>

            <!-- Tab Navigation -->
            <x-job-tabs :vacancy="$vacancy" active-tab="tests" />
        </div>
    </section>

    <!-- Test Statistics -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total Assigned</p>
            <p class="text-lg font-bold text-white">{{ $stats['total'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Test sessions</p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">In Progress</p>
            <p class="text-lg font-bold text-white">{{ $stats['in_progress'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Active tests</p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Completed</p>
            <p class="text-lg font-bold text-white">{{ $stats['completed'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Finished tests</p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Pass Rate</p>
            <p class="text-lg font-bold text-white">{{ $stats['pass_rate'] }}%</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Success rate</p>
        </div>
    </section>

    <!-- Test Sessions Table -->
    <section class="card-elevated rounded-apple-xl p-5 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Test Sessions</h2>
            
            <!-- Filter -->
            <div class="flex items-center gap-3">
                <select class="input-apple-sm" onchange="window.location.href=this.value">
                    <option value="{{ route('admin.jobs.tests', $vacancy->id) }}" {{ !request('status') ? 'selected' : '' }}>
                        All Status
                    </option>
                    <option value="{{ route('admin.jobs.tests', ['id' => $vacancy->id, 'status' => 'pending']) }}" {{ request('status') === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="{{ route('admin.jobs.tests', ['id' => $vacancy->id, 'status' => 'in-progress']) }}" {{ request('status') === 'in-progress' ? 'selected' : '' }}>
                        In Progress
                    </option>
                    <option value="{{ route('admin.jobs.tests', ['id' => $vacancy->id, 'status' => 'completed']) }}" {{ request('status') === 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>
                </select>
            </div>
        </div>

        @if($testSessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="table-apple">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Test Type</th>
                            <th>Assigned Date</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testSessions as $session)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, var(--neuro-primary), var(--neuro-accent));">
                                            <span class="text-white font-semibold text-sm">
                                                {{ substr($session->jobApplication->full_name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-white">{{ $session->jobApplication->full_name }}</div>
                                            <div class="text-sm" style="color: var(--text-dark-tertiary);">{{ $session->jobApplication->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-apple-blue">
                                        {{ $session->testTemplate->title }}
                                    </span>
                                </td>
                                <td style="color: var(--text-dark-secondary);">
                                    {{ $session->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge-apple badge-{{ $session->getStatusColor() }}">
                                        {{ $session->getStatusLabel() }}
                                    </span>
                                </td>
                                <td class="text-white font-medium">
                                    @if($session->score)
                                        {{ number_format($session->score, 1) }}%
                                    @else
                                        <span style="color: var(--text-dark-tertiary);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($session->passed !== null)
                                        @if($session->passed)
                                            <span class="badge-apple badge-green">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Passed
                                            </span>
                                        @else
                                            <span class="badge-apple badge-red">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Failed
                                            </span>
                                        @endif
                                    @else
                                        <span style="color: var(--text-dark-tertiary);">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if($session->status === 'completed')
                                            <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" 
                                               class="btn-icon-sm" 
                                               title="View Results">
                                                <i class="fas fa-chart-bar"></i>
                                            </a>
                                        @else
                                            <button class="btn-icon-sm opacity-50 cursor-not-allowed" 
                                                    title="Results not available yet"
                                                    disabled>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                        
                                        @if($session->status === 'completed' && $session->requires_manual_review)
                                            <a href="{{ route('admin.recruitment.tests.sessions.evaluate', $session) }}" 
                                               class="btn-icon-sm" 
                                               title="Evaluate">
                                                <i class="fas fa-star"></i>
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('admin.recruitment.pipeline.show', $session->jobApplication->id) }}" 
                                           class="btn-icon-sm" 
                                           title="View Pipeline">
                                            <i class="fas fa-stream"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $testSessions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-clipboard-check text-4xl mb-4" style="color: var(--text-dark-tertiary);"></i>
                <p class="text-lg mb-2" style="color: var(--text-dark-secondary);">No tests assigned yet</p>
                <p class="text-sm mb-6" style="color: var(--text-dark-tertiary);">Start by assigning a test to candidates for this position</p>
                <button type="button" 
                        onclick="document.getElementById('assignTestModal').classList.remove('hidden')"
                        class="btn-apple-blue">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Assign Test
                </button>
            </div>
        @endif
    </section>
</div>

<!-- Assign Test Modal -->
<div id="assignTestModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-white">Assign Test to Candidate</h2>
            <button type="button" 
                    onclick="document.getElementById('assignTestModal').classList.add('hidden')"
                    class="text-white/60 hover:text-white transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.recruitment.tests.assign') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Select Candidate -->
            <div>
                <label class="block text-sm font-medium text-white mb-2">Select Candidate</label>
                <select name="job_application_id" class="input-apple" required>
                    <option value="">Choose a candidate...</option>
                    @foreach($candidates as $candidate)
                        <option value="{{ $candidate->id }}">
                            {{ $candidate->full_name }} - {{ $candidate->email }}
                            @if($candidate->recruitment_stage)
                                (Stage: {{ $candidate->recruitment_stage }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">
                    Only showing candidates who applied for this position
                </p>
            </div>

            <!-- Select Test Template -->
            <div>
                <label class="block text-sm font-medium text-white mb-2">Select Test Template</label>
                <select name="test_template_id" class="input-apple" required>
                    <option value="">Choose a test...</option>
                    @foreach($testTemplates as $template)
                        <option value="{{ $template->id }}">
                            {{ $template->title }} 
                            ({{ $template->test_type }} - {{ $template->duration_minutes }} min)
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Expiry Date -->
            <div>
                <label class="block text-sm font-medium text-white mb-2">Test Expiry Date</label>
                <input type="datetime-local" 
                       name="expires_at" 
                       class="input-apple"
                       value="{{ now()->addDays(7)->format('Y-m-d\TH:i') }}"
                       required>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">
                    Candidate must complete the test before this date
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Assign Test & Send Email
                </button>
                <button type="button" 
                        onclick="document.getElementById('assignTestModal').classList.add('hidden')"
                        class="btn-secondary flex-1">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('assignTestModal').classList.add('hidden');
    }
});

// Close modal on backdrop click
document.getElementById('assignTestModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endsection
