import {
  Injectable,
  NotFoundException,
  ForbiddenException,
  BadRequestException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';

import {
  License,
  LicenseStatus,
  LicenseType,
  User,
  UserRole,
  LicenseWorkflow,
} from '../../entities';
import {
  CreateLicenseDto,
  UpdateLicenseDto,
  LicenseResponseDto,
} from '../../dto/licenses/license.dto';
import { PaginationQueryDto, PaginationResponseDto } from '../../common/dto/pagination.dto';

@Injectable()
export class LicensesService {
  constructor(
    @InjectRepository(License)
    private readonly licenseRepository: Repository<License>,
    @InjectRepository(LicenseType)
    private readonly licenseTypeRepository: Repository<LicenseType>,
    @InjectRepository(LicenseWorkflow)
    private readonly licenseWorkflowRepository: Repository<LicenseWorkflow>,
  ) {}

  async create(createLicenseDto: CreateLicenseDto, currentUser: User): Promise<LicenseResponseDto> {
    const {
      businessName,
      businessAddress,
      businessType,
      businessDescription,
      licenseTypeId,
      businessData,
      requirements,
      notes,
    } = createLicenseDto;

    // Validate license type
    const licenseType = await this.licenseTypeRepository.findOne({
      where: { id: licenseTypeId },
    });

    if (!licenseType) {
      throw new NotFoundException('License type not found');
    }

    // Generate license number
    const licenseNumber = await this.generateLicenseNumber(licenseType.code, currentUser.tenantId);

    // Create license
    const license = this.licenseRepository.create({
      licenseNumber,
      businessName,
      businessAddress,
      businessType,
      businessDescription,
      status: LicenseStatus.DRAFT,
      businessData,
      requirements,
      notes,
      applicantId: currentUser.id,
      licenseTypeId,
      tenantId: currentUser.tenantId,
    });

    const savedLicense = await this.licenseRepository.save(license);

    // Create initial workflow entry
    await this.licenseWorkflowRepository.save({
      licenseId: savedLicense.id,
      action: 'submit' as any,
      fromStatus: '',
      toStatus: LicenseStatus.DRAFT,
      comment: 'License application created',
      actorId: currentUser.id,
    });

    return this.formatLicenseResponse(
      await this.licenseRepository.findOne({
        where: { id: savedLicense.id },
        relations: ['applicant', 'licenseType', 'reviewer', 'tenant'],
      }),
    );
  }

  async findAll(
    query: PaginationQueryDto,
    currentUser: User,
  ): Promise<PaginationResponseDto<LicenseResponseDto>> {
    const {
      page = 1,
      limit = 10,
      search,
      status,
      sortBy = 'createdAt',
      sortOrder = 'DESC',
    } = query;

    const queryBuilder = this.licenseRepository
      .createQueryBuilder('license')
      .leftJoinAndSelect('license.applicant', 'applicant')
      .leftJoinAndSelect('license.licenseType', 'licenseType')
      .leftJoinAndSelect('license.reviewer', 'reviewer')
      .leftJoinAndSelect('license.tenant', 'tenant');

    // Apply tenant filtering for non-super admins
    if (currentUser.role !== UserRole.SUPER_ADMIN) {
      queryBuilder.where('license.tenantId = :tenantId', { tenantId: currentUser.tenantId });
    }

    // Apply search filter
    if (search) {
      queryBuilder.andWhere(
        '(license.businessName ILIKE :search OR license.licenseNumber ILIKE :search OR applicant.fullName ILIKE :search)',
        { search: `%${search}%` },
      );
    }

    // Apply status filter
    if (status) {
      queryBuilder.andWhere('license.status = :status', { status });
    }

    // Apply role-based filtering
    if (currentUser.role === UserRole.APPLICANT) {
      queryBuilder.andWhere('license.applicantId = :applicantId', { applicantId: currentUser.id });
    }

    // Apply sorting
    queryBuilder.orderBy(`license.${sortBy}`, sortOrder as 'ASC' | 'DESC');

    // Apply pagination
    const skip = (page - 1) * limit;
    queryBuilder.skip(skip).take(limit);

    const [licenses, total] = await queryBuilder.getManyAndCount();

    const data = licenses.map((license) => this.formatLicenseResponse(license));

    return {
      data,
      meta: {
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
        hasNextPage: page * limit < total,
        hasPreviousPage: page > 1,
      },
    };
  }

  async findUserLicenses(
    userId: string,
    query: PaginationQueryDto,
  ): Promise<PaginationResponseDto<LicenseResponseDto>> {
    const {
      page = 1,
      limit = 10,
      search,
      status,
      sortBy = 'createdAt',
      sortOrder = 'DESC',
    } = query;

    const queryBuilder = this.licenseRepository
      .createQueryBuilder('license')
      .leftJoinAndSelect('license.applicant', 'applicant')
      .leftJoinAndSelect('license.licenseType', 'licenseType')
      .leftJoinAndSelect('license.reviewer', 'reviewer')
      .leftJoinAndSelect('license.tenant', 'tenant')
      .where('license.applicantId = :userId', { userId });

    // Apply search filter
    if (search) {
      queryBuilder.andWhere(
        '(license.businessName ILIKE :search OR license.licenseNumber ILIKE :search)',
        { search: `%${search}%` },
      );
    }

    // Apply status filter
    if (status) {
      queryBuilder.andWhere('license.status = :status', { status });
    }

    // Apply sorting
    queryBuilder.orderBy(`license.${sortBy}`, sortOrder as 'ASC' | 'DESC');

    // Apply pagination
    const skip = (page - 1) * limit;
    queryBuilder.skip(skip).take(limit);

    const [licenses, total] = await queryBuilder.getManyAndCount();

    const data = licenses.map((license) => this.formatLicenseResponse(license));

    return {
      data,
      meta: {
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
        hasNextPage: page * limit < total,
        hasPreviousPage: page > 1,
      },
    };
  }

  async findOne(id: string, currentUser: User): Promise<LicenseResponseDto> {
    const queryBuilder = this.licenseRepository
      .createQueryBuilder('license')
      .leftJoinAndSelect('license.applicant', 'applicant')
      .leftJoinAndSelect('license.licenseType', 'licenseType')
      .leftJoinAndSelect('license.reviewer', 'reviewer')
      .leftJoinAndSelect('license.tenant', 'tenant')
      .where('license.id = :id', { id });

    // Apply tenant filtering for non-super admins
    if (currentUser.role !== UserRole.SUPER_ADMIN) {
      queryBuilder.andWhere('license.tenantId = :tenantId', { tenantId: currentUser.tenantId });
    }

    // Apply applicant filtering for applicant role
    if (currentUser.role === UserRole.APPLICANT) {
      queryBuilder.andWhere('license.applicantId = :applicantId', { applicantId: currentUser.id });
    }

    const license = await queryBuilder.getOne();

    if (!license) {
      throw new NotFoundException('License not found');
    }

    return this.formatLicenseResponse(license);
  }

  async update(
    id: string,
    updateLicenseDto: UpdateLicenseDto,
    currentUser: User,
  ): Promise<LicenseResponseDto> {
    const license = await this.licenseRepository.findOne({
      where: { id },
      relations: ['applicant', 'licenseType', 'reviewer', 'tenant'],
    });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (currentUser.role === UserRole.APPLICANT && license.applicantId !== currentUser.id) {
      throw new ForbiddenException('Can only update your own license applications');
    }

    if (currentUser.role === UserRole.TENANT_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot update license from different tenant');
    }

    // Check if license can be updated
    if (license.status === LicenseStatus.APPROVED || license.status === LicenseStatus.REJECTED) {
      throw new BadRequestException('Cannot update approved or rejected licenses');
    }

    // Update license fields
    Object.assign(license, updateLicenseDto);

    const updatedLicense = await this.licenseRepository.save(license);

    // Add workflow entry for update
    await this.licenseWorkflowRepository.save({
      licenseId: license.id,
      status: license.status,
      notes: 'License application updated',
      processedById: currentUser.id,
      processedAt: new Date(),
    });

    return this.formatLicenseResponse(updatedLicense);
  }

  async remove(id: string, currentUser: User): Promise<void> {
    const license = await this.licenseRepository.findOne({ where: { id } });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      currentUser.role !== UserRole.TENANT_ADMIN &&
      currentUser.role !== UserRole.OFFICER
    ) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot delete license from different tenant');
    }

    await this.licenseRepository.remove(license);
  }

  async updateStatus(id: string, status: string, notes: string, currentUser: User): Promise<void> {
    const license = await this.licenseRepository.findOne({ where: { id } });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      currentUser.role !== UserRole.TENANT_ADMIN &&
      currentUser.role !== UserRole.OFFICER
    ) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot update license from different tenant');
    }

    // Validate status
    if (!Object.values(LicenseStatus).includes(status as LicenseStatus)) {
      throw new BadRequestException('Invalid status');
    }

    await this.licenseRepository.update(id, { status: status as LicenseStatus });

    // Add workflow entry
    await this.licenseWorkflowRepository.save({
      licenseId: id,
      status: status as LicenseStatus,
      notes: notes || `Status updated to ${status}`,
      processedById: currentUser.id,
      processedAt: new Date(),
    });
  }

  async approve(id: string, notes: string, validUntil: string, currentUser: User): Promise<void> {
    const license = await this.licenseRepository.findOne({ where: { id } });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      currentUser.role !== UserRole.TENANT_ADMIN &&
      currentUser.role !== UserRole.OFFICER
    ) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot approve license from different tenant');
    }

    const now = new Date();
    const updateData: any = {
      status: LicenseStatus.APPROVED,
      approvalDate: now,
      validFrom: now,
      reviewerId: currentUser.id,
      reviewerNotes: notes,
    };

    if (validUntil) {
      updateData.validUntil = new Date(validUntil);
    }

    await this.licenseRepository.update(id, updateData);

    // Add workflow entry
    await this.licenseWorkflowRepository.save({
      licenseId: id,
      status: LicenseStatus.APPROVED,
      notes: notes || 'License approved',
      processedById: currentUser.id,
      processedAt: now,
    });
  }

  async reject(id: string, notes: string, currentUser: User): Promise<void> {
    const license = await this.licenseRepository.findOne({ where: { id } });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      currentUser.role !== UserRole.TENANT_ADMIN &&
      currentUser.role !== UserRole.OFFICER
    ) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot reject license from different tenant');
    }

    await this.licenseRepository.update(id, {
      status: LicenseStatus.REJECTED,
      reviewerId: currentUser.id,
      reviewerNotes: notes,
    });

    // Add workflow entry
    await this.licenseWorkflowRepository.save({
      licenseId: id,
      status: LicenseStatus.REJECTED,
      notes: notes || 'License rejected',
      processedById: currentUser.id,
      processedAt: new Date(),
    });
  }

  async getHistory(id: string, currentUser: User) {
    const license = await this.licenseRepository.findOne({ where: { id } });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (currentUser.role === UserRole.APPLICANT && license.applicantId !== currentUser.id) {
      throw new ForbiddenException('Can only view history of your own license applications');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot view history of license from different tenant');
    }

    const history = await this.licenseWorkflowRepository.find({
      where: { licenseId: id },
      relations: ['actor'],
      order: { createdAt: 'DESC' },
    });

    return history.map((entry) => ({
      id: entry.id,
      action: entry.action,
      fromStatus: entry.fromStatus,
      toStatus: entry.toStatus,
      notes: entry.comment,
      processedAt: entry.createdAt,
      processedBy: {
        id: entry.actor.id,
        fullName: entry.actor.fullName,
        email: entry.actor.email,
      },
    }));
  }

  private async generateLicenseNumber(licenseTypeCode: string, tenantId: string): Promise<string> {
    const now = new Date();
    const year = now.getFullYear();
    const month = (now.getMonth() + 1).toString().padStart(2, '0');

    // Get count of licenses for this type and tenant in current month
    const queryBuilder = this.licenseRepository
      .createQueryBuilder('license')
      .where('license.licenseTypeId = :licenseTypeCode', { licenseTypeCode })
      .andWhere('license.tenantId = :tenantId', { tenantId })
      .andWhere('license.createdAt >= :startDate', { startDate: `${year}-${month}-01` })
      .andWhere('license.createdAt < :endDate', {
        endDate: `${year}-${parseInt(month, 10) + 1}-01`,
      });

    const count = await queryBuilder.getCount();

    const sequence = (count + 1).toString().padStart(4, '0');
    return `${licenseTypeCode}/${year}${month}/${sequence}`;
  }

  private formatLicenseResponse(license: License): LicenseResponseDto {
    return {
      id: license.id,
      licenseNumber: license.licenseNumber,
      businessName: license.businessName,
      businessAddress: license.businessAddress,
      businessType: license.businessType,
      businessDescription: license.businessDescription,
      status: license.status,
      businessData: license.businessData,
      requirements: license.requirements,
      notes: license.notes,
      reviewerNotes: license.reviewerNotes,
      validFrom: license.validFrom,
      validUntil: license.validUntil,
      applicationDate: license.applicationDate,
      approvalDate: license.approvalDate,
      createdAt: license.createdAt,
      updatedAt: license.updatedAt,
      applicant: {
        id: license.applicant.id,
        fullName: license.applicant.fullName,
        email: license.applicant.email,
        phone: license.applicant.phone,
      },
      licenseType: {
        id: license.licenseType.id,
        name: license.licenseType.name,
        code: license.licenseType.code,
        category: license.licenseType.category,
      },
      reviewer: license.reviewer
        ? {
            id: license.reviewer.id,
            fullName: license.reviewer.fullName,
            email: license.reviewer.email,
          }
        : undefined,
      tenant: {
        id: license.tenant.id,
        name: license.tenant.name,
        code: license.tenant.code,
        type: license.tenant.type,
      },
    };
  }
}
