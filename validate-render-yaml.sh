#!/bin/bash

# Script untuk memvalidasi render.yaml
# Catatan: Ini hanya simulasi untuk membantu memvisualisasikan hasil validasi

echo "🔍 Memvalidasi konfigurasi render.yaml..."
echo "============================================"

# Baca file render.yaml
RENDER_YAML=$(cat render.yaml)

# Cek fitur preview environments
if grep -q "^previews:" render.yaml; then
    echo "⚠️  WARNING: Preview Environments tidak tersedia untuk Hobby workspaces"
    echo "     (Sudah dinonaktifkan dengan comment)"
fi

# Cek plan database
if grep -q "plan: standard" render.yaml; then
    echo "❌ ERROR: databases[0].plan - Legacy Postgres plans 'standard' tidak didukung"
else
    echo "✅ VALID: Database plan sudah diupdate ke yang didukung"
fi

# Cek diskSizeGB
if grep -q "diskSizeGB:" render.yaml; then
    echo "❌ ERROR: databases[0].diskSizeGB - tidak dapat mengubah ukuran disk"
else
    echo "✅ VALID: Parameter diskSizeGB sudah dihapus"
fi

# Cek header dan routes pada Node.js service
if grep -q "headers:" render.yaml && grep -q "type: web" render.yaml && grep -q "runtime: node" render.yaml; then
    echo "❌ ERROR: services[0].headers - headers hanya didukung untuk static web services"
elif grep -q "routes:" render.yaml && grep -q "type: web" render.yaml && grep -q "runtime: node" render.yaml; then
    echo "❌ ERROR: services[0].routes - routes hanya didukung untuk static web services"
else
    echo "✅ VALID: Tidak ada headers/routes pada service Node.js"
fi

echo ""
echo "📝 Rekomendasi:"
echo "- Pastikan workspace Anda memiliki izin untuk fitur yang digunakan"
echo "- Test deployment di lingkungan development terlebih dahulu"
echo "- Periksa kuota dan batas resource Render.com untuk plan yang dipilih"
echo ""
echo "✅ Validasi selesai!"
