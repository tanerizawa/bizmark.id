#!/bin/bash
# Deployment Helper Script

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Bizmark.id Deployment Helper${NC}"
echo "================================"

show_keys() {
    echo -e "${YELLOW}📋 SSH Key Information:${NC}"
    echo ""
    echo -e "${GREEN}Public Key (copy to VPS):${NC}"
    cat ~/.ssh/bizmark_deploy_key.pub
    echo ""
    echo -e "${GREEN}Private Key (for GitHub Secret VPS_SSH_KEY):${NC}"
    cat ~/.ssh/bizmark_deploy_key
    echo ""
}

show_secrets() {
    echo -e "${YELLOW}🔐 GitHub Secrets to Configure:${NC}"
    echo ""
    echo "Repository → Settings → Secrets and variables → Actions → New repository secret"
    echo ""
    echo "VPS_HOST: YOUR_VPS_IP_OR_DOMAIN"
    echo "VPS_USERNAME: YOUR_VPS_USERNAME"
    echo "VPS_SSH_KEY: (paste private key from above)"  
    echo "VPS_PORT: 22"
    echo ""
}

test_local() {
    echo -e "${YELLOW}🧪 Testing local configuration...${NC}"
    
    # Check Docker
    if command -v docker &> /dev/null; then
        echo -e "${GREEN}✅ Docker installed: $(docker --version)${NC}"
    else
        echo -e "${RED}❌ Docker not installed${NC}"
        return 1
    fi
    
    # Check Docker Compose config
    if docker compose -f docker-compose.prod.yml --env-file .env.production config > /dev/null 2>&1; then
        echo -e "${GREEN}✅ Docker Compose configuration valid${NC}"
    else
        echo -e "${RED}❌ Docker Compose configuration invalid${NC}"
        return 1
    fi
    
    # Check environment file
    if [ -f ".env.production" ]; then
        echo -e "${GREEN}✅ Environment file exists${NC}"
    else
        echo -e "${RED}❌ Environment file missing${NC}"
        return 1
    fi
    
    echo -e "${GREEN}✅ All local tests passed${NC}"
}

deploy_to_vps() {
    echo -e "${YELLOW}🚀 Manual deployment to VPS...${NC}"
    
    if [ -z "$1" ]; then
        echo -e "${RED}Usage: $0 deploy <user@vps-ip>${NC}"
        return 1
    fi
    
    VPS_TARGET=$1
    
    echo "Deploying to: $VPS_TARGET"
    
    # Copy files to VPS
    scp -i ~/.ssh/bizmark_deploy_key -r . $VPS_TARGET:/opt/bizmark-app/
    
    # Run deployment on VPS
    ssh -i ~/.ssh/bizmark_deploy_key $VPS_TARGET << 'EOF'
cd /opt/bizmark-app
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml up -d --build
docker ps
EOF
    
    echo -e "${GREEN}✅ Deployment completed${NC}"
}

case "$1" in
    "keys")
        show_keys
        ;;
    "secrets")
        show_secrets
        ;;
    "test")
        test_local
        ;;
    "deploy")
        deploy_to_vps $2
        ;;
    *)
        echo "Usage: $0 {keys|secrets|test|deploy}"
        echo ""
        echo "Commands:"
        echo "  keys     - Show SSH keys for VPS setup"
        echo "  secrets  - Show GitHub secrets to configure"
        echo "  test     - Test local configuration"
        echo "  deploy   - Manual deploy to VPS (usage: deploy user@vps-ip)"
        echo ""
        ;;
esac
