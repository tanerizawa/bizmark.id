import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { License } from '../../entities/license.entity';
import { User } from '../../entities/user.entity';
import { AuditLog } from '../../entities/audit-log.entity';
import { GenerateReportDto, ReportType, ReportFormat } from './dto/generate-report.dto';
import * as ExcelJS from 'exceljs';

export interface ReportData {
  title: string;
  subtitle?: string;
  data: any[];
  summary?: Record<string, any>;
  metadata: {
    generatedAt: Date;
    period?: string;
    totalRecords: number;
  };
}

@Injectable()
export class ReportsService {
  constructor(
    @InjectRepository(License)
    private licenseRepository: Repository<License>,
    @InjectRepository(User)
    private userRepository: Repository<User>,
    @InjectRepository(AuditLog)
    private auditLogRepository: Repository<AuditLog>,
  ) {}

  async generateReport(
    generateReportDto: GenerateReportDto,
    tenantId: string,
  ): Promise<{ buffer: Buffer; filename: string; contentType: string }> {
    const reportData = await this.getReportData(generateReportDto, tenantId);

    switch (generateReportDto.format) {
      case ReportFormat.EXCEL:
        return this.generateExcelReport(reportData);
      case ReportFormat.CSV:
        return this.generateCSVReport(reportData);
      default:
        return this.generateCSVReport(reportData);
    }
  }

  private async getReportData(
    generateReportDto: GenerateReportDto,
    tenantId: string,
  ): Promise<ReportData> {
    const { type } = generateReportDto;

    switch (type) {
      case ReportType.LICENSE_SUMMARY:
        return this.getLicenseSummaryData(tenantId);
      default:
        return this.getLicenseSummaryData(tenantId);
    }
  }

  private async getLicenseSummaryData(tenantId: string): Promise<ReportData> {
    const licenses = await this.licenseRepository
      .createQueryBuilder('license')
      .leftJoinAndSelect('license.licenseType', 'licenseType')
      .leftJoinAndSelect('license.applicant', 'applicant')
      .where('license.tenantId = :tenantId', { tenantId })
      .getMany();

    const summary = {
      total: licenses.length,
      totalRevenue: licenses.reduce((sum, l) => sum + (l.licenseType.fee || 0), 0),
    };

    return {
      title: 'Laporan Ringkasan Perizinan',
      data: licenses.map((license) => ({
        id: license.id,
        type: license.licenseType.name,
        category: license.licenseType.category,
        applicant: license.applicant.fullName,
        status: license.status,
        submittedAt: license.createdAt,
        fee: license.licenseType.fee,
      })),
      summary,
      metadata: {
        generatedAt: new Date(),
        totalRecords: licenses.length,
      },
    };
  }

  private async generateExcelReport(reportData: ReportData): Promise<{
    buffer: Buffer;
    filename: string;
    contentType: string;
  }> {
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet(reportData.title);

    // Add header
    const headerRow = worksheet.addRow([reportData.title]);
    headerRow.font = { bold: true, size: 16 };
    worksheet.mergeCells('A1:F1');

    worksheet.addRow([]);

    // Add summary
    if (reportData.summary) {
      worksheet.addRow(['RINGKASAN']);
      Object.entries(reportData.summary).forEach(([key, value]) => {
        worksheet.addRow([key, JSON.stringify(value)]);
      });
      worksheet.addRow([]);
    }

    // Add data
    if (reportData.data.length > 0) {
      const headers = Object.keys(reportData.data[0]);
      worksheet.addRow(headers);

      reportData.data.forEach((item) => {
        const row = headers.map((header) => item[header]);
        worksheet.addRow(row);
      });
    }

    // Style the worksheet
    worksheet.getRow(1).alignment = { horizontal: 'center' };
    worksheet.columns.forEach((column) => {
      column.width = 15;
    });

    const buffer = await workbook.xlsx.writeBuffer();

    return {
      buffer: Buffer.from(buffer),
      filename: `${reportData.title.replace(/\s+/g, '_')}_${Date.now()}.xlsx`,
      contentType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    };
  }

  private async generateCSVReport(reportData: ReportData): Promise<{
    buffer: Buffer;
    filename: string;
    contentType: string;
  }> {
    let csv = `${reportData.title}\n`;
    csv += '\n';

    // Add summary
    if (reportData.summary) {
      csv += 'RINGKASAN\n';
      Object.entries(reportData.summary).forEach(([key, value]) => {
        csv += `${key},${JSON.stringify(value)}\n`;
      });
      csv += '\n';
    }

    // Add data
    if (reportData.data.length > 0) {
      const headers = Object.keys(reportData.data[0]);
      csv += headers.join(',') + '\n';

      reportData.data.forEach((item) => {
        const row = headers.map((header) =>
          typeof item[header] === 'string' ? `"${item[header]}"` : item[header],
        );
        csv += row.join(',') + '\n';
      });
    }

    return {
      buffer: Buffer.from(csv, 'utf-8'),
      filename: `${reportData.title.replace(/\s+/g, '_')}_${Date.now()}.csv`,
      contentType: 'text/csv',
    };
  }
}
