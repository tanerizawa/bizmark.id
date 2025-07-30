import { apiClient } from '@/lib/api';
import { 
  LicenseApplication, 
  ApplicationFormData,
  ApiResponse, 
  PaginatedResponse,
  Document
} from '@/types';

export const applicationService = {
  async getApplications(params?: {
    page?: number;
    limit?: number;
  }): Promise<PaginatedResponse<LicenseApplication>> {
    const searchParams = new URLSearchParams();
    if (params?.page) searchParams.append('page', params.page.toString());
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    
    const url = `/applications${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
    return apiClient.get<PaginatedResponse<LicenseApplication>>(url);
  },

  async getApplicationById(id: string): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.get<ApiResponse<LicenseApplication>>(`/applications/${id}`);
  },

  async createApplication(data: ApplicationFormData): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.post<ApiResponse<LicenseApplication>>('/applications', data);
  },

  async updateApplication(id: string, data: Partial<ApplicationFormData>): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.patch<ApiResponse<LicenseApplication>>(`/applications/${id}`, data);
  },

  async deleteApplication(id: string): Promise<ApiResponse<{ message: string }>> {
    return apiClient.delete<ApiResponse<{ message: string }>>(`/applications/${id}`);
  },

  async submitApplication(id: string): Promise<ApiResponse<LicenseApplication>> {
    return apiClient.post<ApiResponse<LicenseApplication>>(`/applications/${id}/submit`);
  },

  async getApplicationsByBusiness(businessId: string): Promise<ApiResponse<LicenseApplication[]>> {
    return apiClient.get<ApiResponse<LicenseApplication[]>>(`/applications/business/${businessId}`);
  },

  async uploadDocument(applicationId: string, file: File): Promise<ApiResponse<Document>> {
    const formData = new FormData();
    formData.append('file', file);
    
    return apiClient.post<ApiResponse<Document>>(`/applications/${applicationId}/documents`, formData);
  }
};
