@extends('layouts.admin')

@section('title', 'Data KBLI')
@section('page-title', 'Data KBLI')

@section('content')
@php
    $totalKbli = $kbliStats['total'] ?? 0;
    $bySector = $kbliStats['by_sector'] ?? collect();
    $topSectors = $bySector->sortByDesc('count')->take(5);
@endphp

    {{-- Hero Section --}}
    <section class="card-elevated rounded-apple-xl p-5 md:p-6 relative overflow-hidden mb-6">
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
            <div class="w-48 h-48 bg-apple-green opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
        </div>
        <div class="relative space-y-5 md:space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2.5 max-w-3xl">
                    <p class="text-sm uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Master Data</p>
                    <h1 class="text-2xl md:text-xl font-bold" style="color: #FFFFFF;">
                        Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)
                    </h1>
                    <p class="text-sm md:text-base" style="color: rgba(235,235,245,0.75);">
                        Database lengkap kode KBLI untuk mengidentifikasi bidang usaha klien dan rekomendasi perizinan berbasis AI.
                    </p>
                </div>
                <div class="space-y-2.5 text-sm" style="color: rgba(235,235,245,0.65);">
                    <p><i class="fas fa-database mr-2"></i>Sinkronisasi terakhir: {{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <p><i class="fas fa-shield-alt mr-2"></i>Akses Khusus Admin</p>
                    <div class="flex gap-3 flex-wrap">
                        <button onclick="showImportModal()" class="btn-primary-sm">
                            <i class="fas fa-upload mr-2"></i>Import CSV
                        </button>
                        <a href="{{ route('admin.settings.kbli.export') }}" class="btn-secondary-sm">
                            <i class="fas fa-download mr-2"></i>Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(10,132,255,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(10,132,255,0.9);">Total KBLI</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color:#FFFFFF;">{{ number_format($totalKbli) }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Klasifikasi tersedia</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(52,199,89,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(52,199,89,0.9);">Sektor Aktif</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color: rgba(52,199,89,1);">{{ $bySector->count() }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">Sektor A-U</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(191,90,242,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(191,90,242,0.9);">Sektor Teratas</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color:#FFFFFF;">{{ $topSectors->first()->sector ?? 'T/A' }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $topSectors->first()->count ?? 0 }} klasifikasi</p>
                </div>
                <div class="rounded-apple-lg p-3.5 md:p-4" style="background: rgba(255,149,0,0.12);">
                    <p class="text-xs uppercase tracking-widest" style="color: rgba(255,149,0,0.9);">Status Data</p>
                    <h2 class="text-lg font-bold mt-1.5" style="color:#FFFFFF;">{{ $totalKbli > 0 ? '✓' : '✗' }}</h2>
                    <p class="text-xs" style="color: rgba(235,235,245,0.6);">{{ $totalKbli > 0 ? 'Siap digunakan' : 'Perlu impor' }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success flex items-center gap-3 mb-5">
            <i class="fas fa-check-circle text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger flex items-center gap-3 mb-5">
            <i class="fas fa-exclamation-circle text-lg"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Quick Actions & Stats --}}
    <section class="space-y-3 md:space-y-4 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.4em]" style="color: rgba(235,235,245,0.5);">Pengelolaan Data</p>
                <h2 class="text-xl font-semibold text-white">Alat Impor dan Ekspor</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Kelola basis data KBLI dengan fitur impor CSV, ekspor data, dan templat standar.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-apple flex items-center justify-center" style="background: rgba(10,132,255,0.15);">
                        <i class="fas fa-upload text-xl" style="color: rgba(10,132,255,0.9);"></i>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(10,132,255,0.18); color: rgba(10,132,255,0.9);">Utama</span>
                </div>
                <h3 class="text-sm font-semibold text-white">Impor Data KBLI</h3>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Unggah berkas CSV dengan format standar untuk menambah atau memperbarui data KBLI. Mendukung pembaruan massal.
                </p>
                <button onclick="showImportModal()" class="w-full btn-primary-sm">
                    <i class="fas fa-upload mr-2"></i>Impor CSV
                </button>
            </div>

            <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-apple flex items-center justify-center" style="background: rgba(52,199,89,0.15);">
                        <i class="fas fa-file-csv text-xl" style="color: rgba(52,199,89,0.9);"></i>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(52,199,89,0.18); color: rgba(52,199,89,0.9);">Ekspor</span>
                </div>
                <h3 class="text-sm font-semibold text-white">Unduh Templat</h3>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Unduh templat CSV dengan format dan contoh data yang sudah sesuai standar untuk memudahkan impor.
                </p>
                <a href="{{ route('admin.settings.kbli.template') }}" class="w-full btn-secondary-sm block text-center">
                    <i class="fas fa-download mr-2"></i>Unduh Templat
                </a>
            </div>

            <div class="card-elevated rounded-apple-lg p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="w-12 h-12 rounded-apple flex items-center justify-center" style="background: rgba(255,59,48,0.15);">
                        <i class="fas fa-trash-alt text-xl" style="color: rgba(255,59,48,0.9);"></i>
                    </div>
                    <span class="text-xs px-3 py-1 rounded-full" style="background: rgba(255,59,48,0.18); color: rgba(255,59,48,0.9);">Danger</span>
                </div>
                <h3 class="text-sm font-semibold text-white">Hapus Semua Data</h3>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                    Menghapus seluruh data KBLI dari database. Tindakan ini tidak dapat dibatalkan. Gunakan dengan hati-hati.
                </p>
                <button onclick="confirmClearData()" class="w-full btn-secondary-sm" style="border-color: rgba(255,59,48,0.5); color: rgba(255,59,48,0.9);">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Hapus Semua
                </button>
            </div>
        </div>
    </section>

    {{-- Sector Distribution --}}
    <section class="card-elevated rounded-apple-xl p-6 space-y-4 mb-5">
        <div class="flex items-center justify-between flex-wrap gap-2.5">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Distribusi</p>
                <h2 class="text-base font-semibold text-white">Breakdown per Sektor</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Jumlah klasifikasi KBLI berdasarkan sektor bisnis A-U.</p>
            </div>
            <span class="text-xs" style="color: rgba(235,235,245,0.65);">Total {{ $totalKbli }} klasifikasi</span>
        </div>

        @if($bySector->count() > 0)
            <div class="space-y-4">
                @foreach($bySector->sortBy('sector') as $stat)
                    @php
                        $sectorNames = [
                            'A' => 'Pertanian, Kehutanan, Perikanan',
                            'B' => 'Pertambangan dan Penggalian',
                            'C' => 'Industri Pengolahan',
                            'D' => 'Pengadaan Listrik, Gas',
                            'E' => 'Pengelolaan Air & Limbah',
                            'F' => 'Konstruksi',
                            'G' => 'Perdagangan Besar & Eceran',
                            'H' => 'Transportasi & Pergudangan',
                            'I' => 'Akomodasi & Makan Minum',
                            'J' => 'Informasi & Komunikasi',
                            'K' => 'Jasa Keuangan & Asuransi',
                            'L' => 'Real Estate',
                            'M' => 'Jasa Profesional, Ilmiah, Teknis',
                            'N' => 'Jasa Persewaan & Sewa Guna',
                            'O' => 'Administrasi Pemerintahan',
                            'P' => 'Jasa Pendidikan',
                            'Q' => 'Jasa Kesehatan & Sosial',
                            'R' => 'Kesenian, Hiburan, Rekreasi',
                            'S' => 'Kegiatan Jasa Lainnya',
                            'T' => 'Jasa Perorangan Rumah Tangga',
                            'U' => 'Badan Internasional'
                        ];
                        $sectorName = $sectorNames[$stat->sector] ?? 'Unknown';
                        $percentage = $totalKbli > 0 ? round(($stat->count / $totalKbli) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm" style="color: rgba(235,235,245,0.8);">
                            <span><strong class="text-white">{{ $stat->sector }}</strong> – {{ $sectorName }}</span>
                            <span class="font-semibold text-white">{{ $stat->count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-white/10">
                            <div class="h-full rounded-full bg-gradient-to-r from-apple-blue to-apple-green" style="width: {{ min(100, $percentage) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-database text-4xl mb-3" style="color: rgba(235,235,245,0.3);"></i>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Belum ada data KBLI. Silakan impor berkas CSV terlebih dahulu.</p>
            </div>
        @endif
    </section>

    {{-- Data Preview --}}
    @if($totalKbli > 0)
    <section class="card-elevated rounded-apple-xl p-6 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs uppercase tracking-[0.35em]" style="color: rgba(235,235,245,0.5);">Pratinjau</p>
                <h2 class="text-base font-semibold text-white">Contoh Data KBLI</h2>
                <p class="text-sm" style="color: rgba(235,235,245,0.65);">Menampilkan beberapa klasifikasi dari basis data untuk referensi.</p>
            </div>
            <a href="{{ route('admin.settings.kbli.export') }}" class="btn-secondary-sm">
                <i class="fas fa-download mr-2"></i>Ekspor Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="text-left">Kode</th>
                        <th class="text-left">Deskripsi</th>
                        <th class="text-left">Sektor</th>
                        <th class="text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sampleData = \App\Models\Kbli::orderBy('code')->limit(10)->get();
                    @endphp
                    @forelse($sampleData as $kbli)
                        <tr>
                            <td class="font-mono text-sm">{{ $kbli->code }}</td>
                            <td class="text-sm">{{ Str::limit($kbli->description, 60) }}</td>
                            <td class="text-sm">{{ $kbli->sector }}</td>
                            <td class="text-sm" style="color: rgba(235,235,245,0.6);">{{ Str::limit($kbli->notes ?? '—', 40) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4" style="color: rgba(235,235,245,0.65);">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-center" style="color: rgba(235,235,245,0.5);">Menampilkan 10 dari {{ number_format($totalKbli) }} klasifikasi. Ekspor untuk melihat semua data.</p>
    </section>
    @endif

{{-- Import Modal --}}
<div id="importModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-xl max-w-2xl w-full p-6 space-y-5" style="background: var(--dark-bg-elevated);">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-white">Impor Data KBLI</h3>
                <p class="text-sm mt-1" style="color: rgba(235,235,245,0.65);">Unggah berkas CSV dengan format standar</p>
            </div>
            <button onclick="closeImportModal()" class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-white/10 transition-apple">
                <i class="fas fa-times" style="color: rgba(235,235,245,0.7);"></i>
            </button>
        </div>

        <form action="{{ route('admin.settings.kbli.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div>
                <label for="csv_file" class="block text-sm font-medium text-white mb-2">File CSV</label>
                <div class="relative">
                    <input type="file" 
                           name="csv_file" 
                           id="csv_file" 
                           accept=".csv,.txt"
                           required
                           class="w-full">
                </div>
                <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">
                    Format: code, description, sector, notes. Max 10MB.
                </p>
            </div>

            <div class="flex items-start gap-3 p-4 rounded-apple" style="background: rgba(255,149,0,0.08); border: 1px solid rgba(255,149,0,0.2);">
                <input type="checkbox" name="clear_existing" id="clear_existing" value="1" class="mt-0.5">
                <div class="flex-1">
                    <label for="clear_existing" class="text-sm font-medium text-white cursor-pointer">
                        Hapus semua data yang ada sebelum import
                    </label>
                    <p class="text-xs mt-1" style="color: rgba(235,235,245,0.6);">
                        <i class="fas fa-exclamation-triangle mr-1" style="color: rgba(255,149,0,0.9);"></i>
                        Gunakan opsi ini untuk replace total database KBLI.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-upload mr-2"></i>Impor Data
                </button>
                <button type="button" onclick="closeImportModal()" class="btn-secondary-sm px-6">
                    Batal
                </button>
            </div>
        </form>

        <div class="pt-4 border-t" style="border-color: var(--dark-separator);">
            <p class="text-xs font-semibold text-white mb-2">Format CSV yang benar:</p>
            <pre class="text-xs p-3 rounded-apple overflow-x-auto" style="background: rgba(0,0,0,0.4); color: rgba(235,235,245,0.8);">code,description,sector,notes
62010,Aktivitas Pemrograman Komputer,J,Contoh data
62020,Aktivitas Konsultasi Komputer,J,Konsultan IT</pre>
            <p class="text-xs mt-2" style="color: rgba(235,235,245,0.6);">
                <i class="fas fa-info-circle mr-1"></i>
                Unduh templat untuk panduan lengkap format CSV.
            </p>
        </div>
    </div>
</div>

{{-- Clear Confirmation Modal --}}
<div id="clearModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="card-elevated rounded-apple-xl max-w-md w-full p-6 space-y-5" style="background: var(--dark-bg-elevated);">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-apple flex items-center justify-center flex-shrink-0" style="background: rgba(255,59,48,0.15);">
                <i class="fas fa-exclamation-triangle text-xl" style="color: rgba(255,59,48,0.9);"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white">Konfirmasi Penghapusan</h3>
                <p class="text-sm mt-1" style="color: rgba(235,235,245,0.65);">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>

        <p class="text-sm" style="color: rgba(235,235,245,0.75);">
            Anda akan menghapus <strong class="text-white">{{ number_format($totalKbli) }} klasifikasi KBLI</strong> dari database. 
            Data yang dihapus tidak dapat dikembalikan.
        </p>

        <form action="{{ route('admin.settings.kbli.clear') }}" method="POST" class="space-y-3">
            @csrf
            @method('DELETE')
            
            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary flex-1" style="background: linear-gradient(135deg, var(--apple-red) 0%, #C0392B 100%);">
                    <i class="fas fa-trash-alt mr-2"></i>Ya, Hapus Semua
                </button>
                <button type="button" onclick="closeClearModal()" class="btn-secondary-sm px-6">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function confirmClearData() {
    document.getElementById('clearModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeClearModal() {
    document.getElementById('clearModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImportModal();
        closeClearModal();
    }
});

// Close modals on backdrop click
document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) closeImportModal();
});

document.getElementById('clearModal').addEventListener('click', function(e) {
    if (e.target === this) closeClearModal();
});
</script>
@endpush
