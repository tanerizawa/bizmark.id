/**
 * Backend API Integration Service
 * Central service for all backend API communications
 */

import axios, { AxiosInstance, AxiosResponse } from 'axios';

// API Configuration
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001';
const API_TIMEOUT = 30000; // 30 seconds

// API Response Types
export interface ApiResponse<T = unknown> {
  success: boolean;
  data?: T;
  message?: string;
  error?: string;
  statusCode?: number;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
}

// Authentication Types
export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  phone?: string;
}

export interface AuthResponse {
  access_token: string;
  refresh_token: string;
  user: {
    id: string;
    email: string;
    firstName: string;
    lastName: string;
    role: string;
    isActive: boolean;
  };
}

// Business Types
export interface Business {
  id: string;
  name: string;
  type: 'MICRO' | 'SMALL' | 'MEDIUM';
  description?: string;
  address: string;
  phone: string;
  email: string;
  website?: string;
  nib?: string;
  status: 'DRAFT' | 'PENDING' | 'APPROVED' | 'REJECTED';
  ownerId: string;
  createdAt: string;
  updatedAt: string;
}

export interface CreateBusinessRequest {
  name: string;
  type: 'MICRO' | 'SMALL' | 'MEDIUM';
  description?: string;
  address: string;
  phone: string;
  email: string;
  website?: string;
}

