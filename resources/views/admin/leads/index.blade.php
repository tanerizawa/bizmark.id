@extends('layouts.admin')

@section('title', 'Lead Management')
@section('page-title', 'Kelola Lead & Inquiry')

@section('content')
<div class="flex flex-col gap-4">

    {{-- Page Header --}}
    <div>
        <p class="lead-header-breadcrumb">Manajemen Prospek</p>
        <h1 class="lead-header-title">Lead & Inquiry</h1>
        <p class="lead-header-desc">Kelola semua masukan prospek, inquiry layanan, dan permohonan biaya dari calon klien.</p>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="flex items-center justify-between p-3" style="background:rgba(52,199,89,0.1);border:1px solid rgba(52,199,89,0.3);border-radius:12px">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-apple-green"></i>
                <span class="text-sm font-semibold text-apple-green">{{ session('success') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="bg-transparent border-none cursor-pointer text-apple-green opacity-70 hover:opacity-100 p-1 rounded-lg transition-opacity" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center justify-between p-3" style="background:rgba(255,59,48,0.1);border:1px solid rgba(255,59,48,0.3);border-radius:12px">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-exclamation-circle text-apple-red"></i>
                <span class="text-sm font-semibold text-apple-red">{{ session('error') }}</span>
            </div>
            <button @click="$el.parentElement.remove()" class="bg-transparent border-none cursor-pointer text-apple-red opacity-70 hover:opacity-100 p-1 rounded-lg transition-opacity" aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Tab Navigation + Content --}}
    <div x-data="leadTabs()" class="bg-dark-bg-secondary border border-dark-separator" style="border-radius:18px;overflow:hidden">

        {{-- Tab Bar --}}
        <div class="lead-tab-bar" role="tablist" @keydown.left.prevent="$event.shiftKey ? focusFirstTab() : focusPrevTab()" @keydown.right.prevent="$event.shiftKey ? focusLastTab() : focusNextTab()" @keydown.home.prevent="focusFirstTab()" @keydown.end.prevent="focusLastTab()">
            <div class="lead-tab-list">
            @php
                $tabs = [
                    'service-inquiries'    => ['icon'=>'fa-envelope',      'label'=>'Service Inquiries',    'color'=>'var(--apple-blue)',   'count'=>$serviceInquiriesCount],
                    'consultation-leads'   => ['icon'=>'fa-calculator',    'label'=>'Consultation Leads',   'color'=>'var(--apple-yellow)', 'count'=>$consultationLeadsCount],
                    'service-cost-requests'=> ['icon'=>'fa-file-signature','label'=>'Permohonan Biaya',     'color'=>'var(--apple-orange)', 'count'=>$serviceCostRequestsCount ?? 0],
                ];
            @endphp
            @foreach($tabs as $tabKey => $tab)
                @php
                    $isActive = $activeTab === $tabKey;
                    $tabColor = $tab['color'];
                    $bgActive = 'color-mix(in srgb,'.$tabColor.' 18%,transparent)';
                @endphp
                <a href="{{ route('admin.leads.index', ['tab' => $tabKey]) }}"
                   role="tab"
                   aria-selected="{{ $isActive ? 'true' : 'false' }}"
                   aria-controls="tabpanel-{{ $tabKey }}"
                   id="tab-{{ $tabKey }}"
                   tabindex="{{ $isActive ? '0' : '-1' }}"
                   class="lead-tab @if($isActive) active @endif"
                   @if($isActive) style="color:{{ $tabColor }};border-bottom-color:{{ $tabColor }}" @endif
                   @click="activateTab('{{ $tabKey }}')">
                    <span class="lead-tab-icon-wrap @if($isActive) active @endif" @if($isActive) style="background:{{ $bgActive }};color:{{ $tabColor }}" @endif>
                        <i class="fas {{ $tab['icon'] }}"></i>
                    </span>
                    {{ $tab['label'] }}
                    @if($tab['count'] > 0)
                        <span class="lead-tab-count @if($isActive) active @endif"
                              @if($isActive) style="background:{{ $tabColor }};color:{{ $tabColor === 'var(--apple-yellow)' ? '#000' : '#fff' }}" @endif>
                            {{ $tab['count'] > 99 ? '99+' : $tab['count'] }}
                        </span>
                    @endif
                </a>
            @endforeach
            </div>
            {{-- Export button aligned right in tab bar --}}
            <div class="flex items-center py-2.5 pl-4 flex-shrink-0">
                @if($activeTab === 'service-inquiries')
                    <a href="{{ route('admin.service-inquiries.export', request()->all()) }}" class="lead-export-btn">
                        <i class="fas fa-download"></i>Export CSV
                    </a>
                @elseif($activeTab === 'consultation-leads')
                    <a href="{{ route('admin.consultation-leads.export', request()->all()) }}" class="lead-export-btn">
                        <i class="fas fa-download"></i>Export CSV
                    </a>
                @endif
            </div>
        </div>

        {{-- Active Tab Content --}}
        <div style="padding:20px" role="tabpanel" id="tabpanel-{{ $activeTab }}" aria-labelledby="tab-{{ $activeTab }}">
            @if($activeTab === 'service-inquiries')
                @include('admin.leads.tabs.service-inquiries')
            @elseif($activeTab === 'consultation-leads')
                @include('admin.leads.tabs.consultation-leads')
            @elseif($activeTab === 'service-cost-requests')
                @include('admin.leads.tabs.service-cost-requests')
            @endif
        </div>
    </div>

