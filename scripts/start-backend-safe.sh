#!/bin/bash

# Backend Safe Startup - No File Watching (untuk menghindari EMFILE error)
# Ensures backend always runs on port 3001 without file watching

set -e

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk log dengan warna
log() {
    echo -e "${BLUE}[BACKEND-SAFE]${NC} $1"
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

log "🔧 Starting NestJS Backend (Safe Mode - No File Watching)"
log "======================================================"

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

# Set explicit port and safe environment
export PORT=$BACKEND_PORT
export NODE_ENV=development
export NODE_OPTIONS="--max-old-space-size=4096"

# Disable file watching completely
export CHOKIDAR_USEPOLLING=false
export DISABLE_FILE_WATCHING=true

# Install dependencies if needed
if [ ! -d "node_modules" ]; then
    log "📦 Installing backend dependencies..."
    npm install
fi

# Build first to avoid watching issues
log "🔨 Building application..."
npm run build

# Start backend in safe mode (no file watching)
log "🚀 Starting NestJS backend on port $BACKEND_PORT (Safe Mode)..."
success "Backend will start without file watching to prevent EMFILE errors"

# Use production build to avoid file watching
npm run start:prod
