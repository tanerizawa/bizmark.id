@extends('layouts.admin')

@section('title', 'Email Accounts Management')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto" x-data="{ deleteOpen: false, deleteForm: '' }">

    {{-- Header --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-at text-blue-400"></i>Email Accounts
            </h1>
            <p class="text-gray-400 mt-1">Manage company email accounts and user assignments</p>
        </div>
        <a href="{{ route('admin.email-accounts.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>New Email Account
        </a>
    </div>

    {{-- Stats --}}
    @php
        $statCards = [
            ['label' => 'Total Accounts', 'value' => $stats['total'] ?? 0, 'icon' => 'fa-at', 'color' => 'bg-blue-600'],
            ['label' => 'Shared Accounts', 'value' => $stats['shared'] ?? 0, 'icon' => 'fa-users', 'color' => 'bg-green-600'],
            ['label' => 'Personal Accounts', 'value' => $stats['personal'] ?? 0, 'icon' => 'fa-user', 'color' => 'bg-purple-600'],
            ['label' => 'Active Users', 'value' => $stats['active_users'] ?? 0, 'icon' => 'fa-user-check', 'color' => 'bg-orange-500'],
        ];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($statCards as $card)
        <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 mb-1">{{ $card['label'] }}</p>
                <p class="text-2xl font-bold text-white">{{ $card['value'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-full {{ $card['color'] }} flex items-center justify-center">
                <i class="fas {{ $card['icon'] }} text-white"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('admin.email-accounts.index') }}" id="filterForm">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by email or name..."
                       class="lg:col-span-2 bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                @foreach(['type' => ['All Types', 'shared' => 'Shared', 'personal' => 'Personal'],
                          'department' => ['All Departments', 'cs' => 'Customer Service', 'sales' => 'Sales', 'support' => 'Support', 'finance' => 'Finance'],
                          'status' => ['All Status', 'active' => 'Active', 'inactive' => 'Inactive']] as $name => $opts)
                <select name="{{ $name }}" onchange="document.getElementById('filterForm').submit()"
                        class="bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($opts as $val => $label)
                        @if(is_int($val))
                            <option value="">{{ $label }}</option>
                        @else
                            <option value="{{ $val }}" {{ request($name) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endif
                    @endforeach
                </select>
                @endforeach

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-400 border-b border-gray-700">
                        <th class="px-5 py-3 font-medium">Email Address</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Department</th>
                        <th class="px-5 py-3 font-medium">Assigned Users</th>
                        <th class="px-5 py-3 font-medium">Emails</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($emailAccounts as $account)
                    <tr class="hover:bg-gray-700/40 text-gray-300">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-white text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $account->email }}</p>
                                    @if($account->forward_to)
                                    <p class="text-gray-400 text-xs"><i class="fas fa-arrow-right mr-1"></i>{{ $account->forward_to }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-white">{{ $account->name }}</td>
                        <td class="px-5 py-3">
                            @if($account->type === 'shared')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                    <i class="fas fa-users"></i>Shared
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/20 text-purple-400">
                                    <i class="fas fa-user"></i>Personal
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300">{{ ucfirst($account->department ?? 'N/A') }}</span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-white">{{ $account->users->count() }}</span>
                                @if($account->users->count() > 0)
                                <div class="flex -space-x-2">
                                    @foreach($account->users->take(3) as $user)
                                    <div class="w-6 h-6 rounded-full bg-blue-600 border-2 border-gray-800 text-white flex items-center justify-center text-xs font-medium"
                                         title="{{ $user->name }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    @endforeach
                                    @if($account->users->count() > 3)
                                    <div class="w-6 h-6 rounded-full bg-gray-600 border-2 border-gray-800 text-white flex items-center justify-center text-xs">
                                        +{{ $account->users->count() - 3 }}
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3 text-white">
                            <span class="text-green-400"><i class="fas fa-arrow-down mr-1"></i>{{ $account->total_received ?? 0 }}</span>
                            <span class="mx-1 text-gray-600">|</span>
                            <span class="text-blue-400"><i class="fas fa-arrow-up mr-1"></i>{{ $account->total_sent ?? 0 }}</span>
                        </td>
                        <td class="px-5 py-3">
                            @if($account->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                    <i class="fas fa-check-circle"></i>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                    <i class="fas fa-times-circle"></i>Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.email-accounts.show', $account) }}" title="View"
                                   class="p-1.5 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 rounded-lg text-xs transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.email-accounts.edit', $account) }}" title="Edit"
                                   class="p-1.5 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-400 rounded-lg text-xs transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" title="Delete"
                                        @click="deleteForm = '/admin/email-accounts/{{ $account->id }}'; deleteOpen = true"
                                        class="p-1.5 border border-red-700 text-red-400 hover:bg-red-900/30 rounded-lg text-xs transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <i class="fas fa-inbox text-3xl text-gray-600 mb-3 block"></i>
                            <p class="text-gray-400">No email accounts found</p>
                            @if(request()->hasAny(['search', 'type', 'department', 'status']))
                            <a href="{{ route('admin.email-accounts.index') }}"
                               class="mt-3 inline-flex items-center px-4 py-2 border border-gray-600 text-gray-300 hover:text-white text-sm rounded-lg transition">
                                <i class="fas fa-times mr-2"></i>Clear Filters
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($emailAccounts->hasPages())
    <div class="mt-4 flex justify-center">
        {{ $emailAccounts->links() }}
    </div>
    @endif

    {{-- Delete Modal --}}
    <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70"
         @keydown.escape.window="deleteOpen = false">
        <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-2xl w-full max-w-md" @click.outside="deleteOpen = false">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-700">
                <h5 class="text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>Confirm Delete
                </h5>
                <button @click="deleteOpen = false" class="text-gray-400 hover:text-white text-xl">&times;</button>
            </div>
            <div class="p-5">
                <p class="text-white mb-2">Are you sure you want to delete this email account?</p>
                <p class="text-red-400 text-sm"><i class="fas fa-info-circle mr-1"></i>This action cannot be undone.</p>
            </div>
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-700">
                <button @click="deleteOpen = false"
                    class="px-4 py-2 border border-gray-600 text-gray-300 hover:text-white rounded-lg text-sm transition">Cancel</button>
                <form :action="deleteForm" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        <i class="fas fa-trash mr-1.5"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