</div>

{{-- Convert to Client Modal — Alpine v3 + x-teleport --}}
<template x-teleport="body">
    <div
        x-data="{ isOpen: false, convertUrl: '' }"
        @open-convert-modal.window="isOpen = true; convertUrl = $event.detail.url"
        x-show="isOpen"
        x-cloak
        class="fixed inset-0 z-[100]"
        style="display:none;"
    >
        <div x-show="isOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm"
             @click="isOpen = false"
             aria-hidden="true"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.outside="isOpen = false"
                 @keydown.escape.window="isOpen = false"
                 class="bg-dark-bg-secondary border border-dark-separator relative rounded-2xl shadow-2xl w-full max-w-md"
                 role="dialog" aria-modal="true" aria-labelledby="convert-modal-title">

                <div class="flex items-center justify-between px-6 py-4 border-b border-dark-separator">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-dark-text-secondary m-0">Lead Management</p>
                        <h2 id="convert-modal-title" class="text-base font-bold text-dark-text-primary mt-0.5 m-0">
                            Konversi ke Klien
                        </h2>
                    </div>
                    <button @click="isOpen = false"
                            class="p-2 rounded-lg bg-transparent border-none text-dark-text-secondary cursor-pointer transition-colors hover:bg-dark-bg-tertiary hover:text-dark-text-primary"
                            aria-label="Tutup">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="px-6 py-5">
                    <p class="text-sm text-dark-text-secondary mb-5">
                        Konversi consultation lead ini menjadi akun klien terdaftar. Sistem akan membuat akun klien dan proyek perizinan secara otomatis.
                    </p>
                    <form id="convertForm" method="POST" :action="convertUrl" class="space-y-4">
                        @csrf
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="checkbox" name="create_client_account" value="1" checked
                                   class="w-4 h-4 rounded border-dark-separator bg-dark-bg-tertiary accent-[var(--color-primary)] cursor-pointer">
                            <span class="text-sm font-medium text-dark-text-primary">
                                Buat akun klien baru
                            </span>
                        </label>
                        <x-ui.input name="password" type="password" label="Password untuk klien" required />
                        <x-ui.input name="company_name" label="Nama perusahaan (opsional)" />
                    </form>
                </div>

                <div class="flex items-center justify-end gap-3 px-6 py-3.5 border-t border-dark-separator">
                    <x-ui.button variant="ghost" size="sm" @click="isOpen = false">Batal</x-ui.button>
                    <x-ui.button type="submit" size="sm" form="convertForm">
                        <i class="fas fa-user-plus mr-1.5"></i>Konversi
                    </x-ui.button>
                </div>
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
function showConvertModal(convertUrl) {
    window.dispatchEvent(new CustomEvent('open-convert-modal', { detail: { url: convertUrl } }));
}

document.addEventListener('alpine:init', () => {
    Alpine.data('leadTabs', () => ({
        activeTab: @js($activeTab),
        activateTab(tabKey) {
            window.location.href = '{{ route('admin.leads.index') }}?tab=' + tabKey;
        },
        focusPrevTab() {
            const tabs = Array.from(this.$el.querySelectorAll('[role="tab"]'));
            const current = tabs.findIndex(t => t.getAttribute('aria-selected') === 'true');
            const prev = (current - 1 + tabs.length) % tabs.length;
            tabs[prev].focus();
        },
        focusNextTab() {
            const tabs = Array.from(this.$el.querySelectorAll('[role="tab"]'));
            const current = tabs.findIndex(t => t.getAttribute('aria-selected') === 'true');
            const next = (current + 1) % tabs.length;
            tabs[next].focus();
        },
        focusFirstTab() {
            const tabs = this.$el.querySelectorAll('[role="tab"]');
            tabs[0].focus();
        },
        focusLastTab() {
            const tabs = this.$el.querySelectorAll('[role="tab"]');
            tabs[tabs.length - 1].focus();
        }
    }));
});
</script>
@endpush
@endsection
