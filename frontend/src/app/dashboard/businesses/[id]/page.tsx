'use client';

import { useEffect, useState } from 'react';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import { businessService } from '@/services/business.service';
import { applicationService } from '@/services/application.service';
import { Business, LicenseApplication, BusinessType } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';

export default function BusinessDetailPage() {
  const params = useParams();
  const businessId = params.id as string;
  
  const [loading, setLoading] = useState(true);
  const [business, setBusiness] = useState<Business | null>(null);
  const [applications, setApplications] = useState<LicenseApplication[]>([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchData = async () => {
      try {
        const [businessResponse, applicationsResponse] = await Promise.all([
          businessService.getBusinessById(businessId),
          applicationService.getApplicationsByBusiness(businessId)
        ]);
        
        setBusiness(businessResponse.data);
        setApplications(applicationsResponse.data);
      } catch (error) {
        console.error('Failed to fetch business data:', error);
        setError('Gagal memuat data bisnis');
      } finally {
        setLoading(false);
      }
    };

    if (businessId) {
      fetchData();
    }
  }, [businessId]);

  const getBusinessTypeLabel = (type: BusinessType): string => {
    const labels = {
      [BusinessType.MICRO]: 'Usaha Mikro',
      [BusinessType.SMALL]: 'Usaha Kecil',
      [BusinessType.MEDIUM]: 'Usaha Menengah',
    };
    return labels[type] || type;
  };

  const getBusinessTypeColor = (type: BusinessType): string => {
    const colors = {
      [BusinessType.MICRO]: 'bg-green-100 text-green-800',
      [BusinessType.SMALL]: 'bg-blue-100 text-blue-800',
      [BusinessType.MEDIUM]: 'bg-purple-100 text-purple-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
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

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
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

  if (error || !business) {
    return (
      <DashboardLayout>
        <div className="text-center py-12">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">Bisnis Tidak Ditemukan</h2>
          <p className="text-gray-600 mb-6">{error || 'Bisnis yang Anda cari tidak dapat ditemukan.'}</p>
          <Link
            href="/dashboard/businesses"
            className="text-blue-600 hover:text-blue-500 font-medium"
          >
            Kembali ke Daftar Bisnis
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
              href="/dashboard/businesses"
              className="text-gray-500 hover:text-gray-700 mr-4"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 className="text-2xl font-bold text-gray-900">{business.name}</h1>
              <p className="text-gray-600 mt-1">Detail informasi bisnis dan aplikasi perizinan</p>
            </div>
          </div>
          <div className="flex space-x-3">
            <Link
              href={`/dashboard/businesses/${business.id}/edit`}
              className="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Edit Bisnis
            </Link>
            <Link
              href="/dashboard/applications/create"
              className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
            >
              Ajukan Izin Baru
            </Link>
          </div>
        </div>

        {/* Business Information */}
        <div className="bg-white rounded-lg shadow">
          <div className="p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-semibold text-gray-900">Informasi Bisnis</h2>
              <div className="flex items-center space-x-3">
                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getBusinessTypeColor(business.businessType)}`}>
                  {getBusinessTypeLabel(business.businessType)}
                </span>
                <div className="flex items-center">
                  <div className={`w-2 h-2 rounded-full mr-2 ${business.isActive ? 'bg-green-500' : 'bg-red-500'}`}></div>
                  <span className={`text-sm font-medium ${business.isActive ? 'text-green-600' : 'text-red-600'}`}>
                    {business.isActive ? 'Aktif' : 'Tidak Aktif'}
                  </span>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Nama Bisnis</label>
                  <p className="text-gray-900">{business.name}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                  <p className="text-gray-900">{business.description || 'Tidak ada deskripsi'}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Alamat</label>
                  <p className="text-gray-900">{business.address}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Nomor Telepon</label>
                  <p className="text-gray-900">{business.phone}</p>
                </div>
              </div>
              
              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Email Bisnis</label>
                  <p className="text-gray-900">{business.email || 'Tidak ada email'}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Nama Pemilik</label>
                  <p className="text-gray-900">{business.ownerName}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Nomor KTP</label>
                  <p className="text-gray-900">{business.ownerIdNumber}</p>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-500 mb-1">Tanggal Daftar</label>
                  <p className="text-gray-900">{formatDate(business.createdAt)}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Applications */}
        <div className="bg-white rounded-lg shadow">
          <div className="p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-semibold text-gray-900">Aplikasi Perizinan</h2>
              <Link
                href="/dashboard/applications/create"
                className="text-blue-600 hover:text-blue-500 text-sm font-medium"
              >
                + Ajukan Izin Baru
              </Link>
            </div>

            {applications.length === 0 ? (
              <div className="text-center py-8">
                <div className="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                  <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">Belum Ada Aplikasi Izin</h3>
                <p className="text-gray-600 mb-4">Mulai ajukan izin untuk bisnis ini</p>
                <Link
                  href="/dashboard/applications/create"
                  className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
                >
                  <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                  </svg>
                  Ajukan Izin Pertama
                </Link>
              </div>
            ) : (
              <div className="space-y-4">
                {applications.map((application) => (
                  <div key={application.id} className="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                    <div className="flex items-center justify-between">
                      <div className="flex-1">
                        <div className="flex items-center space-x-3 mb-2">
                          <h3 className="font-medium text-gray-900">{application.applicationNumber}</h3>
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(application.status)}`}>
                            {getStatusLabel(application.status)}
                          </span>
                        </div>
                        <p className="text-sm text-gray-600 mb-1">
                          Jenis Izin: {application.licenseType?.name || 'N/A'}
                        </p>
                        <p className="text-xs text-gray-500">
                          Diajukan: {formatDate(application.createdAt)}
                        </p>
                      </div>
                      <div className="flex space-x-2">
                        <Link
                          href={`/dashboard/applications/${application.id}`}
                          className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                          Detail
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
