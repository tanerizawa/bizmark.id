#!/bin/bash
# Script ini untuk dijalankan di VPS (31.97.109.198) sebagai root
# Updated: July 31, 2025
# Compatible with existing Webmin setup (multiple websites on port 80/443)
# Uses reverse proxy: bizmark.id -> localhost:3000, api.bizmark.id -> localhost:3001

set -e  # Exit on any error

echo "🚀 Starting Bizmark.id VPS Setup..."
echo "🔧 Webmin Multi-Domain Compatible Installation"
echo "📋 Target: bizmark.id (frontend:3000) + api.bizmark.id (backend:3001)"
echo "=============================================================="

# Update sistem
echo "📦 Updating system..."
apt update && apt upgrade -y

# Install Docker
echo "🐳 Installing Docker..."
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
rm get-docker.sh

# Install Docker Compose
echo "📦 Installing Docker Compose..."
curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
chmod +x /usr/local/bin/docker-compose

# Install tools (don't install web server - use existing Webmin setup)
echo "🔧 Installing essential tools only (avoiding web server conflicts)..."
# Skip nginx/apache installation since Webmin already manages web servers
apt install -y git curl wget htop ufw

# Note about web server
echo "ℹ️  Skipping web server installation - using existing Webmin setup"
echo "    Ports 80/443 already managed by Webmin for multiple domains"
echo "    Docker apps will use internal ports: 3000 (frontend), 3001 (backend)"

# Setup firewall (compatible with Webmin/Virtualmin)
echo "🔒 Setting up firewall..."
# Check if UFW is already configured by Webmin
if ufw status | grep -q "Status: active"; then
    echo "UFW already active, adding rules..."
    ufw allow ssh
    ufw allow 80
    ufw allow 443
    ufw allow 10000  # Webmin port
else
    ufw allow ssh
    ufw allow 80
    ufw allow 443
    ufw allow 10000  # Webmin port
    ufw --force enable
fi

# Create directories (compatible with Virtualmin structure)
echo "📁 Creating directories..."
mkdir -p /opt/bizmark-app
mkdir -p /var/lib/bizmark/{postgres,redis,uploads}

# Create symlink for easy Webmin/Virtualmin access if needed
if [ -d "/var/www/html" ]; then
    echo "Creating symlink for Webmin integration..."
    mkdir -p /var/www/bizmark-docker
    ln -sfn /opt/bizmark-app /var/www/bizmark-docker/app
    echo "Symlink created: /var/www/bizmark-docker/app -> /opt/bizmark-app"
fi

# Setup SSH key for GitHub Actions
echo "🔑 Setting up SSH key..."
mkdir -p ~/.ssh
cat > ~/.ssh/authorized_keys << 'SSH_KEY'
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIF0lYJE4todkUV+X6U2JM3Hb7Fhz6EGIww7JCcujYPB6 github-actions-deploy
SSH_KEY
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh

# Clone repository
echo "📋 Cloning repository..."
cd /opt/bizmark-app
if [ -d ".git" ]; then
    echo "Repository already exists, pulling latest changes..."
    git pull origin main
else
    git clone https://github.com/tanerizawa/bizmark.id.git .
fi

# Create environment file
echo "⚙️ Creating environment file..."
cat > .env.production << 'EOF'
# Database
DB_PASSWORD=XMQbLI5pWJ0vABjgZxXdwtbkAD5b2OKtWnq7tUGiFSc=
REDIS_PASSWORD=hxmGl01YIEvAFgpAbYrqGyIoYfc+oEms7mLTJknZNI8=

# Application
NODE_ENV=production
JWT_SECRET=qmlXRn06wJFGUlYIQOFEv82xVq4fHwpiAF7DMXoWmAI=
JWT_REFRESH_SECRET=EWMRFsvjXonEYtuc4irbLPNfQfv+OhlYwE7xSOALJ8M=
SESSION_SECRET=18EIUnhH0ClbT+B+0nycZ5q6Y6vY09sPTwXJXFBcxbk=

# CORS
CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id

# API URL
NEXT_PUBLIC_API_URL=https://api.bizmark.id
EOF

# Verify installation
echo "✅ Verifying installation..."
echo "Docker: $(docker --version 2>/dev/null || echo 'Installation may need reboot')"
echo "Docker Compose: $(docker compose version --short 2>/dev/null || echo 'Not available yet')"
echo "Git: $(git --version)"

# Note about web server (don't test nginx/apache as we're not managing them)
echo "Web Server: Managed by existing Webmin setup"

# Test Docker Compose configuration
echo "🧪 Testing Docker Compose configuration..."
if docker compose -f docker-compose.prod.yml config > /dev/null 2>&1; then
    echo "✅ Docker Compose configuration is valid"
else
    echo "⚠️  Docker Compose configuration has warnings (normal if images don't exist yet)"
fi

echo "🎉 VPS Setup completed!"
echo ""
echo "📋 Setup Summary:"
echo "- VPS IP: 31.97.109.198"
echo "- User: root"
echo "- SSH Key: Configured for GitHub Actions"
echo "- Docker: $(docker --version 2>/dev/null || echo 'Installation may need reboot')"
echo "- Application: /opt/bizmark-app"
echo "- Web Server: Existing Webmin setup (ports 80/443 managed)"
echo ""
echo "🔧 Webmin Integration Required:"
echo "⚠️  IMPORTANT: Configure these domains in Webmin:"
echo ""
echo "1. Domain: bizmark.id"
echo "   - Document Root: /var/www/bizmark-docker/app (or any path)"
echo "   - Reverse Proxy: Enable"
echo "   - Proxy Target: http://127.0.0.1:3000"
echo "   - SSL: Enable (recommended)"
echo ""
echo "2. Subdomain: api.bizmark.id"
echo "   - Document Root: /var/www/bizmark-docker/app (or any path)"
echo "   - Reverse Proxy: Enable" 
echo "   - Proxy Target: http://127.0.0.1:3001"
echo "   - SSL: Enable (recommended)"
echo ""
echo "🎯 Next Steps:"
echo "1. Configure GitHub Secrets at:"
echo "   https://github.com/tanerizawa/bizmark.id/settings/secrets/actions"
echo ""
echo "   Required secrets:"
echo "   - VPS_HOST: 31.97.109.198"
echo "   - VPS_USERNAME: root"
echo "   - VPS_SSH_KEY: (private key from local machine)"
echo "   - VPS_PORT: 22"
echo ""
echo "2. Setup domains in Webmin (see instructions above)"
echo ""
echo "3. Push to main branch to trigger auto-deployment"
echo ""
echo "🚀 Ready for deployment!"
