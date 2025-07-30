'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { businessService } from '@/services/business.service';
import { applicationService } from '@/services/application.service';
import { Business, LicenseType, ApplicationFormData } from '@/types';
import DashboardLayout from '@/components/DashboardLayout';

export default function CreateApplicationPage() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [businessesLoading, setBusinessesLoading] = useState(true);
  const [error, setError] = useState('');
  const [businesses, setBusinesses] = useState<Business[]>([]);
  const [selectedBusiness, setSelectedBusiness] = useState<Business | null>(null);
  const [formData, setFormData] = useState<ApplicationFormData>({
    businessId: '',
    licenseType: LicenseType.BUSINESS_PERMIT,
    notes: '',
  });

  useEffect(() => {
    const fetchBusinesses = async () => {
      try {
        const response = await businessService.getBusinesses();
        setBusinesses(response.data);
      } catch (error) {
        console.error('Failed to fetch businesses:', error);
        setError('Gagal memuat daftar bisnis');
      } finally {
        setBusinessesLoading(false);
      }
    };

    fetchBusinesses();
  }, []);

  const handleBusinessChange = (businessId: string) => {
    const business = businesses.find(b => b.id === businessId);
    setSelectedBusiness(business || null);
    setFormData(prev => ({
      ...prev,
      businessId,
    }));
  };

  const handleChange = (e: React.ChangeEvent<HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      await applicationService.createApplication(formData);
      router.push('/dashboard/applications');
    } catch (err) {
      console.error('Failed to create application:', err);
      setError('Gagal membuat aplikasi. Silakan coba lagi.');
    } finally {
      setLoading(false);
    }
  };

  const getLicenseTypeLabel = (type: LicenseType): string => {
    const labels = {
      [LicenseType.BUSINESS_PERMIT]: 'Izin Usaha (SIUP)',
      [LicenseType.TRADE_PERMIT]: 'Izin Perdagangan (SIUP)',
      [LicenseType.LOCATION_PERMIT]: 'Izin Lokasi (SITU)',
      [LicenseType.ENVIRONMENT_PERMIT]: 'Izin Lingkungan (AMDAL)',
      [LicenseType.BUILDING_PERMIT]: 'Izin Mendirikan Bangunan (IMB)',
      [LicenseType.OPERATIONAL_PERMIT]: 'Izin Operasional',
    };
    return labels[type] || type;
  };

  const getLicenseTypeDescription = (type: LicenseType): string => {
    const descriptions = {
      [LicenseType.BUSINESS_PERMIT]: 'Izin dasar untuk menjalankan kegiatan usaha',
      [LicenseType.TRADE_PERMIT]: 'Izin untuk kegiatan perdagangan barang dan jasa',
      [LicenseType.LOCATION_PERMIT]: 'Izin penggunaan tempat untuk kegiatan usaha',
      [LicenseType.ENVIRONMENT_PERMIT]: 'Izin dampak lingkungan untuk usaha tertentu',
      [LicenseType.BUILDING_PERMIT]: 'Izin untuk mendirikan atau merenovasi bangunan',
      [LicenseType.OPERATIONAL_PERMIT]: 'Izin khusus sesuai bidang usaha',
    };
    return descriptions[type] || '';
  };

  if (businessesLoading) {
    return (
      <DashboardLayout>
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        </div>
      </DashboardLayout>
    );
  }

  if (businesses.length === 0) {
    return (
      <DashboardLayout>
        <div className="text-center py-12">
          <div className="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg className="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <h2 className="text-2xl font-bold text-gray-900 mb-4">Belum Ada Bisnis Terdaftar</h2>
          <p className="text-gray-600 mb-6 max-w-md mx-auto">
            Anda perlu mendaftarkan bisnis terlebih dahulu sebelum dapat mengajukan aplikasi izin.
          </p>
          <Link
            href="/dashboard/businesses/create"
            className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center"
          >
            <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            Daftarkan Bisnis Pertama
          </Link>
        </div>
      </DashboardLayout>
    );
  }

  return (
    <DashboardLayout>
      <div className="max-w-3xl mx-auto">
        {/* Header */}
        <div className="mb-8">
          <div className="flex items-center mb-4">
            <Link
              href="/dashboard/applications"
              className="text-gray-500 hover:text-gray-700 mr-4"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 className="text-2xl font-bold text-gray-900">Ajukan Izin Baru</h1>
              <p className="text-gray-600 mt-1">Pilih bisnis dan jenis izin yang ingin diajukan</p>
            </div>
          </div>
        </div>

        {/* Form */}
        <div className="bg-white rounded-lg shadow">
          <form onSubmit={handleSubmit} className="p-6 space-y-6">
            {error && (
              <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {error}
              </div>
            )}

            {/* Business Selection */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">Pilih Bisnis</h3>
              <div className="space-y-4">
                <div>
                  <label htmlFor="businessId" className="block text-sm font-medium text-gray-700 mb-2">
                    Bisnis yang Akan Diajukan Izin *
                  </label>
                  <select
                    id="businessId"
                    name="businessId"
                    value={formData.businessId}
                    onChange={(e) => handleBusinessChange(e.target.value)}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                  >
                    <option value="">Pilih bisnis...</option>
                    {businesses.map((business) => (
                      <option key={business.id} value={business.id}>
                        {business.name} - {business.ownerName}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Business Info Display */}
                {selectedBusiness && (
                  <div className="bg-blue-50 p-4 rounded-lg">
                    <h4 className="font-medium text-blue-900 mb-2">Informasi Bisnis</h4>
                    <div className="text-sm text-blue-800 space-y-1">
                      <p><span className="font-medium">Nama:</span> {selectedBusiness.name}</p>
                      <p><span className="font-medium">Pemilik:</span> {selectedBusiness.ownerName}</p>
                      <p><span className="font-medium">Alamat:</span> {selectedBusiness.address}</p>
                      <p><span className="font-medium">Telepon:</span> {selectedBusiness.phone}</p>
                    </div>
                  </div>
                )}
              </div>
            </div>

            {/* License Type Selection */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">Jenis Izin</h3>
              <div>
                <label htmlFor="licenseType" className="block text-sm font-medium text-gray-700 mb-2">
                  Pilih Jenis Izin yang Dibutuhkan *
                </label>
                <select
                  id="licenseType"
                  name="licenseType"
                  value={formData.licenseType}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  required
                >
                  {Object.values(LicenseType).map((type) => (
                    <option key={type} value={type}>
                      {getLicenseTypeLabel(type)}
                    </option>
                  ))}
                </select>
                
                {/* License Type Description */}
                <div className="mt-3 p-3 bg-gray-50 rounded-lg">
                  <p className="text-sm text-gray-600">
                    <span className="font-medium">Deskripsi:</span> {getLicenseTypeDescription(formData.licenseType as LicenseType)}
                  </p>
                </div>
              </div>
            </div>

            {/* Additional Notes */}
            <div>
              <h3 className="text-lg font-medium text-gray-900 mb-4">Catatan Tambahan</h3>
              <div>
                <label htmlFor="notes" className="block text-sm font-medium text-gray-700 mb-2">
                  Catatan atau Keterangan Khusus
                </label>
                <textarea
                  id="notes"
                  name="notes"
                  value={formData.notes}
                  onChange={handleChange}
                  rows={4}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Tambahkan catatan khusus untuk aplikasi ini (opsional)"
                />
              </div>
            </div>

            {/* Process Information */}
            <div className="bg-yellow-50 p-4 rounded-lg">
              <h4 className="font-medium text-yellow-900 mb-2">Informasi Proses</h4>
              <div className="text-sm text-yellow-800 space-y-1">
                <p>• Aplikasi akan diproses dalam 1-3 hari kerja</p>
                <p>• Anda akan menerima notifikasi untuk melengkapi dokumen yang diperlukan</p>
                <p>• Status aplikasi dapat dipantau melalui dashboard</p>
                <p>• Tim kami akan menghubungi jika ada informasi tambahan yang dibutuhkan</p>
              </div>
            </div>

            {/* Submit Buttons */}
            <div className="flex justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                href="/dashboard/applications"
                className="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Batal
              </Link>
              <button
                type="submit"
                disabled={loading}
                className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                {loading ? 'Mengirim Aplikasi...' : 'Ajukan Izin'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </DashboardLayout>
  );
}
