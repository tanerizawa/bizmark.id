@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="space-y-4">
    {{-- Compact Hero Section --}}
    <section class="card-elevated rounded-apple-lg admin-hero relative overflow-hidden mb-4">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-48 h-48 bg-apple-orange opacity-20 blur-3xl rounded-full absolute -top-10 -right-6"></div>
            <div class="w-32 h-32 bg-apple-red opacity-15 blur-2xl rounded-full absolute bottom-0 left-6"></div>
        </div>
        <div class="relative space-y-1 max-w-3xl">
            <p class="admin-label-compact">Konfigurasi Sistem</p>
            <h1 class="admin-hero-title">Pengaturan Aplikasi</h1>
            <p class="admin-body" style="color: rgba(235,235,245,0.75);">Kelola konfigurasi bisnis, pengguna, keamanan, dan preferensi sistem secara terpusat.</p>
        </div>
    </section>

    @if(session('success'))
        <div class="mb-3 p-2 rounded-apple" style="background: rgba(52, 199, 89, 0.1); border-left: 3px solid rgba(52, 199, 89, 1);">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-xs" style="color: rgba(52, 199, 89, 1);"></i>
                <span class="admin-body" style="color: rgba(52, 199, 89, 1);">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-3 p-2 rounded-apple" style="background: rgba(255, 59, 48, 0.1); border-left: 3px solid rgba(255, 59, 48, 1);">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xs" style="color: rgba(255, 59, 48, 1);"></i>
                <span class="admin-body" style="color: rgba(255, 59, 48, 1);">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabs Container -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b" style="border-color: rgba(84, 84, 88, 0.65);">
            <div class="flex space-x-0.5 p-1.5 overflow-x-auto" role="tablist">
                <a href="{{ route('settings.index', ['tab' => 'general']) }}" 
                   class="tab-button {{ $activeTab === 'general' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-building mr-1.5"></i>Umum
                </a>
                <a href="{{ route('settings.index', ['tab' => 'users']) }}" 
                   class="tab-button {{ $activeTab === 'users' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-users mr-1.5"></i>Pengguna
                </a>
                <a href="{{ route('settings.index', ['tab' => 'roles']) }}" 
                   class="tab-button {{ $activeTab === 'roles' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-user-shield mr-1.5"></i>Peran
                </a>
                <a href="{{ route('settings.index', ['tab' => 'financial']) }}" 
                   class="tab-button {{ $activeTab === 'financial' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-wallet mr-1.5"></i>Keuangan
                </a>
                <a href="{{ route('settings.index', ['tab' => 'project']) }}" 
                   class="tab-button {{ $activeTab === 'project' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-project-diagram mr-1.5"></i>Proyek
                </a>
                <a href="{{ route('settings.index', ['tab' => 'security']) }}" 
                   class="tab-button {{ $activeTab === 'security' ? 'active' : '' }} px-3 py-1.5 rounded text-xs font-medium transition-apple whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-1.5"></i>Keamanan
                </a>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="p-4">
            @if($activeTab === 'general')
                @include('settings.tabs.general', ['setting' => $businessSetting])
            @elseif($activeTab === 'users')
                @include('settings.tabs.users', ['users' => $users, 'roles' => $roles])
            @elseif($activeTab === 'roles')
                @include('settings.tabs.roles', ['roles' => $roles, 'permissions' => $permissions])
            @elseif($activeTab === 'financial')
                @include('settings.tabs.financial', [
                    'expenseCategories' => $expenseCategories,
                    'paymentMethods' => $paymentMethods,
                    'taxRates' => $taxRates,
                ])
            @elseif($activeTab === 'project')
                @include('settings.tabs.project', ['statuses' => $projectStatuses])
            @elseif($activeTab === 'security')
                @include('settings.tabs.security', ['securitySetting' => $securitySetting])
            @endif
        </div>
    </div>
</div>
@endsection
