import { DataSource } from 'typeorm';
import { ConfigService } from '@nestjs/config';
import { config } from 'dotenv';

import {
  Tenant,
  User,
  License,
  LicenseType,
  Document,
  LicenseWorkflow,
  AuditLog,
  Notification,
} from '../entities';

config();

const configService = new ConfigService();

export default new DataSource({
  type: 'postgres',
  host: configService.get('DATABASE_HOST') || 'localhost',
  port: parseInt(configService.get('DATABASE_PORT'), 10) || 5432,
  username: configService.get('DATABASE_USERNAME') || 'postgres',
  password: configService.get('DATABASE_PASSWORD') || 'password',
  database: configService.get('DATABASE_NAME') || 'bizmark_db',
  entities: [Tenant, User, License, LicenseType, Document, LicenseWorkflow, AuditLog, Notification],
  migrations: ['src/database/migrations/*.ts'],
  synchronize: false,
  logging: configService.get('NODE_ENV') === 'development',
});
