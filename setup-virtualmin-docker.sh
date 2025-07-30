#!/bin/bash
# Setup script untuk Virtualmin + Docker deployment
# File: setup-virtualmin-docker.sh

set -e # Exit jika ada error

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}====== SETUP BIZMARK.ID DENGAN VIRTUALMIN + DOCKER ======${NC}"
echo -e "${YELLOW}Starting setup at $(date)${NC}"

# Check if Virtualmin is installed
if ! command -v virtualmin &> /dev/null; then
    echo -e "${RED}Virtualmin is not installed. Please install Virtualmin first.${NC}"
    echo "Visit: https://www.virtualmin.com/download.html"
    exit 1
fi

# Check if running as correct user (not root)
if [[ $EUID -eq 0 ]]; then
   echo -e "${RED}This script should not be run as root${NC}" 
   exit 1
fi

# Install Docker jika belum ada
if ! command -v docker &> /dev/null; then
    echo -e "${GREEN}Installing Docker...${NC}"
    curl -fsSL https://get.docker.com -o get-docker.sh
    sudo sh get-docker.sh
    sudo usermod -aG docker $USER
    rm get-docker.sh
    echo -e "${YELLOW}Please logout and login again to apply Docker group membership${NC}"
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

# Buat direktori aplikasi
echo -e "${GREEN}Creating application directory...${NC}"
sudo mkdir -p /opt/bizmark-app
sudo chown -R $USER:$USER /opt/bizmark-app

# Buat direktori untuk data persisten
sudo mkdir -p /var/lib/bizmark/{postgres,redis,uploads}
sudo chown -R $USER:$USER /var/lib/bizmark

# Buat direktori untuk logs
sudo mkdir -p /var/log/bizmark
sudo chown -R $USER:$USER /var/log/bizmark

# Clone repository jika belum ada
if [ ! -d "/opt/bizmark-app/.git" ]; then
    echo -e "${GREEN}Cloning repository...${NC}"
    git clone https://github.com/tanerizawa/bizmark.id.git /opt/bizmark-app
else
    echo -e "${YELLOW}Repository already exists, pulling latest changes...${NC}"
    cd /opt/bizmark-app
    git pull origin main
fi

# Masuk ke direktori aplikasi
cd /opt/bizmark-app

# Setup environment file
if [ ! -f ".env.production" ]; then
    echo -e "${GREEN}Setting up production environment file...${NC}"
    
    # Generate strong passwords dan secrets
    DB_PASSWORD=$(openssl rand -base64 32)
    REDIS_PASSWORD=$(openssl rand -base64 32)
    JWT_SECRET=$(openssl rand -base64 32)
    JWT_REFRESH_SECRET=$(openssl rand -base64 32)
    SESSION_SECRET=$(openssl rand -base64 32)
    
    # Create production environment file
    cat > .env.production << EOF
# Database Configuration
DB_PASSWORD=$DB_PASSWORD
REDIS_PASSWORD=$REDIS_PASSWORD

# Application Secrets
JWT_SECRET=$JWT_SECRET
JWT_REFRESH_SECRET=$JWT_REFRESH_SECRET
SESSION_SECRET=$SESSION_SECRET

# Environment
NODE_ENV=production

# CORS Configuration - update with your actual domains
CORS_ORIGIN=https://bizmark.id,https://www.bizmark.id,https://api.bizmark.id

# API URL for frontend
NEXT_PUBLIC_API_URL=https://api.bizmark.id
EOF
    
    echo -e "${GREEN}Generated production environment file with secure secrets${NC}"
else
    echo -e "${YELLOW}Production environment file already exists${NC}"
fi

# Test Docker installation
echo -e "${GREEN}Testing Docker installation...${NC}"
docker --version
docker-compose --version

# Build and start containers
echo -e "${GREEN}Building and starting Docker containers...${NC}"
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for services to be ready
echo -e "${YELLOW}Waiting for services to start (60 seconds)...${NC}"
sleep 60

# Check container status
echo -e "${GREEN}Checking container status...${NC}"
docker ps --filter "name=bizmark" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

# Test services
echo -e "${GREEN}Testing services...${NC}"
echo "Frontend (port 3000):"
curl -s -o /dev/null -w "%{http_code}" http://localhost:3000 || echo "Frontend not ready yet"

echo "Backend (port 3001):"
curl -s -o /dev/null -w "%{http_code}" http://localhost:3001/api/v1/health || echo "Backend not ready yet"

echo -e "${GREEN}====== SETUP COMPLETE ======${NC}"
echo -e "${YELLOW}Setup completed at $(date)${NC}"
echo ""
echo -e "${GREEN}Next steps:${NC}"
echo "1. Create Virtual Server in Virtualmin for bizmark.id"
echo "2. Create Sub-server for api.bizmark.id"
echo "3. Configure reverse proxy in Virtualmin:"
echo "   - bizmark.id → http://127.0.0.1:3000 (Frontend)"
echo "   - api.bizmark.id → http://127.0.0.1:3001 (Backend API)"
echo "4. Setup SSL certificates with Let's Encrypt"
echo "5. Test your deployment"
echo ""
echo -e "${YELLOW}Important information:${NC}"
echo "- Application directory: /opt/bizmark-app"
echo "- Data directory: /var/lib/bizmark/"
echo "- Logs directory: /var/log/bizmark/"
echo "- Environment file: /opt/bizmark-app/.env.production"
echo ""
echo -e "${YELLOW}Container services:${NC}"
echo "- Frontend: http://localhost:3000"
echo "- Backend API: http://localhost:3001"
echo "- PostgreSQL: localhost:5432 (internal only)"
echo "- Redis: localhost:6379 (internal only)"
echo ""
echo -e "${YELLOW}Virtualmin Configuration Commands:${NC}"
echo "# Enable required Apache modules:"
echo "sudo a2enmod proxy proxy_http proxy_balancer lbmethod_byrequests"
echo "sudo systemctl restart apache2"
