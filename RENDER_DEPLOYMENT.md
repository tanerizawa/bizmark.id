# Deployment ke Render.com - Panduan Lengkap

## Persiapan Sebelum Deploy

### 1. Prerequisites
- [x] Akun di [Render.com](https://render.com)
- [x] Repository GitHub dengan kode Bizmark.id
- [x] Konfigurasi keamanan sudah complete (OpenSSL secrets)
- [x] Backend tested dan berjalan dengan baik di local

### 2. Test Build Lokal
```bash
# Test build process sebelum deploy
./test-build.sh
```

### 3. Generate Production Environment Variables
```bash
# Generate secure environment variables untuk production
./setup-render-env.sh
```

## Langkah Deployment

### Step 1: Setup Database PostgreSQL

1. Login ke [Render.com Dashboard](https://dashboard.render.com)
2. Klik "New" → "PostgreSQL"
3. Konfigurasi database:
   - **Name**: `bizmark-db`
   - **Database**: `bizmark_prod`
   - **User**: `bizmark_user` (atau biarkan default)
   - **Region**: `Singapore` (terdekat dengan Indonesia)
   - **PostgreSQL Version**: `15` (latest stable)
   - **Plan**: `Starter` (Free) atau `Standard` untuk production
4. Klik "Create Database"
5. **PENTING**: Catat **External Database URL** yang muncul
   - Format: `postgres://username:password@hostname:port/database`

### Step 2: Setup Redis Service

1. Klik "New" → "Redis"
2. Konfigurasi Redis:
   - **Name**: `bizmark-redis`
   - **Region**: `Singapore`
   - **Plan**: `Starter` (Free) atau sesuai kebutuhan
3. Klik "Create Redis"
4. **PENTING**: Catat **Redis URL** yang muncul
   - Format: `redis://username:password@hostname:port`

### Step 3: Deploy Backend API

1. Klik "New" → "Web Service"
2. Connect GitHub repository
3. Konfigurasi service:
   - **Name**: `bizmark-api`
   - **Region**: `Singapore`
   - **Branch**: `main`
   - **Root Directory**: `.` (root of repository)
   - **Build Command**: `cd backend && npm install && npm run build`
   - **Start Command**: `cd backend && npm run start:prod`
   - **Plan**: `Starter` (Free) atau `Standard` untuk production

### Step 4: Configure Environment Variables

Di halaman service configuration, tambahkan environment variables berikut:

#### 🔐 Dari Output setup-render-env.sh:
```bash
NODE_ENV=production
API_PREFIX=api
API_VERSION=v1
LOG_LEVEL=info
JWT_SECRET=[dari setup-render-env.sh]
JWT_REFRESH_SECRET=[dari setup-render-env.sh]
SESSION_SECRET=[dari setup-render-env.sh]
BCRYPT_ROUNDS=14
MAX_LOGIN_ATTEMPTS=3
LOCKOUT_TIME=1800000
RATE_LIMIT_WINDOW_MS=900000
RATE_LIMIT_MAX_REQUESTS=50
MAX_FILE_SIZE=10485760
ALLOWED_FILE_TYPES=pdf,doc,docx,jpg,jpeg,png
DB_POOL_MIN=5
DB_POOL_MAX=20
HEALTH_CHECK_TIMEOUT=5000
METRICS_ENABLED=true
```

#### 🗄️ Database & Cache URLs:
```bash
DATABASE_URL=[External Database URL dari Step 1]
REDIS_URL=[Redis URL dari Step 2]
```

#### 🌐 Application URLs (Update setelah deploy):
```bash
APP_URL=https://bizmark-frontend.onrender.com
API_URL=https://[your-service-name].onrender.com
CORS_ORIGINS=https://bizmark-frontend.onrender.com
```

### Step 5: Deploy & Verify

1. Klik "Create Web Service"
2. Tunggu proses build dan deployment (5-10 menit)
3. Setelah deployment selesai:
   - Service status harus "Live" 
   - Akses URL yang diberikan
   - Test health endpoint: `https://[your-service].onrender.com/api/v1/health/live`

### Step 6: Run Database Migrations

1. Buka service dashboard → "Shell" tab
2. Jalankan perintah:
   ```bash
   cd backend && npm run migration:run
   ```
3. Jika perlu seeding data:
   ```bash
   cd backend && npm run seed
   ```

## Post-Deployment Setup

### 1. Update URLs
Setelah deployment berhasil, update environment variables:
- `API_URL`: URL service yang diberikan Render
- `APP_URL`: URL frontend (jika ada)
- `CORS_ORIGINS`: URL frontend

### 2. Verify API Documentation
Akses: `https://[your-service].onrender.com/api/docs`

### 3. Test API Endpoints
```bash
# Health check
curl https://[your-service].onrender.com/api/v1/health/live

# Full health check
curl https://[your-service].onrender.com/api/v1/health
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
