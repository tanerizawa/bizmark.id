# 🚀 Manual VPS Setup Guide

Karena Anda sudah login ke VPS sebagai root, ikuti langkah-langkah berikut:

## 1. Update Sistem
```bash
apt update && apt upgrade -y
```

## 2. Install Docker
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh
```

## 3. Install Docker Compose
```bash
curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose
```

## 4. Install Tools yang Diperlukan
```bash
apt install -y git curl wget nginx certbot python3-certbot-nginx htop ufw
```

## 5. Setup Firewall
```bash
ufw allow ssh
ufw allow 80
ufw allow 443
ufw --force enable
```

## 6. Buat Direktori Aplikasi
```bash
mkdir -p /opt/bizmark-app
mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
```

## 7. Clone Repository
```bash
cd /opt/bizmark-app
git clone https://github.com/tanerizawa/bizmark.id.git .
```

## 8. Verifikasi Instalasi
```bash
docker --version
docker-compose --version
nginx -v
```

## 9. Setup SSH Key untuk GitHub Actions
Copy public key dari lokal ke VPS:

**Di komputer lokal, jalankan:**
```bash
cat ~/.ssh/bizmark_deploy_key.pub
```

**Di VPS, jalankan:**
```bash
mkdir -p ~/.ssh
echo "ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIF0lYJE4todkUV+X6U2JM3Hb7Fhz6EGIww7JCcujYPB6 github-actions-deploy" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
```

## 10. Test SSH Key Authentication
**Di komputer lokal:**
```bash
ssh -i ~/.ssh/bizmark_deploy_key root@31.97.109.198 "whoami"
```

## 11. Setup GitHub Secrets
- Repository: https://github.com/tanerizawa/bizmark.id/settings/secrets/actions
- Add secrets:
  - **VPS_HOST**: `31.97.109.198`  
  - **VPS_USERNAME**: `root`
  - **VPS_SSH_KEY**: (paste private key dari `cat ~/.ssh/bizmark_deploy_key`)
  - **VPS_PORT**: `22`

## 12. Test Manual Deployment
**Di komputer lokal:**
```bash
./deploy-helper.sh deploy root@31.97.109.198
```

## 13. Trigger Auto Deployment
```bash
git push origin main
```

Setelah ini, setiap push ke branch main akan otomatis deploy ke VPS!
