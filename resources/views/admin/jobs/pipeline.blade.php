@extends('layouts.admin')

@section('title', $vacancy->title . ' - Pipeline')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title, 'url' => route('admin.jobs.show', $vacancy->id)],
        ['label' => 'Pipeline']
    ]" />

    {{-- Header with Tabs --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 blur-3xl rounded-full absolute -top-10 -right-6" style="background: var(--neuro-accent); opacity: var(--opacity-bg-light);"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl md:text-xl font-bold text-white">{{ $vacancy->title }}</h1>
                    <p class="text-sm mt-1" style="color: var(--text-dark-secondary); opacity: var(--opacity-text-medium);">
                        Recruitment Pipeline Overview
                    </p>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <x-job-tabs :vacancy="$vacancy" active-tab="pipeline" />
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: var(--neuro-success); opacity: var(--opacity-bg-light); border: 1px solid var(--neuro-success); opacity: var(--opacity-border-medium); color: var(--neuro-success);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Pipeline Statistics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">In Pipeline</p>
            <p class="text-lg font-bold text-white">{{ $stats['total_in_pipeline'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Active candidates</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Screening</p>
            <p class="text-lg font-bold text-white">{{ $stats['screening'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">In progress</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Interview</p>
            <p class="text-lg font-bold text-white">{{ $stats['interview'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Scheduled</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Passed</p>
            <p class="text-lg font-bold text-white">{{ $stats['passed'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Successful</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.jobs.pipeline', $vacancy->id) }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search candidates..." 
                       class="input-apple w-full">
            </div>
            
            <select name="stage" class="input-apple min-w-[150px]">
                <option value="">All Stages</option>
                <option value="screening" {{ request('stage') === 'screening' ? 'selected' : '' }}>Screening</option>
                <option value="testing" {{ request('stage') === 'testing' ? 'selected' : '' }}>Testing</option>
                <option value="interview" {{ request('stage') === 'interview' ? 'selected' : '' }}>Interview</option>
                <option value="final" {{ request('stage') === 'final' ? 'selected' : '' }}>Final</option>
            </select>

            <button type="submit" class="btn-secondary-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            @if(request('search') || request('stage'))
                <a href="{{ route('admin.jobs.pipeline', $vacancy->id) }}" class="btn-secondary-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </section>

    {{-- Pipeline Table --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        @if($applications->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: var(--light-separator); opacity: var(--opacity-bg-light); border-bottom: 1px solid var(--dark-separator); opacity: var(--opacity-border-medium);">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-medium);">
                                Candidate
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-medium);">
                                Current Stage
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-medium);">
                                Progress
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-medium);">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-medium);">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dark-separator); opacity: var(--opacity-border-light);">
                        @foreach($applications as $application)
                            @php
                                $currentStage = $application->recruitmentStages->where('status', 'in-progress')->first();
                                $completedStages = $application->recruitmentStages->where('status', 'passed')->count();
                                $totalStages = $application->recruitmentStages->count();
                                $progress = $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
                                
                                $stageColors = [
                                    'screening' => 'var(--neuro-warning)',
                                    'testing' => 'var(--neuro-info)',
                                    'interview' => 'var(--neuro-accent)',
                                    'final' => 'var(--neuro-success)',
                                ];
                            @endphp
                            <tr class="hover:bg-white/5 transition-apple">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" 
                                             style="background: var(--neuro-primary); opacity: var(--opacity-bg-medium);">
                                            {{ substr($application->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $application->full_name }}</p>
                                            <p class="text-xs" style="color: var(--text-dark-secondary); opacity: var(--opacity-text-medium);">
                                                {{ $application->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($currentStage)
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                              style="background: {{ $stageColors[$currentStage->stage_name] ?? 'var(--text-dark-tertiary)' }}; opacity: var(--opacity-bg-light); color: {{ $stageColors[$currentStage->stage_name] ?? 'var(--text-dark-tertiary)' }};">
                                            {{ ucfirst($currentStage->stage_name) }}
                                        </span>
                                    @else
                                        <span class="text-xs" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-light);">Not started</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 rounded-full overflow-hidden" style="background: var(--light-separator); opacity: var(--opacity-bg-medium);">
                                            <div class="h-full rounded-full transition-all" 
                                                 style="width: {{ $progress }}%; background: var(--neuro-primary);"></div>
                                        </div>
                                        <span class="text-xs font-semibold" style="color: var(--text-dark-secondary); opacity: var(--opacity-text-strong);">
                                            {{ $progress }}%
                                        </span>
                                    </div>
                                    <p class="text-xs mt-1" style="color: var(--text-dark-tertiary); opacity: var(--opacity-text-light);">
                                        {{ $completedStages }}/{{ $totalStages }} stages
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'pending' => ['bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
                                            'in-progress' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)'],
                                            'passed' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)'],
                                            'failed' => ['bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
                                        ];
                                        $appStatus = $application->status;
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                          style="background: {{ $statusColors[$appStatus]['bg'] ?? 'rgba(142,142,147,0.2)' }}; color: {{ $statusColors[$appStatus]['text'] ?? 'rgba(142,142,147,1)' }};">
                                        {{ ucfirst($appStatus) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" class="btn-secondary-sm">
                                        <i class="fas fa-stream mr-1"></i>Pipeline
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3" style="border-top: 1px solid rgba(235,235,245,0.1);">
                {{ $applications->links() }}
            </div>
        @else
            <div class="p-8 text-center" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-stream text-4xl mb-3"></i>
                <p class="text-sm">No candidates in pipeline</p>
                @if(request('search') || request('stage'))
                    <a href="{{ route('admin.jobs.pipeline', $vacancy->id) }}" class="text-xs text-apple-blue hover:underline mt-2 inline-block">
                        Clear filters
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
