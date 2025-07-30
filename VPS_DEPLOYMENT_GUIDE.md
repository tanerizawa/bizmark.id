# 🚀 Panduan Deployment ke VPS Ubuntu

## Daftar Isi
1. [Persyaratan VPS](#persyaratan-vps)
2. [Setup GitHub Actions](#setup-github-actions)
3. [Setup VPS](#setup-vps)
4. [Deployment Manual](#deployment-manual)
5. [Monitoring dan Scaling](#monitoring-dan-scaling)
6. [Troubleshooting](#troubleshooting)

## Persyaratan VPS

### Spesifikasi Minimum
- Ubuntu 20.04 LTS atau lebih baru
- 2GB RAM
- 2 vCPU
- 30GB SSD

### Software Prasyarat
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Node.js 18.x
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install PostgreSQL 15
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt $(lsb_release -cs)-pgdg main" > /etc/apt/sources.list.d/pgdg.list'
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
sudo apt-get update
sudo apt-get -y install postgresql-15

# Install Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Install Nginx
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx

# Install PM2 for Node.js process management
sudo npm install -g pm2
```

## Setup GitHub Actions

### 1. Buat SSH Key untuk Deployment
Pada mesin lokal:
```bash
ssh-keygen -t ed25519 -C "github-actions-bizmark" -f ~/.ssh/bizmark_deploy_key
```

### 2. Tambahkan Public Key ke VPS
Pada mesin lokal:
```bash
# Salin public key ke VPS
ssh-copy-id -i ~/.ssh/bizmark_deploy_key.pub user@your-vps-ip
```

### 3. Tambahkan Secret ke GitHub Repository
1. Buka repositori GitHub
2. Pilih Settings > Secrets and Variables > Actions
3. Tambahkan secrets berikut:

| Name | Value |
|------|-------|
| VPS_HOST | IP address VPS |
| VPS_USERNAME | Username SSH |
| VPS_SSH_KEY | Isi dari private key (`cat ~/.ssh/bizmark_deploy_key`) |
| VPS_PORT | 22 (atau port SSH yang dikonfigurasi) |

## Setup VPS

### 1. Setup PostgreSQL

```bash
# Login ke postgres user
sudo -i -u postgres

# Buat user dan database
psql -c "CREATE USER bizmark_user WITH ENCRYPTED PASSWORD 'strong_password_here';"
psql -c "CREATE DATABASE bizmark_production;"
psql -c "GRANT ALL PRIVILEGES ON DATABASE bizmark_production TO bizmark_user;"

# Aktifkan ekstensi yang dibutuhkan
psql -d bizmark_production -c "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\";"
psql -c "ALTER USER bizmark_user WITH SUPERUSER;"

# Keluar dari postgres user
exit
```

### 2. Setup Redis Security

```bash
# Edit konfigurasi Redis
sudo nano /etc/redis/redis.conf

# Tambahkan/ubah konfigurasi berikut:
# requirepass StrongRedisPasswordHere
# bind 127.0.0.1

# Restart Redis
sudo systemctl restart redis-server
```

### 3. Setup Firewall

```bash
# Setup UFW firewall
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 4. Persiapan Direktori Project

```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/bizmark.id
sudo chown -R $USER:$USER /var/www/bizmark.id

# Buat direktori backup
sudo mkdir -p /var/backups/bizmark
sudo chown -R $USER:$USER /var/backups/bizmark
```

## Deployment Manual

Untuk deployment manual (tanpa GitHub Actions):

```bash
# Clone repository
git clone https://github.com/tanerizawa/bizmark.id.git /var/www/bizmark.id

# Setup dan deployment
cd /var/www/bizmark.id
chmod +x deploy-to-vps.sh
sudo ./deploy-to-vps.sh
```

## Monitoring dan Scaling

### Monitoring dengan PM2

```bash
# Melihat status aplikasi
pm2 status

# Melihat logs
pm2 logs bizmark-api

# Monitoring real-time
pm2 monit
```

### Setup Ketersediaan Tinggi (Future)

Untuk meningkatkan ketersediaan dan skalabilitas:

1. Gunakan load balancer (Nginx atau HAProxy)
2. Setup multiple instance dengan PM2:
   ```bash
   pm2 start dist/main.js --name bizmark-api -i max
   ```
3. Pertimbangkan Redis cluster untuk caching terdistribusi

## Troubleshooting

### Masalah Database Connection

```bash
# Cek status PostgreSQL
sudo systemctl status postgresql

# Cek log PostgreSQL
sudo tail -f /var/log/postgresql/postgresql-15-main.log
```

### Masalah Aplikasi

```bash
# Cek log aplikasi
pm2 logs bizmark-api

# Restart aplikasi
pm2 restart bizmark-api
```

### Masalah Nginx

```bash
# Test konfigurasi Nginx
sudo nginx -t

# Cek log Nginx
sudo tail -f /var/log/nginx/error.log
```
