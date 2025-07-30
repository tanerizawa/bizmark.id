#!/bin/bash

# Full Stack Development Startup Script with Port Management
# Phase 4: Complete Frontend + Backend Integration with Port Consistency

set -e

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk log dengan warna
log() {
    echo -e "${BLUE}[STARTUP]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

# Configuration
BACKEND_PORT=3001
FRONTEND_PORT=3000
BACKEND_DIR="backend"
FRONTEND_DIR="frontend"
DOCKER_COMPOSE_FILE="docker-compose.dev.yml"

# Port Manager Script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT_MANAGER="$SCRIPT_DIR/port-manager.sh"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

log "🚀 Phase 4: Full Stack Integration Startup with Port Management"
log "=============================================================="

# Function untuk cleanup saat script exit
cleanup() {
    log "Cleaning up background processes..."
    jobs -p | xargs -r kill 2>/dev/null || true
    wait 2>/dev/null || true
}
trap cleanup EXIT

# Validate project structure
log "📁 Validating project structure..."
if [ ! -d "$PROJECT_ROOT/$FRONTEND_DIR" ]; then
    error "Frontend directory not found: $PROJECT_ROOT/$FRONTEND_DIR"
    exit 1
fi

if [ ! -d "$PROJECT_ROOT/$BACKEND_DIR" ]; then
    error "Backend directory not found: $PROJECT_ROOT/$BACKEND_DIR"
    exit 1
fi

if [ ! -f "$PORT_MANAGER" ]; then
    error "Port manager script not found: $PORT_MANAGER"
    exit 1
fi

success "Project structure validated"

# Check and reserve ports
log "🔌 Managing ports for consistent deployment..."

log "Reserving frontend port $FRONTEND_PORT..."
"$PORT_MANAGER" reserve $FRONTEND_PORT

log "Reserving backend port $BACKEND_PORT..."
"$PORT_MANAGER" reserve $BACKEND_PORT

success "Ports $FRONTEND_PORT and $BACKEND_PORT are reserved"

# Check Docker
log "🐳 Checking Docker availability..."
if ! docker info > /dev/null 2>&1; then
    error "Docker is not running. Please start Docker first."
    exit 1
fi
success "Docker is available"

# Start Docker services
log "🐳 Starting Docker services..."
cd "$PROJECT_ROOT"

if [ -f "$DOCKER_COMPOSE_FILE" ]; then
    log "Using docker-compose file: $DOCKER_COMPOSE_FILE"
    docker-compose -f "$DOCKER_COMPOSE_FILE" up -d
else
    log "Using default docker-compose.yml"
    docker-compose up -d
fi

# Wait for Docker services to be healthy
log "⏳ Waiting for Docker services to be healthy..."
sleep 10

# Check Docker services health
log "🏥 Checking Docker services health..."
POSTGRES_HEALTHY=$(docker ps --filter "name=bizmark_postgres_dev" --filter "health=healthy" --format "{{.Names}}" | wc -l)
REDIS_HEALTHY=$(docker ps --filter "name=bizmark_redis_dev" --filter "health=healthy" --format "{{.Names}}" | wc -l)
MINIO_HEALTHY=$(docker ps --filter "name=bizmark_minio_dev" --filter "health=healthy" --format "{{.Names}}" | wc -l)

if [ "$POSTGRES_HEALTHY" -eq 0 ] || [ "$REDIS_HEALTHY" -eq 0 ] || [ "$MINIO_HEALTHY" -eq 0 ]; then
    warn "Some Docker services are not healthy yet. Waiting additional 15 seconds..."
    sleep 15
fi

docker ps --format "table {{.Names}}\t{{.Status}}" | grep bizmark
success "Docker services are running"

# Start Backend with port enforcement
log "🔧 Starting NestJS Backend on port $BACKEND_PORT..."
cd "$PROJECT_ROOT/$BACKEND_DIR"

# Double check backend port is free
"$PORT_MANAGER" check $BACKEND_PORT || "$PORT_MANAGER" kill $BACKEND_PORT

# Set explicit port in environment
export PORT=$BACKEND_PORT

# Start backend in background with explicit port
npm run start &
BACKEND_PID=$!

log "Backend started with PID $BACKEND_PID on port $BACKEND_PORT"

# Wait for backend to be ready
log "⏳ Waiting for backend to be ready..."
BACKEND_READY=false
for i in {1..30}; do
    if curl -s -f "http://localhost:$BACKEND_PORT/api/v1/health/ready" > /dev/null 2>&1; then
        BACKEND_READY=true
        break
    fi
    log "Attempt $i/30 - Waiting for backend..."
    sleep 2
done

if [ "$BACKEND_READY" = false ]; then
    error "Backend failed to start within 60 seconds"
    kill $BACKEND_PID 2>/dev/null || true
    exit 1
fi

success "Backend is ready at http://localhost:$BACKEND_PORT"

# Test backend health
log "🏥 Testing backend health endpoints..."
HEALTH_RESPONSE=$(curl -s "http://localhost:$BACKEND_PORT/api/v1/health/ready")
log "Health check response: $(echo $HEALTH_RESPONSE | jq -r '.data.status' 2>/dev/null || echo 'OK')"

# Start Frontend with port enforcement
log "🎨 Starting Next.js Frontend on port $FRONTEND_PORT..."
cd "$PROJECT_ROOT/$FRONTEND_DIR"

# Double check frontend port is free
"$PORT_MANAGER" check $FRONTEND_PORT || "$PORT_MANAGER" kill $FRONTEND_PORT

# Set explicit port in environment
export PORT=$FRONTEND_PORT

# Start frontend in background with explicit port
npm run dev -- --port $FRONTEND_PORT &
FRONTEND_PID=$!

log "Frontend started with PID $FRONTEND_PID on port $FRONTEND_PORT"

# Wait for frontend to be ready
log "⏳ Waiting for frontend to be ready..."
FRONTEND_READY=false
for i in {1..30}; do
    if curl -s -f "http://localhost:$FRONTEND_PORT" > /dev/null 2>&1; then
        FRONTEND_READY=true
        break
    fi
    log "Attempt $i/30 - Waiting for frontend..."
    sleep 3
done

if [ "$FRONTEND_READY" = false ]; then
    error "Frontend failed to start within 90 seconds"
    kill $FRONTEND_PID 2>/dev/null || true
    kill $BACKEND_PID 2>/dev/null || true
    exit 1
fi

success "Frontend is ready at http://localhost:$FRONTEND_PORT"

# Final validation
log "🧪 Final integration validation..."
sleep 5

# Test frontend access
FRONTEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:$FRONTEND_PORT" || echo "000")
BACKEND_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:$BACKEND_PORT/api/v1/health/ready" || echo "000")

if [ "$FRONTEND_STATUS" = "200" ] && [ "$BACKEND_STATUS" = "200" ]; then
    success "✅ Full-stack integration successful!"
    log ""
    log "🎉 Phase 4 Complete - All services are running:"
    log "────────────────────────────────────────────────"
    log "🎨 Frontend:  http://localhost:$FRONTEND_PORT"
    log "🔧 Backend:   http://localhost:$BACKEND_PORT"
    log "🏥 Health:    http://localhost:$BACKEND_PORT/api/v1/health/ready"
    log "📊 Dashboard: http://localhost:$FRONTEND_PORT/dashboard/backend-integration"
    log ""
    log "🐳 Docker Services:"
    docker ps --format "  {{.Names}}: {{.Status}}" | grep bizmark
    log ""
    log "📝 Process IDs for management:"
    log "  Backend PID: $BACKEND_PID"
    log "  Frontend PID: $FRONTEND_PID"
    log ""
    log "⚡ To stop services: kill $BACKEND_PID $FRONTEND_PID"
    log "🔄 To restart: $0"
else
    error "Integration validation failed"
    error "Frontend Status: $FRONTEND_STATUS, Backend Status: $BACKEND_STATUS"
    kill $FRONTEND_PID $BACKEND_PID 2>/dev/null || true
    exit 1
fi

# Keep script running to monitor processes
log "🔍 Monitoring services (Ctrl+C to stop)..."
while true; do
    # Check if processes are still running
    if ! kill -0 $BACKEND_PID 2>/dev/null; then
        error "Backend process $BACKEND_PID died"
        kill $FRONTEND_PID 2>/dev/null || true
        exit 1
    fi
    
    if ! kill -0 $FRONTEND_PID 2>/dev/null; then
        error "Frontend process $FRONTEND_PID died"
        kill $BACKEND_PID 2>/dev/null || true
        exit 1
    fi
    
    sleep 30
done
