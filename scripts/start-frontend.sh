#!/bin/bash

# Frontend Startup Script with Port Management
# Ensures frontend always runs on port 3000

set -e

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk log dengan warna
log() {
    echo -e "${BLUE}[FRONTEND]${NC} $1"
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
FRONTEND_PORT=3000
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT_MANAGER="$SCRIPT_DIR/port-manager.sh"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
FRONTEND_DIR="$PROJECT_ROOT/frontend"

log "🎨 Starting Next.js Frontend with Port Management"
log "=============================================="

# Validate frontend directory
if [ ! -d "$FRONTEND_DIR" ]; then
    error "Frontend directory not found: $FRONTEND_DIR"
    exit 1
fi

# Validate port manager
if [ ! -f "$PORT_MANAGER" ]; then
    error "Port manager script not found: $PORT_MANAGER"
    exit 1
fi

# Reserve frontend port
log "🔌 Reserving port $FRONTEND_PORT for frontend..."
"$PORT_MANAGER" reserve $FRONTEND_PORT

# Change to frontend directory
cd "$FRONTEND_DIR"

# Check if package.json exists
if [ ! -f "package.json" ]; then
    error "package.json not found in frontend directory"
    exit 1
fi

# Set explicit port in environment
export PORT=$FRONTEND_PORT

# Install dependencies if needed (optional)
if [ ! -d "node_modules" ]; then
    log "📦 Installing frontend dependencies..."
    npm install
fi

# Start frontend
log "🚀 Starting Next.js frontend on port $FRONTEND_PORT..."

# Use different start methods based on available scripts
if npm run | grep -q "dev" && [ "$NODE_ENV" != "production" ]; then
    log "Using default development mode (webpack-based, stable)..."
    npm run dev
elif npm run | grep -q "dev:turbo"; then
    log "Using Turbopack development mode (experimental)..."
    npm run dev:turbo
elif npm run | grep -q "start"; then
    log "Using production mode..."
    npm run start
else
    error "No dev or start script found in package.json"
    exit 1
fi
