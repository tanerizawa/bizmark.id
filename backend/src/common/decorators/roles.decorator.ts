import { SetMetadata } from '@nestjs/common';
import { UserRole } from '../../entities';

/**
 * Key used for roles metadata
 */
export const ROLES_KEY = 'roles';

/**
 * Decorator for specifying required roles to access a resource
 * @param roles - Array of UserRole values required for access
 */
export const Roles = (...roles: UserRole[]) => SetMetadata(ROLES_KEY, roles);
