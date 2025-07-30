'use client';

import { useState } from 'react';

interface FAQItem {
  id: string;
  question: string;
  answer: string;
  category: string;
}

interface GuideSection {
  id: string;
  title: string;
  content: string;
  steps?: string[];
}

export default function HelpPage() {
  const [activeTab, setActiveTab] = useState('guide');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');

  const faqData: FAQItem[] = [
    {
      id: '1',
      question: 'Bagaimana cara mengajukan permohonan izin usaha?',
      answer: 'Untuk mengajukan permohonan izin usaha, masuk ke menu "Permohonan" di dashboard Anda, pilih jenis izin yang dibutuhkan, isi formulir dengan lengkap, dan upload dokumen yang diperlukan. Setelah semua data lengkap, klik "Kirim Permohonan".',
      category: 'permohonan'
    },
    {
      id: '2',
      question: 'Dokumen apa saja yang diperlukan untuk mengajukan SIUP?',
      answer: 'Untuk mengajukan SIUP, Anda memerlukan: KTP pemilik usaha, NPWP, akta pendirian usaha (jika PT/CV), surat keterangan domisili usaha, dan pas foto pemilik usaha 3x4.',
      category: 'dokumen'
    },
    {
      id: '3',
      question: 'Berapa lama proses persetujuan permohonan?',
      answer: 'Waktu proses bervariasi tergantung jenis izin: SIUP (5-7 hari kerja), TDP (3-5 hari kerja), NIB (1-3 hari kerja), BPOM (10-14 hari kerja), Sertifikat Halal (7-10 hari kerja).',
      category: 'proses'
    },
    {
      id: '4',
      question: 'Bagaimana cara melacak status permohonan saya?',
      answer: 'Anda dapat melacak status permohonan di menu "Permohonan" pada dashboard. Status akan terupdate secara real-time dan Anda akan mendapat notifikasi setiap ada perubahan status.',
      category: 'tracking'
    },
    {
      id: '5',
      question: 'Apa yang harus dilakukan jika permohonan ditolak?',
      answer: 'Jika permohonan ditolak, Anda dapat melihat alasan penolakan di detail permohonan. Perbaiki dokumen atau data sesuai catatan, lalu ajukan permohonan ulang dengan data yang sudah diperbaiki.',
      category: 'permohonan'
    },
    {
      id: '6',
      question: 'Bagaimana cara mengubah data profil bisnis?',
      answer: 'Data profil bisnis dapat diubah melalui menu "Profil" → tab "Informasi Usaha". Setelah mengubah data, klik "Simpan Perubahan". Perubahan data akan mempengaruhi permohonan yang akan datang.',
      category: 'akun'
    }
  ];

  const guideData: GuideSection[] = [
    {
      id: 'getting-started',
      title: 'Memulai dengan Bizmark.id',
      content: 'Panduan lengkap untuk memulai menggunakan platform Bizmark.id untuk mengurus izin usaha UMKM Anda.',
      steps: [
        'Daftar akun baru dengan email dan data diri yang valid',
        'Verifikasi email dan lengkapi profil bisnis Anda',
        'Pilih jenis izin usaha yang dibutuhkan',
        'Siapkan dokumen pendukung dalam format digital',
        'Ajukan permohonan dan pantau statusnya'
      ]
    },
    {
      id: 'document-preparation',
      title: 'Persiapan Dokumen',
      content: 'Tips dan panduan untuk mempersiapkan dokumen yang diperlukan dalam pengajuan izin usaha.',
      steps: [
        'Pastikan semua dokumen dalam format PDF atau JPG',
        'Ukuran file maksimal 5MB per dokumen',
        'Dokumen harus jelas dan mudah dibaca',
        'Gunakan scan berkualitas tinggi, hindari foto dari kamera',
        'Pastikan informasi dalam dokumen sesuai dengan data yang diinput'
      ]
    },
    {
      id: 'application-process',
      title: 'Proses Pengajuan',
      content: 'Langkah-langkah detail dalam mengajukan permohonan izin usaha melalui platform Bizmark.id.',
      steps: [
        'Login ke dashboard dan pilih menu "Permohonan"',
        'Klik "Ajukan Permohonan Baru" dan pilih jenis izin',
        'Isi formulir dengan data yang akurat dan lengkap',
        'Upload semua dokumen yang diperlukan',
        'Review semua data sebelum mengirim permohonan',
        'Pantau status permohonan melalui dashboard'
      ]
    },
    {
      id: 'business-management',
      title: 'Manajemen Bisnis',
      content: 'Cara mengelola informasi bisnis dan mengoptimalkan penggunaan platform untuk kebutuhan usaha Anda.',
      steps: [
        'Lengkapi profil bisnis dengan informasi yang akurat',
        'Update data bisnis secara berkala',
        'Gunakan fitur notifikasi untuk mengikuti perkembangan',
        'Manfaatkan dashboard analytics untuk insight bisnis',
        'Hubungi support jika memerlukan bantuan'
      ]
    }
  ];

  const categories = [
    { id: 'all', label: 'Semua Kategori' },
    { id: 'permohonan', label: 'Permohonan' },
    { id: 'dokumen', label: 'Dokumen' },
    { id: 'proses', label: 'Proses' },
    { id: 'tracking', label: 'Pelacakan' },
    { id: 'akun', label: 'Akun' }
  ];

  const tabs = [
    { id: 'guide', label: 'Panduan', icon: '📚' },
    { id: 'faq', label: 'FAQ', icon: '❓' },
    { id: 'contact', label: 'Hubungi Kami', icon: '📞' },
    { id: 'resources', label: 'Resources', icon: '📋' }
  ];

  const filteredFAQ = faqData.filter(faq => {
    const matchesSearch = faq.question.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         faq.answer.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = selectedCategory === 'all' || faq.category === selectedCategory;
    return matchesSearch && matchesCategory;
  });

  return (
    <div className="max-w-6xl mx-auto space-y-6">
      {/* Header */}
      <div className="bg-white rounded-lg shadow p-6">
        <div className="text-center">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">Pusat Bantuan Bizmark.id</h1>
          <p className="text-gray-600 text-lg">
            Temukan jawaban untuk pertanyaan Anda dan pelajari cara menggunakan platform kami
          </p>
        </div>
      </div>

      {/* Search Bar */}
      <div className="bg-white rounded-lg shadow p-6">
        <div className="relative max-w-xl mx-auto">
          <input
            type="text"
            placeholder="Cari bantuan, panduan, atau FAQ..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <svg className="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      {/* Tabs */}
      <div className="bg-white rounded-lg shadow">
        <div className="border-b border-gray-200">
          <nav className="flex space-x-8 px-6">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                <span className="mr-2">{tab.icon}</span>
                {tab.label}
              </button>
            ))}
          </nav>
        </div>

        <div className="p-6">
          {/* Guide Tab */}
          {activeTab === 'guide' && (
            <div className="space-y-8">
              <div className="text-center mb-8">
                <h2 className="text-2xl font-bold text-gray-900 mb-2">Panduan Lengkap</h2>
                <p className="text-gray-600">Pelajari cara menggunakan Bizmark.id secara optimal</p>
              </div>

              <div className="grid gap-6">
                {guideData.map((guide) => (
                  <div key={guide.id} className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-xl font-semibold text-gray-900 mb-3">{guide.title}</h3>
                    <p className="text-gray-600 mb-4">{guide.content}</p>
                    {guide.steps && (
                      <div className="space-y-2">
                        <h4 className="font-medium text-gray-900">Langkah-langkah:</h4>
                        <ol className="list-decimal list-inside space-y-1 text-gray-600">
                          {guide.steps.map((step, index) => (
                            <li key={index}>{step}</li>
                          ))}
                        </ol>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* FAQ Tab */}
          {activeTab === 'faq' && (
            <div className="space-y-6">
              <div className="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <h2 className="text-2xl font-bold text-gray-900">Frequently Asked Questions</h2>
                <select
                  value={selectedCategory}
                  onChange={(e) => setSelectedCategory(e.target.value)}
                  className="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  {categories.map((category) => (
                    <option key={category.id} value={category.id}>
                      {category.label}
                    </option>
                  ))}
                </select>
              </div>

              <div className="space-y-4">
                {filteredFAQ.map((faq) => (
                  <div key={faq.id} className="bg-gray-50 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-3">{faq.question}</h3>
                    <p className="text-gray-600 leading-relaxed">{faq.answer}</p>
                    <div className="mt-3">
                      <span className="inline-flex px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                        {categories.find(cat => cat.id === faq.category)?.label}
                      </span>
                    </div>
                  </div>
                ))}
              </div>

              {filteredFAQ.length === 0 && (
                <div className="text-center py-12">
                  <div className="text-gray-400 text-6xl mb-4">🔍</div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">Tidak ada hasil ditemukan</h3>
                  <p className="text-gray-600">Coba gunakan kata kunci yang berbeda atau hubungi tim support kami</p>
                </div>
              )}
            </div>
          )}

          {/* Contact Tab */}
          {activeTab === 'contact' && (
            <div className="space-y-8">
              <div className="text-center">
                <h2 className="text-2xl font-bold text-gray-900 mb-2">Hubungi Tim Support</h2>
                <p className="text-gray-600">Kami siap membantu Anda 24/7</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {/* Email Support */}
                <div className="bg-blue-50 rounded-lg p-6 text-center">
                  <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Email Support</h3>
                  <p className="text-gray-600 mb-4">Kirim email dan kami akan balas dalam 24 jam</p>
                  <a href="mailto:support@bizmark.id" className="text-blue-600 font-medium hover:text-blue-700">
                    support@bizmark.id
                  </a>
                </div>

                {/* Phone Support */}
                <div className="bg-green-50 rounded-lg p-6 text-center">
                  <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Telepon</h3>
                  <p className="text-gray-600 mb-4">Hubungi langsung tim support kami</p>
                  <a href="tel:+6221234567890" className="text-green-600 font-medium hover:text-green-700">
                    (021) 234-567-890
                  </a>
                </div>

                {/* Live Chat */}
                <div className="bg-purple-50 rounded-lg p-6 text-center">
                  <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Live Chat</h3>
                  <p className="text-gray-600 mb-4">Chat langsung dengan tim support</p>
                  <button className="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                    Mulai Chat
                  </button>
                </div>
              </div>

              {/* Contact Form */}
              <div className="bg-gray-50 rounded-lg p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Kirim Pesan</h3>
                <form className="space-y-4">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                      <input
                        type="text"
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Nama lengkap Anda"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">Email</label>
                      <input
                        type="email"
                        className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="email@example.com"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Subjek</label>
                    <input
                      type="text"
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Subjek pesan"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                    <textarea
                      rows={4}
                      className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                      placeholder="Tulis pesan Anda di sini..."
                    />
                  </div>
                  <div>
                    <button
                      type="submit"
                      className="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      Kirim Pesan
                    </button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* Resources Tab */}
          {activeTab === 'resources' && (
            <div className="space-y-8">
              <div className="text-center">
                <h2 className="text-2xl font-bold text-gray-900 mb-2">Resources & Downloads</h2>
                <p className="text-gray-600">Template, formulir, dan dokumen panduan</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Document Templates */}
                <div className="bg-gray-50 rounded-lg p-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-4">Template Dokumen</h3>
                  <div className="space-y-3">
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-red-500 mr-3">📄</div>
                        <span className="text-sm font-medium">Template Surat Keterangan Domisili</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-red-500 mr-3">📄</div>
                        <span className="text-sm font-medium">Template Akta Pendirian Usaha</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-red-500 mr-3">📄</div>
                        <span className="text-sm font-medium">Formulir Permohonan SIUP</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                  </div>
                </div>

                {/* Guides & Manuals */}
                <div className="bg-gray-50 rounded-lg p-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-4">Panduan & Manual</h3>
                  <div className="space-y-3">
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-blue-500 mr-3">📚</div>
                        <span className="text-sm font-medium">Panduan Lengkap Mengurus SIUP</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-blue-500 mr-3">📚</div>
                        <span className="text-sm font-medium">Manual Penggunaan Platform</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                    <div className="flex items-center justify-between p-3 bg-white rounded border">
                      <div className="flex items-center">
                        <div className="text-blue-500 mr-3">📚</div>
                        <span className="text-sm font-medium">Checklist Persiapan Dokumen</span>
                      </div>
                      <button className="text-blue-600 hover:text-blue-700 text-sm">Download</button>
                    </div>
                  </div>
                </div>
              </div>

              {/* Video Tutorials */}
              <div className="bg-gray-50 rounded-lg p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">Video Tutorial</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div className="bg-white rounded-lg p-4 border">
                    <div className="aspect-video bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                      <svg className="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                    <h4 className="font-medium text-gray-900 mb-1">Cara Daftar Akun Baru</h4>
                    <p className="text-sm text-gray-600">5 menit</p>
                  </div>
                  <div className="bg-white rounded-lg p-4 border">
                    <div className="aspect-video bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                      <svg className="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                    <h4 className="font-medium text-gray-900 mb-1">Mengajukan Permohonan SIUP</h4>
                    <p className="text-sm text-gray-600">8 menit</p>
                  </div>
                  <div className="bg-white rounded-lg p-4 border">
                    <div className="aspect-video bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                      <svg className="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                      </svg>
                    </div>
                    <h4 className="font-medium text-gray-900 mb-1">Upload dan Kelola Dokumen</h4>
                    <p className="text-sm text-gray-600">6 menit</p>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
