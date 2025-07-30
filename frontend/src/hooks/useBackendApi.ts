/**
 * Backend Integration React Hooks
 * Custom hooks for seamless backend API integration
 */

import { useState, useEffect, useCallback } from 'react';
import { backendApi, Business, CreateBusinessRequest } from '../services/backendApi';

// Authentication Hooks
export function useAuth() {
  const [isAuthenticated, setIsAuthenticated] = useState(backendApi.isAuthenticated);
  const [user, setUser] = useState(backendApi.currentUser);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const login = useCallback(async (email: string, password: string) => {
    setLoading(true);
    setError(null);
    
    try {
      const authResponse = await backendApi.login({ email, password });
      setIsAuthenticated(true);
      setUser(backendApi.currentUser);
      return authResponse;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Login failed';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  const register = useCallback(async (userData: {
    email: string;
    password: string;
    firstName: string;
    lastName: string;
    phone?: string;
  }) => {
    setLoading(true);
    setError(null);
    
    try {
      const authResponse = await backendApi.register(userData);
      setIsAuthenticated(true);
      setUser(backendApi.currentUser);
      return authResponse;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Registration failed';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  const logout = useCallback(async () => {
    setLoading(true);
    
    try {
      await backendApi.logout();
      setIsAuthenticated(false);
      setUser(null);
    } catch (err) {
      console.error('Logout error:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    isAuthenticated,
    user,
    loading,
    error,
    login,
    register,
    logout,
  };
}

// Business Management Hooks
export function useBusinesses(page = 1, limit = 10) {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [pagination, setPagination] = useState({
    total: 0,
    page: 1,
    limit: 10,
    totalPages: 0,
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchBusinesses = useCallback(async (pageNum = page, limitNum = limit) => {
    setLoading(true);
    setError(null);

    try {
      const response = await backendApi.getBusinesses(pageNum, limitNum);
      setBusinesses(response.data);
      setPagination({
        total: response.total,
        page: response.page,
        limit: response.limit,
        totalPages: response.totalPages,
      });
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch businesses';
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  }, [page, limit]);

  useEffect(() => {
    fetchBusinesses();
  }, [fetchBusinesses]);

  const refetch = useCallback(() => {
    fetchBusinesses(pagination.page, pagination.limit);
  }, [fetchBusinesses, pagination.page, pagination.limit]);

  return {
    businesses,
    pagination,
    loading,
    error,
    refetch,
    fetchBusinesses,
  };
}

export function useBusiness(id: string | null) {
  const [business, setBusiness] = useState<Business | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchBusiness = useCallback(async (businessId: string) => {
    setLoading(true);
    setError(null);

    try {
      const response = await backendApi.getBusiness(businessId);
      setBusiness(response);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch business';
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    if (id) {
      fetchBusiness(id);
    }
  }, [id, fetchBusiness]);

  return {
    business,
    loading,
    error,
    refetch: () => id && fetchBusiness(id),
  };
}

export function useBusinessMutations() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const createBusiness = useCallback(async (businessData: CreateBusinessRequest) => {
    setLoading(true);
    setError(null);

    try {
      const response = await backendApi.createBusiness(businessData);
      return response;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to create business';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  const updateBusiness = useCallback(async (id: string, businessData: Partial<CreateBusinessRequest>) => {
    setLoading(true);
    setError(null);

    try {
      const response = await backendApi.updateBusiness(id, businessData);
      return response;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to update business';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  const deleteBusiness = useCallback(async (id: string) => {
    setLoading(true);
    setError(null);

    try {
      await backendApi.deleteBusiness(id);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to delete business';
      setError(errorMessage);
      throw err;
    } finally {
      setLoading(false);
    }
  }, []);

  return {
    loading,
    error,
    createBusiness,
    updateBusiness,
    deleteBusiness,
  };
}

// File Upload Hook
export function useFileUpload() {
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);
  const [error, setError] = useState<string | null>(null);

  const uploadFile = useCallback(async (file: File, category: string) => {
    setUploading(true);
    setProgress(0);
    setError(null);

    try {
      // Simulate progress for better UX
      const progressInterval = setInterval(() => {
        setProgress((prev) => Math.min(prev + 10, 90));
      }, 100);

      const response = await backendApi.uploadFile(file, category);
      
      clearInterval(progressInterval);
      setProgress(100);
      
      return response;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to upload file';
      setError(errorMessage);
      throw err;
    } finally {
      setUploading(false);
      setTimeout(() => setProgress(0), 1000);
    }
  }, []);

  return {
    uploading,
    progress,
    error,
    uploadFile,
  };
}

// Health Check Hook
export function useHealthCheck() {
  const [status, setStatus] = useState<'unknown' | 'healthy' | 'unhealthy'>('unknown');
  const [lastCheck, setLastCheck] = useState<Date | null>(null);
  const [loading, setLoading] = useState(false);

  const checkHealth = useCallback(async () => {
    setLoading(true);

    try {
      await backendApi.healthCheck();
      setStatus('healthy');
      setLastCheck(new Date());
    } catch {
      setStatus('unhealthy');
      setLastCheck(new Date());
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    // Check health on mount
    checkHealth();

    // Set up periodic health checks every 5 minutes
    const interval = setInterval(checkHealth, 5 * 60 * 1000);

    return () => clearInterval(interval);
  }, [checkHealth]);

  return {
    status,
    lastCheck,
    loading,
    checkHealth,
  };
}

// Generic Data Fetching Hook
export function useApiData<T>(
  fetchFunction: () => Promise<T>
) {
  const [data, setData] = useState<T | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchData = useCallback(async () => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetchFunction();
      setData(response);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 'Failed to fetch data';
      setError(errorMessage);
    } finally {
      setLoading(false);
    }
  }, [fetchFunction]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  return {
    data,
    loading,
    error,
    refetch: fetchData,
  };
}

// Connection Status Hook
export function useConnectionStatus() {
  const [isOnline, setIsOnline] = useState(
    typeof navigator !== 'undefined' ? navigator.onLine : true
  );
  const [backendConnected, setBackendConnected] = useState(false);

  useEffect(() => {
    const handleOnline = () => setIsOnline(true);
    const handleOffline = () => setIsOnline(false);

    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    return () => {
      window.removeEventListener('online', handleOnline);
      window.removeEventListener('offline', handleOffline);
    };
  }, []);

  useEffect(() => {
    // Check backend connectivity when online status changes
    if (isOnline) {
      backendApi.healthCheck()
        .then(() => setBackendConnected(true))
        .catch(() => setBackendConnected(false));
    } else {
      setBackendConnected(false);
    }
  }, [isOnline]);

  return {
    isOnline,
    backendConnected,
    isFullyConnected: isOnline && backendConnected,
  };
}
