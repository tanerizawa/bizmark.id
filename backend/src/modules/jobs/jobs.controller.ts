import { Controller, Get, Post, Param, Body, HttpStatus, HttpException } from '@nestjs/common';
import { JobsService } from './jobs.service';
import { GetTenant } from '../../common/decorators/tenant.decorator';

export class CreateEmailJobDto {
  to: string;
  subject: string;
  template: string;
  data: Record<string, any>;
}

export class CreateReportJobDto {
  reportType: string;
  format: string;
  filters: Record<string, any>;
  email: string;
}

@Controller('jobs')
export class JobsController {
  constructor(private readonly jobsService: JobsService) {}

  @Post('email')
  async createEmailJob(
    @Body() createEmailJobDto: CreateEmailJobDto,
    @GetTenant() tenantId: string,
  ) {
    try {
      const job = await this.jobsService.addEmailJob({
        ...createEmailJobDto,
        tenantId,
      });

      return {
        jobId: job.id,
        message: 'Email job created successfully',
      };
    } catch (error) {
      throw new HttpException(
        `Failed to create email job: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }

  @Post('report')
  async createReportJob(
    @Body() createReportJobDto: CreateReportJobDto,
    @GetTenant() tenantId: string,
  ) {
    try {
      const job = await this.jobsService.addReportJob({
        ...createReportJobDto,
        userId: 'current-user-id', // TODO: Get from auth context
        tenantId,
      });

      return {
        jobId: job.id,
        message: 'Report job created successfully',
      };
    } catch (error) {
      throw new HttpException(
        `Failed to create report job: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }

  @Get('stats')
  async getQueueStats(@GetTenant() tenantId: string) {
    try {
      const stats = await this.jobsService.getQueueStats();
      return {
        tenantId,
        queues: stats,
      };
    } catch (error) {
      throw new HttpException(
        `Failed to get queue stats: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }

  @Post('queue/:name/pause')
  async pauseQueue(@Param('name') queueName: string) {
    try {
      return await this.jobsService.pauseQueue(queueName);
    } catch (error) {
      throw new HttpException(
        `Failed to pause queue: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }

  @Post('queue/:name/resume')
  async resumeQueue(@Param('name') queueName: string) {
    try {
      return await this.jobsService.resumeQueue(queueName);
    } catch (error) {
      throw new HttpException(
        `Failed to resume queue: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }

  @Post('queue/:name/clear')
  async clearQueue(@Param('name') queueName: string) {
    try {
      return await this.jobsService.clearQueue(queueName);
    } catch (error) {
      throw new HttpException(
        `Failed to clear queue: ${error.message}`,
        HttpStatus.INTERNAL_SERVER_ERROR,
      );
    }
  }
}
