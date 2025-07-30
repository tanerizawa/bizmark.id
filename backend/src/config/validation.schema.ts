import * as Joi from 'joi';

export const validationSchema = Joi.object({
  // App Config
  NODE_ENV: Joi.string().valid('development', 'production', 'test').default('development'),
  PORT: Joi.number().port().default(3000),
  API_PREFIX: Joi.string().default('api'),
  API_VERSION: Joi.string().default('v1'),
  CORS_ORIGINS: Joi.string().default('http://localhost:3001'),

  // Database Config
  DATABASE_HOST: Joi.string().required(),
  DATABASE_PORT: Joi.number().port().default(5432),
  DATABASE_USERNAME: Joi.string().required(),
  DATABASE_PASSWORD: Joi.string().required(),
  DATABASE_NAME: Joi.string().required(),

  // JWT Config
  JWT_SECRET: Joi.string().min(32).required(),
  JWT_EXPIRES_IN: Joi.string().default('1d'),
  JWT_REFRESH_SECRET: Joi.string().min(32).required(),
  JWT_REFRESH_EXPIRES_IN: Joi.string().default('7d'),

  // Redis Config
  REDIS_HOST: Joi.string().default('localhost'),
  REDIS_PORT: Joi.number().port().default(6379),
  REDIS_PASSWORD: Joi.string().allow('').default(''),
  REDIS_DB: Joi.number().min(0).max(15).default(0),

  // MinIO Config
  MINIO_ENDPOINT: Joi.string().default('localhost'),
  MINIO_PORT: Joi.number().port().default(9000),
  MINIO_USE_SSL: Joi.string().valid('true', 'false').default('false'),
  MINIO_ACCESS_KEY: Joi.string().required(),
  MINIO_SECRET_KEY: Joi.string().required(),
  MINIO_BUCKET_NAME: Joi.string().default('umkm-documents'),

  // File Upload Config
  MAX_FILE_SIZE: Joi.number()
    .positive()
    .default(50 * 1024 * 1024), // 50MB
  ALLOWED_FILE_TYPES: Joi.string().default(
    'image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
  ),

  // Email Config (untuk notifikasi)
  SMTP_HOST: Joi.string().default('localhost'),
  SMTP_PORT: Joi.number().port().default(587),
  SMTP_USER: Joi.string().default(''),
  SMTP_PASS: Joi.string().default(''),
  SMTP_FROM: Joi.string().email().default('noreply@bizmark.id'),

  // Security
  BCRYPT_ROUNDS: Joi.number().min(10).max(15).default(12),
  RATE_LIMIT_TTL: Joi.number().positive().default(60000), // 1 minute
  RATE_LIMIT_MAX: Joi.number().positive().default(100), // 100 requests per minute
});
