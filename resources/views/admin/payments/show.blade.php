@extends('layouts.admin')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.payments.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h1 class="text-lg font-bold text-gray-900">Detail Pembayaran</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Informasi Pembayaran</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($payment->status === 'processing') bg-yellow-100 text-yellow-800
                        @elseif($payment->status === 'success') bg-green-100 text-green-800
                        @elseif($payment->status === 'failed') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">No. Pembayaran</p>
                        <p class="font-semibold text-gray-900">{{ $payment->payment_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jumlah</p>
                        <p class="font-bold text-lg text-gray-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tipe Pembayaran</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Metode</p>
                        <p class="font-semibold text-gray-900">{{ ucfirst($payment->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Upload</p>
                        <p class="font-semibold text-gray-900">{{ $payment->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($payment->verified_at)
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Verifikasi</p>
                            <p class="font-semibold text-gray-900">{{ $payment->verified_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if($payment->verification_notes)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Catatan Verifikasi</p>
                        <p class="text-gray-900">{{ $payment->verification_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Transfer Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Transfer</h2>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama Bank</span>
                        <span class="font-semibold">{{ $payment->bank_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nama Pengirim</span>
                        <span class="font-semibold">{{ $payment->account_holder }}</span>
                    </div>
                </div>
            </div>

            <!-- Transfer Proof -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Bukti Transfer</h2>
                
                @if($payment->transfer_proof_path)
                    @php
                        $extension = pathinfo($payment->transfer_proof_path, PATHINFO_EXTENSION);
                        $proofUrl = route('admin.payments.proof', $payment->id);
                        $proofDownloadUrl = route('admin.payments.proof', ['id' => $payment->id, 'download' => 1]);
                    @endphp
                    
                    @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                        <div class="border rounded-lg overflow-hidden">
                            <img src="{{ $proofUrl }}" 
                                 alt="Bukti Transfer" 
                                 class="w-full h-auto">
                        </div>
                        <a href="{{ $proofUrl }}" 
                           target="_blank"
                           class="mt-3 inline-flex items-center text-purple-600 hover:text-purple-800">
                            <i class="fas fa-external-link-alt mr-2"></i>Buka di tab baru
                        </a>
                    @elseif(strtolower($extension) === 'pdf')
                        <div class="border rounded-lg p-8 text-center bg-gray-50">
                            <i class="fas fa-file-pdf text-red-500 text-4xl mb-4"></i>
                            <p class="text-gray-700 mb-4">File PDF</p>
                            <a href="{{ $proofUrl }}" 
                               target="_blank"
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-download mr-2"></i>Download / Preview
                            </a>
                        </div>
                    @else
                        <a href="{{ $proofDownloadUrl }}"
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            <i class="fas fa-download mr-2"></i>Unduh Bukti Transfer
                        </a>
                    @endif
                @else
                    <p class="text-gray-500">Tidak ada bukti transfer</p>
                @endif
            </div>

            <!-- Verification Actions -->
            @if($payment->status === 'processing' || $payment->status === 'pending')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Verifikasi Pembayaran</h2>
                    
                    <!-- Approve Form -->
                    <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" class="mb-4" x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini?')) $el.submit()">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                            <textarea 
                                name="notes" 
                                rows="3" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                placeholder="Tambahkan catatan verifikasi..."></textarea>
                        </div>
                        
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-check-circle mr-2"></i>Verifikasi Pembayaran
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST" x-data @submit.prevent="if(confirm('Apakah Anda yakin ingin menolak pembayaran ini?')) $el.submit()">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                name="notes"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                placeholder="Jelaskan alasan penolakan..."
                                required></textarea>
                        </div>
                        
                        <button
                            type="submit"
                            class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times-circle mr-2"></i>Tolak Pembayaran
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Client Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Client</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Nama</p>
                        <p class="font-semibold text-gray-900">{{ $payment->client->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-gray-900">{{ $payment->client->email }}</p>
                    </div>
                    @if($payment->client->phone)
                        <div>
                            <p class="text-sm text-gray-600">Telepon</p>
                            <p class="text-gray-900">{{ $payment->client->phone }}</p>
                        </div>
                    @endif
                </div>

                <a href="{{ route('admin.permit-applications.show', $payment->quotation->application->id) }}" 
                   class="mt-4 block text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-eye mr-2"></i>Lihat Aplikasi
                </a>
            </div>

            <!-- Application Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Aplikasi</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">No. Aplikasi</p>
                        <p class="font-semibold text-gray-900">{{ $payment->quotation->application->application_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Jenis Perizinan</p>
                        <p class="text-gray-900">{{ $payment->quotation->application->permitType->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Aplikasi</p>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst(str_replace('_', ' ', $payment->quotation->application->status)) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quotation Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Informasi Quotation</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">No. Quotation</p>
                        <p class="font-semibold text-gray-900">{{ $payment->quotation->quotation_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="font-bold text-lg text-gray-900">Rp {{ number_format($payment->quotation->total_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Down Payment</p>
                        <p class="font-semibold text-gray-900">Rp {{ number_format($payment->quotation->down_payment_amount, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Remaining</p>
                        <p class="font-semibold text-gray-900">
                            Rp {{ number_format($payment->quotation->total_amount - $payment->quotation->down_payment_amount, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            @if($payment->verified_by)
                <div class="bg-green-50 rounded-lg border border-green-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-3">
                        <i class="fas fa-user-check text-green-600 mr-2"></i>Diverifikasi Oleh
                    </h3>
                    <p class="text-gray-900">{{ $payment->verifier->name }}</p>
                    <p class="text-sm text-gray-600">{{ $payment->verified_at->format('d M Y H:i') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
