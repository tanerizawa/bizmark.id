'use client';

import { useEffect, useState } from 'react';
import { dashboardService } from '@/services/dashboard.service';
import { DashboardStats } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';
import Chart from '@/components/Chart';

export default function AnalyticsPage() {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await dashboardService.getStats();
        setStats(response.data);
      } catch (error) {
        console.error('Failed to fetch dashboard stats:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchStats();
  }, []);

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
          <h1 className="text-2xl font-bold text-gray-900">Analytics & Insights</h1>
          <p className="text-gray-600 mt-1">Analisis mendalam tentang performa bisnis dan aplikasi izin Anda</p>
        </div>

        {/* Key Metrics */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <div className="p-3 rounded-full bg-blue-100">
                <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Total Bisnis</p>
                <p className="text-2xl font-semibold text-gray-900">{stats?.totalBusinesses || 0}</p>
                <p className="text-xs text-green-600">+12% dari bulan lalu</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <div className="p-3 rounded-full bg-green-100">
                <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Izin Disetujui</p>
                <p className="text-2xl font-semibold text-gray-900">{stats?.activeLicenses || 0}</p>
                <p className="text-xs text-green-600">+8% dari bulan lalu</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <div className="p-3 rounded-full bg-yellow-100">
                <svg className="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Sedang Diproses</p>
                <p className="text-2xl font-semibold text-gray-900">{stats?.pendingApplications || 0}</p>
                <p className="text-xs text-yellow-600">-3% dari bulan lalu</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <div className="p-3 rounded-full bg-red-100">
                <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Akan Expired</p>
                <p className="text-2xl font-semibold text-gray-900">{stats?.expiringLicenses || 0}</p>
                <p className="text-xs text-red-600">+2 dalam 30 hari</p>
              </div>
            </div>
          </div>
        </div>

        {/* Charts Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Chart
            type="license"
            title="Distribusi Jenis Izin"
            className="col-span-1"
          />
          
          <Chart
            type="application"
            title="Status Aplikasi"
            className="col-span-1"
          />
        </div>

        {/* Trend Analysis */}
        <div className="grid grid-cols-1 gap-6">
          <Chart
            type="trend"
            title="Tren Aplikasi 6 Bulan Terakhir"
            className="col-span-1"
          />
        </div>

        {/* Performance Insights */}
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Insights & Rekomendasi</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div className="bg-blue-50 p-4 rounded-lg">
              <div className="flex items-center mb-2">
                <div className="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                <h4 className="font-medium text-blue-900">Waktu Pemrosesan</h4>
              </div>
              <p className="text-sm text-blue-800">
                Rata-rata waktu pemrosesan izin adalah 7-14 hari kerja. 
                Lengkapi dokumen dengan baik untuk mempercepat proses.
              </p>
            </div>

            <div className="bg-green-50 p-4 rounded-lg">
              <div className="flex items-center mb-2">
                <div className="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                <h4 className="font-medium text-green-900">Tingkat Keberhasilan</h4>
              </div>
              <p className="text-sm text-green-800">
                85% aplikasi disetujui pada pengajuan pertama. 
                Pastikan data bisnis Anda lengkap dan akurat.
              </p>
            </div>

            <div className="bg-yellow-50 p-4 rounded-lg">
              <div className="flex items-center mb-2">
                <div className="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                <h4 className="font-medium text-yellow-900">Reminder Expired</h4>
              </div>
              <p className="text-sm text-yellow-800">
                {stats?.expiringLicenses || 0} izin akan expired dalam 30 hari ke depan. 
                Segera ajukan perpanjangan untuk menghindari gangguan bisnis.
              </p>
            </div>
          </div>
        </div>

        {/* Action Items */}
        <div className="bg-white rounded-lg shadow p-6">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Action Items</h3>
          <div className="space-y-3">
            {stats?.expiringLicenses && stats.expiringLicenses > 0 && (
              <div className="flex items-center p-3 bg-red-50 rounded-lg">
                <div className="flex-shrink-0">
                  <svg className="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div className="ml-3 flex-1">
                  <p className="text-sm font-medium text-red-800">
                    Perpanjang {stats.expiringLicenses} izin yang akan expired
                  </p>
                  <p className="text-xs text-red-600">
                    Hindari gangguan operasional bisnis dengan memperpanjang sebelum expired
                  </p>
                </div>
                <button className="ml-4 text-sm bg-red-100 text-red-800 px-3 py-1 rounded hover:bg-red-200 transition-colors">
                  Lihat Detail
                </button>
              </div>
            )}

            {stats?.pendingApplications && stats.pendingApplications > 0 && (
              <div className="flex items-center p-3 bg-yellow-50 rounded-lg">
                <div className="flex-shrink-0">
                  <svg className="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div className="ml-3 flex-1">
                  <p className="text-sm font-medium text-yellow-800">
                    Pantau {stats.pendingApplications} aplikasi yang sedang diproses
                  </p>
                  <p className="text-xs text-yellow-600">
                    Periksa apakah ada dokumen tambahan yang diminta
                  </p>
                </div>
                <button className="ml-4 text-sm bg-yellow-100 text-yellow-800 px-3 py-1 rounded hover:bg-yellow-200 transition-colors">
                  Cek Status
                </button>
              </div>
            )}

            <div className="flex items-center p-3 bg-blue-50 rounded-lg">
              <div className="flex-shrink-0">
                <svg className="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div className="ml-3 flex-1">
                <p className="text-sm font-medium text-blue-800">
                  Pertimbangkan untuk mengajukan izin operasional tambahan
                </p>
                <p className="text-xs text-blue-600">
                  Untuk ekspansi bisnis dan compliance yang lebih baik
                </p>
              </div>
              <button className="ml-4 text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded hover:bg-blue-200 transition-colors">
                Pelajari
              </button>
            </div>
          </div>
        </div>
      </div>
    </DashboardLayout>
  );
}
