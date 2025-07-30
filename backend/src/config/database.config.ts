import { registerAs } from '@nestjs/config';

export default registerAs('database', () => {
  const isProduction = process.env.NODE_ENV === 'production';

  return {
    type: 'postgres',
    host: process.env.DATABASE_HOST || 'localhost',
    port: parseInt(process.env.DATABASE_PORT, 10) || 5432,
    username: process.env.DATABASE_USERNAME || 'postgres',
    password: process.env.DATABASE_PASSWORD || 'postgres',
    database: process.env.DATABASE_NAME || 'bizmark_dev',

    // Alternative: use DATABASE_URL for production (common in cloud deployments)
    url: process.env.DATABASE_URL,

    // Entity configuration
    entities: [__dirname + '/../**/*.entity{.ts,.js}'],

    // Migration configuration
    migrations: [__dirname + '/../database/migrations/*{.ts,.js}'],
    migrationsTableName: 'migrations',
    migrationsRun: false, // Don't auto-run migrations, use CLI

    // Subscriber configuration
    subscribers: [__dirname + '/../**/*.subscriber{.ts,.js}'],

    // Development vs Production settings
    synchronize: !isProduction, // Only in development
    dropSchema: false, // Never drop schema automatically
    logging: !isProduction ? ['query', 'error', 'warn', 'info'] : ['error', 'warn'],
    logger: 'advanced-console',

    // Connection pool settings (important for production)
    extra: {
      // Connection pool size
      max: isProduction ? 20 : 5,
      min: isProduction ? 5 : 1,

      // Connection timeout
      connectionTimeoutMillis: 30000,
      idleTimeoutMillis: 30000,

      // Statement timeout
      statement_timeout: 30000,

      // SSL configuration for production
      ...(isProduction && {
        ssl: {
          rejectUnauthorized: false, // For self-signed certificates
        },
      }),
    },

    // Retry configuration
    retryAttempts: 3,
    retryDelay: 3000,

    // Enable query result cache in production
    cache: isProduction
      ? {
          duration: 30000, // 30 seconds
          type: 'redis',
          options: {
            host: process.env.REDIS_HOST || 'localhost',
            port: parseInt(process.env.REDIS_PORT, 10) || 6379,
            password: process.env.REDIS_PASSWORD,
            db: 1, // Use different DB for cache
          },
        }
      : false,
  };
});
