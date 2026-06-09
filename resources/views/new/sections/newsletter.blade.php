<section class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl border border-[#E5E0DB] p-6 sm:p-8 lg:p-10 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-40 h-40 rounded-full bg-[#0D9488]/5 blur-3xl"></div>

            <div class="relative max-w-2xl">
                <h2 class="text-xl sm:text-2xl font-bold text-[#2D2A24] mb-2">Pembaruan regulasi langsung ke email Anda.</h2>
                <p class="text-sm text-[#6B6560] leading-relaxed mb-6">Ringkasan bulanan pembaruan regulasi perizinan, perubahan aturan KBLI, dan briefing kepatuhan — dikurasi langsung oleh spesialis regulasi kami.</p>

                <form action="/subscribe" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="email" name="email" required placeholder="Alamat email"
                           class="flex-1 px-4 py-3 bg-[#F8F6F3] border border-[#E5E0DB] rounded-xl text-sm text-[#2D2A24] placeholder:text-[#9C9690] focus:outline-none focus:border-[#0D9488] focus:ring-2 focus:ring-[#0D9488]/20 transition-all duration-200">
                    <button type="submit" class="px-6 py-3 bg-[#0D9488] text-white text-sm font-semibold rounded-xl hover:bg-[#0F766E] transition-all duration-200 shadow-sm hover:shadow-md whitespace-nowrap">
                        Berlangganan
                    </button>
                </form>
                <p class="mt-3 text-xs text-[#9C9690]">Ringkasan bulanan · Bebas spam · Berhenti kapan saja</p>
            </div>
        </div>
    </div>
</section>
