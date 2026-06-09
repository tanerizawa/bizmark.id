@extends('layouts.admin')

@section('title', 'Konsultasi Gratis')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-dark-text-primary mb-1">
                Service Inquiries
            </h1>
            <p class="text-sm text-dark-text-secondary">Kelola leads dari konsultasi gratis AI</p>
        </div>
        <a href="{{ route('admin.service-inquiries.export', request()->all()) }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium inline-flex items-center">
            <i class="fas fa-download mr-2"></i>
            Export CSV
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(52, 199, 89, 0.15); border: 1px solid var(--apple-green);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3" style="color: var(--apple-green);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-green);">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-green); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4 mb-4" style="background-color: rgba(255, 59, 48, 0.15); border: 1px solid var(--apple-red);">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3" style="color: var(--apple-red);"></i>
                    <span class="text-sm font-medium" style="color: var(--apple-red);">{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-sm" style="color: var(--apple-red); opacity: 0.6;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-6">
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-lg font-bold text-white mb-1">{{ $stats['total'] }}</div>
            <div class="text-xs text-dark-text-secondary">Total</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-blue-400 mb-1">{{ $stats['new'] }}</div>
            <div class="text-xs text-dark-text-secondary">Baru</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-indigo-400 mb-1">{{ $stats['analyzed'] }}</div>
            <div class="text-xs text-dark-text-secondary">Dianalisis</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-green-400 mb-1">{{ $stats['contacted'] }}</div>
            <div class="text-xs text-dark-text-secondary">Dihubungi</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-purple-400 mb-1">{{ $stats['converted'] }}</div>
            <div class="text-xs text-dark-text-secondary">Konversi</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-red-400 mb-1">{{ $stats['high_priority'] }}</div>
            <div class="text-xs text-dark-text-secondary">High Priority</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['this_week'] }}</div>
            <div class="text-xs text-dark-text-secondary">Minggu Ini</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-orange-400 mb-1">{{ $stats['this_month'] }}</div>
            <div class="text-xs text-dark-text-secondary">Bulan Ini</div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card-elevated rounded-apple-lg mb-4">
        <div class="px-4 py-3" style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
            <h3 class="text-base font-semibold text-white">Pencarian & Filter</h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('admin.service-inquiries.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           class="w-full px-3 py-2 rounded-apple text-sm"
                           placeholder="Cari nomor inquiry, email, perusahaan, nama kontak..." 
                           value="{{ request('search') }}"
                           style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                </div>
                <div>
                    <select name="status" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Diproses</option>
                        <option value="analyzed" {{ request('status') == 'analyzed' ? 'selected' : '' }}>Dianalisis</option>
                        <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Dihubungi</option>
                        <option value="qualified" {{ request('status') == 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Konversi</option>
                        <option value="registered" {{ request('status') == 'registered' ? 'selected' : '' }}>Terdaftar</option>
                        <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div>
                    <select name="priority" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Prioritas</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.service-inquiries.index') }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium">
                        Reset
                    </a>
                </div>
            </form>
            
            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('admin.service-inquiries.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="priority" value="{{ request('priority') }}">
                <div>
                    <label class="text-xs text-dark-text-secondary mb-1 block">Dari Tanggal</label>
                    <input type="date" 
                           name="date_from" 
                           class="w-full px-3 py-2 rounded-apple text-sm"
                           value="{{ request('date_from') }}"
                           style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                </div>
                <div>
                    <label class="text-xs text-dark-text-secondary mb-1 block">Sampai Tanggal</label>
                    <input type="date" 
                           name="date_to" 
                           class="w-full px-3 py-2 rounded-apple text-sm"
                           value="{{ request('date_to') }}"
                           style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium w-full">
                        Filter Tanggal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Service Inquiries Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Inquiry #
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Perusahaan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Prioritas
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Est. Value
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-separator">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-dark-bg-tertiary transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-mono text-dark-text-primary">{{ $inquiry->inquiry_number }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-dark-text-secondary">
                                {{ $inquiry->created_at->format('d M Y') }}<br>
                                <span class="text-xs">{{ $inquiry->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-dark-text-primary">{{ $inquiry->company_name }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ $inquiry->company_type ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-dark-text-primary">{{ $inquiry->contact_person }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ $inquiry->email }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ $inquiry->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusColors = [
                                        'new' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                        'processing' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                        'analyzed' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                                        'contacted' => 'bg-green-500/20 text-green-400 border-green-500',
                                        'qualified' => 'bg-teal-500/20 text-teal-400 border-teal-500',
                                        'converted' => 'bg-purple-500/20 text-purple-400 border-purple-500',
                                        'registered' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500',
                                        'lost' => 'bg-red-500/20 text-red-400 border-red-500',
                                    ];
                                    $statusLabels = [
                                        'new' => 'Baru',
                                        'processing' => 'Diproses',
                                        'analyzed' => 'Dianalisis',
                                        'contacted' => 'Dihubungi',
                                        'qualified' => 'Qualified',
                                        'converted' => 'Konversi',
                                        'registered' => 'Terdaftar',
                                        'lost' => 'Lost',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $statusColors[$inquiry->status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                    {{ $statusLabels[$inquiry->status] ?? ucfirst($inquiry->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $priorityColors = [
                                        'high' => 'bg-red-500/20 text-red-400 border-red-500',
                                        'medium' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                        'low' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                    ];
                                @endphp
                                @if($inquiry->priority)
                                    <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $priorityColors[$inquiry->priority] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                        {{ ucfirst($inquiry->priority) }}
                                    </span>
                                @else
                                    <span class="text-dark-text-secondary text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-dark-text-primary">
                                @if($inquiry->estimated_value)
                                    Rp {{ number_format($inquiry->estimated_value / 1000000, 0) }}M
                                @else
                                    <span class="text-dark-text-secondary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="{{ route('admin.service-inquiries.show', $inquiry) }}" class="text-apple-blue hover:text-blue-400 text-sm font-medium">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-dark-text-secondary">
                                <i class="fas fa-inbox text-2xl mb-3 block opacity-30"></i>
                                <p>Tidak ada inquiry ditemukan</p>
                                @if(request()->hasAny(['search', 'status', 'priority', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.service-inquiries.index') }}" class="text-apple-blue hover:text-blue-400 text-sm mt-2 inline-block">
                                        Reset Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($inquiries->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                {{ $inquiries->appends(request()->all())->links() }}
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-4 rounded-apple-lg p-4" style="background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-apple-blue mr-3 mt-0.5"></i>
            <div class="text-sm text-dark-text-secondary">
                <p class="font-medium text-apple-blue mb-1">Tentang Service Inquiries</p>
                <p>Data inquiry dari formulir konsultasi gratis AI di landing page. Gunakan fitur ini untuk tracking dan konversi leads menjadi klien terdaftar.</p>
            </div>
        </div>
    </div>
</div>
@endsection
