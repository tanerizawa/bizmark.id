# 🚀 GitHub Actions Deployment ke VPS

## Daftar Isi
1. [Setup VPS](#setup-vps)
2. [Konfigurasi GitHub Actions](#konfigurasi-github-actions)
3. [Docker Configuration](#docker-configuration)
4. [Deployment Process](#deployment-process)
5. [Troubleshooting](#troubleshooting)

## Setup VPS

### Persyaratan Minimum
- Ubuntu 20.04 LTS atau lebih baru
- 4GB RAM
- 2 vCPU
- 50GB SSD
- Docker & Docker Compose terinstall

### Quick Setup Script
```bash
# Setup Docker dan dependencies
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Persiapan direktori
sudo mkdir -p /opt/bizmark-app
sudo chown -R $USER:$USER /opt/bizmark-app
sudo mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
sudo chown -R $USER:$USER /var/lib/bizmark
```

## Konfigurasi GitHub Actions

### 1. Setup SSH Key
```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/bizmark_deploy_key

# Copy public key ke VPS
ssh-copy-id -i ~/.ssh/bizmark_deploy_key.pub user@your-vps-ip
```

### 2. GitHub Secrets
Tambahkan di GitHub Repository → Settings → Secrets:

| Name | Value | Deskripsi |
|------|-------|-----------|
| VPS_HOST | IP/domain VPS | Server target |
| VPS_USERNAME | Username SSH | User untuk SSH |
| VPS_SSH_KEY | Private SSH key | Isi file ~/.ssh/bizmark_deploy_key |
| VPS_PORT | 22 | Port SSH |

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

### 2. Environment Variables
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

# CORS
CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id

# API URL
NEXT_PUBLIC_API_URL=https://api.bizmark.id
```

### 3. Reverse Proxy (Nginx)
```nginx
# /etc/nginx/sites-available/bizmark.id
server {
    listen 80;
    server_name bizmark.id www.bizmark.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name bizmark.id www.bizmark.id;
    
    ssl_certificate /etc/letsencrypt/live/bizmark.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bizmark.id/privkey.pem;
    
    location / {
        proxy_pass http://127.0.0.1:3000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}

# API subdomain
server {
    listen 80;
    server_name api.bizmark.id;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.bizmark.id;
    
    ssl_certificate /etc/letsencrypt/live/bizmark.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/bizmark.id/privkey.pem;
    
    location / {
        proxy_pass http://127.0.0.1:3001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## Deployment Process

### 1. GitHub Actions Workflow
File `.github/workflows/deploy-vps.yml` sudah dikonfigurasi untuk:
- Build Docker images untuk frontend dan backend
- Deploy ke VPS melalui SSH
- Restart containers dengan zero-downtime
- Verifikasi health check

### 2. Manual Deployment (jika diperlukan)
```bash
# SSH ke VPS
ssh user@your-vps-ip

# Navigate ke project directory
cd /opt/bizmark-app

# Pull latest changes
git pull origin main

# Rebuild dan restart containers
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d --build

# Verify deployment
docker ps
curl http://localhost:3000
curl http://localhost:3001/api/v1/health
```

### 3. Setup SSL Certificate
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d bizmark.id -d www.bizmark.id -d api.bizmark.id

# Test auto-renewal
sudo certbot renew --dry-run
```

## Troubleshooting

### Container Issues
```bash
# Check container status
docker ps -a

# View logs
docker logs bizmark-frontend -f
docker logs bizmark-backend -f
docker logs bizmark-postgres -f

# Restart specific container
docker restart bizmark-backend

# Restart all services
docker-compose -f docker-compose.prod.yml restart
```

### Database Connection
```bash
# Test database connection
docker exec -it bizmark-postgres psql -U bizmark_user -d bizmark_production

# Check database logs
docker logs bizmark-postgres -f
```

### Redis Connection
```bash
# Test Redis connection
docker exec -it bizmark-redis redis-cli
# In Redis CLI: AUTH your_redis_password, then PING
```

### Network Issues
```bash
# Check Docker networks
docker network ls

# Test connectivity between containers
docker exec -it bizmark-backend ping postgres
docker exec -it bizmark-backend ping redis
```

### Cleanup
```bash
# Free up space
docker system prune -f
docker image prune -f
docker volume prune -f
```

### Health Check
```bash
# Verify services are running
curl http://localhost:3000
curl http://localhost:3001/api/v1/health

# Check resource usage
docker stats --no-stream
```
