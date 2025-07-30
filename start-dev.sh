#!/bin/bash

# Start Backend and Frontend Development Servers

echo "🚀 Starting Bizmark.id Development Environment"
echo "=============================================="

# Check if ports are available
if lsof -Pi :3000 -sTCP:LISTEN -t >/dev/null ; then
    echo "❌ Port 3000 is already in use (Frontend)"
    echo "Please stop the service running on port 3000 or change the port"
    exit 1
fi

if lsof -Pi :3001 -sTCP:LISTEN -t >/dev/null ; then
    echo "❌ Port 3001 is already in use (Backend)"
    echo "Please stop the service running on port 3001 or change the port"
    exit 1
fi

# Start Docker containers if not running
echo "📦 Starting Docker containers..."
cd backend
if ! docker-compose -f docker-compose.dev.yml ps | grep -q "Up"; then
    docker-compose -f docker-compose.dev.yml up -d
    echo "⏳ Waiting for containers to be ready..."
    sleep 10
fi

# Start Backend
echo "🔧 Starting Backend Server (NestJS)..."
npm run start:dev &
BACKEND_PID=$!

# Wait a bit for backend to start
sleep 5

# Start Frontend
echo "🎨 Starting Frontend Server (Next.js)..."
cd ../frontend
npm run dev &
FRONTEND_PID=$!

# Function to cleanup on exit
cleanup() {
    echo ""
    echo "🛑 Shutting down development servers..."
    kill $BACKEND_PID 2>/dev/null
    kill $FRONTEND_PID 2>/dev/null
    echo "✅ Development servers stopped"
    exit 0
}

# Set trap for cleanup
trap cleanup INT TERM

echo ""
echo "✅ Development Environment Started!"
echo "📱 Frontend: http://localhost:3000"
echo "🔧 Backend:  http://localhost:3001"
echo "📊 API Docs: http://localhost:3001/api"
echo ""
echo "Press Ctrl+C to stop all servers"

# Wait for processes
wait
