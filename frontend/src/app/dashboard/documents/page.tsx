'use client';

import { useState, useEffect } from 'react';
import DocumentUpload from '@/components/DocumentUpload';

interface Document {
  id: string;
  name: string;
  type: string;
  size: number;
  uploadDate: string;
  status: 'uploaded' | 'processing' | 'approved' | 'rejected';
  category: 'identity' | 'business' | 'financial' | 'legal' | 'other';
  applicationId?: string;
  rejectionReason?: string;
  downloadUrl?: string;
}

interface DocumentCategory {
  id: string;
  name: string;
  description: string;
  requiredDocuments: string[];
  optionalDocuments: string[];
}

export default function DocumentsPage() {
  const [documents, setDocuments] = useState<Document[]>([]);
  const [categories, setCategories] = useState<DocumentCategory[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [sortBy, setSortBy] = useState<'name' | 'date' | 'status'>('date');
  const [loading, setLoading] = useState(true);
  const [showUploadModal, setShowUploadModal] = useState(false);

  useEffect(() => {
    const fetchDocuments = async () => {
      try {
        setLoading(true);
        
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        setDocuments([
          {
            id: '1',
            name: 'KTP_Pemilik.pdf',
            type: 'application/pdf',
            size: 2048576,
            uploadDate: '2024-01-20T10:30:00Z',
            status: 'approved',
            category: 'identity',
            applicationId: 'APP001',
            downloadUrl: '/documents/ktp.pdf'
          },
          {
            id: '2',
            name: 'NPWP_Usaha.jpg',
            type: 'image/jpeg',
            size: 1536000,
            uploadDate: '2024-01-20T11:15:00Z',
            status: 'processing',
            category: 'financial',
            applicationId: 'APP001'
          },
          {
            id: '3',
            name: 'Surat_Domisili.pdf',
            type: 'application/pdf',
            size: 3072000,
            uploadDate: '2024-01-19T14:45:00Z',
            status: 'rejected',
            category: 'legal',
            applicationId: 'APP002',
            rejectionReason: 'Dokumen tidak jelas, mohon upload ulang dengan kualitas yang lebih baik'
          },
          {
            id: '4',
            name: 'Akta_Pendirian.pdf',
            type: 'application/pdf',
            size: 4096000,
            uploadDate: '2024-01-18T09:20:00Z',
            status: 'approved',
            category: 'legal',
            applicationId: 'APP003'
          },
          {
            id: '5',
            name: 'Foto_Usaha.jpg',
            type: 'image/jpeg',
            size: 2560000,
            uploadDate: '2024-01-17T16:30:00Z',
            status: 'uploaded',
            category: 'business'
          }
        ]);

        setCategories([
          {
            id: 'identity',
            name: 'Identitas',
            description: 'Dokumen identitas pemilik usaha',
            requiredDocuments: ['KTP', 'Pas Foto'],
            optionalDocuments: ['KK', 'SIM']
          },
          {
            id: 'business',
            name: 'Usaha',
            description: 'Dokumen terkait usaha dan operasional',
            requiredDocuments: ['Foto Tempat Usaha', 'Denah Lokasi'],
            optionalDocuments: ['Logo Usaha', 'Brosur Produk']
          },
          {
            id: 'financial',
            name: 'Keuangan',
            description: 'Dokumen keuangan dan perpajakan',
            requiredDocuments: ['NPWP', 'Rekening Koran'],
            optionalDocuments: ['Laporan Keuangan', 'Slip Gaji']
          },
          {
            id: 'legal',
            name: 'Legal',
            description: 'Dokumen legal dan perizinan',
            requiredDocuments: ['Surat Domisili', 'IMB'],
            optionalDocuments: ['Akta Pendirian', 'SK Menkumham']
          }
        ]);
      } catch (error) {
        console.error('Error fetching documents:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchDocuments();
  }, []);

  const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getStatusColor = (status: Document['status']) => {
    switch (status) {
      case 'approved':
        return 'bg-green-100 text-green-800';
      case 'rejected':
        return 'bg-red-100 text-red-800';
      case 'processing':
        return 'bg-yellow-100 text-yellow-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const getStatusIcon = (status: Document['status']) => {
    switch (status) {
      case 'approved':
        return '✅';
      case 'rejected':
        return '❌';
      case 'processing':
        return '⏳';
      default:
        return '📄';
    }
  };

  const filteredDocuments = documents.filter(doc => {
    const matchesCategory = selectedCategory === 'all' || doc.category === selectedCategory;
    const matchesSearch = doc.name.toLowerCase().includes(searchQuery.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  const sortedDocuments = filteredDocuments.sort((a, b) => {
    switch (sortBy) {
      case 'name':
        return a.name.localeCompare(b.name);
      case 'date':
        return new Date(b.uploadDate).getTime() - new Date(a.uploadDate).getTime();
      case 'status':
        return a.status.localeCompare(b.status);
      default:
        return 0;
    }
  });

  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Manajemen Dokumen</h1>
          <p className="text-gray-600 mt-1">
            Kelola semua dokumen untuk permohonan izin usaha Anda
          </p>
        </div>
        <button
          onClick={() => setShowUploadModal(true)}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2"
        >
          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
          </svg>
          <span>Upload Dokumen</span>
        </button>
      </div>

      {/* Document Categories Overview */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {categories.map((category) => {
          const categoryDocs = documents.filter(doc => doc.category === category.id);
          const approvedDocs = categoryDocs.filter(doc => doc.status === 'approved').length;
          
          return (
            <div key={category.id} className="bg-white rounded-lg shadow p-4">
              <h3 className="font-semibold text-gray-900 mb-2">{category.name}</h3>
              <p className="text-sm text-gray-600 mb-3">{category.description}</p>
              <div className="flex items-center justify-between">
                <div className="text-sm">
                  <span className="font-medium">{approvedDocs}</span>
                  <span className="text-gray-500">/{categoryDocs.length} docs</span>
                </div>
                <div className="w-16 bg-gray-200 rounded-full h-2">
                  <div
                    className="bg-blue-600 h-2 rounded-full"
                    style={{ width: `${categoryDocs.length > 0 ? (approvedDocs / categoryDocs.length) * 100 : 0}%` }}
                  ></div>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Filters and Search */}
      <div className="bg-white rounded-lg shadow p-6">
        <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
          <div className="flex flex-col md:flex-row gap-4 items-center">
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="all">Semua Kategori</option>
              {categories.map((category) => (
                <option key={category.id} value={category.id}>
                  {category.name}
                </option>
              ))}
            </select>

            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value as 'name' | 'date' | 'status')}
              className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="date">Urutkan: Tanggal</option>
              <option value="name">Urutkan: Nama</option>
              <option value="status">Urutkan: Status</option>
            </select>
          </div>

          <div className="relative">
            <input
              type="text"
              placeholder="Cari dokumen..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
            <svg className="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>
      </div>

      {/* Documents List */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-200">
          <h3 className="text-lg font-semibold text-gray-900">
            Dokumen Anda ({sortedDocuments.length})
          </h3>
        </div>

        {sortedDocuments.length === 0 ? (
          <div className="text-center py-12">
            <div className="text-gray-400 text-6xl mb-4">📁</div>
            <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada dokumen</h3>
            <p className="text-gray-600 mb-4">
              {searchQuery || selectedCategory !== 'all' 
                ? 'Tidak ada dokumen yang sesuai dengan filter yang dipilih'
                : 'Belum ada dokumen yang diupload'
              }
            </p>
            <button
              onClick={() => setShowUploadModal(true)}
              className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Upload Dokumen Pertama
            </button>
          </div>
        ) : (
          <div className="divide-y divide-gray-200">
            {sortedDocuments.map((document) => (
              <div key={document.id} className="p-6 hover:bg-gray-50 transition-colors">
                <div className="flex items-center justify-between">
                  <div className="flex items-center space-x-4">
                    <div className="text-3xl">{getStatusIcon(document.status)}</div>
                    <div>
                      <h4 className="font-medium text-gray-900">{document.name}</h4>
                      <div className="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                        <span>{formatFileSize(document.size)}</span>
                        <span>•</span>
                        <span>{formatDate(document.uploadDate)}</span>
                        <span>•</span>
                        <span className="capitalize">{categories.find(cat => cat.id === document.category)?.name || document.category}</span>
                      </div>
                      {document.applicationId && (
                        <span className="inline-block mt-1 text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                          {document.applicationId}
                        </span>
                      )}
                    </div>
                  </div>

                  <div className="flex items-center space-x-4">
                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(document.status)}`}>
                      {document.status === 'approved' ? 'Disetujui' :
                       document.status === 'rejected' ? 'Ditolak' :
                       document.status === 'processing' ? 'Diproses' : 'Terupload'}
                    </span>

                    <div className="flex space-x-2">
                      {document.downloadUrl && (
                        <button className="p-2 text-gray-400 hover:text-blue-600 transition-colors">
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                        </button>
                      )}
                      <button className="p-2 text-gray-400 hover:text-green-600 transition-colors">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                      </button>
                      <button className="p-2 text-gray-400 hover:text-red-600 transition-colors">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                {document.status === 'rejected' && document.rejectionReason && (
                  <div className="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div className="flex items-start">
                      <svg className="w-5 h-5 text-red-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <div>
                        <p className="text-sm font-medium text-red-800">Alasan Penolakan:</p>
                        <p className="text-sm text-red-700 mt-1">{document.rejectionReason}</p>
                      </div>
                    </div>
                  </div>
                )}
              </div>
            ))}
          </div>
        )}
      </div>

      {/* Upload Modal */}
      {showUploadModal && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 w-full max-w-md mx-4">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-semibold text-gray-900">Upload Dokumen</h3>
              <button
                onClick={() => setShowUploadModal(false)}
                className="text-gray-400 hover:text-gray-600"
              >
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <DocumentUpload
              onUploadComplete={() => {
                setShowUploadModal(false);
                // Refresh documents - in a real app, you'd refetch from the server
                console.log('Upload completed');
              }}
            />
          </div>
        </div>
      )}
    </div>
  );
}
