@extends('layouts.admin')

@section('title', 'Send Campaign')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-paper-plane text-blue-400"></i>Send Campaign
            </h1>
            <p class="text-gray-400 mt-1">Review and confirm before sending</p>
        </div>
        <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
           class="inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 text-sm font-medium rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    {{-- Warning --}}
    <div class="flex items-start gap-3 bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-exclamation-triangle mt-0.5 flex-shrink-0"></i>
        <span><strong>Important:</strong> Once you send this campaign, it cannot be stopped or undone. Please review carefully.</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Preview --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Email Preview --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-eye text-gray-400"></i>Email Preview
                    </h5>
                </div>
                <div class="p-5">
                    <div class="bg-gray-900 rounded-lg p-4 mb-4 text-sm space-y-1.5">
                        <p><span class="text-white font-medium">From:</span> <span class="text-gray-400">{{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;</span></p>
                        <p><span class="text-white font-medium">To:</span> <span class="text-gray-400">{{ $recipients->count() }} recipients</span></p>
                        <p><span class="text-white font-medium">Subject:</span> <span class="text-gray-400">{{ $campaign->subject }}</span></p>
                    </div>
                    <div class="rounded-lg overflow-auto" style="background:white;padding:20px;max-height:600px;">
                        {!! $campaign->content !!}
                    </div>
                </div>
            </div>

            {{-- Recipients Preview --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-users text-gray-400"></i>Recipients (First 20)
                    </h5>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-700">
                                    <th class="pb-2 font-medium">Email</th>
                                    <th class="pb-2 font-medium">Name</th>
                                    <th class="pb-2 font-medium">Status</th>
                                    <th class="pb-2 font-medium">Tags</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($recipients->take(20) as $recipient)
                                <tr class="text-gray-300">
                                    <td class="py-2">{{ $recipient->email }}</td>
                                    <td class="py-2">{{ $recipient->name ?? '-' }}</td>
                                    <td class="py-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($recipient->status === 'active') bg-green-500/20 text-green-400
                                            @elseif($recipient->status === 'unsubscribed') bg-yellow-500/20 text-yellow-400
                                            @else bg-red-500/20 text-red-400
                                            @endif">
                                            {{ ucfirst($recipient->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        @if($recipient->tags && is_array($recipient->tags))
                                            @foreach(array_slice($recipient->tags, 0, 2) as $tag)
                                                <span class="px-1.5 py-0.5 rounded text-xs bg-gray-700 text-gray-300">{{ $tag }}</span>
                                            @endforeach
                                            @if(count($recipient->tags) > 2)
                                                <span class="text-gray-500 text-xs">+{{ count($recipient->tags) - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($recipients->count() > 20)
                    <p class="text-gray-400 text-center text-sm mt-3">
                        Showing first 20 of {{ number_format($recipients->count()) }} recipients
                    </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Summary + Checklist + Actions --}}
        <div class="space-y-5">

            {{-- Campaign Summary --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>Campaign Summary
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    <div>
                        <span class="text-gray-400 block mb-1">Campaign Name</span>
                        <p class="text-white">{{ $campaign->name }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Subject Line</span>
                        <p class="text-white">{{ $campaign->subject }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Total Recipients</span>
                        <p class="text-2xl font-bold text-white">{{ number_format($recipients->count()) }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 block mb-1">Recipient Type</span>
                        <p class="text-white">
                            {{ ucfirst($campaign->recipient_type) }}
                            @if($campaign->recipient_type === 'tags' && $campaign->recipient_tags)
                            <span class="text-gray-400 text-xs block">Tags: {{ implode(', ', $campaign->recipient_tags) }}</span>
                            @endif
                        </p>
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
                    </div>
                </div>
            </div>

            {{-- Pre-Send Checklist --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow"
                 x-data="{ checks: [false,false,false,false,false] }">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-check-square text-gray-400"></i>Pre-Send Checklist
                    </h5>
                </div>
                <div class="p-5 space-y-3">
                    @foreach(['Email content reviewed', 'Subject line is clear', 'Recipients verified', 'Links tested', 'Ready to send'] as $i => $label)
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="checks[{{ $i }}]" class="w-4 h-4 accent-blue-500">
                        <span class="text-white text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>

                {{-- Send Actions --}}
                <div class="px-5 pb-5 space-y-3">
                    <form action="{{ route('admin.campaigns.process-send', $campaign->id) }}" method="POST"
                          x-data @submit.prevent="if(confirm('Are you sure you want to send this campaign to {{ number_format($recipients->count()) }} recipients? This action cannot be undone.')) $el.submit()">
                        @csrf
                        <button type="submit" class="w-full font-medium py-2.5 rounded-xl transition text-sm"
                                :disabled="!checks.every(c => c)"
                                :class="checks.every(c => c) ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-gray-700 text-gray-500 cursor-not-allowed'">
                            <i class="fas fa-paper-plane mr-2"></i>Send Campaign Now
                        </button>
                    </form>
                    <a href="{{ route('admin.campaigns.edit', $campaign->id) }}"
                       class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-black font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-edit mr-2"></i>Edit Campaign
                    </a>
                    <a href="{{ route('admin.campaigns.show', $campaign->id) }}"
                       class="block w-full text-center border border-gray-600 text-gray-300 hover:text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>

                    <div class="flex items-start gap-2 bg-blue-500/10 border border-blue-500/30 text-blue-300 rounded-xl px-3 py-2.5 text-xs mt-2">
                        <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                        <span><strong>Note:</strong> Make sure SMTP is configured in your .env file before sending.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
