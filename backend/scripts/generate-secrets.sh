#!/bin/bash
# generate-secrets.sh - Script untuk generate secrets menggunakan OpenSSL

echo "🔑 Generating secure secrets using OpenSSL..."
echo ""

echo "# Generated secrets - $(date)"
echo "# Copy these values to your .env file"
echo ""

echo "# JWT Configuration"
echo "JWT_SECRET=$(openssl rand -hex 32)"
echo "JWT_REFRESH_SECRET=$(openssl rand -hex 32)"
echo ""

echo "# Session Secret"
echo "SESSION_SECRET=$(openssl rand -hex 24)"
echo ""

echo "# MinIO Secret Key"
echo "MINIO_SECRET_KEY=$(openssl rand -base64 32)"
echo ""

echo "# Redis Password"
echo "REDIS_PASSWORD=$(openssl rand -base64 24)"
echo ""

echo "# Database Password (for production only)"
echo "DATABASE_PASSWORD=$(openssl rand -base64 16 | tr -d '=+/' | cut -c1-16)"
echo ""

echo "✅ Secrets generated successfully!"
echo "📝 Please update your .env file with these new values"
echo "⚠️  Keep these secrets secure and never commit them to version control"
