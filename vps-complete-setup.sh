#!/bin/bash
# 🚀 Complete VPS Setup Script for Bizmark.id
# Run this directly in your VPS terminal

set -e

echo "=================================="
echo "🚀 Bizmark.id VPS Setup Starting..."
echo "=================================="

# Colors for better output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 1. Update sistem
print_status "Updating system packages..."
if apt update && apt upgrade -y; then
    print_success "System updated successfully"
else
    print_error "Failed to update system"
    exit 1
fi

# 2. Install Docker
print_status "Installing Docker..."
if curl -fsSL https://get.docker.com -o get-docker.sh && sh get-docker.sh; then
    rm -f get-docker.sh
    print_success "Docker installed successfully"
else
    print_error "Failed to install Docker"
    exit 1
fi

# 3. Install Docker Compose
print_status "Installing Docker Compose..."
if curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && chmod +x /usr/local/bin/docker-compose; then
    print_success "Docker Compose installed successfully"
else
    print_error "Failed to install Docker Compose"
    exit 1
fi

# 4. Install essential tools
print_status "Installing essential tools..."
if apt install -y git curl wget nginx certbot python3-certbot-nginx htop ufw unzip; then
    print_success "Essential tools installed successfully"
else
    print_error "Failed to install essential tools"
    exit 1
fi

# 5. Setup firewall
print_status "Setting up firewall..."
ufw allow ssh
ufw allow 80
ufw allow 443
echo "y" | ufw enable
print_success "Firewall configured successfully"

# 6. Create directories
print_status "Creating application directories..."
mkdir -p /opt/bizmark-app
mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
chown -R root:root /opt/bizmark-app
chown -R root:root /var/lib/bizmark
print_success "Directories created successfully"

# 7. Setup SSH key for GitHub Actions
print_status "Setting up SSH key for GitHub Actions..."
mkdir -p ~/.ssh
cat > ~/.ssh/authorized_keys << 'EOF'
ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIF0lYJE4todkUV+X6U2JM3Hb7Fhz6EGIww7JCcujYPB6 github-actions-deploy
EOF
chmod 600 ~/.ssh/authorized_keys
chmod 700 ~/.ssh
print_success "SSH key configured successfully"

# 8. Clone repository
print_status "Cloning repository..."
cd /opt/bizmark-app
if [ -d ".git" ]; then
    print_warning "Repository already exists, pulling latest changes..."
    git pull origin main
else
    if git clone https://github.com/tanerizawa/bizmark.id.git .; then
        print_success "Repository cloned successfully"
    else
        print_error "Failed to clone repository"
        exit 1
    fi
fi

# 9. Setup environment file
print_status "Setting up environment file..."
if [ -f ".env.production" ]; then
    print_success "Environment file already exists"
else
    print_warning "Environment file not found, creating template..."
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
    print_success "Environment file created"
fi

# 10. Test Docker installation
print_status "Testing Docker installation..."
if docker --version && docker compose version; then
    print_success "Docker is working correctly"
else
    print_error "Docker installation failed"
    exit 1
fi

# 11. Test Docker Compose configuration
print_status "Testing Docker Compose configuration..."
if docker compose -f docker-compose.prod.yml config > /dev/null 2>&1; then
    print_success "Docker Compose configuration is valid"
else
    print_warning "Docker Compose configuration has issues (might be normal if images don't exist yet)"
fi

# 12. Verify installation
echo ""
echo "=================================="
echo "✅ VPS Setup Completed Successfully!"
echo "=================================="
echo ""
echo "📋 Installation Summary:"
echo "- Docker version: $(docker --version)"
echo "- Docker Compose version: $(docker compose version --short)"
echo "- Nginx version: $(nginx -v 2>&1)"
echo "- Git version: $(git --version)"
echo ""
echo "📁 Directory Structure:"
echo "- Application: /opt/bizmark-app"
echo "- Data: /var/lib/bizmark/"
echo ""
echo "🔑 SSH Key configured for GitHub Actions"
echo ""
echo "🎯 Next Steps:"
echo "1. Configure GitHub Secrets:"
echo "   - VPS_HOST: $(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')"
echo "   - VPS_USERNAME: root"
echo "   - VPS_SSH_KEY: (your private SSH key from local machine)"
echo "   - VPS_PORT: 22"
echo ""
echo "2. Go to: https://github.com/tanerizawa/bizmark.id/settings/secrets/actions"
echo ""
echo "3. Test deployment by pushing to main branch"
echo ""
echo "🚀 Ready for deployment!"
