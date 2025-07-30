#!/bin/bash

# Full Stack Development Startup Script
# Phase 4: Complete Frontend + Backend Integration

set -e

echo "🚀 Phase 4: Full Stack Integration Startup"
echo "=========================================="

# Get the project root directory
PROJECT_ROOT="$(dirname "$0")/.."
FRONTEND_DIR="$PROJECT_ROOT/frontend"
BACKEND_DIR="$PROJECT_ROOT/backend"

# Check if both directories exist
if [ ! -d "$FRONTEND_DIR" ]; then
    echo "❌ Frontend directory not found: $FRONTEND_DIR"
    exit 1
fi

if [ ! -d "$BACKEND_DIR" ]; then
    echo "❌ Backend directory not found: $BACKEND_DIR"
    exit 1
fi

echo "✅ Project directories found"

# Check Docker
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker first."
    exit 1
fi

echo "✅ Docker is running"

# Function to start backend services
start_backend() {
    echo "🔥 Starting Backend Services..."
    cd "$BACKEND_DIR"
    
    # Start Docker services
    echo "🐳 Starting Docker containers..."
    docker compose -f docker-compose.dev.yml up -d
    
    # Wait for services
    echo "⏳ Waiting for services to start..."
    sleep 15
    
    # Install dependencies if needed
    if [ ! -d "node_modules" ]; then
        echo "📦 Installing backend dependencies..."
        npm install
    fi
    
    # Run migrations and seed
    echo "🗄️  Setting up database..."
    npm run migration:run || npm run typeorm migration:run || echo "⚠️  Migration check complete"
    npm run seed || echo "⚠️  Seed check complete"
    
    # Start backend server in background
    echo "🚀 Starting backend server..."
    npm run start:dev || npm start &
    BACKEND_PID=$!
    
    # Wait for backend to be ready
    echo "⏳ Waiting for backend to be ready..."
    for i in {1..30}; do
        if curl -f http://localhost:3001/api/v1/health > /dev/null 2>&1; then
            echo "✅ Backend is ready!"
            break
        fi
        if [ $i -eq 30 ]; then
            echo "❌ Backend failed to start"
            kill $BACKEND_PID 2>/dev/null || true
            exit 1
        fi
        sleep 2
    done
}

# Function to start frontend
start_frontend() {
    echo "🎨 Starting Frontend..."
    cd "$FRONTEND_DIR"
    
    # Install dependencies if needed
    if [ ! -d "node_modules" ]; then
        echo "📦 Installing frontend dependencies..."
        npm install
    fi
    
    # Update environment variables for backend connection
    if [ ! -f ".env.local" ]; then
        echo "📝 Creating frontend .env.local..."
        cat > .env.local << EOF
NEXT_PUBLIC_API_URL=http://localhost:3001
NEXT_PUBLIC_APP_ENV=development
EOF
    fi
    
    # Start frontend server
    echo "🚀 Starting frontend development server..."
    npm run dev &
    FRONTEND_PID=$!
    
    # Wait for frontend to be ready
    echo "⏳ Waiting for frontend to be ready..."
    for i in {1..30}; do
        if curl -f http://localhost:3000 > /dev/null 2>&1; then
            echo "✅ Frontend is ready!"
            break
        fi
        if [ $i -eq 30 ]; then
            echo "❌ Frontend failed to start"
            kill $FRONTEND_PID 2>/dev/null || true
            exit 1
        fi
        sleep 2
    done
}

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Shutting down services..."
    
    # Kill frontend
    if [ ! -z "$FRONTEND_PID" ]; then
        kill $FRONTEND_PID 2>/dev/null || true
        echo "✅ Frontend stopped"
    fi
    
    # Kill backend
    if [ ! -z "$BACKEND_PID" ]; then
        kill $BACKEND_PID 2>/dev/null || true
        echo "✅ Backend stopped"
    fi
    
    # Stop Docker containers
    cd "$BACKEND_DIR"
    docker compose -f docker-compose.dev.yml down || true
    echo "✅ Docker containers stopped"
}

# Set trap for cleanup
trap cleanup EXIT

# Start services
start_backend
start_frontend

# Display service information
echo ""
echo "🎉 Full Stack Development Environment Ready!"
echo "============================================"
echo "🎨 Frontend:      http://localhost:3000"
echo "🔥 Backend API:   http://localhost:3001"
echo "📖 API Docs:      http://localhost:3001/api/docs"
echo "🗄️  Database:     localhost:5432 (bizmark_dev)"
echo "🔴 Redis:         localhost:6379"
echo "📁 MinIO Console: http://localhost:9001"
echo ""
echo "👉 Backend Integration Dashboard: http://localhost:3000/dashboard/backend-integration"
echo ""
echo "Press Ctrl+C to stop all services"

# Keep script running
wait
