<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">Pertanyaan Umum</h2>
            <p class="text-base text-[#6B6560]">Masih ragu? <a href="/contact" class="text-[#0D9488] hover:text-[#0F766E] font-semibold">Hubungi tim kami</a>.</p>
        </div>

        @php
            $faqItems = [
                ['q' => 'Berapa lama proses pengurusan perizinan?', 'a' => 'Waktu bervariasi. OSS (NIB) 1-3 hari kerja, UKL-UPL 14-30 hari kerja, AMDAL 3-6 bulan. Tim kami berikan estimasi setelah konsultasi awal.'],
                ['q' => 'Apa saja dokumen yang perlu disiapkan?', 'a' => 'KTP/NPWP Direktur, Akta Pendirian, SK Kemenkumham, NPWP Perusahaan, dan dokumen teknis sesuai jenis izin.'],
                ['q' => 'Bagaimana skema pembayaran?', 'a' => 'Pembayaran bertahap: 50% saat mulai, 50% ketika izin terbit. Skema dapat disesuaikan untuk proyek besar.'],
                ['q' => 'Apakah ada jaminan jika izin tidak berhasil?', 'a' => 'Ya. Jika kegagalan dari sisi kami, biaya dikembalikan sesuai kesepakatan. Tingkat keberhasilan di atas 95%.'],
                ['q' => 'Bisakah saya memantau perkembangan?', 'a' => 'Kami kirim laporan berkala via WhatsApp/email dengan tonggak kemajuan dan dokumen pendukung.'],
                ['q' => 'Apakah melayani klien di luar Jawa Barat?', 'a' => 'Ya. Kami memiliki jaringan konsultan di berbagai provinsi dan melayani seluruh Indonesia. Konsultasi awal daring.'],
            ];
        @endphp

        <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.04),0_2px_12px_rgba(0,0,0,0.03)] overflow-hidden">
            @foreach($faqItems as $item)
            <div x-data="{ open: false }" class="faq-divider">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left hover:bg-[#FCFAF8] transition-colors">
                    <span class="text-sm font-semibold text-[#2D2A24] text-left">{{ $item['q'] }}</span>
                    <svg class="w-4 h-4 text-[#9C9690] flex-shrink-0 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-collapse.duration.300ms>
                    <div class="px-5 pb-4">
                        <p class="text-sm text-[#6B6560] leading-relaxed">{{ $item['a'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
