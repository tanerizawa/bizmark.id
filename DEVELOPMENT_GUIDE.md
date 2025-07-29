# Development Guide - Platform SaaS Perizinan UMKM

## Daftar Isi
1. [Gambaran Umum](#gambaran-umum)
2. [Arsitektur Sistem](#arsitektur-sistem)
3. [Setup Lingkungan Development](#setup-lingkungan-development)
4. [Struktur Proyek](#struktur-proyek)
5. [Panduan Development](#panduan-development)
6. [Testing](#testing)
7. [Deployment](#deployment)
8. [Troubleshooting](#troubleshooting)

## Gambaran Umum

Platform SaaS ini dirancang untuk membantu UMKM dalam mengelola perizinan dan administrasi bisnis dengan menggunakan arsitektur **modular-monolith** yang optimal untuk VPS terbatas.

### Stack Teknologi
- **Frontend**: Next.js 14+ dengan TypeScript
- **Backend**: NestJS dengan TypeScript
- **Database**: PostgreSQL dengan Row-Level Security
- **Cache & Queue**: Redis dengan BullMQ
- **Storage**: MinIO (S3-compatible)
- **Reverse Proxy**: Caddy
- **Containerization**: Docker & Docker Compose

### Spesifikasi VPS Target
- RAM: 8GB
- CPU: 2-4 cores
- Storage: SSD/NVMe (minimal 50GB)
- OS: Ubuntu Server 22.04 LTS

## Arsitektur Sistem

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Next.js)     │◄──►│   (NestJS)      │◄──►│   (PostgreSQL)  │
│   Port: 3000    │    │   Port: 3001    │    │   Port: 5432    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         ▲                       ▲                       ▲
         │                       │                       │
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  Reverse Proxy  │    │     Redis       │    │     MinIO       │
│    (Caddy)      │    │  Cache & Queue  │    │   File Storage  │
│   Port: 80/443  │    │   Port: 6379    │    │   Port: 9000    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Arsitektur Modular-Monolith

```
Backend (NestJS)
├── auth/          # Autentikasi & Otorisasi
├── users/         # Manajemen Pengguna
├── licenses/      # Pengelolaan Perizinan
├── documents/     # Manajemen Dokumen
├── reports/       # Sistem Pelaporan
├── notifications/ # Sistem Notifikasi
├── jobs/          # Background Jobs
└── common/        # Shared Components
```

## Setup Lingkungan Development

### Prerequisites
```bash
# Install Node.js (v18+)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
nvm install 18
nvm use 18

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

### Clone & Setup Repository
```bash
# Clone repository
git clone https://github.com/tanerizawa/bizmark.id.git
cd bizmark.id

# Setup environment files
cp .env.example .env
cp frontend/.env.local.example frontend/.env.local
cp backend/.env.example backend/.env
```

### Docker Development Environment
```bash
# Start semua services
docker-compose -f docker-compose.dev.yml up -d

# Services yang akan berjalan:
# - PostgreSQL (port 5432)
# - Redis (port 6379)
# - MinIO (port 9000, 9001)
# - Adminer (port 8080) - Database GUI
```

### Manual Setup (Tanpa Docker)
```bash
# Install dependencies
npm install -g @nestjs/cli
npm install -g create-next-app

# Setup backend
cd backend
npm install
npm run start:dev

# Setup frontend (terminal baru)
cd frontend
npm install
npm run dev
```

## Struktur Proyek

```
bizmark.id/
├── frontend/                 # Next.js Application
│   ├── src/
│   │   ├── app/             # App Router (Next.js 13+)
│   │   ├── components/      # Reusable Components
│   │   │   ├── ui/          # Basic UI Components
│   │   │   └── forms/       # Form Components
│   │   ├── features/        # Feature-based Modules
│   │   │   ├── auth/
│   │   │   ├── dashboard/
│   │   │   ├── licenses/
│   │   │   └── reports/
│   │   ├── lib/             # Utilities & Configurations
│   │   ├── hooks/           # Custom React Hooks
│   │   └── types/           # TypeScript Types
│   ├── public/              # Static Assets
│   └── package.json
├── backend/                  # NestJS Application
│   ├── src/
│   │   ├── auth/            # Authentication Module
│   │   ├── users/           # User Management Module
│   │   ├── licenses/        # License Management Module
│   │   ├── documents/       # Document Management Module
│   │   ├── reports/         # Reporting Module
│   │   ├── notifications/   # Notification Module
│   │   ├── jobs/            # Background Jobs Module
│   │   ├── common/          # Shared Components
│   │   │   ├── decorators/
│   │   │   ├── guards/
│   │   │   ├── interceptors/
│   │   │   └── pipes/
│   │   ├── database/        # Database Configuration
│   │   └── main.ts
│   └── package.json
├── docs/                     # Documentation
├── docker-compose.dev.yml    # Development Environment
├── docker-compose.prod.yml   # Production Environment
├── .github/                  # GitHub Actions
│   └── workflows/
└── README.md
```

## Panduan Development

### 1. Setup Database Schema

```sql
-- Multi-tenant dengan Row-Level Security
CREATE SCHEMA IF NOT EXISTS public;

-- Enable RLS
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE license_applications ENABLE ROW LEVEL SECURITY;
ALTER TABLE documents ENABLE ROW LEVEL SECURITY;

-- Create RLS Policies
CREATE POLICY tenant_isolation ON users
    FOR ALL TO authenticated_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);
```

### 2. Environment Variables

#### Backend (.env)
```env
# Database
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_USERNAME=postgres
DATABASE_PASSWORD=password
DATABASE_NAME=bizmark_db

# JWT
JWT_SECRET=your-super-secret-jwt-key
JWT_REFRESH_SECRET=your-super-secret-refresh-key
JWT_EXPIRATION=15m
JWT_REFRESH_EXPIRATION=7d

# Redis
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# MinIO
MINIO_ENDPOINT=localhost
MINIO_PORT=9000
MINIO_ACCESS_KEY=minioadmin
MINIO_SECRET_KEY=minioadmin
MINIO_BUCKET_NAME=bizmark-documents

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your-email@gmail.com
SMTP_PASS=your-app-password
```

#### Frontend (.env.local)
```env
NEXT_PUBLIC_API_URL=http://localhost:3001
NEXT_PUBLIC_APP_NAME=Bizmark UMKM
NEXTAUTH_SECRET=your-nextauth-secret
NEXTAUTH_URL=http://localhost:3000
```

### 3. Development Workflow

#### Backend Development
```bash
# Generate module baru
nest g module licenses
nest g controller licenses
nest g service licenses

# Generate entity
nest g class licenses/entities/license.entity --no-spec

# Generate DTO
nest g class licenses/dto/create-license.dto --no-spec

# Run dalam development mode
npm run start:dev
```

#### Frontend Development
```bash
# Create new component
npx create-next-component ComponentName

# Run dalam development mode
npm run dev

# Build untuk production
npm run build
npm run start
```

### 4. Database Migrations

```bash
# Generate migration
npm run migration:generate -- src/database/migrations/CreateUsersTable

# Run migrations
npm run migration:run

# Revert migration
npm run migration:revert
```

### 5. Implementasi RBAC

#### Backend Guard
```typescript
// src/auth/guards/roles.guard.ts
@Injectable()
export class RolesGuard implements CanActivate {
  constructor(private reflector: Reflector) {}

  canActivate(context: ExecutionContext): boolean {
    const requiredRoles = this.reflector.getAllAndOverride<Role[]>(ROLES_KEY, [
      context.getHandler(),
      context.getClass(),
    ]);
    
    if (!requiredRoles) return true;
    
    const { user } = context.switchToHttp().getRequest();
    return requiredRoles.some((role) => user.roles?.includes(role));
  }
}
```

#### Usage di Controller
```typescript
@Controller('licenses')
@UseGuards(JwtAuthGuard, RolesGuard)
export class LicensesController {
  @Get()
  @Roles(Role.Admin, Role.Client)
  findAll() {
    return this.licensesService.findAll();
  }

  @Post()
  @Roles(Role.Admin)
  create(@Body() createLicenseDto: CreateLicenseDto) {
    return this.licensesService.create(createLicenseDto);
  }
}
```

### 6. Multi-tenancy Implementation

#### Tenant Middleware
```typescript
// src/common/middleware/tenant.middleware.ts
@Injectable()
export class TenantMiddleware implements NestMiddleware {
  use(req: Request, res: Response, next: NextFunction) {
    const user = req.user as JwtPayload;
    if (user && user.tenantId) {
      req.tenantId = user.tenantId;
    }
    next();
  }
}
```

#### Tenant Interceptor
```typescript
// src/common/interceptors/tenant.interceptor.ts
@Injectable()
export class TenantInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler) {
    const request = context.switchToHttp().getRequest();
    const tenantId = request.tenantId;
    
    if (tenantId) {
      // Set tenant context untuk query
      return next.handle().pipe(
        map(data => ({ ...data, tenantId }))
      );
    }
    
    return next.handle();
  }
}
```

### 7. Background Jobs dengan BullMQ

#### Setup Queue
```typescript
// src/jobs/jobs.module.ts
@Module({
  imports: [
    BullModule.forRoot({
      redis: {
        host: process.env.REDIS_HOST,
        port: parseInt(process.env.REDIS_PORT),
      },
    }),
    BullModule.registerQueue({
      name: 'notifications',
    }),
    BullModule.registerQueue({
      name: 'reports',
    }),
  ],
  providers: [NotificationProcessor, ReportProcessor],
})
export class JobsModule {}
```

#### Job Processor
```typescript
// src/jobs/processors/notification.processor.ts
@Processor('notifications')
export class NotificationProcessor {
  @Process('send-email')
  async handleSendEmail(job: Job<EmailJobData>) {
    const { to, subject, template, data } = job.data;
    // Implement email sending logic
    await this.emailService.sendEmail(to, subject, template, data);
  }

  @Process('send-sms')
  async handleSendSms(job: Job<SmsJobData>) {
    const { phone, message } = job.data;
    // Implement SMS sending logic
    await this.smsService.sendSms(phone, message);
  }
}
```

### 8. File Upload dengan MinIO

```typescript
// src/documents/documents.service.ts
@Injectable()
export class DocumentsService {
  constructor(
    @Inject('MINIO_CLIENT')
    private readonly minioClient: Client,
  ) {}

  async uploadDocument(
    file: Express.Multer.File,
    tenantId: string,
    userId: string,
  ): Promise<Document> {
    const fileName = `${tenantId}/${userId}/${Date.now()}-${file.originalname}`;
    
    await this.minioClient.putObject(
      process.env.MINIO_BUCKET_NAME,
      fileName,
      file.buffer,
      file.size,
      {
        'Content-Type': file.mimetype,
      },
    );

    return this.documentsRepository.save({
      fileName,
      originalName: file.originalname,
      mimeType: file.mimetype,
      size: file.size,
      tenantId,
      uploadedBy: userId,
    });
  }
}
```

## Testing

### Unit Testing
```bash
# Run semua tests
npm run test

# Run tests dengan coverage
npm run test:cov

# Run tests dalam watch mode
npm run test:watch
```

### Integration Testing
```bash
# Run e2e tests
npm run test:e2e
```

### Example Test
```typescript
// src/licenses/licenses.service.spec.ts
describe('LicensesService', () => {
  let service: LicensesService;
  let repository: Repository<License>;

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        LicensesService,
        {
          provide: getRepositoryToken(License),
          useClass: Repository,
        },
      ],
    }).compile();

    service = module.get<LicensesService>(LicensesService);
    repository = module.get<Repository<License>>(getRepositoryToken(License));
  });

  it('should create a license', async () => {
    const createLicenseDto = {
      name: 'Test License',
      type: 'business',
    };

    jest.spyOn(repository, 'save').mockResolvedValue(createLicenseDto as License);

    const result = await service.create(createLicenseDto);
    expect(result).toEqual(createLicenseDto);
  });
});
```

## Deployment

### Production Build
```bash
# Build backend
cd backend
npm run build

