@extends('layouts.admin')

@section('title', 'Rekonsiliasi Bank')
@section('page-title', 'Rekonsiliasi Bank')

@section('content')
    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em] text-dark-text-tertiary/80">Pengelolaan Keuangan</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">
                        Rekonsiliasi Bank
                    </h1>
                    <p class="text-sm md:text-base text-dark-text-secondary">
                        Rekonsiliasi laporan bank dengan transaksi sistem untuk memastikan keakuratan dan keseimbangan data keuangan.
                    </p>
                </div>
                <div class="space-y-2.5">
                    <a href="{{ route('reconciliations.create') }}"
                       class="inline-flex items-center px-4 py-2 rounded-apple text-sm font-semibold bg-apple-blue/25 text-white/90">
                        <i class="fas fa-plus mr-2"></i>
                        Mulai Rekonsiliasi Baru
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Summary Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <!-- Total Reconciliations -->
                <div class="rounded-apple-lg p-3.5 md:p-4 bg-apple-blue/12">
                    <p class="text-xs uppercase tracking-widest text-apple-blue/90">Total Rekonsiliasi</p>
                    <h2 class="text-2xl font-bold mt-1.5 text-white">{{ $reconciliations->total() }}</h2>
                    <p class="text-xs text-dark-text-tertiary">Seluruh periode</p>
                </div>

                <!-- Completed -->
                <div class="rounded-apple-lg p-3.5 md:p-4 bg-apple-green/12">
                    <p class="text-xs uppercase tracking-widest text-apple-green/90">Selesai</p>
                    <h2 class="text-2xl font-bold mt-1.5 text-apple-green">{{ $reconciliations->where('status', 'completed')->count() }}</h2>
                    <p class="text-xs text-dark-text-tertiary">Rekonsiliasi tuntas</p>
                </div>

                <!-- In Progress -->
                <div class="rounded-apple-lg p-3.5 md:p-4 bg-apple-orange/12">
                    <p class="text-xs uppercase tracking-widest text-apple-orange/90">Sedang Proses</p>
                    <h2 class="text-2xl font-bold mt-1.5 text-apple-orange">{{ $reconciliations->where('status', 'in_progress')->count() }}</h2>
                    <p class="text-xs text-dark-text-tertiary">Perlu diselesaikan</p>
                </div>

                <!-- Balanced -->
                <div class="rounded-apple-lg p-3.5 md:p-4 bg-apple-green/12">
                    <p class="text-xs uppercase tracking-widest text-apple-green/90">Seimbang</p>
                    <h2 class="text-2xl font-bold mt-1.5 text-apple-green">{{ $reconciliations->filter(fn($r) => $r->difference == 0)->count() }}</h2>
                    <p class="text-xs text-dark-text-tertiary">Selisih = 0</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters -->
    <div class="card-elevated rounded-apple-lg mb-5 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-white/80">Pencarian & Filter</h3>
        </div>
        <div class="p-4 md:p-5">
            <form method="GET" action="{{ route('reconciliations.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <!-- Cash Account Filter -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-dark-text-tertiary">Akun Kas</label>
                        <select name="cash_account_id" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Akun</option>
                            @foreach($cashAccounts as $account)
                                <option value="{{ $account->id }}" {{ request('cash_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->account_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-dark-text-tertiary">Status</label>
                        <select name="status" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                            <option value="">Semua Status</option>
                            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Proses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Ditinjau</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-dark-text-tertiary">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="input-dark w-full px-3 py-2 rounded-apple text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-xs font-semibold mb-1.5 text-dark-text-tertiary">Sampai Tanggal</label>
                        <div class="flex space-x-2">
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="input-dark flex-1 px-3 py-2 rounded-apple text-sm">
                            <button type="submit" class="btn-secondary-sm px-4">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('reconciliations.index') }}" class="btn-secondary-sm px-4">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Reconciliations Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead class="bg-dark-bg-secondary/45">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Tanggal</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Akun Kas</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Periode</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Saldo Buku</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Saldo Bank</th>
                        <th scope="col" class="px-4 py-2.5 text-right text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Selisih</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-[11px] font-semibold uppercase tracking-[0.2em] text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-dark-bg-secondary">
                @forelse($reconciliations as $reconciliation)
                <tr class="hover-lift transition-apple border-b border-white/5">
                    <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                        {{ $reconciliation->reconciliation_date->locale('id')->isoFormat('D MMM Y') }}
                    </td>
                    <td class="px-4 py-2.5">
                        <div class="font-semibold text-sm text-dark-text-primary">{{ $reconciliation->cashAccount->account_name }}</div>
                        <div class="text-xs text-dark-text-secondary mt-1">{{ $reconciliation->cashAccount->bank_name }}</div>
                    </td>
                    <td class="px-4 py-2.5 text-sm text-dark-text-primary">
                        {{ $reconciliation->start_date->locale('id')->isoFormat('D MMM') }} - {{ $reconciliation->end_date->locale('id')->isoFormat('D MMM Y') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-medium text-dark-text-primary">
                        Rp {{ number_format($reconciliation->closing_balance_book, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-medium text-dark-text-primary">
                        Rp {{ number_format($reconciliation->closing_balance_bank, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-sm text-right font-bold {{ $reconciliation->difference == 0 ? 'text-apple-green' : 'text-apple-red' }}">
                        Rp {{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                        @if($reconciliation->status === 'in_progress')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-orange/15 text-apple-orange">
                                <i class="fas fa-hourglass-half mr-1"></i> Proses
                            </span>
                        @elseif($reconciliation->status === 'completed')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-green/15 text-apple-green">
                                <i class="fas fa-check-circle mr-1"></i> Selesai
                            </span>
                        @elseif($reconciliation->status === 'reviewed')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-blue/15 text-apple-blue">
                                <i class="fas fa-eye mr-1"></i> Ditinjau
                            </span>
                        @elseif($reconciliation->status === 'approved')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-green/15 text-apple-green">
                                <i class="fas fa-check-double mr-1"></i> Disetujui
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-2.5 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center space-x-1.5">
                            @if($reconciliation->status === 'in_progress')
                                <a href="{{ route('reconciliations.match', $reconciliation) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-apple-blue/15 text-apple-blue border border-apple-blue/25"
                                   title="Lanjutkan Pencocokan">
                                    <i class="fas fa-exchange-alt"></i>
                                </a>
                            @else
                                <a href="{{ route('reconciliations.show', $reconciliation) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-apple-teal/15 text-apple-teal border border-apple-teal/25"
                                   title="Lihat Laporan">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            @endif
                            @if($reconciliation->status === 'in_progress')
                                <form action="{{ route('reconciliations.destroy', $reconciliation) }}" method="POST" class="inline"
                                      x-data @submit.prevent="if(confirm('Yakin ingin menghapus rekonsiliasi ini?')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1 rounded-apple text-xs font-semibold transition-apple bg-apple-red/15 text-apple-red border border-apple-red/25"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-6xl mb-6 text-dark-text-tertiary/50"></i>
                            <h3 class="text-xl font-semibold mb-2 text-white">Belum Ada Rekonsiliasi Bank</h3>
                            <p class="mb-6 text-dark-text-tertiary">Mulai dengan membuat rekonsiliasi pertama Anda</p>
                            <a href="{{ route('reconciliations.create') }}" 
                               class="btn-primary inline-flex items-center px-6 py-3 rounded-apple font-medium">
                                <i class="fas fa-plus mr-2"></i>
                                Mulai Rekonsiliasi Pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($reconciliations->hasPages())
    <div class="rounded-apple-lg px-4 py-3 mt-4 bg-dark-bg-tertiary border border-white/10 shadow-lg">
        {{ $reconciliations->links('pagination::tailwind') }}
    </div>
    @endif

@if(session('success'))
<div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-5">
    <i class="fas fa-check-circle text-lg"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-5">
    <i class="fas fa-exclamation-circle text-lg"></i>
    <span>{{ session('error') }}</span>
</div>
@endif
@endsection
