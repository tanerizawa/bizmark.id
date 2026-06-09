<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>Permohonan Terkirim - {{ $serviceRequest->request_number }} - Bizmark.ID</title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    
    <!-- Tailwind CSS (compiled) -->
    @vite('resources/css/public.css')
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Landing Page Styles -->
    @include('landing.partials.critical-css')
    @vite('resources/css/landing-theme.css')
    
    <style>
        :root {
            --color-primary: #0f172a;
            --color-accent: #0ea5e9;
            --color-secondary: #f97316;
            --surface-cool: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-tertiary: #94a3b8;
            --border-light: #e2e8f0;
        }

        .result-page {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--surface-cool) 0%, #f1f5f9 100%);
        }
        
        .success-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .success-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-accent) 100%);
            padding: 40px 24px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes checkmark {
            0% { stroke-dashoffset: 50; }
            100% { stroke-dashoffset: 0; }
        }
        
        .checkmark {
            animation: checkmark 0.6s ease-out 0.3s forwards;
            stroke-dasharray: 50;
            stroke-dashoffset: 50;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 16px 0;
            border-bottom: 1px solid var(--border-light);
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .info-value {
            color: var(--text-primary);
            font-weight: 600;
            text-align: right;
            max-width: 60%;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: rgba(249, 115, 22, 0.1);
            color: var(--color-secondary);
        }
        
        .timeline-step {
            display: flex;
            gap: 16px;
            padding-bottom: 24px;
            position: relative;
        }
        
        .timeline-step:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            bottom: 0;
            width: 2px;
            background: var(--border-light);
        }
        
        .timeline-step.active::before {
            background: linear-gradient(180deg, var(--color-accent) 0%, var(--border-light) 100%);
        }
        
        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .timeline-icon.completed {
            background: var(--color-accent);
            color: white;
        }
        
        .timeline-icon.active {
            background: rgba(249, 115, 22, 0.1);
            color: var(--color-secondary);
            animation: pulse 2s infinite;
        }
        
        .timeline-icon.pending {
            background: var(--surface-cool);
            color: var(--text-tertiary);
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4); }
            50% { box-shadow: 0 0 0 10px rgba(249, 115, 22, 0); }
        }
        
        .cta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        
        .cta-primary {
            background: var(--color-primary);
            color: white;
        }
        
        .cta-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px -8px rgba(15, 23, 42, 0.5);
        }
        
        .cta-secondary {
            background: var(--surface-cool);
            color: var(--text-secondary);
        }
        
        .cta-secondary:hover {
            background: var(--border-light);
        }
        
        .cta-whatsapp {
            background: #25D366;
            color: white;
        }
        
        .cta-whatsapp:hover {
            background: #128C7E;
        }

        .info-box {
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid var(--color-accent);
        }

        .section-title {
            color: var(--text-primary);
            font-weight: 700;
        }

        .icon-primary {
            color: var(--color-accent);
        }

        .icon-secondary {
            color: var(--color-secondary);
        }
    </style>
