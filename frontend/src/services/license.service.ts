import { apiClient } from '@/lib/api';
import {
  License,
  LicenseApplication,
  LicenseType,
  LicenseApplicationFormData,
  ApiResponse,
  PaginatedResponse,
} from '@/types';

export const licenseService = {
  // Get all license types
  async getLicenseTypes(): Promise<ApiResponse<LicenseType[]>> {
    return apiClient.get<ApiResponse<LicenseType[]>>('/license-types');
  },

  // Get license type by ID
  async getLicenseTypeById(id: string): Promise<ApiResponse<LicenseType>> {
    return apiClient.get<ApiResponse<LicenseType>>(`/license-types/${id}`);
  },

  // Get all licenses for current user
  async getLicenses(params?: {
    page?: number;
    limit?: number;
    search?: string;
    status?: string;
    businessId?: string;
  }): Promise<PaginatedResponse<License>> {
    const searchParams = new URLSearchParams();
    if (params?.page) searchParams.append('page', params.page.toString());
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.search) searchParams.append('search', params.search);
    if (params?.status) searchParams.append('status', params.status);
    if (params?.businessId) searchParams.append('businessId', params.businessId);

    const url = `/licenses${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
    return apiClient.get<PaginatedResponse<License>>(url);
  },

  // Get license by ID
  async getLicenseById(id: string): Promise<ApiResponse<License>> {
    return apiClient.get<ApiResponse<License>>(`/licenses/${id}`);
  },

  // Get applications for current user
  async getApplications(params?: {
    page?: number;
    limit?: number;
    search?: string;
    status?: string;
    businessId?: string;
  }): Promise<PaginatedResponse<LicenseApplication>> {
    const searchParams = new URLSearchParams();
    if (params?.page) searchParams.append('page', params.page.toString());
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.search) searchParams.append('search', params.search);
    if (params?.status) searchParams.append('status', params.status);
    if (params?.businessId) searchParams.append('businessId', params.businessId);

    const url = `/applications${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
    return apiClient.get<PaginatedResponse<LicenseApplication>>(url);
  },

  // Get application by ID
  async getApplicationById(id: string): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.get<ApiResponse<LicenseApplication>>(`/applications/${id}`);
  },

  // Create new application
  async createApplication(applicationData: LicenseApplicationFormData): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.post<ApiResponse<LicenseApplication>>('/applications', applicationData);
  },

  // Update application
  async updateApplication(id: string, applicationData: Partial<LicenseApplicationFormData>): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.patch<ApiResponse<LicenseApplication>>(`/applications/${id}`, applicationData);
  },

  // Submit application
  async submitApplication(id: string): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.post<ApiResponse<LicenseApplication>>(`/applications/${id}/submit`);
  },

  // Cancel application
  async cancelApplication(id: string): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.post<ApiResponse<LicenseApplication>>(`/applications/${id}/cancel`);
  },

  // Delete application (only draft status)
  async deleteApplication(id: string): Promise<ApiResponse<{ message: string }>> {
    return apiClient.delete<ApiResponse<{ message: string }>>(`/applications/${id}`);
  },

  // Get expiring licenses
  async getExpiringLicenses(days: number = 30): Promise<ApiResponse<License[]>> {
    return apiClient.get<ApiResponse<License[]>>(`/licenses/expiring?days=${days}`);
  },
};
