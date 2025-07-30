'use client';

import { useState, useEffect, useCallback } from 'react';
import { getGovernmentApiService, NIBData, BusinessLicenseData } from '@/services/governmentApi';

export interface UseGovernmentApiOptions {
  autoRetry?: boolean;
  retryAttempts?: number;
  retryDelay?: number;
}

export interface ApiState<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
  success: boolean;
}

/**
 * Hook for NIB verification
 */
export const useNIBVerification = () => {
  const [state, setState] = useState<ApiState<NIBData>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const verifyNIB = useCallback(async (nib: string) => {
    if (!nib || nib.length < 10) {
      setState({
        data: null,
        loading: false,
        error: 'NIB harus berisi minimal 10 karakter',
        success: false,
      });
      return;
    }

    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.verifyNIB(nib);

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, []);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    verifyNIB,
    clearState,
  };
};

/**
 * Hook for business license operations
 */
export const useBusinessLicense = () => {
  const [state, setState] = useState<ApiState<BusinessLicenseData>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const getLicense = useCallback(async (licenseNumber: string) => {
    if (!licenseNumber) {
      setState({
        data: null,
        loading: false,
        error: 'Nomor izin tidak boleh kosong',
        success: false,
      });
      return;
    }

    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.getBusinessLicense(licenseNumber);

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, []);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    getLicense,
    clearState,
  };
};

/**
 * Hook for license application submission
 */
export const useLicenseApplication = () => {
  const [state, setState] = useState<ApiState<{ applicationId: string; status: string }>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const submitApplication = useCallback(async (applicationData: {
    businessName: string;
    businessType: string;
    ownerName: string;
    ownerNIK: string;
    address: string;
    activities: string[];
    documents: { type: string; fileUrl: string }[];
  }) => {
    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.submitLicenseApplication(applicationData);

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, []);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    submitApplication,
    clearState,
  };
};

/**
 * Hook for application status tracking
 */
export const useApplicationStatus = (applicationId?: string) => {
  const [state, setState] = useState<ApiState<{
    status: 'pending' | 'approved' | 'rejected' | 'requires_revision';
    message: string;
    lastUpdate: string;
    requirements?: string[];
  }>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const checkStatus = useCallback(async (id?: string) => {
    const targetId = id || applicationId;
    if (!targetId) {
      setState({
        data: null,
        loading: false,
        error: 'Application ID tidak ditemukan',
        success: false,
      });
      return;
    }

    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.checkApplicationStatus(targetId);

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, [applicationId]);

  // Auto-check status every 30 seconds if applicationId is provided
  useEffect(() => {
    if (applicationId) {
      const interval = setInterval(() => {
        checkStatus();
      }, 30000);

      return () => clearInterval(interval);
    }
  }, [applicationId, checkStatus]);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    checkStatus,
    clearState,
  };
};

/**
 * Hook for business categories
 */
export const useBusinessCategories = () => {
  const [state, setState] = useState<ApiState<{
    categories: Array<{
      id: string;
      name: string;
      description: string;
      requirements: string[];
      documents: string[];
    }>;
  }>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const fetchCategories = useCallback(async () => {
    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.getBusinessCategories();

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, []);

  // Auto-fetch on mount
  useEffect(() => {
    fetchCategories();
  }, [fetchCategories]);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    fetchCategories,
    clearState,
  };
};

/**
 * Hook for business data validation
 */
export const useBusinessValidation = () => {
  const [state, setState] = useState<ApiState<{
    isValid: boolean;
    conflicts: string[];
    suggestions: string[];
  }>>({
    data: null,
    loading: false,
    error: null,
    success: false,
  });

  const validateBusiness = useCallback(async (data: {
    businessName: string;
    ownerNIK: string;
    address: string;
  }) => {
    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const apiService = getGovernmentApiService();
      const response = await apiService.validateBusinessData(data);

      if (response.success && response.data) {
        setState({
          data: response.data,
          loading: false,
          error: null,
          success: true,
        });
      } else {
        setState({
          data: null,
          loading: false,
          error: response.error || response.message,
          success: false,
        });
      }
    } catch (error) {
      setState({
        data: null,
        loading: false,
        error: error instanceof Error ? error.message : 'Terjadi kesalahan',
        success: false,
      });
    }
  }, []);

  const clearState = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
      success: false,
    });
  }, []);

  return {
    ...state,
    validateBusiness,
    clearState,
  };
};
