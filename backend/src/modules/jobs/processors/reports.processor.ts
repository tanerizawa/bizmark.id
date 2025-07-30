import { Process, Processor } from '@nestjs/bull';
import { Injectable, Logger } from '@nestjs/common';
import { Job } from 'bull';
import { ReportJobData, JobType } from '../jobs.service';
import { ReportsService } from '../../reports/reports.service';
import { NotificationsService } from '../../notifications/notifications.service';

@Processor('reports')
@Injectable()
export class ReportsProcessor {
  private readonly logger = new Logger(ReportsProcessor.name);

  constructor(
    private readonly reportsService: ReportsService,
    private readonly notificationsService: NotificationsService,
  ) {}

  @Process(JobType.REPORT_GENERATION)
  async handleReportJob(job: Job<ReportJobData>) {
    this.logger.log(`Processing report job: ${job.id}`);

    try {
      const result = await this.reportsService.generateReport(
        {
          type: job.data.reportType as any,
          format: job.data.format as any,
          ...job.data.filters,
        },
        job.data.tenantId,
      );

      // Send the report via email
      await this.notificationsService.sendEmail({
        to: job.data.email,
        subject: 'Laporan Siap Diunduh',
        template: 'report-ready',
        data: {
          reportType: job.data.reportType,
          format: job.data.format,
        },
        tenantId: job.data.tenantId,
      });

      // Store the report file (implement file storage logic here)
      this.logger.log(`Report job ${job.id} completed successfully`);

      return {
        filename: result.filename,
        size: result.buffer.length,
      };
    } catch (error) {
      this.logger.error(`Report job ${job.id} failed: ${error.message}`);

      // Send failure notification
      await this.notificationsService.sendEmail({
        to: job.data.email,
        subject: 'Gagal Membuat Laporan',
        template: 'report-failed',
        data: {
          reportType: job.data.reportType,
          error: error.message,
        },
        tenantId: job.data.tenantId,
      });

      throw error;
    }
  }
}
