import {
  Injectable,
  NotFoundException,
  ConflictException,
  ForbiddenException,
  BadRequestException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import * as bcrypt from 'bcrypt';
import * as crypto from 'crypto';

import { User, UserRole, UserStatus, Tenant } from '../../entities';
import { CreateUserDto, UpdateUserDto, UserResponseDto } from '../../dto/users/user.dto';
import { PaginationQueryDto, PaginationResponseDto } from '../../common/dto/pagination.dto';

@Injectable()
export class UsersService {
  constructor(
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    @InjectRepository(Tenant)
    private readonly tenantRepository: Repository<Tenant>,
  ) {}

  async create(createUserDto: CreateUserDto, currentUser: User): Promise<UserResponseDto> {
    const { email, password, fullName, phone, nik, role, status, tenantId, profile } =
      createUserDto;

    // Check permissions
    if (currentUser.role === UserRole.TENANT_ADMIN && tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot create user for different tenant');
    }

    // Check if user already exists
    const existingUser = await this.userRepository.findOne({
      where: { email, ...(tenantId && { tenantId }) },
    });

    if (existingUser) {
      throw new ConflictException('User with this email already exists');
    }

    // Validate tenant if provided
    let tenant: Tenant | null = null;
    if (tenantId) {
      tenant = await this.tenantRepository.findOne({ where: { id: tenantId } });
      if (!tenant) {
        throw new NotFoundException('Tenant not found');
      }
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 12);

    // Create user
    const user = this.userRepository.create({
      email,
      password: hashedPassword,
      fullName,
      phone,
      nik,
      role: role || UserRole.APPLICANT,
      status: status || UserStatus.PENDING_VERIFICATION,
      tenantId,
      profile,
    });

    const savedUser = await this.userRepository.save(user);

    return this.formatUserResponse(savedUser, tenant);
  }

  async findAll(
    query: PaginationQueryDto,
    currentUser: User,
  ): Promise<PaginationResponseDto<UserResponseDto>> {
    const {
      page = 1,
      limit = 10,
      search,
      role,
      status,
      sortBy = 'createdAt',
      sortOrder = 'DESC',
    } = query;

    const queryBuilder = this.userRepository
      .createQueryBuilder('user')
      .leftJoinAndSelect('user.tenant', 'tenant');

    // Apply tenant filtering for non-super admins
    if (currentUser.role !== UserRole.SUPER_ADMIN) {
      queryBuilder.where('user.tenantId = :tenantId', { tenantId: currentUser.tenantId });
    }

    // Apply search filter
    if (search) {
      queryBuilder.andWhere(
        '(user.fullName ILIKE :search OR user.email ILIKE :search OR user.nik ILIKE :search)',
        { search: `%${search}%` },
      );
    }

    // Apply role filter
    if (role) {
      queryBuilder.andWhere('user.role = :role', { role });
    }

    // Apply status filter
    if (status) {
      queryBuilder.andWhere('user.status = :status', { status });
    }

    // Apply sorting
    queryBuilder.orderBy(`user.${sortBy}`, sortOrder as 'ASC' | 'DESC');

    // Apply pagination
    const skip = (page - 1) * limit;
    queryBuilder.skip(skip).take(limit);

    const [users, total] = await queryBuilder.getManyAndCount();

    const data = users.map((user) => this.formatUserResponse(user, user.tenant));

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

  async findOne(id: string, currentUser: User): Promise<UserResponseDto> {
    const queryBuilder = this.userRepository
      .createQueryBuilder('user')
      .leftJoinAndSelect('user.tenant', 'tenant')
      .where('user.id = :id', { id });

    // Apply tenant filtering for non-super admins
    if (currentUser.role !== UserRole.SUPER_ADMIN && currentUser.id !== id) {
      queryBuilder.andWhere('user.tenantId = :tenantId', { tenantId: currentUser.tenantId });
    }

    const user = await queryBuilder.getOne();

    if (!user) {
      throw new NotFoundException('User not found');
    }

    return this.formatUserResponse(user, user.tenant);
  }

  async update(
    id: string,
    updateUserDto: UpdateUserDto,
    currentUser: User,
  ): Promise<UserResponseDto> {
    const user = await this.userRepository.findOne({
      where: { id },
      relations: ['tenant'],
    });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    // Check permissions
    if (
      currentUser.role !== UserRole.SUPER_ADMIN &&
      currentUser.role !== UserRole.TENANT_ADMIN &&
      currentUser.id !== id
    ) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role === UserRole.TENANT_ADMIN && user.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot update user from different tenant');
    }

    // Update user fields
    Object.assign(user, updateUserDto);

    const updatedUser = await this.userRepository.save(user);

    return this.formatUserResponse(updatedUser, updatedUser.tenant);
  }

  async remove(id: string, currentUser: User): Promise<void> {
    const user = await this.userRepository.findOne({ where: { id } });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    // Check permissions
    if (currentUser.role !== UserRole.SUPER_ADMIN && currentUser.role !== UserRole.TENANT_ADMIN) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role === UserRole.TENANT_ADMIN && user.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot delete user from different tenant');
    }

    // Prevent self-deletion
    if (user.id === currentUser.id) {
      throw new BadRequestException('Cannot delete your own account');
    }

    await this.userRepository.remove(user);
  }

  async updateStatus(id: string, status: string, currentUser: User): Promise<void> {
    const user = await this.userRepository.findOne({ where: { id } });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    // Check permissions
    if (currentUser.role !== UserRole.SUPER_ADMIN && currentUser.role !== UserRole.TENANT_ADMIN) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role === UserRole.TENANT_ADMIN && user.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot update user from different tenant');
    }

    // Validate status
    if (!Object.values(UserStatus).includes(status as UserStatus)) {
      throw new BadRequestException('Invalid status');
    }

    await this.userRepository.update(id, { status: status as UserStatus });
  }

  async resetPassword(id: string, currentUser: User): Promise<string> {
    const user = await this.userRepository.findOne({ where: { id } });

    if (!user) {
      throw new NotFoundException('User not found');
    }

    // Check permissions
    if (currentUser.role !== UserRole.SUPER_ADMIN && currentUser.role !== UserRole.TENANT_ADMIN) {
      throw new ForbiddenException('Insufficient permissions');
    }

    if (currentUser.role === UserRole.TENANT_ADMIN && user.tenantId !== currentUser.tenantId) {
      throw new ForbiddenException('Cannot reset password for user from different tenant');
    }

    // Generate temporary password
    const temporaryPassword = crypto.randomBytes(12).toString('base64').slice(0, 12);
    const hashedPassword = await bcrypt.hash(temporaryPassword, 12);

    await this.userRepository.update(id, {
      password: hashedPassword,
      failedLoginAttempts: 0,
    });

    return temporaryPassword;
  }

  private formatUserResponse(user: User, tenant?: Tenant): UserResponseDto {
    return {
      id: user.id,
      email: user.email,
      fullName: user.fullName,
      phone: user.phone,
      nik: user.nik,
      role: user.role,
      status: user.status,
      avatar: user.avatar,
      profile: user.profile,
      emailVerifiedAt: user.emailVerifiedAt,
      lastLoginAt: user.lastLoginAt,
      createdAt: user.createdAt,
      updatedAt: user.updatedAt,
      tenant: tenant
        ? {
            id: tenant.id,
            name: tenant.name,
            code: tenant.code,
            type: tenant.type,
          }
        : undefined,
    };
  }
}
