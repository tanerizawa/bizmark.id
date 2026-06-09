@extends('layouts.admin')

@section('title', 'Mutasi - ' . $cashAccount->account_name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-3 sm:space-y-0">
        <div class="flex items-center">
            <a href="{{ route('cash-accounts.index') }}" class="mr-4 text-apple-blue/90">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white">Mutasi Rekening</h1>
                <p class="mt-1 text-sm text-dark-text-secondary">
                    {{ $cashAccount->account_name }}
                    @if($cashAccount->account_number)
                        <span class="ml-2">• {{ $cashAccount->account_number }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-white/10 text-dark-text-primary/90">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="exportToCSV()" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors bg-apple-green/20 text-apple-green">
                <i class="fas fa-file-export mr-2"></i>Export CSV
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Saldo Saat Ini</span>
                <i class="fas fa-wallet text-apple-blue/80"></i>
            </div>
            <p class="text-2xl font-bold text-white">
                {{ $cashAccount->formatted_balance }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Total Pemasukan</span>
                <i class="fas fa-arrow-down text-apple-green"></i>
            </div>
            <p class="text-xl font-bold text-apple-green">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Total Pengeluaran</span>
                <i class="fas fa-arrow-up text-apple-red"></i>
            </div>
            <p class="text-xl font-bold text-apple-red">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </p>
        </div>

        <div class="card-elevated rounded-apple-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-dark-text-secondary">Perubahan Bersih</span>
                <i class="fas fa-chart-line {{ $netChange >= 0 ? 'text-apple-green' : 'text-apple-red' }}"></i>
            </div>
            <p class="text-xl font-bold {{ $netChange >= 0 ? 'text-apple-green' : 'text-apple-red' }}">
                Rp {{ number_format(abs($netChange), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card-elevated rounded-apple-lg mb-6 overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10">
            <h3 class="text-base font-semibold text-white">
                <i class="fas fa-filter mr-2 text-dark-text-tertiary/40"></i>
                Filter Mutasi
            </h3>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('cash-accounts.show', $cashAccount) }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Transaction Type Filter -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Jenis Transaksi</label>
                        <select name="transaction_type" class="input-dark w-full px-4 py-2.5 rounded-lg">
                            <option value="all" {{ $transactionType === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="income" {{ $transactionType === 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ $transactionType === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>

                    <!-- Filter Type -->
                    <div>
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Periode</label>
                        <select name="filter_type" class="input-dark w-full px-4 py-2.5 rounded-lg" onchange="toggleDateInputs(this.value)">
                            <option value="month" {{ $filterType === 'month' ? 'selected' : '' }}>Bulanan</option>
                            <option value="quarter" {{ $filterType === 'quarter' ? 'selected' : '' }}>Kuartalan</option>
                            <option value="year" {{ $filterType === 'year' ? 'selected' : '' }}>Tahunan</option>
                            <option value="custom" {{ $filterType === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>

                    <!-- Month/Year Selection -->
                    <div id="monthYearInputs" class="{{ $filterType === 'custom' ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Bulan/Tahun</label>
                        <div class="flex space-x-2">
                            <select name="month" class="input-dark w-1/2 px-3 py-2.5 rounded-lg">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $m, 1)->format('M') }}
                                    </option>
                                @endfor
                            </select>
                            <select name="year" class="input-dark w-1/2 px-3 py-2.5 rounded-lg">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Custom Date Range -->
                    <div id="customDateInputs" class="{{ $filterType !== 'custom' ? 'hidden' : '' }}">
                        <label class="block text-sm font-medium mb-2 text-dark-text-primary/80">Tanggal Custom</label>
                        <div class="flex space-x-2">
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" class="input-dark w-1/2 px-3 py-2.5 rounded-lg">
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" class="input-dark w-1/2 px-3 py-2.5 rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-lg text-sm font-medium transition-colors bg-apple-blue/90 text-white">
                        <i class="fas fa-search mr-2"></i>Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mutations Table -->
    <div class="card-elevated rounded-apple-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-white/10">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-semibold text-white">
                    <i class="fas fa-list mr-2 text-dark-text-tertiary/40"></i>
                    Riwayat Transaksi
                </h3>
                <span class="text-sm text-dark-text-secondary">
                    {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="mutationsTable">
                <thead class="bg-white/[0.02]">
                    <tr class="border-b border-white/10">
                        <th class="text-left py-3 px-4 text-xs font-semibold text-dark-text-secondary">TANGGAL</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-dark-text-secondary">KETERANGAN</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-dark-text-secondary">METODE</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-apple-green">DEBIT</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-apple-red">KREDIT</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-dark-text-secondary">SALDO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutations as $mutation)
                    <tr class="border-b border-white/5">
                        <td class="py-3 px-4 text-sm whitespace-nowrap text-dark-text-primary">
                            {{ $mutation['date']->format('d M Y') }}
                            <div class="text-xs text-dark-text-tertiary/80">
                                {{ $mutation['date']->format('H:i') }}
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-dark-text-primary">
                            <div>{{ $mutation['description'] }}</div>
                            @if(isset($mutation['reference']) && $mutation['reference'] !== '-')
                            <div class="text-xs mt-1 text-dark-text-tertiary/80">
                                Ref: {{ $mutation['reference'] }}
                            </div>
                            @endif
                            @if(isset($mutation['category']))
                            <span class="inline-block px-2 py-0.5 rounded text-xs mt-1 bg-white/10 text-dark-text-primary/70">
                                {{ $mutation['category'] }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-dark-text-primary/70">
                            @if($mutation['type'] === 'income')
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-apple-green/20 text-apple-green">
                                    <i class="fas fa-arrow-down mr-1"></i>{{ ucfirst($mutation['payment_method'] ?? '-') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-apple-red/20 text-apple-red">
                                    <i class="fas fa-arrow-up mr-1"></i>{{ ucfirst($mutation['payment_method'] ?? '-') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-sm text-right font-semibold {{ $mutation['type'] === 'income' ? 'text-apple-green' : 'text-dark-text-tertiary/30' }}">
                            {{ $mutation['type'] === 'income' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-sm text-right font-semibold {{ $mutation['type'] === 'expense' ? 'text-apple-red' : 'text-dark-text-tertiary/30' }}">
                            {{ $mutation['type'] === 'expense' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-4 text-sm text-right font-bold text-white">
                            Rp {{ number_format($mutation['balance'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <i class="fas fa-inbox text-5xl mb-4 text-dark-text-tertiary/30"></i>
                            <p class="text-lg font-semibold text-dark-text-primary">Tidak Ada Transaksi</p>
                            <p class="text-sm mt-1 text-dark-text-tertiary/80">Belum ada transaksi pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleDateInputs(filterType) {
    const monthYearInputs = document.getElementById('monthYearInputs');
    const customDateInputs = document.getElementById('customDateInputs');
    
    if (filterType === 'custom') {
        monthYearInputs.classList.add('hidden');
        customDateInputs.classList.remove('hidden');
    } else {
        monthYearInputs.classList.remove('hidden');
        customDateInputs.classList.add('hidden');
    }
}

function exportToCSV() {
    const table = document.getElementById('mutationsTable');
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length; j++) {
            let text = cols[j].innerText.replace(/\n/g, ' ').trim();
            row.push('"' + text + '"');
        }
        csv.push(row.join(','));
    }
    
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'mutasi_{{ $cashAccount->account_name }}_{{ now()->format("Y-m-d") }}.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Print styles
const style = document.createElement('style');
style.textContent = `
    @media print {
        body * { visibility: hidden; }
        #mutationsTable, #mutationsTable * { visibility: visible; }
        #mutationsTable { position: absolute; left: 0; top: 0; }
    }
`;
document.head.appendChild(style);
</script>
@endsection
