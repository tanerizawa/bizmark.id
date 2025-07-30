#!/bin/bash

# Backend Startup Script with Port Management
# Ensures backend always runs on port 3001

set -e

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk log dengan warna
log() {
    echo -e "${BLUE}[BACKEND]${NC} $1"
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
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PORT_MANAGER="$SCRIPT_DIR/port-manager.sh"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
BACKEND_DIR="$PROJECT_ROOT/backend"

log "🔧 Starting NestJS Backend with Port Management"
log "=============================================="

# Validate backend directory
if [ ! -d "$BACKEND_DIR" ]; then
    error "Backend directory not found: $BACKEND_DIR"
    exit 1
fi

# Validate port manager
if [ ! -f "$PORT_MANAGER" ]; then
    error "Port manager script not found: $PORT_MANAGER"
    exit 1
fi

# Reserve backend port
log "🔌 Reserving port $BACKEND_PORT for backend..."
"$PORT_MANAGER" reserve $BACKEND_PORT

# Change to backend directory
cd "$BACKEND_DIR"

# Check if package.json exists
if [ ! -f "package.json" ]; then
    error "package.json not found in backend directory"
    exit 1
fi

# Set explicit port in environment
export PORT=$BACKEND_PORT

# Install dependencies if needed (optional)
if [ ! -d "node_modules" ]; then
    log "📦 Installing backend dependencies..."
    npm install
fi

# Start backend
log "🚀 Starting NestJS backend on port $BACKEND_PORT..."

# Set environment untuk mengurangi file watching issues pada macOS
export CHOKIDAR_USEPOLLING=true
export CHOKIDAR_INTERVAL=1000
export NODE_OPTIONS="--max-old-space-size=4096"

# Use different start methods based on available scripts
if npm run | grep -q "start:safe"; then
    log "Using safe mode (ts-node, no webpack compilation issues)..."
    npm run start:safe
elif npm run | grep -q "start:dev"; then
    log "Using development mode with polling (fixes EMFILE on macOS)..."
    npm run start:dev
elif npm run | grep -q "start"; then
    log "Using production mode..."
    npm run start
else
    error "No start script found in package.json"
    exit 1
fi
