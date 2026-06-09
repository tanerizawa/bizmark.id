@extends('layouts.admin')

@section('title', 'Edit Campaign')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto"
     x-data="{
         showPreview: false,
         previewSubject: '',
         previewContent: '',
         tagsVisible: {{ old('recipient_type', $campaign->recipient_type) === 'tags' ? 'true' : 'false' }},
     }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-400"></i>Edit Campaign
            </h1>
            <p class="text-gray-400 mt-1">{{ $campaign->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
               class="inline-flex items-center px-4 py-2 bg-cyan-600/20 border border-cyan-600 text-cyan-400 hover:bg-cyan-600/30 text-sm font-medium rounded-lg transition">
                <i class="fas fa-eye mr-2"></i>View
            </a>
            <a href="{{ route('admin.campaigns.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-5">
        <i class="fas fa-exclamation-circle flex-shrink-0"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    @if($campaign->status !== 'draft')
    <div class="flex items-start gap-3 bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 rounded-xl px-4 py-3 mb-5">
        <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
        <span><strong>Note:</strong> Campaign ini berstatus "{{ $campaign->status }}". Beberapa field mungkin dikunci.</span>
    </div>
    @endif

    <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Campaign Details --}}
            <div class="lg:col-span-2 space-y-5">
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2">
                            <i class="fas fa-info-circle text-gray-400"></i>Basic Information
                        </h5>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Campaign Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $campaign->name) }}" required
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                            @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Email Subject <span class="text-red-400">*</span></label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject', $campaign->subject) }}" required
                                   {{ $campaign->status !== 'draft' ? 'readonly' : '' }}
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror">
                            @error('subject')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Email Template</label>
                            <select name="template_id" id="template_id" @change="loadTemplate(this.value)"
                                    {{ $campaign->status !== 'draft' ? 'disabled' : '' }}
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Select Template (Optional) --</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-content="{{ $template->content }}"
                                        {{ old('template_id', $campaign->template_id) == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ ucfirst($template->category) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-1">Email Content (HTML) <span class="text-red-400">*</span></label>
                            <textarea name="content" id="content" rows="15" required
                                      {{ $campaign->status !== 'draft' ? 'readonly' : '' }}
                                      class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono @error('content') border-red-500 @enderror">{{ old('content', $campaign->content) }}</textarea>
                            @error('content')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="button"
                                @click="previewSubject = document.getElementById('subject').value; previewContent = document.getElementById('content').value; showPreview = true"
                                class="inline-flex items-center px-3 py-1.5 border border-cyan-600 text-cyan-400 hover:bg-cyan-600/10 text-sm rounded-lg transition">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                    </div>
                </div>
            </div>

            {{-- Right: Settings --}}
            <div class="space-y-4">

                {{-- Status Badge --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 text-center">
                    <p class="text-gray-400 text-xs mb-2">Current Status</p>
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                        @if($campaign->status === 'draft') bg-yellow-500/20 text-yellow-400
                        @elseif($campaign->status === 'scheduled') bg-cyan-500/20 text-cyan-400
                        @elseif($campaign->status === 'sending') bg-blue-500/20 text-blue-400
                        @elseif($campaign->status === 'sent') bg-green-500/20 text-green-400
                        @else bg-gray-500/20 text-gray-400
                        @endif">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>

                {{-- Recipients --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2 text-sm">
                            <i class="fas fa-users text-gray-400"></i>Recipients
                        </h5>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Send To</label>
                            <select name="recipient_type" id="recipient_type" required
                                    @change="tagsVisible = $event.target.value === 'tags'"
                                    {{ $campaign->status !== 'draft' ? 'disabled' : '' }}
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all" {{ old('recipient_type', $campaign->recipient_type) == 'all' ? 'selected' : '' }}>All Subscribers</option>
                                <option value="active" {{ old('recipient_type', $campaign->recipient_type) == 'active' ? 'selected' : '' }}>Active Only</option>
                                <option value="tags" {{ old('recipient_type', $campaign->recipient_type) == 'tags' ? 'selected' : '' }}>By Tags</option>
                            </select>
                        </div>
                        <div x-show="tagsVisible" x-cloak>
                            <label class="block text-xs text-gray-400 mb-1">Select Tags</label>
                            <input type="text" name="recipient_tags"
                                   value="{{ old('recipient_tags', is_array($campaign->recipient_tags) ? implode(',', $campaign->recipient_tags) : '') }}"
                                   placeholder="e.g., customer, vip, prospect"
                                   {{ $campaign->status !== 'draft' ? 'readonly' : '' }}
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Comma-separated tags</p>
                        </div>
                        @if($campaign->status === 'sent')
                        <div class="flex items-center gap-2 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-3 py-2 text-xs">
                            <i class="fas fa-check-circle flex-shrink-0"></i>
                            <span>Sent to <strong>{{ $campaign->sent_count }}</strong> of <strong>{{ $campaign->total_recipients }}</strong> recipients</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                    <div class="px-5 py-4 border-b border-gray-700">
                        <h5 class="text-white font-semibold flex items-center gap-2 text-sm">
                            <i class="fas fa-clock text-gray-400"></i>Schedule
                        </h5>
                    </div>
                    <div class="p-5">
                        @if($campaign->status === 'draft')
                        <input type="datetime-local" name="scheduled_at"
                               value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to send immediately</p>
                        @else
                        <p class="text-gray-400 text-sm">
                            @if($campaign->scheduled_at)
                                Scheduled: {{ $campaign->scheduled_at->format('d M Y, H:i') }}
                            @elseif($campaign->sent_at)
                                Sent: {{ $campaign->sent_at->format('d M Y, H:i') }}
                            @else
                                Not scheduled
                            @endif
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-2">
                    <button type="submit"
                            class="w-full {{ $campaign->status === 'draft' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-save mr-2"></i>{{ $campaign->status === 'draft' ? 'Update Campaign' : 'Update Details' }}
                    </button>
                    <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
                       class="block w-full text-center border border-cyan-600 text-cyan-400 hover:bg-cyan-600/10 font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-eye mr-2"></i>View Campaign
                    </a>
                    <a href="{{ route('admin.campaigns.index') }}"
                       class="block w-full text-center border border-gray-600 text-gray-300 hover:text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- Preview Modal --}}
    <div x-show="showPreview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="showPreview = false">
        <div class="absolute inset-0 bg-black/60" @click="showPreview = false"></div>
        <div class="relative bg-gray-800 border border-gray-700 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-eye text-gray-400"></i>Email Preview
                </h5>
                <button @click="showPreview = false" class="text-gray-400 hover:text-white transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <p class="text-sm mb-3"><span class="text-white font-medium">Subject:</span> <span class="text-gray-300" x-text="previewSubject || '(No subject)'"></span></p>
                <hr class="border-gray-700 mb-4">
                <div class="bg-white rounded-lg p-4 text-black overflow-auto" x-html="previewContent || '<p class=\'text-gray-500\'>No content</p>'"></div>
            </div>
        </div>
    </div>
</div>

<script>
function loadTemplate(templateId) {
    if (!templateId) return;
    const opt = document.querySelector(`#template_id option[value="${templateId}"]`);
    if (opt) document.getElementById('content').value = opt.getAttribute('data-content') || '';
}
</script>
@endsection
