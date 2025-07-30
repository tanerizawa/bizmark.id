#!/bin/bash

echo "🚀 Starting Bizmark.id Backend Development Setup"
echo "================================================"

# Check if PostgreSQL is running
echo "📋 Checking PostgreSQL connection..."
if ! pg_isready -h localhost -p 5432 > /dev/null 2>&1; then
    echo "❌ PostgreSQL is not running"
    echo "💡 Please start PostgreSQL:"
    echo "   brew services start postgresql"
    echo "   Or: pg_ctl -D /usr/local/var/postgres start"
    exit 1
fi

echo "✅ PostgreSQL is running"

# Check if Redis is running (optional for now)
echo "📋 Checking Redis connection..."
if ! redis-cli ping > /dev/null 2>&1; then
    echo "⚠️  Redis is not running (optional for jobs)"
    echo "💡 To start Redis: brew services start redis"
else
    echo "✅ Redis is running"
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "📦 Installing dependencies..."
    npm install
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "⚠️  .env file not found"
    echo "📝 Creating .env from template..."
    
    cat > .env << EOL
# Database
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_USERNAME=$USER
DATABASE_PASSWORD=
DATABASE_NAME=bizmark_dev
DATABASE_SSL=false

# JWT
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
JWT_EXPIRES_IN=1d

# Redis (for jobs)
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# Email (SMTP)
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM=noreply@bizmark.id

# MinIO (File Storage)
MINIO_ENDPOINT=localhost
MINIO_PORT=9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET=bizmark-files

# App
NODE_ENV=development
PORT=3000
APP_NAME=Bizmark.id
APP_URL=http://localhost:3000
FRONTEND_URL=http://localhost:3001

# Rate Limiting
RATE_LIMIT_TTL=60000
RATE_LIMIT_MAX=100
EOL

    echo "✅ .env file created"
    echo "⚠️  Please update database credentials in .env file"
fi

# Create database if it doesn't exist
echo "📋 Checking database..."
DB_NAME="bizmark_dev"
if ! psql -lqt | cut -d \| -f 1 | grep -qw $DB_NAME; then
    echo "🗄️  Creating database: $DB_NAME"
    createdb $DB_NAME
    echo "✅ Database created"
else
    echo "✅ Database exists"
fi

# Run migrations
echo "🔧 Running database migrations..."
npm run migration:run

if [ $? -eq 0 ]; then
    echo "✅ Migrations completed"
    
    # Run seeders
    echo "🌱 Running database seeders..."
    npm run seed:run
    
    if [ $? -eq 0 ]; then
        echo "✅ Seeders completed"
    else
        echo "⚠️  Seeders failed (continuing anyway)"
    fi
else
    echo "❌ Migrations failed"
    exit 1
fi

# Build the application
echo "🔨 Building application..."
npm run build

if [ $? -eq 0 ]; then
    echo "✅ Build completed"
else
    echo "❌ Build failed"
    exit 1
fi

echo ""
echo "🎉 Setup completed successfully!"
echo "================================================"
echo ""
echo "🚀 To start the development server:"
echo "   npm run start:dev"
echo ""
echo "📋 Default admin credentials:"
echo "   Email: admin@bizmark.id"
echo "   Password: admin123"
echo ""
echo "🌐 API will be available at: http://localhost:3000"
echo "📚 API Documentation: http://localhost:3000/api"
echo ""
echo "⚡ Available scripts:"
echo "   npm run start:dev    - Start development server"
echo "   npm run start:prod   - Start production server"
echo "   npm run build        - Build application"
echo "   npm run lint         - Lint code"
echo "   npm run test         - Run tests"
echo "   npm run migration:run - Run database migrations"
echo "   npm run seed:run     - Run database seeders"
echo ""
