

# **Rancangan Teknis Platform SaaS Pengelolaan Perizinan dan Manajemen Usaha UMKM**

Sebagai seorang Arsitek Perangkat Lunak Senior dan Insinyur DevOps yang sangat berpengalaman dalam solusi SaaS yang skalabel dan penerapan yang dioptimalkan pada infrastruktur terbatas, laporan ini menyajikan rancangan teknis yang komprehensif untuk platform SaaS pengelolaan perizinan dan manajemen usaha UMKM. Rancangan ini berfokus pada pemanfaatan Next.js (TypeScript) untuk *frontend*, NestJS (TypeScript) untuk *backend*, dan PostgreSQL sebagai basis data, semuanya dalam arsitektur *modular-monolith*.

## **I. Pendahuluan**

### **A. Latar Belakang dan Tujuan Proyek**

Proyek ini bertujuan untuk mengembangkan platform *Software-as-a-Service* (SaaS) yang komprehensif, dirancang khusus untuk memenuhi kebutuhan unik Usaha Mikro, Kecil, dan Menengah (UMKM) dalam pengelolaan perizinan dan manajemen usaha. Di tengah kompleksitas regulasi dan tuntutan administratif, UMKM seringkali menghadapi tantangan signifikan dalam mengelola kepatuhan, pelaporan keuangan, dan dokumentasi. Platform ini dirancang untuk menyederhanakan proses-proses ini, memungkinkan UMKM untuk beroperasi lebih efisien dan fokus pada pertumbuhan bisnis inti mereka.

Tujuan utama dari platform ini adalah untuk menghadirkan solusi SaaS yang berkinerja tinggi, aman, dan ramah pengguna, yang dapat beroperasi secara optimal bahkan pada sumber daya *Virtual Private Server* (VPS) yang terbatas (8GB RAM, 2-4 *core*). Dengan memanfaatkan arsitektur *modular-monolith* dan teknologi *cutting-edge* seperti Next.js, NestJS, dan PostgreSQL, platform ini akan melayani kebutuhan spesifik dari tiga jenis pengguna utama: admin sistem, klien (pemilik UMKM), dan konsultan yang memberikan layanan kepada UMKM.

### **B. Ringkasan Kebutuhan Fungsional dan Non-Fungsional**

Platform yang diusulkan harus memenuhi serangkaian kebutuhan fungsional dan non-fungsional yang ketat untuk memastikan keberhasilan dan adopsi yang luas di kalangan UMKM.

Kebutuhan Fungsional:  
Platform ini akan menyediakan fitur-fitur inti berikut:

* **Pengelolaan Pengajuan Izin:** Sistem harus memungkinkan pengelolaan siklus hidup pengajuan izin secara lengkap, mulai dari pengajuan awal hingga persetujuan atau penolakan. Fitur krusial adalah kemampuan untuk menambahkan jenis izin secara dinamis, memungkinkan platform beradaptasi dengan perubahan regulasi atau kebutuhan bisnis baru tanpa perlu modifikasi kode yang signifikan.  
* **Laporan Administrasi Perusahaan:** Penyediaan laporan yang relevan dengan administrasi perusahaan, seperti daftar karyawan, aset, dan informasi operasional lainnya.  
* **Laporan Keuangan dan Pajak:** Generasi laporan keuangan (misalnya, laba rugi, neraca) dan laporan pajak (fiskal dan non-fiskal) yang akurat dan sesuai standar.  
* **Manajemen Dokumen Izin dan Lisensi:** Repositori terpusat untuk menyimpan, mengelola, dan melacak semua dokumen terkait izin dan lisensi, memastikan akses mudah dan kepatuhan.  
* **Pelacakan Izin dan Klien:** Fitur pelacakan yang jelas untuk memantau status pengajuan izin dan mengelola informasi klien secara terorganisir.  
* **Sistem Notifikasi:** Pemberitahuan otomatis kepada pengguna mengenai status izin, tenggat waktu, atau pembaruan penting lainnya.  
* ***Background Job*** **Berkala:** Pelaksanaan tugas-tugas otomatis di latar belakang, seperti pembuatan laporan periodik, sinkronisasi data, atau pembersihan data lama.

Kebutuhan Non-Fungsional:  
Aspek non-fungsional yang akan menjadi fokus utama meliputi:

* **Performa:** Platform harus beroperasi secara optimal dan responsif pada lingkungan VPS dengan spesifikasi terbatas (8GB RAM, 2-4 *core*). Ini memerlukan optimasi menyeluruh di seluruh *stack* teknologi.  
* **Keamanan:** Keamanan data merupakan prioritas tertinggi, terutama mengingat penanganan data sensitif seperti informasi keuangan, pajak, dan perizinan. Implementasi *Role-Based Access Control* (RBAC) yang ketat akan membatasi akses pengguna sesuai peran mereka. Selain itu, mitigasi terhadap kerentanan keamanan umum (misalnya, OWASP Top 10\) akan diterapkan.  
* **Skalabilitas:** Meskipun dimulai sebagai *modular-monolith*, arsitektur harus dirancang untuk mendukung evolusi dan skalabilitas di masa depan, dengan jalur yang jelas untuk potensi transisi ke *microservices* jika volume pengguna atau kompleksitas fitur meningkat.  
* **Maintainability:** Kode harus terstruktur dengan baik, modular, dan mudah dikelola untuk memastikan keberlanjutan jangka panjang, memfasilitasi penambahan fitur baru, dan penyelesaian *bug* secara efisien.  
* **Pengalaman Pengguna (UX):** Antarmuka pengguna harus intuitif, dengan pelacakan yang jelas dan notifikasi yang responsif untuk memastikan pengalaman yang mulus bagi admin, klien, dan konsultan.

## **II. Gambaran Umum Arsitektur Sistem**

### **A. Pilihan Arsitektur: Modular-Monolith**

Platform ini akan mengadopsi arsitektur *modular-monolith*, sebuah pendekatan yang menyeimbangkan kesederhanaan *monolith* dengan keuntungan modularitas. Arsitektur ini sangat cocok untuk proyek awal yang kompleks dengan sumber daya terbatas, karena memungkinkan pengelolaan aplikasi yang komprehensif sambil tetap membuka jalan bagi skalabilitas di masa depan.1

**Justifikasi dan Keuntungan:**

* **Keseimbangan Optimal:** *Modular-monolith* mendorong organisasi kode yang sangat baik, memecah aplikasi menjadi modul-modul yang lebih kecil dan independen, masing-masing dengan tanggung jawab yang spesifik.2 Pendekatan ini meningkatkan kemudahan pemeliharaan, kemampuan pengujian, dan reusabilitas komponen, yang sangat penting untuk platform UMKM yang mencakup berbagai fitur manajemen bisnis dan perizinan.  
* **Pengembangan dan Pemeliharaan yang Efisien:** Dengan memisahkan fungsionalitas ke dalam modul-modul yang kohesif, pengembang dapat bekerja secara independen pada bagian-bagian aplikasi yang berbeda, mempercepat proses pengembangan dan menyederhanakan pemecahan masalah atau pembaruan.2 Hal ini mengurangi risiko "efek riak" di mana perubahan di satu area secara tidak sengaja memengaruhi area lain.  
* **Pengelolaan Kompleksitas:** Untuk aplikasi dengan cakupan fungsional yang luas seperti manajemen perizinan dan keuangan, *modular-monolith* membantu mengelola kompleksitas dengan mendefinisikan batasan yang jelas antara domain-domain bisnis yang berbeda. Ini mencegah *codebase* menjadi "monolitik spaghetti" yang sulit dipahami dan dimodifikasi.

Strategi Evolusi Masa Depan (Strangler Fig Pattern):  
Pilihan arsitektur modular-monolith secara inheren mendukung transisi bertahap menuju arsitektur microservices di masa depan, jika kebutuhan bisnis dan volume pengguna menuntutnya. Strategi migrasi yang direkomendasikan adalah Strangler Fig Pattern.6

* **Pendekatan Bertahap:** *Strangler Fig Pattern* memungkinkan penggantian bagian-bagian *monolith* secara perlahan dengan layanan baru yang independen, tanpa perlu melakukan penulisan ulang besar-besaran ("*big bang*") yang berisiko.6 Proses ini melibatkan pengenalan lapisan fasad (proxy) yang mencegat permintaan ke sistem lama dan secara bertahap mengalihkannya ke layanan baru yang telah dimodernisasi.7  
* **Meminimalkan Gangguan Bisnis:** Setiap fungsionalitas dapat diekstraksi dan dibangun kembali sebagai *microservice* secara terisolasi, memastikan gangguan minimal pada operasi platform yang sedang berjalan.6 Ini sangat penting untuk platform SaaS UMKM, di mana waktu henti dapat berdampak langsung pada operasional bisnis klien.  
* **Pemanfaatan Sumber Daya yang Pragmatis:** Desain *modular-monolith* secara alami membagi *codebase* menjadi unit-unit logis yang kohesif. Unit-unit ini, yang telah diatur dengan baik sebagai modul dalam *monolith*, menjadi kandidat ideal untuk diekstraksi menjadi *microservices* independen ketika kebutuhan bisnis muncul—misalnya, ketika modul tertentu menjadi hambatan kinerja atau memerlukan penskalaan independen. *Strangler Fig Pattern* kemudian menyediakan metodologi yang terstruktur untuk ekstraksi ini, memastikan kontinuitas bisnis selama proses migrasi. Pendekatan ini menawarkan yang terbaik dari kedua dunia: pengembangan dan penyebaran awal yang cepat pada sumber daya VPS terbatas (keuntungan *monolith*) sambil mempertahankan jalur yang jelas untuk evolusi skala tinggi di masa depan (keuntungan *microservice*) tanpa kompleksitas dan biaya awal dari arsitektur *microservices* penuh. Ini adalah pilihan pragmatis dan strategis untuk target pasar UMKM yang membutuhkan solusi yang dapat tumbuh bersama mereka.

