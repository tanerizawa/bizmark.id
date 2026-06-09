@extends('landing.layout')

@section('title', 'Cek Kebutuhan Izin Usaha — Gratis | Bizmark.ID')
@section('meta_description', 'Cek kebutuhan perizinan usaha Anda secara gratis menggunakan AI Bizmark.ID. Hasil dalam 30 detik, akurat, dan tanpa biaya apa pun.')

@section('content')
<div class="inquiry-page font-sans bg-white text-gray-900 min-h-screen flex flex-col">
    @php
        $contact = config('landing_metrics.contact');
        $experience = config('landing_metrics.experience');
        $expYears = $experience['years'] ?? ((int) date('Y') - 2014);
        $benefits = [
            'AI kami memetakan izin yang Anda butuhkan hanya dalam 30 detik',
            'Tim konsultan senior memvalidasi hasil sebelum dikirim ke Anda',
            'Mencakup rekomendasi estimasi waktu, instansi terkait, dan biaya'
        ];
        $documentTips = [
            'OSS RBA (NIB, NIB Perizinan Berusaha)',
            'UKL-UPL / AMDAL & perizinan lingkungan',
            'PBG, SLF, TDG, penetapan KBLI, dan izin sektoral lain'
        ];
    @endphp

    {{-- Navbar & mobile menu disediakan oleh landing.layout --}}

    <div x-data="inquiryForm()" x-init="init()" x-cloak>

    <!-- Hero Section -->
    <section class="pt-24 pb-12 sm:pt-28 sm:pb-16 bg-gradient-to-b from-[#FDFBF8] via-[#FDFBF8] to-[#F5F3F8] bg-mesh relative overflow-hidden">
        <!-- Decorative circles -->
        <div class="absolute -top-16 -right-16 w-64 h-64 bg-primary-100/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-accent-400/10 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-600 rounded-full text-sm font-semibold mb-6 animate-fade-in border border-primary-100">
                <i class="fas fa-bolt"></i>
                <span>AI-Powered &middot; Gratis &middot; 30 Detik</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight animate-fade-in delay-100 leading-[1.15]">
                Cek Kebutuhan Izin Usaha
                <span class="block mt-1 bg-gradient-to-r from-[#0A66C2] to-[#00A0DC] bg-clip-text text-transparent">Berbasis AI — Gratis</span>
            </h1>
            <p class="text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto mb-8 animate-fade-in delay-200 leading-relaxed">
                Jawab beberapa pertanyaan singkat, dan AI kami akan memetakan <strong class="text-gray-700">semua izin yang Anda butuhkan</strong> lengkap dengan estimasi waktu, biaya, dan instansi yang bertanggung jawab.
            </p>

            <!-- Trust row -->
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-gray-400 mb-10 animate-fade-in delay-300">
                <span class="flex items-center gap-1.5"><i class="fas fa-shield-alt text-primary-500"></i> Data Terenkripsi</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-clock text-primary-500"></i> Hasil 30 Detik</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-globe text-primary-500"></i> Bilingual ID / EN</span>
                <span class="flex items-center gap-1.5"><i class="fas fa-star text-accent-500"></i> {{ $expYears }}+ Tahun Sejak 2014</span>
            </div>

            <!-- Progress Bar -->
            <div class="max-w-md mx-auto animate-fade-in delay-400">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-600">
                        Langkah <span x-text="step" class="text-primary-500 font-bold"></span> dari 2
                    </span>
                    <span class="text-sm font-bold text-primary-500" x-text="Math.round(progress) + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-500 ease-out"
                         class="bg-gradient-to-r from-[#0A66C2] to-[#00A0DC]"
                         :style="'width: ' + progress + '%'"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-10 sm:py-14 bg-gradient-to-b from-[#F5F3F8] to-white bg-mesh">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto grid lg:grid-cols-3 gap-8 items-start">
                
                <!-- Sidebar -->
                <aside class="space-y-6 lg:sticky lg:top-24 order-2 lg:order-1">
                    <!-- Benefits Card -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-soft card-hover card-accent-bar animate-fade-in">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-primary-50 text-primary-500">
                                <i class="fas fa-check-circle text-lg"></i>
                            </span>
                            Kenapa Analisis Ini?
                        </h3>
                        <ul class="space-y-3 text-sm text-gray-600">
                            @foreach($benefits as $benefit)
                                <li class="flex items-start gap-3">
                                    <span class="w-5 h-5 mt-0.5 rounded-full bg-primary-50 text-primary-500 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </span>
                                    <span>{{ $benefit }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-5 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100/50 border border-primary-100 px-4 py-3 text-sm text-primary-700">
                            <i class="fas fa-award mr-1"></i>
                            <strong>{{ $experience['years'] ?? '10+' }} tahun</strong> lintas industri &bull; <strong>{{ $contact['hours'] ?? 'Portal 24/7' }}</strong>
                        </div>
                    </div>

                    <!-- CTA Card -->
                    <div class="bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 text-white rounded-2xl p-6 shadow-soft-lg card-hover animate-fade-in delay-100 relative overflow-hidden">
                        <div class="absolute -top-8 -right-8 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
                        <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/5 rounded-full blur-lg"></div>
                        <div class="relative z-10">
                            <p class="text-xs uppercase tracking-[0.2em] text-white/60 mb-1.5 font-semibold">Konsultan Siaga</p>
                            <h3 class="text-xl font-bold mb-2">Butuh Jawaban Cepat?</h3>
                            <p class="text-white/75 text-sm mb-5 leading-relaxed">Tim kami siap merespons dalam 5 menit melalui WhatsApp.</p>
                            <div class="space-y-2.5">
                                <a href="{{ $contact['whatsapp_link'] ?? 'https://wa.me/6283879602855' }}" target="_blank" rel="noopener noreferrer"
                                   class="flex items-center justify-center gap-2.5 bg-white text-primary-600 font-semibold rounded-xl py-3 shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all text-sm">
                                    <i class="fab fa-whatsapp text-green-500" aria-hidden="true"></i> Chat WhatsApp
                                </a>
                                <a href="tel:{{ $contact['phone'] ?? '+6283879602855' }}"
                                   class="flex items-center justify-center gap-2.5 bg-white/10 border border-white/20 text-white font-semibold rounded-xl py-3 hover:bg-white/20 transition-all text-sm backdrop-blur-sm">
                                    <i class="fas fa-phone-alt text-sm flex-shrink-0 leading-none" aria-hidden="true"></i> {{ $contact['phone_display'] ?? 'Hubungi Kami' }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Document Tips -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-soft card-hover card-accent-bar animate-fade-in delay-200">
                        <h4 class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400 mb-4">
                            <i class="fas fa-lightbulb text-accent-500 mr-1"></i> Cakupan Analisis
                        </h4>
                        <ul class="space-y-2.5 text-sm text-gray-600">
                            @foreach($documentTips as $tip)
                                <li class="flex items-start gap-2.5">
                                    <i class="fas fa-folder-open text-primary-400 mt-0.5 text-xs"></i>
                                    <span>{{ $tip }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 flex items-center gap-2 text-xs text-gray-400">
                            <i class="fas fa-lock text-xs"></i>
                            Data disimpan terenkripsi & hanya untuk asesmen awal.
                        </div>
                    </div>
                    
                    <!-- Recommendation Box -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-5 shadow-soft animate-fade-in delay-300">
                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-compass text-primary-500"></i>
                            Pilih Layanan yang Tepat
                        </h4>
                        
                        <div class="space-y-3">
                            <!-- This page recommendation -->
                            <div class="p-3 rounded-xl bg-primary-50 border border-primary-100">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-lg bg-primary-500 text-white flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold text-primary-600">Anda di halaman yang tepat jika:</p>
                                        <ul class="text-[11px] text-gray-600 mt-1 space-y-0.5">
                                            <li>• Belum yakin <strong>izin apa</strong> yang dibutuhkan</li>
                                            <li>• Ingin AI menganalisis kebutuhan perizinan</li>
                                            <li>• Butuh rekomendasi instansi & timeline</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Alternative recommendation -->
                            <div class="p-3 rounded-xl bg-orange-50 border border-orange-100">
                                <div class="flex items-start gap-2.5">
                                    <span class="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-calculator text-xs"></i>
                                    </span>
                                    <div>
                                        <p class="text-xs font-semibold text-orange-600">Gunakan Estimasi Biaya jika:</p>
                                        <ul class="text-[11px] text-gray-600 mt-1 space-y-0.5">
                                            <li>• Sudah tahu <strong>KBLI / jenis usaha</strong></li>
                                            <li>• Ingin hitung <strong>berapa biaya</strong> perizinan</li>
                                            <li>• Butuh estimasi cepat untuk budgeting</li>
                                        </ul>
                                        <a href="/estimasi-biaya" class="inline-flex items-center gap-1.5 mt-2 text-xs font-semibold text-orange-600 hover:text-orange-700 transition-colors">
                                            Estimasi Biaya <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Form Container -->
                <div class="lg:col-span-2 space-y-6 order-1 lg:order-2">

                    <!-- Hero search context banner — appears when user came from /?q=... -->
                    @if(request()->filled('q'))
                        <div class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100/40 px-5 py-4 shadow-soft animate-fade-in" role="status">
                            <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center">
                                    <i class="fas fa-magnifying-glass text-sm"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold uppercase tracking-[.18em] text-emerald-700 mb-1">
                                        Pencarian Anda
                                    </p>
                                    <p class="text-sm text-gray-700 mb-1">
                                        <span class="font-semibold text-gray-900">"{{ \Illuminate\Support\Str::limit(request('q'), 120) }}"</span>
                                        sudah kami catat di Tahap 2.
                                    </p>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        AI butuh info kontak singkat dulu (Tahap 1, ±30 detik) supaya hasil analisis bisa dikirim langsung ke email Anda. Tidak ada pendaftaran akun.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form @submit.prevent="submitForm" @input.debounce.500ms="saveDraft()" class="bg-white rounded-2xl shadow-soft-lg border border-gray-100 overflow-hidden card-accent-bar">
                    
                    <!-- Step 1: Contact & Company Info -->
                    <div x-show="step === 1" x-transition role="tabpanel" aria-label="Informasi Kontak" class="p-6 sm:p-8">
                        <div class="mb-8">
                            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 flex items-center gap-3 tracking-tight">
                                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-primary-500 text-white text-sm font-bold shadow-soft">1</span>
                                Informasi Kontak & Perusahaan
                            </h2>
                            <p class="text-gray-500 ml-12">Kami butuh info ini untuk mengirim hasil analisis ke Anda.</p>
                        </div>

                        <!-- Draft Restored Notice -->
                        <div x-show="draftLoaded || lastSavedAt" class="mb-6 rounded-xl border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-800 flex flex-col gap-2 backdrop-blur-sm">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-info-circle mt-0.5"></i>
                                <div>
                                    <p>Data sebelumnya dipulihkan otomatis.</p>
                                    <p x-show="lastSavedAt" class="text-xs text-amber-600 mt-0.5" x-text="'Terakhir tersimpan: ' + savedAtLabel"></p>
                                </div>
                            </div>
                            <div class="flex items-center justify-end">
                                <button type="button" @click="clearDraft" class="text-xs font-semibold text-amber-700 hover:text-amber-900 underline underline-offset-2">Reset Form</button>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-envelope text-sm"></i></span>
                                    <input type="email" x-model="formData.email" @blur="checkRateLimit" required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="email@perusahaan.com">
                                </div>
                                <p x-show="rateLimitWarning" x-text="rateLimitWarning" class="mt-1.5 text-sm text-amber-600" role="status" aria-live="polite"></p>
                            </div>

                            <!-- Company Name -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Perusahaan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-building text-sm"></i></span>
                                    <input type="text" x-model="formData.company_name" required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="PT/CV/Nama Perusahaan Anda">
                                </div>
                            </div>

                            <!-- Company Type -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Badan Usaha</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-briefcase text-sm"></i></span>
                                    <select x-model="formData.company_type" class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm appearance-none bg-white">
                                        <option value="">Pilih jenis badan usaha...</option>
                                        <option value="PT">PT (Perseroan Terbatas)</option>
                                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                                        <option value="Individual">Perorangan</option>
                                        <option value="Koperasi">Koperasi</option>
                                        <option value="Yayasan">Yayasan</option>
                                        <option value="Belum Terdaftar">Belum Terdaftar</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon (WhatsApp) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fab fa-whatsapp text-sm"></i></span>
                                    <input type="tel" x-model="formData.phone" required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="08xx-xxxx-xxxx atau +62xxx">
                                </div>
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kontak Person <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-user text-sm"></i></span>
                                    <input type="text" x-model="formData.contact_person" required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="Nama lengkap Anda">
                                </div>
                            </div>

                            <!-- Position -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jabatan</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-id-badge text-sm"></i></span>
                                    <input type="text" x-model="formData.position"
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="Direktur, Owner, Manager, dll">
                                </div>
                            </div>
                        </div>

                        <!-- Next Button -->
                        <div class="mt-8 flex gap-4">
                            <a href="{{ route('landing.id') }}" 
                               class="flex-1 px-6 py-3.5 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition text-center text-sm">
                                <i class="fas fa-arrow-left mr-1.5"></i> Kembali
                            </a>
                            <button type="button" @click="nextStep" :disabled="!isStep1Valid"
                                    :class="isStep1Valid ? 'bg-primary-500 hover:bg-primary-600 btn-lift' : 'bg-gray-200 cursor-not-allowed text-gray-400'"
                                    class="flex-1 px-6 py-3.5 text-white font-semibold rounded-xl transition-all text-sm">
                                Lanjut <i class="fas fa-arrow-right ml-1.5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Business Info -->
                    <div x-show="step === 2" x-transition role="tabpanel" aria-label="Informasi Usaha" class="p-6 sm:p-8">
                        <div class="mb-8">
                            <h2 class="text-2xl font-extrabold text-gray-900 mb-2 flex items-center gap-3 tracking-tight">
                                <span class="flex items-center justify-center w-9 h-9 rounded-xl bg-primary-500 text-white text-sm font-bold shadow-soft">2</span>
                                Informasi Usaha & Proyek
                            </h2>
                            <p class="text-gray-500 ml-12">Ceritakan tentang usaha Anda agar AI memberikan rekomendasi terbaik.</p>
                        </div>

                        <div class="space-y-5">
                            <!-- Business Activity -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Usaha / Aktivitas Bisnis <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute top-3 left-4 text-gray-400"><i class="fas fa-industry text-sm"></i></span>
                                    <textarea x-model="formData.business_activity" required rows="3" maxlength="1000"
                                              class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition resize-none text-sm"
                                              placeholder="Contoh: Produksi makanan ringan, Cafe & Restoran, Jasa Konstruksi, dll"></textarea>
                                </div>
                                <p class="mt-1 text-xs text-gray-400" x-text="formData.business_activity.length + '/1000 karakter'"></p>
                            </div>

                            <!-- Business Scale -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Skala Usaha <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <template x-for="scale in [{v:'micro',l:'Mikro',d:'< 10 karyawan',i:'fas fa-seedling'},{v:'small',l:'Kecil',d:'10-50 karyawan',i:'fas fa-store'},{v:'medium',l:'Menengah',d:'50-100 karyawan',i:'fas fa-building'},{v:'large',l:'Besar',d:'> 100 karyawan',i:'fas fa-city'}]" :key="scale.v">
                                        <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                               :class="formData.business_scale === scale.v ? 'border-primary-500 bg-primary-50 radio-card-active' : 'border-gray-200 hover:border-primary-300 hover:bg-gray-50'">
                                            <input type="radio" x-model="formData.business_scale" :value="scale.v" class="sr-only" required>
                                            <span class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                  :class="formData.business_scale === scale.v ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-400'">
                                                <i :class="scale.i" class="text-sm"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-gray-900 text-sm" x-text="scale.l"></div>
                                                <div class="text-xs text-gray-500" x-text="scale.d"></div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Location Province -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Provinsi Lokasi Usaha <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-map-marker-alt text-sm"></i></span>
                                    <select x-model="formData.location_province" required
                                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm appearance-none bg-white">
                                        <option value="">Pilih provinsi...</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province }}">{{ $province }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Location City -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-city text-sm"></i></span>
                                    <input type="text" x-model="formData.location_city" required
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="Nama kota/kabupaten">
                                </div>
                            </div>

                            <!-- Location Category -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori Lokasi <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-3">
                                    <template x-for="loc in [{v:'industrial',l:'Kawasan Industri',i:'fas fa-industry'},{v:'commercial',l:'Area Komersial',i:'fas fa-store-alt'},{v:'residential',l:'Area Residensial',i:'fas fa-home'},{v:'rural',l:'Pedesaan',i:'fas fa-tree'}]" :key="loc.v">
                                        <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all"
                                               :class="formData.location_category === loc.v ? 'border-primary-500 bg-primary-50 radio-card-active' : 'border-gray-200 hover:border-primary-300 hover:bg-gray-50'">
                                            <input type="radio" x-model="formData.location_category" :value="loc.v" class="sr-only" required>
                                            <span class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                  :class="formData.location_category === loc.v ? 'bg-primary-500 text-white' : 'bg-gray-100 text-gray-400'">
                                                <i :class="loc.i" class="text-sm"></i>
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-semibold text-gray-900 text-sm" x-text="loc.l"></div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Estimated Investment -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Estimasi Investasi <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-coins text-sm"></i></span>
                                    <select x-model="formData.estimated_investment" required
                                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm appearance-none bg-white">
                                        <option value="">Pilih range investasi...</option>
                                        <option value="under_100m">< Rp 100 juta</option>
                                        <option value="100m_500m">Rp 100 - 500 juta</option>
                                        <option value="500m_2b">Rp 500 juta - 2 miliar</option>
                                        <option value="over_2b">> Rp 2 miliar</option>
                                    </select>
                                </div>
                            </div>

                            <!-- KBLI Code -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode KBLI <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-hashtag text-sm"></i></span>
                                    <input type="text" x-model="formData.kbli_code"
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm"
                                           placeholder="Contoh: 10710, 56101">
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Jika belum tahu, biarkan kosong — AI akan menyarankan.</p>
                            </div>

                            <!-- Timeline -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Target Timeline</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400"><i class="fas fa-calendar-alt text-sm"></i></span>
                                    <select x-model="formData.timeline"
                                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition text-sm appearance-none bg-white">
                                        <option value="">Pilih timeline...</option>
                                        <option value="urgent">Urgent (< 1 bulan)</option>
                                        <option value="1-3_months">1-3 bulan</option>
                                        <option value="3-6_months">3-6 bulan</option>
                                        <option value="6plus_months">> 6 bulan</option>
                                        <option value="not_sure">Belum pasti</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additional Notes -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan Tambahan <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <textarea x-model="formData.additional_notes" rows="3" maxlength="2000"
                                          class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-transparent transition resize-none text-sm"
                                          placeholder="Informasi tambahan yang perlu kami ketahui..."></textarea>
                                <p class="mt-1 text-xs text-gray-400" x-text="formData.additional_notes.length + '/2000 karakter'"></p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-8 flex gap-4">
                            <button type="button" @click="prevStep"
                                    class="flex-1 px-6 py-3.5 border-2 border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition text-sm">
                                <i class="fas fa-arrow-left mr-1.5"></i> Kembali
                            </button>
                            <button type="submit" :disabled="isSubmitting || !isStep2Valid"
                                    :class="(isSubmitting || !isStep2Valid) ? 'bg-gray-200 cursor-not-allowed text-gray-400' : 'bg-primary-500 hover:bg-primary-600 btn-lift'"
                                    class="flex-1 px-6 py-3.5 text-white font-semibold rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                                <span x-show="!isSubmitting"><i class="fas fa-robot mr-1"></i> Analisis dengan AI</span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>

                        <p x-show="errorMessage" x-text="errorMessage" 
                           class="mt-4 text-sm text-red-600 text-center bg-red-50 border border-red-200 rounded-xl px-4 py-3" 
                           role="alert" aria-live="assertive"></p>
                    </div>

                    </form>

                    <!-- Trust Indicators -->
                    <div class="text-center animate-fade-in delay-400">
                        <div class="inline-flex flex-wrap items-center justify-center gap-4 text-sm text-gray-500 mb-3">
                            <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                                <i class="fas fa-gift text-primary-500"></i> 100% Gratis
                            </span>
                            <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                                <i class="fas fa-lock text-primary-500"></i> Data Aman
                            </span>
                            <span class="flex items-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-100">
                                <i class="fas fa-bolt text-accent-500"></i> Hasil 30 Detik
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">
                            Dengan mengirim form ini, Anda setuju dengan 
                            <a href="{{ route('privacy.policy.id') }}" class="text-primary-500 hover:underline" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a> dan 
                            <a href="{{ route('terms.conditions.id') }}" class="text-primary-500 hover:underline" target="_blank" rel="noopener noreferrer">Syarat & Ketentuan</a> kami.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Loading Overlay -->
    <div x-show="isSubmitting" x-cloak x-transition.opacity.duration.300ms
         class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-soft-xl animate-fade-in">
            <div class="relative w-16 h-16 mx-auto mb-5">
                <svg class="animate-spin w-full h-full text-primary-500" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">AI Sedang Menganalisis...</h3>
            <p class="text-gray-500 text-sm mb-5">Memproses data Anda dan menyiapkan rekomendasi perizinan.</p>
            <div class="flex items-center justify-center gap-1.5">
                <div class="w-2 h-2 bg-primary-500 rounded-full animate-bounce [animation-delay:0ms]"></div>
                <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:150ms]"></div>
                <div class="w-2 h-2 bg-primary-300 rounded-full animate-bounce [animation-delay:300ms]"></div>
            </div>
            <p class="text-xs text-gray-400 mt-4">Estimasi: 10-30 detik</p>
        </div>
    </div>

    </div>{{-- /x-data inquiryForm --}}

</div>{{-- /inquiry-page --}}
@endsection

@push('scripts')
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        function inquiryForm() {
            return {
                step: 1, isSubmitting: false, rateLimitWarning: '', draftLoaded: false,
                lastSavedAt: null, errorMessage: '', storageKey: 'bizmark_inquiry_draft',
                formData: {
                    email: '', company_name: '', company_type: '', phone: '', contact_person: '', position: '',
                    business_activity: '', kbli_code: '', business_scale: '', location_province: '', location_city: '',
                    location_category: '', estimated_investment: '', timeline: '', additional_notes: '',
                    utm_source: urlParams.get('utm_source') || '', utm_medium: urlParams.get('utm_medium') || '', utm_campaign: urlParams.get('utm_campaign') || '',
                },
                init() {
                    this.loadDraft();
                    // Prefill from hero quick-check (?q=...) or estimator handoff (?kbli=...)
                    const heroQuery = urlParams.get('q');
                    const kbliCode = urlParams.get('kbli');
                    if (heroQuery && !this.formData.business_activity) {
                        this.formData.business_activity = heroQuery.slice(0, 500);
                    }
                    if (kbliCode && !this.formData.kbli_code) {
                        this.formData.kbli_code = kbliCode.slice(0, 20);
                    }
                },
                get progress() { return this.step === 1 ? 50 : 100; },
                get savedAtLabel() {
                    if (!this.lastSavedAt) return '';
                    try { return new Date(this.lastSavedAt).toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', day: 'numeric', month: 'short' }); }
                    catch (e) { return ''; }
                },
                get isStep1Valid() { return this.formData.email && this.formData.company_name && this.formData.phone && this.formData.contact_person; },
                get isStep2Valid() { return this.formData.business_activity && this.formData.business_scale && this.formData.location_province && this.formData.location_city && this.formData.location_category && this.formData.estimated_investment; },
                nextStep() { if (this.isStep1Valid) { this.step = 2; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                prevStep() { this.step = 1; window.scrollTo({ top: 0, behavior: 'smooth' }); },
                getDefaultFormData() {
                    return { email: '', company_name: '', company_type: '', phone: '', contact_person: '', position: '',
                        business_activity: '', kbli_code: '', business_scale: '', location_province: '', location_city: '',
                        location_category: '', estimated_investment: '', timeline: '', additional_notes: '',
                        utm_source: urlParams.get('utm_source') || '', utm_medium: urlParams.get('utm_medium') || '', utm_campaign: urlParams.get('utm_campaign') || '' };
                },
                saveDraft() {
                    const payload = { ...this.formData, _savedAt: new Date().toISOString() };
                    localStorage.setItem(this.storageKey, JSON.stringify(payload));
                    this.lastSavedAt = payload._savedAt;
                },
                loadDraft() {
                    const saved = localStorage.getItem(this.storageKey);
                    if (!saved) return;
                    try { const parsed = JSON.parse(saved); const { _savedAt, ...data } = parsed; this.formData = { ...this.formData, ...data }; this.lastSavedAt = _savedAt || null; this.draftLoaded = true; }
                    catch (error) { console.warn('Failed to load draft', error); }
                },
                async parseResponseSafely(response) {
                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        return {
                            success: false,
                            message: response.status >= 500
                                ? 'Server sedang mengalami gangguan. Silakan coba lagi.'
                                : 'Respons server tidak valid. Silakan coba lagi.'
                        };
                    }
                    try {
                        return await response.json();
                    } catch (error) {
                        return { success: false, message: 'Gagal membaca respons server.' };
                    }
                },
                clearDraft(resetFields = true) {
                    localStorage.removeItem(this.storageKey); this.draftLoaded = false; this.lastSavedAt = null; this.errorMessage = ''; this.rateLimitWarning = '';
                    if (resetFields) { this.formData = this.getDefaultFormData(); this.step = 1; }
                },
                async checkRateLimit() {
                    if (!this.formData.email) { this.rateLimitWarning = ''; return; }
                    const email = String(this.formData.email).trim();
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        this.rateLimitWarning = '';
                        return;
                    }
                    try {
                        const response = await fetch('{{ route("landing.service-inquiry.check-rate-limit") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ email }) });
                        const data = await this.parseResponseSafely(response);

                        if (!response.ok) {
                            this.rateLimitWarning = data.message || 'Gagal mengecek batas penggunaan.';
                            return;
                        }

                        if (!data.allowed) { this.rateLimitWarning = data.limit_info?.message || 'Batas analisis harian telah tercapai.'; }
                        else if ((data.stats?.email_remaining ?? 0) <= 2) { this.rateLimitWarning = 'Anda memiliki ' + (data.stats?.email_remaining ?? 0) + ' analisis gratis tersisa hari ini.'; }
                        else { this.rateLimitWarning = ''; }
                    } catch (error) { console.error('Rate limit check failed:', error); }
                },
                async submitForm() {
                    if (!this.isStep2Valid) return;
                    this.isSubmitting = true; this.errorMessage = '';
                    try {
                        const response = await fetch('{{ route("landing.service-inquiry.store") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify(this.formData) });
                        const data = await this.parseResponseSafely(response);

                        if (!response.ok) {
                            if (response.status === 429 || data.error === 'rate_limit') {
                                this.errorMessage = data.message || 'Batas analisis gratis tercapai untuk hari ini.';
                            } else if (response.status === 422 && data.errors) {
                                const firstKey = Object.keys(data.errors)[0];
                                const firstMessage = firstKey ? data.errors[firstKey]?.[0] : null;
                                this.errorMessage = firstMessage || data.message || 'Data belum lengkap. Mohon periksa kembali.';
                            } else {
                                this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                            }
                            this.isSubmitting = false;
                            return;
                        }

                        if (data.success) { this.clearDraft(false); this.pollResult(data.inquiry_number); }
                        else { this.errorMessage = data.message || 'Terjadi kesalahan. Silakan coba lagi.'; this.isSubmitting = false; }
                    } catch (error) { console.error('Submit error:', error); this.errorMessage = 'Gagal mengirim data. Periksa koneksi internet Anda.'; this.isSubmitting = false; }
                },
                async pollResult(inquiryNumber, attempts = 0) {
                    const maxAttempts = 30;
                    if (attempts >= maxAttempts) { this.errorMessage = 'Analisis membutuhkan waktu lebih lama dari biasanya. Hasil akan dikirim ke email Anda.'; this.isSubmitting = false; return; }
                    try {
                        const response = await fetch('/konsultasi-gratis/api/status/' + inquiryNumber);
                        const data = await response.json();
                        if (data.status === 'completed') { window.location.href = '/konsultasi-gratis/hasil/' + inquiryNumber; }
                        else if (data.status === 'error') { this.isSubmitting = false; if (data.analysis) { window.location.href = '/konsultasi-gratis/hasil/' + inquiryNumber; } else { this.errorMessage = data.message || 'Terjadi kendala dalam analisis. Hasil estimasi akan dikirim ke email Anda.'; } }
                        else { setTimeout(() => this.pollResult(inquiryNumber, attempts + 1), 2000); }
                    } catch (error) { console.error('Poll error:', error); const delay = Math.min(2000 * (1 + attempts * 0.2), 5000); setTimeout(() => this.pollResult(inquiryNumber, attempts + 1), delay); }
                }
            }
        }
    </script>
@endpush
