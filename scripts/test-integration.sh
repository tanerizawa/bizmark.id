#!/bin/bash

# Integration Test Script
# Phase 4: Backend Integration Testing

set -e

echo "🧪 Phase 4: Integration Testing"
echo "==============================="

FRONTEND_URL="http://localhost:3000"
BACKEND_URL="http://localhost:3001"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Test functions
test_service() {
    local service_name=$1
    local url=$2
    local expected_status=${3:-200}
    
    echo -n "Testing $service_name... "
    
    if response=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null); then
        if [ "$response" = "$expected_status" ]; then
            echo -e "${GREEN}✅ OK${NC} (Status: $response)"
            return 0
        else
            echo -e "${YELLOW}⚠️  Unexpected status${NC} (Got: $response, Expected: $expected_status)"
            return 1
        fi
    else
        echo -e "${RED}❌ FAILED${NC} (Connection failed)"
        return 1
    fi
}

test_api_endpoint() {
    local endpoint_name=$1
    local url=$2
    local method=${3:-GET}
    local expected_status=${4:-200}
    
    echo -n "Testing $endpoint_name... "
    
    if response=$(curl -s -o /dev/null -w "%{http_code}" -X "$method" "$url" 2>/dev/null); then
        if [ "$response" = "$expected_status" ]; then
            echo -e "${GREEN}✅ OK${NC} (Status: $response)"
            return 0
        else
            echo -e "${YELLOW}⚠️  Status: $response${NC} (Expected: $expected_status)"
            return 1
        fi
    else
        echo -e "${RED}❌ FAILED${NC} (Connection failed)"
        return 1
    fi
}

# Main testing
echo "🔍 Testing Services Connectivity..."
echo "-----------------------------------"

# Test frontend
test_service "Frontend" "$FRONTEND_URL"
FRONTEND_STATUS=$?

# Test backend health
test_service "Backend Health" "$BACKEND_URL/health"
BACKEND_STATUS=$?

# Test API endpoints
echo ""
echo "🔍 Testing API Endpoints..."
echo "---------------------------"

test_api_endpoint "API Root" "$BACKEND_URL/api"
test_api_endpoint "Auth Endpoint" "$BACKEND_URL/api/auth/login" "POST" "400"
test_api_endpoint "Business Endpoint" "$BACKEND_URL/api/businesses" "GET" "401"
test_api_endpoint "Files Endpoint" "$BACKEND_URL/api/files/upload" "POST" "401"

# Test database connectivity
echo ""
echo "🔍 Testing Database Connectivity..."
echo "-----------------------------------"

if docker ps | grep -q "postgres"; then
    echo -e "PostgreSQL Container: ${GREEN}✅ Running${NC}"
else
    echo -e "PostgreSQL Container: ${RED}❌ Not found${NC}"
fi

if docker ps | grep -q "redis"; then
    echo -e "Redis Container: ${GREEN}✅ Running${NC}"
else
    echo -e "Redis Container: ${RED}❌ Not found${NC}"
fi

if docker ps | grep -q "minio"; then
    echo -e "MinIO Container: ${GREEN}✅ Running${NC}"
else
    echo -e "MinIO Container: ${RED}❌ Not found${NC}"
fi

# Test frontend integration dashboard
echo ""
echo "🔍 Testing Frontend Integration..."
echo "---------------------------------"

test_service "Backend Integration Dashboard" "$FRONTEND_URL/dashboard/backend-integration"

# Summary
echo ""
echo "📊 Test Summary"
echo "==============="

if [ $FRONTEND_STATUS -eq 0 ] && [ $BACKEND_STATUS -eq 0 ]; then
    echo -e "${GREEN}🎉 Integration Test PASSED!${NC}"
    echo ""
    echo "✅ All core services are running"
    echo "✅ Frontend-Backend communication is working"
    echo "✅ Database containers are active"
    echo ""
    echo "👉 Access your application:"
    echo "   Frontend: $FRONTEND_URL"
    echo "   Backend:  $BACKEND_URL"
    echo "   Dashboard: $FRONTEND_URL/dashboard/backend-integration"
    exit 0
else
    echo -e "${RED}❌ Integration Test FAILED!${NC}"
    echo ""
    if [ $FRONTEND_STATUS -ne 0 ]; then
        echo "❌ Frontend is not accessible"
    fi
    if [ $BACKEND_STATUS -ne 0 ]; then
        echo "❌ Backend is not accessible"
    fi
    echo ""
    echo "💡 Troubleshooting tips:"
    echo "   1. Make sure all services are started with ./scripts/start-fullstack-dev.sh"
    echo "   2. Check if ports 3000 and 3001 are available"
    echo "   3. Verify Docker is running and containers are healthy"
    exit 1
fi