# Build frontend
cd frontend
npm run build
```

### Docker Production
```bash
# Build production images
docker-compose -f docker-compose.prod.yml build

# Deploy
docker-compose -f docker-compose.prod.yml up -d
```

### CI/CD dengan GitHub Actions
```yaml
# .github/workflows/deploy.yml
name: Deploy to VPS

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy to VPS
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.VPS_HOST }}
          username: ${{ secrets.VPS_USER }}
          key: ${{ secrets.VPS_SSH_KEY }}
          script: |
            cd /var/www/bizmark.id
            git pull origin main
            docker-compose -f docker-compose.prod.yml down
            docker-compose -f docker-compose.prod.yml up -d --build
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check PostgreSQL status
sudo systemctl status postgresql

# Check connection
psql -h localhost -U postgres -d bizmark_db
```

#### 2. Redis Connection Error
```bash
# Check Redis status
sudo systemctl status redis

# Test connection
redis-cli ping
```

#### 3. MinIO Access Error
```bash
# Check MinIO status
docker logs minio

# Access MinIO console
http://localhost:9001
```

#### 4. Memory Issues pada VPS
```bash
# Check memory usage
free -h

# Optimize PostgreSQL
# Edit /etc/postgresql/14/main/postgresql.conf
shared_buffers = 2GB
work_mem = 64MB
effective_cache_size = 6GB
```

### Performance Optimization

#### 1. Database Indexing
```sql
-- Index untuk query yang sering digunakan
CREATE INDEX idx_users_tenant_id ON users(tenant_id);
CREATE INDEX idx_licenses_status ON license_applications(status);
CREATE INDEX idx_documents_created_at ON documents(created_at);
```

#### 2. Redis Caching
```typescript
// Cache frequently accessed data
@Injectable()
export class CacheService {
  constructor(@Inject(CACHE_MANAGER) private cacheManager: Cache) {}

  async get(key: string): Promise<any> {
    return await this.cacheManager.get(key);
  }

  async set(key: string, value: any, ttl = 3600): Promise<void> {
    await this.cacheManager.set(key, value, ttl);
  }
}
```

#### 3. Frontend Optimization
```typescript
// Dynamic imports untuk code splitting
const Dashboard = dynamic(() => import('../components/Dashboard'), {
  loading: () => <Spinner />,
});

// Image optimization
import Image from 'next/image';

<Image
  src="/logo.png"
  alt="Logo"
  width={200}
  height={100}
  priority
/>
```

Dokumentasi ini akan terus diperbarui seiring dengan perkembangan proyek. Pastikan untuk selalu merujuk ke versi terbaru sebelum memulai development.
