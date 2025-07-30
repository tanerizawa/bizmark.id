'use client';

import { useEffect, useState } from 'react';
import { licenseService } from '@/services/license.service';
import { License, LicenseStatus } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';

export default function LicensesPage() {
  const [licenses, setLicenses] = useState<License[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchLicenses = async () => {
      try {
        const response = await licenseService.getLicenses();
        setLicenses(response.data);
      } catch (err) {
        console.error('Failed to fetch licenses:', err);
        setError('Gagal memuat data izin');
      } finally {
        setLoading(false);
      }
    };

    fetchLicenses();
  }, []);

  const getStatusLabel = (status: LicenseStatus) => {
    const labels = {
      [LicenseStatus.DRAFT]: 'Draft',
      [LicenseStatus.SUBMITTED]: 'Diajukan',
      [LicenseStatus.UNDER_REVIEW]: 'Sedang Ditinjau',
      [LicenseStatus.APPROVED]: 'Disetujui',
      [LicenseStatus.REJECTED]: 'Ditolak',
      [LicenseStatus.EXPIRED]: 'Kedaluwarsa',
      [LicenseStatus.CANCELLED]: 'Dibatalkan',
    };
    return labels[status];
  };

  const getStatusColor = (status: LicenseStatus) => {
    const colors = {
      [LicenseStatus.DRAFT]: 'bg-gray-100 text-gray-800',
      [LicenseStatus.SUBMITTED]: 'bg-blue-100 text-blue-800',
      [LicenseStatus.UNDER_REVIEW]: 'bg-yellow-100 text-yellow-800',
      [LicenseStatus.APPROVED]: 'bg-green-100 text-green-800',
      [LicenseStatus.REJECTED]: 'bg-red-100 text-red-800',
      [LicenseStatus.EXPIRED]: 'bg-red-100 text-red-800',
      [LicenseStatus.CANCELLED]: 'bg-gray-100 text-gray-800',
    };
    return colors[status];
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  };

  const isExpiringSoon = (expiryDate: string) => {
    const expiry = new Date(expiryDate);
    const thirtyDaysFromNow = new Date();
    thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
    return expiry <= thirtyDaysFromNow;
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

  return (
    <DashboardLayout>
      <div className="space-y-6">
        {/* Header */}
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Izin Aktif</h1>
          <p className="text-gray-600 mt-1">Kelola dan monitor izin usaha yang telah disetujui</p>
        </div>

        {error && (
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {error}
          </div>
        )}

        {/* Licenses Grid */}
        {licenses.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-12 text-center">
            <svg className="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 className="text-lg font-medium text-gray-900 mb-2">Belum Ada Izin Aktif</h3>
            <p className="text-gray-600 mb-6">
              Izin yang telah disetujui akan muncul di sini.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {licenses.map((license) => (
              <div key={license.id} className="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div className="p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex-1">
                      <h3 className="text-lg font-semibold text-gray-900 mb-1">
                        {license.licenseType.name}
                      </h3>
                      <p className="text-sm text-gray-600 mb-2">{license.business.name}</p>
                      <p className="text-xs text-gray-500">No: {license.licenseNumber}</p>
                    </div>
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(license.status)}`}>
                      {getStatusLabel(license.status)}
                    </span>
                  </div>

                  <div className="space-y-2 text-sm text-gray-600 mb-4">
                    <div className="flex justify-between">
                      <span>Tanggal Disetujui:</span>
                      <span>{license.approvalDate ? formatDate(license.approvalDate) : '-'}</span>
                    </div>
                    <div className="flex justify-between">
                      <span>Tanggal Berakhir:</span>
                      <span className={isExpiringSoon(license.expiryDate) ? 'text-red-600 font-medium' : ''}>
                        {formatDate(license.expiryDate)}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span>Penerbit:</span>
                      <span>{license.issuingAuthority}</span>
                    </div>
                  </div>

                  {isExpiringSoon(license.expiryDate) && license.status === LicenseStatus.APPROVED && (
                    <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                      <div className="flex items-center">
                        <svg className="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span className="text-sm text-yellow-800 font-medium">
                          Izin akan segera berakhir!
                        </span>
                      </div>
                    </div>
                  )}

                  <div className="flex justify-between items-center pt-4 border-t border-gray-200">
                    <button className="text-blue-600 hover:text-blue-800 text-sm font-medium">
                      Download Sertifikat
                    </button>
                    {license.status === LicenseStatus.APPROVED && isExpiringSoon(license.expiryDate) && (
                      <button className="text-green-600 hover:text-green-800 text-sm font-medium">
                        Perpanjang
                      </button>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
