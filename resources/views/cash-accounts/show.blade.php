@extends('layouts.admin')
@section('title', 'Mutasi - ' . $cashAccount->account_name)
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:200px;height:200px;border-radius:50%;top:-55px;right:-25px;background:color-mix(in srgb,var(--apple-green) 14%,transparent);filter:blur(52px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Mutasi Rekening</p>
                <h1 style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-university" style="color:var(--apple-green);font-size:0.82rem"></i>
                    </span>{{ $cashAccount->account_name }}
                </h1>
                @if($cashAccount->account_number)
                <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">{{ $cashAccount->account_number }}</p>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('cash-accounts.edit', $cashAccount) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-orange) 20%,transparent);color:var(--apple-orange);font-size:0.82rem;font-weight:600;text-decoration:none;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)"
                   onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-edit" style="font-size:0.75rem"></i>Edit
                </a>
                <button onclick="window.print()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:rgba(255,255,255,.05);cursor:pointer"
                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-print" style="font-size:0.75rem"></i>Print
                </button>
                <button onclick="exportToCSV()"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);color:var(--apple-green);font-size:0.82rem;font-weight:600;border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);cursor:pointer"
                        onmouseover="this.style.opacity='.8'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-file-export" style="font-size:0.75rem"></i>Export CSV
                </button>
                <a href="{{ route('cash-accounts.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Summary KPI --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-blue) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-blue) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">Saldo Saat Ini</span>
                <i class="fas fa-wallet" style="color:var(--apple-blue);font-size:0.82rem"></i>
            </div>
            <p style="font-size:1.15rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ $cashAccount->formatted_balance }}</p>
        </div>
        <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-green) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-green) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">Total Pemasukan</span>
                <i class="fas fa-arrow-down" style="color:var(--apple-green);font-size:0.82rem"></i>
            </div>
            <p style="font-size:1.15rem;font-weight:800;color:var(--apple-green);margin:0">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        </div>
        <div style="background:linear-gradient(135deg,color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,var(--apple-red) 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">Total Pengeluaran</span>
                <i class="fas fa-arrow-up" style="color:var(--apple-red);font-size:0.82rem"></i>
            </div>
            <p style="font-size:1.15rem;font-weight:800;color:var(--apple-red);margin:0">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        </div>
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $netChange >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 14%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $netChange >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }} 28%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">Selisih</span>
                <i class="fas fa-exchange-alt" style="color:var(--dark-text-secondary);font-size:0.82rem"></i>
            </div>
            <p style="font-size:1.15rem;font-weight:800;color:{{ $netChange >= 0 ? 'var(--apple-green)' : 'var(--apple-red)' }};margin:0">{{ $netChange >= 0 ? '+' : '' }}Rp {{ number_format(abs($netChange), 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 20px">
        <form method="GET" action="{{ route('cash-accounts.show', $cashAccount) }}" style="display:grid;grid-template-columns:repeat(4,1fr) auto;gap:12px;align-items:end">
            <div>
                <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Jenis Transaksi</label>
                <select name="transaction_type" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <option value="all" {{ request('transaction_type', 'all') == 'all' ? 'selected' : '' }}>Semua Transaksi</option>
                    <option value="income" {{ request('transaction_type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('transaction_type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
            </div>
            <div>
                <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Periode</label>
                <select name="filter_type" id="filterType" onchange="toggleCustomDates(this.value)" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                    <option value="month" {{ request('filter_type', 'month') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="quarter" {{ request('filter_type') == 'quarter' ? 'selected' : '' }}>Kuartal Ini</option>
                    <option value="year" {{ request('filter_type') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                    <option value="custom" {{ request('filter_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div id="customDates" style="display:{{ request('filter_type') == 'custom' ? 'block' : 'none' }}">
                <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
            </div>
            <div id="customDatesEnd" style="display:{{ request('filter_type') == 'custom' ? 'block' : 'none' }}">
                <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" style="padding:8px 18px;border:none;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-filter" style="margin-right:5px"></i>Filter
                </button>
                <a href="{{ route('cash-accounts.show', $cashAccount) }}" style="padding:8px 14px;border-radius:9px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;border:1px solid var(--dark-separator)">
                    <i class="fas fa-undo" style="font-size:0.72rem"></i>Reset
                </a>
            </div>
        </form>
    </div>

    @php $totalUnassigned = ($unassignedInvoicePayments ?? 0) + ($unassignedExpenses ?? 0); @endphp
    @if($totalUnassigned > 0)
    <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:12px;background:color-mix(in srgb,var(--apple-orange) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)">
        <i class="fas fa-exclamation-triangle" style="color:var(--apple-orange);margin-top:2px;flex-shrink:0"></i>
        <div>
            <p style="font-size:0.85rem;font-weight:600;color:var(--apple-orange);margin:0 0 4px">Transaksi Tidak Terasosiasi</p>
            <p style="font-size:0.78rem;color:var(--dark-text-primary);margin:0;line-height:1.5">
                Terdapat <strong>{{ $totalUnassigned }}</strong> transaksi yang belum ditetapkan ke akun kas manapun dalam periode ini.
                @if(($unassignedInvoicePayments ?? 0) > 0)<br>• Pembayaran Invoice tidak terasosiasi: <strong>{{ number_format($unassignedInvoicePayments) }}</strong> transaksi@endif
                @if(($unassignedExpenses ?? 0) > 0)<br>• Pengeluaran tidak terasosiasi: <strong>{{ number_format($unassignedExpenses) }}</strong> transaksi@endif
            </p>
        </div>
    </div>
    @endif

    {{-- Mutations Table --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
            <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-list" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
            <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Riwayat Mutasi
                <span style="display:inline-flex;align-items:center;justify-content:center;min-width:22px;height:20px;padding:0 6px;border-radius:10px;font-size:0.7rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);margin-left:6px">{{ $mutations->count() }}</span>
            </h3>
        </div>
        @if($mutations && $mutations->count() > 0)
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:var(--dark-bg-tertiary)">
                    <tr>
                        @foreach(['Tanggal','Deskripsi','Metode','Debit','Kredit','Saldo'] as $h)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ in_array($h,['Debit','Kredit','Saldo']) ? 'right' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($mutations as $mutation)
                    <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-primary);white-space:nowrap">{{ \Carbon\Carbon::parse($mutation['date'])->format('d M Y') }}</td>
                        <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-primary);max-width:220px">
                            <div>{{ $mutation['description'] }}</div>
                            @if($mutation['reference'])<div style="font-size:0.7rem;margin-top:2px;color:var(--dark-text-secondary)">{{ $mutation['reference'] }}</div>@endif
                        </td>
                        <td style="padding:12px 14px">
                            @php
                                $methodMap = ['bank_transfer'=>['Transfer Bank','var(--apple-blue)'],'cash'=>['Tunai','var(--apple-green)'],'check'=>['Cek','var(--apple-orange)'],'credit_card'=>['Kartu Kredit','var(--apple-purple)'],'debit_card'=>['Kartu Debit','var(--apple-indigo)']];
                                $m = $methodMap[$mutation['payment_method'] ?? ''] ?? ['Lainnya','var(--dark-text-secondary)'];
                            @endphp
                            <span style="display:inline-flex;padding:2px 8px;border-radius:8px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $m[1] }} 15%,transparent);color:{{ $m[1] }}">{{ $m[0] }}</span>
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;font-weight:700;text-align:right;color:{{ $mutation['type'] == 'income' ? 'var(--apple-green)' : 'rgba(255,255,255,.15)' }}">
                            {{ $mutation['type'] == 'income' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '–' }}
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;font-weight:700;text-align:right;color:{{ $mutation['type'] == 'expense' ? 'var(--apple-red)' : 'rgba(255,255,255,.15)' }}">
                            {{ $mutation['type'] == 'expense' ? 'Rp ' . number_format($mutation['amount'], 0, ',', '.') : '–' }}
                        </td>
                        <td style="padding:12px 14px;font-size:0.82rem;text-align:right;font-family:monospace;color:var(--dark-text-primary)">Rp {{ number_format($mutation['balance'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align:center;padding:48px 20px">
            <div style="width:52px;height:52px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i class="fas fa-inbox" style="color:var(--apple-blue);font-size:1.2rem"></i>
            </div>
            <p style="font-size:0.92rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Tidak Ada Transaksi</p>
            <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">Belum ada transaksi untuk periode yang dipilih</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleCustomDates(val) {
    var show = val === 'custom';
    document.getElementById('customDates').style.display = show ? 'block' : 'none';
    document.getElementById('customDatesEnd').style.display = show ? 'block' : 'none';
}
function exportToCSV() {
    var cashAccountName = '{{ $cashAccount->account_name }}';
    var startDate = '{{ $startDate }}';
    var endDate = '{{ $endDate }}';
    var csv = 'Tanggal,Deskripsi,Referensi,Metode,Debit,Kredit,Saldo\n';
    @foreach($mutations as $mutation)
    csv += '"{{ \Carbon\Carbon::parse($mutation['date'])->format('d M Y') }}",';
    csv += '"{{ str_replace('"', '""', $mutation['description']) }}",';
    csv += '"{{ str_replace('"', '""', $mutation['reference'] ?? '') }}",';
    csv += '"{{ $mutation['payment_method'] ?? '' }}",';
    csv += '{{ $mutation['type'] == 'income' ? $mutation['amount'] : '0' }},';
    csv += '{{ $mutation['type'] == 'expense' ? $mutation['amount'] : '0' }},';
    csv += '{{ $mutation['balance'] }}\n';
    @endforeach
    var blob = new Blob([csv], { type: 'text/csv' });
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url; a.download = 'mutasi_' + cashAccountName + '_' + startDate + '_' + endDate + '.csv';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endpush
@endsection
