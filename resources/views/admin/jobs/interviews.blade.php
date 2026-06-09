@extends('layouts.admin')

@section('title', $vacancy->title . ' - Interviews')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">
    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Jobs', 'url' => route('admin.jobs.index')],
        ['label' => $vacancy->title, 'url' => route('admin.jobs.show', $vacancy->id)],
        ['label' => 'Interviews']
    ]" />

    {{-- Header with Tabs --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-60 h-60 bg-apple-green opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
        </div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl md:text-xl font-bold text-white">{{ $vacancy->title }}</h1>
                    <p class="text-sm mt-1" style="color: rgba(235,235,245,0.7);">
                        Interview Schedule Management
                    </p>
                </div>
                <a href="{{ route('admin.recruitment.interviews.create', ['vacancy_id' => $vacancy->id]) }}" class="btn-primary-sm">
                    <i class="fas fa-calendar-plus mr-2"></i>Schedule Interview
                </a>
            </div>

            {{-- Tab Navigation --}}
            <x-job-tabs :vacancy="$vacancy" active-tab="interviews" />
        </div>
    </section>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Interview Statistics --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total</p>
            <p class="text-lg font-bold text-white">{{ $stats['total'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">All interviews</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(255,214,10,0.9);">Scheduled</p>
            <p class="text-lg font-bold text-white">{{ $stats['scheduled'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Awaiting interview</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Completed</p>
            <p class="text-lg font-bold text-white">{{ $stats['completed'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Finished</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest" style="color: rgba(175,82,222,0.9);">Upcoming</p>
            <p class="text-lg font-bold text-white">{{ $stats['upcoming'] }}</p>
            <p class="text-xs" style="color: rgba(235,235,245,0.6);">Next 7 days</p>
        </div>
    </section>

    {{-- Filters --}}
    <section class="card-elevated rounded-apple-lg p-4">
        <form method="GET" action="{{ route('admin.jobs.interviews', $vacancy->id) }}" class="flex flex-wrap gap-3">
            <select name="status" class="input-apple min-w-[150px]">
                <option value="">All Status</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="rescheduled" {{ request('status') === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
            </select>

            <select name="interview_type" class="input-apple min-w-[150px]">
                <option value="">All Types</option>
                <option value="preliminary" {{ request('interview_type') === 'preliminary' ? 'selected' : '' }}>Preliminary</option>
                <option value="technical" {{ request('interview_type') === 'technical' ? 'selected' : '' }}>Technical</option>
                <option value="hr" {{ request('interview_type') === 'hr' ? 'selected' : '' }}>HR</option>
                <option value="final" {{ request('interview_type') === 'final' ? 'selected' : '' }}>Final</option>
            </select>

            <button type="submit" class="btn-secondary-sm">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>

            @if(request('status') || request('interview_type'))
                <a href="{{ route('admin.jobs.interviews', $vacancy->id) }}" class="btn-secondary-sm">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </section>

    {{-- Interviews Table --}}
    <section class="card-elevated rounded-apple-lg overflow-hidden">
        @if($interviews->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead style="background: rgba(255,255,255,0.03); border-bottom: 1px solid rgba(235,235,245,0.1);">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Candidate
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Schedule
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Type
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Meeting
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: rgba(235,235,245,0.6);">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="divide-color: rgba(235,235,245,0.1);">
                        @foreach($interviews as $interview)
                            @php
                                $statusColors = [
                                    'scheduled' => ['bg' => 'rgba(10,132,255,0.15)', 'text' => 'rgba(10,132,255,1)'],
                                    'completed' => ['bg' => 'rgba(48,209,88,0.15)', 'text' => 'rgba(48,209,88,1)'],
                                    'cancelled' => ['bg' => 'rgba(255,69,58,0.15)', 'text' => 'rgba(255,69,58,1)'],
                                    'rescheduled' => ['bg' => 'rgba(255,214,10,0.15)', 'text' => 'rgba(255,214,10,1)'],
                                ];
                                
                                $typeLabels = [
                                    'preliminary' => 'Preliminary',
                                    'technical' => 'Technical',
                                    'hr' => 'HR',
                                    'final' => 'Final',
                                ];
                            @endphp
                            <tr class="hover:bg-white/5 transition-apple">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold" 
                                             style="background: rgba(10,132,255,0.2);">
                                            {{ substr($interview->jobApplication->full_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $interview->jobApplication->full_name }}</p>
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                                {{ $interview->jobApplication->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-white">
                                        {{ $interview->scheduled_at->format('d M Y') }}
                                    </p>
                                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                        {{ $interview->scheduled_at->format('H:i') }} · {{ $interview->duration_minutes }}min
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                          style="background: rgba(175,82,222,0.2); color: rgba(175,82,222,1);">
                                        {{ $typeLabels[$interview->interview_type] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($interview->meeting_type === 'video-call')
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-video text-sm" style="color: rgba(10,132,255,1);"></i>
                                            <span class="text-sm text-white">Video Call</span>
                                        </div>
                                        @if($interview->meeting_link)
                                            <a href="{{ $interview->meeting_link }}" target="_blank" 
                                               class="text-xs hover:underline" style="color: rgba(10,132,255,1);">
                                                Join meeting →
                                            </a>
                                        @endif
                                    @elseif($interview->meeting_type === 'phone')
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-phone text-sm" style="color: rgba(52,199,89,1);"></i>
                                            <span class="text-sm text-white">Phone</span>
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-sm" style="color: rgba(255,214,10,1);"></i>
                                            <span class="text-sm text-white">In Person</span>
                                        </div>
                                        @if($interview->location)
                                            <p class="text-xs" style="color: rgba(235,235,245,0.6);">
                                                {{ Str::limit($interview->location, 20) }}
                                            </p>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold" 
                                          style="background: {{ $statusColors[$interview->status]['bg'] }}; color: {{ $statusColors[$interview->status]['text'] }};">
                                        {{ ucfirst($interview->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.recruitment.interviews.show', $interview->id) }}" class="btn-secondary-sm">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        @if($interview->status === 'scheduled')
                                            <a href="{{ route('admin.recruitment.interviews.edit', $interview->id) }}" class="btn-secondary-sm">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-4 py-3" style="border-top: 1px solid rgba(235,235,245,0.1);">
                {{ $interviews->links() }}
            </div>
        @else
            <div class="p-8 text-center" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-calendar-alt text-4xl mb-3"></i>
                <p class="text-sm">No interviews scheduled</p>
                @if(request('status') || request('interview_type'))
                    <a href="{{ route('admin.jobs.interviews', $vacancy->id) }}" class="text-xs text-apple-blue hover:underline mt-2 inline-block">
                        Clear filters
                    </a>
                @else
                    <a href="{{ route('admin.recruitment.interviews.create', ['vacancy_id' => $vacancy->id]) }}" 
                       class="btn-primary-sm mt-3 inline-flex">
                        <i class="fas fa-calendar-plus mr-2"></i>Schedule First Interview
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>
@endsection
