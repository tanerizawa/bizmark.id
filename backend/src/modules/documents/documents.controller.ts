import {
  Controller,
  Get,
  Post,
  Delete,
  Param,
  UseGuards,
  Query,
  Request,
  UploadedFile,
  UseInterceptors,
  ParseUUIDPipe,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiConsumes } from '@nestjs/swagger';
import { FileInterceptor } from '@nestjs/platform-express';

import { DocumentsService } from './documents.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { TenantGuard } from '../../common/guards/tenant.guard';
import { PaginationQueryDto } from '../../common/dto/pagination.dto';

@ApiTags('Documents')
@Controller('documents')
@UseGuards(JwtAuthGuard, TenantGuard)
@ApiBearerAuth()
export class DocumentsController {
  constructor(private readonly documentsService: DocumentsService) {}

  @Post('upload/:licenseId')
  @UseInterceptors(FileInterceptor('file'))
  @ApiConsumes('multipart/form-data')
  @ApiOperation({ summary: 'Upload document for license' })
  @ApiResponse({ status: 201, description: 'Document uploaded successfully' })
  @ApiResponse({ status: 400, description: 'Bad request' })
  async uploadDocument(
    @Param('licenseId', ParseUUIDPipe) licenseId: string,
    @UploadedFile() file: any,
    @Request() req: any,
  ) {
    return this.documentsService.uploadDocument(licenseId, file, req.user);
  }

  @Get('license/:licenseId')
  @ApiOperation({ summary: 'Get documents for license' })
  @ApiResponse({ status: 200, description: 'Documents retrieved successfully' })
  async getDocumentsByLicense(
    @Param('licenseId', ParseUUIDPipe) licenseId: string,
    @Query() query: PaginationQueryDto,
    @Request() req: any,
  ) {
    return this.documentsService.getDocumentsByLicense(licenseId, query, req.user);
  }

  @Get(':id/download')
  @ApiOperation({ summary: 'Download document by ID' })
  @ApiResponse({ status: 200, description: 'Document downloaded successfully' })
  async downloadDocument(@Param('id', ParseUUIDPipe) id: string, @Request() req: any) {
    return this.documentsService.downloadDocument(id, req.user);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete document by ID' })
  @ApiResponse({ status: 200, description: 'Document deleted successfully' })
  async deleteDocument(
    @Param('id', ParseUUIDPipe) id: string,
    @Request() req: any,
  ): Promise<{ message: string }> {
    await this.documentsService.deleteDocument(id, req.user);
    return { message: 'Document deleted successfully' };
  }
}
