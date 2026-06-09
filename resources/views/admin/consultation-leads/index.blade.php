@extends('layouts.admin')

@section('title', 'Consultation Leads')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl font-semibold text-dark-text-primary mb-1">
                Consultation Leads
            </h1>
            <p class="text-sm text-dark-text-secondary">Kelola leads dari estimasi biaya perizinan AI</p>
        </div>
        <a href="{{ route('admin.consultation-leads.export', request()->all()) }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium inline-flex items-center">
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
            <div class="text-2xl font-bold text-green-400 mb-1">{{ $stats['contacted'] }}</div>
            <div class="text-xs text-dark-text-secondary">Dihubungi</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-purple-400 mb-1">{{ $stats['converted'] }}</div>
            <div class="text-xs text-dark-text-secondary">Konversi</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-yellow-400 mb-1">{{ $stats['pending_review'] }}</div>
            <div class="text-xs text-dark-text-secondary">Perlu Review</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-red-400 mb-1">{{ $stats['high_value'] }}</div>
            <div class="text-xs text-dark-text-secondary">High Value</div>
        </div>
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="text-2xl font-bold text-indigo-400 mb-1">{{ $stats['this_week'] }}</div>
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
            <form method="GET" action="{{ route('admin.consultation-leads.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="md:col-span-2">
                    <input type="text" 
                           name="search" 
                           class="w-full px-3 py-2 rounded-apple text-sm"
                           placeholder="Cari ID, email, nama, perusahaan..." 
                           value="{{ request('search') }}"
                           style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                </div>

                <div>
                    <select name="status" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Semua Status</option>
                        <option value="auto_estimated" {{ request('status') === 'auto_estimated' ? 'selected' : '' }}>Auto Estimated</option>
                        <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="quoted" {{ request('status') === 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <select name="contacted" 
                            class="w-full px-3 py-2 rounded-apple text-sm"
                            style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
                        <option value="">Status Kontak</option>
                        <option value="yes" {{ request('contacted') === 'yes' ? 'selected' : '' }}>Sudah Dihubungi</option>
                        <option value="no" {{ request('contacted') === 'no' ? 'selected' : '' }}>Belum Dihubungi</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm font-medium flex-1">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                    <a href="{{ route('admin.consultation-leads.index') }}" class="btn-secondary px-4 py-2 rounded-apple text-sm font-medium">
                        Reset
                    </a>
                </div>
            </form>
            
            <!-- Date Range Filter -->
            <form method="GET" action="{{ route('admin.consultation-leads.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="contacted" value="{{ request('contacted') }}">
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

    <!-- Consultation Leads Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(84, 84, 88, 0.65);">
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Lead #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Perusahaan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Kontak</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Estimasi</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-dark-text-secondary uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-separator">
                    @forelse($consultations as $consultation)
                        <tr class="hover:bg-dark-bg-tertiary transition-colors">
                            <td class="px-4 py-3 text-sm">
                                <span class="font-mono text-dark-text-primary">#{{ $consultation->id }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-dark-text-secondary">
                                {{ $consultation->created_at->format('d M Y') }}<br>
                                <span class="text-xs">{{ $consultation->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-dark-text-primary">{{ $consultation->company_name ?: $consultation->name }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ optional($consultation->kbli)->description }}</div>
                                <div class="text-xs text-dark-text-secondary mt-1">
                                    @php
                                        $sizeColors = [
                                            'large' => 'bg-red-500/20 text-red-400 border-red-500',
                                            'medium' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                            'small' => 'bg-green-500/20 text-green-400 border-green-500',
                                            'micro' => 'bg-gray-500/20 text-gray-400 border-gray-500',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border {{ $sizeColors[$consultation->business_size] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                        {{ ucfirst($consultation->business_size) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-dark-text-primary">{{ $consultation->name }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ $consultation->email }}</div>
                                <div class="text-xs text-dark-text-secondary">{{ $consultation->phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="font-medium text-dark-text-primary">
                                    {{ $consultation->auto_estimate['cost_summary']['formatted']['grand_total'] ?? '-' }}
                                </div>
                                <div class="text-xs text-dark-text-secondary">
                                    Confidence: {{ number_format(($consultation->confidence_score ?? 0.5) * 100, 0) }}%
                                </div>
                                @if(isset($consultation->auto_estimate['cost_summary']['grand_total']) && $consultation->auto_estimate['cost_summary']['grand_total'] >= 10000000)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-red-500/20 text-red-400 border-red-500">
                                        High Value
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @php
                                    $statusColors = [
                                        'auto_estimated' => 'bg-blue-500/20 text-blue-400 border-blue-500',
                                        'reviewed' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500',
                                        'approved' => 'bg-green-500/20 text-green-400 border-green-500',
                                        'quoted' => 'bg-indigo-500/20 text-indigo-400 border-indigo-500',
                                        'rejected' => 'bg-red-500/20 text-red-400 border-red-500',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium border {{ $statusColors[$consultation->estimate_status] ?? 'bg-gray-500/20 text-gray-400 border-gray-500' }}">
                                    {{ ucfirst(str_replace('_', ' ', $consultation->estimate_status)) }}
                                </span>
                                
                                @if($consultation->contacted)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-green-500/20 text-green-400 border-green-500 mt-1">
                                        <i class="fas fa-phone mr-1"></i>Contacted
                                    </span>
                                @endif

                                @if($consultation->converted_to_client)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-apple text-xs font-medium border bg-purple-500/20 text-purple-400 border-purple-500 mt-1">
                                        <i class="fas fa-check-circle mr-1"></i>Converted
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-right">
                                <a href="{{ route('admin.consultation-leads.show', $consultation) }}" class="text-apple-blue hover:text-blue-400 text-sm font-medium">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-dark-text-secondary">
                                <i class="fas fa-inbox text-2xl mb-3 block opacity-30"></i>
                                <p>Tidak ada consultation leads ditemukan</p>
                                @if(request()->hasAny(['search', 'status', 'contacted', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.consultation-leads.index') }}" class="text-apple-blue hover:text-blue-400 text-sm mt-2 inline-block">
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
        @if($consultations->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid rgba(84, 84, 88, 0.65);">
                {{ $consultations->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="mt-4 rounded-apple-lg p-4" style="background-color: rgba(10, 132, 255, 0.1); border: 1px solid rgba(10, 132, 255, 0.3);">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-apple-blue mr-3 mt-0.5"></i>
            <div class="text-sm text-dark-text-secondary">
                <p class="font-medium text-apple-blue mb-1">Tentang Consultation Leads</p>
                <p>Data leads dari estimasi biaya perizinan AI. Gunakan fitur ini untuk tracking dan konversi leads menjadi klien terdaftar dengan proyek perizinan.</p>
            </div>
        </div>
    </div>
</div>

<!-- Convert to Client Modal -->
<div id="convertModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-lg max-w-md w-full p-6">
        <h3 class="text-base font-semibold text-white mb-4">Konversi ke Klien</h3>
        <p class="text-sm text-dark-text-secondary mb-6">
            Konversi consultation lead ini menjadi akun klien terdaftar?
        </p>
        <form id="convertForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="create_client_account" value="1" class="form-checkbox bg-dark-bg-tertiary border-dark-separator" checked>
                    <span class="ml-2 text-sm text-dark-text-primary">Buat akun klien</span>
                </label>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password untuk klien" 
                       class="w-full px-3 py-2 rounded-apple text-sm" 
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);"
                       required>
            </div>
            <div>
                <input type="text" name="company_name" placeholder="Nama perusahaan (opsional)" 
                       class="w-full px-3 py-2 rounded-apple text-sm" 
                       style="background-color: var(--dark-bg-tertiary); border: 1px solid var(--dark-separator); color: var(--dark-text-primary);">
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="hideConvertModal()" class="btn-secondary px-4 py-2 rounded-apple text-sm">Batal</button>
                <button type="submit" class="btn-primary px-4 py-2 rounded-apple text-sm">Konversi</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showConvertModal(consultationId) {
    document.getElementById('convertForm').action = `/admin/consultation-leads/${consultationId}/convert`;
    document.getElementById('convertModal').classList.remove('hidden');
}

function hideConvertModal() {
    document.getElementById('convertModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('convertModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConvertModal();
    }
});
</script>
@endpush