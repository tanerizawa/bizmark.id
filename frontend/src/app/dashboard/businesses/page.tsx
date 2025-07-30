'use client';

import { useEffect, useState } from 'react';
import Link from 'next/link';
import { businessService } from '@/services/business.service';
import { Business, BusinessType } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';

export default function BusinessesPage() {
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchBusinesses = async () => {
      try {
        const response = await businessService.getBusinesses();
        setBusinesses(response.data);
      } catch (err) {
        console.error('Failed to fetch businesses:', err);
        setError('Gagal memuat data bisnis');
      } finally {
        setLoading(false);
      }
    };

    fetchBusinesses();
  }, []);

  const getBusinessTypeLabel = (type: BusinessType) => {
    const labels = {
      [BusinessType.MICRO]: 'Mikro',
      [BusinessType.SMALL]: 'Kecil',
      [BusinessType.MEDIUM]: 'Menengah',
    };
    return labels[type];
  };

  const getBusinessTypeColor = (type: BusinessType) => {
    const colors = {
      [BusinessType.MICRO]: 'bg-blue-100 text-blue-800',
      [BusinessType.SMALL]: 'bg-green-100 text-green-800',
      [BusinessType.MEDIUM]: 'bg-purple-100 text-purple-800',
    };
    return colors[type];
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
        <div className="flex justify-between items-center">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">Manajemen Bisnis</h1>
            <p className="text-gray-600 mt-1">Kelola informasi bisnis dan profil perusahaan Anda</p>
          </div>
          <Link
            href="/dashboard/businesses/create"
            className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
          >
            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Bisnis
          </Link>
        </div>

        {error && (
          <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {error}
          </div>
        )}

        {/* Businesses Grid */}
        {businesses.length === 0 ? (
          <div className="bg-white rounded-lg shadow p-12 text-center">
            <svg className="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 className="text-lg font-medium text-gray-900 mb-2">Belum Ada Bisnis</h3>
            <p className="text-gray-600 mb-6">
              Mulai dengan mendaftarkan bisnis pertama Anda untuk mengajukan izin usaha.
            </p>
            <Link
              href="/dashboard/businesses/create"
              className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
            >
              <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Daftarkan Bisnis Pertama
            </Link>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {businesses.map((business) => (
              <div key={business.id} className="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div className="p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex-1">
                      <h3 className="text-lg font-semibold text-gray-900 mb-1">{business.name}</h3>
                      <p className="text-sm text-gray-600 line-clamp-2">{business.description || 'Tidak ada deskripsi'}</p>
                    </div>
                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getBusinessTypeColor(business.businessType)}`}>
                      {getBusinessTypeLabel(business.businessType)}
                    </span>
                  </div>

                  <div className="space-y-2 text-sm text-gray-600">
                    <div className="flex items-center">
                      <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span className="line-clamp-1">{business.address}</span>
                    </div>
                    <div className="flex items-center">
                      <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                      <span>{business.phone}</span>
                    </div>
                    <div className="flex items-center">
                      <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                      <span>{business.ownerName}</span>
                    </div>
                  </div>

                  <div className="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                    <div className="flex items-center">
                      <div className={`w-2 h-2 rounded-full mr-2 ${business.isActive ? 'bg-green-500' : 'bg-red-500'}`}></div>
                      <span className={`text-xs font-medium ${business.isActive ? 'text-green-600' : 'text-red-600'}`}>
                        {business.isActive ? 'Aktif' : 'Tidak Aktif'}
                      </span>
                    </div>
                    <div className="flex space-x-2">
                      <Link
                        href={`/dashboard/businesses/${business.id}`}
                        className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                      >
                        Detail
                      </Link>
                      <Link
                        href={`/dashboard/businesses/${business.id}/edit`}
                        className="text-gray-600 hover:text-gray-800 text-sm font-medium"
                      >
                        Edit
                      </Link>
                    </div>
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
