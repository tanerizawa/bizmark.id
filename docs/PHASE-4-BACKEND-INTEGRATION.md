# Phase 4: Backend Integration & Production Deployment

## Overview

Phase 4 implementasi lengkap backend integration dengan NestJS, PostgreSQL, Redis, dan MinIO untuk production-ready SaaS Perizinan UMKM.

## Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Next.js       │    │    NestJS       │    │   PostgreSQL    │
│   Frontend      │◄──►│   Backend       │◄──►│   Database      │
│   (Port 3000)   │    │   (Port 3001)   │    │   (Port 5432)   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         │                       ▼                       │
         │              ┌─────────────────┐              │
         │              │     Redis       │              │
         └──────────────►│   Cache/Queue   │◄─────────────┘
                        │   (Port 6379)   │
                        └─────────────────┘
                                 │
                                 ▼
                        ┌─────────────────┐
                        │     MinIO       │
                        │  File Storage   │
                        │ (Port 9000/9001)│
                        └─────────────────┘
```

## Components

### 1. Backend API Service (`/frontend/src/services/backendApi.ts`)

Central service untuk komunikasi dengan backend:

- **Authentication**: Login, register, logout, token refresh
- **Business Management**: CRUD operations untuk data bisnis
- **File Management**: Upload dan download file documents
- **Health Checks**: Monitoring status backend services

### 2. React Hooks (`/frontend/src/hooks/useBackendApi.ts`)

React hooks untuk integrasi seamless:

- `useAuth()`: Authentication state management
- `useBusinesses()`: Business data fetching
- `useBusinessMutations()`: Business CRUD operations
- `useFileUpload()`: File upload functionality
- `useHealthCheck()`: Backend health monitoring
- `useConnectionStatus()`: Real-time connection status

### 3. Integration Dashboard (`/frontend/src/app/dashboard/backend-integration/page.tsx`)

Dashboard monitoring untuk:

- Connection status dengan backend
- Authentication state
- Health check results
- API endpoints testing
- Performance metrics

### 4. Development Scripts

#### Full Stack Startup (`/scripts/start-fullstack-dev.sh`)
```bash
./scripts/start-fullstack-dev.sh
```

Features:
- Automated Docker services startup
- Database migration dan seeding
- Frontend dan backend startup
- Health checks dan readiness verification
- Graceful shutdown handling

#### Integration Testing (`/scripts/test-integration.sh`)
```bash
./scripts/test-integration.sh
```

Features:
- Service connectivity testing
- API endpoint validation
- Database container verification
- Integration dashboard testing
- Comprehensive test reporting

## Getting Started

### Prerequisites

1. **Docker Desktop** - untuk development services
2. **Node.js 18+** - untuk frontend dan backend
3. **Git** - untuk version control

### Quick Start

1. **Clone dan setup project:**
```bash
git clone <repository-url>
cd bizmark.id
```

2. **Start full development environment:**
```bash
./scripts/start-fullstack-dev.sh
```

3. **Verify integration:**
```bash
./scripts/test-integration.sh
```

4. **Access applications:**
- Frontend: http://localhost:3000
- Backend API: http://localhost:3001
- API Documentation: http://localhost:3001/api/docs
- Integration Dashboard: http://localhost:3000/dashboard/backend-integration

## Backend Services

### NestJS Backend (Port 3001)

```typescript
// Authentication
POST /api/auth/login
POST /api/auth/register
POST /api/auth/refresh
POST /api/auth/logout

// Business Management
GET    /api/businesses
POST   /api/businesses
GET    /api/businesses/:id
PUT    /api/businesses/:id
DELETE /api/businesses/:id

// File Management
POST   /api/files/upload
GET    /api/files/:id
DELETE /api/files/:id

// Health Check
GET    /health
```

### Database Services

#### PostgreSQL (Port 5432)
- Database: `bizmark_dev`
- Schema: Users, Businesses, Documents, Permits
- Migrations: Automated dengan TypeORM
- Seeding: Development data

#### Redis (Port 6379)
- Session storage
- Cache layer
- Job queue (future)

#### MinIO (Port 9000/9001)
- Document storage
- File upload handling
- S3-compatible API

## Frontend Integration

### API Service Usage

```typescript
import { backendApi } from '@/services/backendApi';

