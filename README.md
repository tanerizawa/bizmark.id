# Platform SaaS Perizinan UMKM - Bizmark.id

## Deskripsi Proyek
Platform SaaS yang dirancang khusus untuk membantu UMKM dalam mengelola perizinan dan administrasi bisnis. Menggunakan arsitektur modular-monolith yang optimal untuk VPS dengan sumber daya terbatas.

## Stack Teknologi
- **Frontend**: Next.js 14+ dengan TypeScript
- **Backend**: NestJS dengan TypeScript  
- **Database**: PostgreSQL dengan Row-Level Security
- **Cache & Queue**: Redis dengan BullMQ
- **Storage**: MinIO (S3-compatible)
- **Reverse Proxy**: Caddy
- **Containerization**: Docker & Docker Compose

## Fitur Utama
- ✅ Pengelolaan pengajuan izin dengan jenis izin dinamis
- ✅ Sistem multi-tenancy dengan isolasi data yang kuat
- ✅ Manajemen dokumen dengan enkripsi
- ✅ Laporan keuangan dan pajak otomatis
- ✅ Notifikasi real-time dan background jobs
- ✅ Role-Based Access Control (RBAC)
- ✅ Audit trail komprehensif
- ✅ API rate limiting dan keamanan berlapis

## Dokumentasi Lengkap

### 📖 Panduan Utama
- **[Development Guide](DEVELOPMENT_GUIDE.md)** - Panduan lengkap untuk development
- **[API Documentation](API_DOCUMENTATION.md)** - Dokumentasi lengkap API endpoints
- **[Database Schema](DATABASE_SCHEMA.md)** - Desain database dan optimasi
- **[Deployment Guide](DEPLOYMENT_GUIDE.md)** - Panduan deployment ke production
- **[Security Guide](SECURITY_GUIDE.md)** - Panduan keamanan komprehensif

### 🔧 Quick Start

#### Prerequisites
- Node.js 18+
- Docker & Docker Compose
- Git

#### Setup Development Environment
```bash
# Clone repository
git clone https://github.com/tanerizawa/bizmark.id.git
cd bizmark.id

# Setup environment
cp .env.example .env
# Edit .env dengan konfigurasi yang sesuai

# Start development services
docker-compose -f docker-compose.dev.yml up -d

# Install dependencies & start apps
npm run dev:setup
npm run dev:start
```

#### Akses Aplikasi
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:3001
- **Database Admin**: http://localhost:8080 (Adminer)
- **MinIO Console**: http://localhost:9001

## Arsitektur Sistem

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Next.js)     │◄──►│   (NestJS)      │◄──►│   (PostgreSQL)  │
│   Port: 3000    │    │   Port: 3001    │    │   Port: 5432    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         ▲                       ▲                       ▲
         │                       │                       │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  Reverse Proxy  │    │     Redis       │    │     MinIO       │
│    (Caddy)      │    │  Cache & Queue  │    │   File Storage  │
│   Port: 80/443  │    │   Port: 6379    │    │   Port: 9000    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Kontribusi

### Workflow Development
1. Fork repository ini
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit perubahan (`git commit -m 'Add amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

### Coding Standards
- Menggunakan ESLint dan Prettier untuk formatting
- Mengikuti konvensi penamaan TypeScript
- Menambahkan unit tests untuk fitur baru
- Dokumentasi API menggunakan Swagger/OpenAPI

## Lisensi
Proyek ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## Tim Pengembang
- **Lead Developer**: [Tanerizawa](https://github.com/tanerizawa)

## Support
Jika Anda memiliki pertanyaan atau menemukan bug, silakan:
1. Buat issue di GitHub repository
2. Hubungi tim development melalui email
3. Bergabung dengan diskusi di komunitas

---

**Dibuat dengan ❤️ untuk UMKM Indonesia** 🇮🇩
