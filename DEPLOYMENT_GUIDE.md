# Deployment Guide - Platform SaaS Perizinan UMKM

## Daftar Isi
1. [Persiapan Server](#persiapan-server)
2. [Setup Docker Environment](#setup-docker-environment)
3. [Konfigurasi Database](#konfigurasi-database)
4. [Deployment Aplikasi](#deployment-aplikasi)
5. [Konfigurasi Reverse Proxy](#konfigurasi-reverse-proxy)
6. [SSL/TLS Setup](#ssltls-setup)
7. [Monitoring & Logging](#monitoring--logging)
8. [Backup Strategy](#backup-strategy)
9. [CI/CD Pipeline](#cicd-pipeline)
10. [Troubleshooting](#troubleshooting)

## Persiapan Server

### Spesifikasi VPS Minimum
- **RAM**: 8GB
- **CPU**: 2-4 cores
- **Storage**: 50GB SSD/NVMe
- **OS**: Ubuntu Server 22.04 LTS
- **Network**: Minimal 100 Mbps

### Initial Server Setup
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install essential packages
sudo apt install -y curl wget git htop nginx-utils unzip

# Create deployment user
sudo adduser deploy
sudo usermod -aG sudo deploy
sudo usermod -aG docker deploy

# Setup SSH key for deployment user
sudo -u deploy mkdir -p /home/deploy/.ssh
sudo -u deploy chmod 700 /home/deploy/.ssh

# Copy your public key to authorized_keys
echo "your_public_key_here" | sudo -u deploy tee /home/deploy/.ssh/authorized_keys
sudo -u deploy chmod 600 /home/deploy/.ssh/authorized_keys

# Disable password authentication (optional, after SSH key setup)
sudo sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config
sudo systemctl restart ssh
```

### Firewall Configuration
```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow ssh

# Allow HTTP and HTTPS
sudo ufw allow 80
sudo ufw allow 443

# Check status
sudo ufw status verbose
```

### System Optimization
```bash
# Increase file descriptor limits
echo "* soft nofile 65536" | sudo tee -a /etc/security/limits.conf
echo "* hard nofile 65536" | sudo tee -a /etc/security/limits.conf

# Optimize kernel parameters
sudo tee -a /etc/sysctl.conf << EOF
vm.swappiness=10
vm.dirty_ratio=15
vm.dirty_background_ratio=5
net.core.somaxconn=1024
net.ipv4.tcp_max_syn_backlog=2048
EOF

sudo sysctl -p
```

## Setup Docker Environment

### Install Docker & Docker Compose
```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group
sudo usermod -aG docker deploy

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Enable Docker service
sudo systemctl enable docker
sudo systemctl start docker

# Test installation
docker --version
docker-compose --version
```

### Docker Daemon Configuration
```bash
# Optimize Docker for production
sudo tee /etc/docker/daemon.json << EOF
{
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "10m",
    "max-file": "3"
  },
  "storage-driver": "overlay2",
  "userland-proxy": false,
  "experimental": false,
  "metrics-addr": "127.0.0.1:9323",
  "default-ulimits": {
    "nofile": {
      "Name": "nofile",
      "Hard": 64000,
      "Soft": 64000
    }
  }
}
EOF

sudo systemctl restart docker
```

## Konfigurasi Database

### PostgreSQL Container Setup
```bash
# Create data directories
sudo mkdir -p /opt/bizmark/data/postgres
sudo mkdir -p /opt/bizmark/data/redis
sudo mkdir -p /opt/bizmark/data/minio
sudo chown -R deploy:deploy /opt/bizmark

# Create PostgreSQL config
mkdir -p /opt/bizmark/config/postgres
```

### Production PostgreSQL Configuration
```bash
# /opt/bizmark/config/postgres/postgresql.conf
cat > /opt/bizmark/config/postgres/postgresql.conf << EOF
# Memory Configuration (for 8GB RAM VPS)
shared_buffers = 2GB
work_mem = 64MB
maintenance_work_mem = 512MB
effective_cache_size = 6GB
wal_buffers = 16MB

# Connection Settings
max_connections = 100
superuser_reserved_connections = 3

# Checkpoint Settings
checkpoint_completion_target = 0.9
wal_checkpoint_timeout = 15min
checkpoint_warning = 30s

# Performance Settings
random_page_cost = 1.1  # SSD optimized
effective_io_concurrency = 200
max_worker_processes = 4
max_parallel_workers_per_gather = 2
max_parallel_workers = 4

# Logging
log_line_prefix = '%t [%p]: [%l-1] user=%u,db=%d,app=%a,client=%h '
log_checkpoints = on
log_connections = on
log_disconnections = on
log_lock_waits = on
log_temp_files = 0
log_autovacuum_min_duration = 0
log_error_verbosity = default

# Autovacuum
autovacuum = on
autovacuum_naptime = 1min
autovacuum_vacuum_threshold = 50
autovacuum_analyze_threshold = 50
autovacuum_vacuum_scale_factor = 0.2
autovacuum_analyze_scale_factor = 0.1

# Security
ssl = on
password_encryption = scram-sha-256
EOF
```

### Docker Compose Production Configuration
```yaml
# /opt/bizmark/docker-compose.prod.yml
version: '3.8'

services:
  postgres:
    image: postgres:15-alpine
    container_name: bizmark_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: bizmark_db
      POSTGRES_USER: bizmark_user
      POSTGRES_PASSWORD: ${POSTGRES_PASSWORD}
      PGDATA: /var/lib/postgresql/data/pgdata
    volumes:
      - /opt/bizmark/data/postgres:/var/lib/postgresql/data
      - /opt/bizmark/config/postgres/postgresql.conf:/etc/postgresql/postgresql.conf
      - /opt/bizmark/backups:/backups
    ports:
      - "127.0.0.1:5432:5432"
    command: postgres -c config_file=/etc/postgresql/postgresql.conf
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U bizmark_user -d bizmark_db"]
      interval: 30s
      timeout: 10s
      retries: 3
    networks:
      - bizmark_network

  redis:
    image: redis:7-alpine
    container_name: bizmark_redis
    restart: unless-stopped
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - /opt/bizmark/data/redis:/data
    ports:
      - "127.0.0.1:6379:6379"
    healthcheck:
      test: ["CMD", "redis-cli", "--raw", "incr", "ping"]
      interval: 30s
      timeout: 10s
      retries: 3
    networks:
      - bizmark_network

  minio:
    image: minio/minio:latest
    container_name: bizmark_minio
    restart: unless-stopped
    environment:
      MINIO_ROOT_USER: ${MINIO_ROOT_USER}
      MINIO_ROOT_PASSWORD: ${MINIO_ROOT_PASSWORD}
    volumes:
      - /opt/bizmark/data/minio:/data
    ports:
      - "127.0.0.1:9000:9000"
      - "127.0.0.1:9001:9001"
    command: server /data --console-address ":9001"
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:9000/minio/health/live"]
      interval: 30s
      timeout: 10s
      retries: 3
    networks:
      - bizmark_network

  backend:
    image: bizmark/backend:latest
    container_name: bizmark_backend
    restart: unless-stopped
    environment:
      NODE_ENV: production
      DATABASE_HOST: postgres
      DATABASE_PORT: 5432
      DATABASE_USERNAME: bizmark_user
      DATABASE_PASSWORD: ${POSTGRES_PASSWORD}
      DATABASE_NAME: bizmark_db
      REDIS_HOST: redis
      REDIS_PORT: 6379
      REDIS_PASSWORD: ${REDIS_PASSWORD}
      JWT_SECRET: ${JWT_SECRET}
      JWT_REFRESH_SECRET: ${JWT_REFRESH_SECRET}
      MINIO_ENDPOINT: minio
      MINIO_PORT: 9000
      MINIO_ACCESS_KEY: ${MINIO_ROOT_USER}
      MINIO_SECRET_KEY: ${MINIO_ROOT_PASSWORD}
    ports:
      - "127.0.0.1:3001:3001"
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_healthy
      minio:
        condition: service_healthy
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:3001/health"]
      interval: 30s
      timeout: 10s
      retries: 3
    networks:
      - bizmark_network

  frontend:
    image: bizmark/frontend:latest
    container_name: bizmark_frontend
    restart: unless-stopped
    environment:
      NODE_ENV: production
      NEXT_PUBLIC_API_URL: https://api.yourdomain.com
    ports:
      - "127.0.0.1:3000:3000"
    depends_on:
      - backend
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:3000/api/health"]
      interval: 30s
      timeout: 10s
      retries: 3
    networks:
      - bizmark_network

networks:
  bizmark_network:
    driver: bridge

volumes:
  postgres_data:
  redis_data:
  minio_data:
```

### Environment Variables
```bash
# /opt/bizmark/.env.prod
# Database
POSTGRES_PASSWORD=your_strong_postgres_password

# Redis
REDIS_PASSWORD=your_strong_redis_password

# MinIO
MINIO_ROOT_USER=bizmark_admin
MINIO_ROOT_PASSWORD=your_strong_minio_password

# JWT
JWT_SECRET=your_super_secret_jwt_key_min_256_bits
JWT_REFRESH_SECRET=your_super_secret_refresh_key_min_256_bits

# Application
APP_NAME=Bizmark UMKM
APP_URL=https://yourdomain.com
API_URL=https://api.yourdomain.com

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password

# Monitoring
SENTRY_DSN=your_sentry_dsn_if_using_sentry
```

## Deployment Aplikasi

### Build Docker Images
```bash
# Clone repository
cd /opt/bizmark
git clone https://github.com/tanerizawa/bizmark.id.git .

# Build backend image
docker build -t bizmark/backend:latest -f backend/Dockerfile backend/

# Build frontend image
docker build -t bizmark/frontend:latest -f frontend/Dockerfile frontend/
```

### Backend Dockerfile
```dockerfile
# backend/Dockerfile
FROM node:18-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production && npm cache clean --force

FROM node:18-alpine AS production

RUN apk add --no-cache dumb-init curl

WORKDIR /app
COPY --from=builder /app/node_modules ./node_modules
COPY . .

RUN npm run build && \
    rm -rf src && \
    rm -rf node_modules/@types && \
    rm -rf node_modules/typescript

USER node

EXPOSE 3001

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:3001/health || exit 1

CMD ["dumb-init", "node", "dist/main.js"]
```

### Frontend Dockerfile
```dockerfile
# frontend/Dockerfile
FROM node:18-alpine AS builder

WORKDIR /app
COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM node:18-alpine AS production

RUN apk add --no-cache dumb-init curl

WORKDIR /app

COPY --from=builder /app/public ./public
COPY --from=builder /app/.next/standalone ./
COPY --from=builder /app/.next/static ./.next/static

USER node

EXPOSE 3000

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:3000/api/health || exit 1

CMD ["dumb-init", "node", "server.js"]
```

### Database Migration & Setup
```bash
# Run database migrations
docker-compose -f docker-compose.prod.yml exec backend npm run migration:run

# Create initial admin user (if needed)
docker-compose -f docker-compose.prod.yml exec backend npm run seed:admin

# Setup MinIO buckets
docker-compose -f docker-compose.prod.yml exec backend npm run setup:storage
```

## Konfigurasi Reverse Proxy

### Install Caddy
```bash
# Install Caddy
sudo apt install -y debian-keyring debian-archive-keyring apt-transport-https
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/gpg.key' | sudo gpg --dearmor -o /usr/share/keyrings/caddy-stable-archive-keyring.gpg
curl -1sLf 'https://dl.cloudsmith.io/public/caddy/stable/debian.deb.txt' | sudo tee /etc/apt/sources.list.d/caddy-stable.list
sudo apt update
sudo apt install caddy
```

### Caddy Configuration
```bash
# /etc/caddy/Caddyfile
{
    email your-email@domain.com
    admin off
}

# Main application
yourdomain.com {
    reverse_proxy localhost:3000 {
        health_uri /api/health
        health_interval 30s
        health_timeout 10s
    }
    
    encode gzip
    
    header {
        # Security headers
        Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        X-Content-Type-Options "nosniff"
        X-Frame-Options "DENY"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' wss: https:; media-src 'self'; object-src 'none'; child-src 'none'; worker-src 'self'; frame-ancestors 'none'; form-action 'self'; base-uri 'self'; manifest-src 'self';"
        
        # Remove server info
        -Server
    }
    
    # Rate limiting
    rate_limit {
        zone dynamic_zone {
            key {remote_host}
            events 100
            window 1m
        }
    }
    
    # Logging
    log {
        output file /var/log/caddy/yourdomain.com.log {
            roll_size 100mb
            roll_keep 10
        }
        format json
    }
}

# API subdomain
api.yourdomain.com {
    reverse_proxy localhost:3001 {
        health_uri /health
        health_interval 30s
        health_timeout 10s
    }
    
    encode gzip
    
    header {
        # API-specific headers
        Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        X-Content-Type-Options "nosniff"
        X-Frame-Options "DENY"
        Access-Control-Allow-Origin "https://yourdomain.com"
        Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
        Access-Control-Allow-Headers "Content-Type, Authorization"
        
        -Server
    }
    
    # API rate limiting (more restrictive)
    rate_limit {
        zone api_zone {
            key {remote_host}
            events 1000
            window 1h
        }
    }
    
    # Special handling for file uploads
    handle /api/documents/upload {
        request_body {
            max_size 50MB
        }
        reverse_proxy localhost:3001
    }
    
    log {
        output file /var/log/caddy/api.yourdomain.com.log {
            roll_size 100mb
            roll_keep 10
        }
        format json
    }
}

# MinIO admin (optional, for direct access)
files.yourdomain.com {
    reverse_proxy localhost:9001
    
    # Restrict access to admin IPs only
    @allowed {
        remote_ip 10.0.0.0/8 172.16.0.0/12 192.168.0.0/16 your.admin.ip.here
    }
    
    handle @allowed {
        reverse_proxy localhost:9001
    }
    
    handle {
        respond "Access Denied" 403
    }
}
```

### Start Caddy Service
```bash
# Enable and start Caddy
sudo systemctl enable caddy
sudo systemctl start caddy

# Check status
sudo systemctl status caddy

# Test configuration
sudo caddy validate --config /etc/caddy/Caddyfile

# Reload configuration
sudo systemctl reload caddy
```

## SSL/TLS Setup

### Automatic SSL with Caddy
Caddy automatically handles SSL certificates through Let's Encrypt. No additional configuration needed!

### Manual SSL Configuration (if using Nginx)
```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain certificates
sudo certbot --nginx -d yourdomain.com -d api.yourdomain.com -d files.yourdomain.com

# Test automatic renewal
sudo certbot renew --dry-run

# Setup auto-renewal cron job
echo "0 12 * * * /usr/bin/certbot renew --quiet" | sudo crontab -
```

## Monitoring & Logging

### Setup Log Rotation
```bash
# Create logrotate configuration
sudo tee /etc/logrotate.d/bizmark << EOF
/var/log/caddy/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 caddy caddy
    postrotate
        systemctl reload caddy
    endscript
}

/opt/bizmark/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 deploy deploy
}
EOF
```

### Basic Monitoring Setup
```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs ncdu

# Create monitoring script
cat > /opt/bizmark/scripts/monitor.sh << 'EOF'
#!/bin/bash

# System metrics
echo "=== System Resources ==="
free -h
df -h
uptime

# Docker containers
echo -e "\n=== Docker Containers ==="
docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# Database connection test
echo -e "\n=== Database Status ==="
docker-compose -f /opt/bizmark/docker-compose.prod.yml exec -T postgres pg_isready -U bizmark_user -d bizmark_db

# Application health checks
echo -e "\n=== Application Health ==="
curl -f http://localhost:3000/api/health > /dev/null 2>&1 && echo "Frontend: OK" || echo "Frontend: ERROR"
curl -f http://localhost:3001/health > /dev/null 2>&1 && echo "Backend: OK" || echo "Backend: ERROR"

# Recent errors in logs
echo -e "\n=== Recent Errors ==="
docker-compose -f /opt/bizmark/docker-compose.prod.yml logs --tail=10 --since=1h | grep -i error || echo "No recent errors"
EOF

chmod +x /opt/bizmark/scripts/monitor.sh

# Add to crontab for daily monitoring report
(crontab -l 2>/dev/null; echo "0 8 * * * /opt/bizmark/scripts/monitor.sh | mail -s 'Bizmark Daily Report' your-email@domain.com") | crontab -
```

### Application Logging Configuration
```typescript
// backend/src/main.ts - Add structured logging
import { Logger } from '@nestjs/common';
import * as winston from 'winston';

const logger = winston.createLogger({
  level: process.env.LOG_LEVEL || 'info',
  format: winston.format.combine(
    winston.format.timestamp(),
    winston.format.errors({ stack: true }),
    winston.format.json()
  ),
  transports: [
    new winston.transports.File({ 
      filename: '/opt/bizmark/logs/error.log', 
      level: 'error' 
    }),
    new winston.transports.File({ 
      filename: '/opt/bizmark/logs/combined.log' 
    }),
    new winston.transports.Console({
      format: winston.format.simple()
    })
  ],
});
```

## Backup Strategy

### Automated Database Backup
```bash
# Create backup script
cat > /opt/bizmark/scripts/backup-db.sh << 'EOF'
#!/bin/bash

BACKUP_DIR="/opt/bizmark/backups"
DATE=$(date +%Y%m%d_%H%M%S)
DAYS_TO_KEEP=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
docker-compose -f /opt/bizmark/docker-compose.prod.yml exec -T postgres pg_dump -U bizmark_user bizmark_db | gzip > $BACKUP_DIR/bizmark_db_$DATE.sql.gz

# Backup uploaded files
tar -czf $BACKUP_DIR/minio_data_$DATE.tar.gz -C /opt/bizmark/data minio/

# Remove old backups
find $BACKUP_DIR -name "bizmark_db_*.sql.gz" -mtime +$DAYS_TO_KEEP -delete
find $BACKUP_DIR -name "minio_data_*.tar.gz" -mtime +$DAYS_TO_KEEP -delete

# Upload to remote storage (optional)
# aws s3 sync $BACKUP_DIR s3://your-backup-bucket/bizmark/

echo "Backup completed: $DATE"
EOF

chmod +x /opt/bizmark/scripts/backup-db.sh

# Schedule daily backups
(crontab -l 2>/dev/null; echo "0 2 * * * /opt/bizmark/scripts/backup-db.sh >> /opt/bizmark/logs/backup.log 2>&1") | crontab -
```

### Backup Restoration
```bash
# Restore database from backup
cat > /opt/bizmark/scripts/restore-db.sh << 'EOF'
#!/bin/bash

BACKUP_FILE=$1

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file.sql.gz>"
    exit 1
fi

echo "Restoring database from $BACKUP_FILE"
echo "This will OVERWRITE the current database. Continue? (y/N)"
read -r response

if [[ "$response" =~ ^[Yy]$ ]]; then
    # Stop application
    docker-compose -f /opt/bizmark/docker-compose.prod.yml stop backend frontend
    
    # Drop and recreate database
    docker-compose -f /opt/bizmark/docker-compose.prod.yml exec postgres psql -U bizmark_user -c "DROP DATABASE IF EXISTS bizmark_db;"
    docker-compose -f /opt/bizmark/docker-compose.prod.yml exec postgres psql -U bizmark_user -c "CREATE DATABASE bizmark_db;"
    
    # Restore backup
    zcat $BACKUP_FILE | docker-compose -f /opt/bizmark/docker-compose.prod.yml exec -T postgres psql -U bizmark_user bizmark_db
    
    # Start application
    docker-compose -f /opt/bizmark/docker-compose.prod.yml start backend frontend
    
    echo "Database restored successfully"
else
    echo "Restore cancelled"
fi
EOF

chmod +x /opt/bizmark/scripts/restore-db.sh
```

## CI/CD Pipeline

### GitHub Actions Workflow
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]
  workflow_dispatch:

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Node.js
        uses: actions/setup-node@v3
        with:
          node-version: '18'
          cache: 'npm'
          
      - name: Install dependencies
        run: |
          cd backend && npm ci
          cd ../frontend && npm ci
          
      - name: Run tests
        run: |
          cd backend && npm run test
          cd ../frontend && npm run test
          
      - name: Build applications
        run: |
          cd backend && npm run build
          cd ../frontend && npm run build

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup Docker Buildx
        uses: docker/setup-buildx-action@v2
        
      - name: Build Backend Image
        run: |
          docker build -t bizmark/backend:${{ github.sha }} -f backend/Dockerfile backend/
          docker tag bizmark/backend:${{ github.sha }} bizmark/backend:latest
          
      - name: Build Frontend Image
        run: |
          docker build -t bizmark/frontend:${{ github.sha }} -f frontend/Dockerfile frontend/
          docker tag bizmark/frontend:${{ github.sha }} bizmark/frontend:latest
          
      - name: Save Docker Images
        run: |
          docker save bizmark/backend:latest | gzip > backend.tar.gz
          docker save bizmark/frontend:latest | gzip > frontend.tar.gz
          
      - name: Deploy to VPS
        uses: appleboy/ssh-action@v0.1.8
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /opt/bizmark
            
            # Backup current images
            docker tag bizmark/backend:latest bizmark/backend:backup || true
            docker tag bizmark/frontend:latest bizmark/frontend:backup || true
            
            # Pull latest code
            git pull origin main
            
            # Stop services
            docker-compose -f docker-compose.prod.yml stop backend frontend
            
            # Load new images
            docker load < backend.tar.gz
            docker load < frontend.tar.gz
            
            # Start services
            docker-compose -f docker-compose.prod.yml up -d
            
            # Health check
            sleep 30
            curl -f http://localhost:3000/api/health
            curl -f http://localhost:3001/health
            
            # Remove old images
            docker image prune -f
            
            echo "Deployment completed successfully"
            
      - name: Copy Docker Images
        uses: appleboy/scp-action@v0.1.4
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          source: "backend.tar.gz,frontend.tar.gz"
          target: "/opt/bizmark/"
```

### Deployment Script
```bash
# /opt/bizmark/scripts/deploy.sh
#!/bin/bash

set -e

echo "Starting deployment..."

# Pull latest code
git pull origin main

# Build new images
docker-compose -f docker-compose.prod.yml build

# Start database migration if needed
docker-compose -f docker-compose.prod.yml run --rm backend npm run migration:run

# Rolling update
docker-compose -f docker-compose.prod.yml up -d --no-deps backend frontend

# Health check
echo "Waiting for services to start..."
sleep 30

# Check backend health
if curl -f http://localhost:3001/health; then
    echo "Backend is healthy"
else
    echo "Backend health check failed"
    exit 1
fi

# Check frontend health
if curl -f http://localhost:3000/api/health; then
    echo "Frontend is healthy"
else
    echo "Frontend health check failed"
    exit 1
fi

# Cleanup old images
docker image prune -f

echo "Deployment completed successfully!"
```

## Troubleshooting

### Common Issues & Solutions

#### 1. Application Won't Start
```bash
# Check container logs
docker-compose -f docker-compose.prod.yml logs backend
docker-compose -f docker-compose.prod.yml logs frontend

# Check container status
docker ps -a

# Restart specific service
docker-compose -f docker-compose.prod.yml restart backend
```

#### 2. Database Connection Issues
```bash
# Check PostgreSQL logs
docker-compose -f docker-compose.prod.yml logs postgres

# Test database connection
docker-compose -f docker-compose.prod.yml exec postgres psql -U bizmark_user -d bizmark_db -c "SELECT version();"

# Check database permissions
docker-compose -f docker-compose.prod.yml exec postgres psql -U bizmark_user -d bizmark_db -c "\dt"
```

#### 3. High Memory Usage
```bash
# Monitor memory usage
docker stats

# Check for memory leaks
docker-compose -f docker-compose.prod.yml exec backend npm run memory-check

# Restart services if needed
docker-compose -f docker-compose.prod.yml restart
```

#### 4. SSL Certificate Issues
```bash
# Check certificate status
sudo caddy list-certificates

# Force certificate renewal
sudo caddy reload --config /etc/caddy/Caddyfile

# Check Caddy logs
sudo journalctl -u caddy -f
```

#### 5. Performance Issues
```bash
# Check system resources
htop
iotop -a

# Analyze database performance
docker-compose -f docker-compose.prod.yml exec postgres psql -U bizmark_user -d bizmark_db -c "SELECT * FROM pg_stat_activity;"

# Check slow queries
docker-compose -f docker-compose.prod.yml exec postgres psql -U bizmark_user -d bizmark_db -c "SELECT query, mean_time, calls FROM pg_stat_statements ORDER BY mean_time DESC LIMIT 10;"
```

### Emergency Procedures

#### Rollback Deployment
```bash
# /opt/bizmark/scripts/rollback.sh
#!/bin/bash

echo "Rolling back to previous version..."

# Stop current services
docker-compose -f docker-compose.prod.yml stop backend frontend

# Restore backup images
docker tag bizmark/backend:backup bizmark/backend:latest
docker tag bizmark/frontend:backup bizmark/frontend:latest

# Start services
docker-compose -f docker-compose.prod.yml up -d

# Health check
sleep 30
curl -f http://localhost:3000/api/health
curl -f http://localhost:3001/health

echo "Rollback completed"
```

#### Database Recovery
```bash
# In case of database corruption
docker-compose -f docker-compose.prod.yml stop backend frontend
docker-compose -f docker-compose.prod.yml stop postgres

# Restore from latest backup
/opt/bizmark/scripts/restore-db.sh /opt/bizmark/backups/bizmark_db_latest.sql.gz

# Start services
docker-compose -f docker-compose.prod.yml up -d
```

### Monitoring Commands
```bash
# System monitoring one-liner
watch -n 5 'echo "=== System ===" && free -h && echo -e "\n=== Docker ===" && docker stats --no-stream && echo -e "\n=== Disk ===" && df -h'

# Application logs tail
docker-compose -f docker-compose.prod.yml logs -f --tail=100

# Database monitoring
docker-compose -f docker-compose.prod.yml exec postgres psql -U bizmark_user -d bizmark_db -c "SELECT datname, numbackends, xact_commit, xact_rollback FROM pg_stat_database WHERE datname = 'bizmark_db';"
```

Dokumentasi deployment ini memberikan panduan lengkap untuk men-deploy aplikasi SaaS perizinan UMKM ke production environment dengan fokus pada keamanan, performa, dan maintainability pada VPS dengan sumber daya terbatas.
