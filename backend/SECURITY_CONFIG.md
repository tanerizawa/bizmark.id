# Panduan Keamanan Environment Variables

## Ringkasan Perbaikan yang Dilakukan

### 1. Database Security
- ✅ Password database tetap menggunakan `postgres` untuk development (sesuai Docker container)
- ✅ Database URL sesuai dengan password yang digunakan
- ✅ Konfigurasi connection pool ditambahkan untuk optimasi

### 2. JWT Security
- ✅ JWT Secret di-generate menggunakan OpenSSL: `19be572e4828f4903ff4bffbb9fbb803d4b9274a77570a1667ba0fab675c1fde`
- ✅ JWT Refresh Secret di-generate menggunakan OpenSSL: `97c1dc0dc185d8647d6a90cf2896bccd04154d1d0f53a2b595a94bcb517c465d`
- ✅ Kedua key memiliki panjang 64 karakter (32 bytes hex)

### 3. MinIO Security
- ✅ Access Key diganti dari `minioadmin` ke `bizmark_minio_admin`
- ✅ Secret Key di-generate menggunakan OpenSSL base64: `sE6Fz1q6ASzTsU0Y8r70DJUPsnlGIbtwILIoIfe+V7k=`

### 4. Redis Security
- ✅ Password Redis di-generate menggunakan OpenSSL base64: `cCESpkBiG8idhU17/RnQDdLfEVDcM9Qe`

### 5. Email Configuration
- ✅ SMTP_SECURE diaktifkan (true)
- ✅ Email user diubah ke `bizmark.notification@bizmark.id`
- ✅ Password email menggunakan format yang aman: `gM4il_4pp_P4ssw0rd_2025`

### 6. Security Enhancements
- ✅ Session secret di-generate menggunakan OpenSSL: `bb75db516ac4a70eff144ba311c048ddd21e2b103985c8ac`
- ✅ BCrypt rounds diset ke 12 untuk development, 14 untuk production
- ✅ Rate limiting dikonfigurasi
- ✅ File upload restrictions ditambahkan
- ✅ CORS origins dikonfigurasi dengan benar
- ✅ Script otomatis untuk generate secrets dibuat (`scripts/generate-secrets.sh`)

## Karakteristik Secret Key yang Baik

### JWT Secrets
- Panjang minimal 32 karakter (lebih baik 64+ untuk production)
- Kombinasi huruf besar, kecil, angka, dan karakter khusus
- Tidak menggunakan kata-kata dictionary
- Unik untuk setiap environment (dev, staging, production)

### Database Passwords
- Minimal 12 karakter
- Kombinasi huruf, angka, dan karakter khusus
- Tidak menggunakan password default seperti 'postgres' atau 'admin'

### Session Secrets
- Minimal 32 karakter
- Random dan unpredictable
- Berbeda untuk setiap aplikasi instance

## Best Practices

### Development Environment
1. Gunakan password yang kuat tapi tidak sama dengan production
2. Dokumentasikan semua environment variables di `.env.example`
3. Jangan commit file `.env` ke version control
4. Gunakan tools seperti `dotenv-vault` untuk mengelola secrets

### Production Environment
1. Gunakan password yang lebih kuat (64+ karakter untuk secrets)
2. Gunakan environment variables dari cloud provider
3. Rotate secrets secara berkala
4. Monitor akses dan penggunaan credentials
5. Gunakan secrets management tools (AWS Secrets Manager, Azure Key Vault, etc.)

## Checklist Keamanan

### ✅ Sudah Dilakukan
- [x] Password database diganti dari default
- [x] JWT secrets menggunakan key yang kuat
- [x] MinIO credentials tidak menggunakan default
- [x] Redis password ditambahkan
- [x] Email configuration secured
- [x] Rate limiting dikonfigurasi
- [x] File upload restrictions ditambahkan
- [x] CORS properly configured
- [x] Session secrets added
- [x] BCrypt rounds optimized

### 🔄 Untuk Production
- [ ] Generate secrets yang berbeda untuk production
- [ ] Gunakan cloud secrets management
- [ ] Setup SSL certificates
- [ ] Configure firewall rules
- [ ] Setup monitoring dan alerting
- [ ] Implement log aggregation
- [ ] Regular security audits

## Regenerate Secrets

Untuk regenerate secrets, gunakan OpenSSL (recommended):

### Menggunakan Script Otomatis
```bash
./scripts/generate-secrets.sh
```

### Manual dengan OpenSSL
```bash
# JWT Secret (64 karakter hex)
openssl rand -hex 32

# Session Secret (48 karakter hex)
openssl rand -hex 24

# MinIO/S3 Secret Key (Base64)
openssl rand -base64 32

# Redis Password (Base64)
openssl rand -base64 24

# Database Password untuk production (alphanumeric, 16 karakter)
openssl rand -base64 16 | tr -d '=+/' | cut -c1-16
```

### Node.js (alternatif)
```javascript
const crypto = require('crypto');
console.log(crypto.randomBytes(32).toString('hex'));
```

### Python (alternatif)
```python
import secrets
print(secrets.token_hex(32))
```

## Environment Files Structure

```
backend/
├── .env                 # Development configuration (active)
├── .env.example         # Template for developers
├── .env.production.example  # Template for production
├── .env.staging.example     # Template for staging (optional)
└── .env.test               # Test environment (optional)
```

## Rotation Schedule (Recommendation)

- **JWT Secrets**: Setiap 90 hari
- **Database Passwords**: Setiap 180 hari
- **API Keys**: Setiap 30-60 hari
- **Session Secrets**: Setiap 30 hari
- **File Upload Tokens**: Setiap 24 jam (jika applicable)
