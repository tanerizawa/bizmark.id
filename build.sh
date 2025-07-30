#!/usr/bin/env bash
# build.sh - Script untuk membantu proses build di Render.com

# Masuk ke direktori backend
cd backend

# Install dependencies
echo "Installing dependencies..."
npm install

# Build aplikasi
echo "Building application..."
npm run build

# Jalankan migrasi database jika perlu
if [[ "$RUN_MIGRATIONS" == "true" ]]; then
  echo "Running database migrations..."
  npm run migration:run
fi

echo "Build completed successfully!"
