@extends('layouts.admin')

@section('title', 'Detail Revisi - ' . $application->application_number)

@section('content')
<div class="py-6">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.permit-applications.show', $application->id) }}" class="text-dark-text-secondary hover:text-white transition-colors">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-white">Detail Revisi Paket</h1>
                <p class="text-dark-text-secondary mt-1">
                    {{ $application->application_number }} · Revisi #{{ $revision->revision_number ?? '—' }}
                </p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-apple-lg p-4 bg-apple-green/15 border-l-4 border-[#34C759]">
                <span class="text-apple-green"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

            <!-- Revision Info -->
            <div class="card-elevated rounded-apple-xl p-5">
                <h2 class="font-semibold mb-4 text-white">
                    <i class="fas fa-edit mr-2 text-apple-blue"></i>Informasi Revisi
                </h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-dark-text-tertiary">Nomor Revisi</dt>
                        <dd class="text-dark-text-primary/90 font-medium">#{{ $revision->revision_number ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-dark-text-tertiary">Tanggal Revisi</dt>
                        <dd class="text-dark-text-primary/90">{{ $revision->created_at?->format('d M Y, H:i') ?? '—' }}</dd>
                    </div>
                    @if($revision->revisedBy)
                    <div class="flex justify-between">
                        <dt class="text-dark-text-tertiary">Direvisi Oleh</dt>
                        <dd class="text-dark-text-primary/90">{{ $revision->revisedBy->name }}</dd>
                    </div>
                    @endif
                    @if($revision->reason)
                    <div>
                        <dt class="text-dark-text-tertiary mb-1">Alasan Revisi</dt>
                        <dd class="text-dark-text-primary/80 rounded-apple-lg p-3 bg-white/5">
                            {{ $revision->reason }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Package Summary -->
            <div class="card-elevated rounded-apple-xl p-5">
                <h2 class="font-semibold mb-4 text-white">
                    <i class="fas fa-box-open mr-2 text-[#5856D6]"></i>Ringkasan Paket Saat Ini
                </h2>
                @if($currentPackage)
                    <dl class="space-y-3 text-sm">
                        @if(isset($currentPackage['project_data']['project_name']))
                        <div class="flex justify-between">
                            <dt class="text-dark-text-tertiary">Nama Proyek</dt>
                            <dd class="text-dark-text-primary/90 font-medium">{{ $currentPackage['project_data']['project_name'] }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-dark-text-tertiary">Jumlah Izin</dt>
                            <dd class="text-dark-text-primary/90">{{ count($currentPackage['permits'] ?? []) }} izin</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-dark-text-tertiary">Total Biaya</dt>
                            <dd class="font-bold text-apple-green">
                                Rp {{ number_format($currentPackage['total_cost'] ?? 0, 0, ',', '.') }}
                            </dd>
                        </div>
                        @if(isset($currentPackage['revision_number']))
                        <div class="flex justify-between">
                            <dt class="text-dark-text-tertiary">Nomor Versi</dt>
                            <dd class="text-dark-text-primary/90">v{{ $currentPackage['revision_number'] }}</dd>
                        </div>
                        @endif
                    </dl>
                @else
                    <p class="text-dark-text-tertiary text-sm">Data paket tidak tersedia</p>
                @endif
            </div>
        </div>

        <!-- Revised Quotation Items -->
        @if($revision->quotationItems && $revision->quotationItems->count() > 0)
        <div class="card-elevated rounded-apple-xl p-5 mb-6">
            <h2 class="font-semibold mb-4 text-white">
                <i class="fas fa-list-ul mr-2 text-apple-orange"></i>Item Quotation yang Direvisi
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/8">
                            <th class="text-left pb-3 pr-4 text-dark-text-tertiary">Jenis Izin</th>
                            <th class="text-right pb-3 pr-4 text-dark-text-tertiary">Harga Sebelum</th>
                            <th class="text-right pb-3 pr-4 text-dark-text-tertiary">Harga Baru</th>
                            <th class="text-right pb-3 text-dark-text-tertiary">Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revision->quotationItems as $item)
                        @php
                            $diff = ($item->new_price ?? 0) - ($item->old_price ?? 0);
                        @endphp
                        <tr class="border-b border-white/5">
                            <td class="py-3 pr-4 text-dark-text-primary/90">
                                {{ $item->permitType?->name ?? $item->permit_type ?? '—' }}
                            </td>
                            <td class="py-3 pr-4 text-right text-dark-text-secondary">
                                Rp {{ number_format($item->old_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 pr-4 text-right font-medium text-dark-text-primary/90">
                                Rp {{ number_format($item->new_price ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-semibold">
                                @if($diff > 0)
                                    <span class="text-[#FF453A]">+Rp {{ number_format($diff, 0, ',', '.') }}</span>
                                @elseif($diff < 0)
                                    <span class="text-apple-green">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</span>
                                @else
                                    <span class="text-dark-text-tertiary">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Permits List in Current Package -->
        @if(!empty($currentPackage['permits']))
        <div class="card-elevated rounded-apple-xl p-5">
            <h2 class="font-semibold mb-4 text-white">
                <i class="fas fa-file-alt mr-2 text-[#30D958]"></i>Daftar Izin dalam Paket
            </h2>
            <div class="space-y-3">
                @foreach($currentPackage['permits'] as $permit)
                <div class="flex items-center justify-between p-3 rounded-apple-lg bg-white/4 border border-white/6">
                    <div>
                        <p class="font-medium text-sm text-dark-text-primary/90">
                            {{ $permit['name'] ?? $permit['permit_type'] ?? '—' }}
                        </p>
                        @if(isset($permit['service']))
                            <p class="text-xs mt-0.5 text-dark-text-tertiary">
                                Penanganan: {{ ucfirst($permit['service']) }}
                            </p>
                        @endif
                    </div>
                    @if(isset($permit['price']))
                    <span class="font-semibold text-sm text-apple-green">
                        Rp {{ number_format($permit['price'], 0, ',', '.') }}
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('admin.permit-applications.show', $application->id) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-apple-lg text-sm font-medium transition-colors bg-white/8 text-dark-text-primary/80 hover:bg-white/12">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail Permohonan
            </a>
        </div>

    </div>
</div>
@endsection
