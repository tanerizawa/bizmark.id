#!/bin/bash

# Quick Phase 4 Status Check
# Simple verification without complex timeout checks

echo "🔍 Phase 4: Quick Status Check"
echo "============================="

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo ""
echo "📦 Dependencies Status:"
echo "----------------------"

# Frontend Dependencies
if [ -d "frontend/node_modules" ]; then
    echo -e "Frontend Dependencies: ${GREEN}✅ INSTALLED${NC}"
else
    echo -e "Frontend Dependencies: ${RED}❌ MISSING${NC}"
fi

# Backend Dependencies  
if [ -d "backend/node_modules" ]; then
    echo -e "Backend Dependencies:  ${GREEN}✅ INSTALLED${NC}"
else
    echo -e "Backend Dependencies:  ${RED}❌ MISSING${NC}"
fi

echo ""
echo "🔧 System Status:"
echo "-----------------"

# Docker
if docker info > /dev/null 2>&1; then
    echo -e "Docker:               ${GREEN}✅ RUNNING${NC}"
else
    echo -e "Docker:               ${RED}❌ NOT RUNNING${NC}"
fi

# Docker Compose
if docker compose version > /dev/null 2>&1; then
    echo -e "Docker Compose:       ${GREEN}✅ AVAILABLE${NC}"
else
    echo -e "Docker Compose:       ${RED}❌ NOT AVAILABLE${NC}"
fi

echo ""
echo "🚀 Phase 4 Key Files:"
echo "--------------------"

# Critical Phase 4 files
FILES=(
    "frontend/src/services/backendApi.ts:Backend API Service"
    "frontend/src/hooks/useBackendApi.ts:React Hooks"
    "frontend/src/app/dashboard/backend-integration/page.tsx:Integration Dashboard"
    "scripts/start-fullstack-dev.sh:Full Stack Startup"
    "scripts/test-integration.sh:Integration Testing"
    "docs/PHASE-4-BACKEND-INTEGRATION.md:Phase 4 Docs"
)

for file_info in "${FILES[@]}"; do
    IFS=':' read -r file_path file_desc <<< "$file_info"
    if [ -f "$file_path" ]; then
        echo -e "$file_desc: ${GREEN}✅ OK${NC}"
    else
        echo -e "$file_desc: ${RED}❌ MISSING${NC}"
    fi
done

echo ""
echo "📊 Summary:"
echo "----------"

# Count files
total_files=0
existing_files=0

for file_info in "${FILES[@]}"; do
    IFS=':' read -r file_path file_desc <<< "$file_info"
    total_files=$((total_files + 1))
    if [ -f "$file_path" ]; then
        existing_files=$((existing_files + 1))
    fi
done

if [ -d "frontend/node_modules" ] && [ -d "backend/node_modules" ] && [ $existing_files -eq $total_files ]; then
    echo -e "${GREEN}🎉 Phase 4 is READY!${NC}"
    echo ""
    echo "✅ All dependencies installed"
    echo "✅ All critical files present"
    echo "✅ Development environment ready"
    echo ""
    echo "🚀 Quick Start Commands:"
    echo "   ./scripts/start-fullstack-dev.sh  # Start development"
    echo "   ./scripts/test-integration.sh     # Test integration"
    echo ""
    echo "🌐 Application URLs:"
    echo "   Frontend: http://localhost:3000"
    echo "   Backend:  http://localhost:3001"
    echo "   Monitor:  http://localhost:3000/dashboard/backend-integration"
else
    echo -e "${YELLOW}⚠️  Phase 4 needs attention${NC}"
    echo ""
    if [ ! -d "frontend/node_modules" ]; then
        echo "❌ Frontend dependencies missing - run: cd frontend && npm install"
    fi
    if [ ! -d "backend/node_modules" ]; then
        echo "❌ Backend dependencies missing - run: cd backend && npm install"
    fi
    if [ $existing_files -ne $total_files ]; then
        echo "❌ Some Phase 4 files are missing"
    fi
fi

echo ""
