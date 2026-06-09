@extends('layouts.admin')

@section('title', 'Template Detail')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto" x-data="{ previewOpen: false }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-400"></i>{{ $template->name }}
            </h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($template->category === 'newsletter') bg-cyan-500/20 text-cyan-400
                    @elseif($template->category === 'promotional') bg-yellow-500/20 text-yellow-400
                    @elseif($template->category === 'transactional') bg-green-500/20 text-green-400
                    @else bg-gray-500/20 text-gray-400
                    @endif">
                    {{ ucfirst($template->category) }}
                </span>
                @if($template->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">Active</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">Inactive</span>
                @endif
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.templates.edit', $template->id) }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black text-sm font-medium rounded-lg transition">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.templates.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Preview --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Preview Card --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-eye text-gray-400"></i>Template Preview
                    </h5>
                    <button @click="previewOpen = true"
                        class="inline-flex items-center text-sm px-3 py-1.5 border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 rounded-lg transition">
                        <i class="fas fa-expand mr-1.5"></i>Full Screen
                    </button>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 mb-4 text-sm">
                        <p class="mb-2"><span class="text-white font-medium">Subject:</span> <span class="text-gray-400">{{ $template->subject }}</span></p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-white font-medium">Variables:</span>
                            @if($template->variables && is_array($template->variables))
                                @foreach($template->variables as $var)
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{ '{{'.$var.'}}' }}</span>
                                @endforeach
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{name}}</span>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{email}}</span>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{unsubscribe_url}}</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-lg overflow-auto" style="background:white;padding:20px;max-height:600px;">
                        {!! $template->content !!}
                    </div>
                </div>
            </div>

            {{-- Plain Text --}}
            @if($template->plain_content)
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-align-left text-gray-400"></i>Plain Text Version
                    </h5>
                </div>
                <div class="p-5">
                    <pre class="text-gray-400 text-sm whitespace-pre-wrap">{{ $template->plain_content }}</pre>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Info + Actions --}}
        <div class="space-y-5">

            {{-- Template Info --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>Template Info
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <span class="text-gray-400 block mb-1">Template Name</span>
                        <p class="text-white">{{ $template->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Category</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($template->category === 'newsletter') bg-cyan-500/20 text-cyan-400
                            @elseif($template->category === 'promotional') bg-yellow-500/20 text-yellow-400
                            @elseif($template->category === 'transactional') bg-green-500/20 text-green-400
                            @else bg-gray-500/20 text-gray-400
                            @endif">
                            {{ ucfirst($template->category) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Status</span>
                        @if($template->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">Inactive</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Subject Line</span>
                        <p class="text-white">{{ $template->subject }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Variables</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @if($template->variables && is_array($template->variables))
                                @foreach($template->variables as $var)
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{ '{{'.$var.'}}' }}</span>
                                @endforeach
                            @else
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{name}}</span>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{email}}</span>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">@{{unsubscribe_url}}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Created</span>
                        <p class="text-white">{{ $template->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->created_at->diffForHumans() }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Last Updated</span>
                        <p class="text-white">{{ $template->updated_at->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-xs">{{ $template->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Usage Stats --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-chart-bar text-gray-400"></i>Usage Statistics
                    </h5>
                </div>
                <div class="p-5 text-center">
                    <p class="text-3xl font-bold text-white">{{ $template->campaigns->count() }}</p>
                    <p class="text-gray-400 text-sm">Campaigns using this template</p>

                    @if($template->campaigns->count() > 0)
                    <hr class="border-gray-700 my-4">
                    <div class="text-left space-y-3">
                        <p class="text-xs text-gray-400 mb-2">Recent Campaigns:</p>
                        @foreach($template->campaigns()->latest()->limit(5)->get() as $campaign)
                        <div>
                            <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="text-cyan-400 hover:text-cyan-300 text-sm">
                                {{ $campaign->name }}
                            </a>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-gray-500">{{ $campaign->created_at->format('d M Y') }}</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs
                                    @if($campaign->status === 'sent') bg-green-500/20 text-green-400
                                    @elseif($campaign->status === 'draft') bg-yellow-500/20 text-yellow-400
                                    @else bg-cyan-500/20 text-cyan-400
                                    @endif">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
                <a href="{{ route('admin.templates.edit', $template->id) }}"
                   class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-black font-medium py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Template
                </a>
                <a href="{{ route('admin.campaigns.create') }}?template_id={{ $template->id }}"
                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-paper-plane mr-2"></i>Use in Campaign
                </a>

                @if($template->is_active)
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $template->name }}">
                    <input type="hidden" name="subject" value="{{ $template->subject }}">
                    <input type="hidden" name="content" value="{{ $template->content }}">
                    <input type="hidden" name="category" value="{{ $template->category }}">
                    <input type="hidden" name="is_active" value="0">
                    <button type="submit" class="w-full border border-gray-600 text-gray-300 hover:text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-eye-slash mr-2"></i>Deactivate
                    </button>
                </form>
                @else
                <form action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="name" value="{{ $template->name }}">
                    <input type="hidden" name="subject" value="{{ $template->subject }}">
                    <input type="hidden" name="content" value="{{ $template->content }}">
                    <input type="hidden" name="category" value="{{ $template->category }}">
                    <input type="hidden" name="is_active" value="1">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-eye mr-2"></i>Activate
                    </button>
                </form>
                @endif

                <button @click="navigator.clipboard.writeText(`{!! addslashes($template->content) !!}`).then(() => { $el.innerHTML='<i class=\'fas fa-check mr-2\'></i>Tersalin!'; setTimeout(()=>$el.innerHTML='<i class=\'fas fa-copy mr-2\'></i>Copy HTML',2000) })"
                    class="w-full border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 font-medium py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-copy mr-2"></i>Copy HTML
                </button>

                @if($template->campaigns->count() === 0)
                <form action="{{ route('admin.templates.destroy', $template->id) }}" method="POST"
                      x-data @submit.prevent="if(confirm('Delete this template? This cannot be undone.')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full border border-red-600 text-red-400 hover:bg-red-900/30 font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-trash mr-2"></i>Delete Template
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Full Preview Modal --}}
    <div x-show="previewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="previewOpen = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
             @click.outside="previewOpen = false">
            <div class="flex items-center justify-between px-5 py-4 bg-gray-800 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-envelope text-gray-400"></i>{{ $template->name }}
                </h5>
                <button @click="previewOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="bg-gray-100 px-5 py-3 border-b text-sm">
                <strong>Subject:</strong> {{ $template->subject }}
            </div>
            <div class="p-6 overflow-y-auto bg-white flex-1">
                {!! $template->content !!}
            </div>
        </div>
    </div>
</div>
@endsection
