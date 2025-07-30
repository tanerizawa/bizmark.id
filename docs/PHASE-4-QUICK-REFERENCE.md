# Phase 4: Quick Reference

## 🚀 Quick Commands

### Start Full Development Environment
```bash
./scripts/start-fullstack-dev.sh
```

### Test Integration
```bash
./scripts/test-integration.sh
```

### Individual Services
```bash
# Backend only
./scripts/start-backend-dev.sh

# Frontend only (after backend is running)
cd frontend && npm run dev
```

## 🌐 Access Points

| Service | URL | Description |
|---------|-----|-------------|
| Frontend | http://localhost:3000 | Main application |
| Backend API | http://localhost:3001 | REST API |
| API Docs | http://localhost:3001/api/docs | Swagger documentation |
| Integration Dashboard | http://localhost:3000/dashboard/backend-integration | Backend monitoring |
| MinIO Console | http://localhost:9001 | File storage admin |

## 🔧 Key Features

### Backend API Service (`/frontend/src/services/backendApi.ts`)
```typescript
// Authentication
await backendApi.login(email, password)
await backendApi.register(userData)
await backendApi.logout()

// Business Management  
await backendApi.getBusinesses()
await backendApi.createBusiness(data)
await backendApi.updateBusiness(id, data)
await backendApi.deleteBusiness(id)

// File Management
await backendApi.uploadFile(file)
await backendApi.deleteFile(fileId)

// Health Checks
await backendApi.healthCheck()
```

### React Hooks (`/frontend/src/hooks/useBackendApi.ts`)
```typescript
// Authentication
const { user, login, logout, loading } = useAuth()

// Business Operations
const { businesses, loading, error } = useBusinesses()
const { createBusiness, updateBusiness, deleteBusiness } = useBusinessMutations()

// File Upload
const { uploadFile, uploading, progress } = useFileUpload()

// Monitoring
const { isHealthy, healthData } = useHealthCheck()
const { isConnected, connectionStatus } = useConnectionStatus()
```

## 🐳 Docker Services

### Development Stack
- **PostgreSQL**: Database (Port 5432)
- **Redis**: Cache/Sessions (Port 6379)  
- **MinIO**: File Storage (Port 9000/9001)
- **NestJS**: Backend API (Port 3001)
- **Next.js**: Frontend (Port 3000)

### Quick Docker Commands
```bash
# View running containers
docker ps

# View logs
docker logs bizmark-backend
docker logs bizmark-postgres
docker logs bizmark-redis
docker logs bizmark-minio

# Reset services
docker-compose down
docker-compose up -d
```

## 🛠️ Troubleshooting

### Common Issues

**Port conflicts:**
```bash
lsof -ti:3000 | xargs kill -9  # Kill frontend
lsof -ti:3001 | xargs kill -9  # Kill backend
```

**Docker issues:**
```bash
docker system prune -f        # Clean up
docker-compose down           # Stop all
docker-compose up -d          # Restart all
```

**Database reset:**
```bash
docker-compose down
docker volume rm bizmark_postgres_data
docker-compose up -d
```

## ✅ Health Checks

### Manual Verification
```bash
# Backend health
curl http://localhost:3001/health

# Frontend access  
curl http://localhost:3000

# Database connection
docker exec -it bizmark-postgres pg_isready

# Redis connection
docker exec -it bizmark-redis redis-cli ping
```

### Integration Dashboard
Access comprehensive monitoring at:
http://localhost:3000/dashboard/backend-integration

Features:
- Real-time connection status
- API endpoint testing
- Health check results
- Performance metrics
- Error monitoring

## 📝 Environment Variables

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

## 🚦 Status Indicators

### Green (Healthy)
✅ All services running  
✅ Database connected  
✅ Authentication working  
✅ File uploads functional  

### Yellow (Warning)
⚠️ Slow response times  
⚠️ Connection intermittent  
⚠️ Some features degraded  

### Red (Critical)
❌ Service unavailable  
❌ Database disconnected  
❌ Authentication failing  
❌ File uploads broken  

## 📚 Documentation

- **Full Documentation**: `/docs/PHASE-4-BACKEND-INTEGRATION.md`
- **API Reference**: http://localhost:3001/api/docs
- **Component Guide**: `/frontend/src/components/README.md`
- **Backend Guide**: `/backend/README.md`

## 🔄 Next Steps

After Phase 4 completion:

1. **Phase 5: Advanced Features**
   - Real-time notifications with WebSocket
   - Advanced workflow automation
   - Government API integrations
   - Enhanced reporting systems

2. **Production Deployment**
   - Environment setup
   - SSL certificates
   - Load balancing
   - Monitoring setup
