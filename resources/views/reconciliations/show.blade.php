@extends('layouts.admin')

@section('title', 'Laporan Rekonsiliasi')
@section('page-title', 'Laporan Rekonsiliasi Bank')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div>
            <a href="{{ route('reconciliations.index') }}" 
               class="inline-flex items-center text-sm mb-2 text-apple-blue">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>
        <div class="flex items-center space-x-3">
            <button onclick="window.print()" 
                    class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                    class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90 bg-white/5 text-dark-text-primary">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <button class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90"
                    class="px-4 py-2 rounded-apple text-sm font-medium transition-all hover:opacity-90 bg-apple-green/20 text-apple-green">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Report Card -->
    <div class="rounded-apple p-8 shadow-lg print:shadow-none bg-white/5" id="reportContent">
        <!-- Report Header -->
        <div class="text-center mb-8 pb-6 border-b-2 border-white/10">
            <h1 class="text-2xl font-bold mb-2 text-dark-text-primary">
                PT CANGAH PAJARATAN MANDIRI
            </h1>
            <h2 class="text-xl font-semibold mb-1 text-dark-text-primary/90">
                LAPORAN REKONSILIASI BANK
            </h2>
            <p class="text-sm text-dark-text-secondary">
                {{ $reconciliation->cashAccount->bank_name }} - {{ $reconciliation->cashAccount->account_number }}
            </p>
            <p class="text-sm text-dark-text-secondary">
                Periode: {{ $reconciliation->start_date->format('d F Y') }} s/d {{ $reconciliation->end_date->format('d F Y') }}
            </p>
        </div>

        <!-- Status Badge -->
        <div class="flex items-center justify-center mb-6">
            @if($reconciliation->status === 'completed')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-apple-green/20 text-apple-green">
                    <i class="fas fa-check-circle mr-2"></i> Rekonsiliasi Selesai
                </span>
            @elseif($reconciliation->status === 'reviewed')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium" 
                      class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-apple-blue/20 text-apple-blue">
                    <i class="fas fa-eye mr-2"></i> Sudah Direview
                </span>
            @elseif($reconciliation->status === 'approved')
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium" 
                      class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-apple-green/20 text-apple-green">
                    <i class="fas fa-check-double mr-2"></i> Sudah Disetujui
                </span>
            @else
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium" 
                      class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-apple-orange/20 text-apple-orange">
                    <i class="fas fa-hourglass-half mr-2"></i> Sedang Proses
                </span>
            @endif
        </div>

        <!-- Section 1: Bank Balance Reconciliation -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-4 pb-2 text-dark-text-primary border-b border-white/10">
                1. REKONSILIASI SALDO BANK
            </h3>
            
            <table class="w-full text-sm">
                <tr>
                    <td class="py-2 text-dark-text-primary/90">Saldo per Bank Statement ({{ $reconciliation->end_date->format('d M Y') }})</td>
                    <td class="text-right font-medium text-dark-text-primary">
                        Rp {{ number_format($reconciliation->closing_balance_bank, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-apple-green/[0.05]">
                    <td class="py-2 pl-4 text-dark-text-primary/90">
                        Tambah: Deposits in Transit (setoran dalam perjalanan)
                    </td>
                    <td class="text-right font-medium text-apple-green">
                        +Rp {{ number_format($reconciliation->total_deposits_in_transit, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-apple-red/[0.05]">
                    <td class="py-2 pl-4 text-dark-text-primary/90">
                        Kurang: Outstanding Checks (cek/transfer belum cair)
                    </td>
                    <td class="text-right font-medium text-apple-red">
                        -Rp {{ number_format($reconciliation->total_outstanding_checks, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="border-t-2 border-white/20 bg-apple-blue/[0.05]">
                    <td class="py-3 font-bold text-dark-text-primary">
                        ADJUSTED BANK BALANCE
                    </td>
                    <td class="text-right font-bold text-lg text-apple-blue">
                        Rp {{ number_format($reconciliation->adjusted_bank_balance, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section 2: Book Balance Reconciliation -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-4 pb-2 text-dark-text-primary border-b border-white/10">
                2. REKONSILIASI SALDO BUKU
            </h3>
            
            <table class="w-full text-sm">
                <tr>
                    <td class="py-2 text-dark-text-primary/90">Saldo per Buku/Sistem ({{ $reconciliation->end_date->format('d M Y') }})</td>
                    <td class="text-right font-medium text-dark-text-primary">
                        Rp {{ number_format($reconciliation->closing_balance_book, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-apple-green/[0.05]">
                    <td class="py-2 pl-4 text-dark-text-primary/90">
                        Tambah: Bank Credits (bunga, dll belum dicatat)
                    </td>
                    <td class="text-right font-medium text-apple-green">
                        +Rp {{ number_format($reconciliation->total_bank_credits, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-apple-red/[0.05]">
                    <td class="py-2 pl-4 text-dark-text-primary/90">
                        Kurang: Bank Charges (biaya admin, transfer, dll belum dicatat)
                    </td>
                    <td class="text-right font-medium text-apple-red">
                        -Rp {{ number_format($reconciliation->total_bank_charges, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="border-t-2 border-white/20 bg-apple-blue/[0.05]">
                    <td class="py-3 font-bold text-dark-text-primary">
                        ADJUSTED BOOK BALANCE
                    </td>
                    <td class="text-right font-bold text-lg text-apple-blue">
                        Rp {{ number_format($reconciliation->adjusted_book_balance, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Section 3: Difference Analysis -->
        <div class="mb-8 p-4 rounded-apple {{ $reconciliation->difference == 0 ? 'bg-apple-green/10' : 'bg-apple-red/10' }}">
            <h3 class="text-lg font-bold mb-3 {{ $reconciliation->difference == 0 ? 'text-apple-green' : 'text-apple-red' }}">
                3. SELISIH REKONSILIASI
            </h3>
            
            <div class="flex items-center justify-between">
                <span class="text-sm text-dark-text-primary/90">
                    Adjusted Bank Balance - Adjusted Book Balance
                </span>
                <span class="text-2xl font-bold {{ $reconciliation->difference == 0 ? 'text-apple-green' : 'text-apple-red' }}">
                    Rp {{ number_format(abs($reconciliation->difference), 0, ',', '.') }}
                </span>
            </div>
            
            @if($reconciliation->difference == 0)
                <div class="mt-3 flex items-center text-apple-green">
                    <i class="fas fa-check-circle text-2xl mr-3"></i>
                    <span class="text-sm font-medium">✅ Rekonsiliasi SEIMBANG - Tidak ada selisih</span>
                </div>
            @else
                <div class="mt-3 flex items-start text-apple-red">
                    <i class="fas fa-exclamation-triangle text-2xl mr-3 mt-1"></i>
                    <div>
                        <p class="text-sm font-medium mb-2">⚠️ Terdapat selisih yang perlu investigasi</p>
                        <p class="text-xs text-dark-text-secondary/90">
                            Kemungkinan penyebab: transaksi belum dicatat, kesalahan input, atau timing difference
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Section 4: Matching Statistics -->
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-4 pb-2 text-dark-text-primary border-b border-white/10">
                4. STATISTIK MATCHING
            </h3>
            
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 rounded-apple bg-apple-blue/10">
                    <p class="text-3xl font-bold mb-1 text-apple-blue">{{ $stats['total'] }}</p>
                    <p class="text-xs text-dark-text-secondary/90">Total Transaksi Bank</p>
                </div>
                <div class="text-center p-4 rounded-apple bg-apple-green/10">
                    <p class="text-3xl font-bold mb-1 text-apple-green">{{ $stats['matched'] }}</p>
                    <p class="text-xs text-dark-text-secondary/90">Sudah Dicocokkan</p>
                </div>
                <div class="text-center p-4 rounded-apple bg-apple-orange/10">
                    <p class="text-3xl font-bold mb-1 text-apple-orange">{{ $stats['unmatched'] }}</p>
                    <p class="text-xs text-dark-text-secondary/90">Belum Dicocokkan</p>
                </div>
            </div>
            
            <div class="mt-4 text-center">
                <p class="text-sm text-dark-text-secondary/90">
                    Match Rate: <span class="font-bold text-lg text-apple-blue">{{ number_format($stats['match_rate'], 1) }}%</span>
                </p>
            </div>
        </div>

        <!-- Section 5: Matched Transactions -->
        @if($matchedEntries->count() > 0)
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-4 pb-2 text-dark-text-primary border-b border-white/10">
                5. TRANSAKSI YANG COCOK ({{ $matchedEntries->count() }})
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-white/[0.02]">
                            <th class="px-3 py-2 text-left text-dark-text-tertiary">Tanggal</th>
                            <th class="px-3 py-2 text-left text-dark-text-tertiary">Deskripsi</th>
                            <th class="px-3 py-2 text-right text-dark-text-tertiary">Debit</th>
                            <th class="px-3 py-2 text-right text-dark-text-tertiary">Kredit</th>
                            <th class="px-3 py-2 text-center text-dark-text-tertiary">Confidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($matchedEntries as $entry)
                        <tr class="border-b border-white/5">
                            <td class="px-3 py-2 text-dark-text-primary/90">
                                {{ $entry->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-dark-text-primary/90">
                                {{ Str::limit($entry->description, 60) }}
                            </td>
                            <td class="px-3 py-2 text-right text-apple-red">
                                @if($entry->debit_amount > 0)
                                    Rp {{ number_format($entry->debit_amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right text-apple-green">
                                @if($entry->credit_amount > 0)
                                    Rp {{ number_format($entry->credit_amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-apple-green/20 text-apple-green">
                                    {{ ucfirst($entry->match_confidence ?? 'matched') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Section 6: Outstanding Items -->
        @if($unmatchedEntries->count() > 0)
        <div class="mb-8">
            <h3 class="text-lg font-bold mb-4 pb-2 text-apple-orange border-b border-white/10">
                6. OUTSTANDING ITEMS ({{ $unmatchedEntries->count() }})
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-apple-orange/[0.05]">
                            <th class="px-3 py-2 text-left text-dark-text-tertiary">Tanggal</th>
                            <th class="px-3 py-2 text-left text-dark-text-tertiary">Deskripsi</th>
                            <th class="px-3 py-2 text-right text-dark-text-tertiary">Debit</th>
                            <th class="px-3 py-2 text-right text-dark-text-tertiary">Kredit</th>
                            <th class="px-3 py-2 text-left text-dark-text-tertiary">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unmatchedEntries as $entry)
                        <tr class="border-b border-white/5">
                            <td class="px-3 py-2 text-dark-text-primary/90">
                                {{ $entry->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-2 text-dark-text-primary/90">
                                {{ Str::limit($entry->description, 60) }}
                            </td>
                            <td class="px-3 py-2 text-right text-apple-red">
                                @if($entry->debit_amount > 0)
                                    Rp {{ number_format($entry->debit_amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right text-apple-green">
                                @if($entry->credit_amount > 0)
                                    Rp {{ number_format($entry->credit_amount, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-3 py-2 text-apple-orange">
                                {{ $entry->unmatch_reason ? ucwords(str_replace('_', ' ', $entry->unmatch_reason)) : 'Perlu Investigasi' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Notes Section -->
        @if($reconciliation->notes)
        <div class="mb-8 p-4 rounded-apple bg-white/[0.02]">
            <h3 class="text-sm font-bold mb-2 text-dark-text-primary">CATATAN:</h3>
            <p class="text-xs text-dark-text-secondary/90">{{ $reconciliation->notes }}</p>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="grid grid-cols-3 gap-8 mt-12 pt-6 border-t border-white/10">
            <div class="text-center">
                <p class="text-xs mb-12 text-dark-text-secondary/90">Direkonsiliasi oleh:</p>
                <div class="border-t border-white/30 pt-2">
                    <p class="text-sm font-medium text-dark-text-primary">
                        {{ $reconciliation->reconciledBy->name ?? '-' }}
                    </p>
                    <p class="text-xs text-dark-text-secondary">
                        {{ $reconciliation->completed_at ? $reconciliation->completed_at->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs mb-12 text-dark-text-secondary/90">Direview oleh:</p>
                <div class="border-t border-white/30 pt-2">
                    <p class="text-sm font-medium text-dark-text-primary">
                        {{ $reconciliation->reviewedBy->name ?? '-' }}
                    </p>
                    <p class="text-xs text-dark-text-secondary">
                        {{ $reconciliation->reviewed_at ? $reconciliation->reviewed_at->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            <div class="text-center">
                <p class="text-xs mb-12 text-dark-text-secondary/90">Disetujui oleh:</p>
                <div class="border-t border-white/30 pt-2">
                    <p class="text-sm font-medium text-dark-text-primary">
                        {{ $reconciliation->approvedBy->name ?? '-' }}
                    </p>
                    <p class="text-xs text-dark-text-secondary">
                        {{ $reconciliation->approved_at ? $reconciliation->approved_at->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 text-center text-xs border-t border-white/10 text-dark-text-tertiary/80">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
            <p class="mt-1">PT CANGAH PAJARATAN MANDIRI - Bizmark.ID</p>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background: white !important;
    }
    .rounded-apple {
        border-radius: 0 !important;
    }
    button, .print\:hidden {
        display: none !important;
    }
}
</style>

@if(session('success'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-apple shadow-lg z-50 animate-fade-in print:hidden">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif
@endsection