</head>
<body class="result-page">
    <!-- Navbar -->
    @include('landing.partials.navbar')
    @include('landing.partials.mobile-menu')
    
    <div class="container mx-auto px-4 py-8" style="padding-top: 100px;">
        <div class="max-w-2xl mx-auto">
            
            <!-- Success Card -->
            <div class="success-card">
                <!-- Success Header -->
                <div class="success-header">
                    <div class="success-icon">
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path class="checkmark" d="M10 20L17 27L30 14" stroke="#0ea5e9" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Permohonan Berhasil Dikirim!</h1>
                    <p class="opacity-90">Terima kasih telah mengajukan permohonan</p>
                </div>
                
                <!-- Content -->
                <div class="p-6 sm:p-8">
                    @php
                        $statusConfig = [
                            'pending' => [
                                'badge_style' => 'background: rgba(249, 115, 22, 0.1); color: var(--color-secondary);',
                                'badge_icon' => 'fa-clock',
                                'headline' => 'Permohonan Anda sudah masuk ke sistem dan menunggu review awal.',
                                'info_title' => 'Konfirmasi Email',
                                'info_body' => 'Kami telah mengirimkan email konfirmasi ke <strong>' . e($serviceRequest->email) . '</strong>. Tim kami akan segera memulai review awal terhadap permohonan ini.',
                                'wa_text' => 'Halo Bizmark! Saya ingin menindaklanjuti permohonan dengan nomor ' . $serviceRequest->request_number . '.',
                            ],
                            'reviewing' => [
                                'badge_style' => 'background: rgba(14, 165, 233, 0.1); color: var(--color-accent);',
                                'badge_icon' => 'fa-magnifying-glass',
                                'headline' => 'Permohonan sedang ditinjau oleh tim konsultan.',
                                'info_title' => 'Sedang Ditinjau',
                                'info_body' => 'Tim kami sedang memeriksa kebutuhan, ruang lingkup layanan, dan kelengkapan informasi permohonan Anda. Jika ada detail tambahan yang diperlukan, kami akan menghubungi Anda.',
                                'wa_text' => 'Halo Bizmark! Saya ingin menanyakan progres review untuk permohonan ' . $serviceRequest->request_number . '.',
                            ],
                            'quoted' => [
                                'badge_style' => 'background: rgba(99, 102, 241, 0.12); color: #6366f1;',
                                'badge_icon' => 'fa-file-invoice-dollar',
                                'headline' => 'Penawaran sudah tersedia untuk Anda pelajari.',
                                'info_title' => 'Penawaran Telah Dikirim',
                                'info_body' => 'Penawaran biaya telah disiapkan' . ($serviceRequest->responded_at ? ' dan dikirim pada <strong>' . $serviceRequest->responded_at->format('d M Y H:i') . '</strong>' : '') . '. Silakan cek email Anda untuk detail penawaran dan timeline.',
                                'wa_text' => 'Halo Bizmark! Saya ingin mendiskusikan penawaran untuk permohonan ' . $serviceRequest->request_number . '.',
                            ],
                            'accepted' => [
                                'badge_style' => 'background: rgba(34, 197, 94, 0.12); color: #16a34a;',
                                'badge_icon' => 'fa-circle-check',
                                'headline' => 'Penawaran telah dikonfirmasi dan layanan sedang berjalan.',
                                'info_title' => 'Layanan Sedang Berjalan',
                                'info_body' => 'Terima kasih atas konfirmasinya. Tim kami sedang atau akan segera menjalankan layanan sesuai ruang lingkup yang telah disepakati.',
                                'wa_text' => 'Halo Bizmark! Saya ingin menanyakan update pelaksanaan layanan untuk permohonan ' . $serviceRequest->request_number . '.',
                            ],
                            'rejected' => [
                                'badge_style' => 'background: rgba(239, 68, 68, 0.12); color: #dc2626;',
                                'badge_icon' => 'fa-circle-xmark',
                                'headline' => 'Permohonan belum dapat dilanjutkan pada tahap ini.',
                                'info_title' => 'Permohonan Tidak Dilanjutkan',
                                'info_body' => 'Permohonan ini saat ini tidak dilanjutkan. Jika Anda ingin mengajukan ulang atau menyesuaikan ruang lingkup kebutuhan, silakan hubungi tim kami.',
                                'wa_text' => 'Halo Bizmark! Saya ingin berdiskusi ulang mengenai permohonan ' . $serviceRequest->request_number . '.',
                            ],
                            'cancelled' => [
                                'badge_style' => 'background: rgba(100, 116, 139, 0.14); color: #64748b;',
                                'badge_icon' => 'fa-ban',
                                'headline' => 'Permohonan telah dibatalkan.',
                                'info_title' => 'Permohonan Dibatalkan',
                                'info_body' => 'Permohonan ini sudah dibatalkan. Anda dapat membuat permohonan baru kapan saja jika membutuhkan bantuan lanjutan.',
                                'wa_text' => 'Halo Bizmark! Saya ingin membuat permohonan baru setelah pembatalan nomor ' . $serviceRequest->request_number . '.',
                            ],
                        ];

                        $currentStatus = $serviceRequest->status;
                        $currentStatusConfig = $statusConfig[$currentStatus] ?? $statusConfig['pending'];
                        $statusOrder = ['pending' => 1, 'reviewing' => 2, 'quoted' => 3, 'accepted' => 4];
                        $currentOrder = $statusOrder[$currentStatus] ?? 1;

                        $timelineSteps = [
                            [
                                'key' => 'pending',
                                'icon' => 'fa-check',
                                'title' => 'Permohonan Diterima',
                                'description' => 'Data permohonan Anda telah kami terima dan tercatat di sistem.',
                                'timestamp' => $serviceRequest->created_at,
                            ],
                            [
                                'key' => 'reviewing',
                                'icon' => 'fa-search',
                                'title' => 'Review Tim',
                                'description' => $serviceRequest->reviewed_at
                                    ? 'Tim telah melakukan review awal terhadap kebutuhan layanan Anda.'
                                    : 'Tahap review akan dimulai setelah antrian permohonan diproses oleh tim.',
                                'timestamp' => $serviceRequest->reviewed_at,
                            ],
                            [
                                'key' => 'quoted',
                                'icon' => 'fa-file-invoice-dollar',
                                'title' => 'Penawaran Harga',
                                'description' => $serviceRequest->quoted_price
                                    ? 'Penawaran sebesar ' . $serviceRequest->formatted_quoted_price . ($serviceRequest->quoted_timeline ? ' dengan estimasi timeline ' . $serviceRequest->quoted_timeline : '') . ' telah disiapkan.'
                                    : 'Penawaran akan dikirimkan setelah hasil review selesai.',
                                'timestamp' => $serviceRequest->quoted_at,
                            ],
                            [
                                'key' => 'accepted',
                                'icon' => 'fa-handshake',
                                'title' => 'Konfirmasi & Mulai',
                                'description' => match ($currentStatus) {
                                    'accepted' => 'Konfirmasi telah diterima dan tim mulai menjalankan layanan Anda.',
                                    'rejected' => 'Tahap ini tidak dilanjutkan karena penawaran tidak disetujui.',
                                    'cancelled' => 'Tahap ini tidak dijalankan karena permohonan dibatalkan.',
                                    default => 'Setelah penawaran disetujui, layanan akan segera dimulai oleh tim kami.',
                                },
                                'timestamp' => $serviceRequest->completed_at,
                            ],
                        ];
                    @endphp

                    <!-- Request Number -->
                    <div class="text-center mb-8 p-5 rounded-xl" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.05) 0%, rgba(249, 115, 22, 0.05) 100%); border: 1px solid var(--border-light);">
                        <p class="text-sm" style="color: var(--text-secondary);">Nomor Permohonan</p>
                        <p class="text-2xl font-bold mt-1 mb-2" style="color: var(--text-primary); font-family: 'Courier New', monospace;">{{ $serviceRequest->request_number }}</p>
                        <p class="text-xs" style="color: var(--text-tertiary);">Simpan nomor ini untuk tracking status</p>
                    </div>

                    <div class="mb-8 rounded-xl p-5" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.03) 0%, rgba(14, 165, 233, 0.08) 100%); border: 1px solid var(--border-light);">
                        <div class="flex items-start justify-between gap-4 flex-col sm:flex-row">
                            <div>
                                <p class="text-xs uppercase tracking-wide mb-2" style="color: var(--text-tertiary);">Status Saat Ini</p>
                                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-sm font-semibold" style="{{ $currentStatusConfig['badge_style'] }}">
                                    <i class="fas {{ $currentStatusConfig['badge_icon'] }}"></i>
                                    {{ $serviceRequest->status_label }}
                                </div>
                                <p class="text-sm mt-3" style="color: var(--text-secondary);">{{ $currentStatusConfig['headline'] }}</p>
                            </div>
                            @if($serviceRequest->quoted_price)
                                <div class="text-left sm:text-right">
                                    <p class="text-xs uppercase tracking-wide" style="color: var(--text-tertiary);">Nilai Penawaran</p>
                                    <p class="text-xl font-bold mt-1" style="color: var(--text-primary);">{{ $serviceRequest->formatted_quoted_price }}</p>
                                    @if($serviceRequest->quoted_timeline)
                                        <p class="text-xs mt-1" style="color: var(--text-secondary);">{{ $serviceRequest->quoted_timeline }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Request Summary -->
                    <div class="mb-8">
                        <h3 class="font-bold section-title mb-4 flex items-center gap-2">
                            <i class="fas fa-file-alt icon-primary"></i>
                            Ringkasan Permohonan
                        </h3>
                        
                        <div class="rounded-xl p-4" style="background: var(--surface-cool);">
                            <div class="info-row">
                                <span class="info-label">Jenis Pemohon</span>
                                <span class="info-value">
                                    @if($serviceRequest->applicant_type === 'perorangan')
                                        <i class="fas fa-user icon-primary mr-1"></i> Perorangan
                                    @else
                                        <i class="fas fa-building icon-secondary mr-1"></i> Badan Usaha
                                    @endif
                                </span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Nama</span>
                                <span class="info-value">{{ $serviceRequest->display_name }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $serviceRequest->email }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Telepon</span>
                                <span class="info-value">{{ $serviceRequest->phone }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Kategori Layanan</span>
                                <span class="info-value">
                                    {{ \App\Models\ServiceCostRequest::getServiceCategories()[$serviceRequest->service_category] ?? $serviceRequest->service_category }}
                                </span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Layanan Diminta</span>
                                <span class="info-value">{{ count($serviceRequest->services_requested ?? []) }} layanan</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Status</span>
                                <span class="info-value">
                                    <span class="status-badge" style="{{ $currentStatusConfig['badge_style'] }}">
                                        <i class="fas {{ $currentStatusConfig['badge_icon'] }}"></i>
                                        {{ $serviceRequest->status_label }}
                                    </span>
                                </span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Tanggal Pengajuan</span>
                                <span class="info-value">{{ $serviceRequest->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="mb-8">
                        <h3 class="font-bold section-title mb-4 flex items-center gap-2">
                            <i class="fas fa-route icon-secondary"></i>
                            Proses Selanjutnya
                        </h3>
                        
                        <div>
                            @foreach($timelineSteps as $step)
                                @php
                                    $stepOrder = $statusOrder[$step['key']] ?? 1;
                                    $isCompleted = $currentOrder > $stepOrder || ($currentStatus === 'accepted' && $step['key'] === 'accepted');
                                    $isActive = $currentStatus === $step['key'] || ($currentStatus === 'pending' && $step['key'] === 'reviewing') || (($currentStatus === 'rejected' || $currentStatus === 'cancelled') && $step['key'] === 'accepted');
                                    $iconState = $isCompleted ? 'completed' : ($isActive ? 'active' : 'pending');
                                @endphp
                                <div class="timeline-step {{ $isActive ? 'active' : '' }}">
                                    <div class="timeline-icon {{ $iconState }}">
                                        <i class="fas {{ $step['icon'] }} text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold" style="color: var(--text-primary);">{{ $step['title'] }}</h4>
                                        <p class="text-sm" style="color: var(--text-secondary);">{{ $step['description'] }}</p>
                                        @if($step['timestamp'])
                                            <p class="text-xs mt-1" style="color: var(--text-tertiary);">{{ $step['timestamp']->format('d M Y H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Info Box -->
                    <div class="info-box rounded-xl p-5 mb-8">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: var(--color-accent); color: white;">
                                <i class="fas fa-info"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1" style="color: var(--text-primary);">{{ $currentStatusConfig['info_title'] }}</h4>
                                {{-- SAFE: $statusConfig is a hardcoded PHP array in this template, not user-supplied --}}
                                <p class="text-sm" style="color: var(--text-secondary);">{!! $currentStatusConfig['info_body'] !!}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        @php
                            $whatsappNumber = config('landing_metrics.contact.whatsapp', '6283879602855');
                            $whatsappMessage = urlencode($currentStatusConfig['wa_text']);
                        @endphp
                        
                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMessage }}" target="_blank" class="cta-button cta-whatsapp w-full">
                            <i class="fab fa-whatsapp text-xl"></i>
                            Hubungi via WhatsApp
                        </a>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('landing.id') }}" class="cta-button cta-secondary">
                                <i class="fas fa-home"></i>
                                Beranda
                            </a>
                            <a href="{{ route('services.index.id') }}" class="cta-button cta-primary">
                                Lihat Layanan
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Info -->
            <div class="mt-8 text-center text-sm" style="color: var(--text-tertiary);">
                <p>Ada pertanyaan? Hubungi kami di</p>
                <p class="font-medium mt-1" style="color: var(--text-primary);">
                    <i class="fas fa-envelope mr-1"></i> info@bizmark.id
                    <span class="mx-2">|</span>
                    <i class="fas fa-phone mr-1"></i> +62 838 7960 2855
                </p>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    @include('landing.partials.footer')
    
    <!-- Scripts -->
    @include('landing.partials.scripts')
</body>
</html>
