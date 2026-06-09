<section class="py-12 sm:py-16 lg:py-20 bg-[#FCFAF8]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Pertanyaan Umum
            </h2>
            <p class="text-base text-[#6B6560]">Masih ragu? <a href="/contact" class="text-[#0D9488] hover:text-[#0F766E] font-semibold">Hubungi tim kami</a> dan kami akan bantu jelaskan.</p>
        </div>

        @php
            $faqItems = [
                ['q' => 'Berapa lama proses pengurusan perizinan?', 'a' => 'Waktu pengurusan bervariasi tergantung jenis izin. OSS (NIB) biasanya 1-3 hari kerja, UKL-UPL 14-30 hari kerja, sedangkan AMDAL dapat memakan waktu 3-6 bulan. Kami memberikan estimasi setelah konsultasi awal.'],
                ['q' => 'Apa saja dokumen yang perlu disiapkan?', 'a' => 'Dokumen dasar meliputi KTP/NPWP Direktur, Akta Pendirian, SK Kemenkumham, NPWP Perusahaan, serta dokumen teknis tambahan sesuai jenis perizinan. Tim kami membantu menyiapkan seluruh kebutuhan tersebut.'],
                ['q' => 'Bagaimana skema pembayaran layanan Bizmark.ID?', 'a' => 'Kami menerapkan pembayaran bertahap: 50% saat pekerjaan dimulai sebagai uang muka dan 50% ketika izin terbit. Untuk proyek besar kami dapat menyesuaikan skema sesuai kesepakatan.'],
                ['q' => 'Apakah ada jaminan jika perizinan tidak berhasil?', 'a' => 'Ada. Jika kegagalan berasal dari sisi kami, biaya akan dikembalikan sebagian sesuai kesepakatan awal. Dengan tingkat keberhasilan di atas 95%, situasi tersebut jarang terjadi.'],
                ['q' => 'Bisakah saya memantau perkembangan perizinan?', 'a' => 'Kami mengirimkan laporan berkala lewat WhatsApp atau email lengkap dengan tonggak kemajuan dan dokumen pendukung sehingga Anda memiliki visibilitas penuh.'],
                ['q' => 'Apakah melayani klien di luar Jawa Barat?', 'a' => 'Ya. Walau kantor pusat di Karawang, kami memiliki jaringan konsultan di berbagai provinsi dan dapat mengurus izin di seluruh Indonesia. Konsultasi awal dapat dilakukan secara daring.'],
            ];
        @endphp

        <div class="space-y-2">
            @foreach($faqItems as $i => $item)
            <div x-data="{ open: false }" class="bg-white rounded-xl border border-[#E5E0DB] overflow-hidden hover:border-[#0D9488]/20 transition-colors duration-200">
                <button @click="open = !open" class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left" :class="{ 'border-b border-[#F0ECE6]': open }">
                    <span class="text-sm font-semibold text-[#2D2A24]">{{ $item['q'] }}</span>
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
