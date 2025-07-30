#!/bin/bash
# Deploy script untuk Ubuntu VPS
# File: deploy-to-vps.sh

set -e # Exit jika ada error

# Warna untuk output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}====== DEPLOYMENT BIZMARK.ID KE VPS UBUNTU ======${NC}"
echo -e "${YELLOW}Starting deployment at $(date)${NC}"

# Direktori project
PROJECT_DIR="/var/www/bizmark.id"
BACKUP_DIR="/var/backups/bizmark"

# Cek apakah direktori ada
if [ ! -d "$PROJECT_DIR" ]; then
  echo -e "${RED}Project directory $PROJECT_DIR does not exist.${NC}"
  echo -e "${YELLOW}Creating directory...${NC}"
  mkdir -p $PROJECT_DIR
  # Initial clone jika direktori tidak ada
  git clone https://github.com/tanerizawa/bizmark.id.git $PROJECT_DIR
fi

# Cek apakah direktori backup ada
if [ ! -d "$BACKUP_DIR" ]; then
  echo -e "${YELLOW}Creating backup directory...${NC}"
  mkdir -p $BACKUP_DIR
fi

# Masuk ke direktori project
cd $PROJECT_DIR

# Backup konfigurasi dan data penting
echo -e "${GREEN}Backing up configuration...${NC}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
mkdir -p $BACKUP_DIR/$TIMESTAMP
cp -r backend/.env* $BACKUP_DIR/$TIMESTAMP/ 2>/dev/null || echo "No .env files to backup"

# Update kode dari GitHub
echo -e "${GREEN}Updating code from GitHub...${NC}"
git fetch origin
git reset --hard origin/main

# Setup backend
echo -e "${GREEN}Setting up backend...${NC}"
cd backend

# Cek jika .env.production tidak ada, gunakan .env.production.template
if [ ! -f .env.production ]; then
  echo -e "${YELLOW}Production .env not found. Creating from template...${NC}"
  cp .env.production.template .env.production
  
  # Generate secrets for JWT dan session
  JWT_SECRET=$(openssl rand -base64 32)
  JWT_REFRESH_SECRET=$(openssl rand -base64 32)
  SESSION_SECRET=$(openssl rand -base64 32)
  
  # Update .env.production dengan secrets
  sed -i "s/JWT_SECRET=.*/JWT_SECRET=$JWT_SECRET/g" .env.production
  sed -i "s/JWT_REFRESH_SECRET=.*/JWT_REFRESH_SECRET=$JWT_REFRESH_SECRET/g" .env.production
  sed -i "s/SESSION_SECRET=.*/SESSION_SECRET=$SESSION_SECRET/g" .env.production
fi

# Install dependencies dan build
echo -e "${GREEN}Installing backend dependencies...${NC}"
npm ci --production

echo -e "${GREEN}Building backend...${NC}"
npm run build

# Apply database migrations
echo -e "${GREEN}Applying database migrations...${NC}"
npm run db:migrate

# Setup dan restart service dengan PM2
echo -e "${GREEN}Restarting service...${NC}"
if pm2 list | grep -q "bizmark-api"; then
  pm2 restart bizmark-api
else
  pm2 start dist/main.js --name bizmark-api
  pm2 save
fi

# Setup Nginx jika dibutuhkan
if [ ! -f /etc/nginx/sites-available/bizmark.id.conf ]; then
  echo -e "${YELLOW}Nginx configuration not found. Creating...${NC}"
  cat > /etc/nginx/sites-available/bizmark.id.conf << EOF
server {
    listen 80;
    server_name bizmark.id www.bizmark.id;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_cache_bypass \$http_upgrade;
    }
}
EOF

  # Enable site dan reload Nginx
  ln -s /etc/nginx/sites-available/bizmark.id.conf /etc/nginx/sites-enabled/
  nginx -t && systemctl reload nginx
fi

echo -e "${GREEN}====== DEPLOYMENT COMPLETE ======${NC}"
echo -e "${YELLOW}Deployment completed at $(date)${NC}"
echo "App running at: http://localhost:3000/api/v1"
echo "API docs available at: http://localhost:3000/api/docs"
