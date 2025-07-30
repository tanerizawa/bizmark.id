# Deployment ke Render.com

## Persiapan

1. Pastikan memiliki akun di [Render.com](https://render.com)
2. Hubungkan akun GitHub dengan Render.com

## Langkah Deployment

### 1. Siapkan Database PostgreSQL

1. Login ke dashboard Render.com
2. Pergi ke menu "New" dan pilih "PostgreSQL"
3. Isi informasi database:
   - Name: `bizmark-db`
   - Database: `bizmark_prod`
   - User: `bizmark_user`
   - Region: `Singapore` (atau region terdekat dengan pengguna)
   - PostgreSQL Version: `15` (versi terbaru)
4. Klik "Create Database"
5. Catat informasi koneksi database yang muncul (terutama "External Database URL")

### 2. Siapkan Redis Service (Opsional)

1. Pergi ke menu "New" dan pilih "Redis"
2. Isi informasi:
   - Name: `bizmark-redis`
   - Region: `Singapore` (atau region terdekat dengan pengguna)
3. Klik "Create Redis"
4. Catat informasi koneksi Redis (Redis URL)

### 3. Deploy Backend API

#### Opsi 1: Menggunakan Blueprint dari render.yaml

1. Pergi ke menu "Blueprints"
2. Klik "New Blueprint"
3. Pilih repository GitHub yang berisi kode Bizmark.id
4. Render akan mendeteksi `render.yaml` dan menawarkan untuk deploy semua service
5. Klik "Apply Blueprint"

#### Opsi 2: Deploy Manual

1. Pergi ke menu "New" dan pilih "Web Service"
2. Pilih repositori GitHub yang berisi kode Bizmark.id
3. Isi konfigurasi:
   - Name: `bizmark-api`
   - Region: `Singapore`
   - Branch: `main` (atau branch yang ingin di-deploy)
   - Root Directory: `.` (root repo)
   - Build Command: `cd backend && npm install && npm run build`
   - Start Command: `cd backend && npm run start:prod`
   - Health Check Path: `/api/v1/health`
4. Pilih plan yang sesuai (mulai dari Free)
5. Konfigurasi Environment Variables:
   - DATABASE_URL: [Database URL dari langkah 1]
   - NODE_ENV: `production`
   - JWT_SECRET: [Buat secret key baru, jangan gunakan yang ada di dev]
   - JWT_REFRESH_SECRET: [Buat secret key baru]
   - REDIS_URL: [Redis URL dari langkah 2, jika ada]
   - Tambahkan environment variable lain sesuai kebutuhan dari production.env
6. Klik "Create Web Service"

### 4. Menjalankan Database Migration (Setelah Deploy)

1. Pergi ke halaman detail service `bizmark-api`
2. Klik tab "Shell"
3. Jalankan perintah:
   ```bash
   cd backend && npm run migration:run
   ```
4. Jika perlu menjalankan seed:
   ```bash
   cd backend && npm run seed
   ```

## Konfigurasi Tambahan

### Mengaktifkan Automatic Deployments

1. Pergi ke halaman detail service `bizmark-api`
2. Klik tab "Settings"
3. Di bagian "Auto-Deploy", pilih "Yes"

### Mengatur Custom Domain (Opsional)

1. Pergi ke halaman detail service `bizmark-api`
2. Klik tab "Settings"
3. Scroll ke "Custom Domain"
4. Klik "Add Custom Domain"
5. Ikuti instruksi untuk mengonfigurasi DNS

### Monitoring dan Logs

1. Monitoring dan alerting tersedia di tab "Metrics"
2. Logs dapat dilihat di tab "Logs"

## Troubleshooting

### Service Gagal Start

1. Periksa logs untuk melihat error
2. Pastikan semua environment variables sudah dikonfigurasi dengan benar
3. Jika terkait database, pastikan migrations sudah dijalankan

### Connection Timeout

1. Periksa apakah service database sudah running
2. Periksa apakah URL koneksi database sudah benar
3. Pastikan database user memiliki akses yang diperlukan
