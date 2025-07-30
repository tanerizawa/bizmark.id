# 📋 VPS Deployment Checklist

## 🔍 Persiapan
- [ ] VPS dengan Ubuntu 20.04 LTS atau lebih baru
- [ ] Domain yang sudah terdaftar dan mengarah ke IP VPS
- [ ] SSH key untuk akses VPS
- [ ] GitHub repository dengan akses push

## 🛠️ Setup Server
- [ ] Update dan upgrade sistem operasi
  ```bash
  sudo apt update && sudo apt upgrade -y
  ```

- [ ] Install Node.js
  ```bash
  curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
  sudo apt install -y nodejs
  ```

- [ ] Install PostgreSQL
  ```bash
  sudo apt install -y postgresql postgresql-contrib
  ```

- [ ] Install Redis
  ```bash
  sudo apt install -y redis-server
  ```

- [ ] Install Nginx
  ```bash
  sudo apt install -y nginx
  ```

- [ ] Install PM2
  ```bash
  sudo npm install -g pm2
  ```

## 🗄️ Setup Database
- [ ] Buat database user dan database
  ```bash
  sudo -u postgres psql
  CREATE USER bizmark_user WITH PASSWORD 'your_strong_password';
  CREATE DATABASE bizmark_production;
  GRANT ALL PRIVILEGES ON DATABASE bizmark_production TO bizmark_user;
  \q
  ```

## 🚀 Setup GitHub Actions
- [ ] Tambahkan secrets ke GitHub repository:
  - [ ] VPS_HOST
  - [ ] VPS_USERNAME
  - [ ] VPS_SSH_KEY
  - [ ] VPS_PORT

## 📁 Setup Direktori Project
- [ ] Buat direktori aplikasi
  ```bash
  sudo mkdir -p /var/www/bizmark.id
  sudo chown -R $USER:$USER /var/www/bizmark.id
  ```

- [ ] Buat direktori untuk backup
  ```bash
  sudo mkdir -p /var/backups/bizmark
  sudo chown -R $USER:$USER /var/backups/bizmark
  ```

## 🔒 Setup Keamanan
- [ ] Konfigurasi firewall
  ```bash
  sudo ufw allow OpenSSH
  sudo ufw allow 'Nginx Full'
  sudo ufw enable
  ```

- [ ] Aktifkan HTTPS dengan Certbot
  ```bash
  sudo apt install -y certbot python3-certbot-nginx
  sudo certbot --nginx -d bizmark.id -d www.bizmark.id
  ```

## 🌐 Setup Nginx
- [ ] Buat konfigurasi Nginx
  ```bash
  sudo nano /etc/nginx/sites-available/bizmark.id
  ```
  
- [ ] Isi dengan konfigurasi berikut:
  ```nginx
  server {
      listen 80;
      server_name bizmark.id www.bizmark.id;
  
      location / {
          proxy_pass http://localhost:3000;
          proxy_http_version 1.1;
          proxy_set_header Upgrade $http_upgrade;
          proxy_set_header Connection 'upgrade';
          proxy_set_header Host $host;
          proxy_cache_bypass $http_upgrade;
      }
  }
  ```

- [ ] Aktifkan konfigurasi
  ```bash
  sudo ln -s /etc/nginx/sites-available/bizmark.id /etc/nginx/sites-enabled/
  sudo nginx -t
  sudo systemctl reload nginx
  ```

## 🚀 Deployment Pertama
- [ ] Clone repository
  ```bash
  git clone https://github.com/tanerizawa/bizmark.id.git /var/www/bizmark.id
  ```

- [ ] Jalankan script deployment
  ```bash
  cd /var/www/bizmark.id
  chmod +x deploy-to-vps.sh
  ./deploy-to-vps.sh
  ```

## ✅ Verifikasi Deployment
- [ ] Cek status aplikasi
  ```bash
  pm2 status bizmark-api
  ```

- [ ] Cek logs aplikasi
  ```bash
  pm2 logs bizmark-api
  ```

- [ ] Tes API endpoint
  ```bash
  curl http://localhost:3000/api/v1/health
  ```

- [ ] Tes website di browser: https://bizmark.id

## 🔄 Setup Auto-Restart
- [ ] Setup PM2 untuk startup otomatis
  ```bash
  pm2 startup
  pm2 save
  ```

- [ ] ATAU setup sebagai Systemd service
  ```bash
  sudo cp /var/www/bizmark.id/bizmark-api.service /etc/systemd/system/
  sudo systemctl enable bizmark-api
  sudo systemctl start bizmark-api
  ```

## 🔍 Monitoring Setup
- [ ] Setup basic monitoring
  ```bash
  pm2 install pm2-server-monit
  ```

- [ ] Lihat dashboard monitoring
  ```bash
  pm2 monit
  ```

## 🛡️ Security Checklist
- [ ] Disable root login via SSH
- [ ] Implementasi fail2ban untuk SSH
- [ ] Aktifkan automatic security updates
- [ ] Setup backup database rutin

## 🔄 Konfigurasi CI/CD
- [ ] Test GitHub Actions workflow
  ```bash
  git commit --allow-empty -m "Test CI/CD pipeline"
  git push origin main
  ```

- [ ] Verifikasi deployment otomatis berhasil