// Authentication
const { data: user } = await backendApi.login(email, password);

// Business operations
const businesses = await backendApi.getBusinesses();
const business = await backendApi.createBusiness(businessData);

// File upload
const uploadResult = await backendApi.uploadFile(file);
```

### React Hooks Usage

```typescript
import { useAuth, useBusinesses, useFileUpload } from '@/hooks/useBackendApi';

function BusinessComponent() {
  const { user, login, logout } = useAuth();
  const { businesses, loading, error } = useBusinesses();
  const { uploadFile, uploading } = useFileUpload();
  
  // Component logic...
}
```

## Environment Configuration

### Frontend (.env.local)
```env
NEXT_PUBLIC_API_URL=http://localhost:3001
NEXT_PUBLIC_APP_ENV=development
```

### Backend (.env)
```env
DATABASE_URL=postgresql://postgres:password@localhost:5432/bizmark_dev
REDIS_URL=redis://localhost:6379
MINIO_ENDPOINT=localhost
MINIO_PORT=9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
JWT_SECRET=your-jwt-secret
```

## Production Deployment

### Docker Production Setup

1. **Build production images:**
```bash
docker-compose -f docker-compose.prod.yml build
```

2. **Deploy with orchestration:**
```bash
docker-compose -f docker-compose.prod.yml up -d
```

### Environment Variables (Production)

```env
# Database
DATABASE_URL=postgresql://user:pass@db:5432/bizmark_prod
REDIS_URL=redis://redis:6379

# MinIO
MINIO_ENDPOINT=minio
MINIO_ROOT_USER=admin
MINIO_ROOT_PASSWORD=securepassword

# Security
JWT_SECRET=production-jwt-secret-key
BCRYPT_ROUNDS=12

# API
API_URL=https://api.yourdomain.com
FRONTEND_URL=https://yourdomain.com
```

## Monitoring & Debugging

### Health Checks

1. **Backend Health:**
```bash
curl http://localhost:3001/health
```

2. **Database Connection:**
```bash
docker exec -it bizmark-postgres psql -U postgres -d bizmark_dev
```

3. **Redis Connection:**
```bash
docker exec -it bizmark-redis redis-cli ping
```

### Logs

```bash
# Backend logs
docker logs bizmark-backend

# Database logs
docker logs bizmark-postgres

# Frontend logs (development)
npm run dev --prefix frontend
```

## Troubleshooting

### Common Issues

1. **Port already in use:**
```bash
lsof -ti:3000 | xargs kill -9  # Kill process on port 3000
lsof -ti:3001 | xargs kill -9  # Kill process on port 3001
```

2. **Docker services not starting:**
```bash
docker-compose down
docker system prune -f
docker-compose up -d
```

3. **Database connection issues:**
```bash
# Reset database
docker-compose down
docker volume rm bizmark_postgres_data
docker-compose up -d
```

4. **Integration test failures:**
- Check all services are running
- Verify environment variables
- Check firewall/network settings
- Review logs for specific errors

## Security Considerations

### Development
- JWT tokens dengan short expiration
- Password hashing dengan bcrypt
- Rate limiting pada API endpoints
- CORS configuration

### Production
- HTTPS enforcement
- Environment variables encryption
- Database connection encryption
- File upload validation
- API authentication middleware

## Next Steps

1. **Phase 5: Advanced Features**
   - Real-time notifications
   - Advanced reporting
   - Workflow automation
   - Integration dengan sistem perizinan

2. **Phase 6: Scale & Optimize**
   - Load balancing
   - Database optimization
   - Caching strategies
   - Performance monitoring

## Support

Untuk troubleshooting dan support:

1. Check integration dashboard: http://localhost:3000/dashboard/backend-integration
2. Run integration tests: `./scripts/test-integration.sh`
3. Review logs untuk specific errors
4. Consult documentation di `/docs` directory
