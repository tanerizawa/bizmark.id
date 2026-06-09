@extends('layouts.admin')

@section('title', 'Compliance Monitor — Admin')

@push('styles')
<style>
    .cm-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: .875rem;
        margin-bottom: 1.5rem;
    }

    .cm-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: .875rem;
        padding: 1rem;
        text-align: center;
    }

    .cm-stat .num { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .cm-stat .lbl { font-size: .75rem; color: #64748b; margin-top: 4px; }

    .filter-bar {
        display: flex;
        gap: .625rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
        align-items: flex-end;
    }

    .filter-bar select, .filter-bar input {
        border: 1px solid #e2e8f0;
        border-radius: .5rem;
        padding: .5rem .75rem;
        font-size: .875rem;
        background: #fff;
    }

    .permit-table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }

    .permit-table th {
        background: #f8fafc;
        padding: .625rem .875rem;
        text-align: left;
        font-size: .75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        border-bottom: 1px solid #e2e8f0;
    }

    .permit-table td {
        padding: .75rem .875rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .permit-table tr:hover td { background: #fafafa; }

    .badge {
        font-size: .7rem;
        font-weight: 700;
        padding: .2rem .5rem;
        border-radius: 999px;
        text-transform: uppercase;
    }

    .badge-active         { background: #dcfce7; color: #166534; }
    .badge-expiring_soon  { background: #fef9c3; color: #854d0e; }
    .badge-expired        { background: #fee2e2; color: #991b1b; }
    .badge-renewed        { background: #ede9fe; color: #4c1d95; }

    .days-chip {
        font-size: .75rem;
        padding: .15rem .5rem;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Compliance Monitor</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pantau semua izin klien dan kirim notifikasi expiry</p>
        </div>
        <div class="flex gap-2">
            <button
                onclick="document.getElementById('add-modal').classList.remove('hidden')"
                class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Monitor
            </button>
            <span class="btn btn-secondary btn-sm text-slate-500 cursor-default" title="Cek expiry otomatis berjalan setiap hari pukul 08:00">
                <i class="fas fa-clock mr-1"></i> Auto: 08:00 WIB
            </span>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat">
            <p class="num text-slate-800">{{ $stats['total'] }}</p>
            <p class="lbl">Total Monitor</p>
        </div>
        <div class="cm-stat">
            <p class="num text-green-600">{{ $stats['active'] }}</p>
            <p class="lbl">Aktif</p>
        </div>
        <div class="cm-stat">
            <p class="num text-amber-500">{{ $stats['expiring_soon'] }}</p>
            <p class="lbl">Segera Expire</p>
        </div>
        <div class="cm-stat">
            <p class="num text-red-500">{{ $stats['expired'] }}</p>
            <p class="lbl">Expired</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" class="filter-bar">
        <select name="status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="active" @selected(request('status') === 'active')>Aktif</option>
            <option value="expiring_soon" @selected(request('status') === 'expiring_soon')>Segera Expire</option>
            <option value="expired" @selected(request('status') === 'expired')>Expired</option>
            <option value="renewed" @selected(request('status') === 'renewed')>Diperbarui</option>
        </select>
        <select name="client_id" onchange="this.form.submit()">
            <option value="">Semua Klien</option>
            @foreach($clients as $c)
                <option value="{{ $c->id }}" @selected(request('client_id') == $c->id)>
                    {{ $c->company_name ?: $c->name }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="permit-table">
                <thead>
                    <tr>
                        <th>Klien</th>
                        <th>Jenis Izin</th>
                        <th>No. Izin</th>
                        <th>Expire</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th>Notifikasi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitors as $monitor)
                    @php $days = $monitor->daysUntilExpiry(); @endphp
                    <tr>
                        <td>
                            <p class="font-semibold text-slate-800">{{ $monitor->client?->company_name ?: $monitor->client?->name ?? '-' }}</p>
                            @if($monitor->project)
                                <p class="text-xs text-slate-400">{{ $monitor->project->name }}</p>
                            @endif
                        </td>
                        <td class="font-medium">{{ $monitor->permit_type }}</td>
                        <td class="text-slate-500">{{ $monitor->permit_number ?? '—' }}</td>
                        <td>{{ $monitor->expires_at->format('d M Y') }}</td>
                        <td>
                            @if($days > 0)
                                <span class="days-chip" style="background:{{ $days <= 7 ? '#fee2e2' : ($days <= 30 ? '#fef9c3' : '#f0fdf4') }};color:{{ $days <= 7 ? '#991b1b' : ($days <= 30 ? '#854d0e' : '#166534') }}">
                                    {{ $days }}h
                                </span>
                            @elseif($days === 0)
                                <span class="days-chip" style="background:#fee2e2;color:#991b1b">Hari ini</span>
                            @else
                                <span class="days-chip" style="background:#f1f5f9;color:#64748b">{{ abs($days) }}h lalu</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $monitor->status }}">
                                {{ match($monitor->status) {
                                    'active'        => 'Aktif',
                                    'expiring_soon' => 'Expiring',
                                    'expired'       => 'Expired',
                                    'renewed'       => 'Renewed',
                                } }}
                            </span>
                        </td>
                        <td class="text-xs text-slate-400">
                            <span title="H-90">{{ $monitor->notified_90 ? '✓90' : '·90' }}</span>
                            <span title="H-30" class="ml-1">{{ $monitor->notified_30 ? '✓30' : '·30' }}</span>
                            <span title="H-7" class="ml-1">{{ $monitor->notified_7 ? '✓7' : '·7' }}</span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.compliance.destroy', $monitor->id) }}"
                                  onsubmit="return confirm('Hapus monitor ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 text-sm" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-slate-400">Belum ada data monitor.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($monitors->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $monitors->links() }}</div>
        @endif
    </div>

</div>

{{-- Add Modal --}}
<div id="add-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-slate-900">Tambah Monitor Izin</h3>
            <button onclick="document.getElementById('add-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.compliance.store') }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Klien *</label>
                    <select name="client_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih klien...</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">{{ $c->company_name ?: $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Proyek *</label>
                    <select name="project_id" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih proyek...</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Jenis Izin *</label>
                <input type="text" name="permit_type" required placeholder="contoh: SIUP, NIB, HO..."
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">No. Izin</label>
                    <input type="text" name="permit_number" placeholder="—"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600 block mb-1">Tanggal Terbit</label>
                    <input type="date" name="issued_at"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Tanggal Expire *</label>
                <input type="date" name="expires_at" required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-600 block mb-1">Catatan</label>
                <textarea name="notes" rows="2" placeholder="Opsional..."
                          class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm resize-none"></textarea>
            </div>
            <div class="flex gap-2 justify-end pt-1">
                <button type="button" onclick="document.getElementById('add-modal').classList.add('hidden')"
                        class="btn btn-secondary btn-sm">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Monitor</button>
            </div>
        </form>
    </div>
</div>
@endsection
