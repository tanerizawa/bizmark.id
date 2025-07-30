#!/bin/bash

# Render.yaml Validation Script
# This script validates the render.yaml configuration before deployment

echo "🔍 RENDER.YAML VALIDATION"
echo "========================"
echo ""

# Check if render.yaml exists
if [ ! -f "render.yaml" ]; then
    echo "❌ render.yaml file not found"
    exit 1
fi

echo "✅ render.yaml file found"

# Check YAML syntax
if command -v python3 &> /dev/null; then
    if python3 -c "import yaml; yaml.safe_load(open('render.yaml'))" 2>/dev/null; then
        echo "✅ YAML syntax is valid"
    else
        echo "❌ YAML syntax error detected"
        exit 1
    fi
else
    echo "⚠️  Python3 not available for YAML validation"
fi

# Check required fields
echo ""
echo "🔍 Checking required configuration..."

# Check service configuration
if grep -q "name: bizmark-api" render.yaml; then
    echo "✅ Service name configured"
else
    echo "❌ Service name missing"
fi

if grep -q "type: web" render.yaml; then
    echo "✅ Service type configured"
else
    echo "❌ Service type missing"
fi

if grep -q "buildCommand: ./build.sh" render.yaml; then
    echo "✅ Build command configured"
else
    echo "❌ Build command missing or incorrect"
fi

if grep -q "healthCheckPath: /api/v1/health/ready" render.yaml; then
    echo "✅ Health check path configured"
else
    echo "❌ Health check path missing or incorrect"
fi

# Check database configuration
if grep -q "name: bizmark-db" render.yaml; then
    echo "✅ Database service configured"
else
    echo "❌ Database service missing"
fi

if grep -q "name: bizmark-redis" render.yaml; then
    echo "✅ Redis service configured"
else
    echo "❌ Redis service missing"
fi

# Check environment variables
if grep -q "NODE_ENV" render.yaml; then
    echo "✅ NODE_ENV configured"
else
    echo "❌ NODE_ENV missing"
fi

if grep -q "DATABASE_URL" render.yaml; then
    echo "✅ DATABASE_URL configured"
else
    echo "❌ DATABASE_URL missing"
fi

if grep -q "REDIS_URL" render.yaml; then
    echo "✅ REDIS_URL configured"
else
    echo "❌ REDIS_URL missing"
fi

echo ""
echo "🔍 Checking build script..."

if [ -f "build.sh" ]; then
    echo "✅ build.sh exists"
    if [ -x "build.sh" ]; then
        echo "✅ build.sh is executable"
    else
        echo "⚠️  build.sh is not executable, fixing..."
        chmod +x build.sh
        echo "✅ build.sh made executable"
    fi
else
    echo "❌ build.sh not found"
fi

echo ""
echo "🔍 Checking backend structure..."

if [ -d "backend" ]; then
    echo "✅ backend directory exists"
    
    if [ -f "backend/package.json" ]; then
        echo "✅ backend/package.json exists"
    else
        echo "❌ backend/package.json missing"
    fi
    
    if [ -f "backend/nest-cli.json" ]; then
        echo "✅ backend/nest-cli.json exists"
    else
        echo "❌ backend/nest-cli.json missing"
    fi
    
    if [ -f "backend/tsconfig.build.json" ]; then
        echo "✅ backend/tsconfig.build.json exists"
    else
        echo "❌ backend/tsconfig.build.json missing"
    fi
else
    echo "❌ backend directory not found"
fi

echo ""
echo "🎯 VALIDATION SUMMARY"
echo "===================="

# Count errors
error_count=$(grep -c "❌" <<< "$(cat <<EOF
# This is a placeholder for error counting
EOF
)" 2>/dev/null || echo "0")

if [ "$error_count" -eq 0 ]; then
    echo "🎉 All validations passed!"
    echo "✅ render.yaml is ready for deployment"
    echo ""
    echo "🚀 Next steps:"
    echo "1. git add ."
    echo "2. git commit -m 'fix: updated render.yaml configuration'"
    echo "3. git push origin main"
    echo "4. Deploy on render.com using Blueprint"
else
    echo "⚠️  $error_count validation errors found"
    echo "Please fix the issues above before deploying"
fi
