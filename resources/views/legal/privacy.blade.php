@extends('new.layouts.app')

@section('title', 'Kebijakan Privasi - Bizmark.ID')
@section('description', 'Kebijakan Privasi PT Cangah Pajaratan Mandiri (Bizmark.ID) tentang pengumpulan, penggunaan, dan perlindungan data pribadi Anda.')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';
    $phoneRaw = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = 'tel:' . preg_replace('/\s+/', '', $phoneRaw);
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="section-badge mb-4">Legal</span>
            <h1 class="section-title mb-3">Kebijakan Privasi</h1>
            <p class="section-description" style="margin-left:0;">
                PT Cangah Pajaratan Mandiri (Bizmark.ID) · Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="card">
            <div class="content-prose">
                <h2>Pendahuluan</h2>
                <p>PT Cangah Pajaratan Mandiri ("Bizmark.ID", "kami", "kita", atau "milik kami") berkomitmen untuk melindungi privasi dan keamanan informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan melindungi informasi yang Anda berikan saat menggunakan layanan konsultasi perizinan industri kami, mengakses platform digital kami, maupun menggunakan alat digital gratis yang tersedia di website kami.</p>
                <p>Dengan menggunakan layanan, platform, atau alat digital kami, Anda menyetujui pengumpulan dan penggunaan informasi sesuai dengan kebijakan ini. Jika Anda tidak setuju dengan kebijakan ini, mohon untuk tidak menggunakan layanan kami.</p>

                <h2>Informasi yang Kami Kumpulkan</h2>
                <h3>1. Informasi yang Anda Berikan</h3>
                <p>Kami mengumpulkan informasi yang Anda berikan secara langsung kepada kami, termasuk:</p>
                <ul>
                    <li><strong>Data Identitas:</strong> Nama lengkap, alamat, nomor telepon, alamat email, nomor KTP/NPWP</li>
                    <li><strong>Data Perusahaan:</strong> Nama perusahaan, alamat kantor, bidang usaha, struktur organisasi, dokumen perusahaan (akta, SK, SIUP, dll)</li>
                    <li><strong>Data Perizinan:</strong> Informasi terkait jenis izin yang diajukan, lokasi proyek, data teknis fasilitas, dokumen lingkungan</li>
                    <li><strong>Data Komunikasi:</strong> Isi percakapan via email, WhatsApp, telepon, atau formulir kontak</li>
                    <li><strong>Data Transaksi:</strong> Riwayat pembayaran, invoice, kontrak layanan</li>
                </ul>

                <h3>1.2 Informasi dari Alat Digital Gratis</h3>
                <p>Saat Anda menggunakan alat digital gratis kami (seperti Polygon SHP Maker atau Kalkulator Perizinan), kami mengumpulkan:</p>
                <ul>
                    <li><strong>Data Pemohon:</strong> Nama perusahaan, nama kontak (contact person), alamat email, dan nomor WhatsApp/telepon</li>
                    <li><strong>Data Proyek/Lokasi:</strong> Nama lahan atau proyek, alamat administratif (provinsi, kabupaten/kota, kecamatan, kelurahan), koordinat geografis (latitude/longitude), luas area, dan keterangan proyek</li>
                    <li><strong>Data Persetujuan:</strong> Timestamp persetujuan Syarat &amp; Ketentuan saat menggunakan alat digital</li>
                </ul>

                <h3>1.3 Informasi yang Dikumpulkan Secara Otomatis</h3>
                <p>Saat Anda mengakses website kami, kami dapat mengumpulkan informasi teknis secara otomatis:</p>
                <ul>
                    <li>Alamat IP dan lokasi geografis</li>
                    <li>Jenis browser dan sistem operasi (user agent)</li>
                    <li>Halaman yang dikunjungi dan durasi kunjungan</li>
                    <li>Sumber referral (dari mana Anda menemukan kami)</li>
                    <li>Cookies dan teknologi pelacakan serupa</li>
                </ul>

                <h3>1.4 Penyimpanan Lokal (localStorage)</h3>
                <p>Beberapa alat digital kami menggunakan fitur localStorage pada browser Anda untuk menyimpan data sementara secara otomatis (auto-save), seperti data formulir yang sedang Anda isi. Data ini:</p>
                <ul>
                    <li>Tersimpan secara lokal di perangkat Anda, bukan di server kami</li>
                    <li>Dapat dihapus melalui pengaturan browser atau fitur "Hapus Data" pada alat digital terkait</li>
                    <li>Digunakan untuk memulihkan pekerjaan Anda jika browser ditutup secara tidak sengaja</li>
                    <li>Tidak dikirimkan ke server kami sampai Anda secara aktif mengirimkan formulir</li>
                </ul>

                <h2>Bagaimana Kami Menggunakan Informasi Anda</h2>
                <p>Kami menggunakan informasi yang dikumpulkan untuk tujuan berikut:</p>
                <ul>
                    <li><strong>Penyediaan Layanan:</strong> Memproses pengajuan perizinan, menyiapkan dokumen, berkomunikasi dengan instansi pemerintah</li>
                    <li><strong>Komunikasi:</strong> Mengirimkan update progress, notifikasi penting, konfirmasi pembayaran, laporan berkala</li>
                    <li><strong>Administrasi:</strong> Mengelola akun Anda, memproses pembayaran, mengirim invoice dan dokumen kontrak</li>
                    <li><strong>Peningkatan Layanan:</strong> Menganalisis penggunaan website dan alat digital, meningkatkan kualitas layanan, mengembangkan fitur baru</li>
                    <li><strong>Kepatuhan Hukum:</strong> Memenuhi kewajiban hukum, merespons permintaan otoritas, melindungi hak dan keamanan</li>
                    <li><strong>Pemasaran:</strong> Mengirimkan informasi tentang layanan baru, penawaran khusus (dengan persetujuan Anda)</li>
                    <li><strong>Tindak Lanjut Leads:</strong> Data yang dikumpulkan melalui alat digital gratis akan digunakan untuk menghubungi Anda guna menawarkan layanan konsultasi yang relevan dengan proyek atau kebutuhan Anda</li>
                </ul>

                <h2>Pembagian Informasi dengan Pihak Ketiga</h2>
                <p>Kami dapat membagikan informasi Anda kepada:</p>
                <ul>
                    <li><strong>Instansi Pemerintah:</strong> Kementerian, dinas, dan lembaga terkait untuk proses perizinan</li>
                    <li><strong>Mitra Konsultan:</strong> Konsultan ahli (AMDAL, HSE, teknik) yang membantu dalam proyek Anda</li>
                    <li><strong>Penyedia Layanan:</strong> Penyedia layanan IT, cloud storage, payment gateway yang mendukung operasional kami</li>
                    <li><strong>Auditor &amp; Legal:</strong> Auditor, konsultan hukum, atau penasihat profesional untuk keperluan compliance</li>
                    <li><strong>Penegak Hukum:</strong> Saat diwajibkan oleh hukum, perintah pengadilan, atau proses hukum lainnya</li>
                </ul>
                <p>Kami memastikan pihak ketiga terikat kewajiban kerahasiaan dan hanya menggunakan informasi sesuai instruksi kami.</p>

                <h2>Keamanan Data</h2>
                <p>Kami menerapkan langkah-langkah keamanan teknis dan organisasi untuk melindungi informasi Anda:</p>
                <ul>
                    <li>Enkripsi data saat transmisi (SSL/TLS)</li>
                    <li>Akses terbatas hanya untuk karyawan yang berwenang</li>
                    <li>Sistem backup dan disaster recovery</li>
                    <li>Pemantauan aktivitas mencurigakan secara berkala</li>
                    <li>Perjanjian kerahasiaan dengan karyawan dan mitra</li>
                </ul>
                <p>Namun, tidak ada metode transmisi melalui internet atau penyimpanan elektronik yang 100% aman. Kami akan terus berupaya melindungi informasi Anda tetapi tidak dapat menjamin keamanan absolut.</p>

                <h2>Hak Anda</h2>
                <p>Sesuai dengan peraturan perlindungan data yang berlaku, Anda memiliki hak untuk:</p>
                <ul>
                    <li><strong>Akses:</strong> Meminta salinan informasi pribadi yang kami simpan tentang Anda</li>
                    <li><strong>Koreksi:</strong> Meminta pembaruan atau perbaikan informasi yang tidak akurat</li>
                    <li><strong>Penghapusan:</strong> Meminta penghapusan informasi Anda (dengan pengecualian tertentu)</li>
                    <li><strong>Pembatasan:</strong> Meminta pembatasan pemrosesan informasi Anda</li>
                    <li><strong>Portabilitas:</strong> Meminta transfer data Anda ke penyedia layanan lain</li>
                    <li><strong>Keberatan:</strong> Menolak pemrosesan informasi untuk tujuan tertentu</li>
                    <li><strong>Penarikan Persetujuan:</strong> Menarik persetujuan yang telah diberikan sebelumnya</li>
                </ul>
                <p>Untuk menggunakan hak Anda, silakan hubungi kami di <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>.</p>

                <h2>Penyimpanan dan Retensi Data</h2>
                <p>Kami menyimpan informasi pribadi Anda selama diperlukan untuk:</p>
                <ul>
                    <li>Memberikan layanan yang Anda minta</li>
                    <li>Memenuhi kewajiban hukum dan peraturan (minimal 5 tahun untuk dokumen perizinan)</li>
                    <li>Menyelesaikan sengketa dan menegakkan perjanjian</li>
                </ul>
                <p>Setelah periode retensi berakhir, kami akan menghapus atau menganonimkan informasi Anda secara aman.</p>

                <h2>Cookies dan Teknologi Pelacakan</h2>
                <p>Website kami menggunakan cookies dan teknologi serupa untuk:</p>
                <ul>
                    <li>Mengingat preferensi dan pengaturan Anda</li>
                    <li>Menganalisis traffic dan perilaku pengguna</li>
                    <li>Meningkatkan performa dan keamanan website</li>
                    <li>Menyediakan konten dan iklan yang relevan</li>
                </ul>
                <p>Anda dapat mengatur browser untuk menolak cookies atau memberikan notifikasi saat cookies dikirim. Namun, beberapa fitur website mungkin tidak berfungsi dengan baik tanpa cookies.</p>

                <h2>Tautan ke Website Pihak Ketiga</h2>
                <p>Website kami dapat berisi tautan ke website pihak ketiga. Kami tidak bertanggung jawab atas praktik privasi atau konten website tersebut. Kami menyarankan Anda untuk membaca kebijakan privasi setiap website yang Anda kunjungi.</p>

                <h2>Perubahan Kebijakan Privasi</h2>
                <p>Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan diposting di halaman ini dengan tanggal "Terakhir diperbarui" yang baru. Penggunaan layanan kami setelah perubahan berarti Anda menerima kebijakan yang diperbarui.</p>

                <h2>Privasi Anak-anak</h2>
                <p>Layanan kami tidak ditujukan untuk individu berusia di bawah 18 tahun. Kami tidak secara sengaja mengumpulkan informasi pribadi dari anak-anak. Jika Anda mengetahui bahwa seorang anak telah memberikan informasi pribadi kepada kami, silakan hubungi kami agar kami dapat mengambil tindakan yang diperlukan.</p>

                <h2>Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan, kekhawatiran, atau ingin menggunakan hak Anda terkait kebijakan privasi ini, silakan hubungi kami:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                    <li><strong>Telepon:</strong> <a href="{{ $phoneHref }}">{{ $phoneRaw }}</a></li>
                    <li><strong>Alamat:</strong> PT Cangah Pajaratan Mandiri, Karawang, Jawa Barat 41361, Indonesia</li>
                </ul>
            </div>
        </article>
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="text-gray-900 mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Ada Pertanyaan tentang Privasi?</h2>
        <p class="mb-7 text-gray-600">Tim kami siap membantu menjawab pertanyaan Anda tentang bagaimana kami melindungi data Anda.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="mailto:{{ $supportEmail }}" class="btn btn-primary"><i class="fas fa-envelope"></i> Kirim Email</a>
            <a href="{{ route('landing.id') }}" class="btn btn-ghost"><i class="fas fa-home"></i> Kembali ke Beranda</a>
        </div>
    </div>
</section>
@endsection
