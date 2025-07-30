# 🔧 GitHub Actions Workflow - VPS Deployment

## ⚠️ Tentang Warning VS Code

Jika Anda melihat warning seperti ini di VS Code:
```
Context access might be invalid: VPS_HOST
Context access might be invalid: VPS_USERNAME
Context access might be invalid: VPS_SSH_KEY
Context access might be invalid: VPS_PORT
```

**Warning ini adalah NORMAL dan dapat diabaikan.** VS Code menampilkan warning ini karena secrets belum dikonfigurasi di repository GitHub Anda. Setelah Anda menambahkan secrets di GitHub repository settings, workflow akan berfungsi dengan baik.

## 📋 Secrets yang Perlu Dikonfigurasi

Sebelum menjalankan workflow ini, Anda perlu menambahkan secrets berikut di GitHub repository:

1. **VPS_HOST** - IP address atau hostname VPS Anda
2. **VPS_USERNAME** - Username SSH untuk login ke VPS
3. **VPS_SSH_KEY** - Private SSH key untuk akses ke VPS
4. **VPS_PORT** - Port SSH (biasanya 22)

## 🔐 Cara Menambahkan Secrets di GitHub

1. Buka repository GitHub Anda
2. Klik **Settings** > **Secrets and variables** > **Actions**
3. Klik **New repository secret**
4. Tambahkan masing-masing secret dengan nama dan nilai yang sesuai

## 🚀 Cara Kerja Workflow

Workflow ini akan:
1. Trigger otomatis saat ada push ke branch `main`
2. Atau dapat dipicu manual dari GitHub Actions tab
3. Build dan test aplikasi
4. Deploy ke VPS melalui SSH
5. Restart service menggunakan PM2

## 📝 Catatan Penting

- Pastikan VPS sudah dikonfigurasi sesuai panduan di `VPS_DEPLOYMENT_GUIDE.md`
- Pastikan PM2 sudah terinstall dan dikonfigurasi di VPS
- Pastikan aplikasi sudah di-clone ke `/var/www/bizmark.id` di VPS

## 🔍 Troubleshooting

Jika deployment gagal, cek:
1. Apakah semua secrets sudah dikonfigurasi dengan benar
2. Apakah SSH key memiliki akses ke VPS
3. Apakah direktori `/var/www/bizmark.id` ada di VPS
4. Apakah PM2 berjalan dengan baik di VPS
