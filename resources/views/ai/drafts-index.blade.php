@extends('layouts.admin')

@section('title', 'Draft Dokumen AI - ' . $project->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center">
            <a href="{{ route('projects.show', $project) }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold" style="color: #FFFFFF;">
                    <i class="fas fa-file-alt mr-3 text-apple-blue-dark"></i>Draft Dokumen AI
                </h1>
                <p class="mt-1" style="color: rgba(235, 235, 245, 0.6);">{{ $project->name }}</p>
            </div>
        </div>
        <a href="{{ route('ai.paraphrase.create', $project) }}" 
           class="px-4 py-2 rounded-lg font-medium transition-colors"
           style="background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%); color: #FFFFFF;">
            <i class="fas fa-plus mr-2"></i>Buat Draft Baru
        </a>
    </div>

    <!-- Processing Status (if any) -->
    <div id="processingStatus"></div>

    <!-- Drafts List -->
    @if($drafts->isEmpty())
        <div class="card-elevated rounded-apple-lg p-12 text-center">
            <i class="fas fa-inbox text-6xl mb-4" style="color: rgba(235, 235, 245, 0.3);"></i>
            <h3 class="text-xl font-semibold mb-2" style="color: #FFFFFF;">Belum Ada Draft</h3>
            <p class="mb-6" style="color: rgba(235, 235, 245, 0.6);">
                Buat draft dokumen pertama Anda dengan AI
            </p>
            <a href="{{ route('ai.paraphrase.create', $project) }}" 
               class="inline-block px-6 py-3 rounded-lg font-medium"
               style="background: linear-gradient(135deg, #007AFF 0%, #0051D5 100%); color: #FFFFFF;">
                <i class="fas fa-magic mr-2"></i>Parafrase Dokumen
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($drafts as $draft)
            <div class="card-elevated rounded-apple-lg p-6 hover:shadow-xl transition-all">
                <div class="flex items-start justify-between">
                    <!-- Draft Info -->
                    <div class="flex-1">
                        <div class="flex items-start mb-3">
                            <i class="fas fa-file-alt text-apple-blue-dark text-2xl mr-4 mt-1"></i>
                            <div>
                                <h3 class="text-lg font-semibold mb-1" style="color: #FFFFFF;">
                                    {{ $draft->title }}
                                </h3>
                                <div class="flex flex-wrap gap-3 items-center">
                                    <!-- Status Badge -->
                                    @php
                                        $statusColors = [
                                            'draft' => ['bg' => 'rgba(255, 149, 0, 0.2)', 'text' => '#FF9500'],
                                            'reviewed' => ['bg' => 'rgba(0, 122, 255, 0.2)', 'text' => '#007AFF'],
                                            'approved' => ['bg' => 'rgba(52, 199, 89, 0.2)', 'text' => '#34C759'],
                                            'rejected' => ['bg' => 'rgba(255, 59, 48, 0.2)', 'text' => '#FF3B30'],
                                            'exported' => ['bg' => 'rgba(175, 82, 222, 0.2)', 'text' => '#AF52DE'],
                                        ];
                                        $color = $statusColors[$draft->status] ?? $statusColors['draft'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                                        {{ strtoupper($draft->status) }}
                                    </span>

                                    <!-- Template -->
                                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                        <i class="fas fa-layer-group mr-1"></i>{{ $draft->template->name }}
                                    </span>

                                    <!-- Stats -->
                                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                        <i class="fas fa-font mr-1"></i>{{ number_format($draft->word_count) }} kata
                                    </span>

                                    <!-- Creator -->
                                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                        <i class="fas fa-user mr-1"></i>{{ $draft->creator->name }}
                                    </span>

                                    <!-- Date -->
                                    <span class="text-sm" style="color: rgba(235, 235, 245, 0.6);">
                                        <i class="fas fa-clock mr-1"></i>{{ $draft->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- AI Processing Info -->
                                @if($draft->aiLog)
                                <div class="mt-3 p-3 rounded-lg" style="background: rgba(28, 28, 30, 0.6);">
                                    <div class="flex flex-wrap gap-4 text-xs" style="color: rgba(235, 235, 245, 0.7);">
                                        <span>
                                            <i class="fas fa-microchip mr-1 text-apple-blue-dark"></i>
                                            AI: {{ $draft->aiLog->total_tokens }} tokens
                                        </span>
                                        @if($draft->aiLog->metadata && isset($draft->aiLog->metadata['duration_seconds']))
                                        <span>
                                            <i class="fas fa-stopwatch mr-1"></i>
                                            Durasi: {{ round($draft->aiLog->metadata['duration_seconds'], 1) }}s
                                        </span>
                                        @endif
                                        @if($draft->aiLog->metadata && isset($draft->aiLog->metadata['chunks_count']))
                                        <span>
                                            <i class="fas fa-puzzle-piece mr-1"></i>
                                            {{ $draft->aiLog->metadata['chunks_count'] }} chunks
                                        </span>
                                        @endif
                                        @if($draft->aiLog->cost > 0)
                                        <span>
                                            <i class="fas fa-dollar-sign mr-1"></i>
                                            Cost: ${{ number_format($draft->aiLog->cost, 6) }}
                                        </span>
                                        @else
                                        <span class="text-green-500 font-medium">
                                            <i class="fas fa-check-circle mr-1"></i>FREE (Gemini Flash)
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- Compliance Score Badge -->
                                @php
                                    $latestCheck = $draft->complianceChecks->first();
                                @endphp
                                @if($latestCheck && $latestCheck->status === 'completed')
                                <div class="mt-3 flex items-center space-x-2">
                                    <span class="text-xs font-medium" style="color: rgba(235, 235, 245, 0.6);">
                                        Compliance:
                                    </span>
                                    @php
                                        $score = $latestCheck->overall_score;
                                        if ($score >= 80) {
                                            $badgeClass = 'bg-green-500/20 text-green-400 border-green-500/50';
                                            $icon = 'fa-check-circle';
                                        } elseif ($score >= 70) {
                                            $badgeClass = 'bg-yellow-500/20 text-yellow-400 border-yellow-500/50';
                                            $icon = 'fa-exclamation-triangle';
                                        } else {
                                            $badgeClass = 'bg-red-500/20 text-red-400 border-red-500/50';
                                            $icon = 'fa-times-circle';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                        <i class="fas {{ $icon }} mr-1.5"></i>
                                        {{ round($score, 1) }}/100
                                    </span>
                                    @if($latestCheck->critical_issues > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                        {{ $latestCheck->critical_issues }} critical
                                    </span>
                                    @endif
                                    @if($latestCheck->warning_issues > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                        {{ $latestCheck->warning_issues }} warning
                                    </span>
                                    @endif
                                </div>
                                @endif

                                <!-- Approval Info -->
                                @if($draft->status === 'approved' && $draft->approver)
                                <div class="mt-2 text-sm" style="color: rgba(52, 199, 89, 0.9);">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Disetujui oleh {{ $draft->approver->name }} pada {{ $draft->approved_at->format('d M Y H:i') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2 ml-4">
                        <a href="{{ route('ai.drafts.show', [$project, $draft]) }}" 
                           class="px-4 py-2 rounded-lg font-medium transition-colors"
                           style="background: rgba(0, 122, 255, 0.9); color: #FFFFFF;"
                           title="Lihat Detail">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if($draft->status !== 'rejected')
                        <a href="{{ route('ai.drafts.export', [$project, $draft]) }}?format=pdf" 
                           class="px-4 py-2 rounded-lg font-medium transition-colors"
                           style="background: rgba(52, 199, 89, 0.9); color: #FFFFFF;"
                           title="Export PDF">
                            <i class="fas fa-download"></i>
                        </a>
                        @endif

                        @if($draft->status !== 'approved')
                        <!-- Delete button (only non-approved) -->
                        <form action="{{ route('ai.drafts.destroy', [$project, $draft]) }}" method="POST" class="inline"
                              x-data @submit.prevent="if(confirm('Hapus draft: {{ $draft->title }}?')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 rounded-lg font-medium transition-colors"
                                    style="background: rgba(255, 59, 48, 0.7); color: #FFFFFF;"
                                    title="Hapus Draft">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($drafts->hasPages())
        <div class="mt-6">
            {{ $drafts->links() }}
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
// Check processing status every 10 seconds
function checkProcessingStatus() {
    fetch('{{ route('ai.status', $project) }}')
        .then(response => response.json())
        .then(data => {
            const statusDiv = document.getElementById('processingStatus');

            if (data.processing && data.processing.length > 0) {
                let html = '<div class="card-elevated rounded-apple-lg p-4 mb-6" style="background: rgba(255, 149, 0, 0.1); border-left: 4px solid #FF9500;">';
                html += '<div class="flex items-center">';
                html += '<i class="fas fa-spinner fa-spin text-xl mr-3" style="color: #FF9500;"></i>';
                html += '<div style="color: #FFFFFF;"><strong>Proses AI Sedang Berjalan:</strong></div>';
                html += '</div>';
                html += '<ul class="mt-3 space-y-2">';

                data.processing.forEach(item => {
                    html += '<li class="text-sm" style="color: rgba(235, 235, 245, 0.8);">';
                    html += '<i class="fas fa-circle text-xs mr-2" style="color: #FF9500;"></i>';
                    html += item.template_name + ' - ' + item.status + ' (' + item.started_at + ')';
                    html += '</li>';
                });

                html += '</ul></div>';
                statusDiv.innerHTML = html;

                // Clear any existing reload timeout
                if (window.processingReloadTimeout) {
                    clearTimeout(window.processingReloadTimeout);
                }

                // Reload page when processing completes
                window.processingReloadTimeout = setTimeout(() => location.reload(), 30000);
            } else {
                statusDiv.innerHTML = '';

                // No processing items, stop checking
                if (window.processingCheckInterval) {
                    clearInterval(window.processingCheckInterval);
                    window.processingCheckInterval = null;
                }

                // Clear any pending reload
                if (window.processingReloadTimeout) {
                    clearTimeout(window.processingReloadTimeout);
                    window.processingReloadTimeout = null;
                }
            }
        })
        .catch(error => console.error('Error checking status:', error));
}

// Check on page load
checkProcessingStatus();

// Check every 10 seconds (store interval ID for cleanup)
window.processingCheckInterval = setInterval(checkProcessingStatus, 10000);
</script>
@endpush
@endsection
