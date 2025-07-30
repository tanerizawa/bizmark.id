#!/bin/bash

# Production Deployment Script
# Automated deployment for Bizmark UMKM Platform

set -e  # Exit on any error

# Configuration
PROJECT_NAME="bizmark-umkm"
DOCKER_COMPOSE_FILE="docker-compose.prod.yml"
BACKUP_DIR="/opt/bizmark/backups"
LOG_FILE="/opt/bizmark/logs/deployment.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Pre-deployment checks
pre_deployment_checks() {
    log "Starting pre-deployment checks..."
    
    # Check if Docker is running
    if ! docker info > /dev/null 2>&1; then
        error "Docker is not running. Please start Docker and try again."
        exit 1
    fi
    
    # Check if docker-compose is available
    if ! command -v docker-compose &> /dev/null; then
        error "docker-compose is not installed. Please install it and try again."
        exit 1
    fi
    
    # Check if .env.prod file exists
    if [ ! -f ".env.prod" ]; then
        error ".env.prod file not found. Please create it from .env.production.example"
        exit 1
    fi
    
    # Check disk space (minimum 5GB)
    available_space=$(df /opt/bizmark | awk 'NR==2 {print $4}')
    if [ "$available_space" -lt 5242880 ]; then  # 5GB in KB
        error "Insufficient disk space. At least 5GB required."
        exit 1
    fi
    
    # Check memory (minimum 4GB)
    available_memory=$(free -m | awk 'NR==2{printf "%.1f", $7/1024}')
    if (( $(echo "$available_memory < 4.0" | bc -l) )); then
        error "Insufficient memory. At least 4GB free memory required."
        exit 1
    fi
    
    success "Pre-deployment checks passed"
}

# Backup current deployment
backup_current_deployment() {
    log "Creating backup of current deployment..."
    
    timestamp=$(date +%Y%m%d_%H%M%S)
    backup_name="backup_${timestamp}"
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR/$backup_name"
    
    # Backup database
    if docker-compose -f "$DOCKER_COMPOSE_FILE" ps postgres | grep -q "Up"; then
        log "Backing up database..."
        docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres pg_dump -U bizmark_user bizmark_db | gzip > "$BACKUP_DIR/$backup_name/database.sql.gz"
        success "Database backup completed"
    fi
    
    # Backup uploaded files
    if [ -d "/opt/bizmark/data/minio" ]; then
        log "Backing up uploaded files..."
        tar -czf "$BACKUP_DIR/$backup_name/minio_data.tar.gz" -C /opt/bizmark/data minio/
        success "Files backup completed"
    fi
    
    # Save current image tags
    docker images --format "table {{.Repository}}:{{.Tag}}" | grep bizmark > "$BACKUP_DIR/$backup_name/image_tags.txt"
    
    # Keep only last 5 backups
    ls -1t "$BACKUP_DIR" | tail -n +6 | xargs -I {} rm -rf "$BACKUP_DIR/{}"
    
    success "Backup completed: $backup_name"
    echo "$backup_name" > /tmp/last_backup_name
}

# Build application images
build_images() {
    log "Building application images..."
    
    # Build backend image
    log "Building backend image..."
    if docker build -t bizmark/backend:latest -f backend/Dockerfile backend/; then
        success "Backend image built successfully"
    else
        error "Failed to build backend image"
        exit 1
    fi
    
    # Build frontend image
    log "Building frontend image..."
    if docker build -t bizmark/frontend:latest -f frontend/Dockerfile frontend/; then
        success "Frontend image built successfully"
    else
        error "Failed to build frontend image"
        exit 1
    fi
    
    # Tag images with timestamp
    timestamp=$(date +%Y%m%d_%H%M%S)
    docker tag bizmark/backend:latest "bizmark/backend:$timestamp"
    docker tag bizmark/frontend:latest "bizmark/frontend:$timestamp"
    
    log "Images tagged with timestamp: $timestamp"
}

# Deploy application
deploy_application() {
    log "Deploying application..."
    
    # Stop current services gracefully
    log "Stopping current services..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" stop backend frontend || true
    
    # Run database migrations if needed
    log "Running database migrations..."
    docker-compose -f "$DOCKER_COMPOSE_FILE" run --rm backend npm run migration:run || warning "Migration failed or not needed"
    
    # Start all services
    log "Starting services..."
    if docker-compose -f "$DOCKER_COMPOSE_FILE" up -d --remove-orphans; then
        success "Services started successfully"
    else
        error "Failed to start services"
        rollback
        exit 1
    fi
    
    # Wait for services to be ready
    log "Waiting for services to be ready..."
    sleep 30
    
    # Health checks
    health_check
}

