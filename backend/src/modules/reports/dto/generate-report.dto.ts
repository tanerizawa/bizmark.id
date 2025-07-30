import { IsEnum, IsOptional, IsDateString, IsString, IsUUID } from 'class-validator';

export enum ReportType {
  LICENSE_SUMMARY = 'license_summary',
  PROCESSING_TIME = 'processing_time',
  REVENUE = 'revenue',
  USER_ACTIVITY = 'user_activity',
  DOCUMENT_STATUS = 'document_status',
  MONTHLY_STATISTICS = 'monthly_statistics',
  YEARLY_STATISTICS = 'yearly_statistics',
}

export enum ReportFormat {
  PDF = 'pdf',
  EXCEL = 'excel',
  CSV = 'csv',
}

export class GenerateReportDto {
  @IsEnum(ReportType)
  type: ReportType;

  @IsEnum(ReportFormat)
  format: ReportFormat;

  @IsOptional()
  @IsDateString()
  startDate?: string;

  @IsOptional()
  @IsDateString()
  endDate?: string;

  @IsOptional()
  @IsString()
  category?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsUUID()
  userId?: string;
}
