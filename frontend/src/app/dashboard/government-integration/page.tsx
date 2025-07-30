'use client';

import { useState } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { TouchButton, TouchCard, TouchInput } from '@/components/TouchComponents';
import { useNIBVerification, useBusinessLicense, useBusinessCategories } from '@/hooks/useGovernmentApi';

export default function GovernmentIntegrationPage() {
  const [activeTab, setActiveTab] = useState<'nib' | 'license' | 'categories' | 'validation'>('nib');
  const [nibInput, setNibInput] = useState('');
  const [licenseInput, setLicenseInput] = useState('');

  const nibVerification = useNIBVerification();
  const businessLicense = useBusinessLicense();
  const businessCategories = useBusinessCategories();

  const handleNIBVerification = () => {
    if (nibInput.trim()) {
      nibVerification.verifyNIB(nibInput.trim());
    }
  };

  const handleLicenseCheck = () => {
    if (licenseInput.trim()) {
      businessLicense.getLicense(licenseInput.trim());
    }
  };

  const tabs = [
    { id: 'nib' as const, name: 'Verifikasi NIB', icon: '🏢' },
    { id: 'license' as const, name: 'Cek Izin', icon: '📋' },
    { id: 'categories' as const, name: 'Kategori Usaha', icon: '📂' },
    { id: 'validation' as const, name: 'Validasi Data', icon: '✅' },
  ];

  return (
    <DashboardLayout>
      <div className="space-y-6">
        <div className="border-b border-gray-200 dark:border-gray-700">
          <div className="sm:flex sm:items-center">
            <div className="sm:flex-auto">
              <h1 className="text-2xl font-bold leading-6 text-gray-900 dark:text-white">
                Integrasi Pemerintah
              </h1>
              <p className="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Integrasi dengan sistem pemerintah Indonesia (OSS, NIB, dan lainnya)
              </p>
            </div>
          </div>
          
          <div className="mt-4">
            <nav className="-mb-px flex space-x-8 overflow-x-auto">
              {tabs.map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`
                    whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors
                    ${activeTab === tab.id
                      ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
                    }
                  `}
                >
                  <span className="mr-2">{tab.icon}</span>
                  {tab.name}
                </button>
              ))}
            </nav>
          </div>
        </div>

        {/* NIB Verification Tab */}
        {activeTab === 'nib' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Verifikasi Nomor Induk Berusaha (NIB)
              </h3>
              
              <div className="space-y-4">
                <TouchInput
                  label="Nomor Induk Berusaha (NIB)"
                  placeholder="Masukkan NIB (minimal 10 karakter)"
                  value={nibInput}
                  onChange={(e) => setNibInput(e.target.value)}
                  error={nibVerification.error || undefined}
                />
                
                <TouchButton
                  onClick={handleNIBVerification}
                  loading={nibVerification.loading}
                  disabled={!nibInput.trim() || nibInput.length < 10}
                >
                  Verifikasi NIB
                </TouchButton>
              </div>

              {nibVerification.success && nibVerification.data && (
                <div className="mt-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                  <h4 className="font-medium text-green-800 dark:text-green-200 mb-3">
                    ✅ NIB Valid - Data Bisnis Ditemukan
                  </h4>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">NIB:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{nibVerification.data.nib}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Nama Usaha:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{nibVerification.data.businessName}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Jenis Usaha:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{nibVerification.data.businessType}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                      <span className={`ml-2 px-2 py-1 rounded text-xs font-medium ${
                        nibVerification.data.status === 'active' 
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                          : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                      }`}>
                        {nibVerification.data.status}
                      </span>
                    </div>
                    <div className="md:col-span-2">
                      <span className="font-medium text-gray-700 dark:text-gray-300">Alamat:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{nibVerification.data.address}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Pemilik:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{nibVerification.data.owner}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Berlaku Hingga:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">
                        {new Date(nibVerification.data.validUntil).toLocaleDateString('id-ID')}
                      </span>
                    </div>
                  </div>
                  
                  {nibVerification.data.activities.length > 0 && (
                    <div className="mt-4">
                      <span className="font-medium text-gray-700 dark:text-gray-300">Kegiatan Usaha:</span>
                      <ul className="mt-2 space-y-1">
                        {nibVerification.data.activities.map((activity, index) => (
                          <li key={index} className="text-sm text-gray-600 dark:text-gray-400">
                            • {activity}
                          </li>
                        ))}
                      </ul>
                    </div>
                  )}
                </div>
              )}
            </TouchCard>
          </div>
        )}

        {/* License Check Tab */}
        {activeTab === 'license' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Cek Status Izin Usaha
              </h3>
              
              <div className="space-y-4">
                <TouchInput
                  label="Nomor Izin Usaha"
                  placeholder="Masukkan nomor izin usaha"
                  value={licenseInput}
                  onChange={(e) => setLicenseInput(e.target.value)}
                  error={businessLicense.error || undefined}
                />
                
                <TouchButton
                  onClick={handleLicenseCheck}
                  loading={businessLicense.loading}
                  disabled={!licenseInput.trim()}
                >
                  Cek Status Izin
                </TouchButton>
              </div>

              {businessLicense.success && businessLicense.data && (
                <div className="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                  <h4 className="font-medium text-blue-800 dark:text-blue-200 mb-3">
                    📋 Informasi Izin Usaha
                  </h4>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Nomor Izin:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{businessLicense.data.licenseNumber}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Jenis Izin:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{businessLicense.data.licenseType}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Nama Usaha:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{businessLicense.data.businessName}</span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Status:</span>
                      <span className={`ml-2 px-2 py-1 rounded text-xs font-medium ${
                        businessLicense.data.status === 'active' 
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                          : businessLicense.data.status === 'expired'
                          ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
                          : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100'
                      }`}>
                        {businessLicense.data.status}
                      </span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Tanggal Terbit:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">
                        {new Date(businessLicense.data.issuedDate).toLocaleDateString('id-ID')}
                      </span>
                    </div>
                    <div>
                      <span className="font-medium text-gray-700 dark:text-gray-300">Tanggal Berakhir:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">
                        {new Date(businessLicense.data.expiryDate).toLocaleDateString('id-ID')}
                      </span>
                    </div>
                    <div className="md:col-span-2">
                      <span className="font-medium text-gray-700 dark:text-gray-300">Instansi Penerbit:</span>
                      <span className="ml-2 text-gray-900 dark:text-white">{businessLicense.data.authority}</span>
                    </div>
                  </div>
                </div>
              )}
            </TouchCard>
          </div>
        )}

        {/* Business Categories Tab */}
        {activeTab === 'categories' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Kategori dan Persyaratan Usaha
              </h3>
              
              {businessCategories.loading && (
                <div className="flex items-center justify-center py-8">
                  <div className="text-center">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">Memuat kategori usaha...</p>
                  </div>
                </div>
              )}

              {businessCategories.error && (
                <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                  <p className="text-red-800 dark:text-red-200">{businessCategories.error}</p>
                </div>
              )}

              {businessCategories.success && businessCategories.data && (
                <div className="space-y-4">
                  {businessCategories.data.categories.map((category) => (
                    <div key={category.id} className="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                      <h4 className="font-medium text-gray-900 dark:text-white mb-2">
                        {category.name}
                      </h4>
                      <p className="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        {category.description}
                      </p>
                      
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <h5 className="font-medium text-gray-700 dark:text-gray-300 mb-2">Persyaratan:</h5>
                          <ul className="space-y-1">
                            {category.requirements.map((req, index) => (
                              <li key={index} className="text-sm text-gray-600 dark:text-gray-400">
                                • {req}
                              </li>
                            ))}
                          </ul>
                        </div>
                        
                        <div>
                          <h5 className="font-medium text-gray-700 dark:text-gray-300 mb-2">Dokumen yang Diperlukan:</h5>
                          <ul className="space-y-1">
                            {category.documents.map((doc, index) => (
                              <li key={index} className="text-sm text-gray-600 dark:text-gray-400">
                                • {doc}
                              </li>
                            ))}
                          </ul>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </TouchCard>
          </div>
        )}

        {/* Data Validation Tab */}
        {activeTab === 'validation' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Validasi Data Usaha
              </h3>
              
              <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div className="flex">
                  <div className="flex-shrink-0">
                    <svg className="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                      <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                    </svg>
                  </div>
                  <div className="ml-3">
                    <h3 className="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                      Fitur dalam Pengembangan
                    </h3>
                    <div className="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                      <p>
                        Fitur validasi data usaha sedang dalam pengembangan dan akan segera tersedia.
                        Fitur ini akan memvalidasi data bisnis terhadap database pemerintah.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </TouchCard>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
