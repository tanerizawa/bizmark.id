'use client';

import { useState } from 'react';
import { documentService } from '@/services/document.service';
import { DocumentType } from '@/types';

interface DocumentUploadProps {
  applicationId?: string;
  businessId?: string;
  onUploadComplete?: () => void;
}

export default function DocumentUpload({ applicationId, businessId, onUploadComplete }: DocumentUploadProps) {
  const [uploading, setUploading] = useState(false);
  const [dragActive, setDragActive] = useState(false);
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [documentType, setDocumentType] = useState<DocumentType>(DocumentType.OTHER);
  const [error, setError] = useState('');

  const handleDrag = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    if (e.type === 'dragenter' || e.type === 'dragover') {
      setDragActive(true);
    } else if (e.type === 'dragleave') {
      setDragActive(false);
    }
  };

  const handleDrop = (e: React.DragEvent) => {
    e.preventDefault();
    e.stopPropagation();
    setDragActive(false);
    
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      setSelectedFile(e.dataTransfer.files[0]);
      setError('');
    }
  };

  const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files[0]) {
      setSelectedFile(e.target.files[0]);
      setError('');
    }
  };

  const handleUpload = async () => {
    if (!selectedFile) {
      setError('Pilih file yang akan diupload');
      return;
    }

    if (!applicationId && !businessId) {
      setError('ID aplikasi atau bisnis diperlukan');
      return;
    }

    setUploading(true);
    setError('');

    try {
      if (applicationId) {
        await documentService.uploadDocument(selectedFile, documentType, applicationId, 'application');
      } else if (businessId) {
        await documentService.uploadDocument(selectedFile, documentType, businessId, 'business');
      }

      setSelectedFile(null);
      setDocumentType(DocumentType.OTHER);
      onUploadComplete?.();
    } catch (error) {
      console.error('Upload failed:', error);
      setError('Gagal mengupload dokumen. Silakan coba lagi.');
    } finally {
      setUploading(false);
    }
  };

  const getDocumentTypeLabel = (type: DocumentType): string => {
    const labels = {
      [DocumentType.BUSINESS_REGISTRATION]: 'Akta Pendirian Usaha',
      [DocumentType.ID_CARD]: 'Kartu Identitas (KTP)',
      [DocumentType.TAX_REGISTRATION]: 'NPWP',
      [DocumentType.BUILDING_PERMIT]: 'IMB (Izin Mendirikan Bangunan)',
      [DocumentType.ENVIRONMENTAL_PERMIT]: 'Izin Lingkungan',
      [DocumentType.OTHER]: 'Dokumen Lainnya',
    };
    return labels[type] || type;
  };

  const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  return (
    <div className="bg-white rounded-lg shadow p-6">
      <h3 className="text-lg font-semibold text-gray-900 mb-4">Upload Dokumen</h3>
      
      {/* Document Type Selection */}
      <div className="mb-4">
        <label htmlFor="documentType" className="block text-sm font-medium text-gray-700 mb-2">
          Jenis Dokumen *
        </label>
        <select
          id="documentType"
          value={documentType}
          onChange={(e) => setDocumentType(e.target.value as DocumentType)}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          {Object.values(DocumentType).map((type) => (
            <option key={type} value={type}>
              {getDocumentTypeLabel(type)}
            </option>
          ))}
        </select>
      </div>

      {/* File Upload Area */}
      <div
        className={`relative border-2 border-dashed rounded-lg p-6 text-center transition-colors ${
          dragActive
            ? 'border-blue-400 bg-blue-50'
            : selectedFile
            ? 'border-green-400 bg-green-50'
            : 'border-gray-300 hover:border-gray-400'
        }`}
        onDragEnter={handleDrag}
        onDragLeave={handleDrag}
        onDragOver={handleDrag}
        onDrop={handleDrop}
      >
        <input
          type="file"
          id="file-upload"
          className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
          onChange={handleFileSelect}
          accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
        />
        
        {selectedFile ? (
          <div className="space-y-2">
            <div className="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p className="text-sm font-medium text-gray-900">{selectedFile.name}</p>
              <p className="text-xs text-gray-500">{formatFileSize(selectedFile.size)}</p>
            </div>
            <button
              type="button"
              onClick={() => setSelectedFile(null)}
              className="text-sm text-red-600 hover:text-red-500"
            >
              Hapus File
            </button>
          </div>
        ) : (
          <div className="space-y-2">
            <div className="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
            </div>
            <div>
              <p className="text-sm text-gray-900">
                <span className="font-medium text-blue-600 hover:text-blue-500 cursor-pointer">
                  Klik untuk upload
                </span>{' '}
                atau drag & drop file di sini
              </p>
              <p className="text-xs text-gray-500">PDF, DOC, DOCX, JPG, PNG (maks. 10MB)</p>
            </div>
          </div>
        )}
      </div>

      {/* Error Message */}
      {error && (
        <div className="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
          {error}
        </div>
      )}

      {/* Upload Button */}
      <div className="mt-6 flex justify-end">
        <button
          type="button"
          onClick={handleUpload}
          disabled={!selectedFile || uploading}
          className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          {uploading ? 'Mengupload...' : 'Upload Dokumen'}
        </button>
      </div>
    </div>
  );
}
