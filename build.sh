#!/usr/bin/env bash
# build.sh - Production build script for Render.com deployment

set -e # Exit on error

echo "🏗️  Starting Bizmark.id Backend Build Process..."
echo "================================================="

# Check if we're in the right directory and navigate to backend
if [ -d "backend" ]; then
    cd backend
    echo "📂 Changed to backend directory"
elif [ -f "package.json" ]; then
    echo "📂 Already in backend directory"
else
    echo "❌ Backend directory not found"
    exit 1
fi

# Display Node.js and npm versions
echo "📦 Node.js version: $(node --version)"
echo "📦 npm version: $(npm --version)"

# Clean cache dan install dependencies
echo "🧹 Cleaning npm cache..."
npm cache clean --force

echo "📥 Installing dependencies..."
npm install --no-audit --no-fund

# Build aplikasi
echo "🔨 Building application..."
if npm run build; then
    echo "✅ Build with NestJS CLI successful"
elif npm run build:tsc; then
    echo "✅ Build with TypeScript compiler successful"
else
    echo "❌ Both build methods failed"
    exit 1
fi

# Verify build output
if [ -d "dist" ]; then
    echo "✅ Build output verified: dist/ directory exists"
    echo "📁 Build contents:"
    ls -la dist/
else
    echo "❌ Build failed: dist/ directory not found"
    exit 1
fi

# Run database migrations if in production
if [[ "$NODE_ENV" == "production" ]]; then
    if [[ "$RUN_MIGRATIONS" == "true" ]]; then
        echo "🗄️  Running database migrations..."
        npm run migration:run
        echo "✅ Database migrations completed"
    else
        echo "⏭️  Skipping database migrations (RUN_MIGRATIONS not set to true)"
    fi
fi

# Health check for required files
echo "🔍 Verifying required files..."
REQUIRED_FILES=("dist/main.js" "package.json")
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file exists"
    else
        echo "❌ Required file missing: $file"
        exit 1
    fi
done

echo "================================================="
echo "🎉 Build completed successfully!"
echo "🚀 Ready for deployment to Render.com"