# Perform health checks
health_check() {
    log "Performing health checks..."
    
    local max_attempts=10
    local attempt=1
    
    # Check backend health
    while [ $attempt -le $max_attempts ]; do
        if curl -f http://localhost:3001/health > /dev/null 2>&1; then
            success "Backend health check passed"
            break
        else
            warning "Backend health check failed (attempt $attempt/$max_attempts)"
            if [ $attempt -eq $max_attempts ]; then
                error "Backend health check failed after $max_attempts attempts"
                rollback
                exit 1
            fi
            sleep 10
            ((attempt++))
        fi
    done
    
    # Reset attempt counter for frontend
    attempt=1
    
    # Check frontend health
    while [ $attempt -le $max_attempts ]; do
        if curl -f http://localhost:3000/api/health > /dev/null 2>&1; then
            success "Frontend health check passed"
            break
        else
            warning "Frontend health check failed (attempt $attempt/$max_attempts)"
            if [ $attempt -eq $max_attempts ]; then
                error "Frontend health check failed after $max_attempts attempts"
                rollback
                exit 1
            fi
            sleep 10
            ((attempt++))
        fi
    done
    
    # Check database connection
    if docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres pg_isready -U bizmark_user -d bizmark_db > /dev/null 2>&1; then
        success "Database health check passed"
    else
        error "Database health check failed"
        rollback
        exit 1
    fi
    
    success "All health checks passed"
}

# Rollback deployment
rollback() {
    error "Rolling back deployment..."
    
    if [ -f "/tmp/last_backup_name" ]; then
        backup_name=$(cat /tmp/last_backup_name)
        log "Rolling back to backup: $backup_name"
        
        # Stop current services
        docker-compose -f "$DOCKER_COMPOSE_FILE" stop backend frontend
        
        # Restore database if backup exists
        if [ -f "$BACKUP_DIR/$backup_name/database.sql.gz" ]; then
            log "Restoring database..."
            zcat "$BACKUP_DIR/$backup_name/database.sql.gz" | docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres psql -U bizmark_user bizmark_db
        fi
        
        # Restore files if backup exists
        if [ -f "$BACKUP_DIR/$backup_name/minio_data.tar.gz" ]; then
            log "Restoring files..."
            tar -xzf "$BACKUP_DIR/$backup_name/minio_data.tar.gz" -C /opt/bizmark/data/
        fi
        
        # Start services
        docker-compose -f "$DOCKER_COMPOSE_FILE" up -d
        
        warning "Rollback completed"
    else
        error "No backup found for rollback"
    fi
}

# Post-deployment tasks
post_deployment() {
    log "Running post-deployment tasks..."
    
    # Clear application cache if needed
    # docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend npm run cache:clear || true
    
    # Update search index if needed
    # docker-compose -f "$DOCKER_COMPOSE_FILE" exec backend npm run search:reindex || true
    
    # Send deployment notification (implement if needed)
    # send_deployment_notification
    
    # Clean up old Docker images
    log "Cleaning up old Docker images..."
    docker image prune -f || true
    
    # Show deployment summary
    show_deployment_summary
    
    success "Post-deployment tasks completed"
}

# Show deployment summary
show_deployment_summary() {
    log "Deployment Summary:"
    echo "=================================="
    echo "Deployment completed at: $(date)"
    echo "Services status:"
    docker-compose -f "$DOCKER_COMPOSE_FILE" ps
    echo "=================================="
    echo "Application URLs:"
    echo "Frontend: http://localhost:3000"
    echo "Backend: http://localhost:3001"
    echo "MinIO Console: http://localhost:9001"
    echo "=================================="
}

# Main deployment process
main() {
    log "Starting deployment of $PROJECT_NAME..."
    
    # Ensure we're in the correct directory
    cd /opt/bizmark
    
    # Run deployment steps
    pre_deployment_checks
    backup_current_deployment
    build_images
    deploy_application
    post_deployment
    
    success "Deployment completed successfully!"
}

# Handle script interruption
trap 'error "Deployment interrupted"; rollback; exit 1' INT TERM

# Run main function if script is executed directly
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
