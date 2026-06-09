<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-10">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Layanan Utama</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Perizinan dikelola penuh. Dijamin para ahli.
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Enam kategori perizinan tersedia. Satu tim khusus untuk setiap proyek Anda. Komitmen SLA yang jelas sejak hari pertama.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
            @php
                $servicesList = [
                    ['title' => 'Izin Limbah B3', 'slug' => 'perizinan-lb3', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'items' => ['Izin TPS LB3', 'Manifest angkut', 'Pemulihan / pembuangan']],
                    ['title' => 'Lingkungan', 'slug' => 'amdal', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'items' => ['Studi AMDAL lengkap', 'Dokumen UKL-UPL', 'SPPL']],
                    ['title' => 'Perizinan Gedung', 'slug' => 'pbg-slf', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'items' => ['Penerbitan PBG', 'Sertifikasi SLF', 'Konversi IMB']],
                    ['title' => 'Izin Usaha', 'slug' => 'oss-nib', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'items' => ['Registrasi NIB', 'Setup OSS-RBA', 'SIUP · API']],
                    ['title' => 'PMA / Investasi Asing', 'slug' => 'pma-investasi-asing', 'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9', 'items' => ['Registrasi BKPM', 'Izin sektoral', 'Dukungan bilingual']],
                    ['title' => 'Operasional', 'slug' => 'izin-operasional', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 'items' => ['Izin industri', 'Logistik · K3', 'Audit kepatuhan']],
                ];
            @endphp

            @foreach($servicesList as $svc)
            <a href="/layanan/{{ $svc['slug'] }}" class="group bg-white rounded-xl border border-[#E5E0DB] p-5 hover:border-[#0D9488]/20 hover:shadow-sm transition-all duration-200">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-9 h-9 rounded-lg bg-[#CCFBF1] flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-[#0D9488]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $svc['icon'] }}"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-[#CCFBF1] text-[#115E59]">SLA aktif</span>
                </div>
                <h3 class="text-base font-bold text-[#2D2A24] group-hover:text-[#0D9488] transition-colors mb-2">{{ $svc['title'] }}</h3>
                <ul class="space-y-1.5 mb-3">
                    @foreach($svc['items'] as $item)
                    <li class="flex items-center gap-2 text-xs text-[#6B6560]">
                        <svg class="w-3 h-3 text-[#0D9488] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>
                <div class="flex items-center gap-1.5 text-xs font-semibold text-[#0D9488] group-hover:text-[#0F766E] transition-colors">
                    Detail layanan
                    <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="/layanan" class="inline-flex items-center gap-2 px-5 py-2.5 border border-[#E5E0DB] text-sm font-semibold text-[#6B6560] rounded-xl hover:border-[#0D9488]/30 hover:text-[#0D9488] transition-all duration-200">
                Lihat semua 20+ layanan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>
    </div>
</section>
