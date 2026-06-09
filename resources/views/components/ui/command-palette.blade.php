@props([
    'name' => 'portal',
])

@php
    // Default command set — extendable via slot or window.portalCommands
    $commands = [
        ['id' => 'goto-dashboard',      'label' => 'Buka Beranda',                'group' => 'Navigasi', 'icon' => 'fas fa-house',         'shortcut' => 'g d', 'url' => route('client.dashboard')],
        ['id' => 'goto-applications',   'label' => 'Permohonan Saya',             'group' => 'Navigasi', 'icon' => 'fas fa-file-signature','shortcut' => 'g a', 'url' => route('client.applications.index')],
        ['id' => 'goto-projects',       'label' => 'Proyek Aktif',                'group' => 'Navigasi', 'icon' => 'fas fa-diagram-project','shortcut' => 'g p', 'url' => route('client.projects.index')],
        ['id' => 'goto-services',       'label' => 'Katalog Layanan',             'group' => 'Navigasi', 'icon' => 'fas fa-layer-group',   'shortcut' => 'g s', 'url' => route('client.services.index')],
        ['id' => 'goto-vault',          'label' => 'Document Vault',              'group' => 'Arsip',    'icon' => 'fas fa-folder-tree',                       'url' => route('client.vault.index')],
        ['id' => 'goto-compliance',     'label' => 'Compliance Monitor',          'group' => 'Arsip',    'icon' => 'fas fa-shield-halved',                     'url' => route('client.compliance.index')],
        ['id' => 'goto-oss',            'label' => 'OSS-RBA Tracker',             'group' => 'Arsip',    'icon' => 'fas fa-satellite-dish',                    'url' => route('client.oss-tracker.index')],
        ['id' => 'goto-notifications',  'label' => 'Notifikasi',                  'group' => 'Navigasi', 'icon' => 'fas fa-bell',                              'url' => route('client.notifications.index')],
        ['id' => 'goto-profile',        'label' => 'Profil & Perusahaan',         'group' => 'Pengaturan','icon' => 'fas fa-user-gear',                        'url' => route('client.profile.edit')],
        ['id' => 'create-application',  'label' => 'Ajukan Permohonan Baru',      'group' => 'Aksi',     'icon' => 'fas fa-plus',          'shortcut' => 'c',   'url' => route('client.applications.create')],
        ['id' => 'upload-document',     'label' => 'Upload Dokumen',              'group' => 'Aksi',     'icon' => 'fas fa-upload',                            'url' => route('client.documents.index')],
        ['id' => 'theme-toggle',        'label' => 'Ganti Tema (Terang/Gelap)',   'group' => 'Pengaturan','icon' => 'fas fa-moon',          'action' => 'theme'],
        ['id' => 'logout',              'label' => 'Keluar',                      'group' => 'Akun',     'icon' => 'fas fa-right-from-bracket','action' => 'logout'],
    ];
@endphp

<div
    x-data="portalCmdk(@js($commands))"
    @keydown.window.prevent.cmd.k="open()"
    @keydown.window.prevent.ctrl.k="open()"
    @keydown.window.prevent.slash="if (!$event.target.matches('input,textarea,[contenteditable]')) open()"
    @cmdk-open.window="open()"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        x-show="isOpen"
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-[69] bg-[rgba(15,23,42,0.45)] backdrop-blur-sm"
        @click="close()"
        aria-hidden="true"
    ></div>

    {{-- Modal --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="portal-cmdk"
        role="dialog"
        aria-modal="true"
        aria-label="Command palette"
        x-trap.noscroll.inert="isOpen"
        @keydown.escape.prevent="close()"
        @keydown.arrow-down.prevent="moveCursor(1)"
        @keydown.arrow-up.prevent="moveCursor(-1)"
        @keydown.enter.prevent="execute()"
    >
        {{-- Search input --}}
        <div class="flex items-center gap-3 px-4 py-3 border-b border-[var(--border-subtle)]">
            <i class="fas fa-magnifying-glass text-[var(--text-tertiary)]" aria-hidden="true"></i>
            <input
                x-ref="search"
                x-model="query"
                @input="onQueryInput()"
                type="text"
                placeholder="Cari proyek, dokumen, permohonan, KBLI…"
                class="flex-1 bg-transparent outline-none text-sm text-[var(--text-primary)] placeholder:text-[var(--text-tertiary)]"
                autocomplete="off"
                spellcheck="false"
            />
            <template x-if="liveLoading">
                <i class="fas fa-circle-notch fa-spin text-xs text-[var(--text-tertiary)]" aria-hidden="true"></i>
            </template>
            <span class="hidden sm:inline-flex">
                <kbd>Esc</kbd>
            </span>
        </div>

        {{-- Results --}}
        <div class="overflow-y-auto py-1 max-h-[calc(70vh-110px)]">
            <template x-if="filtered.length === 0">
                <div class="px-4 py-10 text-center">
                    <p class="text-sm text-[var(--text-secondary)]">Tidak ada hasil untuk
                        "<span class="font-medium text-[var(--text-primary)]" x-text="query"></span>"
                    </p>
                    <p class="text-xs text-[var(--text-tertiary)] mt-1">Coba kata kunci lain.</p>
                </div>
            </template>

            <template x-for="(group, gi) in groupedFiltered" :key="gi">
                <div>
                    <div class="px-4 pt-3 pb-1.5 text-[10px] uppercase tracking-wider font-semibold text-[var(--text-tertiary)]"
                         x-text="group.name"></div>
                    <ul role="listbox">
                        <template x-for="(item, idx) in group.items" :key="item.id">
                            <li
                                role="option"
                                :aria-selected="globalIndex(item) === cursor"
                                @click="executeItem(item)"
                                @mouseenter="cursor = globalIndex(item)"
                                :class="globalIndex(item) === cursor
                                    ? 'bg-[var(--client-primary-light)] text-[var(--client-primary)]'
                                    : 'text-[var(--text-primary)] hover:bg-[var(--surface-sunken)]'"
                                class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                            >
                                <i :class="item.icon" class="w-4 text-center text-sm" aria-hidden="true"></i>
                                <div class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium truncate" x-text="item.label"></span>
                                    <template x-if="item.meta">
                                        <span class="block text-[11px] leading-4 text-[var(--text-tertiary)] truncate" x-text="item.meta"></span>
                                    </template>
                                </div>
                                <template x-if="item.shortcut">
                                    <span class="text-[10px] text-[var(--text-tertiary)] font-mono uppercase" x-text="item.shortcut"></span>
                                </template>
                            </li>
                        </template>
                    </ul>
                </div>
            </template>
        </div>

        {{-- Footer hints --}}
        <div class="flex items-center justify-between px-4 py-2 border-t border-[var(--border-subtle)] bg-[var(--surface-cool)] text-[11px] text-[var(--text-tertiary)]">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1"><kbd>↑</kbd><kbd>↓</kbd> Navigasi</span>
                <span class="inline-flex items-center gap-1"><kbd>↵</kbd> Pilih</span>
            </div>
            <span class="hidden sm:inline-flex items-center gap-1">
                <kbd x-text="isMac ? '⌘' : 'Ctrl'"></kbd><kbd>K</kbd> Buka
            </span>
        </div>
    </div>
</div>
