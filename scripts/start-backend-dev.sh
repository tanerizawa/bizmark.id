#!/bin/bash

# Backend Development Startup Script
# Phase 4: Backend Integration & Production Deployment

set -e  # Exit on any error

echo "🚀 Starting Phase 4: Backend Integration & Production Deployment"
echo "=================================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "✅ Docker is running"

# Navigate to backend directory
cd "$(dirname "$0")/../backend"

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from template..."
    cp .env.example .env
    echo "⚠️  Please edit .env file with your actual values before proceeding"
    echo "📄 .env file location: $(pwd)/.env"
    read -p "Press Enter after editing .env file..."
fi

echo "✅ Environment file found"

# Start development services
echo "🐳 Starting development services (PostgreSQL, Redis, MinIO)..."
npm run docker:dev:up

# Wait for services to be ready
echo "⏳ Waiting for services to start..."
sleep 10

# Check service health
echo "🔍 Checking service health..."

# Check PostgreSQL
if docker exec bizmark_postgres_dev pg_isready -U postgres > /dev/null 2>&1; then
    echo "✅ PostgreSQL is ready"
else
    echo "❌ PostgreSQL is not ready"
    exit 1
fi

# Check Redis
if docker exec bizmark_redis_dev redis-cli ping | grep -q PONG; then
    echo "✅ Redis is ready"
else
    echo "❌ Redis is not ready"
    exit 1
fi

# Check MinIO
if docker exec bizmark_minio_dev curl -f http://localhost:9000/minio/health/ready > /dev/null 2>&1; then
    echo "✅ MinIO is ready"
else
    echo "⚠️  MinIO might need additional setup"
fi

# Install backend dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing backend dependencies..."
    npm install
fi

echo "✅ Backend dependencies installed"

# Run database migrations
echo "🗄️  Running database migrations..."
npm run migration:run || echo "⚠️  Migration might have failed - check manually"

# Seed database with initial data
echo "🌱 Seeding database..."
npm run seed || echo "⚠️  Seeding might have failed - check manually"

echo "✅ Database setup complete"

# Start backend development server
echo "🔥 Starting backend development server..."
echo "📡 Backend will be available at: http://localhost:3001"
echo "📖 API Documentation: http://localhost:3001/api/docs"
echo "💾 Database: localhost:5432 (bizmark_dev)"
echo "🔴 Redis: localhost:6379"
echo "📁 MinIO Console: http://localhost:9001 (minioadmin/minioadmin)"

# Function to handle cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Shutting down services..."
    npm run docker:dev:down
    echo "✅ Services stopped"
}

# Set trap to cleanup on script exit
trap cleanup EXIT

# Start the backend server
npm run start:dev
