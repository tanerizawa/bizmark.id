#!/bin/bash
# Docker setup script untuk VPS Ubuntu
# File: setup-docker-vps.sh

set -e # Exit jika ada error

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}====== SETUP DOCKER VPS UNTUK BIZMARK.ID ======${NC}"
echo -e "${YELLOW}Starting setup at $(date)${NC}"

# Check if running as root
if [[ $EUID -eq 0 ]]; then
   echo -e "${RED}This script should not be run as root${NC}" 
   exit 1
fi

# Update sistem
echo -e "${GREEN}Updating system packages...${NC}"
sudo apt update && sudo apt upgrade -y

# Install Docker jika belum ada
if ! command -v docker &> /dev/null; then
    echo -e "${GREEN}Installing Docker...${NC}"
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
else
    echo -e "${YELLOW}Docker already installed${NC}"
fi

# Install Docker Compose jika belum ada
if ! command -v docker-compose &> /dev/null; then
    echo -e "${GREEN}Installing Docker Compose...${NC}"
    sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    sudo chmod +x /usr/local/bin/docker-compose
else
    echo -e "${YELLOW}Docker Compose already installed${NC}"
fi

# Install Nginx untuk reverse proxy
if ! command -v nginx &> /dev/null; then
    echo -e "${GREEN}Installing Nginx...${NC}"
    sudo apt install -y nginx
    sudo systemctl enable nginx
else
    echo -e "${YELLOW}Nginx already installed${NC}"
fi

# Install tools tambahan
echo -e "${GREEN}Installing additional tools...${NC}"
sudo apt install -y curl wget git htop ufw certbot python3-certbot-nginx

# Setup firewall
echo -e "${GREEN}Setting up firewall...${NC}"
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

# Buat direktori project
echo -e "${GREEN}Creating project directories...${NC}"
sudo mkdir -p /var/www/bizmark.id
sudo chown -R $USER:$USER /var/www/bizmark.id

# Buat direktori untuk data persisten
sudo mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
sudo chown -R $USER:$USER /var/lib/bizmark

# Buat direktori untuk logs
sudo mkdir -p /var/log/bizmark
sudo chown -R $USER:$USER /var/log/bizmark

# Clone repository jika belum ada
if [ ! -d "/var/www/bizmark.id/.git" ]; then
    echo -e "${GREEN}Cloning repository...${NC}"
    git clone https://github.com/tanerizawa/bizmark.id.git /var/www/bizmark.id
else
    echo -e "${YELLOW}Repository already exists, pulling latest changes...${NC}"
    cd /var/www/bizmark.id
    git pull origin main
fi

# Setup environment file
cd /var/www/bizmark.id
if [ ! -f ".env.production" ]; then
    echo -e "${GREEN}Setting up production environment file...${NC}"
    cp .env.production .env.production.bak 2>/dev/null || true
    
    # Generate strong passwords dan secrets
    DB_PASSWORD=$(openssl rand -base64 32)
    REDIS_PASSWORD=$(openssl rand -base64 32)
    JWT_SECRET=$(openssl rand -base64 32)
    JWT_REFRESH_SECRET=$(openssl rand -base64 32)
    SESSION_SECRET=$(openssl rand -base64 32)
    
    # Update .env.production dengan generated secrets
    sed -i "s/your_super_strong_db_password_here_change_this/$DB_PASSWORD/g" .env.production
    sed -i "s/your_super_strong_redis_password_here_change_this/$REDIS_PASSWORD/g" .env.production
    sed -i "s/your_jwt_secret_generated_with_openssl_rand_base64_32/$JWT_SECRET/g" .env.production
    sed -i "s/your_jwt_refresh_secret_generated_with_openssl_rand_base64_32/$JWT_REFRESH_SECRET/g" .env.production
    sed -i "s/your_session_secret_generated_with_openssl_rand_base64_32/$SESSION_SECRET/g" .env.production
    
    echo -e "${GREEN}Generated production secrets${NC}"
else
    echo -e "${YELLOW}Production environment file already exists${NC}"
fi

# Verify Docker installation
echo -e "${GREEN}Verifying Docker installation...${NC}"
docker --version
docker-compose --version

# Test Docker dengan hello-world
echo -e "${GREEN}Testing Docker...${NC}"
docker run hello-world

echo -e "${GREEN}====== SETUP COMPLETE ======${NC}"
echo -e "${YELLOW}Setup completed at $(date)${NC}"
echo ""
echo -e "${GREEN}Next steps:${NC}"
echo "1. Logout and login again to apply Docker group membership"
echo "2. Configure your domain DNS to point to this server"
echo "3. Setup SSL certificate: sudo certbot --nginx -d yourdomain.com"
echo "4. Run initial deployment: cd /var/www/bizmark.id && docker-compose -f docker-compose.prod.yml up -d"
echo ""
echo -e "${YELLOW}Important files:${NC}"
echo "- Application: /var/www/bizmark.id"
echo "- Data: /var/lib/bizmark/"
echo "- Logs: /var/log/bizmark/"
echo "- Environment: /var/www/bizmark.id/.env.production"
