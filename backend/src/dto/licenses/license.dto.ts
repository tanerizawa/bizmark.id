import { IsString, IsOptional, IsEnum, IsUUID, IsObject, IsDateString } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { LicenseStatus } from '../../entities';

export class CreateLicenseDto {
  @ApiProperty({ description: 'Business name', example: 'Warung Makan Sejahtera' })
  @IsString()
  businessName: string;

  @ApiProperty({ description: 'Business address', example: 'Jl. Merdeka No. 123, Jakarta' })
  @IsString()
  businessAddress: string;

  @ApiProperty({ description: 'Business type', example: 'Restoran' })
  @IsString()
  businessType: string;

  @ApiPropertyOptional({ description: 'Business description' })
  @IsOptional()
  @IsString()
  businessDescription?: string;

  @ApiProperty({ description: 'License type ID' })
  @IsUUID()
  licenseTypeId: string;

  @ApiProperty({ description: 'Business data specific to license type' })
  @IsObject()
  businessData: Record<string, any>;

  @ApiPropertyOptional({ description: 'Requirements checklist' })
  @IsOptional()
  @IsObject()
  requirements?: Record<string, any>;

  @ApiPropertyOptional({ description: 'Additional notes' })
  @IsOptional()
  @IsString()
  notes?: string;
}

export class UpdateLicenseDto {
  @ApiPropertyOptional({ description: 'Business name', example: 'Warung Makan Sejahtera' })
  @IsOptional()
  @IsString()
  businessName?: string;

  @ApiPropertyOptional({ description: 'Business address', example: 'Jl. Merdeka No. 123, Jakarta' })
  @IsOptional()
  @IsString()
  businessAddress?: string;

  @ApiPropertyOptional({ description: 'Business type', example: 'Restoran' })
  @IsOptional()
  @IsString()
  businessType?: string;

  @ApiPropertyOptional({ description: 'Business description' })
  @IsOptional()
  @IsString()
  businessDescription?: string;

  @ApiPropertyOptional({ description: 'Business data specific to license type' })
  @IsOptional()
  @IsObject()
  businessData?: Record<string, any>;

  @ApiPropertyOptional({ description: 'Requirements checklist' })
  @IsOptional()
  @IsObject()
  requirements?: Record<string, any>;

  @ApiPropertyOptional({ description: 'Additional notes' })
  @IsOptional()
  @IsString()
  notes?: string;

  @ApiPropertyOptional({ description: 'License status', enum: LicenseStatus })
  @IsOptional()
  @IsEnum(LicenseStatus)
  status?: LicenseStatus;
}

export class ReviewLicenseDto {
  @ApiProperty({ description: 'Review decision', enum: ['approve', 'reject', 'request_revision'] })
  @IsEnum(['approve', 'reject', 'request_revision'])
  decision: 'approve' | 'reject' | 'request_revision';

  @ApiPropertyOptional({ description: 'Review comment or rejection reason' })
  @IsOptional()
  @IsString()
  comment?: string;

  @ApiPropertyOptional({ description: 'License expiry date (for approval)' })
  @IsOptional()
  @IsDateString()
  expiresAt?: string;
}

export class LicenseQueryDto {
  @ApiPropertyOptional({ description: 'Search by business name or license number' })
  @IsOptional()
  @IsString()
  search?: string;

  @ApiPropertyOptional({ description: 'Filter by status', enum: LicenseStatus })
  @IsOptional()
  @IsEnum(LicenseStatus)
  status?: LicenseStatus;

  @ApiPropertyOptional({ description: 'Filter by license type ID' })
  @IsOptional()
  @IsUUID()
  licenseTypeId?: string;

  @ApiPropertyOptional({ description: 'Filter by applicant ID' })
  @IsOptional()
  @IsUUID()
  applicantId?: string;

  @ApiPropertyOptional({ description: 'Filter by reviewer ID' })
  @IsOptional()
  @IsUUID()
  reviewerId?: string;

  @ApiPropertyOptional({ description: 'Start date filter (YYYY-MM-DD)' })
  @IsOptional()
  @IsDateString()
  startDate?: string;

  @ApiPropertyOptional({ description: 'End date filter (YYYY-MM-DD)' })
  @IsOptional()
  @IsDateString()
  endDate?: string;

  @ApiPropertyOptional({ description: 'Page number', default: 1 })
  @IsOptional()
  page?: number;

  @ApiPropertyOptional({ description: 'Items per page', default: 10 })
  @IsOptional()
  limit?: number;

  @ApiPropertyOptional({ description: 'Sort by field', default: 'createdAt' })
  @IsOptional()
  @IsString()
  sortBy?: string;

  @ApiPropertyOptional({ description: 'Sort order', enum: ['ASC', 'DESC'], default: 'DESC' })
  @IsOptional()
  sortOrder?: 'ASC' | 'DESC';
}

export class LicenseResponseDto {
  @ApiProperty({ description: 'License ID' })
  id: string;

  @ApiProperty({ description: 'License number' })
  licenseNumber: string;

  @ApiProperty({ description: 'Business name' })
  businessName: string;

  @ApiProperty({ description: 'Business address' })
  businessAddress: string;

  @ApiProperty({ description: 'Business type' })
  businessType: string;

  @ApiPropertyOptional({ description: 'Business description' })
  businessDescription?: string;

  @ApiProperty({ description: 'License status', enum: LicenseStatus })
  status: LicenseStatus;

  @ApiProperty({ description: 'Business data' })
  businessData: Record<string, any>;

  @ApiPropertyOptional({ description: 'Requirements checklist' })
  requirements?: Record<string, any>;

  @ApiPropertyOptional({ description: 'Application notes' })
  notes?: string;

  @ApiPropertyOptional({ description: 'Reviewer notes' })
  reviewerNotes?: string;

  @ApiPropertyOptional({ description: 'Valid from date' })
  validFrom?: Date;

  @ApiPropertyOptional({ description: 'Valid until date' })
  validUntil?: Date;

  @ApiProperty({ description: 'Application date' })
  applicationDate: Date;

  @ApiPropertyOptional({ description: 'Approval date' })
  approvalDate?: Date;

  @ApiProperty({ description: 'Created timestamp' })
  createdAt: Date;

  @ApiProperty({ description: 'Updated timestamp' })
  updatedAt: Date;

  @ApiProperty({ description: 'Applicant information' })
  applicant: {
    id: string;
    fullName: string;
    email: string;
    phone?: string;
  };

  @ApiProperty({ description: 'License type information' })
  licenseType: {
    id: string;
    name: string;
    code: string;
    category: string;
  };

  @ApiPropertyOptional({ description: 'Reviewer information' })
  reviewer?: {
    id: string;
    fullName: string;
    email: string;
  };

  @ApiProperty({ description: 'Tenant information' })
  tenant: {
    id: string;
    name: string;
    code: string;
    type: string;
  };
}
