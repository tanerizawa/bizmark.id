@extends('layouts.admin')

@section('title', 'Campaign Details')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto" x-data="{ previewOpen: false }">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-paper-plane text-blue-400"></i>{{ $campaign->name }}
            </h1>
            <p class="text-gray-400 mt-1 flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($campaign->status === 'draft') bg-yellow-500/20 text-yellow-400
                    @elseif($campaign->status === 'scheduled') bg-blue-500/20 text-blue-400
                    @elseif($campaign->status === 'sending') bg-indigo-500/20 text-indigo-400
                    @elseif($campaign->status === 'sent') bg-green-500/20 text-green-400
                    @else bg-gray-500/20 text-gray-400
                    @endif">
                    {{ ucfirst($campaign->status) }}
                </span>
                @if($campaign->sent_at)
                    <span>• Sent {{ $campaign->sent_at->diffForHumans() }}</span>
                @elseif($campaign->scheduled_at)
                    <span>• Scheduled for {{ $campaign->scheduled_at->format('d M Y, H:i') }}</span>
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($campaign->status === 'draft')
            <a href="{{ route('admin.campaigns.edit', $campaign->id) }}"
               class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-black font-medium rounded-lg transition text-sm">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @endif
            <a href="{{ route('admin.campaigns.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 font-medium rounded-lg transition text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Stats (if sent) --}}
            @if($campaign->status === 'sent' || $campaign->status === 'sending')
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach([
                    ['icon' => 'fa-paper-plane', 'color' => 'text-blue-400', 'value' => number_format($campaign->sent_count), 'label' => 'Sent'],
                    ['icon' => 'fa-envelope-open', 'color' => 'text-green-400', 'value' => number_format($campaign->opened_count), 'label' => 'Opened ('.number_format($campaign->open_rate, 1).'%)'],
                    ['icon' => 'fa-mouse-pointer', 'color' => 'text-cyan-400', 'value' => number_format($campaign->clicked_count), 'label' => 'Clicked ('.number_format($campaign->click_rate, 1).'%)'],
                    ['icon' => 'fa-exclamation-triangle', 'color' => 'text-red-400', 'value' => number_format($campaign->bounced_count), 'label' => 'Bounced ('.number_format($campaign->bounce_rate, 1).'%)'],
                ] as $stat)
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 text-center">
                    <i class="fas {{ $stat['icon'] }} text-2xl {{ $stat['color'] }} mb-2"></i>
                    <div class="text-xl font-bold text-white">{{ $stat['value'] }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $stat['label'] }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Email Content Preview --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-envelope text-gray-400"></i>Email Content
                    </h5>
                    <button @click="previewOpen = true"
                        class="inline-flex items-center text-sm px-3 py-1.5 border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 rounded-lg transition">
                        <i class="fas fa-expand mr-1.5"></i>Full Preview
                    </button>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <span class="text-white font-medium">Subject:</span>
                        <p class="text-gray-400 mt-0.5">{{ $campaign->subject }}</p>
                    </div>
                    <hr class="border-gray-700 mb-4">
                    <div class="rounded-lg overflow-auto" style="max-height:500px; background:white; padding:20px;">
                        {!! $campaign->content !!}
                    </div>
                </div>
            </div>

            {{-- Delivery Log --}}
            @if($campaign->status === 'sent' && $campaign->emailLogs->count() > 0)
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-list text-gray-400"></i>Email Delivery Log
                    </h5>
                </div>
                <div class="p-5 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="text-gray-400 border-b border-gray-700">
                                <th class="pb-3 font-medium">Recipient</th>
                                <th class="pb-3 font-medium">Status</th>
                                <th class="pb-3 font-medium">Sent</th>
                                <th class="pb-3 font-medium">Opened</th>
                                <th class="pb-3 font-medium">Clicked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach($campaign->emailLogs()->limit(50)->get() as $log)
                            <tr class="text-gray-300 hover:bg-gray-700/40">
                                <td class="py-2.5">{{ $log->recipient_email }}</td>
                                <td class="py-2.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        @if($log->status === 'sent') bg-gray-500/20 text-gray-400
                                        @elseif($log->status === 'delivered') bg-cyan-500/20 text-cyan-400
                                        @elseif($log->status === 'opened') bg-green-500/20 text-green-400
                                        @elseif($log->status === 'clicked') bg-blue-500/20 text-blue-400
                                        @elseif($log->status === 'bounced') bg-red-500/20 text-red-400
                                        @else bg-yellow-500/20 text-yellow-400
                                        @endif">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="py-2.5 text-gray-400">{{ $log->sent_at->format('d M, H:i') }}</td>
                                <td class="py-2.5 text-gray-400">{{ $log->opened_at ? $log->opened_at->format('d M, H:i') : '—' }}</td>
                                <td class="py-2.5 text-gray-400">{{ $log->clicked_at ? $log->clicked_at->format('d M, H:i') : '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($campaign->emailLogs->count() > 50)
                    <p class="text-gray-500 text-center text-sm mt-3">
                        Showing first 50 of {{ number_format($campaign->emailLogs->count()) }} emails
                    </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Info + Actions --}}
        <div class="space-y-5">
            {{-- Campaign Info --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>Campaign Info
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <span class="text-gray-400 block mb-1">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($campaign->status === 'draft') bg-yellow-500/20 text-yellow-400
                            @elseif($campaign->status === 'scheduled') bg-blue-500/20 text-blue-400
                            @elseif($campaign->status === 'sending') bg-indigo-500/20 text-indigo-400
                            @elseif($campaign->status === 'sent') bg-green-500/20 text-green-400
                            @else bg-gray-500/20 text-gray-400
                            @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Recipients</span>
                        <p class="text-white">{{ ucfirst($campaign->recipient_type) }}</p>
                        @if($campaign->recipient_type === 'tags' && $campaign->recipient_tags)
                        <p class="text-gray-400 text-xs">Tags: {{ implode(', ', $campaign->recipient_tags) }}</p>
                        @endif
                    </div>
                    @if($campaign->template)
                    <div>
                        <span class="text-gray-400 block mb-1">Template</span>
                        <p class="text-white">{{ $campaign->template->name }}</p>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-400 block mb-1">Created</span>
                        <p class="text-white">{{ $campaign->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-xs">{{ $campaign->created_at->diffForHumans() }}</p>
                    </div>
                    @if($campaign->scheduled_at)
                    <div>
                        <span class="text-gray-400 block mb-1">Scheduled For</span>
                        <p class="text-white">{{ $campaign->scheduled_at->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-xs">{{ $campaign->scheduled_at->diffForHumans() }}</p>
                    </div>
                    @endif
                    @if($campaign->sent_at)
                    <div>
                        <span class="text-gray-400 block mb-1">Sent At</span>
                        <p class="text-white">{{ $campaign->sent_at->format('d M Y, H:i') }}</p>
                        <p class="text-gray-500 text-xs">{{ $campaign->sent_at->diffForHumans() }}</p>
                    </div>
                    @endif
                    @if($campaign->creator)
                    <div>
                        <span class="text-gray-400 block mb-1">Created By</span>
                        <p class="text-white">{{ $campaign->creator->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 space-y-3">
                @if($campaign->status === 'draft')
                <form action="{{ route('admin.campaigns.send', $campaign->id) }}" method="POST"
                      x-data @submit.prevent="if(confirm('Send this campaign now?')) $el.submit()">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Send Now
                    </button>
                </form>
                <a href="{{ route('admin.campaigns.edit', $campaign->id) }}"
                   class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-black font-medium py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-edit mr-2"></i>Edit Campaign
                </a>
                @elseif($campaign->status === 'scheduled')
                <form action="{{ route('admin.campaigns.cancel', $campaign->id) }}" method="POST"
                      x-data @submit.prevent="if(confirm('Cancel this scheduled campaign?')) $el.submit()">
                    @csrf
                    <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-times mr-2"></i>Cancel Schedule
                    </button>
                </form>
                @endif

                @if($campaign->status === 'sent')
                <a href="{{ route('admin.campaigns.export', $campaign->id) }}"
                   class="block w-full text-center border border-cyan-600 text-cyan-400 hover:bg-cyan-900/30 font-medium py-2.5 rounded-xl transition text-sm">
                    <i class="fas fa-download mr-2"></i>Export Report
                </a>
                @endif

                @if($campaign->status === 'draft' || $campaign->status === 'cancelled')
                <form action="{{ route('admin.campaigns.destroy', $campaign->id) }}" method="POST"
                      x-data @submit.prevent="if(confirm('Delete this campaign? This cannot be undone.')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full border border-red-600 text-red-400 hover:bg-red-900/30 font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-trash mr-2"></i>Delete Campaign
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Full Preview Modal (Alpine) --}}
    <div x-show="previewOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="previewOpen = false">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
             @click.outside="previewOpen = false">
            <div class="flex items-center justify-between px-5 py-4 bg-gray-800 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-envelope text-gray-400"></i>Email Preview
                </h5>
                <button @click="previewOpen = false" class="text-gray-400 hover:text-white text-xl leading-none">&times;</button>
            </div>
            <div class="bg-gray-100 px-5 py-3 border-b text-sm">
                <strong>Subject:</strong> {{ $campaign->subject }}
            </div>
            <div class="p-6 overflow-y-auto bg-white flex-1">
                {!! $campaign->content !!}
            </div>
        </div>
    </div>
</div>
@endsection
