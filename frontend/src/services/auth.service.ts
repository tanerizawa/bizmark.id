import { apiClient } from '@/lib/api';
import { 
  LoginRequest, 
  LoginResponse, 
  RegisterRequest, 
  User, 
  ApiResponse 
} from '@/types';

export const authService = {
  // Login
  async login(credentials: LoginRequest): Promise<LoginResponse> {
    return apiClient.post<LoginResponse>('/auth/login', credentials);
  },

  // Register
  async register(userData: RegisterRequest): Promise<ApiResponse<User>> {
    return apiClient.post<ApiResponse<User>>('/auth/register', userData);
  },

  // Get current user profile
  async getProfile(): Promise<ApiResponse<User>> {
    return apiClient.get<ApiResponse<User>>('/auth/profile');
  },

  // Update profile
  async updateProfile(profileData: Partial<User>): Promise<ApiResponse<User>> {
    return apiClient.patch<ApiResponse<User>>('/auth/profile', profileData);
  },

  // Change password
  async changePassword(passwordData: {
    currentPassword: string;
    newPassword: string;
  }): Promise<ApiResponse<{ message: string }>> {
    return apiClient.post<ApiResponse<{ message: string }>>('/auth/change-password', passwordData);
  },

  // Logout
  async logout(): Promise<ApiResponse<{ message: string }>> {
    return apiClient.post<ApiResponse<{ message: string }>>('/auth/logout');
  },

  // Refresh token
  async refreshToken(): Promise<LoginResponse> {
    return apiClient.post<LoginResponse>('/auth/refresh');
  },

  // Forgot password
  async forgotPassword(email: string): Promise<ApiResponse<{ message: string }>> {
    return apiClient.post<ApiResponse<{ message: string }>>('/auth/forgot-password', { email });
  },

  // Reset password
  async resetPassword(resetData: {
    token: string;
    newPassword: string;
  }): Promise<ApiResponse<{ message: string }>> {
    return apiClient.post<ApiResponse<{ message: string }>>('/auth/reset-password', resetData);
  },
};
