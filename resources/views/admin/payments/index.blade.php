@extends('layouts.admin')

@section('title', 'Verifikasi Pembayaran')
@section('page-title', 'Verifikasi Pembayaran')

@section('content')
@php
    use App\Models\Payment;

    $queueStatuses = ['processing', 'pending'];
    $manualPayments = Payment::query()->where('payment_method', 'manual');

    $queueCount = (clone $manualPayments)->whereIn('status', $queueStatuses)->count();
    $processingCount = (clone $manualPayments)->where('status', 'processing')->count();
    $pendingCount = (clone $manualPayments)->where('status', 'pending')->count();
    $queueAmount = (clone $manualPayments)->whereIn('status', $queueStatuses)->sum('amount');
    $verifiedToday = Payment::where('status', 'success')->whereDate('verified_at', today())->count();
    $rejectedToday = Payment::where('status', 'failed')->whereDate('verified_at', today())->count();
    $oldestPayment = (clone $manualPayments)->whereIn('status', $queueStatuses)->orderBy('created_at')->first();
    $oldestAge = $oldestPayment ? $oldestPayment->created_at->diffForHumans(null, true) : '0 jam';

    $statusFilters = [
        ['key' => 'all', 'label' => 'Semua', 'count' => $queueCount],
        ['key' => 'processing', 'label' => 'Menunggu Review', 'count' => $processingCount],
        ['key' => 'pending', 'label' => 'Perlu Follow-up', 'count' => $pendingCount],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <p class="text-xs uppercase tracking-[0.4em] text-dark-text-tertiary">Operasional Pembayaran</p>
                <h1 class="text-2xl md:text-xl font-bold text-white">Verifikasi Pembayaran Manual</h1>
                <p class="text-sm md:text-base text-dark-text-primary/70">
                    Pantau antrean pembayaran manual, tinjau bukti transfer, dan verifikasi transaksi dalam satu tampilan terpadu.
                </p>
                <div class="text-xs flex flex-wrap gap-3 text-dark-text-secondary">
                    <span><i class="fas fa-database mr-2"></i>{{ $queueCount }} transaksi menunggu</span>
                    <span><i class="fas fa-hourglass-half mr-2"></i>Antrean tertua {{ $oldestAge }}</span>
                </div>
            </div>
            <div class="space-y-3">
                <a href="{{ route('admin.permit-applications.index') }}" class="btn-secondary-sm">
                    <i class="fas fa-list-check mr-2"></i>Lihat Permohonan Terkait
                </a>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-dark-text-primary/70">
                    Panduan verifikasi →
                </a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest text-apple-orange/90">Menunggu Review</p>
            <p class="text-xl font-bold text-white">{{ $queueCount }}</p>
            <p class="text-xs text-dark-text-secondary">{{ $processingCount }} menunggu verifikasi admin</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest text-apple-green/90">Terverifikasi Hari Ini</p>
            <p class="text-xl font-bold text-apple-green">{{ $verifiedToday }}</p>
            <p class="text-xs text-dark-text-secondary">{{ $rejectedToday }} ditolak hari ini</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest text-[rgba(191,90,242,0.9)]">Nilai Antrean</p>
            <p class="text-xl font-bold text-[rgba(191,90,242,1)]">Rp {{ number_format($queueAmount, 0, ',', '.') }}</p>
            <p class="text-xs text-dark-text-secondary">Total dana menunggu approval</p>
        </div>
        <div class="card-elevated rounded-apple-lg p-4 space-y-1">
            <p class="text-xs uppercase tracking-widest text-apple-blue/90">Durasi Terbuka</p>
            <p class="text-xl font-bold text-apple-blue">{{ $oldestAge }}</p>
            <p class="text-xs text-dark-text-secondary">Antrean tertua di pipeline</p>
        </div>
    </section>

    {{-- Filter & search --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 space-y-4 mb-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                @foreach($statusFilters as $filter)
                    <button type="button"
                            class="status-chip {{ $loop->first ? 'active' : '' }}"
                            data-status="{{ $filter['key'] }}">
                        <span>{{ $filter['label'] }}</span>
                        <span class="chip-count">{{ $filter['count'] }}</span>
                    </button>
                @endforeach
            </div>
            <div class="w-full md:w-72">
                <label for="paymentSearch" class="text-xs uppercase tracking-widest mb-2 block text-dark-text-secondary">Cari Transaksi</label>
                <input type="text" id="paymentSearch" placeholder="Nomor pembayaran, klien, permohonan..." class="w-full px-4 py-2.5 rounded-apple text-sm text-white placeholder-gray-500 bg-[rgba(28,28,30,0.6)] border border-[rgba(84,84,88,0.35)]"
                       style="background: rgba(28,28,30,0.6); border: 1px solid rgba(84,84,88,0.35);">
            </div>
        </div>
    </section>

    {{-- Table --}}
    <section class="card-elevated rounded-apple-xl overflow-hidden">
        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-[rgba(28,28,30,0.45)]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Pembayaran</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Klien</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Permohonan</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Jumlah</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Metode</th>
                            <th class="px-6 py-4 text-left text-xs uppercase tracking-widest text-dark-text-secondary">Status</th>
                            <th class="px-6 py-4 text-right text-xs uppercase tracking-widest text-dark-text-secondary">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            @php
                                $statusColors = [
                                    'processing' => ['bg' => 'rgba(255,149,0,0.15)', 'text' => 'rgba(255,149,0,1)', 'border' => 'rgba(255,149,0,0.3)', 'label' => 'Menunggu Verifikasi'],
                                    'pending' => ['bg' => 'rgba(142,142,147,0.15)', 'text' => 'rgba(142,142,147,1)', 'border' => 'rgba(142,142,147,0.3)', 'label' => 'Pending'],
                                    'success' => ['bg' => 'rgba(52,199,89,0.15)', 'text' => 'rgba(52,199,89,1)', 'border' => 'rgba(52,199,89,0.3)', 'label' => 'Terverifikasi'],
                                    'failed' => ['bg' => 'rgba(255,59,48,0.15)', 'text' => 'rgba(255,59,48,1)', 'border' => 'rgba(255,59,48,0.3)', 'label' => 'Ditolak'],
                                ];
                                $statusStyle = $statusColors[$payment->status] ?? $statusColors['pending'];
                                $searchString = strtolower(trim($payment->payment_number . ' ' . ($payment->client->name ?? '') . ' ' . ($payment->quotation->application->application_number ?? '')));
                            @endphp
                            <tr class="payment-row border-b border-white/5 hover:bg-white/5 transition"
                                data-status="{{ $payment->status }}"
                                data-search="{{ $searchString }}">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">{{ $payment->payment_number }}</p>
                                    <p class="text-xs text-dark-text-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $payment->payment_type ?? 'manual')) }} • {{ $payment->created_at->format('d M Y H:i') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-white">{{ $payment->client->name ?? '-' }}</p>
                                    <p class="text-xs text-dark-text-secondary">{{ $payment->client->email ?? '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-white">{{ $payment->quotation->application->application_number ?? '-' }}</p>
                                    <p class="text-xs text-dark-text-secondary">{{ $payment->quotation->application->permitType->name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-dark-text-secondary">{{ $payment->payment_type === 'down_payment' ? 'Uang Muka' : 'Pelunasan' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-dark-text-primary/85">{{ strtoupper($payment->bank_name ?? 'Transfer Manual') }}</p>
                                    <p class="text-xs text-dark-text-secondary">{{ $payment->gateway_provider ?? 'Manual' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-apple"
                                          style="background: {{ $statusStyle['bg'] }}; color: {{ $statusStyle['text'] }}; border: 1px solid {{ $statusStyle['border'] }};">
                                        {{ $statusStyle['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}"
                                       class="btn-primary-sm">
                                        <i class="fas fa-eye mr-2"></i>Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-white/5">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-10 space-y-3">
                <i class="fas fa-inbox text-4xl text-dark-text-tertiary"></i>
                <p class="text-sm text-dark-text-secondary">Tidak ada pembayaran manual menunggu verifikasi.</p>
            </div>
        @endif
    </section>

<style>
.status-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.45rem 0.9rem;
    border-radius: 999px;
    border: 1px solid rgba(84,84,88,0.35);
    background: rgba(28,28,30,0.4);
    color: rgba(235,235,245,0.65);
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s;
}
.status-chip .chip-count {
    padding: 0.15rem 0.6rem;
    border-radius: 999px;
    background: rgba(255,255,255,0.08);
    font-size: 0.7rem;
}
.status-chip.active {
    border-color: rgba(10,132,255,0.4);
    background: rgba(10,132,255,0.15);
    color: rgba(10,132,255,0.95);
}
.status-chip:hover {
    border-color: rgba(255,255,255,0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chips = document.querySelectorAll('.status-chip');
    const rows = document.querySelectorAll('.payment-row');
    const searchInput = document.getElementById('paymentSearch');
    let activeStatus = 'all';

    function filterRows() {
        const query = (searchInput.value || '').toLowerCase();
        rows.forEach(row => {
            const matchesStatus = activeStatus === 'all' || row.dataset.status === activeStatus;
            const matchesSearch = row.dataset.search.includes(query);
            const shouldShow = matchesStatus && matchesSearch;
            row.style.display = shouldShow ? '' : 'none';
        });
    }

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            activeStatus = chip.dataset.status;
            filterRows();
        });
    });

    searchInput?.addEventListener('input', filterRows);
});
</script>
@endsection
