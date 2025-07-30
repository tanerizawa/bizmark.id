/**
 * Government API Integration Service
 * Handles integration with Indonesian government systems (OSS, NIB, etc.)
 */

export interface OSSApiResponse<T = unknown> {
  success: boolean;
  message: string;
  data?: T;
  error?: string;
  timestamp: string;
}

export interface NIBData {
  nib: string;
  businessName: string;
  businessType: string;
  address: string;
  owner: string;
  status: 'active' | 'inactive' | 'suspended';
  issuedDate: string;
  validUntil: string;
  activities: string[];
}

export interface BusinessLicenseData {
  licenseNumber: string;
  licenseType: string;
  businessName: string;
  status: 'active' | 'expired' | 'revoked';
  issuedDate: string;
  expiryDate: string;
  authority: string;
  requirements: string[];
}

export interface GovernmentApiConfig {
  baseUrl: string;
  apiKey: string;
  clientId: string;
  clientSecret: string;
  environment: 'sandbox' | 'production';
}

class GovernmentApiService {
  private config: GovernmentApiConfig;
  private accessToken: string | null = null;
  private tokenExpiry: Date | null = null;

  constructor(config: GovernmentApiConfig) {
    this.config = config;
  }

  /**
   * Authenticate with OSS API
   */
  async authenticate(): Promise<string> {
    try {
      const response = await fetch(`${this.config.baseUrl}/auth/token`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-API-Key': this.config.apiKey,
        },
        body: JSON.stringify({
          client_id: this.config.clientId,
          client_secret: this.config.clientSecret,
          grant_type: 'client_credentials',
        }),
      });

      if (!response.ok) {
        throw new Error(`Authentication failed: ${response.statusText}`);
      }

      const data = await response.json();
      this.accessToken = data.access_token;
      this.tokenExpiry = new Date(Date.now() + data.expires_in * 1000);

      if (!this.accessToken) {
        throw new Error('Invalid access token received');
      }

      return this.accessToken;
    } catch (error) {
      console.error('OSS Authentication error:', error);
      throw new Error('Failed to authenticate with government API');
    }
  }

  /**
   * Ensure valid access token
   */
  private async ensureAuthenticated(): Promise<void> {
    if (!this.accessToken || !this.tokenExpiry || new Date() >= this.tokenExpiry) {
      await this.authenticate();
    }
  }

  /**
   * Make authenticated API request
   */
  private async apiRequest<T>(
    endpoint: string,
    options: RequestInit = {}
  ): Promise<OSSApiResponse<T>> {
    await this.ensureAuthenticated();

    const response = await fetch(`${this.config.baseUrl}${endpoint}`, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${this.accessToken}`,
        'X-API-Key': this.config.apiKey,
        ...options.headers,
      },
    });

    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || `API request failed: ${response.statusText}`);
    }

    return {
      success: true,
      message: data.message || 'Success',
      data: data.data || data,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Verify NIB (Nomor Induk Berusaha)
   */
  async verifyNIB(nib: string): Promise<OSSApiResponse<NIBData>> {
    try {
      return await this.apiRequest<NIBData>(`/nib/verify/${nib}`, {
        method: 'GET',
      });
    } catch (error) {
      return {
        success: false,
        message: 'NIB verification failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Get business license information
   */
  async getBusinessLicense(licenseNumber: string): Promise<OSSApiResponse<BusinessLicenseData>> {
    try {
      return await this.apiRequest<BusinessLicenseData>(`/license/${licenseNumber}`, {
        method: 'GET',
      });
    } catch (error) {
      return {
        success: false,
        message: 'License retrieval failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Submit new business license application
   */
  async submitLicenseApplication(applicationData: {
    businessName: string;
    businessType: string;
    ownerName: string;
    ownerNIK: string;
    address: string;
    activities: string[];
    documents: { type: string; fileUrl: string }[];
  }): Promise<OSSApiResponse<{ applicationId: string; status: string }>> {
    try {
      return await this.apiRequest(`/license/apply`, {
        method: 'POST',
        body: JSON.stringify(applicationData),
      });
    } catch (error) {
      return {
        success: false,
        message: 'License application submission failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Check application status
   */
  async checkApplicationStatus(applicationId: string): Promise<OSSApiResponse<{
    status: 'pending' | 'approved' | 'rejected' | 'requires_revision';
    message: string;
    lastUpdate: string;
    requirements?: string[];
  }>> {
    try {
      return await this.apiRequest(`/application/${applicationId}/status`, {
        method: 'GET',
      });
    } catch (error) {
      return {
        success: false,
        message: 'Status check failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Get business categories and requirements
   */
  async getBusinessCategories(): Promise<OSSApiResponse<{
    categories: Array<{
      id: string;
      name: string;
      description: string;
      requirements: string[];
      documents: string[];
    }>;
  }>> {
    try {
      return await this.apiRequest('/categories', {
        method: 'GET',
      });
    } catch (error) {
      return {
        success: false,
        message: 'Failed to fetch business categories',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }

  /**
   * Validate business data against government records
   */
  async validateBusinessData(data: {
    businessName: string;
    ownerNIK: string;
    address: string;
  }): Promise<OSSApiResponse<{
    isValid: boolean;
    conflicts: string[];
    suggestions: string[];
  }>> {
    try {
      return await this.apiRequest('/validate', {
        method: 'POST',
        body: JSON.stringify(data),
      });
    } catch (error) {
      return {
        success: false,
        message: 'Business data validation failed',
        error: error instanceof Error ? error.message : 'Unknown error',
        timestamp: new Date().toISOString(),
      };
    }
  }
}

// Create singleton instance
let governmentApiInstance: GovernmentApiService | null = null;

export const createGovernmentApiService = (config: GovernmentApiConfig): GovernmentApiService => {
  if (!governmentApiInstance) {
    governmentApiInstance = new GovernmentApiService(config);
  }
  return governmentApiInstance;
};

export const getGovernmentApiService = (): GovernmentApiService => {
  if (!governmentApiInstance) {
    throw new Error('Government API service not initialized. Call createGovernmentApiService first.');
  }
  return governmentApiInstance;
};

export default GovernmentApiService;
