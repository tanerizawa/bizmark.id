#!/bin/bash

# Development Environment Setup Script
# Compatible dengan Production Deployment

set -e

echo "🚀 Setting up Bizmark.id Development Environment"
echo "================================================="

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running"
    echo "💡 Please start Docker Desktop and try again"
    exit 1
fi

# Check if Docker Compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed"
    echo "💡 Please install Docker Compose and try again"
    exit 1
fi

echo "📋 Starting infrastructure services..."

# Stop any existing containers
echo "🛑 Stopping existing containers..."
docker-compose -f docker-compose.dev.yml down 2>/dev/null || true

# Pull latest images
echo "📥 Pulling latest Docker images..."
docker-compose -f docker-compose.dev.yml pull

# Start services
echo "🏗️  Starting infrastructure services..."
docker-compose -f docker-compose.dev.yml up -d

# Wait for services to be healthy
echo "⏳ Waiting for services to be ready..."
max_attempts=30
attempt=0

while [ $attempt -lt $max_attempts ]; do
    if docker-compose -f docker-compose.dev.yml ps | grep -q "unhealthy"; then
        echo "   Attempt $((attempt + 1))/$max_attempts - Services not ready yet..."
        sleep 5
        attempt=$((attempt + 1))
    else
        break
    fi
done

if [ $attempt -eq $max_attempts ]; then
    echo "❌ Services failed to start within expected time"
    echo "💡 Check logs with: docker-compose -f docker-compose.dev.yml logs"
    exit 1
fi

# Check individual services
echo "🔍 Checking service health..."

# PostgreSQL
if docker-compose -f docker-compose.dev.yml exec -T postgres pg_isready -U postgres -d bizmark_dev > /dev/null 2>&1; then
    echo "✅ PostgreSQL is ready"
else
    echo "❌ PostgreSQL is not ready"
    exit 1
fi

# Redis
if docker-compose -f docker-compose.dev.yml exec -T redis redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis is ready"
else
    echo "❌ Redis is not ready"
    exit 1
fi

# MinIO (check if port is responding)
if curl -f http://localhost:9000/minio/health/live > /dev/null 2>&1; then
    echo "✅ MinIO is ready"
else
    echo "❌ MinIO is not ready"
    exit 1
fi

# Install Node.js dependencies if needed
if [ ! -d "node_modules" ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
fi

# Run database migrations
echo "🗄️  Running database migrations..."
npm run migration:run 2>/dev/null || {
    echo "⚠️  Migration failed, this might be expected for first run"
    echo "💡 Generating and running migrations..."
    npm run migration:generate -- --name=InitialMigration 2>/dev/null || true
    npm run migration:run 2>/dev/null || true
}

# Seed database with initial data
echo "🌱 Seeding database with initial data..."
npm run seed 2>/dev/null || {
    echo "⚠️  Seeding failed, this might be expected"
    echo "💡 Make sure seed scripts are available"
}

# Setup MinIO buckets
echo "🪣 Setting up MinIO buckets..."
docker-compose -f docker-compose.dev.yml exec -T minio mc alias set local http://localhost:9000 minioadmin minioadmin 2>/dev/null || true
docker-compose -f docker-compose.dev.yml exec -T minio mc mb local/bizmark-files 2>/dev/null || true
docker-compose -f docker-compose.dev.yml exec -T minio mc policy set public local/bizmark-files 2>/dev/null || true

echo ""
echo "🎉 Development environment setup completed!"
echo "================================================="
echo ""
echo "📋 Service URLs:"
echo "   🖥️  Application API: http://localhost:3001"
echo "   🗄️  PostgreSQL: localhost:5432"
echo "   🔴 Redis: localhost:6379"
echo "   📁 MinIO Console: http://localhost:9001 (minioadmin/minioadmin)"
echo "   📁 MinIO API: http://localhost:9000"
echo ""
echo "🛠️  Useful commands:"
echo "   📊 View logs: docker-compose -f docker-compose.dev.yml logs -f"
echo "   🛑 Stop services: docker-compose -f docker-compose.dev.yml down"
echo "   🔄 Restart services: docker-compose -f docker-compose.dev.yml restart"
echo "   🧹 Clean up: docker-compose -f docker-compose.dev.yml down -v"
echo ""
echo "🚀 Ready to start development!"
echo "   Run: npm run start:dev"
echo ""
