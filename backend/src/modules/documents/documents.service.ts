import {
  Injectable,
  NotFoundException,
  ForbiddenException,
  BadRequestException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import * as path from 'path';
import * as fs from 'fs';

import { Document, License, User, UserRole } from '../../entities';
import { PaginationQueryDto, PaginationResponseDto } from '../../common/dto/pagination.dto';

@Injectable()
export class DocumentsService {
  constructor(
    @InjectRepository(Document)
    private readonly documentRepository: Repository<Document>,
    @InjectRepository(License)
    private readonly licenseRepository: Repository<License>,
  ) {}

  async uploadDocument(licenseId: string, file: any, currentUser: User) {
    if (!file) {
      throw new BadRequestException('No file provided');
    }

    // Validate license exists and user has access
    const license = await this.licenseRepository.findOne({
      where: { id: licenseId },
      relations: ['tenant'],
    });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (currentUser.role === UserRole.APPLICANT && license.applicantId !== currentUser.id) {
      throw new ForbiddenException('Can only upload documents for your own license applications');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot upload documents for license from different tenant');
    }

    // Validate file type and size
    const allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
    const fileExt = path.extname(file.originalname).toLowerCase().slice(1);

    if (!allowedTypes.includes(fileExt)) {
      throw new BadRequestException(
        'Invalid file type. Allowed types: pdf, jpg, jpeg, png, doc, docx',
      );
    }

    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
      throw new BadRequestException('File size exceeds 10MB limit');
    }

    // Generate unique filename
    const filename = `${Date.now()}-${Math.random().toString(36).substring(7)}.${fileExt}`;
    const uploadPath = path.join(process.cwd(), 'uploads', 'documents');

    // Ensure upload directory exists
    if (!fs.existsSync(uploadPath)) {
      fs.mkdirSync(uploadPath, { recursive: true });
    }

    const filePath = path.join(uploadPath, filename);

    // Save file to disk
    fs.writeFileSync(filePath, file.buffer);

    // Create document record
    const document = this.documentRepository.create({
      originalName: file.originalname,
      fileName: filename,
      filePath: filePath,
      mimeType: file.mimetype,
      fileSize: file.size,
      type: 'OTHER' as any, // Default type, should be passed as parameter
      licenseId,
      uploadedById: currentUser.id,
    });

    const savedDocument = await this.documentRepository.save(document);

    return {
      id: savedDocument.id,
      filename: savedDocument.originalName,
      size: savedDocument.fileSize,
      mimeType: savedDocument.mimeType,
      uploadedAt: savedDocument.createdAt,
      uploadedBy: {
        id: currentUser.id,
        fullName: currentUser.fullName,
        email: currentUser.email,
      },
    };
  }

  async getDocumentsByLicense(
    licenseId: string,
    query: PaginationQueryDto,
    currentUser: User,
  ): Promise<PaginationResponseDto<any>> {
    // Validate license exists and user has access
    const license = await this.licenseRepository.findOne({
      where: { id: licenseId },
    });

    if (!license) {
      throw new NotFoundException('License not found');
    }

    // Check permissions
    if (currentUser.role === UserRole.APPLICANT && license.applicantId !== currentUser.id) {
      throw new ForbiddenException('Can only view documents for your own license applications');
    }

    if (currentUser.role !== UserRole.SUPER_ADMIN && license.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot view documents for license from different tenant');
    }

    const { page = 1, limit = 10, search } = query;

    const queryBuilder = this.documentRepository
      .createQueryBuilder('document')
      .leftJoinAndSelect('document.uploadedBy', 'uploadedBy')
      .where('document.licenseId = :licenseId', { licenseId });

    // Apply search filter
    if (search) {
      queryBuilder.andWhere('document.filename ILIKE :search', { search: `%${search}%` });
    }

    // Apply sorting
    queryBuilder.orderBy('document.createdAt', 'DESC');

    // Apply pagination
    const skip = (page - 1) * limit;
    queryBuilder.skip(skip).take(limit);

    const [documents, total] = await queryBuilder.getManyAndCount();

    const data = documents.map((doc) => ({
      id: doc.id,
      filename: doc.originalName,
      size: doc.fileSize,
      mimeType: doc.mimeType,
      uploadedAt: doc.createdAt,
      uploadedBy: {
        id: doc.uploadedBy.id,
        fullName: doc.uploadedBy.fullName,
        email: doc.uploadedBy.email,
      },
    }));

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

  async downloadDocument(id: string, currentUser: User) {
    const document = await this.documentRepository.findOne({
      where: { id },
      relations: ['license'],
    });

    if (!document) {
      throw new NotFoundException('Document not found');
    }

    // Check permissions
    if (
      currentUser.role === UserRole.APPLICANT &&
      document.license.applicantId !== currentUser.id
    ) {
      throw new ForbiddenException('Can only download documents for your own license applications');
    }

    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      document.license.tenantId !== currentUser.tenantId
    ) {
      throw new ForbiddenException('Cannot download documents for license from different tenant');
    }

    // Check if file exists
    if (!fs.existsSync(document.filePath)) {
      throw new NotFoundException('File not found on disk');
    }

    const fileBuffer = fs.readFileSync(document.filePath);

    return {
      buffer: fileBuffer,
      filename: document.originalName,
      mimeType: document.mimeType,
    };
  }

  async deleteDocument(id: string, currentUser: User): Promise<void> {
    const document = await this.documentRepository.findOne({
      where: { id },
      relations: ['license'],
    });

    if (!document) {
      throw new NotFoundException('Document not found');
    }

    // Check permissions
    if (
      currentUser.role === UserRole.APPLICANT &&
      document.license.applicantId !== currentUser.id
    ) {
      throw new ForbiddenException('Can only delete documents for your own license applications');
    }

    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      document.license.tenantId !== currentUser.tenantId
    ) {
      throw new ForbiddenException('Cannot delete documents for license from different tenant');
    }

    // Delete file from disk
    if (fs.existsSync(document.filePath)) {
      fs.unlinkSync(document.filePath);
    }

    // Delete document record
    await this.documentRepository.remove(document);
  }
}
