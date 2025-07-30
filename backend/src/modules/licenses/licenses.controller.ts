import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  UseGuards,
  Query,
  Request,
  ParseUUIDPipe,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiQuery } from '@nestjs/swagger';

import { LicensesService } from './licenses.service';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../common/guards/roles.guard';
import { TenantGuard } from '../../common/guards/tenant.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import {
  CreateLicenseDto,
  UpdateLicenseDto,
  LicenseResponseDto,
} from '../../dto/licenses/license.dto';
import { UserRole } from '../../entities';
import { PaginationQueryDto } from '../../common/dto/pagination.dto';

@ApiTags('Licenses')
@Controller('licenses')
@UseGuards(JwtAuthGuard, TenantGuard)
@ApiBearerAuth()
export class LicensesController {
  constructor(private readonly licensesService: LicensesService) {}

  @Post()
  @ApiOperation({ summary: 'Create a new license application' })
  @ApiResponse({
    status: 201,
    description: 'License application created successfully',
    type: LicenseResponseDto,
  })
  @ApiResponse({ status: 400, description: 'Bad request' })
  async create(
    @Body() createLicenseDto: CreateLicenseDto,
    @Request() req: any,
  ): Promise<LicenseResponseDto> {
    return this.licensesService.create(createLicenseDto, req.user);
  }

  @Get()
  @ApiOperation({ summary: 'Get all licenses with pagination' })
  @ApiQuery({ name: 'page', required: false, type: Number })
  @ApiQuery({ name: 'limit', required: false, type: Number })
  @ApiQuery({ name: 'search', required: false, type: String })
  @ApiQuery({ name: 'status', required: false, type: String })
  @ApiQuery({ name: 'licenseTypeId', required: false, type: String })
  @ApiResponse({ status: 200, description: 'Licenses retrieved successfully' })
  async findAll(@Query() query: PaginationQueryDto, @Request() req: any) {
    return this.licensesService.findAll(query, req.user);
  }

  @Get('my-licenses')
  @ApiOperation({ summary: 'Get current user licenses' })
  @ApiResponse({ status: 200, description: 'User licenses retrieved successfully' })
  async getMyLicenses(@Query() query: PaginationQueryDto, @Request() req: any) {
    return this.licensesService.findUserLicenses(req.user.id, query);
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get license by ID' })
  @ApiResponse({
    status: 200,
    description: 'License retrieved successfully',
    type: LicenseResponseDto,
  })
  @ApiResponse({ status: 404, description: 'License not found' })
  async findOne(
    @Param('id', ParseUUIDPipe) id: string,
    @Request() req: any,
  ): Promise<LicenseResponseDto> {
    return this.licensesService.findOne(id, req.user);
  }

  @Patch(':id')
  @ApiOperation({ summary: 'Update license by ID' })
  @ApiResponse({
    status: 200,
    description: 'License updated successfully',
    type: LicenseResponseDto,
  })
  @ApiResponse({ status: 404, description: 'License not found' })
  async update(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() updateLicenseDto: UpdateLicenseDto,
    @Request() req: any,
  ): Promise<LicenseResponseDto> {
    return this.licensesService.update(id, updateLicenseDto, req.user);
  }

  @Delete(':id')
  @UseGuards(RolesGuard)
  @Roles(UserRole.SUPER_ADMIN, UserRole.TENANT_ADMIN, UserRole.OFFICER)
  @ApiOperation({ summary: 'Delete license by ID' })
  @ApiResponse({ status: 200, description: 'License deleted successfully' })
  @ApiResponse({ status: 404, description: 'License not found' })
  async remove(
    @Param('id', ParseUUIDPipe) id: string,
    @Request() req: any,
  ): Promise<{ message: string }> {
    await this.licensesService.remove(id, req.user);
    return { message: 'License deleted successfully' };
  }

  @Patch(':id/status')
  @UseGuards(RolesGuard)
  @Roles(UserRole.SUPER_ADMIN, UserRole.TENANT_ADMIN, UserRole.OFFICER)
  @ApiOperation({ summary: 'Update license status' })
  @ApiResponse({ status: 200, description: 'License status updated' })
  async updateStatus(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() body: { status: string; notes?: string },
    @Request() req: any,
  ): Promise<{ message: string }> {
    await this.licensesService.updateStatus(id, body.status, body.notes, req.user);
    return { message: 'License status updated successfully' };
  }

  @Post(':id/approve')
  @UseGuards(RolesGuard)
  @Roles(UserRole.SUPER_ADMIN, UserRole.TENANT_ADMIN, UserRole.OFFICER)
  @ApiOperation({ summary: 'Approve license application' })
  @ApiResponse({ status: 200, description: 'License approved successfully' })
  async approve(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() body: { notes?: string; validUntil?: string },
    @Request() req: any,
  ): Promise<{ message: string }> {
    await this.licensesService.approve(id, body.notes, body.validUntil, req.user);
    return { message: 'License approved successfully' };
  }

  @Post(':id/reject')
  @UseGuards(RolesGuard)
  @Roles(UserRole.SUPER_ADMIN, UserRole.TENANT_ADMIN, UserRole.OFFICER)
  @ApiOperation({ summary: 'Reject license application' })
  @ApiResponse({ status: 200, description: 'License rejected successfully' })
  async reject(
    @Param('id', ParseUUIDPipe) id: string,
    @Body() body: { notes: string },
    @Request() req: any,
  ): Promise<{ message: string }> {
    await this.licensesService.reject(id, body.notes, req.user);
    return { message: 'License rejected successfully' };
  }

  @Get(':id/history')
  @ApiOperation({ summary: 'Get license status history' })
  @ApiResponse({ status: 200, description: 'License history retrieved successfully' })
  async getHistory(@Param('id', ParseUUIDPipe) id: string, @Request() req: any) {
    return this.licensesService.getHistory(id, req.user);
  }
}
