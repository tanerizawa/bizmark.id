#!/bin/bash
# VPS Setup Script untuk Bizmark.id

set -e

echo "🚀 Setting up VPS for Bizmark.id deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   echo -e "${RED}This script should not be run as root${NC}" 
   exit 1
fi

echo -e "${YELLOW}📦 Installing Docker and Docker Compose...${NC}"

# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo usermod -aG docker $USER
rm get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

echo -e "${YELLOW}📁 Creating directories...${NC}"

# Create application directory
sudo mkdir -p /opt/bizmark-app
sudo chown -R $USER:$USER /opt/bizmark-app

# Create persistent data directories
sudo mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
sudo chown -R $USER:$USER /var/lib/bizmark

echo -e "${YELLOW}🔧 Installing additional tools...${NC}"

# Install essential tools
sudo apt install -y git curl wget nginx certbot python3-certbot-nginx htop

echo -e "${YELLOW}🔒 Setting up basic firewall...${NC}"

# Setup UFW firewall
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw --force enable

echo -e "${YELLOW}📋 Cloning repository...${NC}"

# Clone repository to /opt/bizmark-app
cd /opt/bizmark-app
if [ ! -d ".git" ]; then
    git clone https://github.com/tanerizawa/bizmark.id.git .
else
    git pull origin main
fi

echo -e "${GREEN}✅ VPS setup completed successfully!${NC}"
echo ""
echo "Next steps:"
echo "1. Logout and login again to apply Docker group membership"
echo "2. Add your domain DNS A records to point to this server"
echo "3. Setup GitHub Secrets with your SSH key"
echo "4. Push to main branch to trigger deployment"
echo ""
echo "GitHub Secrets needed:"
echo "- VPS_HOST: $(curl -s ifconfig.me)"
echo "- VPS_USERNAME: $USER"
echo "- VPS_SSH_KEY: (your private SSH key)"
echo "- VPS_PORT: 22"
