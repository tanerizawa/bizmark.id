# Database & Production Compatibility Guide

## Overview
Platform SaaS Perizinan UMKM kini memiliki setup database yang compatible antara development dan production environment. Konfigurasi telah disesuaikan dengan deployment guide untuk memastikan transisi yang smooth ke production.

## Development Setup

### Option 1: Docker-based Development (Recommended)
```bash
# Start infrastructure services
npm run docker:dev

# Run migrations and seed data
npm run migration:run
npm run seed

# Start development server
npm run start:dev
```

### Option 2: Local Services
```bash
# Install and start PostgreSQL, Redis, MinIO locally
# Then run:
npm run start:dev
```

## Database Configuration

### Development (.env)
```env
NODE_ENV=development
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_USERNAME=postgres
DATABASE_PASSWORD=postgres
DATABASE_NAME=bizmark_dev
DATABASE_URL=postgresql://postgres:postgres@localhost:5432/bizmark_dev

# Redis
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# MinIO
MINIO_ENDPOINT=localhost
MINIO_PORT=9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET_NAME=bizmark-files
MINIO_USE_SSL=false
```

### Production (.env.prod)
```env
NODE_ENV=production
DATABASE_HOST=postgres
DATABASE_USERNAME=bizmark_user
DATABASE_PASSWORD=${POSTGRES_PASSWORD}
DATABASE_NAME=bizmark_db

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=${REDIS_PASSWORD}

# MinIO
MINIO_ENDPOINT=minio
MINIO_PORT=9000
MINIO_ACCESS_KEY=${MINIO_ROOT_USER}
MINIO_SECRET_KEY=${MINIO_ROOT_PASSWORD}
```

## Key Compatibility Features

### 1. Flexible Database Configuration
- Supports both individual config vars and DATABASE_URL
- Connection pooling optimized for production
- SSL support for production databases
- Query caching with Redis in production

### 2. Health Checks
- **GET /health**: Comprehensive health check (database, memory, disk)
- **GET /health/ready**: Kubernetes readiness probe
- **GET /health/live**: Kubernetes liveness probe

### 3. Docker Support
- Multi-stage Dockerfile for optimized production builds
- Development stage for local Docker development
- Health checks integrated for container orchestration
- Non-root user for security

### 4. Environment-Specific Optimizations

#### Development
- Synchronize: true (auto-sync schema)
- Query logging: enabled
- Connection pool: small (5 max)
- No query caching

#### Production
- Synchronize: false (use migrations)
- Query logging: errors only
- Connection pool: large (20 max)
- Redis query caching enabled
- SSL connection support

## Migration Strategy

### 1. Development to Staging
```bash
# Build production image
docker build -t bizmark/backend:staging .

# Deploy to staging with production-like environment
docker-compose -f docker-compose.staging.yml up -d

# Run migrations
docker exec bizmark_backend npm run migration:run
```

### 2. Staging to Production
```bash
# Use CI/CD pipeline or manual deployment
# Environment variables are managed through secrets
# Health checks ensure zero-downtime deployment
```

## Database Migrations

### Development
```bash
# Generate migration from entity changes
npm run migration:generate -- --name=FeatureName

# Run migrations
npm run migration:run

# Revert last migration
npm run migration:revert
```

### Production
```bash
# Migrations run automatically during deployment
# Or manually through container:
docker exec bizmark_backend npm run migration:run
```

## Monitoring & Observability

### Health Monitoring
- Health endpoints return structured JSON
- Integration with monitoring tools (Prometheus, etc.)
- Memory and disk usage monitoring
- Database connection monitoring

### Logging
- Structured JSON logging in production
- Console logging in development
- Different log levels per environment
- File rotation for production logs

## File Storage

### Development
- MinIO with simple credentials
- Local volume storage
- Public bucket policy for development assets

### Production
- MinIO with strong credentials
- Persistent volume storage
- Proper bucket policies for security
- CDN integration ready

## Performance Optimizations

### Connection Pooling
- Development: 1-5 connections
- Production: 5-20 connections
- Proper timeout configurations

### Query Optimization
- Development: All queries logged
- Production: Only slow queries logged
- Redis caching for frequent queries

### Resource Limits
- Memory limits in Docker containers
- Health checks monitor resource usage
- Automatic restart on resource exhaustion

## Security Considerations

### Development
- Relaxed security for ease of development
- Default credentials documented
- No SSL requirements

### Production
- Strong, randomly generated passwords
- SSL/TLS encryption enforced
- Non-root container execution
- Network isolation with Docker networks
- Firewall rules as per deployment guide

## Backup & Recovery

### Development
- Optional local backups
- Easy database reset with Docker volumes

### Production
- Automated daily backups
- Point-in-time recovery capability
- Remote backup storage
- Tested restore procedures

## Troubleshooting

### Common Issues

#### Database Connection Failed
```bash
# Check if services are running
docker-compose -f docker-compose.dev.yml ps

# Check logs
docker-compose -f docker-compose.dev.yml logs postgres

# Test connection
npm run health
```

#### Migration Issues
```bash
# Check migration status
npm run migration:show

# Reset development database
docker-compose -f docker-compose.dev.yml down -v
npm run docker:dev
```

#### Memory Issues
```bash
# Check health endpoint
curl http://localhost:3001/health

# Monitor Docker stats
docker stats
```

## Next Steps for Production Deployment

1. **Environment Setup**: Follow DEPLOYMENT_GUIDE.md for VPS preparation
2. **SSL Configuration**: Setup domain and certificates
3. **Database Backup**: Implement automated backup strategy
4. **Monitoring**: Setup monitoring and alerting
5. **CI/CD**: Implement automated deployment pipeline
6. **Performance Testing**: Load test before production launch

## Development Workflow

```bash
# Daily development
npm run docker:dev:up     # Start infrastructure
npm run start:dev         # Start application
npm run test              # Run tests
npm run docker:dev:down   # Stop infrastructure

# When making schema changes
npm run migration:generate -- --name=MyFeature
npm run migration:run

# Before production deployment
npm run build            # Verify build
npm run test:e2e        # Run full test suite
npm run docker:build    # Build production image
```

Konfigurasi ini memastikan bahwa development environment dapat dengan mudah ditransisikan ke production tanpa perubahan kode yang signifikan, sambil tetap mempertahankan optimisasi yang sesuai untuk masing-masing environment.
