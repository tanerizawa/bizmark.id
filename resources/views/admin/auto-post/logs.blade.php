@extends('layouts.admin')

@section('content')
<div class="container-custom">
    <div class="page-header-apple">
        <div>
            <h1 class="page-title-apple">
                <i class="fas fa-file-alt mr-3"></i>Auto-Post Activity Logs
            </h1>
            <p class="page-subtitle-apple">Monitor semua aktivitas auto-posting sistem</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(10,132,255,0.15); color: var(--apple-blue);">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ number_format($stats['total']) }}</div>
                <div class="stat-label-apple">Total Processed</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ number_format($stats['completed']) }}</div>
                <div class="stat-label-apple">Completed</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(255,69,58,0.15); color: var(--apple-red);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ number_format($stats['failed']) }}</div>
                <div class="stat-label-apple">Failed</div>
            </div>
        </div>

        <div class="stat-card-apple">
            <div class="stat-icon-apple" style="background: rgba(255,159,10,0.15); color: var(--apple-orange);">
                <i class="fas fa-spinner"></i>
            </div>
            <div>
                <div class="stat-value-apple">{{ number_format($stats['processing']) }}</div>
                <div class="stat-label-apple">Processing</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card-apple mb-5">
        <form action="{{ route('auto-post.logs.index') }}" method="GET" class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="label-apple">Status</label>
                    <select name="status" class="input-apple">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    </select>
                </div>
                <div>
                    <label class="label-apple">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-apple">
                </div>
                <div>
                    <label class="label-apple">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-apple">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary-apple w-full">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="card-apple">
        <div class="table-responsive">
            <table class="table-apple">
                <thead>
                    <tr>
                        <th>Processed At</th>
                        <th>Article</th>
                        <th>Status</th>
                        <th>Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <div class="text-sm text-dark-text-primary">
                                {{ $log->completed_at ? $log->completed_at->format('d M Y H:i') : '-' }}
                            </div>
                        </td>
                        <td>
                            @if($log->article)
                            <a href="{{ route('articles.show', $log->article->id) }}" class="text-apple-blue hover:underline">
                                {{ $log->article->title }}
                            </a>
                            @else
                            <span class="text-dark-text-tertiary">-</span>
                            @endif
                        </td>
                        <td>
                            @if($log->status == 'completed')
                            <span class="badge-apple" style="background: rgba(48,209,88,0.15); color: var(--apple-green);">
                                <i class="fas fa-check-circle mr-1"></i>Completed
                            </span>
                            @elseif($log->status == 'failed')
                            <span class="badge-apple" style="background: rgba(255,69,58,0.15); color: var(--apple-red);">
                                <i class="fas fa-times-circle mr-1"></i>Failed
                            </span>
                            @elseif($log->status == 'processing')
                            <span class="badge-apple" style="background: rgba(255,159,10,0.15); color: var(--apple-orange);">
                                <i class="fas fa-spinner fa-spin mr-1"></i>Processing
                            </span>
                            @else
                            <span class="badge-apple">{{ ucfirst($log->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="text-xs text-dark-text-tertiary max-w-md truncate">
                                {{ $log->error_message ?? '-' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-dark-text-tertiary">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p>No logs found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="p-5 border-t" style="border-color: var(--dark-separator);">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
