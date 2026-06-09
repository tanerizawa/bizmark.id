@extends('layouts.admin')

@section('title', 'Detail Email')
@section('page-title', 'Detail Email')

@section('content')
@php
    $htmlDocument = null;
    if ($email->body_html) {
        $cleanedHtml = (string) $email->clean_body_html;
        
        // Hanya hapus tracking pixel (img 1x1)
        $cleanedHtml = preg_replace('/<img\b(?=[^>]*\bwidth\s*=\s*["\']?1["\']?)(?=[^>]*\bheight\s*=\s*["\']?1["\']?)[^>]*>/i', '', $cleanedHtml) ?? $cleanedHtml;
        
        $htmlDocument = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<base target="_blank">'
            . '<style>'
            . 'html,body{margin:0;padding:0;background:#f8f9fa;color:#111827;}'
            . 'body{font:14px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;overflow-wrap:anywhere;padding:0;}'
            . '*{box-sizing:border-box;}'
            . 'img{max-width:100%;height:auto;display:block;}'
            . 'table{border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt;}'
            . 'table td{border-collapse:collapse;}'
            . 'a{color:#0a66c2;text-decoration:none;}'
            . 'a:hover{text-decoration:underline;}'
            . 'pre{white-space:pre-wrap;word-break:break-word;}'
            . 'p{margin:0;padding:0;}'
            . 'h1,h2,h3,h4,h5,h6{margin:0;padding:0;}'
            . '.gmail_quote,.gmail_attr{display:block;}'
            . '</style></head><body>'
            . $cleanedHtml
            . '</body></html>';
    }
            
            // Tambahkan base target untuk links
            if (!preg_match('/<base\s+target=/i', $htmlDocument)) {
                $htmlDocument = preg_replace('/(<head[^>]*>)/i', '$1<base target="_blank">', $htmlDocument, 1);
            }
        } else {
            // HTML tidak lengkap, bungkus dengan struktur lengkap
            $htmlDocument = '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
                . '<base target="_blank">'
                . '<style>'
                . 'html,body{margin:0;padding:0;background:#eef2f7;color:#111827;}'
                . 'body{font:14px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;overflow-wrap:anywhere;padding:18px;}'
                . '@media (min-width: 768px){body{padding:24px;}}'
                . '*{box-sizing:border-box;}'
                . '.email-frame-inner{max-width:100%;margin:0 auto;background:#ffffff;border-radius:18px;box-shadow:0 12px 30px rgba(15,23,42,0.08);padding:18px;overflow:visible;}'
                . '@media (min-width: 768px){.email-frame-inner{padding:24px;}}'
                . 'img{max-width:100%;height:auto;}'
                . 'table{max-width:100% !important;}'
                . 'body table{width:auto;}'
                . 'a{color:#0a66c2;}'
                . 'pre{white-space:pre-wrap;word-break:break-word;}'
                . '</style></head><body><div class="email-frame-inner">'
                . $selectedHtmlBodyContent
                . '</div></body></html>';
        }
    }