### **B. Komponen Teknologi Utama**

Pemilihan teknologi yang cermat adalah fondasi untuk mencapai tujuan performa, keamanan, dan skalabilitas pada VPS terbatas.

* Next.js (Frontend):  
  Next.js adalah framework React yang kuat untuk membangun aplikasi web modern. Fitur utamanya adalah dukungan untuk berbagai strategi rendering, termasuk Client-Side Rendering (CSR), Server-Side Rendering (SSR), Static Site Generation (SSG), dan Incremental Static Regeneration (ISR).11 Fleksibilitas ini sangat penting untuk mengoptimalkan kinerja berbagai jenis halaman dalam platform, misalnya, menggunakan CSR untuk  
  *dashboard* yang sangat dinamis dan SSR untuk halaman yang membutuhkan *initial load* cepat atau penting untuk SEO. Next.js juga menyediakan *API Routes* bawaan yang menyederhanakan interaksi dengan *backend* untuk kasus penggunaan yang lebih sederhana.13  
* NestJS (Backend):  
  NestJS adalah framework Node.js progresif yang dibangun dengan TypeScript, dirancang untuk membangun aplikasi sisi server yang efisien, skalabel, dan mudah diuji.1 Desain modularnya, sistem  
  *Dependency Injection* (DI) yang kuat, dan adopsi pola arsitektur yang telah terbukti (seperti modul, *controller*, dan *provider*) sangat selaras dengan pendekatan *modular-monolith*.2 Hal ini memungkinkan pengembangan  
  *backend* yang terstruktur, mudah dipelihara, dan dapat diperluas.  
* PostgreSQL (Database):  
  PostgreSQL adalah sistem manajemen basis data relasional objek sumber terbuka yang dikenal luas karena ketahanan, ekstensibilitas, integritas data, dan kecepatannya.19 Kepatuhan ACID (Atomicity, Consistency, Isolation, Durability) sangat penting untuk menjaga integritas data keuangan dan perizinan yang sensitif.20 PostgreSQL juga mendukung berbagai strategi  
  *multi-tenancy*, termasuk penggunaan skema terpisah per *tenant* atau tabel bersama dengan *Row-Level Security* (RLS).22 Ini memungkinkan isolasi data yang kuat antar UMKM dalam satu instansi basis data, mengoptimalkan penggunaan sumber daya VPS.  
* Redis (Caching & Queuing):  
  Redis akan digunakan sebagai store untuk sistem caching dan message queue. Ini sangat penting untuk menangani pekerjaan latar belakang yang berjalan lama (melalui BullMQ) dan notifikasi real-time, sehingga mengalihkan tugas-tugas intensif dari thread aplikasi utama.26 Dengan demikian, responsivitas aplikasi tetap terjaga. Redis juga dapat digunakan untuk  
  *caching* data yang sering diakses, mengurangi frekuensi permintaan ke basis data dan meningkatkan kinerja secara keseluruhan.28  
* Reverse Proxy (Nginx/Caddy):  
  Sebuah reverse proxy akan bertindak sebagai gateway untuk semua permintaan masuk ke platform. Ini akan merutekan permintaan ke layanan frontend Next.js atau backend NestJS yang sesuai.29  
  *Reverse proxy* sangat penting untuk menangani terminasi SSL/TLS, menyediakan HTTPS otomatis (terutama dengan Caddy), dan berpotensi melakukan penyeimbangan beban di masa depan.34 Caddy, khususnya, menawarkan konfigurasi HTTPS otomatis yang sangat sederhana dengan Let's Encrypt, mengurangi kompleksitas manajemen sertifikat secara manual.34

## **III. Rancangan Frontend: Next.js (TypeScript)**

### **A. Struktur Aplikasi dan Modul**

Struktur aplikasi *frontend* akan dirancang untuk mencerminkan prinsip modularitas yang diterapkan pada *backend*, memastikan konsistensi dan kemudahan pemeliharaan di seluruh *stack*.

* **Organisasi Berbasis Fitur/Domain:** Aplikasi Next.js akan diorganisir ke dalam modul-modul logis berdasarkan fitur atau domain bisnis, seperti dashboard, perizinan, laporan, dan admin. Setiap modul akan memiliki direktori sendiri yang berisi semua komponen, *hook*, utilitas, dan logika terkait. Pendekatan ini mempromosikan kohesi tinggi dan kopling rendah antar bagian aplikasi.  
* **Pemanfaatan *File-System Routing* Next.js:** Next.js secara otomatis menangani *routing* berdasarkan struktur direktori pages/ atau app/ (tergantung pada versi Next.js yang digunakan). Ini akan dimanfaatkan untuk organisasi halaman yang jelas dan intuitif, meminimalkan kebutuhan untuk konfigurasi *routing* manual yang kompleks.  
* **Pemisahan Komponen (UI vs. Container):** Komponen akan dipisahkan secara hierarkis menjadi komponen UI (*User Interface*) dan komponen *container*.  
  * **Komponen UI (Presentasional):** Ini adalah komponen yang dapat digunakan kembali, fokus pada presentasi data dan interaksi pengguna, tanpa logika bisnis atau pengambilan data. Contohnya termasuk tombol, *form input*, tabel generik, atau modal.  
  * **Komponen *Container* (Logika & Data):** Ini adalah komponen yang bertanggung jawab untuk pengambilan data, logika bisnis, dan pengelolaan *state*. Mereka akan menggunakan komponen UI untuk menampilkan informasi. Pemisahan ini meningkatkan reusabilitas komponen UI dan membuat logika aplikasi lebih mudah diuji dan dipahami.  
* **Penyelarasan Arsitektur *Frontend* dan *Backend*:** Meskipun Next.js sendiri tidak memberlakukan struktur modular yang ketat seperti NestJS, mengadopsi organisasi modul berbasis fitur di *frontend* (misalnya, src/features/perizinan, src/features/laporan) akan mencerminkan desain *modular-monolith* *backend*. Konsistensi arsitektur ini di seluruh *stack* mengurangi beban kognitif bagi pengembang, karena mereka dapat dengan mudah memetakan domain bisnis di *backend* ke modul fitur yang sesuai di *frontend*. Hal ini juga menyederhanakan pemetaan panggilan API ke komponen UI yang relevan. Lebih jauh lagi, pendekatan ini secara strategis mempersiapkan *frontend* untuk potensi *decoupling* di masa depan menjadi *micro-frontends*, di mana setiap modul fitur dapat berevolusi menjadi unit yang dapat disebarkan secara independen tanpa memerlukan penulisan ulang lengkap struktur internal UI. Ini adalah langkah proaktif untuk skalabilitas *frontend* jangka panjang.

### **B. Strategi Rendering untuk Performa Optimal**

Next.js menawarkan berbagai strategi *rendering* yang dapat dimanfaatkan untuk mengoptimalkan performa aplikasi pada VPS terbatas.11 Pemilihan strategi yang tepat untuk setiap halaman atau komponen sangat penting untuk menyeimbangkan kecepatan pemuatan, responsivitas, dan efisiensi sumber daya

*server*.

* **Client-Side Rendering (CSR) untuk *Dashboard* Dinamis:**  
  * **Penerapan:** CSR akan menjadi pilihan utama untuk *dashboard* yang padat data, seperti *dashboard* klien atau konsultan yang menampilkan status izin *real-time*, ringkasan keuangan, atau metrik bisnis.12 Halaman-halaman ini cenderung memiliki konten yang sangat dinamis dan spesifik untuk setiap pengguna, sehingga  
    *rendering* di sisi *server* untuk setiap permintaan akan membebani VPS secara tidak perlu.  
  * **Manfaat:** Dengan CSR, *server* hanya mengirimkan berkas HTML minimal dan paket JavaScript, mendelegasikan tugas *rendering* konten ke *browser* klien. Ini secara signifikan mengurangi beban *server* awal dan memungkinkan interaktivitas yang lebih cepat setelah halaman dimuat.11 Untuk pengambilan data yang efisien,  
    *hook* useEffect React atau pustaka pengambilan data seperti SWR akan digunakan. SWR menyediakan fitur seperti *caching*, *revalidation*, dan *error retries*, yang meningkatkan kinerja dan pengalaman pengguna dengan memastikan data selalu segar dan tersedia dengan cepat.11  
* **Server-Side Rendering (SSR) untuk Halaman Kritis:**  
  * **Penerapan:** SSR akan digunakan untuk halaman-halaman yang membutuhkan data dinamis yang segar pada setiap permintaan, terutama untuk halaman yang kritis terhadap SEO atau halaman dengan konten sensitif dan spesifik pengguna yang harus langsung terlihat saat dimuat. Contohnya termasuk formulir aplikasi izin yang terperinci, halaman profil pengguna, atau laporan keuangan spesifik yang memerlukan validasi akses ketat sebelum ditampilkan.12  
  * **Manfaat:** SSR memastikan bahwa konten HTML sudah sepenuhnya dirender di *server* sebelum dikirim ke *browser* klien. Hal ini menghasilkan waktu pemuatan yang dirasakan lebih cepat bagi pengguna dan sangat menguntungkan untuk SEO, karena *crawler* mesin pencari dapat melihat konten lengkap tanpa perlu mengeksekusi JavaScript.12 Fungsi  
    getServerSideProps Next.js akan digunakan untuk mengambil data di sisi *server* pada setiap permintaan.14  
