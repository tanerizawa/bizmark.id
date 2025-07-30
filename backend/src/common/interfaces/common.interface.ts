export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  error?: any;
  timestamp: string;
  path: string;
}

export interface CurrentUser {
  id: string;
  email: string;
  fullName: string;
  role: string;
  tenantId: string;
  status: string;
  iat?: number;
  exp?: number;
}

export interface PaginatedResponse<T = any> {
  data: T[];
  meta: {
    page: number;
    limit: number;
    total: number;
    totalPages: number;
    hasNext: boolean;
    hasPrev: boolean;
  };
}

export interface QueryOptions {
  page?: number;
  limit?: number;
  sortBy?: string;
  sortOrder?: 'ASC' | 'DESC';
  search?: string;
}

export interface FileUploadResult {
  fileName: string;
  originalName: string;
  filePath: string;
  mimeType: string;
  fileSize: number;
  hash?: string;
}

export interface CurrentUser {
  id: string;
  email: string;
  fullName: string;
  role: string;
  tenantId: string;
  status: string;
  permissions?: string[];
  iat?: number;
  exp?: number;
}

export interface JwtPayload {
  sub: string;
  email: string;
  role: string;
  tenantId?: string;
  iat?: number;
  exp?: number;
}

export interface DatabaseOptions {
  transaction?: boolean;
  relations?: string[];
  select?: string[];
}

export interface AuditContext {
  userId?: string;
  tenantId?: string;
  ipAddress?: string;
  userAgent?: string;
  action: string;
  entityType: string;
  entityId?: string;
}
