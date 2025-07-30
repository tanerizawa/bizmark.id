import { Module } from '@nestjs/common';
import { BullModule } from '@nestjs/bull';
import { JobsController } from './jobs.controller';
import { JobsService } from './jobs.service';
import { EmailProcessor } from './processors/email.processor';
import { ReportsProcessor } from './processors/reports.processor';
import { NotificationsModule } from '../notifications/notifications.module';
import { ReportsModule } from '../reports/reports.module';

@Module({
  imports: [
    BullModule.forRoot({
      redis: {
        host: process.env.REDIS_HOST || 'localhost',
        port: parseInt(process.env.REDIS_PORT) || 6379,
        password: process.env.REDIS_PASSWORD,
      },
    }),
    BullModule.registerQueue(
      { name: 'email' },
      { name: 'reports' },
      { name: 'documents' },
      { name: 'maintenance' },
    ),
    NotificationsModule,
    ReportsModule,
  ],
  controllers: [JobsController],
  providers: [JobsService, EmailProcessor, ReportsProcessor],
  exports: [JobsService],
})
export class JobsModule {}
