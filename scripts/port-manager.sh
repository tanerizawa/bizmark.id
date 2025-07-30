#!/bin/bash

# Port Manager - Utility untuk mengelola port dengan konsisten
# Usage: ./port-manager.sh kill <port> atau ./port-manager.sh check <port>

set -e

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function untuk log dengan warna
log() {
    echo -e "${BLUE}[PORT-MANAGER]${NC} $1"
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

# Function untuk kill proses di port tertentu
kill_port() {
    local port=$1
    
    if [ -z "$port" ]; then
        error "Port number is required"
        exit 1
    fi
    
    log "Checking for processes on port $port..."
    
    # Cari proses yang menggunakan port
    local pids=$(lsof -ti:$port 2>/dev/null || true)
    
    if [ -z "$pids" ]; then
        success "Port $port is already free"
        return 0
    fi
    
    log "Found processes using port $port: $pids"
    
    # Kill semua proses yang menggunakan port
    for pid in $pids; do
        local process_info=$(ps -p $pid -o comm= 2>/dev/null || echo "unknown")
        warn "Killing process $pid ($process_info) on port $port"
        kill -9 $pid 2>/dev/null || true
    done
    
    # Tunggu sebentar untuk memastikan port benar-benar free
    sleep 2
    
    # Verifikasi port sudah free
    local remaining_pids=$(lsof -ti:$port 2>/dev/null || true)
    if [ -z "$remaining_pids" ]; then
        success "Port $port is now free"
    else
        error "Failed to free port $port. Remaining processes: $remaining_pids"
        exit 1
    fi
}

# Function untuk check status port
check_port() {
    local port=$1
    
    if [ -z "$port" ]; then
        error "Port number is required"
        exit 1
    fi
    
    local pids=$(lsof -ti:$port 2>/dev/null || true)
    
    if [ -z "$pids" ]; then
        success "Port $port is available"
        return 0
    else
        warn "Port $port is in use by process(es): $pids"
        for pid in $pids; do
            local process_info=$(ps -p $pid -o pid,comm,args 2>/dev/null || echo "$pid unknown unknown")
            echo "  $process_info"
        done
        return 1
    fi
}

# Function untuk wait sampai port tersedia
wait_for_port() {
    local port=$1
    local max_attempts=${2:-30}
    local attempt=0
    
    log "Waiting for port $port to become available..."
    
    while [ $attempt -lt $max_attempts ]; do
        if check_port $port >/dev/null 2>&1; then
            success "Port $port is available after $attempt attempts"
            return 0
        fi
        
        attempt=$((attempt + 1))
        log "Attempt $attempt/$max_attempts - Port $port still in use, waiting..."
        sleep 1
    done
    
    error "Port $port is still not available after $max_attempts attempts"
    return 1
}

# Function untuk reserve port (kill existing dan tunggu available)
reserve_port() {
    local port=$1
    
    log "Reserving port $port..."
    kill_port $port
    wait_for_port $port 10
    success "Port $port is reserved and ready"
}

# Main script logic
case "${1:-}" in
    "kill")
        kill_port "$2"
        ;;
    "check")
        check_port "$2"
        ;;
    "wait")
        wait_for_port "$2" "$3"
        ;;
    "reserve")
        reserve_port "$2"
        ;;
    *)
        echo "Usage: $0 {kill|check|wait|reserve} <port> [max_attempts]"
        echo ""
        echo "Commands:"
        echo "  kill <port>              - Kill all processes using the specified port"
        echo "  check <port>             - Check if port is available"
        echo "  wait <port> [attempts]   - Wait for port to become available"
        echo "  reserve <port>           - Kill existing processes and reserve port"
        echo ""
        echo "Examples:"
        echo "  $0 kill 3000"
        echo "  $0 check 3001"
        echo "  $0 wait 3000 30"
        echo "  $0 reserve 3001"
        exit 1
        ;;
esac
