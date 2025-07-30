# 🛡️ Konfigurasi Keamanan untuk Render.com

## 📄 Perubahan pada render.yaml

### Perbaikan Konfigurasi Render.yaml
Kami telah menghapus bagian `headers` dan `routes` dari layanan web Node.js di `render.yaml` karena:

- Pesan error: `headers only supported for static web services`
- Pesan error: `routes only supported for static web services`

Fitur-fitur ini hanya tersedia untuk layanan web statis dengan `runtime: static`, bukan untuk layanan Node.js.

### ✅ Konfigurasi Sekarang Valid
Konfigurasi `render.yaml` sekarang valid dan siap digunakan untuk deployment.

## 🔒 Mengimplementasikan Header Keamanan di Aplikasi NestJS

Karena header keamanan tidak dapat dideklarasikan dalam `render.yaml` untuk aplikasi Node.js, Anda harus mengimplementasikannya langsung di kode aplikasi NestJS. Berikut cara melakukannya:

### 1. Menggunakan Helmet Middleware

[Helmet](https://helmetjs.github.io/) adalah middleware yang membantu mengamankan aplikasi Express/NestJS dengan menyetel berbagai header HTTP.

**Instalasi:**
```bash
cd backend
npm install helmet
```

**Implementasi di `main.ts`:**
```typescript
import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import * as helmet from 'helmet';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  // Aktifkan Helmet untuk header keamanan
  app.use(helmet());
  
  // Atau konfigurasi lebih spesifik
  app.use(
    helmet({
      contentSecurityPolicy: {
        directives: {
          defaultSrc: ["'self'"],
          scriptSrc: ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
          styleSrc: ["'self'", "'unsafe-inline'"],
          imgSrc: ["'self'", "data:", "validator.swagger.io"],
          connectSrc: ["'self'"],
        },
      },
      xssFilter: true,
      frameguard: {
        action: 'sameorigin',
      },
    })
  );
  
  await app.listen(process.env.PORT || 3000);
}

bootstrap();
```

### 2. Header Keamanan yang Diimplementasikan

Header keamanan yang direkomendasikan untuk diterapkan:

| Header | Deskripsi | Nilai Rekomendasi |
|--------|-----------|-------------------|
| X-Frame-Options | Mencegah clickjacking | `SAMEORIGIN` |
| X-XSS-Protection | Perlindungan XSS | `1; mode=block` |
| X-Content-Type-Options | Mencegah MIME sniffing | `nosniff` |
| Content-Security-Policy | Membatasi sumber daya | Disesuaikan dengan kebutuhan |
| Strict-Transport-Security | Memaksa HTTPS | `max-age=31536000; includeSubDomains` |

### 3. Untuk Rute Rewrite

Jika Anda memerlukan fungsionalitas serupa dengan bagian `routes` yang dihapus dari `render.yaml`, Anda dapat:

1. **Menggunakan NestJS Controller:** Buat controller yang menangani rute-rute yang dibutuhkan

2. **Menggunakan Express Middleware:**
```typescript
// Di main.ts
import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import * as express from 'express';
import * as path from 'path';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  // Untuk SPA (Single Page Application)
  app.use('*', (req, res, next) => {
    const indexPath = path.join(__dirname, '..', 'public', 'index.html');
    if (
      !req.path.startsWith('/api') && 
      !req.path.startsWith('/assets') && 
      !req.path.includes('.')
    ) {
      res.sendFile(indexPath);
    } else {
      next();
    }
  });
  
  await app.listen(process.env.PORT || 3000);
}

bootstrap();
```

## 📝 Catatan Penting

- **Strategi Deployment**: Pertimbangkan untuk memisahkan frontend dan backend menjadi layanan terpisah di Render.com, dengan frontend sebagai situs statis (yang dapat menggunakan fitur `headers` dan `routes`).

- **Validasi Lokal**: Sebelum mendeploy, validasi file `render.yaml` dengan:
  ```bash
  curl -X POST https://render.com/api/v1/blueprint/validate \
    -H "Content-Type: application/json" \
    -d @- << EOF
  {
    "content": $(cat render.yaml)
  }
  EOF
  ```

- **Pengujian Header**: Setelah deploy, verifikasi header keamanan dengan:
  ```bash
  curl -I https://your-app.onrender.com
  ```
