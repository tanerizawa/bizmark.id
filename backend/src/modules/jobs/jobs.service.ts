import { Injectable } from '@nestjs/common';
import { InjectQueue } from '@nestjs/bull';
import { Queue } from 'bull';

export enum JobType {
  EMAIL_NOTIFICATION = 'email_notification',
  REPORT_GENERATION = 'report_generation',
  DOCUMENT_PROCESSING = 'document_processing',
  DATA_CLEANUP = 'data_cleanup',
  BACKUP_DATABASE = 'backup_database',
}

export interface EmailJobData {
  to: string;
  subject: string;
  template: string;
  data: Record<string, any>;
  tenantId: string;
}

export interface ReportJobData {
  reportType: string;
  format: string;
  filters: Record<string, any>;
  userId: string;
  tenantId: string;
  email: string;
}

export interface DocumentJobData {
  documentId: string;
  action: 'convert' | 'compress' | 'validate';
  tenantId: string;
}

@Injectable()
export class JobsService {
  constructor(
    @InjectQueue('email') private emailQueue: Queue,
    @InjectQueue('reports') private reportsQueue: Queue,
    @InjectQueue('documents') private documentsQueue: Queue,
    @InjectQueue('maintenance') private maintenanceQueue: Queue,
  ) {}

  // Email Jobs
  async addEmailJob(data: EmailJobData, options: { delay?: number; attempts?: number } = {}) {
    return this.emailQueue.add(JobType.EMAIL_NOTIFICATION, data, {
      attempts: options.attempts || 3,
      delay: options.delay || 0,
      backoff: {
        type: 'exponential',
        delay: 2000,
      },
      removeOnComplete: 10,
      removeOnFail: 5,
    });
  }

  async addBulkEmailJob(emails: EmailJobData[]) {
    const jobs = emails.map((email, index) => ({
      name: JobType.EMAIL_NOTIFICATION,
      data: email,
      opts: {
        delay: index * 1000, // Stagger emails by 1 second
        attempts: 3,
        backoff: {
          type: 'exponential',
          delay: 2000,
        },
      },
    }));

    return this.emailQueue.addBulk(jobs);
  }

  // Report Jobs
  async addReportJob(data: ReportJobData) {
    return this.reportsQueue.add(JobType.REPORT_GENERATION, data, {
      attempts: 2,
      timeout: 300000, // 5 minutes
      backoff: {
        type: 'fixed',
        delay: 30000,
      },
      removeOnComplete: 5,
      removeOnFail: 3,
    });
  }

  // Document Jobs
  async addDocumentJob(data: DocumentJobData) {
    return this.documentsQueue.add(JobType.DOCUMENT_PROCESSING, data, {
      attempts: 3,
      timeout: 180000, // 3 minutes
      backoff: {
        type: 'exponential',
        delay: 5000,
      },
      removeOnComplete: 10,
      removeOnFail: 5,
    });
  }

  // Maintenance Jobs
  async addCleanupJob(tenantId?: string) {
    return this.maintenanceQueue.add(
      JobType.DATA_CLEANUP,
      { tenantId },
      {
        repeat: { cron: '0 2 * * *' }, // Daily at 2 AM
        attempts: 1,
        removeOnComplete: 1,
        removeOnFail: 1,
      },
    );
  }

  async addBackupJob() {
    return this.maintenanceQueue.add(
      JobType.BACKUP_DATABASE,
      {},
      {
        repeat: { cron: '0 1 * * 0' }, // Weekly on Sunday at 1 AM
        attempts: 1,
        removeOnComplete: 1,
        removeOnFail: 1,
      },
    );
  }

  // Queue monitoring
  async getQueueStats() {
    const emailStats = await this.getQueueStatus(this.emailQueue);
    const reportsStats = await this.getQueueStatus(this.reportsQueue);
    const documentsStats = await this.getQueueStatus(this.documentsQueue);
    const maintenanceStats = await this.getQueueStatus(this.maintenanceQueue);

    return {
      email: emailStats,
      reports: reportsStats,
      documents: documentsStats,
      maintenance: maintenanceStats,
    };
  }

  private async getQueueStatus(queue: Queue) {
    const [waiting, active, completed, failed, delayed] = await Promise.all([
      queue.getWaiting(),
      queue.getActive(),
      queue.getCompleted(),
      queue.getFailed(),
      queue.getDelayed(),
    ]);

    return {
      waiting: waiting.length,
      active: active.length,
      completed: completed.length,
      failed: failed.length,
      delayed: delayed.length,
    };
  }

  // Job management
  async pauseQueue(queueName: string) {
    const queue = this.getQueueByName(queueName);
    await queue.pause();
    return { message: `Queue ${queueName} paused` };
  }

  async resumeQueue(queueName: string) {
    const queue = this.getQueueByName(queueName);
    await queue.resume();
    return { message: `Queue ${queueName} resumed` };
  }

  async clearQueue(queueName: string) {
    const queue = this.getQueueByName(queueName);
    await queue.clean(0, 'completed');
    await queue.clean(0, 'failed');
    return { message: `Queue ${queueName} cleared` };
  }

  private getQueueByName(name: string): Queue {
    switch (name) {
      case 'email':
        return this.emailQueue;
      case 'reports':
        return this.reportsQueue;
      case 'documents':
        return this.documentsQueue;
      case 'maintenance':
        return this.maintenanceQueue;
      default:
        throw new Error(`Queue ${name} not found`);
    }
  }
}
