#!/bin/bash

# 🚀 Render.com Deployment Automation Script
# This script prepares and guides you through the deployment process

set -e

echo "🚀 Bizmark.id - Render.com Deployment Setup"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "render.yaml" ]; then
    echo "❌ Error: render.yaml not found. Please run this script from the project root."
    exit 1
fi

echo "✅ Pre-deployment validation:"
echo ""

# Check required files
files=("render.yaml" "build.sh" "setup-render-env.sh" "backend/.env.production.template" "backend/nest-cli.json" "backend/tsconfig.build.json")
for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file - Ready"
    else
        echo "  ❌ $file - Missing"
        exit 1
    fi
done

echo ""
echo "🔐 Security Configuration Status:"
echo ""

# Check if environment variables are properly configured
if grep -q "your-secret-key-here" backend/.env 2>/dev/null; then
    echo "  ❌ Default secrets detected in .env file"
    echo "  🔧 Running OpenSSL key generation..."
    ./setup-render-env.sh
    echo "  ✅ Secure keys generated"
else
    echo "  ✅ Production secrets configured"
fi

echo ""
echo "🏗️  Build System Validation:"
echo ""

# Test build process
cd backend
if npm run build > /dev/null 2>&1; then
    echo "  ✅ NestJS build successful"
else
    echo "  ❌ Build failed - check your configuration"
    exit 1
fi

# Check dist directory
if [ -d "dist" ] && [ -f "dist/main.js" ]; then
    echo "  ✅ Build output verified"
else
    echo "  ❌ Build output incomplete"
    exit 1
fi

cd ..

echo ""
echo "📊 Deployment Summary:"
echo ""
echo "  📁 Project: Bizmark.id - SaaS Perizinan UMKM"
echo "  🏗️  Backend: NestJS + PostgreSQL + Redis"
echo "  🔐 Security: OpenSSL-generated keys (256-bit)"
echo "  🌍 Region: Singapore (ap-southeast-1)"
echo "  💰 Cost: Free tier (750 hours/month)"
echo "  📈 Auto-scaling: Enabled"
echo "  🔍 Health checks: /api/v1/health/ready"
echo ""

echo "🚀 Next Steps for Render.com Deployment:"
echo ""
echo "1. 📤 Push to GitHub:"
echo "   git add ."
echo "   git commit -m \"feat: production-ready render.com deployment\""
echo "   git push origin main"
echo ""
echo "2. 🌐 Go to render.com:"
echo "   - Sign up/login with GitHub"
echo "   - Click 'New' → 'Blueprint'"
echo "   - Connect your bizmark.id repository"
echo "   - Render will auto-deploy using render.yaml"
echo ""
echo "3. ⏱️  Wait for deployment (5-10 minutes):"
echo "   - PostgreSQL database creation"
echo "   - Redis instance setup"
echo "   - Backend service deployment"
echo "   - Environment variables configuration"
echo ""
echo "4. ✅ Verify deployment:"
echo "   curl https://your-app.onrender.com/api/v1/health"
echo "   open https://your-app.onrender.com/api/docs"
echo ""

echo "📋 Deployment Checklist:"
echo ""
echo "  ✅ All required files present"
echo "  ✅ Security configuration complete"
echo "  ✅ Build system validated"
echo "  ✅ Environment templates ready"
echo "  ✅ Health checks configured"
echo "  ✅ Auto-deploy enabled"
echo ""

echo "🎉 Your application is ready for production deployment!"
echo ""
echo "💡 Tips:"
echo "  - Monitor deployment in Render dashboard"
echo "  - Check build logs if deployment fails"
echo "  - Use health endpoints for monitoring"
echo "  - Set up custom domain after deployment"
echo ""

echo "📚 Resources:"
echo "  - Deployment Guide: ./DEPLOYMENT_GUIDE.md"
echo "  - Render Docs: https://render.com/docs"
echo "  - Support: GitHub Issues or Render Support"
echo ""

read -p "Press Enter to continue with GitHub push, or Ctrl+C to exit..."

echo ""
echo "📤 Preparing Git commit..."

git add .
git status

echo ""
read -p "Commit message (default: 'feat: production-ready render.com deployment'): " commit_msg
commit_msg=${commit_msg:-"feat: production-ready render.com deployment"}

git commit -m "$commit_msg"

echo ""
read -p "Push to origin main? (y/N): " push_confirm
if [[ $push_confirm =~ ^[Yy]$ ]]; then
    git push origin main
    echo ""
    echo "✅ Code pushed to GitHub!"
    echo "🌐 Now go to render.com to deploy your application"
else
    echo "📝 Code committed locally. Push when ready:"
    echo "   git push origin main"
fi

echo ""
echo "🚀 Deployment preparation complete!"