* **Kombinasi Strategis untuk VPS Terbatas:** Perpaduan strategis antara CSR dan SSR sangat penting untuk mengoptimalkan kinerja pada VPS terbatas. Dengan mengalihkan *rendering* dinamis ke klien jika memungkinkan (CSR untuk *dashboard*), sumber daya CPU dan memori *backend* dapat dihemat secara signifikan. Ini berarti *server* tidak perlu menghabiskan siklus komputasi untuk merender setiap elemen UI yang berubah secara dinamis berdasarkan interaksi pengguna. Sebaliknya, menggunakan SSR untuk halaman kritis dan sensitif data memastikan integritas data dan kecepatan pemuatan awal di mana itu paling penting, seperti halaman login, formulir pengajuan, atau laporan akhir yang memerlukan data paling mutakhir. Pendekatan hibrida ini memungkinkan aplikasi untuk memanfaatkan kekuatan setiap strategi, meminimalkan beban pada VPS tunggal dengan mengalokasikan tugas *rendering* secara cerdas, dan pada akhirnya menghasilkan pemanfaatan sumber daya yang lebih efisien dan pengalaman pengguna yang responsif.

## **IV. Rancangan Backend: NestJS (TypeScript)**

### **A. Struktur Modular-Monolith**

NestJS secara alami mendukung arsitektur modular, menjadikannya pilihan ideal untuk implementasi *modular-monolith*. Struktur ini akan mengorganisir *codebase* menjadi unit-unit fungsional yang terisolasi, mempromosikan pemeliharaan dan skalabilitas.

* **Modul Berbasis Domain/Fitur:** Aplikasi NestJS akan dibagi menjadi modul-modul yang mewakili domain bisnis atau fitur utama (misalnya, AuthModule, UsersModule, LicensesModule, ReportsModule, DocumentsModule, NotificationsModule, BackgroundJobsModule).2 Setiap modul akan mengelompokkan  
  *controller*, *service*, *provider*, dan entitas terkait, memastikan kohesi dan meminimalkan ketergantungan lintas modul.3  
* **Prinsip Injeksi Dependensi (DI):** NestJS dibangun di atas pola DI, yang akan dimanfaatkan secara ekstensif untuk mencapai kopling longgar antar komponen.2 Ini mempermudah pengujian unit dan integrasi, serta memungkinkan penggantian implementasi (misalnya,  
  *mocking* layanan untuk pengujian) tanpa memengaruhi kode lain.  
* **Batasan Komunikasi Antar Modul:** Komunikasi antar modul akan dibatasi dan diatur dengan ketat. Modul harus memperlakukan satu sama lain sebagai konteks bisnis yang terisolasi, hanya mengekspos antarmuka publik yang jelas melalui mekanisme *export* modul NestJS.4 Hal ini mencegah ketergantungan melingkar dan  
  *leaking implementation details*, menjaga integritas setiap domain.  
* **Penggunaan DTOs (Data Transfer Objects):** DTOs akan digunakan secara konsisten untuk validasi dan transformasi data masuk dari *frontend* dan keluar ke *frontend*.3 Pustaka  
  class-validator dan class-transformer akan diintegrasikan secara global melalui ValidationPipe untuk memastikan semua *payload* API divalidasi secara otomatis dan ketat sesuai dengan skema yang ditentukan.39 Ini sangat penting untuk keamanan data dan konsistensi.

### **B. Implementasi Fitur Utama**

* **Pengelolaan Pengajuan Izin (Jenis Izin Dinamis):**  
  * **Desain Skema Basis Data:** Skema basis data akan dirancang untuk mendukung jenis izin yang dapat ditambahkan secara dinamis. Ini dapat dicapai dengan tabel LicenseTypes yang menyimpan definisi izin (nama, deskripsi, bidang yang diperlukan dalam format JSON schema atau serupa) dan tabel LicenseApplications yang mereferensikan LicenseTypes dan menyimpan data pengajuan aktual (mungkin dalam kolom JSONB untuk fleksibilitas).43  
  * **Antarmuka Administratif:** Admin akan memiliki antarmuka untuk membuat, mengedit, dan menghapus jenis izin baru. Untuk mendukung ini, *backend* NestJS akan menyediakan API untuk manajemen skema izin dinamis, yang dapat digunakan oleh *frontend* untuk menghasilkan formulir pengajuan izin secara dinamis. NestJS dapat mendukung pembuatan formulir dinamis dari definisi skema.43  
  * **Alur Kerja Pengajuan:** Modul Licenses akan mengelola alur kerja pengajuan izin, termasuk validasi data, perubahan status, dan notifikasi.  
* **Laporan Administrasi Perusahaan, Keuangan, dan Pajak:**  
  * **Modul Pelaporan Khusus:** Sebuah modul Reports akan bertanggung jawab untuk menghasilkan berbagai jenis laporan. Modul ini akan mengintegrasikan logika agregasi data dari berbagai entitas basis data.46  
  * **Agregasi Data:** Untuk laporan keuangan dan pajak, data akan diagregasi dari tabel transaksi, aset, dan entitas terkait lainnya. NestJS dapat menggunakan fitur agregasi dari ORM (misalnya, TypeORM) atau kueri SQL mentah untuk perhitungan yang kompleks.46  
  * **Ekspor Laporan:** Laporan akan tersedia untuk diunduh dalam format umum (misalnya, PDF, Excel, CSV). Proses ekspor dapat ditangani sebagai *background job* jika laporan sangat besar untuk menghindari pemblokiran *thread* utama.26  
* **Manajemen Dokumen Izin dan Lisensi:**  
  * **Penyimpanan Dokumen:** Dokumen akan disimpan di sistem penyimpanan objek yang kompatibel dengan S3 (misalnya, MinIO di VPS atau layanan *cloud* seperti AWS S3 jika migrasi *cloud* di masa depan).48 NestJS akan mengelola  
    *upload*, *download*, dan penghapusan dokumen melalui API.  
  * **Keamanan Dokumen:** Akses ke dokumen akan diatur oleh RBAC. Dokumen sensitif dapat dienkripsi saat disimpan (*at rest*) dan dienkripsi saat transit (*in transit*) menggunakan SSL/TLS.51  
  * **Validasi *File Upload*:** NestJS dapat menggunakan multer dan ParseFilePipe untuk memvalidasi *file upload* (ukuran, tipe MIME) guna mencegah *upload* berbahaya.53  
* **Pelacakan Izin dan Klien:**  
  * **Relasi Basis Data:** Struktur basis data akan mencakup relasi yang jelas antara klien, izin, dan pengajuan. Setiap pengajuan izin akan memiliki referensi ke klien yang mengajukan dan jenis izin yang relevan.  
  * **API Pelacakan:** API akan memungkinkan pengguna untuk mencari, memfilter, dan melihat status izin dan detail klien secara efisien.  
* **Notifikasi:**  
  * **Sistem Antrean (BullMQ & Redis):** Notifikasi akan dikirim secara asinkron menggunakan BullMQ sebagai sistem antrean dengan Redis sebagai *backend*.26 Ini memastikan bahwa pengiriman notifikasi tidak memblokir alur kerja utama aplikasi.  
  * **Jenis Notifikasi:** Notifikasi dapat berupa email, SMS, atau notifikasi dalam aplikasi. Modul Notifications akan mengelola templat, penerima, dan status pengiriman.  
* **Background Job Berkala:**  
  * **BullMQ Scheduler:** BullMQ menyediakan kemampuan untuk menjadwalkan *job* berulang (misalnya, *cron jobs*).26 Ini akan digunakan untuk tugas-tugas seperti pembuatan laporan bulanan/tahunan, pembersihan data log, atau pembaruan status data secara periodik.  
  * **Worker Processes:** *Background job* akan diproses oleh *worker* terpisah yang juga terhubung ke Redis, memastikan bahwa tugas-tugas intensif sumber daya tidak memengaruhi performa aplikasi *real-time*.26

### **C. Keamanan dan RBAC (Role-Based Access Control)**

Keamanan adalah aspek krusial dari platform SaaS ini, terutama karena penanganan data sensitif UMKM. Implementasi RBAC yang ketat dan praktik keamanan terbaik akan menjadi prioritas.

* **RBAC (Role-Based Access Control):**  
  * **Definisi Peran dan Izin:** Sistem akan mendefinisikan peran (Admin, Klien, Konsultan) dan izin terperinci yang terkait dengan setiap peran (misalnya, create:license\_application, view:financial\_report, manage:users). Setiap pengguna akan diberi satu atau lebih peran.58  
  * **Implementasi di NestJS:** NestJS menyediakan *Guard* yang dapat digunakan untuk melindungi *endpoint* API berdasarkan peran atau izin pengguna.55  
    *Custom decorator* (@Roles()) akan digunakan untuk menandai *endpoint* dengan peran yang diperlukan.58 Sebuah  
    *Guard* (RolesGuard) akan mencegat permintaan, memeriksa peran pengguna yang terotentikasi, dan membandingkannya dengan peran yang diperlukan untuk *endpoint* tersebut.58 Pustaka seperti  
    rust-rbac (untuk Rust jika *backend* di masa depan beralih) atau implementasi kustom dengan nestjs-passport dan nestjs-jwt dapat digunakan untuk manajemen peran dan izin.62  
  * **RBAC di *Frontend*:** *Frontend* Next.js akan menyesuaikan antarmuka pengguna (misalnya, menampilkan/menyembunyikan menu atau tombol) berdasarkan peran dan izin pengguna yang diterima dari *backend*.64 Ini memastikan pengalaman pengguna yang dipersonalisasi dan mencegah pengguna melihat opsi yang tidak dapat mereka akses.  