@endphp
<div class="max-w-5xl mx-auto space-y-6"
     x-data="{ starred: {{ $email->is_starred ? 'true' : 'false' }}, activeView: '{{ $email->body_html ? 'html' : 'text' }}', dropdownOpen: false }">
    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.inbox.index', ['category' => request('category', 'inbox')]) }}"
           class="inline-flex items-center text-sm font-medium text-white/70 hover:text-white transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Inbox
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button"
                    @click="
                        fetch('/admin/inbox/{{ $email->id }}/star', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => { if (data.success) starred = !starred; })
                        .catch(console.error);
                    "
                    :class="starred ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/40' : 'bg-white/5 text-white/80 border border-white/10'"
                    class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold transition-apple">
                <i class="fas fa-star mr-2" :class="starred ? 'text-yellow-400' : 'text-white/60'"></i>
                <span x-text="starred ? 'Starred' : 'Star'"></span>
            </button>
            <a href="{{ route('admin.inbox.reply', $email->id) }}"
               class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold text-white bg-apple-blue transition-apple">
                <i class="fas fa-reply mr-2"></i>Balas
            </a>
            <div x-data @click.outside="dropdownOpen = false" class="relative">
                <button type="button"
                        @click="dropdownOpen = !dropdownOpen"
                        class="inline-flex items-center px-4 py-2 rounded-apple text-xs font-semibold text-white/80 bg-white/10 border border-white/15 hover:bg-white/15 transition-apple">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div x-show="dropdownOpen"
                     @click.outside="dropdownOpen = false"
                     x-cloak
                     class="absolute right-0 mt-2 w-48 card-elevated rounded-apple-lg py-2 z-20">
                    <button type="button" @click="window.print()"
                            class="w-full px-4 py-2 text-left text-xs font-semibold text-white/70 hover:bg-white/5 transition-colors">
                        <i class="fas fa-print mr-2"></i>Cetak
                    </button>
                    <form action="{{ route('admin.inbox.mark-unread', $email->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-2 text-left text-xs font-semibold text-white/70 hover:bg-white/5 transition-colors">
                            <i class="fas fa-envelope mr-2"></i> Tandai belum dibaca
                        </button>
                    </form>
                    <hr class="border-white/10 my-1">
                    @if($email->category === 'trash')
                        <form action="{{ route('admin.inbox.delete', $email->id) }}" method="POST" x-data @submit.prevent="if(confirm('Yakin ingin menghapus email ini secara PERMANEN? Tindakan ini tidak dapat dibatalkan!')) $el.submit()">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full px-4 py-2 text-left text-xs font-semibold text-red-400 hover:bg-white/5 transition-colors">
                                <i class="fas fa-times-circle mr-2"></i> Hapus Permanen
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.inbox.trash', $email->id) }}" method="POST" x-data @submit.prevent="if(confirm('Pindahkan email ini ke trash?')) $el.submit()">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 text-left text-xs font-semibold text-red-400 hover:bg-white/5 transition-colors">
                                <i class="fas fa-trash mr-2"></i> Pindahkan ke Trash
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-apple-lg px-4 py-3 flex items-center gap-3" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3); color: rgba(52,199,89,1);">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Email card --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        <div class="px-6 py-6 border-b border-white/5 space-y-4">
            <div>
                <h1 class="text-xl font-semibold text-white">{{ $email->subject }}</h1>
                @if($email->labels && count($email->labels) > 0)
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($email->labels as $label)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: rgba(10,132,255,0.15); color: rgba(10,132,255,0.9);">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-apple-blue to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($email->from_name ?? $email->from_email, 0, 2)) }}
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-widest text-white/40">From</p>
                        <p class="text-sm font-semibold text-white">{{ $email->from_name ?? $email->from_email }}</p>
                        <p class="text-xs text-white/60">{{ $email->from_email }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-semibold text-sm">
                        {{ strtoupper(substr($email->to_email, 0, 2)) }}
                    </div>
                    <div class="space-y-1 min-w-0">
                        <p class="text-xs uppercase tracking-widest text-white/40">To</p>
                        <p class="text-sm font-semibold text-white truncate">{{ $email->to_email }}</p>
                        @if($email->emailAccount)
                            <p class="text-xs text-white/60">via {{ $email->emailAccount->display_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-white/5 flex flex-wrap gap-4 text-sm text-white/70">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock text-white/40"></i>
                    <span>{{ $email->received_at->format('d M Y, H:i') }}</span>
                    <span class="text-xs text-white/40">({{ $email->received_at->diffForHumans() }})</span>
                </div>
                @if($email->has_attachments)
                    <div class="flex items-center gap-2">
                        <i class="fas fa-paperclip text-white/40"></i>
                        <span>{{ count($email->attachments ?? []) }} lampiran</span>
                    </div>
                @endif
                @if(!$email->is_read)
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                        Unread
                    </span>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="px-6 py-6">
            {{-- Toggle view buttons --}}
            @if($email->body_html && $email->body_text)
                <div class="flex gap-2 mb-4">
                    <button type="button" @click="activeView = 'html'"
                            :class="activeView === 'html' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                            class="px-4 py-2 text-xs font-semibold rounded-apple">
                        <i class="fas fa-code mr-2"></i>HTML View
                    </button>
                    <button type="button" @click="activeView = 'text'"
                            :class="activeView === 'text' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                            class="px-4 py-2 text-xs font-semibold rounded-apple">
                        <i class="fas fa-align-left mr-2"></i>Text View
                    </button>
                    <button type="button" @click="activeView = 'raw'"
                            :class="activeView === 'raw' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                            class="px-4 py-2 text-xs font-semibold rounded-apple">
                        <i class="fas fa-file-code mr-2"></i>Raw View
                    </button>
                </div>
            @elseif($email->body_html || $email->body_text)
                <div class="flex gap-2 mb-4">
                    @if($email->body_html)
                        <button type="button" @click="activeView = 'html'"
                                :class="activeView === 'html' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                                class="px-4 py-2 text-xs font-semibold rounded-apple">
                            <i class="fas fa-code mr-2"></i>HTML View
                        </button>
                    @endif
                    @if($email->body_text)
                        <button type="button" @click="activeView = 'text'"
                                :class="activeView === 'text' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                                class="px-4 py-2 text-xs font-semibold rounded-apple {{ !$email->body_html ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70' }}">
                            <i class="fas fa-align-left mr-2"></i>Text View
                        </button>
                    @endif
                    <button type="button" @click="activeView = 'raw'"
                            :class="activeView === 'raw' ? 'bg-apple-blue text-white' : 'bg-white/10 text-white/70'"
                            class="px-4 py-2 text-xs font-semibold rounded-apple">
                        <i class="fas fa-file-code mr-2"></i>Raw View (Debug)
                    </button>
                </div>
            @endif

            {{-- HTML View --}}
            @if($email->body_html)
                <div x-show="activeView === 'html'" x-transition.opacity.duration.150ms
                     class="rounded-apple-xl overflow-hidden email-html-shell">
                    <div class="email-html-meta">
                        <span class="email-html-badge">
                            <i class="fas fa-envelope-open-text mr-2"></i>Rendered HTML Email
                        </span>
                        <span class="text-xs text-slate-500">Konten diisolasi agar layout email tetap utuh</span>
                    </div>
                    <iframe
                        id="emailHtmlFrame"
                        class="email-html-frame"
                        title="Email HTML content"
                        sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin"
                        referrerpolicy="no-referrer"
                        loading="lazy"
                        scrolling="no"
                    ></iframe>
                    <script type="application/json" id="emailHtmlPayload">@json($htmlDocument)</script>
                </div>
            @endif

            {{-- Text View --}}
            @if($email->body_text)
                <div x-show="activeView === 'text'" x-transition.opacity.duration.150ms
                     class="rounded-apple-xl p-6" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                    <div class="text-sm text-white" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; white-space: pre-wrap;">{{ $email->clean_body_text }}</div>
                </div>
            @endif

            {{-- Raw View (for debugging) --}}
            <div x-show="activeView === 'raw'" x-transition.opacity.duration.150ms
                 class="rounded-apple-xl p-6" style="background: rgba(28,28,30,1); border: 1px solid rgba(255,255,255,0.06); max-height: 600px; overflow: auto;">
                <div class="mb-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: rgba(255,149,0,0.2); color: rgba(255,149,0,1);">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Debug View - Raw MIME Content
                    </span>
                </div>
                <pre class="text-xs text-white/70" style="font-family: 'Courier New', monospace; line-height: 1.4; white-space: pre-wrap; word-break: break-all;">{{ $email->raw_body }}</pre>
            </div>
        </div>

        {{-- Attachments --}}
        @if($email->attachments && count($email->attachments) > 0)
            <div class="px-6 py-5 border-t border-white/5 space-y-4">
                <h3 class="text-sm font-semibold text-white">
                    <i class="fas fa-paperclip mr-2"></i>Lampiran ({{ count($email->attachments) }})
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($email->attachments as $attachment)
                        <div class="p-4 rounded-apple border border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $attachment['filename'] ?? 'Attachment' }}</p>
                                <p class="text-xs text-white/50">{{ $attachment['content_type'] ?? 'File' }}</p>
                            </div>
                            @if(isset($attachment['download_url']))
                                <a href="{{ $attachment['download_url'] }}" class="text-xs font-semibold text-apple-blue hover:underline">
                                    Unduh
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>

<style>
.email-html-shell {
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.4);
    border-radius: 14px;
    overflow: hidden;
}

.email-html-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
}

.email-html-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.01em;
    color: #0f172a;
    background: rgba(10, 132, 255, 0.12);
}

.email-html-frame {
    display: block;
    width: 100%;
    min-height: 220px;
    border: 0;
    background: #ffffff;
    overflow: hidden;
}
</style>

<script>
const emailFrame = document.getElementById('emailHtmlFrame');
const emailPayloadNode = document.getElementById('emailHtmlPayload');

function applyEmailFrameHeight(nextHeight) {
    if (!emailFrame) {
        return;
    }

    if (!Number.isFinite(nextHeight)) {
        return;
    }

    emailFrame.style.height = `${Math.max(220, Math.ceil(nextHeight) + 2)}px`;
}

function measureEmailFrameHeight() {
    if (!emailFrame || !emailFrame.contentDocument) {
        return null;
    }

    const doc = emailFrame.contentDocument;
    const body = doc.body;
    const html = doc.documentElement;

    if (!body || !html) {
        return null;
    }

    const lastElement = body.lastElementChild;
    const lastElementBottom = lastElement
        ? lastElement.getBoundingClientRect().bottom + body.getBoundingClientRect().top
        : 0;

    return Math.max(
        body.scrollHeight,
        body.offsetHeight,
        body.clientHeight,
        html.scrollHeight,
        html.offsetHeight,
        html.clientHeight,
        lastElementBottom
    );
}

function syncEmailFrameHeight() {
    const measuredHeight = measureEmailFrameHeight();
    if (measuredHeight) {
        applyEmailFrameHeight(measuredHeight);
    }
}

function scheduleEmailFrameResizes() {
    [0, 60, 180, 360, 700, 1200, 2200, 4000].forEach((delay) => {
        window.setTimeout(syncEmailFrameHeight, delay);
    });
}

function bindEmailFrameObservers() {
    if (!emailFrame || !emailFrame.contentDocument) {
        return;
    }

    const doc = emailFrame.contentDocument;

    doc.querySelectorAll('img').forEach((img) => {
        if (!img.complete) {
            img.addEventListener('load', syncEmailFrameHeight);
            img.addEventListener('error', syncEmailFrameHeight);
        }
    });

    if (doc.fonts?.ready) {
        doc.fonts.ready.then(syncEmailFrameHeight).catch(() => {});
    }

    if (window.ResizeObserver && doc.body) {
        const resizeObserver = new ResizeObserver(syncEmailFrameHeight);
        resizeObserver.observe(doc.body);
        if (doc.documentElement) {
            resizeObserver.observe(doc.documentElement);
        }
    }
}

if (emailFrame && emailPayloadNode) {
    const payload = JSON.parse(emailPayloadNode.textContent || 'null');
    if (payload) {
        applyEmailFrameHeight(220);
        emailFrame.addEventListener('load', () => {
            bindEmailFrameObservers();
            syncEmailFrameHeight();
            scheduleEmailFrameResizes();
        });
        emailFrame.srcdoc = payload;
    }
}

</script>
@endsection
