'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import { applicationService } from '@/services/application.service';
import { documentService } from '@/services/document.service';
import { LicenseApplication, Document } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';
import StatusTimeline from '@/components/StatusTimeline';
import DocumentUpload from '@/components/DocumentUpload';

export default function ApplicationDetailPage() {
  const params = useParams();
  const applicationId = params.id as string;
  
  const [loading, setLoading] = useState(true);
  const [application, setApplication] = useState<LicenseApplication | null>(null);
  const [documents, setDocuments] = useState<Document[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [applicationResponse, documentsResponse] = await Promise.all([
          applicationService.getApplicationById(applicationId),
          documentService.getDocuments({ entityId: applicationId, entityType: 'application' })
        ]);
        
        setApplication(applicationResponse.data);
        setDocuments(documentsResponse.data);
      } catch (error) {
        console.error('Failed to fetch application data:', error);
        setError('Gagal memuat data aplikasi');
      } finally {
        setLoading(false);
      }
    };

    if (applicationId) {
      fetchData();
    }
  }, [applicationId]);

  const handleDocumentUpload = async () => {
    // Refresh documents after upload
    try {
      const response = await documentService.getDocuments({ entityId: applicationId, entityType: 'application' });
      setDocuments(response.data);
    } catch (error) {
      console.error('Failed to refresh documents:', error);
    }
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

  const getStatusColor = (status: string): string => {
    const colors: Record<string, string> = {
      DRAFT: 'bg-gray-100 text-gray-800',
      SUBMITTED: 'bg-blue-100 text-blue-800',
      UNDER_REVIEW: 'bg-yellow-100 text-yellow-800',
      APPROVED: 'bg-green-100 text-green-800',
      REJECTED: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
  };

  const getStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
      DRAFT: 'Draft',
      SUBMITTED: 'Dikirim',
      UNDER_REVIEW: 'Sedang Ditinjau',
      APPROVED: 'Disetujui',
      REJECTED: 'Ditolak',
    };
    return labels[status] || status;
  };

  const getDocumentTypeLabel = (type: string): string => {
    const labels: Record<string, string> = {
      BUSINESS_REGISTRATION: 'Akta Pendirian Usaha',
      ID_CARD: 'KTP',
      TAX_REGISTRATION: 'NPWP',
      BUILDING_PERMIT: 'IMB',
      ENVIRONMENTAL_PERMIT: 'Izin Lingkungan',
      OTHER: 'Lainnya',
    };
    return labels[type] || type;
  };

  if (loading) {
    return (
      <DashboardLayout>
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
      </DashboardLayout>
    );
  }

  if (error || !application) {
    return (
      <DashboardLayout>
        <div className="text-center py-12">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">Aplikasi Tidak Ditemukan</h2>
          <p className="text-gray-600 mb-6">{error || 'Aplikasi yang Anda cari tidak dapat ditemukan.'}</p>
          <Link
            href="/dashboard/applications"
            className="text-blue-600 hover:text-blue-500 font-medium"
          >
            Kembali ke Daftar Aplikasi
          </Link>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <div className="space-y-6">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center">
            <Link
              href="/dashboard/applications"
              className="text-gray-500 hover:text-gray-700 mr-4"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 className="text-2xl font-bold text-gray-900">
                Aplikasi {application.applicationNumber}
              </h1>
              <p className="text-gray-600 mt-1">Detail aplikasi izin dan status pemrosesan</p>
            </div>
          </div>
          <div className="flex items-center space-x-3">
            <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(application.status)}`}>
              {getStatusLabel(application.status)}
            </span>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-6">
            {/* Application Information */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-4">Informasi Aplikasi</h2>
              
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Nomor Aplikasi</label>
                    <p className="text-gray-900 font-mono">{application.applicationNumber}</p>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Jenis Izin</label>
                    <p className="text-gray-900">{application.licenseType?.name || 'N/A'}</p>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Nama Bisnis</label>
                    <p className="text-gray-900">{application.business?.name}</p>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Pemilik Bisnis</label>
                    <p className="text-gray-900">{application.business?.ownerName}</p>
                  </div>
                </div>
                
                <div className="space-y-4">
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Tanggal Pengajuan</label>
                    <p className="text-gray-900">{formatDate(application.createdAt)}</p>
                  </div>
                  {application.submittedAt && (
                    <div>
                      <label className="block text-sm font-medium text-gray-500 mb-1">Tanggal Pengiriman</label>
                      <p className="text-gray-900">{formatDate(application.submittedAt)}</p>
                    </div>
                  )}
                  {application.processedAt && (
                    <div>
                      <label className="block text-sm font-medium text-gray-500 mb-1">Tanggal Diproses</label>
                      <p className="text-gray-900">{formatDate(application.processedAt)}</p>
                    </div>
                  )}
                  <div>
                    <label className="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(application.status)}`}>
                      {getStatusLabel(application.status)}
                    </span>
                  </div>
                </div>
              </div>

              {application.notes && (
                <div className="mt-6 pt-6 border-t border-gray-200">
                  <label className="block text-sm font-medium text-gray-500 mb-2">Catatan</label>
                  <p className="text-gray-900 text-sm bg-gray-50 p-3 rounded-lg">{application.notes}</p>
                </div>
              )}

              {application.rejectionReason && (
                <div className="mt-6 pt-6 border-t border-gray-200">
                  <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 className="font-medium text-red-800 mb-2">Alasan Penolakan</h4>
                    <p className="text-sm text-red-700">{application.rejectionReason}</p>
                  </div>
                </div>
              )}
            </div>

            {/* Documents */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-4">Dokumen Pendukung</h2>
              
              {documents.length === 0 ? (
                <div className="text-center py-8">
                  <div className="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">Belum Ada Dokumen</h3>
                  <p className="text-gray-600 mb-4">Upload dokumen pendukung untuk aplikasi ini</p>
                </div>
              ) : (
                <div className="space-y-4">
                  {documents.map((document) => (
                    <div key={document.id} className="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                      <div className="flex items-center space-x-3">
                        <div className="flex-shrink-0">
                          <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                          </svg>
                        </div>
                        <div>
                          <p className="text-sm font-medium text-gray-900">{document.originalName}</p>
                          <p className="text-xs text-gray-500">
                            {getDocumentTypeLabel(document.documentType)} • {(document.size / 1024 / 1024).toFixed(2)} MB
                          </p>
                        </div>
                      </div>
                      <button className="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Download
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>

            {/* Document Upload */}
            {['DRAFT', 'SUBMITTED', 'UNDER_REVIEW'].includes(application.status) && (
              <DocumentUpload
                applicationId={application.id}
                onUploadComplete={handleDocumentUpload}
              />
            )}
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            {/* Status Timeline */}
            <StatusTimeline
              currentStatus={application.status}
              submittedAt={application.submittedAt}
              processedAt={application.processedAt}
              rejectionReason={application.rejectionReason}
            />

            {/* Actions */}
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>
              <div className="space-y-3">
                {application.status === 'DRAFT' && (
                  <button className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Kirim Aplikasi
                  </button>
                )}
                
                <Link
                  href={`/dashboard/businesses/${application.business.id}`}
                  className="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-center block"
                >
                  Lihat Detail Bisnis
                </Link>
                
                <button className="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                  Download PDF
                </button>
              </div>
            </div>

            {/* Help & Support */}
            <div className="bg-blue-50 rounded-lg p-6">
              <h3 className="text-lg font-semibold text-blue-900 mb-2">Butuh Bantuan?</h3>
              <p className="text-sm text-blue-700 mb-4">
                Tim support kami siap membantu Anda dengan aplikasi perizinan.
              </p>
              <div className="space-y-2 text-sm text-blue-600">
                <p>📧 support@bizmark.id</p>
                <p>📞 0800-1234-5678</p>
                <p>💬 Live Chat (8:00 - 17:00)</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