* **Autentikasi dan Otorisasi (JWT & Refresh Token):**  
  * **JSON Web Tokens (JWT):** Autentikasi akan menggunakan JWT untuk mengamankan komunikasi antara *frontend* dan *backend*.55 JWT bersifat  
    *stateless*, yang ideal untuk skalabilitas.  
  * **Access Token & Refresh Token:** Untuk meningkatkan keamanan dan pengalaman pengguna, sistem akan menggunakan kombinasi *access token* berumur pendek dan *refresh token* berumur panjang.66  
    *Access token* akan digunakan untuk mengakses sumber daya yang dilindungi, sementara *refresh token* akan digunakan untuk mendapatkan *access token* baru ketika yang lama kadaluarsa, tanpa memerlukan *login* ulang.66  
    *Refresh token* akan disimpan dengan aman (misalnya, di *cookie* httpOnly dan secure) dan dapat dicabut jika terjadi kompromi.66  
* **Keamanan Data Sensitif:**  
  * **Enkripsi Data *at Rest*:** Data sensitif di basis data (misalnya, detail keuangan, nomor identifikasi) akan dienkripsi saat disimpan (*at rest*). Ini dapat dicapai melalui enkripsi tingkat sistem berkas pada VPS atau menggunakan fitur enkripsi kolom di PostgreSQL (misalnya, modul pgcrypto).51  
  * **Enkripsi Data *in Transit*:** Semua komunikasi antara *frontend*, *backend*, dan basis data akan dienkripsi menggunakan SSL/TLS (HTTPS).51  
    *Reverse proxy* (Nginx/Caddy) akan menangani terminasi SSL/TLS.34  
  * **Validasi Input dan Sanitasi:** Semua input dari pengguna akan divalidasi dan disanitasi secara ketat di *backend* untuk mencegah serangan injeksi (SQL Injection, XSS) dan kerentanan lainnya.69 NestJS  
    ValidationPipe dengan class-validator sangat efektif untuk ini.40  
  * **Manajemen Rahasia:** Kunci API, kredensial basis data, dan rahasia lainnya tidak akan di-*hardcode* dalam kode. Sebaliknya, mereka akan disimpan dengan aman sebagai variabel lingkungan atau menggunakan sistem manajemen rahasia.70 Untuk Next.js, variabel lingkungan yang sensitif hanya akan diakses di komponen  
    *server* atau *API Routes*.74  
* **Praktik Keamanan Umum (OWASP Top 10):**  
  * **Pembaruan Dependensi:** Secara teratur memperbarui semua *package* dan *library* ke versi terbaru untuk mendapatkan *patch* keamanan dan perbaikan *bug*.70  
  * ***Logging*** **Keamanan:** Menerapkan *logging* aplikasi yang komprehensif untuk melacak aktivitas pengguna, akses data, dan potensi insiden keamanan.55  
    *Log* harus disimpan di lokasi terpisah (misalnya, *remote server*) untuk mencegah penghapusan oleh penyerang.75  
  * ***Security Headers*****:** Mengonfigurasi *HTTP security headers* (misalnya, Strict-Transport-Security, X-Frame-Options, Content-Security-Policy) di *reverse proxy* atau Next.js untuk mitigasi serangan umum seperti *clickjacking* dan XSS.71

### **D. Multi-Tenancy**

Platform ini dirancang untuk melayani banyak UMKM, yang berarti implementasi *multi-tenancy* sangat penting.

* **Pilihan Strategi: Skema per *Tenant* atau Tabel Bersama dengan RLS:**  
  * **Skema per *Tenant*:** Dalam pendekatan ini, setiap UMKM (tenant) akan memiliki skema basis data terpisah di dalam satu basis data PostgreSQL yang sama.22 Setiap skema akan berisi tabel-tabel yang identik, tetapi data akan tersegregasi secara logis untuk setiap  
    *tenant*.  
    * **Keuntungan:** Memberikan isolasi data yang kuat, menyederhanakan pencadangan/pemulihan per *tenant* (jika diimplementasikan dengan hati-hati), dan memungkinkan penyesuaian skema yang lebih mudah per *tenant* jika diperlukan di masa depan.23  
    * **Kelemahan:** Kompleksitas pengelolaan skema meningkat seiring bertambahnya jumlah *tenant*, dan migrasi skema perlu diterapkan ke setiap skema *tenant*.23  
  * **Tabel Bersama dengan *Row-Level Security* (RLS):** Pendekatan ini menggunakan satu basis data dan skema tunggal untuk semua *tenant*, dengan data *tenant* diisolasi pada tingkat baris menggunakan kolom tenant\_id di setiap tabel.23 PostgreSQL RLS kemudian digunakan untuk memastikan bahwa pengguna hanya dapat mengakses baris data yang sesuai dengan  
    tenant\_id mereka.23  
    * **Keuntungan:** Paling sederhana dan hemat biaya dalam hal pengelolaan skema (perubahan diterapkan sekali untuk semua *tenant*), pemanfaatan sumber daya yang efisien, dan menyediakan kontrol keamanan yang sangat granular.23  
    * **Kelemahan:** Membutuhkan konfigurasi RLS yang cermat untuk menghindari kebocoran data, dan mungkin ada sedikit dampak kinerja pada kumpulan data yang sangat besar karena filter RLS.24  
  * **Rekomendasi Awal:** Untuk UMKM dengan VPS terbatas dan kebutuhan awal untuk efisiensi sumber daya, pendekatan **Tabel Bersama dengan RLS** lebih disukai. Ini menawarkan kesederhanaan dalam manajemen skema dan pemanfaatan sumber daya yang efisien, sambil tetap memberikan isolasi data yang kuat melalui RLS. Jika kepatuhan regulasi yang sangat ketat atau kebutuhan kustomisasi per *tenant* yang ekstrem muncul di masa depan, transisi ke skema per *tenant* atau bahkan basis data per *tenant* dapat dipertimbangkan menggunakan *Strangler Fig Pattern* untuk modul data.  
* **Penanganan Koneksi Basis Data:** NestJS dengan TypeORM dapat dikonfigurasi untuk menangani koneksi basis data secara dinamis berdasarkan konteks *tenant* dari permintaan masuk.77 Ini memastikan bahwa setiap permintaan diarahkan ke data  
  *tenant* yang benar.

## **V. Optimasi Performa pada VPS Terbatas**

Mengoptimalkan performa pada VPS dengan 8GB RAM dan 2-4 *core* adalah tantangan utama. Strategi akan berfokus pada efisiensi sumber daya di semua lapisan *stack*.

### **A. Optimasi PostgreSQL**

* **Konfigurasi postgresql.conf:** Penyesuaian parameter kunci di postgresql.conf sangat penting untuk memanfaatkan RAM yang tersedia secara efektif.19  
  * shared\_buffers: Atur ke 25-40% dari total RAM sistem (misalnya, 2GB-3.2GB untuk 8GB RAM) untuk *caching* data yang sering diakses dan mengurangi I/O disk.19  
  * work\_mem: Sesuaikan berdasarkan beban kerja, mulai dari 16MB atau 1-5% dari RAM total. Nilai ini per operasi, jadi beberapa operasi dalam satu kueri dapat melipatgandakan penggunaan memori.19  
  * wal\_buffers: Tingkatkan untuk beban kerja yang banyak menulis, misalnya sekitar 16MB-64MB, untuk meningkatkan kinerja penulisan dengan mengurangi frekuensi penulisan disk.19  
  * effective\_cache\_size: Atur ke 70-80% dari RAM sistem (misalnya, 5.6GB-6.4GB) untuk memberikan petunjuk kepada PostgreSQL tentang berapa banyak data yang dapat diharapkan ditemukan di *cache* sistem operasi.80  
  * max\_connections: Tetapkan jumlah koneksi yang wajar (misalnya, 4-8 \* jumlah *core* CPU), dan pertimbangkan penggunaan *connection pooler* seperti PgBouncer di depan PostgreSQL untuk mengelola koneksi secara efisien dan mencegah kesalahan "terlalu banyak koneksi".80  
* **Optimasi Sistem Operasi (Ubuntu):**  
  * **RAM dan I/O Disk:** RAM adalah faktor paling murah dan terbaik untuk kinerja PostgreSQL, karena memungkinkan lebih banyak *caching* dan mengurangi I/O disk.81 Penggunaan SSD/NVMe sangat direkomendasikan untuk I/O yang cepat.  
  * ***Huge Pages*****:** Mengaktifkan *huge pages* di Linux dapat meningkatkan kinerja PostgreSQL dengan mengalokasikan blok memori yang lebih besar, mengurangi *overhead* manajemen memori.81  
  * ***Noatime*****:** Nonaktifkan atime (timestamp akses terakhir) pada *filesystem* tempat data PostgreSQL disimpan untuk menghemat siklus CPU.81  
  * **Penyetelan Kernel:** Pertimbangkan untuk menyesuaikan parameter kernel seperti SHMMIN dan SHMAX untuk alokasi memori bersama PostgreSQL yang optimal.82  
* **Indeks dan Partisi:** Penggunaan indeks yang tepat pada kolom yang sering dikueri dan strategi partisi tabel (jika data sangat besar) akan meningkatkan kecepatan kueri secara signifikan.80  
* **Pemantauan:** Memantau kinerja basis data secara teratur menggunakan metrik bawaan PostgreSQL (pg\_stat\_activity, pg\_stat\_database) sangat penting untuk mengidentifikasi dan mengatasi hambatan kinerja.80

### **B. Optimasi NestJS Backend**

