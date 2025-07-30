#!/bin/bash
# setup-render-env.sh - Script untuk generate environment variables untuk Render.com

echo "🔧 Generating Production Environment Variables for Render.com"
echo "============================================================"
echo ""

# Generate new production secrets
JWT_SECRET=$(openssl rand -hex 32)
JWT_REFRESH_SECRET=$(openssl rand -hex 32)
SESSION_SECRET=$(openssl rand -hex 24)
REDIS_PASSWORD=$(openssl rand -base64 24)

echo "📋 Environment Variables untuk Render.com Dashboard:"
echo ""
echo "=== CORE APPLICATION ==="
echo "NODE_ENV=production"
echo "API_PREFIX=api"
echo "API_VERSION=v1"
echo "LOG_LEVEL=info"
echo "ENABLE_DOCS=true"
echo ""

echo "=== JWT CONFIGURATION ==="
echo "JWT_SECRET=${JWT_SECRET}"
echo "JWT_REFRESH_SECRET=${JWT_REFRESH_SECRET}"
echo "JWT_EXPIRES_IN=3600"
echo ""

echo "=== SESSION CONFIGURATION ==="
echo "SESSION_SECRET=${SESSION_SECRET}"
echo ""

echo "=== SECURITY CONFIGURATION ==="
echo "BCRYPT_ROUNDS=14"
echo "MAX_LOGIN_ATTEMPTS=3"
echo "LOCKOUT_TIME=1800000"
echo ""

echo "=== RATE LIMITING ==="
echo "RATE_LIMIT_WINDOW_MS=900000"
echo "RATE_LIMIT_MAX_REQUESTS=50"
echo ""

echo "=== FILE UPLOAD ==="
echo "MAX_FILE_SIZE=10485760"
echo "ALLOWED_FILE_TYPES=pdf,doc,docx,jpg,jpeg,png"
echo ""

echo "=== DATABASE POOL ==="
echo "DB_POOL_MIN=5"
echo "DB_POOL_MAX=20"
echo "DB_POOL_ACQUIRE_TIMEOUT=60000"
echo "DB_POOL_IDLE_TIMEOUT=30000"
echo ""

echo "=== MONITORING ==="
echo "HEALTH_CHECK_TIMEOUT=5000"
echo "METRICS_ENABLED=true"
echo ""

echo "=== URLS (Update setelah deploy) ==="
echo "APP_URL=https://bizmark-frontend.onrender.com"
echo "API_URL=https://bizmark-api.onrender.com"
echo "CORS_ORIGINS=https://bizmark-frontend.onrender.com"
echo ""

echo "=== DATABASE & CACHE (Dari Render Dashboard) ==="
echo "DATABASE_URL=[Copy dari Render PostgreSQL Service]"
echo "REDIS_URL=[Copy dari Render Redis Service]" 
echo ""

echo "============================================================"
echo "✅ Copy environment variables di atas ke Render Dashboard"
echo "📝 Pastikan untuk update APP_URL dan API_URL setelah deploy"
echo "🔐 Jangan commit secrets ini ke version control!"
echo ""

# Save to temporary file for reference
TEMP_FILE="render-env-$(date +%Y%m%d-%H%M%S).txt"
{
    echo "# Render.com Environment Variables - Generated $(date)"
    echo "# JANGAN COMMIT FILE INI KE GIT!"
    echo ""
    echo "NODE_ENV=production"
    echo "API_PREFIX=api"
    echo "API_VERSION=v1"
    echo "LOG_LEVEL=info"
    echo "JWT_SECRET=${JWT_SECRET}"
    echo "JWT_REFRESH_SECRET=${JWT_REFRESH_SECRET}"
    echo "SESSION_SECRET=${SESSION_SECRET}"
    echo "BCRYPT_ROUNDS=14"
    echo "MAX_LOGIN_ATTEMPTS=3"
    echo "LOCKOUT_TIME=1800000"
    echo "RATE_LIMIT_WINDOW_MS=900000"
    echo "RATE_LIMIT_MAX_REQUESTS=50"
    echo "MAX_FILE_SIZE=10485760"
    echo "ALLOWED_FILE_TYPES=pdf,doc,docx,jpg,jpeg,png"
    echo "DB_POOL_MIN=5"
    echo "DB_POOL_MAX=20"
    echo "HEALTH_CHECK_TIMEOUT=5000"
    echo "METRICS_ENABLED=true"
    echo ""
    echo "# Update these after deployment:"
    echo "APP_URL=https://bizmark-frontend.onrender.com"
    echo "API_URL=https://bizmark-api.onrender.com"
    echo "CORS_ORIGINS=https://bizmark-frontend.onrender.com"
    echo ""
    echo "# Get these from Render Dashboard:"
    echo "DATABASE_URL="
    echo "REDIS_URL="
} > "$TEMP_FILE"

echo "💾 Environment variables saved to: $TEMP_FILE"
echo "🗑️  Delete this file after copying to Render Dashboard"
