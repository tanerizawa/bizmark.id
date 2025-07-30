# 📄 Update Konfigurasi Render.yaml

## 🔧 Perubahan Terakhir pada Konfigurasi

### 1. Disabled Preview Environments untuk Hobby Workspace
**Masalah:**
```
Preview Environments are not available for Hobby workspaces
```

**Solusi:**
Preview environments dinonaktifkan (di-comment out) karena tidak tersedia untuk workspace Hobby. Anda perlu mengupgrade ke workspace Team atau yang lebih tinggi untuk menggunakan fitur ini.

```yaml
# Sebelum
previews:
  generation: automatic
  expireAfterDays: 7

# Sesudah
# previews:
#   generation: automatic
#   expireAfterDays: 7
```

### 2. Update Plan Database PostgreSQL
**Masalah:**
```
databases[0].plan
Legacy Postgres plans, including 'standard', are no longer supported for new databases. 
Update your database instance to a new plan in your render.yaml
```

**Solusi:**
Plan database diubah dari 'standard' (yang sudah tidak didukung) ke plan yang didukung yaitu 'basic-1gb'.

```yaml
# Sebelum
plan: standard

# Sesudah
plan: basic-1gb
```

### 3. Hapus Konfigurasi Disk Size
**Masalah:**
```
databases[0].diskSizeGB
plan Standard cannot change disk size
```

**Solusi:**
Parameter `diskSizeGB` dihapus karena tidak didukung untuk plan database yang dipilih. Setiap plan sudah memiliki ukuran disk default.

```yaml
# Sebelum
diskSizeGB: 15  # Explicitly define disk size

# Sesudah
# (parameter dihapus)
```

## 💰 Implikasi Biaya

| Plan | Harga | Spesifikasi |
|------|-------|-------------|
| ❌ **standard** (lama) | ~$20/bulan | 1 CPU dedicated, 2 GB RAM |
| ✅ **basic-1gb** (baru) | ~$14/bulan | 1 CPU shared, 1 GB RAM, 3 koneksi maksimum |

⚠️ **Catatan Performa**: Plan `basic-1gb` memiliki harga yang lebih rendah namun dengan spesifikasi yang lebih rendah juga. Jika Anda membutuhkan performa yang lebih tinggi, pertimbangkan untuk menggunakan plan yang lebih tinggi seperti `pro-1gb` atau `pro-2gb`.

## ✅ Status Validasi

Konfigurasi `render.yaml` sekarang seharusnya valid dan siap untuk digunakan dalam deployment.

## 🔍 Referensi

- [Render.com Database Plans](https://render.com/docs/databases#plans)
- [Render.com Preview Environments](https://render.com/docs/preview-environments)
- [Render.com Blueprint Spec](https://render.com/docs/blueprint-spec)
