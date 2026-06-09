@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-white">Audit Logs</h1>
            <p class="text-sm mt-0.5" style="color: rgba(235,235,245,0.6);">Rekam jejak perubahan data oleh admin</p>
        </div>
        <a href="{{ route('admin.security.webhook-metrics') }}"
           class="flex items-center gap-2 px-3 py-1.5 rounded-apple text-xs font-medium transition-all"
           style="background: rgba(255,255,255,0.06); color: rgba(235,235,245,0.7); border: 1px solid rgba(255,255,255,0.08);">
            <i class="fas fa-shield-alt"></i> Webhook Metrics
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.security.audit-logs') }}" class="flex flex-wrap items-center gap-3">
        <select name="event" onchange="this.form.submit()"
                class="rounded-apple px-3 py-1.5 text-sm" style="background: rgba(44,44,46,0.9); border: 1px solid rgba(84,84,88,0.35); color: rgba(235,235,245,0.8);">
            <option value="">Semua Event</option>
            @foreach($events as $ev)
                <option value="{{ $ev }}" {{ request('event') == $ev ? 'selected' : '' }}>{{ $ev }}</option>
            @endforeach
        </select>

        @if(request()->hasAny(['event', 'user_id']))
            <a href="{{ route('admin.security.audit-logs') }}" class="text-xs px-3 py-1.5 rounded-apple"
               style="background: rgba(255,69,58,0.12); color: #FF453A; border: 1px solid rgba(255,69,58,0.25);">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        @endif

        <span class="text-xs ml-auto" style="color: rgba(235,235,245,0.4);">
            {{ $logs->total() }} log ditemukan
        </span>
    </form>

    {{-- Table --}}
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background: rgba(255,255,255,0.04); border-bottom: 1px solid rgba(255,255,255,0.07);">
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">Event</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">Model</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">Route</th>
                        <th class="px-4 py-3 text-left text-xs font-medium" style="color: rgba(235,235,245,0.45);">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $eventColors = [
                                'created'  => ['bg' => 'rgba(48,209,88,0.12)',  'text' => '#30D158'],
                                'updated'  => ['bg' => 'rgba(10,132,255,0.12)', 'text' => '#0A84FF'],
                                'deleted'  => ['bg' => 'rgba(255,69,58,0.12)',  'text' => '#FF453A'],
                                'login'    => ['bg' => 'rgba(255,159,10,0.12)', 'text' => '#FF9F0A'],
                                'logout'   => ['bg' => 'rgba(142,142,147,0.12)','text' => 'rgba(235,235,245,0.5)'],
                                '2fa_enabled'  => ['bg' => 'rgba(48,209,88,0.12)',  'text' => '#30D158'],
                                '2fa_disabled' => ['bg' => 'rgba(255,69,58,0.12)',  'text' => '#FF453A'],
                            ];
                            $ec = $eventColors[$log->event] ?? ['bg' => 'rgba(255,255,255,0.06)', 'text' => 'rgba(235,235,245,0.6)'];
                        @endphp
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <td class="px-4 py-3 text-xs font-mono whitespace-nowrap" style="color: rgba(235,235,245,0.55);">
                                {{ $log->created_at->format('d/m/y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-xs" style="color: rgba(235,235,245,0.8);">
                                {{ $log->user?->name ?? ('ID #' . $log->user_id) }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background: {{ $ec['bg'] }}; color: {{ $ec['text'] }};">
                                    {{ $log->event }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono" style="color: rgba(235,235,245,0.65);">
                                {{ class_basename($log->auditable_type ?? '') ?: '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono" style="color: rgba(235,235,245,0.45);">
                                {{ $log->auditable_id ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono max-w-xs truncate" style="color: rgba(235,235,245,0.45);" title="{{ $log->route }}">
                                {{ $log->route ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs font-mono" style="color: rgba(235,235,245,0.4);">
                                {{ $log->ip_address ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm" style="color: rgba(235,235,245,0.35);">
                                <i class="fas fa-shield-alt text-2xl mb-3 block opacity-30"></i>
                                Belum ada audit log
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-4 py-3 border-t" style="border-color: rgba(255,255,255,0.06);">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection


