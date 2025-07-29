# Security Guide - Platform SaaS Perizinan UMKM

## Daftar Isi
1. [Overview Keamanan](#overview-keamanan)
2. [Authentication & Authorization](#authentication--authorization)
3. [Data Protection](#data-protection)
4. [Input Validation & Sanitization](#input-validation--sanitization)
5. [API Security](#api-security)
6. [Infrastructure Security](#infrastructure-security)
7. [Multi-tenancy Security](#multi-tenancy-security)
8. [Audit & Compliance](#audit--compliance)
9. [Security Monitoring](#security-monitoring)
10. [Incident Response](#incident-response)

## Overview Keamanan

### Threat Model
Platform SaaS perizinan UMKM menangani data sensitif termasuk:
- Informasi pribadi (KTP, NPWP)
- Data keuangan dan pajak
- Dokumen resmi dan izin
- Informasi bisnis rahasia

### Security Principles
1. **Defense in Depth**: Berlapis-lapis keamanan di setiap level
2. **Least Privilege**: Akses minimal yang diperlukan
3. **Zero Trust**: Verifikasi semua akses
4. **Data Classification**: Klasifikasi dan perlindungan data sesuai tingkat sensitivitas
5. **Compliance**: Mematuhi regulasi GDPR, SOC 2, dan standar lokal

## Authentication & Authorization

### JWT Implementation

#### JWT Configuration
```typescript
// backend/src/auth/jwt.strategy.ts
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PassportStrategy } from '@nestjs/passport';
import { ExtractJwt, Strategy } from 'passport-jwt';
import { ConfigService } from '@nestjs/config';
import { AuthService } from './auth.service';

@Injectable()
export class JwtStrategy extends PassportStrategy(Strategy) {
  constructor(
    private configService: ConfigService,
    private authService: AuthService,
  ) {
    super({
      jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
      ignoreExpiration: false,
      secretOrKey: configService.get<string>('JWT_SECRET'),
      algorithms: ['HS256'],
    });
  }

  async validate(payload: any) {
    const user = await this.authService.validateJwtPayload(payload);
    if (!user) {
      throw new UnauthorizedException('Invalid token');
    }
    return user;
  }
}
```

#### Secure Token Generation
```typescript
// backend/src/auth/auth.service.ts
import { Injectable } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import { ConfigService } from '@nestjs/config';
import * as crypto from 'crypto';
import * as bcrypt from 'bcrypt';

@Injectable()
export class AuthService {
  constructor(
    private jwtService: JwtService,
    private configService: ConfigService,
  ) {}

  async generateTokens(user: any) {
    const payload = {
      sub: user.id,
      email: user.email,
      role: user.role,
      tenantId: user.tenantId,
      iat: Math.floor(Date.now() / 1000),
      jti: crypto.randomUUID(), // JWT ID for tracking
    };

    const accessToken = this.jwtService.sign(payload, {
      expiresIn: '15m',
      issuer: 'bizmark-api',
      audience: 'bizmark-client',
    });

    const refreshToken = this.jwtService.sign(
      { sub: user.id, type: 'refresh', jti: payload.jti },
      {
        secret: this.configService.get<string>('JWT_REFRESH_SECRET'),
        expiresIn: '7d',
        issuer: 'bizmark-api',
        audience: 'bizmark-client',
      },
    );

    // Store refresh token hash in database
    const refreshTokenHash = await bcrypt.hash(refreshToken, 12);
    await this.storeRefreshToken(user.id, refreshTokenHash, payload.jti);

    return {
      accessToken,
      refreshToken,
      expiresIn: 900, // 15 minutes
      tokenType: 'Bearer',
    };
  }

  async hashPassword(password: string): Promise<string> {
    const saltRounds = 12;
    return bcrypt.hash(password, saltRounds);
  }

  async validatePassword(password: string, hash: string): Promise<boolean> {
    return bcrypt.compare(password, hash);
  }
}
```

### Role-Based Access Control (RBAC)

#### Role & Permission System
```typescript
// backend/src/auth/roles.enum.ts
export enum Role {
  SUPER_ADMIN = 'super_admin',
  ADMIN = 'admin',
  CLIENT = 'client',
  CONSULTANT = 'consultant',
}

export enum Permission {
  // User management
  CREATE_USER = 'create:user',
  READ_USER = 'read:user',
  UPDATE_USER = 'update:user',
  DELETE_USER = 'delete:user',
  
  // License management
  CREATE_LICENSE_TYPE = 'create:license_type',
  READ_LICENSE_TYPE = 'read:license_type',
  UPDATE_LICENSE_TYPE = 'update:license_type',
  DELETE_LICENSE_TYPE = 'delete:license_type',
  
  CREATE_LICENSE_APPLICATION = 'create:license_application',
  READ_LICENSE_APPLICATION = 'read:license_application',
  UPDATE_LICENSE_APPLICATION = 'update:license_application',
  APPROVE_LICENSE_APPLICATION = 'approve:license_application',
  
  // Document management
  UPLOAD_DOCUMENT = 'upload:document',
  READ_DOCUMENT = 'read:document',
  DELETE_DOCUMENT = 'delete:document',
  
  // Reports
  GENERATE_REPORT = 'generate:report',
  READ_FINANCIAL_REPORT = 'read:financial_report',
  READ_TAX_REPORT = 'read:tax_report',
}

// Role-Permission mapping
export const ROLE_PERMISSIONS: Record<Role, Permission[]> = {
  [Role.SUPER_ADMIN]: Object.values(Permission),
  [Role.ADMIN]: [
    Permission.CREATE_USER,
    Permission.READ_USER,
    Permission.UPDATE_USER,
    Permission.CREATE_LICENSE_TYPE,
    Permission.READ_LICENSE_TYPE,
    Permission.UPDATE_LICENSE_TYPE,
    Permission.READ_LICENSE_APPLICATION,
    Permission.UPDATE_LICENSE_APPLICATION,
    Permission.APPROVE_LICENSE_APPLICATION,
    Permission.READ_DOCUMENT,
    Permission.GENERATE_REPORT,
    Permission.READ_FINANCIAL_REPORT,
    Permission.READ_TAX_REPORT,
  ],
  [Role.CLIENT]: [
    Permission.READ_USER,
    Permission.UPDATE_USER,
    Permission.READ_LICENSE_TYPE,
    Permission.CREATE_LICENSE_APPLICATION,
    Permission.READ_LICENSE_APPLICATION,
    Permission.UPDATE_LICENSE_APPLICATION,
    Permission.UPLOAD_DOCUMENT,
    Permission.READ_DOCUMENT,
    Permission.DELETE_DOCUMENT,
    Permission.GENERATE_REPORT,
  ],
  [Role.CONSULTANT]: [
    Permission.READ_USER,
    Permission.READ_LICENSE_TYPE,
    Permission.READ_LICENSE_APPLICATION,
    Permission.UPDATE_LICENSE_APPLICATION,
    Permission.APPROVE_LICENSE_APPLICATION,
    Permission.READ_DOCUMENT,
    Permission.GENERATE_REPORT,
    Permission.READ_FINANCIAL_REPORT,
  ],
};
```

#### Permission Guard
```typescript
// backend/src/auth/guards/permissions.guard.ts
import { Injectable, CanActivate, ExecutionContext, ForbiddenException } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { Permission, ROLE_PERMISSIONS } from '../roles.enum';
import { PERMISSIONS_KEY } from '../decorators/permissions.decorator';

@Injectable()
export class PermissionsGuard implements CanActivate {
  constructor(private reflector: Reflector) {}

  canActivate(context: ExecutionContext): boolean {
    const requiredPermissions = this.reflector.getAllAndOverride<Permission[]>(
      PERMISSIONS_KEY,
      [context.getHandler(), context.getClass()],
    );

    if (!requiredPermissions) {
      return true;
    }

    const { user } = context.switchToHttp().getRequest();
    if (!user) {
      throw new ForbiddenException('User not authenticated');
    }

    const userPermissions = ROLE_PERMISSIONS[user.role] || [];
    const hasPermission = requiredPermissions.every(permission =>
      userPermissions.includes(permission),
    );

    if (!hasPermission) {
      throw new ForbiddenException('Insufficient permissions');
    }

    return true;
  }
}
```

#### Usage in Controllers
```typescript
// backend/src/licenses/licenses.controller.ts
import { Controller, Get, Post, UseGuards } from '@nestjs/common';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';
import { PermissionsGuard } from '../auth/guards/permissions.guard';
import { RequirePermissions } from '../auth/decorators/permissions.decorator';
import { Permission } from '../auth/roles.enum';

@Controller('licenses')
@UseGuards(JwtAuthGuard, PermissionsGuard)
export class LicensesController {
  @Get('types')
  @RequirePermissions(Permission.READ_LICENSE_TYPE)
  findAllTypes() {
    return this.licensesService.findAllTypes();
  }

  @Post('types')
  @RequirePermissions(Permission.CREATE_LICENSE_TYPE)
  createType(@Body() createLicenseTypeDto: CreateLicenseTypeDto) {
    return this.licensesService.createType(createLicenseTypeDto);
  }
}
```

### Multi-Factor Authentication (MFA)

#### TOTP Implementation
```typescript
// backend/src/auth/mfa.service.ts
import { Injectable } from '@nestjs/common';
import * as speakeasy from 'speakeasy';
import * as qrcode from 'qrcode';

@Injectable()
export class MfaService {
  async generateSecret(userEmail: string) {
    const secret = speakeasy.generateSecret({
      name: userEmail,
      issuer: 'Bizmark UMKM',
      length: 32,
    });

    const qrCodeUrl = await qrcode.toDataURL(secret.otpauth_url);

    return {
      secret: secret.base32,
      qrCode: qrCodeUrl,
    };
  }

  verifyToken(token: string, secret: string): boolean {
    return speakeasy.totp.verify({
      secret,
      encoding: 'base32',
      token,
      window: 2, // Allow 2 time steps (60 sec) tolerance
    });
  }

  async enableMfa(userId: string, secret: string) {
    // Store encrypted secret in database
    const encryptedSecret = await this.encryptSecret(secret);
    await this.userRepository.update(userId, {
      mfaSecret: encryptedSecret,
      mfaEnabled: true,
    });
  }

  private async encryptSecret(secret: string): Promise<string> {
    // Implement encryption using crypto module
    const cipher = crypto.createCipher('aes-256-cbc', process.env.MFA_ENCRYPTION_KEY);
    let encrypted = cipher.update(secret, 'utf8', 'hex');
    encrypted += cipher.final('hex');
    return encrypted;
  }
}
```

## Data Protection

### Data Encryption

#### Database Encryption
```sql
-- Enable pgcrypto extension
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Encrypt sensitive columns
CREATE TABLE user_profiles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL,
    encrypted_npwp BYTEA, -- Encrypted NPWP
    encrypted_phone BYTEA, -- Encrypted phone
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Functions for encryption/decryption
CREATE OR REPLACE FUNCTION encrypt_pii(data TEXT)
RETURNS BYTEA AS $$
BEGIN
    RETURN pgp_sym_encrypt(data, current_setting('app.encryption_key'));
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

CREATE OR REPLACE FUNCTION decrypt_pii(encrypted_data BYTEA)
RETURNS TEXT AS $$
BEGIN
    RETURN pgp_sym_decrypt(encrypted_data, current_setting('app.encryption_key'));
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
```

#### Application-Level Encryption
```typescript
// backend/src/common/encryption/encryption.service.ts
import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as crypto from 'crypto';

@Injectable()
export class EncryptionService {
  private readonly algorithm = 'aes-256-gcm';
  private readonly keyLength = 32;
  private readonly ivLength = 16;
  private readonly tagLength = 16;

  constructor(private configService: ConfigService) {}

  encrypt(text: string): string {
    const key = Buffer.from(this.configService.get<string>('ENCRYPTION_KEY'), 'hex');
    const iv = crypto.randomBytes(this.ivLength);
    const cipher = crypto.createCipher(this.algorithm, key);
    cipher.setAAD(Buffer.from('bizmark-aad'));
    
    let encrypted = cipher.update(text, 'utf8', 'hex');
    encrypted += cipher.final('hex');
    
    const tag = cipher.getAuthTag();
    
    return iv.toString('hex') + ':' + tag.toString('hex') + ':' + encrypted;
  }

  decrypt(encryptedData: string): string {
    const parts = encryptedData.split(':');
    const iv = Buffer.from(parts[0], 'hex');
    const tag = Buffer.from(parts[1], 'hex');
    const encrypted = parts[2];
    
    const key = Buffer.from(this.configService.get<string>('ENCRYPTION_KEY'), 'hex');
    const decipher = crypto.createDecipher(this.algorithm, key);
    decipher.setAAD(Buffer.from('bizmark-aad'));
    decipher.setAuthTag(tag);
    
    let decrypted = decipher.update(encrypted, 'hex', 'utf8');
    decrypted += decipher.final('utf8');
    
    return decrypted;
  }

  hashSensitiveData(data: string): string {
    return crypto.createHash('sha256').update(data).digest('hex');
  }
}
```

### File Storage Security

#### Secure File Upload
```typescript
// backend/src/documents/documents.service.ts
import { Injectable, BadRequestException } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import * as crypto from 'crypto';
import * as path from 'path';
import * as sharp from 'sharp';

@Injectable()
export class DocumentsService {
  private readonly allowedMimeTypes = [
    'application/pdf',
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  ];

  private readonly maxFileSize = 10 * 1024 * 1024; // 10MB

  async uploadDocument(
    file: Express.Multer.File,
    userId: string,
    tenantId: string,
  ) {
    // Validate file
    this.validateFile(file);
    
    // Scan for malware (integrate with ClamAV or similar)
    await this.scanForMalware(file);
    
    // Generate secure filename
    const secureFileName = this.generateSecureFileName(file.originalname);
    const filePath = `${tenantId}/${userId}/${secureFileName}`;
    
    // Calculate file hash for integrity check
    const fileHash = crypto.createHash('sha256').update(file.buffer).digest('hex');
    
    // Encrypt file if sensitive
    let processedBuffer = file.buffer;
    if (this.isSensitiveDocument(file)) {
      processedBuffer = await this.encryptFile(file.buffer);
    }
    
    // Upload to secure storage
    await this.minioService.uploadFile(filePath, processedBuffer);
    
    // Store metadata in database
    return this.documentsRepository.save({
      fileName: secureFileName,
      originalName: file.originalname,
      filePath,
      mimeType: file.mimetype,
      size: file.size,
      checksum: fileHash,
      isEncrypted: this.isSensitiveDocument(file),
      uploadedBy: userId,
      tenantId,
    });
  }

  private validateFile(file: Express.Multer.File) {
    // Check file size
    if (file.size > this.maxFileSize) {
      throw new BadRequestException('File size exceeds limit');
    }

    // Check MIME type
    if (!this.allowedMimeTypes.includes(file.mimetype)) {
      throw new BadRequestException('File type not allowed');
    }

    // Additional file header validation
    if (!this.validateFileHeader(file)) {
      throw new BadRequestException('Invalid file format');
    }
  }

  private validateFileHeader(file: Express.Multer.File): boolean {
    const fileSignatures = {
      'application/pdf': [0x25, 0x50, 0x44, 0x46], // %PDF
      'image/jpeg': [0xFF, 0xD8, 0xFF],
      'image/png': [0x89, 0x50, 0x4E, 0x47],
    };

    const signature = fileSignatures[file.mimetype];
    if (!signature) return true; // Skip validation for unknown types

    const header = Array.from(file.buffer.slice(0, signature.length));
    return signature.every((byte, index) => header[index] === byte);
  }

  private generateSecureFileName(originalName: string): string {
    const ext = path.extname(originalName);
    const timestamp = Date.now();
    const random = crypto.randomBytes(16).toString('hex');
    return `${timestamp}-${random}${ext}`;
  }

  private async encryptFile(buffer: Buffer): Promise<Buffer> {
    // Implement file encryption
    const key = crypto.randomBytes(32);
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipher('aes-256-cbc', key);
    
    const encrypted = Buffer.concat([cipher.update(buffer), cipher.final()]);
    
    // Store key securely (encrypted with master key)
    // This is a simplified example
    return Buffer.concat([iv, encrypted]);
  }
}
```

## Input Validation & Sanitization

### Comprehensive Validation
```typescript
// backend/src/common/dto/base.dto.ts
import { IsNotEmpty, IsString, Length, Matches, IsEmail, IsOptional } from 'class-validator';
import { Transform } from 'class-transformer';
import { sanitizeHtml } from '../utils/sanitizer';

export class CreateUserDto {
  @IsEmail({}, { message: 'Invalid email format' })
  @Transform(({ value }) => value?.toLowerCase().trim())
  email: string;

  @IsString({ message: 'Password must be a string' })
  @Length(8, 128, { message: 'Password must be between 8 and 128 characters' })
  @Matches(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/, {
    message: 'Password must contain uppercase, lowercase, number and special character',
  })
  password: string;

  @IsString({ message: 'First name must be a string' })
  @Length(1, 50, { message: 'First name must be between 1 and 50 characters' })
  @Transform(({ value }) => sanitizeHtml(value?.trim()))
  @Matches(/^[a-zA-Z\s]+$/, { message: 'First name can only contain letters and spaces' })
  firstName: string;

  @IsString({ message: 'Last name must be a string' })
  @Length(1, 50, { message: 'Last name must be between 1 and 50 characters' })
  @Transform(({ value }) => sanitizeHtml(value?.trim()))
  @Matches(/^[a-zA-Z\s]+$/, { message: 'Last name can only contain letters and spaces' })
  lastName: string;

  @IsOptional()
  @IsString({ message: 'Company name must be a string' })
  @Length(1, 100, { message: 'Company name must be between 1 and 100 characters' })
  @Transform(({ value }) => sanitizeHtml(value?.trim()))
  companyName?: string;

  @IsOptional()
  @IsString({ message: 'Phone must be a string' })
  @Matches(/^\+?[1-9]\d{1,14}$/, { message: 'Invalid phone number format' })
  phone?: string;
}
```

### SQL Injection Prevention
```typescript
// backend/src/common/utils/query-builder.ts
import { SelectQueryBuilder } from 'typeorm';
import { BadRequestException } from '@nestjs/common';

export class SafeQueryBuilder {
  static addWhereCondition<T>(
    qb: SelectQueryBuilder<T>,
    field: string,
    value: any,
    operator: '=' | 'LIKE' | 'IN' | '>' | '<' = '=',
  ) {
    // Whitelist allowed fields to prevent column injection
    const allowedFields = [
      'id', 'email', 'firstName', 'lastName', 'status', 'createdAt', 'updatedAt'
    ];
    
    if (!allowedFields.includes(field)) {
      throw new BadRequestException(`Invalid field: ${field}`);
    }

    // Use parameterized queries
    const paramName = `param_${Math.random().toString(36).substring(7)}`;
    
    switch (operator) {
      case 'LIKE':
        qb.andWhere(`${field} LIKE :${paramName}`, { [paramName]: `%${value}%` });
        break;
      case 'IN':
        qb.andWhere(`${field} IN (:...${paramName})`, { [paramName]: value });
        break;
      default:
        qb.andWhere(`${field} ${operator} :${paramName}`, { [paramName]: value });
    }

    return qb;
  }
}
```

### XSS Prevention
```typescript
// backend/src/common/utils/sanitizer.ts
import * as DOMPurify from 'isomorphic-dompurify';
import { JSDOM } from 'jsdom';

const window = new JSDOM('').window;
const purify = DOMPurify(window);

export function sanitizeHtml(dirty: string): string {
  if (!dirty) return '';
  
  // Configure DOMPurify for strict sanitization
  const clean = purify.sanitize(dirty, {
    ALLOWED_TAGS: [], // No HTML tags allowed
    ALLOWED_ATTR: [],
    KEEP_CONTENT: true,
  });
  
  return clean.trim();
}

export function sanitizeForDisplay(dirty: string): string {
  if (!dirty) return '';
  
  const clean = purify.sanitize(dirty, {
    ALLOWED_TAGS: ['b', 'i', 'em', 'strong', 'p', 'br'],
    ALLOWED_ATTR: [],
    KEEP_CONTENT: true,
  });
  
  return clean;
}

// Custom validation decorator
import { registerDecorator, ValidationOptions, ValidationArguments } from 'class-validator';

export function IsNotXSS(validationOptions?: ValidationOptions) {
  return function (object: Object, propertyName: string) {
    registerDecorator({
      name: 'isNotXSS',
      target: object.constructor,
      propertyName: propertyName,
      options: validationOptions,
      validator: {
        validate(value: any, args: ValidationArguments) {
          if (typeof value !== 'string') return true;
          
          // Check for common XSS patterns
          const xssPatterns = [
            /<script[\s\S]*?>[\s\S]*?<\/script>/gi,
            /javascript:/gi,
            /vbscript:/gi,
            /onload\s*=/gi,
            /onerror\s*=/gi,
            /onclick\s*=/gi,
          ];
          
          return !xssPatterns.some(pattern => pattern.test(value));
        },
        defaultMessage(args: ValidationArguments) {
          return 'Input contains potentially malicious content';
        },
      },
    });
  };
}
```

## API Security

### Rate Limiting
```typescript
// backend/src/common/guards/rate-limit.guard.ts
import { Injectable, CanActivate, ExecutionContext, HttpException, HttpStatus } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { Redis } from 'ioredis';
import { InjectRedis } from '@nestjs-modules/ioredis';

@Injectable()
export class RateLimitGuard implements CanActivate {
  constructor(
    private reflector: Reflector,
    @InjectRedis() private readonly redis: Redis,
  ) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const request = context.switchToHttp().getRequest();
    const handler = context.getHandler();
    const classTarget = context.getClass();

    // Get rate limit configuration
    const rateLimit = this.reflector.getAllAndOverride<{
      windowMs: number;
      max: number;
      keyGenerator?: (req: any) => string;
    }>('rateLimit', [handler, classTarget]);

    if (!rateLimit) {
      return true; // No rate limiting configured
    }

    // Generate key for rate limiting
    const key = rateLimit.keyGenerator 
      ? rateLimit.keyGenerator(request)
      : `rate_limit:${request.ip}:${request.route.path}`;

    const windowMs = rateLimit.windowMs;
    const max = rateLimit.max;

    // Sliding window rate limiting
    const now = Date.now();
    const window = Math.floor(now / windowMs);
    const windowKey = `${key}:${window}`;

    const current = await this.redis.incr(windowKey);
    
    if (current === 1) {
      await this.redis.expire(windowKey, Math.ceil(windowMs / 1000));
    }

    if (current > max) {
      throw new HttpException({
        statusCode: HttpStatus.TOO_MANY_REQUESTS,
        message: 'Too many requests',
        error: 'Rate limit exceeded',
      }, HttpStatus.TOO_MANY_REQUESTS);
    }

    // Add rate limit headers
    const response = context.switchToHttp().getResponse();
    response.header('X-RateLimit-Limit', max);
    response.header('X-RateLimit-Remaining', Math.max(0, max - current));
    response.header('X-RateLimit-Reset', new Date(window * windowMs + windowMs));

    return true;
  }
}

// Rate limit decorator
export const RateLimit = (options: { windowMs: number; max: number; keyGenerator?: (req: any) => string }) =>
  SetMetadata('rateLimit', options);
```

### API Usage Examples
```typescript
// backend/src/auth/auth.controller.ts
@Controller('auth')
export class AuthController {
  @Post('login')
  @RateLimit({ windowMs: 15 * 60 * 1000, max: 5 }) // 5 attempts per 15 minutes
  @UseGuards(RateLimitGuard)
  async login(@Body() loginDto: LoginDto) {
    return this.authService.login(loginDto);
  }

  @Post('register')
  @RateLimit({ windowMs: 60 * 60 * 1000, max: 3 }) // 3 registrations per hour
  @UseGuards(RateLimitGuard)
  async register(@Body() registerDto: RegisterDto) {
    return this.authService.register(registerDto);
  }
}

// File upload rate limiting
@Post('upload')
@RateLimit({ 
  windowMs: 60 * 60 * 1000, 
  max: 10,
  keyGenerator: (req) => `upload:${req.user.id}` // Per user limit
})
@UseGuards(JwtAuthGuard, RateLimitGuard)
async uploadDocument(@UploadedFile() file: Express.Multer.File) {
  return this.documentsService.uploadDocument(file);
}
```

### CORS Configuration
```typescript
// backend/src/main.ts
import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';
import helmet from 'helmet';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);

  // Security middleware
  app.use(helmet({
    contentSecurityPolicy: {
      directives: {
        defaultSrc: ["'self'"],
        styleSrc: ["'self'", "'unsafe-inline'"],
        scriptSrc: ["'self'"],
        imgSrc: ["'self'", "data:", "https:"],
        connectSrc: ["'self'"],
        fontSrc: ["'self'"],
        objectSrc: ["'none'"],
        mediaSrc: ["'self'"],
        frameSrc: ["'none'"],
      },
    },
    crossOriginEmbedderPolicy: false,
  }));

  // CORS configuration
  app.enableCors({
    origin: process.env.NODE_ENV === 'production' 
      ? ['https://yourdomain.com', 'https://app.yourdomain.com']
      : ['http://localhost:3000', 'http://127.0.0.1:3000'],
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
    credentials: true,
    maxAge: 86400, // 24 hours
  });

  await app.listen(3001);
}
bootstrap();
```

## Infrastructure Security

### Docker Security
```dockerfile
# Secure Dockerfile example
FROM node:18-alpine AS builder

# Create non-root user
RUN addgroup -g 1001 -S nodejs && \
    adduser -S nextjs -u 1001

# Security updates
RUN apk update && apk upgrade && \
    apk add --no-cache dumb-init && \
    rm -rf /var/cache/apk/*

WORKDIR /app
COPY package*.json ./

# Install dependencies
RUN npm ci --only=production && npm cache clean --force

# Copy source code
COPY --chown=nextjs:nodejs . .

# Build application
RUN npm run build

# Production stage
FROM node:18-alpine AS production

RUN addgroup -g 1001 -S nodejs && \
    adduser -S nextjs -u 1001

RUN apk update && apk upgrade && \
    apk add --no-cache dumb-init curl && \
    rm -rf /var/cache/apk/*

WORKDIR /app

# Copy built application
COPY --from=builder --chown=nextjs:nodejs /app/dist ./dist
COPY --from=builder --chown=nextjs:nodejs /app/node_modules ./node_modules
COPY --from=builder --chown=nextjs:nodejs /app/package.json ./package.json

# Switch to non-root user
USER nextjs

# Security labels
LABEL security.scan="true"
LABEL security.updated="$(date)"

EXPOSE 3001

HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost:3001/health || exit 1

CMD ["dumb-init", "node", "dist/main.js"]
```

### Server Hardening
```bash
#!/bin/bash
# server-hardening.sh

echo "Starting server hardening..."

# Disable root login
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config

# Configure SSH
echo "
# Security hardening
Protocol 2
MaxAuthTries 3
ClientAliveInterval 300
ClientAliveCountMax 2
LoginGraceTime 60
MaxStartups 10:30:60
AllowUsers deploy
" >> /etc/ssh/sshd_config

systemctl restart ssh

# Install and configure fail2ban
apt update && apt install -y fail2ban

cat > /etc/fail2ban/jail.local << EOF
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 3
backend = systemd

[sshd]
enabled = true
port = ssh
filter = sshd
logpath = /var/log/auth.log
maxretry = 3

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
action = iptables-multiport[name=ReqLimit, port="http,https", protocol=tcp]
logpath = /var/log/nginx/error.log
findtime = 600
bantime = 7200
maxretry = 10
EOF

systemctl enable fail2ban
systemctl start fail2ban

# Configure automatic security updates
apt install -y unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades

# Set up log monitoring
apt install -y logwatch
echo "/usr/sbin/logwatch --output mail --mailto your-email@domain.com --detail high" > /etc/cron.daily/00logwatch

# File system security
# Mount /tmp with noexec
echo "tmpfs /tmp tmpfs defaults,rw,nosuid,nodev,noexec,relatime 0 0" >> /etc/fstab

# Kernel hardening
cat >> /etc/sysctl.conf << EOF
# Network security
net.ipv4.ip_forward = 0
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0
net.ipv4.conf.all.accept_source_route = 0
net.ipv4.conf.default.accept_source_route = 0
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.default.secure_redirects = 0
net.ipv4.conf.all.log_martians = 1
net.ipv4.conf.default.log_martians = 1
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.icmp_ignore_bogus_error_responses = 1
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1
net.ipv6.conf.all.accept_ra = 0
net.ipv6.conf.default.accept_ra = 0
net.ipv6.conf.all.accept_redirects = 0
net.ipv6.conf.default.accept_redirects = 0

# Kernel security
kernel.dmesg_restrict = 1
kernel.kptr_restrict = 1
kernel.yama.ptrace_scope = 1
EOF

sysctl -p

echo "Server hardening completed!"
```

## Multi-tenancy Security

### Tenant Isolation
```typescript
// backend/src/common/interceptors/tenant-isolation.interceptor.ts
import { Injectable, NestInterceptor, ExecutionContext, CallHandler, ForbiddenException } from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

@Injectable()
export class TenantIsolationInterceptor implements NestInterceptor {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const request = context.switchToHttp().getRequest();
    const user = request.user;

    if (!user || !user.tenantId) {
      throw new ForbiddenException('Tenant information missing');
    }

    // Set tenant context for database queries
    request.tenantId = user.tenantId;

    return next.handle().pipe(
      map(data => {
        // Ensure response data belongs to user's tenant
        if (data && typeof data === 'object') {
          return this.filterTenantData(data, user.tenantId);
        }
        return data;
      }),
    );
  }

  private filterTenantData(data: any, tenantId: string): any {
    if (Array.isArray(data)) {
      return data.filter(item => 
        !item.tenantId || item.tenantId === tenantId
      );
    }

    if (data.tenantId && data.tenantId !== tenantId) {
      throw new ForbiddenException('Access denied to tenant data');
    }

    return data;
  }
}
```

### Database Row-Level Security
```sql
-- Enable RLS for all tenant tables
ALTER TABLE users ENABLE ROW LEVEL SECURITY;
ALTER TABLE license_applications ENABLE ROW LEVEL SECURITY;
ALTER TABLE documents ENABLE ROW LEVEL SECURITY;
ALTER TABLE financial_transactions ENABLE ROW LEVEL SECURITY;

-- Create RLS policies
CREATE POLICY tenant_isolation_users ON users
    FOR ALL TO application_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);

CREATE POLICY tenant_isolation_licenses ON license_applications
    FOR ALL TO application_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);

CREATE POLICY tenant_isolation_documents ON documents
    FOR ALL TO application_user
    USING (tenant_id = current_setting('app.current_tenant')::uuid);

-- Function to set tenant context
CREATE OR REPLACE FUNCTION set_tenant_context(tenant_uuid UUID)
RETURNS void AS $$
BEGIN
    PERFORM set_config('app.current_tenant', tenant_uuid::TEXT, true);
END;
$$ LANGUAGE plpgsql SECURITY DEFINER;

-- Middleware to set tenant context
CREATE OR REPLACE FUNCTION ensure_tenant_context()
RETURNS TRIGGER AS $$
BEGIN
    IF current_setting('app.current_tenant', true) IS NULL THEN
        RAISE EXCEPTION 'Tenant context not set';
    END IF;
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

-- Apply to all tenant tables
CREATE TRIGGER ensure_tenant_context_users
    BEFORE INSERT OR UPDATE OR DELETE ON users
    FOR EACH ROW EXECUTE FUNCTION ensure_tenant_context();
```

## Audit & Compliance

### Audit Logging
```typescript
// backend/src/common/decorators/audit-log.decorator.ts
import { SetMetadata } from '@nestjs/common';

export interface AuditOptions {
  action: string;
  resource: string;
  sensitiveFields?: string[];
}

export const AUDIT_KEY = 'audit';
export const AuditLog = (options: AuditOptions) => SetMetadata(AUDIT_KEY, options);

// Audit interceptor
import { Injectable, NestInterceptor, ExecutionContext, CallHandler } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { AuditService } from '../services/audit.service';

@Injectable()
export class AuditInterceptor implements NestInterceptor {
  constructor(
    private reflector: Reflector,
    private auditService: AuditService,
  ) {}

  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const auditOptions = this.reflector.get<AuditOptions>(
      AUDIT_KEY,
      context.getHandler(),
    );

    if (!auditOptions) {
      return next.handle();
    }

    const request = context.switchToHttp().getRequest();
    const user = request.user;
    const startTime = Date.now();

    return next.handle().pipe(
      tap({
        next: (data) => {
          this.auditService.logActivity({
            userId: user?.id,
            tenantId: user?.tenantId,
            action: auditOptions.action,
            resource: auditOptions.resource,
            resourceId: this.extractResourceId(data, request),
            ipAddress: request.ip,
            userAgent: request.get('User-Agent'),
            requestData: this.sanitizeData(request.body, auditOptions.sensitiveFields),
            responseData: this.sanitizeData(data, auditOptions.sensitiveFields),
            duration: Date.now() - startTime,
            success: true,
            timestamp: new Date(),
          });
        },
        error: (error) => {
          this.auditService.logActivity({
            userId: user?.id,
            tenantId: user?.tenantId,
            action: auditOptions.action,
            resource: auditOptions.resource,
            ipAddress: request.ip,
            userAgent: request.get('User-Agent'),
            requestData: this.sanitizeData(request.body, auditOptions.sensitiveFields),
            error: error.message,
            duration: Date.now() - startTime,
            success: false,
            timestamp: new Date(),
          });
        },
      }),
    );
  }

  private extractResourceId(data: any, request: any): string {
    return data?.id || request.params?.id || null;
  }

  private sanitizeData(data: any, sensitiveFields: string[] = []): any {
    if (!data || typeof data !== 'object') return data;

    const sanitized = { ...data };
    sensitiveFields.forEach(field => {
      if (sanitized[field]) {
        sanitized[field] = '[REDACTED]';
      }
    });

    return sanitized;
  }
}
```

### Usage in Controllers
```typescript
// backend/src/licenses/licenses.controller.ts
@Controller('licenses')
@UseInterceptors(AuditInterceptor)
export class LicensesController {
  @Post('applications')
  @AuditLog({ 
    action: 'CREATE', 
    resource: 'LICENSE_APPLICATION',
    sensitiveFields: ['applicantData', 'documents']
  })
  createApplication(@Body() dto: CreateLicenseApplicationDto) {
    return this.licensesService.createApplication(dto);
  }

  @Put('applications/:id/approve')
  @AuditLog({ 
    action: 'APPROVE', 
    resource: 'LICENSE_APPLICATION'
  })
  approveApplication(@Param('id') id: string) {
    return this.licensesService.approveApplication(id);
  }
}
```

### Compliance Reporting
```typescript
// backend/src/compliance/compliance.service.ts
import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Between } from 'typeorm';
import { AuditLog } from '../entities/audit-log.entity';

@Injectable()
export class ComplianceService {
  constructor(
    @InjectRepository(AuditLog)
    private auditRepository: Repository<AuditLog>,
  ) {}

  async generateComplianceReport(startDate: Date, endDate: Date, tenantId: string) {
    const auditLogs = await this.auditRepository.find({
      where: {
        tenantId,
        timestamp: Between(startDate, endDate),
      },
      order: { timestamp: 'DESC' },
    });

    return {
      period: { startDate, endDate },
      totalActivities: auditLogs.length,
      securityEvents: auditLogs.filter(log => 
        log.action.includes('LOGIN') || 
        log.action.includes('FAILED') ||
        log.action.includes('UNAUTHORIZED')
      ).length,
      dataAccess: auditLogs.filter(log => 
        log.resource.includes('DOCUMENT') ||
        log.resource.includes('FINANCIAL')
      ).length,
      adminActions: auditLogs.filter(log => 
        log.action.includes('CREATE') ||
        log.action.includes('DELETE') ||
        log.action.includes('APPROVE')
      ).length,
      activities: auditLogs,
    };
  }

  async detectAnomalies(tenantId: string) {
    // Detect unusual patterns
    const recentLogs = await this.auditRepository.find({
      where: {
        tenantId,
        timestamp: Between(new Date(Date.now() - 24 * 60 * 60 * 1000), new Date()),
      },
    });

    const anomalies = [];

    // Multiple failed login attempts
    const failedLogins = recentLogs.filter(log => 
      log.action === 'LOGIN' && log.success === false
    ).length;

    if (failedLogins > 5) {
      anomalies.push({
        type: 'MULTIPLE_FAILED_LOGINS',
        count: failedLogins,
        severity: 'HIGH',
      });
    }

    // Unusual access patterns
    const accessByHour = recentLogs.reduce((acc, log) => {
      const hour = log.timestamp.getHours();
      acc[hour] = (acc[hour] || 0) + 1;
      return acc;
    }, {});

    const nightAccess = Object.keys(accessByHour)
      .filter(hour => parseInt(hour) < 6 || parseInt(hour) > 22)
      .reduce((sum, hour) => sum + accessByHour[hour], 0);

    if (nightAccess > 10) {
      anomalies.push({
        type: 'UNUSUAL_ACCESS_HOURS',
        count: nightAccess,
        severity: 'MEDIUM',
      });
    }

    return anomalies;
  }
}
```

## Security Monitoring

### Real-time Security Monitoring
```typescript
// backend/src/security/security-monitor.service.ts
import { Injectable } from '@nestjs/common';
import { EventEmitter2 } from '@nestjs/event-emitter';
import { Cron, CronExpression } from '@nestjs/schedule';

@Injectable()
export class SecurityMonitorService {
  constructor(private eventEmitter: EventEmitter2) {}

  @Cron(CronExpression.EVERY_5_MINUTES)
  async monitorSecurity() {
    await Promise.all([
      this.checkFailedLogins(),
      this.checkUnusualActivity(),
      this.checkSystemHealth(),
      this.checkFileIntegrity(),
    ]);
  }

  private async checkFailedLogins() {
    const threshold = 10;
    const timeWindow = 5 * 60 * 1000; // 5 minutes
    
    const failedLogins = await this.getFailedLogins(timeWindow);
    
    if (failedLogins.length > threshold) {
      this.eventEmitter.emit('security.alert', {
        type: 'BRUTE_FORCE_ATTACK',
        severity: 'HIGH',
        details: {
          attempts: failedLogins.length,
          timeWindow: '5 minutes',
          ips: [...new Set(failedLogins.map(login => login.ipAddress))],
        },
      });
    }
  }

  private async checkUnusualActivity() {
    const suspiciousPatterns = [
      'multiple_tenant_access',
      'rapid_api_calls',
      'unusual_file_access',
      'privilege_escalation_attempt',
    ];

    for (const pattern of suspiciousPatterns) {
      const detected = await this.detectPattern(pattern);
      if (detected) {
        this.eventEmitter.emit('security.alert', {
          type: 'SUSPICIOUS_ACTIVITY',
          severity: 'MEDIUM',
          pattern,
          details: detected,
        });
      }
    }
  }

  private async checkSystemHealth() {
    const metrics = await this.getSystemMetrics();
    
    if (metrics.memoryUsage > 90) {
      this.eventEmitter.emit('system.alert', {
        type: 'HIGH_MEMORY_USAGE',
        severity: 'HIGH',
        value: metrics.memoryUsage,
      });
    }

    if (metrics.diskUsage > 85) {
      this.eventEmitter.emit('system.alert', {
        type: 'HIGH_DISK_USAGE',
        severity: 'MEDIUM',
        value: metrics.diskUsage,
      });
    }
  }
}

// Security event handlers
@Injectable()
export class SecurityEventHandler {
  @OnEvent('security.alert')
  async handleSecurityAlert(alert: any) {
    console.error('Security Alert:', alert);
    
    // Send to monitoring service (e.g., Sentry, DataDog)
    await this.sendToMonitoring(alert);
    
    // Notify security team
    await this.notifySecurityTeam(alert);
    
    // Auto-remediation for critical alerts
    if (alert.severity === 'CRITICAL') {
      await this.autoRemediate(alert);
    }
  }

  private async autoRemediate(alert: any) {
    switch (alert.type) {
      case 'BRUTE_FORCE_ATTACK':
        await this.blockSuspiciousIPs(alert.details.ips);
        break;
      case 'SQL_INJECTION_ATTEMPT':
        await this.enableStrictMode();
        break;
    }
  }
}
```

Dokumentasi keamanan ini memberikan panduan komprehensif untuk mengimplementasikan keamanan berlapis pada platform SaaS perizinan UMKM, mulai dari autentikasi hingga monitoring keamanan real-time.
