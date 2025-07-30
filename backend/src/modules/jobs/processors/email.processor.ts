import { Process, Processor } from '@nestjs/bull';
import { Injectable, Logger } from '@nestjs/common';
import { Job } from 'bull';
import { EmailJobData, JobType } from '../jobs.service';
import { NotificationsService } from '../../notifications/notifications.service';

@Processor('email')
@Injectable()
export class EmailProcessor {
  private readonly logger = new Logger(EmailProcessor.name);

  constructor(private readonly notificationsService: NotificationsService) {}

  @Process(JobType.EMAIL_NOTIFICATION)
  async handleEmailJob(job: Job<EmailJobData>) {
    this.logger.log(`Processing email job: ${job.id}`);

    try {
      await this.notificationsService.sendEmail({
        to: job.data.to,
        subject: job.data.subject,
        template: job.data.template,
        data: job.data.data,
        tenantId: job.data.tenantId,
      });

      this.logger.log(`Email job ${job.id} completed successfully`);
    } catch (error) {
      this.logger.error(`Email job ${job.id} failed: ${error.message}`);
      throw error;
    }
  }
}
