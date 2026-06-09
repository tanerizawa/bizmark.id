@extends('layouts.admin')

@section('title', 'Setup Two-Factor Authentication')

@section('content')
<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div>
        <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Keamanan Akun</p>
        <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:3px 0 0;line-height:1.2">Setup Two-Factor Authentication</h1>
        <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:6px 0 0">Lindungi akun Anda dengan lapisan keamanan tambahan menggunakan aplikasi authenticator.</p>
    </div>

    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px">
            <i class="fas fa-exclamation-circle" style="color:var(--apple-red);font-size:1rem;flex-shrink:0;margin-top:2px"></i>
            <div>
                @foreach($errors->all() as $error)
                    <p style="font-size:0.82rem;color:var(--apple-red);margin:0">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Steps --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
            <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-mobile-alt" style="color:var(--apple-blue);font-size:0.9rem"></i>
            </div>
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);opacity:.8;margin:0">Langkah 1</p>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Scan QR Code</h3>
            </div>
        </div>
        <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 16px">
            Buka aplikasi authenticator (Google Authenticator, Authy, atau Microsoft Authenticator), lalu scan QR code di bawah ini.
        </p>

        {{-- QR Code --}}
        <div style="display:flex;justify-content:center;margin:20px 0">
            <div style="background:#fff;padding:16px;border-radius:12px;display:inline-block">
                <div id="qrcode"></div>
            </div>
        </div>

        {{-- Manual Entry Toggle --}}
        <div style="margin-top:14px">
            <button type="button" onclick="toggleManual()"
                    style="font-size:0.75rem;color:var(--apple-blue);background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;padding:0">
                <i class="fas fa-keyboard" style="font-size:0.68rem"></i>
                <span id="manualToggleText">Tidak bisa scan? Masukkan kode manual</span>
            </button>
            <div id="manualEntry" style="display:none;margin-top:12px">
                <p style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin:0 0 6px;text-transform:uppercase;letter-spacing:.06em">Kunci Rahasia (Secret Key)</p>
                <div style="position:relative">
                    <code id="secretCode"
                          style="display:block;padding:10px 40px 10px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;font-size:0.82rem;color:var(--apple-green);font-family:monospace;letter-spacing:.12em;word-break:break-all">{{ $secret }}</code>
                    <button type="button" onclick="copySecret()"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--dark-text-tertiary)"
                            title="Salin"
                            onmouseover="this.style.color='var(--apple-blue)'" onmouseout="this.style.color='var(--dark-text-tertiary)'">
                        <i class="fas fa-copy" id="copyIcon" style="font-size:0.8rem"></i>
                    </button>
                </div>
                <p style="font-size:0.7rem;color:var(--dark-text-tertiary);margin:6px 0 0">Di aplikasi authenticator, pilih "Masukkan kunci secara manual" dan ketikkan kode di atas.</p>
            </div>
        </div>
    </div>

    {{-- Verification Form --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:20px">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--dark-separator)">
            <div style="width:34px;height:34px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="fas fa-shield-alt" style="color:var(--apple-green);font-size:0.9rem"></i>
            </div>
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-green);opacity:.8;margin:0">Langkah 2</p>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:2px 0 0">Verifikasi Kode</h3>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.security.2fa.enable') }}" id="twoFaForm">
            @csrf
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:0.78rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">
                    Kode 6 Digit dari Aplikasi Authenticator
                </label>
                <input name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                       maxlength="6" placeholder="000000" required autofocus
                       style="width:100%;padding:12px 16px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('code') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:10px;color:var(--dark-text-primary);font-size:1.4rem;font-weight:700;letter-spacing:.3em;text-align:center;outline:none;box-sizing:border-box;transition:border-color .2s;font-family:monospace"
                       onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='{{ $errors->has('code') ? 'var(--apple-red)' : 'var(--dark-separator)' }}'"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,6)">
                @error('code')
                    <p style="font-size:0.72rem;color:var(--apple-red);margin-top:6px;display:flex;align-items:center;gap:4px">
                        <i class="fas fa-exclamation-circle"></i>{{ $message }}
                    </p>
                @enderror
                <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin-top:6px">
                    Kode baru dibuat setiap 30 detik. Pastikan waktu perangkat Anda sudah benar.
                </p>
            </div>

            <button type="submit" id="submitBtn"
                    style="width:100%;padding:12px 20px;background:var(--apple-green);color:#fff;border:none;border-radius:10px;font-size:0.9rem;font-weight:700;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s"
                    onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                <i class="fas fa-check-circle" id="btnIcon"></i>
                <span id="btnText">Aktifkan 2FA</span>
                <i class="fas fa-spinner fa-spin" id="btnSpinner" style="display:none"></i>
            </button>
        </form>
    </div>

    {{-- Recommended Apps --}}
    <div style="background:color-mix(in srgb,var(--apple-blue) 6%,var(--dark-bg-secondary));border:1px solid color-mix(in srgb,var(--apple-blue) 20%,var(--dark-separator));border-radius:16px;padding:16px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
            <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.85rem"></i>
            <h4 style="font-size:0.82rem;font-weight:700;color:var(--apple-blue);margin:0">Aplikasi Authenticator yang Direkomendasikan</h4>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
            @foreach([
                ['Google Authenticator','fa-google','var(--apple-blue)','Android & iOS'],
                ['Authy','fa-shield-alt','var(--apple-purple)','Android, iOS & Desktop'],
                ['Microsoft Authenticator','fa-microsoft','var(--apple-teal)','Android & iOS'],
            ] as [$name,$icon,$col,$platform])
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;padding:12px;text-align:center">
                <i class="fab {{ $icon }}" style="color:{{ $col }};font-size:1.4rem;display:block;margin-bottom:6px"></i>
                <p style="font-size:0.72rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $name }}</p>
                <p style="font-size:0.65rem;color:var(--dark-text-tertiary);margin:3px 0 0">{{ $platform }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
// Generate QR Code
const otpauthUrl = @json($otpauthUrl);
QRCode.toCanvas(document.createElement('canvas'), otpauthUrl, {
    width: 200,
    margin: 0,
    color: { dark: '#000000', light: '#ffffff' }
}, function(error, canvas) {
    if (!error) {
        document.getElementById('qrcode').appendChild(canvas);
    } else {
        // Fallback: show text link
        document.getElementById('qrcode').innerHTML =
            '<p style="font-size:0.72rem;color:#666;max-width:200px;word-break:break-all">' +
            'QR tidak tersedia. Gunakan kunci manual di bawah.</p>';
    }
});

function toggleManual() {
    const el = document.getElementById('manualEntry');
    const btn = document.getElementById('manualToggleText');
    const isHidden = el.style.display === 'none';
    el.style.display = isHidden ? 'block' : 'none';
    btn.textContent = isHidden ? 'Sembunyikan kunci manual' : 'Tidak bisa scan? Masukkan kode manual';
}

function copySecret() {
    const text = document.getElementById('secretCode').textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        const icon = document.getElementById('copyIcon');
        icon.className = 'fas fa-check';
        icon.style.color = 'var(--apple-green)';
        setTimeout(() => {
            icon.className = 'fas fa-copy';
            icon.style.color = '';
        }, 1800);
    });
}

// Auto-submit when 6 digits entered
document.querySelector('input[name="code"]').addEventListener('input', function() {
    if (this.value.length === 6) {
        document.getElementById('twoFaForm').submit();
    }
});

// Submit guard
document.getElementById('twoFaForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.style.opacity = '0.6';
    document.getElementById('btnIcon').style.display = 'none';
    document.getElementById('btnText').textContent = 'Memverifikasi...';
    document.getElementById('btnSpinner').style.display = 'inline-block';
});
</script>
@endpush
