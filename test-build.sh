#!/bin/bash
# test-build.sh - Test build process sebelum deploy ke Render

echo "🧪 Testing Build Process for Render.com Deployment"
echo "=================================================="

# Set temporary production environment
export NODE_ENV=production
export RUN_MIGRATIONS=false

# Run build process
echo "🏗️  Running build process..."
if ./build.sh; then
    echo "✅ Build test successful!"
    
    # Test if built app can start
    echo "🔄 Testing application startup..."
    cd backend
    
    # Start app in background and test
    timeout 30 npm run start:prod &
    APP_PID=$!
    
    # Wait for app to start
    sleep 10
    
    # Test health endpoint
    if curl -f -s http://localhost:3001/api/v1/health/live > /dev/null; then
        echo "✅ Application startup test successful!"
        kill $APP_PID 2>/dev/null || true
    else
        echo "❌ Application startup test failed!"
        kill $APP_PID 2>/dev/null || true
        exit 1
    fi
    
    echo "=================================================="
    echo "🎉 All tests passed! Ready for Render deployment"
else
    echo "❌ Build test failed!"
    exit 1
fi
