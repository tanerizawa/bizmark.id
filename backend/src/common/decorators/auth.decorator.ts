import { SetMetadata } from '@nestjs/common';
import { UserRole } from '../../entities';

export const ROLES_KEY = 'roles';
export const Roles = (...roles: UserRole[]) => SetMetadata(ROLES_KEY, roles);

export const PUBLIC_KEY = 'isPublic';
export const Public = () => SetMetadata(PUBLIC_KEY, true);

export const TENANT_REQUIRED_KEY = 'tenantRequired';
export const TenantRequired = () => SetMetadata(TENANT_REQUIRED_KEY, true);

export const PERMISSIONS_KEY = 'permissions';
export const RequirePermissions = (...permissions: string[]) =>
  SetMetadata(PERMISSIONS_KEY, permissions);
