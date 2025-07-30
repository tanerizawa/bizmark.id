'use client';

import { useState, useEffect } from 'react';
import DashboardLayout from '@/components/DashboardLayout';
import { TouchButton, TouchCard, TouchInput } from '@/components/TouchComponents';

interface PaymentMethod {
  id: string;
  name: string;
  type: 'bank_transfer' | 'credit_card' | 'e_wallet' | 'over_the_counter' | 'qris';
  provider: string;
  logo: string;
  fee: number;
  processingTime: string;
  description: string;
  isActive: boolean;
}

export default function PaymentGatewayPage() {
  const [activeTab, setActiveTab] = useState<'methods' | 'test' | 'transactions' | 'settings'>('methods');
  const [paymentMethods, setPaymentMethods] = useState<PaymentMethod[]>([]);
  const [testAmount, setTestAmount] = useState('');
  const [selectedMethod, setSelectedMethod] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    // Load payment methods
    const mockPaymentMethods: PaymentMethod[] = [
      {
        id: 'bank_transfer',
        name: 'Transfer Bank',
        type: 'bank_transfer',
        provider: 'Midtrans',
        logo: '🏦',
        fee: 4000,
        processingTime: '1-2 hari kerja',
        description: 'Transfer melalui ATM, Internet Banking, atau Mobile Banking',
        isActive: true,
      },
      {
        id: 'credit_card',
        name: 'Kartu Kredit/Debit',
        type: 'credit_card',
        provider: 'Midtrans',
        logo: '💳',
        fee: 0,
        processingTime: 'Instan',
        description: 'Visa, Mastercard, JCB, Amex',
        isActive: true,
      },
      {
        id: 'gopay',
        name: 'GoPay',
        type: 'e_wallet',
        provider: 'GoPay',
        logo: '🟢',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan GoPay',
        isActive: true,
      },
      {
        id: 'ovo',
        name: 'OVO',
        type: 'e_wallet',
        provider: 'OVO',
        logo: '🟣',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan OVO',
        isActive: true,
      },
      {
        id: 'dana',
        name: 'DANA',
        type: 'e_wallet',
        provider: 'DANA',
        logo: '🔵',
        fee: 0,
        processingTime: 'Instan',
        description: 'Bayar dengan DANA',
        isActive: true,
      },
      {
        id: 'qris',
        name: 'QRIS',
        type: 'qris',
        provider: 'QRIS',
        logo: '📱',
        fee: 0,
        processingTime: 'Instan',
        description: 'Scan QR Code untuk pembayaran',
        isActive: true,
      },
    ];
    setPaymentMethods(mockPaymentMethods);
  }, []);

  const handleTestPayment = async () => {
    if (!testAmount || !selectedMethod) return;

    setLoading(true);
    try {
      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 2000));
      alert('Test payment berhasil dibuat! (Simulasi)');
    } catch {
      alert('Test payment gagal!');
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
    }).format(amount);
  };

  const tabs = [
    { id: 'methods' as const, name: 'Metode Pembayaran', icon: '💳' },
    { id: 'test' as const, name: 'Test Payment', icon: '🧪' },
    { id: 'transactions' as const, name: 'Transaksi', icon: '📊' },
    { id: 'settings' as const, name: 'Pengaturan', icon: '⚙️' },
  ];

  return (
    <DashboardLayout>
      <div className="space-y-6">
        <div className="border-b border-gray-200 dark:border-gray-700">
          <div className="sm:flex sm:items-center">
            <div className="sm:flex-auto">
              <h1 className="text-2xl font-bold leading-6 text-gray-900 dark:text-white">
                Payment Gateway
              </h1>
              <p className="mt-2 text-sm text-gray-700 dark:text-gray-300">
                Integrasi pembayaran dengan berbagai metode pembayaran Indonesia
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

        {/* Payment Methods Tab */}
        {activeTab === 'methods' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Metode Pembayaran yang Tersedia
              </h3>
              
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {paymentMethods.map((method) => (
                  <div
                    key={method.id}
                    className={`
                      border-2 rounded-lg p-4 transition-all cursor-pointer
                      ${method.isActive 
                        ? 'border-green-200 bg-green-50 dark:border-green-700 dark:bg-green-900/20' 
                        : 'border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50'
                      }
                    `}
                  >
                    <div className="flex items-center justify-between mb-3">
                      <div className="flex items-center">
                        <span className="text-2xl mr-3">{method.logo}</span>
                        <div>
                          <h4 className="font-medium text-gray-900 dark:text-white">
                            {method.name}
                          </h4>
                          <p className="text-sm text-gray-500 dark:text-gray-400">
                            {method.provider}
                          </p>
                        </div>
                      </div>
                      <span className={`
                        px-2 py-1 rounded text-xs font-medium
                        ${method.isActive 
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' 
                          : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                        }
                      `}>
                        {method.isActive ? 'Aktif' : 'Nonaktif'}
                      </span>
                    </div>
                    
                    <p className="text-sm text-gray-600 dark:text-gray-400 mb-2">
                      {method.description}
                    </p>
                    
                    <div className="flex justify-between items-center text-sm">
                      <span className="text-gray-500 dark:text-gray-400">
                        Waktu: {method.processingTime}
                      </span>
                      <span className="font-medium text-gray-900 dark:text-white">
                        Biaya: {method.fee > 0 ? formatCurrency(method.fee) : 'Gratis'}
                      </span>
                    </div>
                  </div>
                ))}
              </div>
            </TouchCard>
          </div>
        )}

        {/* Test Payment Tab */}
        {activeTab === 'test' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Test Pembayaran
              </h3>
              
              <div className="space-y-4 max-w-md">
                <TouchInput
                  label="Jumlah Pembayaran (IDR)"
                  type="number"
                  placeholder="100000"
                  value={testAmount}
                  onChange={(e) => setTestAmount(e.target.value)}
                />
                
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Metode Pembayaran
                  </label>
                  <select
                    value={selectedMethod}
                    onChange={(e) => setSelectedMethod(e.target.value)}
                    className="block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 px-3 py-3 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20"
                  >
                    <option value="">Pilih metode pembayaran</option>
                    {paymentMethods.filter(m => m.isActive).map((method) => (
                      <option key={method.id} value={method.id}>
                        {method.name} - {method.fee > 0 ? `+${formatCurrency(method.fee)}` : 'Gratis'}
                      </option>
                    ))}
                  </select>
                </div>
                
                {testAmount && selectedMethod && (
                  <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <h4 className="font-medium text-blue-800 dark:text-blue-200 mb-2">
                      Ringkasan Pembayaran
                    </h4>
                    <div className="space-y-1 text-sm">
                      <div className="flex justify-between">
                        <span>Jumlah:</span>
                        <span>{formatCurrency(parseInt(testAmount))}</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Biaya Admin:</span>
                        <span>
                          {formatCurrency(paymentMethods.find(m => m.id === selectedMethod)?.fee || 0)}
                        </span>
                      </div>
                      <div className="flex justify-between font-medium border-t pt-1">
                        <span>Total:</span>
                        <span>
                          {formatCurrency(parseInt(testAmount) + (paymentMethods.find(m => m.id === selectedMethod)?.fee || 0))}
                        </span>
                      </div>
                    </div>
                  </div>
                )}
                
                <TouchButton
                  onClick={handleTestPayment}
                  loading={loading}
                  disabled={!testAmount || !selectedMethod}
                  fullWidth
                >
                  Buat Test Payment
                </TouchButton>
              </div>
            </TouchCard>
          </div>
        )}

        {/* Transactions Tab */}
        {activeTab === 'transactions' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Riwayat Transaksi
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
                        Riwayat transaksi akan ditampilkan di sini setelah integrasi dengan backend selesai.
                        Fitur ini akan menampilkan semua transaksi pembayaran dengan status dan detail lengkap.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </TouchCard>
          </div>
        )}

        {/* Settings Tab */}
        {activeTab === 'settings' && (
          <div className="space-y-6">
            <TouchCard className="p-6">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Pengaturan Payment Gateway
              </h3>
              
              <div className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                      Konfigurasi Midtrans
                    </h4>
                    <div className="space-y-3">
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Environment:</span>
                        <span className="px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 rounded text-xs font-medium">
                          Sandbox
                        </span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                        <span className="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded text-xs font-medium">
                          Terhubung
                        </span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Client Key:</span>
                        <span className="text-sm text-gray-400 font-mono">SB-Mid-client-***</span>
                      </div>
                    </div>
                  </div>
                  
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                      Statistik Pembayaran
                    </h4>
                    <div className="space-y-3">
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Total Transaksi:</span>
                        <span className="text-sm font-medium text-gray-900 dark:text-white">0</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Sukses:</span>
                        <span className="text-sm font-medium text-green-600 dark:text-green-400">0</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Gagal:</span>
                        <span className="text-sm font-medium text-red-600 dark:text-red-400">0</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div className="border-t border-gray-200 dark:border-gray-700 pt-6">
                  <h4 className="font-medium text-gray-900 dark:text-white mb-3">
                    Pengaturan Webhook
                  </h4>
                  <div className="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div className="space-y-2">
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Webhook URL:</span>
                        <span className="text-sm font-mono text-gray-900 dark:text-white">
                          https://api.bizmark.id/webhook/payment
                        </span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                        <span className="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 rounded text-xs font-medium">
                          Aktif
                        </span>
                      </div>
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
