// User & Authentication
export interface User {
  id: string;
  email: string;
  role?: string;
  profile: UserProfile;
  createdAt: string;
  updatedAt: string;
}

export interface UserProfile {
  id: string;
  fullName: string;
  phone?: string;
  address?: string;
  dateOfBirth?: string;
  profilePicture?: string;
  businessName?: string;
  businessType?: string;
  businessAddress?: string;
  isActive: boolean;
  lastLoginAt?: string;
  createdAt: string;
  updatedAt: string;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface LoginResponse {
  access_token: string;
  refresh_token: string;
  user: User;
}

export interface RegisterRequest {
  email: string;
  password: string;
  fullName: string;
  phone?: string;
}

// Business
export interface Business {
  id: string;
  name: string;
  description?: string;
  businessType: BusinessType;
  address: string;
  phone: string;
  email?: string;
  ownerName: string;
  ownerIdNumber: string;
  isActive: boolean;
  userId: string;
  createdAt: string;
  updatedAt: string;
}

export enum BusinessType {
  MICRO = 'MICRO',
  SMALL = 'SMALL',
  MEDIUM = 'MEDIUM'
}

// License
export interface License {
  id: string;
  licenseNumber: string;
  licenseType: LicenseTypeInfo;
  status: LicenseStatus;
  applicationDate: string;
  approvalDate?: string;
  expiryDate: string;
  issuingAuthority: string;
  businessId: string;
  business: Business;
  createdAt: string;
  updatedAt: string;
}

export enum LicenseType {
  BUSINESS_PERMIT = 'BUSINESS_PERMIT',
  TRADE_PERMIT = 'TRADE_PERMIT',
  LOCATION_PERMIT = 'LOCATION_PERMIT',
  ENVIRONMENT_PERMIT = 'ENVIRONMENT_PERMIT',
  BUILDING_PERMIT = 'BUILDING_PERMIT',
  OPERATIONAL_PERMIT = 'OPERATIONAL_PERMIT'
}

export interface LicenseTypeInfo {
  id: string;
  name: string;
  description?: string;
  validityPeriod: number; // in months
  fee: number;
  requirements: string[];
  isActive: boolean;
  createdAt: string;
  updatedAt: string;
}

export enum LicenseStatus {
  DRAFT = 'DRAFT',
  SUBMITTED = 'SUBMITTED',
  UNDER_REVIEW = 'UNDER_REVIEW',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED',
  EXPIRED = 'EXPIRED',
  CANCELLED = 'CANCELLED'
}

// Application
export interface LicenseApplication {
  id: string;
  applicationNumber: string;
  status: ApplicationStatus;
  submittedAt?: string;
  processedAt?: string;
  rejectionReason?: string;
  notes?: string;
  licenseTypeId: string;
  licenseType: LicenseTypeInfo;
  businessId: string;
  business: Business;
  createdAt: string;
  updatedAt: string;
}

export enum ApplicationStatus {
  DRAFT = 'DRAFT',
  SUBMITTED = 'SUBMITTED',
  UNDER_REVIEW = 'UNDER_REVIEW',
  APPROVED = 'APPROVED',
  REJECTED = 'REJECTED'
}

// Document
export interface Document {
  id: string;
  filename: string;
  originalName: string;
  mimeType: string;
  size: number;
  documentType: DocumentType;
  applicationId?: string;
  businessId?: string;
  uploadedAt: string;
}

export enum DocumentType {
  BUSINESS_REGISTRATION = 'BUSINESS_REGISTRATION',
  ID_CARD = 'ID_CARD',
  TAX_REGISTRATION = 'TAX_REGISTRATION',
  BUILDING_PERMIT = 'BUILDING_PERMIT',
  ENVIRONMENTAL_PERMIT = 'ENVIRONMENTAL_PERMIT',
  OTHER = 'OTHER'
}

// API Response Types
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export interface PaginatedResponse<T> {
  success: boolean;
  data: T[];
  meta: {
    page: number;
    limit: number;
    total: number;
    totalPages: number;
  };
}

// Form Types
export interface BusinessFormData {
  name: string;
  description?: string;
  businessType: BusinessType;
  address: string;
  phone: string;
  email?: string;
  ownerName: string;
  ownerIdNumber: string;
}

export interface LicenseApplicationFormData {
  licenseTypeId: string;
  businessId: string;
  notes?: string;
}

export interface ApplicationFormData {
  businessId: string;
  licenseType: LicenseType;
  notes?: string;
}

// Dashboard Stats
export interface DashboardStats {
  totalBusinesses: number;
  totalLicenses: number;
  activeLicenses: number;
  pendingApplications: number;
  expiringLicenses: number;
}

// Chart Data
export interface ChartData {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
    backgroundColor?: string[];
    borderColor?: string[];
  }[];
}
