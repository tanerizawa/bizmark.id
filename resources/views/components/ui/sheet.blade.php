@props([
    'name' => 'sheet',
    'title' => null,
])

{{--
    Mobile bottom sheet. Use for filter, sort, action menus on small screens.
    Open: $dispatch('sheet-open', {name: 'filter'})
    Close: $dispatch('sheet-close', {name: 'filter'})
--}}

<div
    x-data="{
        open: false,
        name: @js($name),
        openSheet(detail) { if (detail?.name === this.name) { this.open = true; document.body.style.overflow = 'hidden'; } },
        closeSheet(detail) { if (!detail || detail.name === this.name) { this.open = false; document.body.style.overflow = ''; } },
    }"
    @sheet-open.window="openSheet($event.detail)"
    @sheet-close.window="closeSheet($event.detail)"
    @keydown.escape.window="closeSheet()"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        class="portal-drawer-backdrop"
        :data-open="open"
        @click="closeSheet()"
        aria-hidden="true"
    ></div>

    {{-- Sheet --}}
    <section
        class="portal-sheet"
        :data-open="open"
        role="dialog"
        aria-modal="true"
        :aria-labelledby="`${name}-sheet-title`"
        x-trap.noscroll="open"
    >
        <div class="portal-sheet-handle" aria-hidden="true"></div>

        @if($title || isset($header))
            <div class="px-5 pt-2 pb-3 border-b border-[var(--border-subtle)] flex items-center justify-between gap-3">
                @isset($header)
                    {{ $header }}
                @else
                    <h2 :id="`${name}-sheet-title`" class="text-base font-semibold text-[var(--text-primary)]">
                        {{ $title }}
                    </h2>
                @endisset
                <button
                    type="button"
                    @click="closeSheet()"
                    class="w-8 h-8 inline-flex items-center justify-center rounded-md text-[var(--text-tertiary)] hover:bg-[var(--surface-sunken)]"
                    aria-label="Tutup"
                >
                    <i class="fas fa-xmark text-sm" aria-hidden="true"></i>
                </button>
            </div>
        @endif

        <div class="overflow-y-auto px-5 py-4 max-h-[calc(85vh-4rem)]">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="px-5 py-3 border-t border-[var(--border-subtle)] bg-[var(--surface-cool)]">
                {{ $footer }}
            </div>
        @endisset
    </section>
</div>
