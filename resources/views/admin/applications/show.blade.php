@extends('layouts.admin')

@section('title', 'Detail Lamaran - ' . $application->full_name)

@section('content')
<div class="max-w-screen-2xl mx-auto px-4 py-6 space-y-6">
    {{-- Header --}}
    <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <nav class="text-xs mb-2 flex items-center gap-2" style="color: rgba(235,235,245,0.6);">
                <a href="{{ route('admin.recruitment.index') }}" class="hover:text-apple-blue transition-colors">Rekrutmen</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <a href="{{ route('admin.applications.index') }}" class="hover:text-apple-blue transition-colors">Applications</a>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span style="color: rgba(235,235,245,0.9);">Detail Lamaran</span>
            </nav>
            <h1 class="text-2xl md:text-xl font-bold text-white mb-1">{{ $application->full_name }}</h1>
            <p class="text-sm" style="color: rgba(235,235,245,0.65);">
                <i class="fas fa-briefcase mr-2"></i>{{ $application->jobVacancy->title ?? 'N/A' }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.applications.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
            <a href="{{ route('admin.recruitment.pipeline.show', $application->id) }}" class="btn-primary">
                <i class="fas fa-stream mr-2"></i>Pipeline
            </a>
        </div>
    </section>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="rounded-apple-lg p-4" style="background: rgba(52,199,89,0.15); border: 1px solid rgba(52,199,89,0.3);">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-lg" style="color: rgba(52,199,89,1);"></i>
                <p class="text-white">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-apple-lg p-4" style="background: rgba(255,59,48,0.15); border: 1px solid rgba(255,59,48,0.3);">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-lg" style="color: rgba(255,59,48,1);"></i>
                <p class="text-white">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Personal Information --}}
            <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                <h2 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-user" style="color: rgba(10,132,255,1);"></i>
                    Data Pribadi
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-user mr-1"></i>Nama Lengkap
                        </p>
                        <p class="text-white font-semibold">{{ $application->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-envelope mr-1"></i>Email
                        </p>
                        <a href="mailto:{{ $application->email }}" class="text-apple-blue hover:underline">
                            {{ $application->email }}
                        </a>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-phone mr-1"></i>Nomor Telepon
                        </p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="tel:{{ $application->phone }}" class="text-apple-blue hover:underline">
                                {{ $application->phone }}
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->phone) }}" 
                               class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold transition-all"
                               style="background: rgba(37,211,102,0.2); color: rgba(37,211,102,1);"
                               target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-calendar-alt mr-1"></i>Tanggal Lahir
                        </p>
                        <p class="text-white">{{ $application->birth_date ? $application->birth_date->format('d M Y') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-venus-mars mr-1"></i>Jenis Kelamin
                        </p>
                        <p class="text-white">{{ $application->gender ?? '-' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-map-marker-alt mr-1"></i>Alamat
                        </p>
                        <p class="text-white">{{ $application->address ?? '-' }}</p>
                    </div>
                </div>
            </section>

            {{-- Education --}}
            <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                <h2 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-graduation-cap" style="color: rgba(255,149,0,1);"></i>
                    Pendidikan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-layer-group mr-1"></i>Jenjang
                        </p>
                        <p class="text-white font-semibold">{{ $application->education_level }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-book mr-1"></i>Jurusan
                        </p>
                        <p class="text-white font-semibold">{{ $application->major }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-university mr-1"></i>Institusi
                        </p>
                        <p class="text-white">{{ $application->institution }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-star mr-1"></i>IPK
                        </p>
                        <p class="text-white">{{ $application->gpa ? number_format($application->gpa, 2) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-calendar-check mr-1"></i>Tahun Lulus
                        </p>
                        <p class="text-white">{{ $application->graduation_year ?? '-' }}</p>
                    </div>
                </div>
            </section>

            {{-- Experience & Skills --}}
            <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                <h2 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                    <i class="fas fa-briefcase" style="color: rgba(175,82,222,1);"></i>
                    Pengalaman & Keahlian
                </h2>
                
                @if($application->has_experience_ukl_upl)
                    <div class="rounded-apple-lg p-3 mb-4" style="background: rgba(52,199,89,0.15); border: 1px solid rgba(52,199,89,0.3);">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-badge-check" style="color: rgba(52,199,89,1);"></i>
                            <span class="text-white font-semibold">Memiliki pengalaman UKL-UPL/Kajian Teknis</span>
                        </div>
                    </div>
                @endif

                @if($application->work_experience && count($application->work_experience) > 0)
                    <div class="mb-5">
                        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-history" style="color: rgba(10,132,255,1);"></i>
                            Riwayat Pekerjaan
                        </h3>
                        <div class="space-y-4">
                            @foreach($application->work_experience as $exp)
                                <div class="p-4 rounded-apple-lg" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                                    <p class="text-white font-semibold mb-1">{{ $exp['position'] ?? 'N/A' }}</p>
                                    <p class="text-sm mb-1" style="color: rgba(235,235,245,0.7);">{{ $exp['company'] ?? 'N/A' }}</p>
                                    <p class="text-xs mb-2" style="color: rgba(235,235,245,0.5);">{{ $exp['duration'] ?? 'N/A' }}</p>
                                    @if(!empty($exp['responsibilities']))
                                        <p class="text-sm text-white mt-2">{{ $exp['responsibilities'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm mb-5" style="color: rgba(235,235,245,0.6);">Belum ada pengalaman kerja tercatat.</p>
                @endif

                @if($application->skills && count($application->skills) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-tools" style="color: rgba(255,149,0,1);"></i>
                            Keahlian
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($application->skills as $skill)
                                <span class="px-3 py-1.5 rounded-full text-xs font-semibold" style="background: rgba(10,132,255,0.2); color: rgba(10,132,255,1);">
                                    <i class="fas fa-check-circle mr-1"></i>{{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($application->expected_salary || $application->available_from)
                    <div class="mt-5 pt-5" style="border-top: 1px solid rgba(58,58,60,0.6);">
                        <h3 class="text-sm font-semibold text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle" style="color: rgba(255,214,10,1);"></i>
                            Informasi Tambahan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($application->expected_salary)
                                <div>
                                    <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-money-bill-wave mr-1"></i>Ekspektasi Gaji
                                    </p>
                                    <p class="text-white font-semibold">Rp {{ number_format($application->expected_salary, 0, ',', '.') }}</p>
                                </div>
                            @endif
                            @if($application->available_from)
                                <div>
                                    <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                                        <i class="fas fa-calendar-day mr-1"></i>Bisa Mulai Kerja
                                    </p>
                                    <p class="text-white font-semibold">{{ $application->available_from->format('d M Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            {{-- Cover Letter --}}
            @if($application->cover_letter)
                <section class="card-elevated rounded-apple-xl p-5 md:p-6">
                    <h2 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt" style="color: rgba(52,199,89,1);"></i>
                        Surat Lamaran
                    </h2>
                    <p class="text-white leading-relaxed">{{ $application->cover_letter }}</p>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <section class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-base font-semibold text-white mb-4">Status Lamaran</h3>
                
                <div class="text-center mb-4">
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'rgba(255,214,10,0.2)', 'color' => 'rgba(255,214,10,1)', 'icon' => 'fas fa-clock'],
                            'reviewed' => ['bg' => 'rgba(10,132,255,0.2)', 'color' => 'rgba(10,132,255,1)', 'icon' => 'fas fa-eye'],
                            'interview' => ['bg' => 'rgba(175,82,222,0.2)', 'color' => 'rgba(175,82,222,1)', 'icon' => 'fas fa-users'],
                            'accepted' => ['bg' => 'rgba(52,199,89,0.2)', 'color' => 'rgba(52,199,89,1)', 'icon' => 'fas fa-check-circle'],
                            'rejected' => ['bg' => 'rgba(255,59,48,0.2)', 'color' => 'rgba(255,59,48,1)', 'icon' => 'fas fa-times-circle'],
                        ];
                        $config = $statusConfig[$application->status] ?? ['bg' => 'rgba(142,142,147,0.25)', 'color' => 'rgba(142,142,147,1)', 'icon' => 'fas fa-question'];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold" style="background: {{ $config['bg'] }}; color: {{ $config['color'] }};">
                        <i class="{{ $config['icon'] }}"></i>{{ ucfirst($application->status) }}
                    </span>
                </div>

                <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">Ubah Status</label>
                        <select name="status" class="w-full bg-dark-secondary border border-dark-border text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-apple-blue" required>
                            <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Direview</option>
                            <option value="interview" {{ $application->status === 'interview' ? 'selected' : '' }}>Interview</option>
                            <option value="accepted" {{ $application->status === 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-white mb-2">
                            <i class="fas fa-comment-alt mr-1"></i>Catatan
                        </label>
                        <textarea name="notes" rows="4" class="w-full bg-dark-secondary border border-dark-border text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-apple-blue" placeholder="Catatan akan dikirim ke pelamar via email...">{{ $application->notes }}</textarea>
                        <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">
                            <i class="fas fa-envelope mr-1"></i>Email notifikasi otomatis terkirim
                        </p>
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-paper-plane mr-2"></i>Update & Kirim Email
                    </button>
                </form>

                @if($application->reviewed_at)
                    <div class="mt-4 pt-4" style="border-top: 1px solid rgba(58,58,60,0.6);">
                        <p class="text-xs" style="color: rgba(235,235,245,0.5);">
                            <i class="fas fa-calendar mr-1"></i>Direview: {{ $application->reviewed_at->format('d M Y H:i') }}
                        </p>
                        @if($application->reviewer)
                            <p class="text-xs mt-1" style="color: rgba(235,235,245,0.5);">
                                <i class="fas fa-user mr-1"></i>Oleh: {{ $application->reviewer->name }}
                            </p>
                        @endif
                    </div>
                @endif
            </section>

            {{-- Documents --}}
            <section class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-folder-open" style="color: rgba(255,149,0,1);"></i>
                    Dokumen
                </h3>
                
                <div class="space-y-2">
                    @if($application->cv_path)
                        <a href="{{ route('admin.applications.download-cv', $application->id) }}" 
                           class="btn-primary w-full">
                            <i class="fas fa-file-pdf mr-2"></i>Download CV
                        </a>
                    @else
                        <div class="rounded-apple-lg p-3 text-center" style="background: rgba(255,214,10,0.15); border: 1px solid rgba(255,214,10,0.3);">
                            <p class="text-xs" style="color: rgba(255,214,10,1);">
                                <i class="fas fa-exclamation-triangle mr-1"></i>CV tidak tersedia
                            </p>
                        </div>
                    @endif

                    @if($application->portfolio_path)
                        <a href="{{ route('admin.applications.download-portfolio', $application->id) }}" 
                           class="btn-secondary w-full">
                            <i class="fas fa-folder mr-2"></i>Download Portfolio
                        </a>
                    @endif
                </div>
            </section>

            {{-- Job Info --}}
            <section class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-briefcase" style="color: rgba(10,132,255,1);"></i>
                    Info Lowongan
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-tag mr-1"></i>Posisi
                        </p>
                        <p class="text-white font-semibold">{{ $application->jobVacancy->title ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest mb-1" style="color: rgba(235,235,245,0.6);">
                            <i class="fas fa-clock mr-1"></i>Tanggal Lamar
                        </p>
                        <p class="text-white">{{ $application->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($application->jobVacancy)
                        <a href="{{ route('career.show', $application->jobVacancy->slug) }}" 
                           class="btn-secondary w-full text-center" target="_blank">
                            <i class="fas fa-external-link-alt mr-2"></i>Lihat Lowongan
                        </a>
                    @endif
                </div>
            </section>

            {{-- Actions --}}
            <section class="card-elevated rounded-apple-xl p-5">
                <h3 class="text-base font-semibold text-white mb-4 flex items-center gap-2">
                    <i class="fas fa-cog" style="color: rgba(142,142,147,1);"></i>
                    Aksi
                </h3>
                <form action="{{ route('admin.applications.destroy', $application->id) }}"
                      method="POST"
                      x-data @submit.prevent="if(confirm('Yakin ingin menghapus lamaran ini? Data test, interview, dan file CV/Portfolio akan terhapus.')) $el.submit()">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-secondary w-full" style="background: rgba(255,59,48,0.15); color: rgba(255,59,48,1); border-color: rgba(255,59,48,0.3);">
                        <i class="fas fa-trash-alt mr-2"></i>Hapus Lamaran
                    </button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
