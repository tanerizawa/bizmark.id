import { apiClient } from '@/lib/api';
import {
  Business,
  BusinessFormData,
  ApiResponse,
  PaginatedResponse,
} from '@/types';

export const businessService = {
  // Get all businesses for current user
  async getBusinesses(params?: {
    page?: number;
    limit?: number;
    search?: string;
    businessType?: string;
  }): Promise<PaginatedResponse<Business>> {
    const searchParams = new URLSearchParams();
    if (params?.page) searchParams.append('page', params.page.toString());
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.search) searchParams.append('search', params.search);
    if (params?.businessType) searchParams.append('businessType', params.businessType);

    const url = `/businesses${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
    return apiClient.get<PaginatedResponse<Business>>(url);
  },

  // Get business by ID
  async getBusinessById(id: string): Promise<ApiResponse<Business>> {
    return apiClient.get<ApiResponse<Business>>(`/businesses/${id}`);
  },

  // Create new business
  async createBusiness(businessData: BusinessFormData): Promise<ApiResponse<Business>> {
    return apiClient.post<ApiResponse<Business>>('/businesses', businessData);
  },

  // Update business
  async updateBusiness(id: string, businessData: Partial<BusinessFormData>): Promise<ApiResponse<Business>> {
    return apiClient.patch<ApiResponse<Business>>(`/businesses/${id}`, businessData);
  },

  // Delete business
  async deleteBusiness(id: string): Promise<ApiResponse<{ message: string }>> {
    return apiClient.delete<ApiResponse<{ message: string }>>(`/businesses/${id}`);
  },

  // Toggle business status
  async toggleBusinessStatus(id: string): Promise<ApiResponse<Business>> {
    return apiClient.patch<ApiResponse<Business>>(`/businesses/${id}/toggle-status`);
  },
};