* **Pemanfaatan TypeScript dan DI:** Penggunaan TypeScript dan sistem DI NestJS secara penuh akan membantu dalam penulisan kode yang lebih aman, mudah dipelihara, dan berkinerja tinggi. TypeScript membantu menangkap kesalahan di waktu kompilasi, mengurangi *bug* di *runtime*.83  
* **Pengelolaan Memori:** Meskipun NestJS dan Node.js umumnya efisien, pemantauan penggunaan memori sangat penting pada VPS terbatas. Hindari kebocoran memori dengan mengelola siklus hidup objek dengan benar dan menggunakan *streaming* untuk operasi *file upload* yang besar.48  
* **Asynchronous Operations (BullMQ):** Memindahkan tugas-tugas yang berjalan lama (misalnya, pengiriman email, pembuatan laporan kompleks, pemrosesan dokumen) ke *background job* menggunakan BullMQ dan Redis akan membebaskan *thread* utama NestJS untuk menangani permintaan API secara responsif.26 Ini secara dramatis meningkatkan  
  *throughput* dan mengurangi latensi yang dirasakan pengguna.  
* **Caching (Redis):** Menerapkan *caching* dengan Redis untuk data yang sering diakses tetapi jarang berubah (misalnya, daftar jenis izin, data konfigurasi) akan mengurangi beban pada basis data dan mempercepat respons API.28  
* **Optimasi Dockerfile:** Gunakan *multi-stage builds* dalam Dockerfile untuk NestJS untuk menghasilkan *image* produksi yang ringan dan efisien. Tahap pertama akan membangun aplikasi (menginstal dependensi, kompilasi TypeScript), dan tahap kedua akan menyalin hanya artefak yang diperlukan untuk *runtime*.73 Ini mengurangi ukuran  
  *image*, waktu *deployment*, dan jejak memori.

### **C. Optimasi Next.js Frontend**

* **Strategi Rendering Hibrida:** Seperti yang dibahas sebelumnya, perpaduan CSR dan SSR akan digunakan untuk mengoptimalkan kinerja. CSR untuk *dashboard* dinamis akan mengurangi beban *server*, sedangkan SSR untuk halaman kritis akan memastikan pemuatan awal yang cepat.11  
* **Optimasi Gambar dan Aset:** Next.js memiliki fitur bawaan untuk optimasi gambar (next/image) yang secara otomatis mengoptimalkan gambar untuk berbagai ukuran layar dan memuatnya secara *lazy*.57 Ini sangat penting untuk mengurangi ukuran  
  *payload* dan mempercepat waktu pemuatan halaman. Aset statis lainnya (CSS, JavaScript) juga akan dioptimalkan dan dilayani secara efisien.  
* **Pengambilan Data Efisien (SWR):** Pustaka seperti SWR akan digunakan untuk pengambilan data di sisi klien. SWR menyediakan *caching* otomatis, *revalidation*, dan *error retries*, yang meningkatkan pengalaman pengguna dengan memastikan data selalu segar dan tersedia dengan cepat.11  
* **Optimasi Dockerfile:** Untuk *frontend* Next.js, *multi-stage Docker builds* juga akan digunakan untuk menghasilkan *image* produksi yang ramping. Tahap pembangunan akan mengkompilasi aplikasi, dan tahap *runtime* akan menyalin *output* yang diperlukan (.next/standalone) ke *image* dasar yang minimal (misalnya, Node.js Alpine).57 Penggunaan  
  .dockerignore juga penting untuk mengecualikan berkas yang tidak perlu dari konteks *build*.57

### **D. Optimasi Infrastruktur VPS**

* **Sistem Operasi Minimal:** Menggunakan instalasi Ubuntu Server yang minimal, hanya dengan *package* yang benar-benar diperlukan, akan mengurangi jejak memori dan permukaan serangan.75  
* ***Reverse Proxy*** **(Caddy/Nginx):** Caddy atau Nginx akan digunakan sebagai *reverse proxy*. Caddy lebih disukai karena konfigurasi HTTPS otomatisnya yang sederhana dan performa yang baik untuk sebagian besar kasus penggunaan.34 Nginx, meskipun lebih kompleks dalam konfigurasi SSL, menawarkan fleksibilitas dan kinerja yang terbukti pada beban tinggi.87 Keduanya dapat mengelola permintaan masuk dan merutekannya ke Next.js dan NestJS yang berjalan pada  
  *port* internal.29  
* **Pemantauan Sumber Daya:** Implementasi alat pemantauan dasar (misalnya, htop, netdata, atau metrik bawaan *cloud provider*) pada VPS untuk melacak penggunaan CPU, RAM, I/O disk, dan koneksi jaringan. Ini akan membantu mengidentifikasi hambatan kinerja dan melakukan penyesuaian konfigurasi yang diperlukan.  
* **CI/CD dengan Docker dan GitHub Actions:** *Deployment* akan diotomatisasi menggunakan GitHub Actions untuk membangun *image* Docker (untuk *frontend* dan *backend*) dan kemudian menyebarkannya ke VPS melalui SSH.88 Ini memastikan proses  
  *deployment* yang konsisten, cepat, dan bebas kesalahan.

## **VI. Kesimpulan dan Rekomendasi**

Rancangan teknis ini menyediakan cetak biru yang komprehensif untuk platform SaaS pengelolaan perizinan dan manajemen usaha UMKM, dengan fokus pada Next.js, NestJS, dan PostgreSQL dalam arsitektur *modular-monolith* yang dioptimalkan untuk VPS terbatas.

**Kesimpulan Utama:**

1. **Arsitektur Pragmatis:** Pilihan *modular-monolith* adalah keputusan strategis yang tepat. Ini memungkinkan pengembangan yang lebih cepat dan pemeliharaan yang lebih mudah di fase awal proyek, yang sangat penting untuk UMKM dengan sumber daya terbatas. Pada saat yang sama, desain modular ini secara alami mempersiapkan aplikasi untuk evolusi bertahap menuju *microservices* di masa depan melalui *Strangler Fig Pattern*, meminimalkan risiko dan gangguan bisnis.  
2. **Pemanfaatan Teknologi yang Efisien:** Kombinasi Next.js, NestJS, dan PostgreSQL, didukung oleh Redis untuk *caching* dan antrean, serta *reverse proxy* yang efisien, adalah *stack* teknologi yang kuat. Setiap komponen dipilih karena kemampuannya untuk berkinerja baik dan dioptimalkan dalam lingkungan sumber daya yang terbatas, sambil tetap menyediakan fitur-fitur modern yang diperlukan oleh platform SaaS.  
3. **Keamanan sebagai Fondasi:** Penekanan pada RBAC yang ketat, autentikasi berbasis JWT dengan *refresh token*, enkripsi data *at rest* dan *in transit*, serta validasi input yang komprehensif, membangun fondasi keamanan yang kuat. Ini sangat penting untuk menjaga kepercayaan pengguna, terutama dengan data keuangan dan perizinan yang sensitif.  
4. **Optimasi Sumber Daya yang Cermat:** Strategi optimasi yang dirinci untuk PostgreSQL, *backend* NestJS, *frontend* Next.js, dan infrastruktur VPS secara keseluruhan menunjukkan pemahaman mendalam tentang kendala sumber daya. Pendekatan hibrida untuk *rendering* di Next.js dan penggunaan *background job* di NestJS adalah contoh kunci bagaimana kinerja dapat dipertahankan tanpa perlu *hardware* yang mahal.

**Rekomendasi Aksi:**

1. **Prioritaskan MVP (Minimum Viable Product):** Meskipun rancangan ini komprehensif, disarankan untuk memulai dengan *Minimum Viable Product* (MVP) yang fokus pada fitur-fitur inti yang paling penting untuk memecahkan masalah utama UMKM.96 Ini akan memungkinkan validasi pasar yang cepat dan pengumpulan umpan balik pengguna awal sebelum menginvestasikan sumber daya yang signifikan ke dalam pengembangan fitur tambahan.96  
2. **Implementasi *Multi-Tenancy* dengan RLS:** Untuk efisiensi sumber daya pada VPS terbatas, mulailah dengan strategi *multi-tenancy* Tabel Bersama dengan *Row-Level Security* (RLS) di PostgreSQL. Ini akan menyederhanakan manajemen skema dan mengoptimalkan penggunaan sumber daya basis data.  
3. **Investasi pada Otomasi CI/CD:** Siapkan *pipeline Continuous Integration/Continuous Deployment* (CI/CD) yang kuat menggunakan GitHub Actions dan Docker sejak awal. Otomasi ini akan memastikan *deployment* yang cepat, konsisten, dan andal, mengurangi kesalahan manual dan mempercepat siklus pengembangan.73  
4. **Pemantauan Proaktif:** Implementasikan solusi pemantauan yang komprehensif untuk semua komponen *stack* (PostgreSQL, NestJS, Next.js, Redis, VPS). Pemantauan proaktif akan memungkinkan identifikasi dini masalah kinerja atau keamanan dan memfasilitasi optimasi berkelanjutan.  
5. **Audit Keamanan Berkala:** Lakukan audit keamanan dan pengujian penetrasi secara berkala untuk mengidentifikasi dan mengatasi potensi kerentanan. Pertahankan semua dependensi dan sistem operasi tetap mutakhir untuk mitigasi risiko keamanan.  
6. **Dokumentasi Menyeluruh:** Pertahankan dokumentasi teknis yang menyeluruh untuk arsitektur, modul, API, dan praktik *deployment*. Ini akan sangat berharga untuk pemeliharaan jangka panjang dan *onboarding* tim baru.

Dengan mengikuti rancangan ini dan menerapkan rekomendasi yang diuraikan, platform SaaS pengelolaan perizinan dan manajemen usaha UMKM dapat dibangun sebagai solusi yang tangguh, aman, dan berkinerja tinggi, yang mampu mendukung pertumbuhan UMKM di Indonesia secara berkelanjutan.