class BackendApiService {
  private api: AxiosInstance;
  private accessToken: string | null = null;

  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      timeout: API_TIMEOUT,
      headers: {
        'Content-Type': 'application/json',
      },
    });

    // Request interceptor to add auth token
    this.api.interceptors.request.use(
      (config) => {
        if (this.accessToken) {
          config.headers.Authorization = `Bearer ${this.accessToken}`;
        }
        return config;
      },
      (error) => Promise.reject(error)
    );

    // Response interceptor for error handling
    this.api.interceptors.response.use(
      (response) => response,
      async (error) => {
        if (error.response?.status === 401) {
          // Token expired, try to refresh
          await this.handleTokenRefresh();
        }
        return Promise.reject(error);
      }
    );

    // Initialize token from localStorage if available
    if (typeof window !== 'undefined') {
      this.accessToken = localStorage.getItem('access_token');
    }
  }

  // Authentication Methods
  async login(credentials: LoginRequest): Promise<AuthResponse> {
    try {
      const response: AxiosResponse<ApiResponse<AuthResponse>> = await this.api.post('/auth/login', credentials);
      
      if (response.data.success && response.data.data) {
        this.setAuthTokens(response.data.data.access_token, response.data.data.refresh_token);
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Login failed');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async register(userData: RegisterRequest): Promise<AuthResponse> {
    try {
      const response: AxiosResponse<ApiResponse<AuthResponse>> = await this.api.post('/auth/register', userData);
      
      if (response.data.success && response.data.data) {
        this.setAuthTokens(response.data.data.access_token, response.data.data.refresh_token);
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Registration failed');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async logout(): Promise<void> {
    try {
      await this.api.post('/auth/logout');
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      this.clearAuthTokens();
    }
  }

  async refreshToken(): Promise<void> {
    const refreshToken = localStorage.getItem('refresh_token');
    if (!refreshToken) {
      throw new Error('No refresh token available');
    }

    try {
      const response: AxiosResponse<ApiResponse<{ access_token: string }>> = 
        await this.api.post('/auth/refresh', { refresh_token: refreshToken });
      
      if (response.data.success && response.data.data) {
        this.accessToken = response.data.data.access_token;
        localStorage.setItem('access_token', this.accessToken);
      }
    } catch (error) {
      this.clearAuthTokens();
      throw error;
    }
  }

  // Business Management Methods
  async getBusinesses(page = 1, limit = 10): Promise<PaginatedResponse<Business>> {
    try {
      const response: AxiosResponse<ApiResponse<PaginatedResponse<Business>>> = 
        await this.api.get(`/businesses?page=${page}&limit=${limit}`);
      
      if (response.data.success && response.data.data) {
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Failed to fetch businesses');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async getBusiness(id: string): Promise<Business> {
    try {
      const response: AxiosResponse<ApiResponse<Business>> = await this.api.get(`/businesses/${id}`);
      
      if (response.data.success && response.data.data) {
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Failed to fetch business');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async createBusiness(businessData: CreateBusinessRequest): Promise<Business> {
    try {
      const response: AxiosResponse<ApiResponse<Business>> = await this.api.post('/businesses', businessData);
      
      if (response.data.success && response.data.data) {
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Failed to create business');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async updateBusiness(id: string, businessData: Partial<CreateBusinessRequest>): Promise<Business> {
    try {
      const response: AxiosResponse<ApiResponse<Business>> = await this.api.put(`/businesses/${id}`, businessData);
      
      if (response.data.success && response.data.data) {
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Failed to update business');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  async deleteBusiness(id: string): Promise<void> {
    try {
      const response: AxiosResponse<ApiResponse> = await this.api.delete(`/businesses/${id}`);
      
      if (!response.data.success) {
        throw new Error(response.data.message || 'Failed to delete business');
      }
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  // File Upload Methods
  async uploadFile(file: File, category: string): Promise<{ url: string; filename: string }> {
    try {
      const formData = new FormData();
      formData.append('file', file);
      formData.append('category', category);

      const response: AxiosResponse<ApiResponse<{ url: string; filename: string }>> = 
        await this.api.post('/files/upload', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
      
      if (response.data.success && response.data.data) {
        return response.data.data;
      }
      
      throw new Error(response.data.message || 'Failed to upload file');
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  // Health Check
  async healthCheck(): Promise<{ status: string; timestamp: string }> {
    try {
      const response: AxiosResponse<{ status: string; timestamp: string }> = await this.api.get('/health');
      return response.data;
    } catch (error) {
      throw this.handleApiError(error);
    }
  }

  // Utility Methods
  private setAuthTokens(accessToken: string, refreshToken: string): void {
    this.accessToken = accessToken;
    if (typeof window !== 'undefined') {
      localStorage.setItem('access_token', accessToken);
      localStorage.setItem('refresh_token', refreshToken);
    }
  }

  private clearAuthTokens(): void {
    this.accessToken = null;
    if (typeof window !== 'undefined') {
      localStorage.removeItem('access_token');
      localStorage.removeItem('refresh_token');
    }
  }

  private async handleTokenRefresh(): Promise<void> {
    try {
      await this.refreshToken();
    } catch {
      // Redirect to login if refresh fails
      if (typeof window !== 'undefined') {
        window.location.href = '/auth/login';
      }
    }
  }

  private handleApiError(error: unknown): Error {
    if (error && typeof error === 'object' && 'response' in error) {
      const axiosError = error as { response: { data?: { message?: string; error?: string }; status: number } };
      // Server responded with error status
      const message = axiosError.response.data?.message || axiosError.response.data?.error || 'Server error';
      return new Error(`${message} (${axiosError.response.status})`);
    } else if (error && typeof error === 'object' && 'request' in error) {
      // Request was made but no response received
      return new Error('Network error - please check your connection');
    } else {
      // Something else happened
      const message = error instanceof Error ? error.message : 'Unknown error occurred';
      return new Error(message);
    }
  }

  // Getter for auth status
  get isAuthenticated(): boolean {
    return !!this.accessToken;
  }

  // Get current user info from token (basic implementation)
  get currentUser(): { sub: string; email: string; role: string; iat: number; exp: number } | null {
    if (!this.accessToken) return null;
    
    try {
      // Basic JWT decode (in production, use a proper JWT library)
      const base64Url = this.accessToken.split('.')[1];
      const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      const jsonPayload = decodeURIComponent(
        atob(base64)
          .split('')
          .map((c) => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
          .join('')
      );
      return JSON.parse(jsonPayload);
    } catch {
      return null;
    }
  }
}

// Export singleton instance
export const backendApi = new BackendApiService();

// Export class for testing
export default BackendApiService;
