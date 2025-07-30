# 🚀 Panduan Docker Deployment ke VPS Ubuntu

## Daftar Isi
1. [Persyaratan VPS](#persyaratan-vps)
2. [Setup Docker di VPS](#setup-docker-di-vps)
3. [Setup GitHub Actions untuk Docker](#setup-github-actions-untuk-docker)
4. [Konfigurasi Docker](#konfigurasi-docker)
5. [Deployment Otomatis](#deployment-otomatis)
6. [Monitoring dan Scaling](#monitoring-dan-scaling)
7. [Troubleshooting](#troubleshooting)

## Persyaratan VPS

### Spesifikasi Minimum
- Ubuntu 20.04 LTS atau lebih baru
- 4GB RAM (untuk Docker)
- 2 vCPU
- 50GB SSD
- Virtualmin/Webmin sudah terinstall dan dikonfigurasi
- Port 3000 dan 3001 tersedia (untuk internal Docker services)

### Software Prasyarat
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Logout dan login kembali untuk apply docker group
# newgrp docker

# Note: Nginx sudah terinstall dengan Virtualmin, tidak perlu install lagi
```

## Setup Docker di VPS

### 1. Verifikasi Instalasi Docker
```bash
# Test Docker installation
docker --version
docker-compose --version

# Test Docker dengan hello-world
docker run hello-world
```

### 2. Setup Docker Registry (Optional untuk Private Images)
```bash
# Login ke Docker Hub (jika menggunakan private registry)
docker login

# Atau setup Docker registry lokal (optional)
docker run -d -p 5000:5000 --restart=always --name registry registry:2
```

### 3. Persiapan Direktori Project
```bash
# Buat direktori aplikasi di home directory user domain
sudo mkdir -p /home/bizmark/domains/bizmark.id/docker-app
sudo chown -R bizmark:bizmark /home/bizmark/domains/bizmark.id/docker-app

# Atau gunakan direktori di /opt jika lebih disukai
sudo mkdir -p /opt/bizmark-app
sudo chown -R $USER:$USER /opt/bizmark-app

# Buat direktori untuk data persisten
sudo mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
sudo chown -R $USER:$USER /var/lib/bizmark
```

### 4. Setup Virtualmin Domain
1. **Buat Virtual Server di Virtualmin:**
   - Login ke Webmin/Virtualmin
   - Create Virtual Server untuk `bizmark.id`
   - Enable SSL jika diperlukan

2. **Setup Subdomain untuk API:**
   - Buat subdomain `api.bizmark.id` 
   - Atau gunakan fitur "Sub-servers" di Virtualmin

## Setup GitHub Actions untuk Docker

### 1. Buat SSH Key untuk Deployment
Pada mesin lokal:
```bash
ssh-keygen -t ed25519 -C "github-actions-docker-deploy" -f ~/.ssh/bizmark_docker_key
```

### 2. Tambahkan Public Key ke VPS
```bash
# Salin public key ke VPS
ssh-copy-id -i ~/.ssh/bizmark_docker_key.pub user@your-vps-ip
```

### 3. Konfigurasi GitHub Secrets
Di GitHub repository, tambahkan secrets berikut:

| Name | Value | Deskripsi |
|------|-------|-----------|
| VPS_HOST | IP address VPS | IP atau domain VPS |
| VPS_USERNAME | Username SSH | User untuk SSH ke VPS |
| VPS_SSH_KEY | Private SSH key | Isi file ~/.ssh/bizmark_docker_key |
| VPS_PORT | 22 | Port SSH VPS |
| DOCKER_HUB_USERNAME | Docker Hub username | Untuk push image (optional) |
| DOCKER_HUB_PASSWORD | Docker Hub password | Untuk push image (optional) |

## Konfigurasi Docker

### 1. Docker Compose untuk Production (Virtualmin Setup)
File `docker-compose.prod.yml` akan digunakan untuk production:

```yaml
version: '3.8'

services:
  frontend:
    image: bizmark-frontend:latest
    container_name: bizmark-frontend
    restart: unless-stopped
    ports:
      - "3000:3000"  # Frontend accessible at localhost:3000
    environment:
      - NODE_ENV=production
      - NEXT_PUBLIC_API_URL=https://api.bizmark.id
    networks:
      - bizmark-network

  backend:
    image: bizmark-backend:latest
    container_name: bizmark-backend
    restart: unless-stopped
    ports:
      - "3001:3000"  # Backend accessible at localhost:3001
    environment:
      - NODE_ENV=production
      - DATABASE_URL=postgresql://bizmark_user:${DB_PASSWORD}@postgres:5432/bizmark_production
      - REDIS_URL=redis://redis:6379
      - JWT_SECRET=${JWT_SECRET}
      - JWT_REFRESH_SECRET=${JWT_REFRESH_SECRET}
      - SESSION_SECRET=${SESSION_SECRET}
      - CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id
    depends_on:
      - postgres
      - redis
    volumes:
      - /var/lib/bizmark/uploads:/app/uploads
    networks:
      - bizmark-network

  postgres:
    image: postgres:15-alpine
    container_name: bizmark-postgres
    restart: unless-stopped
    ports:
      - "127.0.0.1:5432:5432"  # Only accessible from localhost
    environment:
      - POSTGRES_DB=bizmark_production
      - POSTGRES_USER=bizmark_user
      - POSTGRES_PASSWORD=${DB_PASSWORD}
    volumes:
      - /var/lib/bizmark/postgres:/var/lib/postgresql/data
    networks:
      - bizmark-network

  redis:
    image: redis:7-alpine
    container_name: bizmark-redis
    restart: unless-stopped
    ports:
      - "127.0.0.1:6379:6379"  # Only accessible from localhost
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}
    volumes:
      - /var/lib/bizmark/redis:/data
    networks:
      - bizmark-network

networks:
  bizmark-network:
    driver: bridge

volumes:
  postgres-data:
  redis-data:
```

### 2. Konfigurasi Reverse Proxy di Virtualmin

#### A. Setup untuk Domain Utama (bizmark.id)
1. **Buka Virtualmin → Server Configuration → Website Options**
2. **Atau edit langsung file Apache/Nginx config:**

**Untuk Apache (jika menggunakan Apache):**
```apache
# File: /etc/apache2/sites-available/bizmark.id.conf
<VirtualHost *:80>
    ServerName bizmark.id
    ServerAlias www.bizmark.id
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName bizmark.id
    ServerAlias www.bizmark.id
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/bizmark.id/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/bizmark.id/privkey.pem
    
    # Proxy to Frontend (Next.js)
    ProxyPreserveHost On
    ProxyPass / http://127.0.0.1:3000/
    ProxyPassReverse / http://127.0.0.1:3000/
    
    # WebSocket support for Next.js
    RewriteEngine On
    RewriteCond %{HTTP:Upgrade} websocket [NC]
    RewriteCond %{HTTP:Connection} upgrade [NC]
    RewriteRule ^/?(.*) "ws://127.0.0.1:3000/$1" [P,L]
</VirtualHost>
```

#### B. Setup untuk API Subdomain (api.bizmark.id)
```apache
# File: /etc/apache2/sites-available/api.bizmark.id.conf
<VirtualHost *:80>
    ServerName api.bizmark.id
    
    # Redirect to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</VirtualHost>

<VirtualHost *:443>
    ServerName api.bizmark.id
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/bizmark.id/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/bizmark.id/privkey.pem
    
    # Proxy to Backend (NestJS)
    ProxyPreserveHost On
    ProxyPass / http://127.0.0.1:3001/
    ProxyPassReverse / http://127.0.0.1:3001/
    
    # Headers untuk API
    ProxyPassReverse / http://127.0.0.1:3001/
    ProxySet X-Forwarded-Proto "https"
    ProxySet X-Forwarded-For %{REMOTE_ADDR}s
</VirtualHost>
```

**Untuk Nginx (jika menggunakan Nginx):**
```nginx
# File: /etc/nginx/sites-available/bizmark.id
server {
    listen 80;
    server_name bizmark.id www.bizmark.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name bizmark.id www.bizmark.id;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/bizmark.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bizmark.id/privkey.pem;
    
    # Proxy to Frontend
    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}

# API Subdomain
server {
    listen 80;
    server_name api.bizmark.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.bizmark.id;
    
    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/bizmark.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bizmark.id/privkey.pem;
    
    # Proxy to Backend API
    location / {
        proxy_pass http://127.0.0.1:3001;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 3. Environment Variables untuk Production
File `.env.production`:
```bash
# Database
DB_PASSWORD=your_strong_db_password_here
REDIS_PASSWORD=your_strong_redis_password_here

# Application
NODE_ENV=production
JWT_SECRET=your_jwt_secret_here
JWT_REFRESH_SECRET=your_jwt_refresh_secret_here
SESSION_SECRET=your_session_secret_here

# CORS - include all your domains
CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id

# API URL for frontend
NEXT_PUBLIC_API_URL=https://api.bizmark.id
```

## Deployment Otomatis

### 1. GitHub Actions Workflow untuk Docker (Virtualmin Setup)
File `.github/workflows/docker-deploy.yml`:

```yaml
name: Docker Deploy to VPS with Virtualmin

on:
  push:
    branches: [ main ]
  workflow_dispatch:

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v4
        
      - name: Build Frontend Docker Image
        run: |
          docker build -t bizmark-frontend:latest -f frontend/Dockerfile frontend/
          
      - name: Build Backend Docker Image
        run: |
          docker build -t bizmark-backend:latest -f backend/Dockerfile backend/
          
      - name: Deploy to VPS
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USERNAME }}
          key: ${{ secrets.VPS_SSH_KEY }}
          port: ${{ secrets.VPS_PORT }}
          script: |
            cd /opt/bizmark-app
            
            # Pull latest code
            git pull origin main
            
            # Stop existing containers
            docker-compose -f docker-compose.prod.yml down
            
            # Build new images
            docker build -t bizmark-frontend:latest -f frontend/Dockerfile frontend/
            docker build -t bizmark-backend:latest -f backend/Dockerfile backend/
            
            # Start containers
            docker-compose -f docker-compose.prod.yml up -d
            
            # Wait for services to be ready
            echo "Waiting for services to start..."
            sleep 30
            
            # Verify deployment
            echo "=== Container Status ==="
            docker ps --filter "name=bizmark" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
            
            # Test services
            echo "=== Testing Services ==="
            curl -f http://localhost:3000 || echo "Frontend health check failed"
            curl -f http://localhost:3001/api/v1/health || echo "Backend health check failed"
            
            # Clean up unused images
            docker image prune -f
            
            # Restart web server to ensure proxy configs are loaded
            systemctl reload apache2 || systemctl reload nginx
            
            # Log successful deployment
            echo "Docker deployment completed successfully at $(date)" >> /var/log/bizmark-deployments.log
```

### 2. Setup Script untuk Virtualmin Environment
File `setup-virtualmin-docker.sh`:

```bash
#!/bin/bash
# Setup script untuk Virtualmin + Docker deployment

set -e

echo "Setting up Bizmark.id with Virtualmin + Docker..."

# Buat direktori aplikasi
sudo mkdir -p /opt/bizmark-app
sudo chown -R $USER:$USER /opt/bizmark-app

# Clone repository
if [ ! -d "/opt/bizmark-app/.git" ]; then
    git clone https://github.com/tanerizawa/bizmark.id.git /opt/bizmark-app
else
    cd /opt/bizmark-app && git pull origin main
fi

cd /opt/bizmark-app

# Setup environment variables
if [ ! -f ".env.production" ]; then
    # Generate strong passwords
    DB_PASSWORD=$(openssl rand -base64 32)
    REDIS_PASSWORD=$(openssl rand -base64 32)
    JWT_SECRET=$(openssl rand -base64 32)
    JWT_REFRESH_SECRET=$(openssl rand -base64 32)
    SESSION_SECRET=$(openssl rand -base64 32)
    
    # Create production environment file
    cat > .env.production << EOF
DB_PASSWORD=$DB_PASSWORD
REDIS_PASSWORD=$REDIS_PASSWORD
JWT_SECRET=$JWT_SECRET
JWT_REFRESH_SECRET=$JWT_REFRESH_SECRET
SESSION_SECRET=$SESSION_SECRET
NODE_ENV=production
CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id
NEXT_PUBLIC_API_URL=https://api.bizmark.id
EOF
    
    echo "Generated production environment file"
fi

# Start Docker containers
docker-compose -f docker-compose.prod.yml up -d

echo "Setup complete!"
echo "Next steps:"
echo "1. Configure reverse proxy in Virtualmin"
echo "2. Setup SSL certificates for bizmark.id and api.bizmark.id"
echo "3. Test the deployment"
```

### 3. Langkah-langkah Setup Manual di Virtualmin

#### A. Membuat Virtual Server
1. **Login ke Virtualmin**
2. **Create Virtual Server:**
   - Domain name: `bizmark.id`
   - Enable SSL: Yes
   - Create associated DNS records: Yes

#### B. Setup Subdomain untuk API
1. **Create Sub-Server:**
   - Sub-domain: `api`
   - Under domain: `bizmark.id`
   - Final domain: `api.bizmark.id`

#### C. Konfigurasi Reverse Proxy
1. **Untuk domain utama (bizmark.id):**
   - Go to: `Server Configuration` → `Website Options`
   - Atau edit: `/etc/apache2/sites-available/bizmark.id.conf`
   - Tambahkan konfigurasi proxy ke `localhost:3000`

2. **Untuk API subdomain (api.bizmark.id):**
   - Go to: `Server Configuration` → `Website Options`
   - Atau edit: `/etc/apache2/sites-available/api.bizmark.id.conf`
   - Tambahkan konfigurasi proxy ke `localhost:3001`

#### D. Setup SSL Certificate
```bash
# Install Certbot jika belum ada
sudo apt install certbot python3-certbot-apache

# Generate SSL untuk domain utama dan subdomain
sudo certbot --apache -d bizmark.id -d www.bizmark.id -d api.bizmark.id

# Test auto-renewal
sudo certbot renew --dry-run
```

## Monitoring dan Scaling

### Monitoring dengan Docker

```bash
# Melihat status semua container
docker ps

# Melihat logs aplikasi
docker logs bizmark-api -f

# Melihat logs database
docker logs bizmark-postgres -f

# Melihat resource usage
docker stats

# Monitoring dengan docker-compose
docker-compose -f docker-compose.prod.yml logs -f
```

### Health Checks dan Auto-restart

Tambahkan health check ke `docker-compose.prod.yml`:

```yaml
services:
  app:
    # ... existing config ...
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:3000/api/v1/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s
```

### Scaling Horizontal (Future)

```bash
# Scale aplikasi ke multiple instances
docker-compose -f docker-compose.prod.yml up -d --scale app=3

# Dengan load balancer (tambahkan ke docker-compose)
services:
  nginx-lb:
    image: nginx:alpine
    volumes:
      - ./nginx-lb.conf:/etc/nginx/nginx.conf:ro
    ports:
      - "80:80"
    depends_on:
      - app
```

## Troubleshooting

### Masalah Container

```bash
# Cek status semua container
docker ps -a

# Cek logs container yang bermasalah
docker logs bizmark-api --tail 100

# Restart container tertentu
docker restart bizmark-api

# Restart semua services
docker-compose -f docker-compose.prod.yml restart
```

### Masalah Database Connection

```bash
# Cek koneksi ke database dari container app
docker exec -it bizmark-api sh
# Dalam container:
npm run db:test-connection

# Cek logs PostgreSQL
docker logs bizmark-postgres -f

# Akses PostgreSQL secara langsung
docker exec -it bizmark-postgres psql -U bizmark_user -d bizmark_production
```

### Masalah Redis

```bash
# Test koneksi Redis
docker exec -it bizmark-redis redis-cli
# Dalam Redis CLI:
AUTH your_redis_password
PING

# Cek logs Redis
docker logs bizmark-redis -f
```

### Masalah Network

```bash
# Cek Docker networks
docker network ls

# Inspect network untuk troubleshooting
docker network inspect bizmark-network

# Test konektivitas antar container
docker exec -it bizmark-api ping postgres
docker exec -it bizmark-api ping redis
```

### Masalah Storage/Volume

```bash
# Cek penggunaan disk
df -h

# Cek volumes Docker
docker volume ls

# Inspect volume tertentu
docker volume inspect bizmark_postgres-data

# Backup data (sebelum troubleshooting)
docker exec bizmark-postgres pg_dump -U bizmark_user bizmark_production > backup.sql
```

### Performance Issues

```bash
# Monitor resource usage real-time
docker stats --no-stream

# Cek penggunaan memory per container
docker exec bizmark-api cat /proc/meminfo

# Cek process dalam container
docker exec bizmark-api ps aux

# Cleanup untuk free up space
docker system prune -f
docker image prune -f
docker volume prune -f
```

### SSL/HTTPS Setup dengan Let's Encrypt

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d bizmark.id -d www.bizmark.id

# Auto-renewal test
sudo certbot renew --dry-run

# Update nginx.conf untuk HTTPS
# Tambahkan redirect HTTP ke HTTPS
```

## Keamanan Docker Production

### 1. Non-root User dalam Container
```dockerfile
# Dalam Dockerfile
RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nestjs
USER nestjs
```

### 2. Secret Management
```bash
# Gunakan Docker secrets (untuk Docker Swarm)
# Atau gunakan external secret management
# Jangan hardcode secrets dalam image
```

### 3. Resource Limits
```yaml
# Dalam docker-compose.prod.yml
services:
  app:
    deploy:
      resources:
        limits:
          cpus: '1.0'
          memory: 1G
        reservations:
          cpus: '0.5'
          memory: 512M
```