#### **Karya yang dikutip**

1. Microservices | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/microservices/basics](https://docs.nestjs.com/microservices/basics)  
2. 5 best practices for NestJS applications | Tech Tonic \- Medium, diakses Juli 29, 2025, [https://medium.com/deno-the-complete-reference/5-best-practices-for-nestjs-applications-831d0566a534](https://medium.com/deno-the-complete-reference/5-best-practices-for-nestjs-applications-831d0566a534)  
3. Best Practices for Structuring a NestJS Application | by @rnab \- Medium, diakses Juli 29, 2025, [https://arnab-k.medium.com/best-practices-for-structuring-a-nestjs-application-b3f627548220](https://arnab-k.medium.com/best-practices-for-structuring-a-nestjs-application-b3f627548220)  
4. NestJS Fundamentals Part 1: Modularity in NestJS \- DEV Community, diakses Juli 29, 2025, [https://dev.to/ehsanahmadzadeh/nestjs-fundamentals-part-1-modularity-in-nestjs-5d7p](https://dev.to/ehsanahmadzadeh/nestjs-fundamentals-part-1-modularity-in-nestjs-5d7p)  
5. Scalable Architecture with NestJS: Best Practices Guide \- Mindbowser, diakses Juli 29, 2025, [https://www.mindbowser.com/scalable-architecture-nestjs/](https://www.mindbowser.com/scalable-architecture-nestjs/)  
6. Migration From Monolith To Microservices Using Strangler Pattern \- Brainhub, diakses Juli 21, 2025, [https://brainhub.eu/library/monolith-to-microservices-using-strangler-pattern](https://brainhub.eu/library/monolith-to-microservices-using-strangler-pattern)  
7. Strangler Fig Pattern \- Azure Architecture Center | Microsoft Learn, diakses Juli 21, 2025, [https://learn.microsoft.com/en-us/azure/architecture/patterns/strangler-fig](https://learn.microsoft.com/en-us/azure/architecture/patterns/strangler-fig)  
8. How to Use the Strangler Fig Pattern in Serverless Stack | by Julien Bras \- Medium, diakses Juli 21, 2025, [https://medium.com/better-programming/how-to-use-the-strangler-fig-pattern-in-serverless-stack-eb6acff24c92](https://medium.com/better-programming/how-to-use-the-strangler-fig-pattern-in-serverless-stack-eb6acff24c92)  
9. Microservices vs. monolithic architecture \- Atlassian, diakses Juli 21, 2025, [https://www.atlassian.com/microservices/microservices-architecture/microservices-vs-monolith](https://www.atlassian.com/microservices/microservices-architecture/microservices-vs-monolith)  
10. implementing the Strangler Fig Pattern with Node.js | by Amr Mohamed | Medium, diakses Juli 29, 2025, [https://medium.com/@amrmuhmedd/implementing-the-strangler-fig-pattern-with-node-js-a280f07e65d5](https://medium.com/@amrmuhmedd/implementing-the-strangler-fig-pattern-with-node-js-a280f07e65d5)  
11. Choosing the best rendering strategy for your Next.js app \- Bejamas, diakses Juli 29, 2025, [https://bejamas.com/hub/guides/choosing-the-best-rendering-strategy-for-your-next-js-app](https://bejamas.com/hub/guides/choosing-the-best-rendering-strategy-for-your-next-js-app)  
12. Rendering Strategies \- SEO \- Next.js, diakses Juli 29, 2025, [https://nextjs.org/learn/seo/rendering-strategies](https://nextjs.org/learn/seo/rendering-strategies)  
13. Building a REST API with Next.js: A Comprehensive Guide \- Apidog, diakses Juli 29, 2025, [https://apidog.com/blog/next-js-build-rest-api/](https://apidog.com/blog/next-js-build-rest-api/)  
14. CSR,SSR AND SSG IN Next JS \- Medium, diakses Juli 29, 2025, [https://medium.com/@nui\_x/csr-ssr-and-ssg-in-next-js-aa513ed45f80](https://medium.com/@nui_x/csr-ssr-and-ssg-in-next-js-aa513ed45f80)  
15. Client-side Rendering (CSR) \- Next.js, diakses Juli 29, 2025, [https://nextjs.org/docs/pages/building-your-application/rendering/client-side-rendering](https://nextjs.org/docs/pages/building-your-application/rendering/client-side-rendering)  
16. How to Create and Consume a REST API in Next.js \- DEV Community, diakses Juli 29, 2025, [https://dev.to/dhrumitdk/how-to-create-and-consume-a-rest-api-in-nextjs-2c6a](https://dev.to/dhrumitdk/how-to-create-and-consume-a-rest-api-in-nextjs-2c6a)  
17. The NestJS Handbook – Learn to Use Nest with Code Examples \- freeCodeCamp, diakses Juli 29, 2025, [https://www.freecodecamp.org/news/the-nestjs-handbook-learn-to-use-nest-with-code-examples/](https://www.freecodecamp.org/news/the-nestjs-handbook-learn-to-use-nest-with-code-examples/)  
18. Modules | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/modules](https://docs.nestjs.com/modules)  
19. Understanding the importance of shared\_buffers, work\_mem, and wal\_buffers in PostgreSQL \- Fujitsu Enterprise Postgres, diakses Juli 21, 2025, [https://www.postgresql.fastware.com/pzone/2024-06-understanding-shared-buffers-work-mem-and-wal-buffers-in-postgresql](https://www.postgresql.fastware.com/pzone/2024-06-understanding-shared-buffers-work-mem-and-wal-buffers-in-postgresql)  
20. Worry-Free Managed PostgreSQL Hosting \- DigitalOcean, diakses Juli 21, 2025, [https://www.digitalocean.com/products/managed-databases-postgresql](https://www.digitalocean.com/products/managed-databases-postgresql)  
21. Managed Databases | DigitalOcean Documentation, diakses Juli 21, 2025, [https://docs.digitalocean.com/products/databases/](https://docs.digitalocean.com/products/databases/)  
22. Multitenancy with Postgres schemas: key concepts explained \- | Arkency Blog, diakses Juli 29, 2025, [https://blog.arkency.com/multitenancy-with-postgres-schemas-key-concepts-explained/](https://blog.arkency.com/multitenancy-with-postgres-schemas-key-concepts-explained/)  
23. Multitenant Database Designs Strategies — with PostgreSQL | by Satish Mishra \- Medium, diakses Juli 29, 2025, [https://techtonics.medium.com/multitenant-database-designs-strategies-with-postgresql-55a9e3ec882c](https://techtonics.medium.com/multitenant-database-designs-strategies-with-postgresql-55a9e3ec882c)  
24. Multi-Tenant Databases with Postgres Row-Level Security \- Midnyte City, diakses Juli 29, 2025, [https://www.midnytecity.com.au/blogs/multi-tenant-databases-with-postgres-row-level-security](https://www.midnytecity.com.au/blogs/multi-tenant-databases-with-postgres-row-level-security)  
25. Multi-Tenant Database Architecture Patterns Explained \- Bytebase, diakses Juli 29, 2025, [https://www.bytebase.com/blog/multi-tenant-database-architecture-patterns-explained/](https://www.bytebase.com/blog/multi-tenant-database-architecture-patterns-explained/)  
26. Using BullMQ with NestJS for Background Job Processing \- Mahabubur Rahman \- Medium, diakses Juli 29, 2025, [https://mahabub-r.medium.com/using-bullmq-with-nestjs-for-background-job-processing-320ab938048a](https://mahabub-r.medium.com/using-bullmq-with-nestjs-for-background-job-processing-320ab938048a)  
27. BullMQ \- Background Jobs processing and message queue for NodeJS | BullMQ, diakses Juli 29, 2025, [https://bullmq.io/](https://bullmq.io/)  
28. Nest.js starter kit with Postgres, Redis, Docker, TypeORM, JWT auth, and more\! \- GitHub, diakses Juli 29, 2025, [https://github.com/rodion-arr/nestjs-starter-kit](https://github.com/rodion-arr/nestjs-starter-kit)  
29. Nginx Reverse Proxy NestJS/NodeJS \- FlyEnv, diakses Juli 29, 2025, [https://www.macphpstudy.com/guide/nginx-reverse-proxy-nestjs.html](https://www.macphpstudy.com/guide/nginx-reverse-proxy-nestjs.html)  
30. Can I host my Next.js app on nginx? : r/nextjs \- Reddit, diakses Juli 29, 2025, [https://www.reddit.com/r/nextjs/comments/1iucn51/can\_i\_host\_my\_nextjs\_app\_on\_nginx/](https://www.reddit.com/r/nextjs/comments/1iucn51/can_i_host_my_nextjs_app_on_nginx/)  
31. How to Host Multiple Node Apps with nginx and pm2 \- codebubb, diakses Juli 29, 2025, [https://codebubb.com/posts/how-to-host-multiple-node-apps/](https://codebubb.com/posts/how-to-host-multiple-node-apps/)  
32. Multiple node apps with NGINX in sub-directories \- Stack Overflow, diakses Juli 29, 2025, [https://stackoverflow.com/questions/39822519/multiple-node-apps-with-nginx-in-sub-directories](https://stackoverflow.com/questions/39822519/multiple-node-apps-with-nginx-in-sub-directories)  
33. Deploying Web Apps with Caddy: A Beginner's Guide | Better Stack Community, diakses Juli 29, 2025, [https://betterstack.com/community/guides/web-servers/caddy/](https://betterstack.com/community/guides/web-servers/caddy/)  
34. Why Choose Caddy Server instead Nginx? | by Salih İbrahimbaş \- Medium, diakses Juli 21, 2025, [https://medium.com/@9ssi7/why-choose-caddy-server-over-nginx-e49b01c631a1](https://medium.com/@9ssi7/why-choose-caddy-server-over-nginx-e49b01c631a1)  
35. Automatic HTTPS \- Caddy, diakses Juli 21, 2025, [https://caddy.its-em.ma/v1/docs/automatic-https](https://caddy.its-em.ma/v1/docs/automatic-https)  
36. Prisma Cloud Multitenant Management Overview, diakses Juli 29, 2025, [https://docs.prismacloud.io/en/enterprise-edition/content-collections/multitenant-management/multitenant-management-introduction](https://docs.prismacloud.io/en/enterprise-edition/content-collections/multitenant-management/multitenant-management-introduction)  
37. Caddy Reverse Proxy NestJS/NodeJS \- FlyEnv, diakses Juli 29, 2025, [https://www.macphpstudy.com/guide/caddy-reverse-proxy-nestjs.html](https://www.macphpstudy.com/guide/caddy-reverse-proxy-nestjs.html)  
38. Reverse proxy quick-start — Caddy Documentation, diakses Juli 29, 2025, [https://caddyserver.com/docs/quick-starts/reverse-proxy](https://caddyserver.com/docs/quick-starts/reverse-proxy)  
39. Nest.js and Modular Architecture: Principles and Best Practices | by ..., diakses Juli 29, 2025, [https://levelup.gitconnected.com/nest-js-and-modular-architecture-principles-and-best-practices-806c2cb008d5](https://levelup.gitconnected.com/nest-js-and-modular-architecture-principles-and-best-practices-806c2cb008d5)  
40. Nest JS Class Validator & Class Transformers \- DEV Community, diakses Juli 29, 2025, [https://dev.to/jay818/nest-js-class-validator-class-transformers-3ppg](https://dev.to/jay818/nest-js-class-validator-class-transformers-3ppg)  
41. Validation | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/techniques/validation](https://docs.nestjs.com/techniques/validation)  
42. NestJS DTO Validation Cheat Sheet with class-validator | by Mohamad Alaskari | Medium, diakses Juli 29, 2025, [https://medium.com/@mohamad-alaskari/nestjs-dto-validation-cheat-sheet-with-class-validator-64d5092a9858](https://medium.com/@mohamad-alaskari/nestjs-dto-validation-cheat-sheet-with-class-validator-64d5092a9858)  
43. Dynamic Form Generation & Validation in Node.js \[With Code\] \- Seven Square, diakses Juli 29, 2025, [https://www.sevensquaretech.com/dynamic-form-generation-validation-nodejs/](https://www.sevensquaretech.com/dynamic-form-generation-validation-nodejs/)  
44. Dynamic modules | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/fundamentals/dynamic-modules](https://docs.nestjs.com/fundamentals/dynamic-modules)  
45. Controllers | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/controllers](https://docs.nestjs.com/controllers)  
46. Aggregations | Nestjs-query \- Blog, diakses Juli 29, 2025, [https://doug-martin.github.io/nestjs-query/docs/graphql/aggregations/](https://doug-martin.github.io/nestjs-query/docs/graphql/aggregations/)  
47. concepta/nestjs-report \- NPM, diakses Juli 29, 2025, [https://www.npmjs.com/package/@concepta/nestjs-report](https://www.npmjs.com/package/@concepta/nestjs-report)  
48. Upload files to S3 compatible storage using NestJS | by Bornak Paul \- Medium, diakses Juli 29, 2025, [https://medium.com/@bornakpaul/upload-files-to-s3-compatible-storage-using-nestjs-c57abee08c1a](https://medium.com/@bornakpaul/upload-files-to-s3-compatible-storage-using-nestjs-c57abee08c1a)  
49. hodfords/nestjs-storage \- NPM, diakses Juli 29, 2025, [https://www.npmjs.com/package/%40hodfords%2Fnestjs-storage](https://www.npmjs.com/package/%40hodfords%2Fnestjs-storage)  
50. OpenNebel/NestJS-S3Client: A NestJS module for interacting with AWS S3. This module simplifies the integration of AWS S3 within a NestJS application by providing injectable services and configuration options. \- GitHub, diakses Juli 29, 2025, [https://github.com/OpenNebel/NestJS-S3Client](https://github.com/OpenNebel/NestJS-S3Client)  
51. PostgreSQL Data Security Best Practices: Encryption and Monitoring \- EDB, diakses Juli 29, 2025, [https://www.enterprisedb.com/postgresql-best-practices-encryption-monitoring](https://www.enterprisedb.com/postgresql-best-practices-encryption-monitoring)  
52. What are some best practices for encrypting data at rest / in use / in transit with PostgreSQL, diakses Juli 29, 2025, [https://www.reddit.com/r/PostgreSQL/comments/eqcwiq/what\_are\_some\_best\_practices\_for\_encrypting\_data/](https://www.reddit.com/r/PostgreSQL/comments/eqcwiq/what_are_some_best_practices_for_encrypting_data/)  
53. File upload | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/techniques/file-upload](https://docs.nestjs.com/techniques/file-upload)  
54. Upload File with NestJS | Bits and Pieces, diakses Juli 29, 2025, [https://blog.bitsrc.io/upload-file-with-nestjs-d7dd74701b38](https://blog.bitsrc.io/upload-file-with-nestjs-d7dd74701b38)  
55. Best Security implementation Practices In NestJS. A Comprehensive Guide, diakses Juli 29, 2025, [https://dev.to/drbenzene/best-security-implementation-practices-in-nestjs-a-comprehensive-guide-2p88](https://dev.to/drbenzene/best-security-implementation-practices-in-nestjs-a-comprehensive-guide-2p88)  
56. Nest JS Queues | NestJS Bull | Redis \- YouTube, diakses Juli 29, 2025, [https://www.youtube.com/watch?v=iOMgLP8rUXA](https://www.youtube.com/watch?v=iOMgLP8rUXA)  
57. sam-the-hai/nestjs-bullmq-example: Nest.js bullmq and schedule to run distributed bash job, diakses Juli 29, 2025, [https://github.com/sam-the-hai/nestjs-bullmq-example](https://github.com/sam-the-hai/nestjs-bullmq-example)  
58. Guards | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/guards](https://docs.nestjs.com/guards)  
59. Implementing Role-Based Access Control with Custom Decorators in NestJS \- MoldStud, diakses Juli 29, 2025, [https://moldstud.com/articles/p-implementing-role-based-access-control-with-custom-decorators-in-nestjs](https://moldstud.com/articles/p-implementing-role-based-access-control-with-custom-decorators-in-nestjs)  
60. Exploring Complex Role-Based Access Control in NestJS with RBAC and CASL Prisma, diakses Juli 29, 2025, [https://blog.devgenius.io/exploring-complex-role-based-access-control-in-nestjs-with-rbac-casl-and-prisma-9471d5b36636](https://blog.devgenius.io/exploring-complex-role-based-access-control-in-nestjs-with-rbac-casl-and-prisma-9471d5b36636)  
61. Designing RBAC Permission System with Nest.js: A Step-by-Step Guide \- DEV Community, diakses Juli 29, 2025, [https://dev.to/leapcell/designing-rbac-permission-system-with-nestjs-a-step-by-step-guide-3bhl](https://dev.to/leapcell/designing-rbac-permission-system-with-nestjs-a-step-by-step-guide-3bhl)  
62. rust-rbac \- crates.io: Rust Package Registry, diakses Juli 21, 2025, [https://crates.io/crates/rust-rbac](https://crates.io/crates/rust-rbac)  
63. rust\_rbac \- Rust \- Docs.rs, diakses Juli 21, 2025, [https://docs.rs/rust-rbac](https://docs.rs/rust-rbac)  
64. Implement Role-Based Access Control in Next.js 15 \- Clerk, diakses Juli 29, 2025, [https://clerk.com/blog/nextjs-role-based-access-control](https://clerk.com/blog/nextjs-role-based-access-control)  
65. Implementing RBAC Authorization in Next.js \- 2024 Updated Guide \- Permit.io, diakses Juli 29, 2025, [https://www.permit.io/blog/how-to-add-rbac-in-nextjs](https://www.permit.io/blog/how-to-add-rbac-in-nextjs)  
66. Mastering JWT Authentication with Refresh Tokens in NestJS & React (Google & Email Auth) | by Njihia Mark | Medium, diakses Juli 29, 2025, [https://medium.com/@njihiamark/mastering-jwt-authentication-with-refresh-tokens-in-nestjs-react-google-email-auth-e4f1e8c8c21e](https://medium.com/@njihiamark/mastering-jwt-authentication-with-refresh-tokens-in-nestjs-react-google-email-auth-e4f1e8c8c21e)  
67. Best Way to Handle Authentication in Next.js with a NestJS Backend? : r/nextjs \- Reddit, diakses Juli 29, 2025, [https://www.reddit.com/r/nextjs/comments/1ip42qk/best\_way\_to\_handle\_authentication\_in\_nextjs\_with/](https://www.reddit.com/r/nextjs/comments/1ip42qk/best_way_to_handle_authentication_in_nextjs_with/)  
68. Step-by-Step Guide to Secure JWT Authentication with Refresh Tokens in Express.js, TypeScript, and Prisma. : r/node \- Reddit, diakses Juli 29, 2025, [https://www.reddit.com/r/node/comments/1jeugpc/stepbystep\_guide\_to\_secure\_jwt\_authentication/](https://www.reddit.com/r/node/comments/1jeugpc/stepbystep_guide_to_secure_jwt_authentication/)  
69. Rust Security Best Practices 2025 \- Corgea \- Home, diakses Juli 21, 2025, [https://corgea.com/Learn/rust-security-best-practices-2025](https://corgea.com/Learn/rust-security-best-practices-2025)  
70. Comprehensive Guide to Rust for Security and Privacy Researchers \- GitHub, diakses Juli 21, 2025, [https://github.com/iAnonymous3000/awesome-rust-security-guide](https://github.com/iAnonymous3000/awesome-rust-security-guide)  
71. Nodejs Security \- OWASP Cheat Sheet Series, diakses Juli 29, 2025, [https://cheatsheetseries.owasp.org/cheatsheets/Nodejs\_Security\_Cheat\_Sheet.html](https://cheatsheetseries.owasp.org/cheatsheets/Nodejs_Security_Cheat_Sheet.html)  
72. Secure authentication in Next.js: Best practices & implementation \- Ory, diakses Juli 29, 2025, [https://www.ory.sh/blog/add-auth-to-nextjs-security-best-practices](https://www.ory.sh/blog/add-auth-to-nextjs-security-best-practices)  
73. Deployment | NestJS \- A progressive Node.js framework, diakses Juli 29, 2025, [https://docs.nestjs.com/deployment](https://docs.nestjs.com/deployment)  
74. Guides: Data Security \- Next.js, diakses Juli 29, 2025, [https://nextjs.org/docs/app/guides/data-security](https://nextjs.org/docs/app/guides/data-security)  
75. What is System Hardening? Essential Checklists from OS to Applications \- Ubuntu, diakses Juli 29, 2025, [https://ubuntu.com/blog/what-is-system-hardening-definition-and-best-practices](https://ubuntu.com/blog/what-is-system-hardening-definition-and-best-practices)  
76. 5 Security Best Practices for Next.js Applications | by Lior Amsalem | Medium, diakses Juli 29, 2025, [https://medium.com/@lior\_amsalem/5-security-best-practices-for-next-js-applications-80402df473a2](https://medium.com/@lior_amsalem/5-security-best-practices-for-next-js-applications-80402df473a2)  
77. Building Scalable SaaS: Multi-Tenant Architecture with PostgreSQL & TypeORM (Design & Implementation) \- Pranit Codes, diakses Juli 29, 2025, [https://blogs.pranitpatil.com/building-scalable-saas-multi-tenant-architecture-with-postgresql-and-typeorm-design-and-implementation](https://blogs.pranitpatil.com/building-scalable-saas-multi-tenant-architecture-with-postgresql-and-typeorm-design-and-implementation)  
78. NestJS/TypeORM Multi-tenancy (Schema-based) \- DataSource Inject & Repository Init Issue in Specific Service \- Stack Overflow, diakses Juli 29, 2025, [https://stackoverflow.com/questions/79675286/nestjs-typeorm-multi-tenancy-schema-based-datasource-inject-repository-ini](https://stackoverflow.com/questions/79675286/nestjs-typeorm-multi-tenancy-schema-based-datasource-inject-repository-ini)  
79. Use TypeORM as a multi-tenant provider for your application \- GitHub, diakses Juli 29, 2025, [https://github.com/teziapp/typeorm-multi-tenant](https://github.com/teziapp/typeorm-multi-tenant)  
80. Tuning PostgreSQL performance \[most important settings\] \- Bun, diakses Juli 21, 2025, [https://bun.uptrace.dev/postgres/performance-tuning.html](https://bun.uptrace.dev/postgres/performance-tuning.html)  
81. PostgreSQL Performance Tuning: Optimize Your Database Server \- EDB, diakses Juli 21, 2025, [https://www.enterprisedb.com/postgres-tutorials/introduction-postgresql-performance-tuning-and-optimization](https://www.enterprisedb.com/postgres-tutorials/introduction-postgresql-performance-tuning-and-optimization)  
82. Tuning Postgresql on a 16GB Linode VPS \- ubuntu \- Server Fault, diakses Juli 21, 2025, [https://serverfault.com/questions/574452/tuning-postgresql-on-a-16gb-linode-vps](https://serverfault.com/questions/574452/tuning-postgresql-on-a-16gb-linode-vps)  
83. Functional Domain Modeling in Rust \- Part 1 \- Xebia, diakses Juli 21, 2025, [https://xebia.com/blog/functional-domain-modeling-in-rust-part-1/](https://xebia.com/blog/functional-domain-modeling-in-rust-part-1/)  
84. Deploying NestJS Applications: Best Practices | by @rnab \- Medium, diakses Juli 29, 2025, [https://arnab-k.medium.com/deploying-nestjs-applications-best-practices-9479e1a0e6ca](https://arnab-k.medium.com/deploying-nestjs-applications-best-practices-9479e1a0e6ca)  
85. kristiyan-velkov/nextjs-prod-dockerfile: This repository offers a guide and examples to optimize Docker for deploying Next.js applications in production. It covers best practices for creating efficient, secure, and lightweight Docker images with multi-stage builds, standalone mode, and selective dependency management to enable faster builds and \- GitHub, diakses Juli 29, 2025, [https://github.com/kristiyan-velkov/nextjs-prod-dockerfile](https://github.com/kristiyan-velkov/nextjs-prod-dockerfile)  
86. Building best practices \- Docker Docs, diakses Juli 29, 2025, [https://docs.docker.com/build/building/best-practices/](https://docs.docker.com/build/building/best-practices/)  
87. Caddy vs Nginx: How Do These Web Servers / Reverse Proxies Compare? \- Reddit, diakses Juli 21, 2025, [https://www.reddit.com/r/selfhosted/comments/hur1hx/caddy\_vs\_nginx\_how\_do\_these\_web\_servers\_reverse/](https://www.reddit.com/r/selfhosted/comments/hur1hx/caddy_vs_nginx_how_do_these_web_servers_reverse/)  
88. Self-Host Next.js with Kamal and GitHub Actions \- GetDeploying, diakses Juli 21, 2025, [https://getdeploying.com/guides/deploy-nextjs](https://getdeploying.com/guides/deploy-nextjs)  
89. Gitlab CI how to deploy an application via SSH \- Stack Overflow, diakses Juli 21, 2025, [https://stackoverflow.com/questions/42676369/gitlab-ci-how-to-deploy-an-application-via-ssh](https://stackoverflow.com/questions/42676369/gitlab-ci-how-to-deploy-an-application-via-ssh)  
90. GitLab CI/CD examples, diakses Juli 21, 2025, [https://docs.gitlab.com/ci/examples/](https://docs.gitlab.com/ci/examples/)  
91. Deploy Next.js application to AWS EC2 using GitHub Actions \- YouTube, diakses Juli 21, 2025, [https://www.youtube.com/watch?v=sjTdluu1yDU](https://www.youtube.com/watch?v=sjTdluu1yDU)  
92. Setting Up CI/CD for NestJS Applications Using GitHub Actions | by Zulfikar Ditya \- Medium, diakses Juli 29, 2025, [https://medium.com/@zulfikarditya/setting-up-ci-cd-for-nestjs-applications-using-github-actions-e7b33c09dfef](https://medium.com/@zulfikarditya/setting-up-ci-cd-for-nestjs-applications-using-github-actions-e7b33c09dfef)  
93. Deploy Next JS App to Google Cloud Run with Github Actions CI/CD \- A Complete Guide, diakses Juli 29, 2025, [https://dev.to/rushi-patel/deploy-next-js-app-to-google-cloud-run-with-github-actions-cicd-a-complete-guide-l29](https://dev.to/rushi-patel/deploy-next-js-app-to-google-cloud-run-with-github-actions-cicd-a-complete-guide-l29)  
94. Deploying to a Server with GitHub Actions using ssh | by Balazs Kocsis \- Medium, diakses Juli 29, 2025, [https://medium.com/@balazskocsis/deploying-to-a-server-with-github-actions-a-deep-dive-e8558e83a4d7](https://medium.com/@balazskocsis/deploying-to-a-server-with-github-actions-a-deep-dive-e8558e83a4d7)  
95. Docker Compose Deployment (SSH) · Actions · GitHub Marketplace, diakses Juli 29, 2025, [https://github.com/marketplace/actions/docker-compose-deployment-ssh](https://github.com/marketplace/actions/docker-compose-deployment-ssh)  
96. SaaS MVP Development: Unleashing Success in Complex Projects \- Apiko, diakses Juli 21, 2025, [https://apiko.com/blog/saas-mvp-development/](https://apiko.com/blog/saas-mvp-development/)  
97. What is MVP in Agile Development and How to Build a Winning Product \- MindK.com, diakses Juli 21, 2025, [https://www.mindk.com/blog/what-is-mvp-agile/](https://www.mindk.com/blog/what-is-mvp-agile/)  
98. SaaS MVP Development: How to Build and Launch Your Product \- Space-O Technologies, diakses Juli 21, 2025, [https://www.spaceotechnologies.com/blog/saas-mvp-development/](https://www.spaceotechnologies.com/blog/saas-mvp-development/)  
99. How to Build, Improve and Pivot a Minimum Viable SaaS Product \- Cobloom, diakses Juli 21, 2025, [https://www.cobloom.com/blog/minimum-viable-product](https://www.cobloom.com/blog/minimum-viable-product)  
100. 7 Common MVP Development Mistakes to Avoid \- Mike Khorev, diakses Juli 21, 2025, [https://mikekhorev.com/7-common-mvp-development-mistakes-to-avoid](https://mikekhorev.com/7-common-mvp-development-mistakes-to-avoid)  
101. Building an MVP? Avoid These 15 Common Mistakes \- LowCode Agency, diakses Juli 21, 2025, [https://www.lowcode.agency/blog/mvp-development-challenges-mistakes](https://www.lowcode.agency/blog/mvp-development-challenges-mistakes)