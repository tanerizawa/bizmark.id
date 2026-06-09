<footer class="site-footer">
    <div class="container-wide py-10">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
            <div class="col-span-2 lg:col-span-1">
                <a href="{{ url('/') }}" class="brand-mark">
                    <img src="{{ asset('images/logo-mark.svg') }}" alt="" class="h-6 w-auto" aria-hidden="true">
                    <span>Bizmark.ID</span>
                </a>
                <p class="footer-note mt-3">Perizinan Industri Terpercaya</p>
            </div>
            <div>
                <p class="footer-title">Navigasi</p>
                <ul class="footer-list">
                    <li><a href="{{ url('/') }}">Beranda</a></li>
                    <li><a href="{{ route('layanan') }}">Layanan</a></li>
                    <li><a href="{{ route('proses') }}">Proses</a></li>
                    <li><a href="{{ route('blog.index') }}">Blog</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-title">Platform</p>
                <ul class="footer-list">
                    <li><a href="{{ route('konsultasi-gratis') }}">Cek Perizinan AI</a></li>
                    <li><a href="{{ route('permohonan') }}">Permohonan</a></li>
                    <li><a href="{{ route('login') }}">Portal Klien</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-title">Hubungi Kami</p>
                <ul class="footer-list">
                    <li><a href="mailto:{{ config('landing_metrics.contact.email', 'info@bizmark.id') }}">{{ config('landing_metrics.contact.email', 'info@bizmark.id') }}</a></li>
                    <li><a href="tel:{{ config('landing_metrics.contact.phone', '+6283879602855') }}">{{ config('landing_metrics.contact.phone', '+62 838 7960 2855') }}</a></li>
                    <li><a href="{{ config('landing_metrics.contact.whatsapp_link', 'https://wa.me/6283879602855') }}">WhatsApp</a></li>
                </ul>
            </div>
            <div>
                <p class="footer-title">Legal</p>
                <ul class="footer-list">
                    <li><a href="{{ url('/kebijakan-privasi') }}">Kebijakan Privasi</a></li>
                    <li><a href="{{ url('/syarat-ketentuan') }}">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-[var(--border-subtle)] text-center text-sm text-[var(--text-tertiary)]">
            &copy; {{ date('Y') }} PT CANGAH PAJARATAN MANDIRI (Bizmark.ID).
        </div>
    </div>
</footer>
