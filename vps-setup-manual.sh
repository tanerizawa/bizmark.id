#!/bin/bash
# VPS Setup Commands untuk Bizmark.id
# Copy dan paste command ini satu per satu di VPS

echo "🚀 Starting VPS Setup for Bizmark.id..."

# 1. Update sistem
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# 2. Install Docker
echo "🐳 Installing Docker..."
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh

# 3. Install Docker Compose
echo "📦 Installing Docker Compose..."
curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# 4. Install essential tools
echo "🔧 Installing essential tools..."
apt install -y git curl wget nginx certbot python3-certbot-nginx htop ufw

# 5. Setup firewall
echo "🔒 Setting up firewall..."
ufw allow ssh
ufw allow 80
ufw allow 443
ufw --force enable

# 6. Create directories
echo "📁 Creating application directories..."
mkdir -p /opt/bizmark-app
mkdir -p /var/lib/bizmark/{postgres,redis,uploads}

# 7. Clone repository
echo "📋 Cloning repository..."
cd /opt/bizmark-app
git clone https://github.com/tanerizawa/bizmark.id.git .

# 8. Verify installation
echo "✅ Verifying installation..."
docker --version
docker-compose --version
nginx -v

echo "🎉 VPS Setup completed!"
echo ""
echo "Next steps:"
echo "1. Configure GitHub Secrets with:"
echo "   - VPS_HOST: $(curl -s ifconfig.me 2>/dev/null || echo '31.97.109.198')"
echo "   - VPS_USERNAME: root"
echo "   - VPS_SSH_KEY: (your private SSH key)"
echo "   - VPS_PORT: 22"
echo ""
echo "2. Setup SSH key authentication"
echo "3. Push to main branch to trigger deployment"
