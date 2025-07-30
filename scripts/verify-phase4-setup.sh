#!/bin/bash

# Phase 4 Setup Verification Script
# Verifies all components are properly configured

set -e

echo "🔍 Phase 4: Setup Verification"
echo "=============================="

PROJECT_ROOT="$(dirname "$0")/.."
FRONTEND_DIR="$PROJECT_ROOT/frontend"
BACKEND_DIR="$PROJECT_ROOT/backend"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

SUCCESS_COUNT=0
TOTAL_CHECKS=0

# Function to check item
check_item() {
    local item_name="$1"
    local check_command="$2"
    local is_critical="${3:-true}"
    
    TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    echo -n "Checking $item_name... "
    
    if eval "$check_command" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ OK${NC}"
        SUCCESS_COUNT=$((SUCCESS_COUNT + 1))
        return 0
    else
        if [ "$is_critical" = "true" ]; then
            echo -e "${RED}❌ FAILED${NC}"
        else
            echo -e "${YELLOW}⚠️  WARNING${NC}"
        fi
        return 1
    fi
}

# Function to check file exists
check_file() {
    local file_name="$1"
    local file_path="$2"
    local is_critical="${3:-true}"
    
    check_item "$file_name" "[ -f '$file_path' ]" "$is_critical"
}

# Function to check directory exists
check_directory() {
    local dir_name="$1"
    local dir_path="$2"
    local is_critical="${3:-true}"
    
    check_item "$dir_name" "[ -d '$dir_path' ]" "$is_critical"
}

echo ""
echo "📁 Project Structure"
echo "-------------------"

# Check main directories
check_directory "Frontend directory" "$FRONTEND_DIR"
check_directory "Backend directory" "$BACKEND_DIR"
check_directory "Scripts directory" "$PROJECT_ROOT/scripts"
check_directory "Docs directory" "$PROJECT_ROOT/docs"

echo ""
echo "📄 Critical Files"
echo "----------------"

# Check critical project files
check_file "Main README" "$PROJECT_ROOT/README.md"
check_file "Frontend package.json" "$FRONTEND_DIR/package.json"
check_file "Backend package.json" "$BACKEND_DIR/package.json"

# Check Phase 4 specific files
check_file "Backend API Service" "$FRONTEND_DIR/src/services/backendApi.ts"
check_file "Backend API Hooks" "$FRONTEND_DIR/src/hooks/useBackendApi.ts"
check_file "Integration Dashboard" "$FRONTEND_DIR/src/app/dashboard/backend-integration/page.tsx"

echo ""
echo "🚀 Development Scripts"
echo "---------------------"

# Check development scripts
check_file "Full Stack Startup Script" "$PROJECT_ROOT/scripts/start-fullstack-dev.sh"
check_file "Backend Development Script" "$PROJECT_ROOT/scripts/start-backend-dev.sh"
check_file "Integration Test Script" "$PROJECT_ROOT/scripts/test-integration.sh"

# Check script permissions
check_item "Full Stack Script Executable" "[ -x '$PROJECT_ROOT/scripts/start-fullstack-dev.sh' ]"
check_item "Backend Script Executable" "[ -x '$PROJECT_ROOT/scripts/start-backend-dev.sh' ]"
check_item "Test Script Executable" "[ -x '$PROJECT_ROOT/scripts/test-integration.sh' ]"

echo ""
echo "📚 Documentation"
echo "---------------"

# Check documentation files
check_file "Phase 4 Documentation" "$PROJECT_ROOT/docs/PHASE-4-BACKEND-INTEGRATION.md"
check_file "Phase 4 Quick Reference" "$PROJECT_ROOT/docs/PHASE-4-QUICK-REFERENCE.md"

echo ""
echo "🔧 System Requirements"
echo "---------------------"

# Check system requirements
check_item "Node.js" "command -v node"
check_item "npm" "command -v npm"
check_item "Docker" "command -v docker"
check_item "Docker Compose" "docker compose version"
check_item "curl" "command -v curl"

echo ""
echo "🐳 Docker Status"
echo "---------------"

# Check Docker status
check_item "Docker Running" "docker info"

echo ""
echo "📦 Dependencies"
echo "--------------"

# Check if node_modules exist (optional)
check_file "Frontend Dependencies" "$FRONTEND_DIR/node_modules" false
check_file "Backend Dependencies" "$BACKEND_DIR/node_modules" false

echo ""
echo "🔍 Code Quality"
echo "--------------"

# Check TypeScript configuration
check_file "Frontend TypeScript Config" "$FRONTEND_DIR/tsconfig.json"
check_file "Backend TypeScript Config" "$BACKEND_DIR/tsconfig.json"

# Check for linting configuration
check_file "Frontend ESLint Config" "$FRONTEND_DIR/.eslintrc.json" false
check_file "Backend ESLint Config" "$BACKEND_DIR/.eslintrc.js" false

echo ""
echo "📊 Verification Summary"
echo "======================"

PERCENTAGE=$((SUCCESS_COUNT * 100 / TOTAL_CHECKS))

echo "Total Checks: $TOTAL_CHECKS"
echo "Passed: $SUCCESS_COUNT"
echo "Failed: $((TOTAL_CHECKS - SUCCESS_COUNT))"
echo "Success Rate: $PERCENTAGE%"

echo ""

if [ $PERCENTAGE -ge 90 ]; then
    echo -e "${GREEN}🎉 EXCELLENT! Phase 4 setup is ready!${NC}"
    echo ""
    echo "✅ All critical components are in place"
    echo "✅ Development environment is properly configured"
    echo "✅ Scripts are executable and ready to use"
    echo ""
    echo "🚀 Next steps:"
    echo "   1. Start development environment: ./scripts/start-fullstack-dev.sh"
    echo "   2. Run integration tests: ./scripts/test-integration.sh"
    echo "   3. Access integration dashboard: http://localhost:3000/dashboard/backend-integration"
    
elif [ $PERCENTAGE -ge 75 ]; then
    echo -e "${YELLOW}⚠️  GOOD: Phase 4 setup is mostly complete${NC}"
    echo ""
    echo "Most components are ready, but some optimizations are needed."
    echo "Review the failed items above and install missing dependencies."
    
elif [ $PERCENTAGE -ge 50 ]; then
    echo -e "${YELLOW}⚠️  PARTIAL: Phase 4 setup needs attention${NC}"
    echo ""
    echo "Several components are missing or misconfigured."
    echo "Please review the failed items and complete the setup."
    
else
    echo -e "${RED}❌ INCOMPLETE: Phase 4 setup requires significant work${NC}"
    echo ""
    echo "Many critical components are missing."
    echo "Please complete the basic project setup before proceeding."
fi

echo ""
echo "📋 Troubleshooting Tips:"
echo "----------------------"
echo "• If node_modules are missing: run 'npm install' in frontend/ and backend/"
echo "• If Docker is not running: start Docker Desktop"
echo "• If scripts are not executable: run 'chmod +x scripts/*.sh'"
echo "• For detailed setup: see docs/PHASE-4-BACKEND-INTEGRATION.md"
echo "• For quick reference: see docs/PHASE-4-QUICK-REFERENCE.md"

echo ""
echo "🔗 Useful Links:"
echo "---------------"
echo "• Project Documentation: README.md"
echo "• Phase 4 Guide: docs/PHASE-4-BACKEND-INTEGRATION.md"
echo "• Quick Reference: docs/PHASE-4-QUICK-REFERENCE.md"

exit 0
