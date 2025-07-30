import { apiClient } from '@/lib/api';
import { Document, ApiResponse, PaginatedResponse } from '@/types';

export const documentService = {
  // Upload document
  async uploadDocument(
    file: File,
    documentType: string,
    entityId?: string,
    entityType?: 'application' | 'business'
  ): Promise<ApiResponse<Document>> {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('documentType', documentType);
    if (entityId) formData.append('entityId', entityId);
    if (entityType) formData.append('entityType', entityType);

    // Override content type for file upload
    const response = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/documents/upload`, {
      method: 'POST',
      body: formData,
      headers: {
        'Authorization': `Bearer ${document.cookie.split('access_token=')[1]?.split(';')[0]}`,
      },
    });

    if (!response.ok) {
      throw new Error('Upload failed');
    }

    return response.json();
  },

  // Get documents
  async getDocuments(params?: {
    page?: number;
    limit?: number;
    documentType?: string;
    entityId?: string;
    entityType?: string;
  }): Promise<PaginatedResponse<Document>> {
    const searchParams = new URLSearchParams();
    if (params?.page) searchParams.append('page', params.page.toString());
    if (params?.limit) searchParams.append('limit', params.limit.toString());
    if (params?.documentType) searchParams.append('documentType', params.documentType);
    if (params?.entityId) searchParams.append('entityId', params.entityId);
    if (params?.entityType) searchParams.append('entityType', params.entityType);

    const url = `/documents${searchParams.toString() ? `?${searchParams.toString()}` : ''}`;
    return apiClient.get<PaginatedResponse<Document>>(url);
  },

  // Get document by ID
  async getDocumentById(id: string): Promise<ApiResponse<Document>> {
    return apiClient.get<ApiResponse<Document>>(`/documents/${id}`);
  },

  // Download document
  async downloadDocument(id: string): Promise<Blob> {
    const response = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/documents/${id}/download`, {
      headers: {
        'Authorization': `Bearer ${document.cookie.split('access_token=')[1]?.split(';')[0]}`,
      },
    });

    if (!response.ok) {
      throw new Error('Download failed');
    }

    return response.blob();
  },

  // Delete document
  async deleteDocument(id: string): Promise<ApiResponse<{ message: string }>> {
    return apiClient.delete<ApiResponse<{ message: string }>>(`/documents/${id}`);
  },

  // Get document URL for preview
  getDocumentUrl(id: string): string {
    return `${process.env.NEXT_PUBLIC_API_BASE_URL}/documents/${id}/preview`;
  },
};
