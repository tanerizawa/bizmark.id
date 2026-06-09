@extends('new.layouts.app')

@section('title', 'Syarat & Ketentuan - Bizmark.ID')
@section('description', 'Syarat dan Ketentuan penggunaan layanan konsultasi perizinan PT Cangah Pajaratan Mandiri (Bizmark.ID).')

@section('content')
@php
    $contact = (array) data_get(config('landing_metrics'), 'contact', []);
    $supportEmail = $contact['email'] ?? 'info@bizmark.id';
    $phoneRaw = $contact['phone'] ?? '+62 838 7960 2855';
    $phoneHref = 'tel:' . preg_replace('/\s+/', '', $phoneRaw);
    $whatsappLink = $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855';
@endphp

<section class="relative overflow-hidden pt-28 pb-16 bg-[var(--bg-raised)] border-b border-gray-200">
    <div class="container-wide">
        <div class="max-w-4xl">
            <span class="section-badge mb-4">Legal</span>
            <h1 class="section-title mb-3">Syarat &amp; Ketentuan</h1>
            <p class="section-description" style="margin-left:0;">PT Cangah Pajaratan Mandiri (Bizmark.ID) · Terakhir diperbarui: {{ now()->translatedFormat('d F Y') }}</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <article class="card">
            <div class="content-prose">
                <h2>Pendahuluan</h2>
                <p>Selamat datang di Bizmark.ID. Syarat dan Ketentuan ini mengatur penggunaan seluruh layanan yang disediakan oleh PT Cangah Pajaratan Mandiri ("Bizmark.ID", "kami", "kita", atau "milik kami"), termasuk layanan konsultasi perizinan industri, platform digital (website, aplikasi), serta alat digital gratis yang tersedia di website kami. Dengan menggunakan layanan kami, Anda ("Klien", "Pengguna", "Anda", atau "milik Anda") menyetujui untuk terikat dengan syarat dan ketentuan berikut.</p>
                <p>Harap membaca syarat dan ketentuan ini dengan seksama sebelum menggunakan layanan kami. Jika Anda tidak setuju dengan bagian mana pun dari syarat ini, mohon untuk tidak menggunakan layanan kami.</p>

                <h2>1. Penerimaan Syarat</h2>
                <p>Dengan mengakses website kami, menghubungi kami, atau menggunakan layanan kami, Anda mengakui bahwa:</p>
                <ul>
                    <li>Anda telah membaca, memahami, dan menyetujui Syarat &amp; Ketentuan ini</li>
                    <li>Anda memiliki kapasitas hukum untuk mengikatkan diri dalam perjanjian ini</li>
                    <li>Anda mewakili entitas bisnis yang sah dengan dokumen legal yang valid (untuk layanan berbayar)</li>
                    <li>Informasi yang Anda berikan adalah akurat dan lengkap</li>
                    <li>Anda akan mematuhi seluruh ketentuan yang berlaku</li>
                    <li>Penggunaan alat digital gratis di platform kami juga tunduk pada Syarat &amp; Ketentuan ini</li>
                </ul>

                <h2>2. Definisi Layanan</h2>
                <p>Bizmark.ID menyediakan layanan konsultasi dan pengurusan perizinan industri, termasuk namun tidak terbatas pada:</p>
                <ul>
                    <li><strong>AMDAL &amp; UKL-UPL:</strong> Dokumen analisis dampak lingkungan dan pengelolaan lingkungan</li>
                    <li><strong>Izin Lingkungan:</strong> Persetujuan lingkungan untuk kegiatan usaha</li>
                    <li><strong>PROPER:</strong> Program penilaian peringkat kinerja perusahaan dalam pengelolaan lingkungan</li>
                    <li><strong>Izin Berusaha (OSS):</strong> Perizinan berusaha berbasis risiko melalui Online Single Submission</li>
                    <li><strong>IPAL &amp; SLO:</strong> Instalasi pengolahan air limbah dan surat layak operasi</li>
                    <li><strong>IMB &amp; SLF:</strong> Izin mendirikan bangunan dan sertifikat laik fungsi</li>
                    <li><strong>Sertifikasi K3:</strong> Sertifikasi kesehatan dan keselamatan kerja</li>
                    <li><strong>Konsultasi HSE:</strong> Health, Safety, and Environment consultation</li>
                    <li><strong>Monitoring Lingkungan Digital:</strong> Sistem pemantauan lingkungan berbasis IoT dan real-time dashboard</li>
                </ul>

                <h3>2.2 Alat Digital Gratis</h3>
                <p>Selain layanan berbayar, Bizmark.ID menyediakan alat digital gratis yang dapat diakses melalui platform kami, antara lain:</p>
                <ul>
                    <li><strong>Polygon SHP Maker:</strong> Alat pembuatan file Shapefile (.shp) untuk keperluan perizinan OSS, perencanaan lahan, dan pemetaan wilayah proyek</li>
                    <li><strong>Kalkulator Perizinan:</strong> Alat estimasi biaya dan waktu pengurusan perizinan berdasarkan jenis industri dan izin</li>
                </ul>
                <p>Alat digital gratis ini disediakan "sebagaimana adanya" (as-is) untuk membantu pengguna. Penggunaan alat digital gratis tunduk pada Syarat &amp; Ketentuan dan Kebijakan Privasi yang berlaku.</p>

                <h2>3. Kewajiban Klien</h2>
                <h3>3.1 Penyediaan Dokumen</h3>
                <p>Klien wajib menyediakan:</p>
                <ul>
                    <li>Dokumen perusahaan lengkap dan terkini (Akta, SK Kemenkumham, NPWP, dll)</li>
                    <li>Data teknis fasilitas/proyek yang akurat dan detail</li>
                    <li>Informasi lingkungan dan operasional yang dibutuhkan</li>
                    <li>Surat kuasa dan dokumen pendukung lainnya</li>
                    <li>Akses lokasi untuk survey dan inspeksi lapangan</li>
                </ul>
                <h3>3.2 Keakuratan Informasi</h3>
                <p>Klien menjamin bahwa seluruh informasi dan dokumen yang diberikan adalah benar, akurat, dan tidak menyesatkan. Klien bertanggung jawab penuh atas kebenaran data dan konsekuensi hukum yang timbul dari informasi yang salah atau tidak lengkap.</p>
                <h3>3.3 Kerja Sama</h3>
                <p>Klien wajib memberikan kerja sama yang baik, termasuk merespons permintaan informasi, menyediakan akses lokasi, dan memfasilitasi koordinasi dengan pihak terkait dalam waktu yang wajar.</p>

                <h2>4. Ketentuan Pembayaran</h2>
                <h3>4.1 Struktur Biaya</h3>
                <p>Biaya layanan akan diinformasikan melalui penawaran resmi (quotation) yang mencakup:</p>
                <ul>
                    <li>Biaya konsultasi dan pengurusan dokumen</li>
                    <li>Biaya survey lapangan dan analisis teknis</li>
                    <li>Biaya administrasi pemerintah (retribusi, PNBP)</li>
                    <li>Biaya lain-lain yang terkait dengan proyek</li>
                </ul>
                <h3>4.2 Skema Pembayaran</h3>
                <p>Pembayaran umumnya dilakukan secara bertahap:</p>
                <ul>
                    <li><strong>Down Payment (DP):</strong> 30-50% dari nilai kontrak saat penandatanganan</li>
                    <li><strong>Progress Payment:</strong> 30-40% saat tahap tertentu selesai (misal: draft dokumen)</li>
                    <li><strong>Final Payment:</strong> 20-30% saat izin terbit atau dokumen final diserahkan</li>
                </ul>
                <p>Skema pembayaran dapat disesuaikan berdasarkan kesepakatan dalam kontrak kerja sama.</p>
                <h3>4.3 Keterlambatan Pembayaran</h3>
                <p>Keterlambatan pembayaran dapat mengakibatkan penundaan proses dan/atau dikenakan denda sesuai kesepakatan. Kami berhak menghentikan layanan jika pembayaran tertunggak lebih dari 30 hari.</p>
                <h3>4.4 Biaya Tambahan</h3>
                <p>Biaya tambahan di luar quotation awal (seperti revisi dokumen mayor, perubahan scope, atau biaya pemerintah yang tidak terduga) akan diinformasikan dan disetujui terlebih dahulu oleh Klien.</p>

                <h2>5. Jangka Waktu dan Penyelesaian</h2>
                <h3>5.1 Timeline Proyek</h3>
                <p>Estimasi waktu penyelesaian akan ditetapkan dalam kontrak kerja sama berdasarkan kompleksitas proyek, kelengkapan dokumen, dan proses di instansi terkait. Timeline bersifat estimasi dan dapat berubah karena faktor di luar kendali kami.</p>
                <h3>5.2 Keterlambatan di Luar Kendali</h3>
                <p>Kami tidak bertanggung jawab atas keterlambatan yang disebabkan oleh:</p>
                <ul>
                    <li>Keterlambatan Klien dalam menyediakan dokumen atau informasi</li>
                    <li>Proses review dan persetujuan di instansi pemerintah</li>
                    <li>Perubahan regulasi atau kebijakan pemerintah</li>
                    <li>Force majeure (bencana alam, pandemi, kerusuhan, dll)</li>
                    <li>Revisi mayor yang diminta Klien atau instansi terkait</li>
                </ul>

                <h2>6. Batasan Tanggung Jawab</h2>
                <h3>6.1 Ruang Lingkup Tanggung Jawab</h3>
                <p>Bizmark.ID bertanggung jawab untuk:</p>
                <ul>
                    <li>Menyusun dokumen perizinan sesuai standar dan regulasi yang berlaku</li>
                    <li>Melakukan koordinasi dengan instansi pemerintah terkait</li>
                    <li>Memberikan update berkala tentang progress pengerjaan</li>
                    <li>Menjaga kerahasiaan informasi Klien</li>
                </ul>
                <h3>6.2 Batasan</h3>
                <p>Kami TIDAK bertanggung jawab atas:</p>
                <ul>
                    <li>Penolakan izin oleh instansi pemerintah karena kondisi objektif lokasi/proyek</li>
                    <li>Kerugian finansial atau operasional Klien akibat proses perizinan yang lama</li>
                    <li>Perubahan kebijakan atau regulasi pemerintah selama proses berjalan</li>
                    <li>Informasi yang salah atau tidak lengkap yang diberikan oleh Klien</li>
                    <li>Tindakan atau kelalaian pihak ketiga (instansi pemerintah, konsultan lain, dll)</li>
                    <li>Kondisi force majeure yang menghambat proses</li>
                </ul>
                <h3>6.3 Batas Ganti Rugi</h3>
                <p>Dalam hal terjadi klaim, total tanggung jawab kami terbatas pada nilai kontrak layanan yang telah dibayarkan. Kami tidak bertanggung jawab atas kerugian tidak langsung, kerugian konsekuensial, atau kehilangan profit.</p>

                <h2>7. Kerahasiaan</h2>
                <p>Kedua belah pihak sepakat untuk:</p>
                <ul>
                    <li>Menjaga kerahasiaan informasi bisnis, teknis, dan pribadi yang diperoleh selama kerja sama</li>
                    <li>Tidak mengungkapkan informasi rahasia kepada pihak ketiga tanpa persetujuan tertulis</li>
                    <li>Menggunakan informasi hanya untuk tujuan pelaksanaan layanan</li>
                    <li>Mengembalikan atau menghancurkan informasi rahasia setelah kerja sama berakhir</li>
                </ul>
                <p>Kewajiban kerahasiaan tidak berlaku untuk informasi yang: (a) sudah menjadi domain publik, (b) diwajibkan diungkapkan oleh hukum, atau (c) dikembangkan secara independen.</p>

                <h2>8. Hak Kekayaan Intelektual</h2>
                <p>Kepemilikan hak kekayaan intelektual diatur sebagai berikut:</p>
                <ul>
                    <li><strong>Dokumen Proyek:</strong> Dokumen perizinan yang disusun untuk Klien menjadi milik Klien setelah pembayaran lunas</li>
                    <li><strong>Template &amp; Metodologi:</strong> Template, metodologi, dan know-how yang kami gunakan tetap menjadi milik Bizmark.ID</li>
                    <li><strong>Data Klien:</strong> Data dan informasi yang diberikan Klien tetap menjadi milik Klien</li>
                    <li><strong>Portfolio:</strong> Kami berhak mencantumkan proyek Klien dalam portfolio kami (tanpa data sensitif) kecuali ada kesepakatan lain</li>
                </ul>

                <h2>9. Penghentian Layanan</h2>
                <h3>9.1 Penghentian oleh Klien</h3>
                <p>Klien dapat menghentikan layanan dengan pemberitahuan tertulis minimal 7 hari sebelumnya. Klien tetap wajib membayar biaya untuk pekerjaan yang telah diselesaikan dan biaya yang sudah dikeluarkan.</p>
                <h3>9.2 Penghentian oleh Bizmark.ID</h3>
                <p>Kami berhak menghentikan layanan jika:</p>
                <ul>
                    <li>Klien melanggar syarat dan ketentuan ini</li>
                    <li>Terjadi keterlambatan pembayaran lebih dari 30 hari</li>
                    <li>Klien tidak memberikan dokumen/informasi yang diperlukan setelah 3x reminder</li>
                    <li>Ditemukan informasi palsu atau menyesatkan dari Klien</li>
                    <li>Terjadi force majeure yang berkepanjangan</li>
                </ul>

                <h2>10. Penyelesaian Sengketa</h2>
                <p>Dalam hal terjadi perselisihan atau sengketa:</p>
                <ol>
                    <li>Kedua belah pihak akan berupaya menyelesaikan secara musyawarah dalam waktu 30 hari</li>
                    <li>Jika musyawarah gagal, sengketa akan diselesaikan melalui mediasi</li>
                    <li>Jika mediasi tidak berhasil, sengketa akan diselesaikan melalui arbitrase atau pengadilan</li>
                    <li>Hukum yang berlaku adalah hukum Republik Indonesia</li>
                    <li>Domisili hukum yang dipilih adalah Pengadilan Negeri Karawang</li>
                </ol>

                <h2>11. Penggunaan Alat Digital dan Platform</h2>
                <h3>11.1 Ketentuan Penggunaan Alat Digital Gratis</h3>
                <p>Dengan menggunakan alat digital gratis yang tersedia di platform Bizmark.ID, Anda menyetujui bahwa:</p>
                <ul>
                    <li>Anda wajib memberikan data yang akurat saat mengisi formulir yang diperlukan untuk menggunakan alat digital</li>
                    <li>Data yang Anda masukkan akan disimpan oleh Bizmark.ID sebagai data leads untuk tujuan tindak lanjut komersial</li>
                    <li>Bizmark.ID berhak menghubungi Anda melalui email atau WhatsApp/telepon yang Anda berikan untuk menawarkan layanan terkait</li>
                    <li>Anda bertanggung jawab atas kebenaran dan keakuratan data yang dimasukkan ke dalam alat digital</li>
                    <li>Hasil yang dihasilkan oleh alat digital bersifat estimasi dan tidak dapat dijadikan dasar hukum</li>
                </ul>
                <h3>11.2 Pengumpulan Data melalui Alat Digital</h3>
                <p>Saat menggunakan alat digital gratis, data berikut akan dikumpulkan dan disimpan:</p>
                <ul>
                    <li><strong>Data Pemohon:</strong> Nama perusahaan, nama kontak (contact person), alamat email, dan nomor WhatsApp/telepon</li>
                    <li><strong>Data Proyek/Lokasi:</strong> Nama lahan/proyek, alamat administratif (provinsi, kabupaten/kota, kecamatan, kelurahan), koordinat geografis, dan keterangan proyek</li>
                    <li><strong>Data Teknis:</strong> Alamat IP, informasi browser (user agent), dan timestamp persetujuan syarat &amp; ketentuan</li>
                    <li><strong>Data Penyimpanan Lokal:</strong> Browser Anda mungkin menyimpan data sementara (localStorage) untuk fitur auto-save</li>
                </ul>
                <h3>11.3 Batasan Tanggung Jawab Alat Digital</h3>
                <p>Bizmark.ID tidak bertanggung jawab atas:</p>
                <ul>
                    <li>Ketidakakuratan hasil perhitungan atau output dari alat digital</li>
                    <li>Kerugian yang timbul dari penggunaan hasil alat digital sebagai dasar keputusan bisnis</li>
                    <li>Kegagalan teknis, gangguan server, atau ketidaktersediaan alat digital</li>
                    <li>Kehilangan data yang tersimpan secara lokal di browser pengguna</li>
                    <li>Penyalahgunaan file atau data yang dihasilkan oleh alat digital oleh pihak ketiga</li>
                </ul>
                <h3>11.4 Penggunaan yang Dilarang</h3>
                <p>Pengguna dilarang untuk:</p>
                <ul>
                    <li>Menggunakan alat digital untuk tujuan ilegal atau melanggar hukum</li>
                    <li>Melakukan scraping, reverse engineering, atau eksploitasi otomatis terhadap alat digital</li>
                    <li>Memasukkan data palsu atau menyesatkan</li>
                    <li>Menggunakan alat digital dengan cara yang dapat merusak, melumpuhkan, atau membebani server kami</li>
                    <li>Mendistribusikan ulang atau memodifikasi alat digital tanpa izin tertulis</li>
                </ul>

                <h2>12. Perubahan Syarat &amp; Ketentuan</h2>
                <p>Kami berhak mengubah atau memperbarui Syarat &amp; Ketentuan ini dari waktu ke waktu. Perubahan akan diposting di website dengan tanggal "Terakhir diperbarui" yang baru. Untuk proyek yang sedang berjalan, syarat yang berlaku adalah syarat saat kontrak ditandatangani. Penggunaan layanan baru setelah perubahan berarti Anda menerima syarat yang diperbarui.</p>

                <h2>13. Ketentuan Lain-lain</h2>
                <h3>13.1 Keseluruhan Perjanjian</h3>
                <p>Syarat &amp; Ketentuan ini, bersama dengan kontrak kerja sama dan dokumen terkait lainnya, merupakan keseluruhan perjanjian antara para pihak dan menggantikan semua perjanjian sebelumnya.</p>
                <h3>13.2 Keterpisahan</h3>
                <p>Jika ada ketentuan yang dinyatakan tidak sah atau tidak dapat dilaksanakan, ketentuan lainnya tetap berlaku sepenuhnya.</p>
                <h3>13.3 Pengabaian</h3>
                <p>Kegagalan kami untuk menegakkan suatu hak atau ketentuan tidak dianggap sebagai pengabaian hak atau ketentuan tersebut.</p>
                <h3>13.4 Pengalihan</h3>
                <p>Klien tidak dapat mengalihkan hak atau kewajiban berdasarkan perjanjian ini tanpa persetujuan tertulis dari kami. Kami dapat mengalihkan hak dan kewajiban kami kepada pihak ketiga.</p>

                <h2>Hubungi Kami</h2>
                <p>Jika Anda memiliki pertanyaan tentang Syarat &amp; Ketentuan ini atau ingin mendiskusikan kerja sama, silakan hubungi kami:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></li>
                    <li><strong>Telepon:</strong> <a href="{{ $phoneHref }}">{{ $phoneRaw }}</a></li>
                    <li><strong>WhatsApp:</strong> <a href="{{ $whatsappLink }}" target="_blank" rel="noopener">{{ $phoneRaw }}</a></li>
                    <li><strong>Alamat:</strong> PT Cangah Pajaratan Mandiri, Karawang, Jawa Barat 41361, Indonesia</li>
                </ul>
            </div>
        </article>
    </div>
</section>

<section class="section-sm section-premium">
    <div class="container-wide text-center">
        <h2 class="text-gray-900 mb-3" style="font-size:clamp(1.5rem,3vw,2.1rem);font-weight:750;">Siap Memulai Kerja Sama?</h2>
        <p class="mb-7 text-gray-600">Konsultasikan kebutuhan perizinan Anda dengan tim ahli kami.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            <a href="{{ route('landing.id') }}" class="btn btn-ghost"><i class="fas fa-home"></i> Kembali ke Beranda</a>
        </div>
    </div>
</section>
@endsection
