<section class="py-12 sm:py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#CCFBF1] border border-[#0D9488]/10 mb-4">
                <span class="text-xs font-semibold text-[#115E59]">Cara Kerja Kami</span>
            </span>
            <h2 class="text-[clamp(1.75rem,3vw,2.5rem)] font-bold leading-[1.1] tracking-tight text-[#2D2A24] mb-3">
                Proses yang mengutamakan kejelasan dan akuntabilitas.
            </h2>
            <p class="text-base text-[#6B6560] leading-relaxed">Empat langkah yang jelas. Satu tim yang bertanggung jawab. Hasil terukur dengan SLA di setiap tahap.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-4 sm:gap-5">
            @php
                $steps = [
                    ['num' => '01', 'time' => '1-2 hari', 'title' => 'Kajian Awal & Pemetaan', 'desc' => 'Kami telaah konteks usaha, kode KBLI, dan celah perizinan Anda — tanpa biaya.'],
                    ['num' => '02', 'time' => '2-3 hari', 'title' => 'Proposal & Kesepakatan', 'desc' => 'Lingkup pekerjaan, biaya, dan ketentuan SLA yang jelas — semua terdokumentasi sebelum mulai.'],
                    ['num' => '03', 'time' => 'Variatif', 'title' => 'Pelaksanaan & Pelaporan', 'desc' => 'Laporan kemajuan SLA mingguan, manajer proyek khusus, dan tindak lanjut langsung di lapangan.', 'highlight' => true],
                    ['num' => '04', 'time' => '1 minggu', 'title' => 'Terbit & Serah Terima', 'desc' => 'Izin diserahkan lengkap beserta peta jalan kepatuhan dan pilihan dukungan berkelanjutan.'],
                ];
            @endphp

            @foreach($steps as $i => $step)
            <div class="relative">
                @if($i < 3)
                <div class="hidden md:block absolute top-6 left-[calc(50%+1.5rem)] right-0 h-px bg-[#E5E0DB] -z-10"></div>
                @endif

                <div class="bg-white rounded-xl border {{ isset($step['highlight']) ? 'border-[#0D9488]/20 border-2' : 'border-[#E5E0DB]' }} p-5 {{ isset($step['highlight']) ? '' : 'hover:border-[#0D9488]/20' }} transition-all duration-200 h-full">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-[#9C9690]">{{ $step['num'] }}</span>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full {{ isset($step['highlight']) ? 'bg-[#CCFBF1] text-[#115E59]' : 'bg-[#F0ECE6] text-[#6B6560]' }}">{{ $step['time'] }}</span>
                    </div>
                    <h3 class="text-sm font-bold text-[#2D2A24] mb-1">{{ $step['title'] }}</h3>
                    <p class="text-xs text-[#6B6560] leading-relaxed">{{ $step['desc'] }}</p>
                    @if(isset($step['highlight']))
                    <div class="mt-3 flex items-center gap-1.5 text-[10px] font-semibold text-[#0D9488]">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Terpantau via Portal Klien
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 text-center">
            <a href="/proses" class="inline-flex items-center gap-2 text-sm font-semibold text-[#0D9488] hover:text-[#0F766E] transition-colors">
                Pelajari proses kami secara lengkap
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>
