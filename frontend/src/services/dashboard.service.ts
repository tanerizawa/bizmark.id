import { apiClient } from '@/lib/api';
import { DashboardStats, ApiResponse, ChartData } from '@/types';

interface Activity {
  id: string;
  type: string;
  description: string;
  timestamp: string;
  entityId: string;
  entityType: string;
}

export const dashboardService = {
  // Get dashboard statistics
  async getStats(): Promise<ApiResponse<DashboardStats>> {
    return apiClient.get<ApiResponse<DashboardStats>>('/dashboard/stats');
  },

  // Get recent activities
  async getRecentActivities(): Promise<ApiResponse<Activity[]>> {
    return apiClient.get<ApiResponse<Activity[]>>('/dashboard/activities');
  },

  // Get license statistics by type
  async getLicenseStats(): Promise<ApiResponse<ChartData>> {
    return apiClient.get<ApiResponse<ChartData>>('/dashboard/license-stats');
  },

  // Get application status distribution
  async getApplicationStats(): Promise<ApiResponse<ChartData>> {
    return apiClient.get<ApiResponse<ChartData>>('/dashboard/application-stats');
  },

  // Get monthly application trends
  async getApplicationTrends(months: number = 12): Promise<ApiResponse<ChartData>> {
    return apiClient.get<ApiResponse<ChartData>>(`/dashboard/trends?months=${months}`);
  },
};
