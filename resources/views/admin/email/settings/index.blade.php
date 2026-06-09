@extends('layouts.admin')

@section('title', 'Email Settings')

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white flex items-center gap-2">
            <i class="fas fa-cog text-blue-400"></i>Email Settings
        </h1>
        <p class="text-gray-400 mt-1">Configure SMTP settings for sending emails</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-check-circle"></i><span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl px-4 py-3 mb-6">
        <i class="fas fa-exclamation-circle"></i><span>{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- SMTP Configuration --}}
        <div class="lg:col-span-2">
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700 bg-blue-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-server"></i>SMTP Configuration
                    </h5>
                </div>
                <div class="p-5">
                    <form action="{{ route('admin.email.settings.update') }}" method="POST" id="smtpForm">
                        @csrf @method('PUT')

                        {{-- Mail Driver --}}
                        <div class="mb-5">
                            <label for="mail_mailer" class="block text-sm font-medium text-white mb-1">
                                Mail Driver <span class="text-red-400">*</span>
                            </label>
                            <select id="mail_mailer" name="mail_mailer" required
                                    onchange="toggleMailFields()"
                                    class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="mailgun" {{ old('mail_mailer', $settings['mail_mailer']) === 'mailgun' ? 'selected' : '' }}>Mailgun (Recommended)</option>
                                <option value="smtp" {{ old('mail_mailer', $settings['mail_mailer']) === 'smtp' ? 'selected' : '' }}>SMTP (Custom Server)</option>
                                <option value="sendmail" {{ old('mail_mailer', $settings['mail_mailer']) === 'sendmail' ? 'selected' : '' }}>Sendmail (Local Server)</option>
                                <option value="log" {{ old('mail_mailer', $settings['mail_mailer']) === 'log' ? 'selected' : '' }}>Log (Development Only)</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Mailgun recommended for production. SMTP for custom servers. Log for testing only.</p>
                        </div>

                        {{-- Mailgun Fields --}}
                        <div id="mailgun-fields" class="hidden space-y-4 mb-5">
                            <div class="flex items-start gap-3 bg-blue-500/10 border border-blue-500/30 text-blue-300 rounded-xl px-4 py-3 text-sm">
                                <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                                <span><strong>Mailgun Setup:</strong> Sign up at <a href="https://signup.mailgun.com/" target="_blank" class="underline">mailgun.com</a>,
                                add domain <code class="bg-gray-900 px-1 rounded">mg.bizmark.id</code>, then add DNS records provided by Mailgun.</span>
                            </div>
                            <div>
                                <label for="mailgun_domain" class="block text-sm font-medium text-white mb-1">Mailgun Domain <span class="text-red-400">*</span></label>
                                <input type="text" id="mailgun_domain" name="mailgun_domain"
                                       value="{{ old('mailgun_domain', config('services.mailgun.domain')) }}"
                                       placeholder="mg.bizmark.id"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Your verified domain in Mailgun</p>
                            </div>
                            <div>
                                <label for="mailgun_secret" class="block text-sm font-medium text-white mb-1">Mailgun API Key <span class="text-red-400">*</span></label>
                                <input type="password" id="mailgun_secret" name="mailgun_secret"
                                       value="{{ old('mailgun_secret', config('services.mailgun.secret')) }}"
                                       placeholder="key-••••••••••••"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Get from Mailgun Dashboard → Settings → API Keys</p>
                            </div>
                            <div>
                                <label for="mailgun_endpoint" class="block text-sm font-medium text-white mb-1">Mailgun Endpoint</label>
                                <select id="mailgun_endpoint" name="mailgun_endpoint"
                                        class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="api.eu.mailgun.net" {{ old('mailgun_endpoint', config('services.mailgun.endpoint')) === 'api.eu.mailgun.net' ? 'selected' : '' }}>EU (api.eu.mailgun.net) — GDPR Compliant</option>
                                    <option value="api.mailgun.net" {{ old('mailgun_endpoint', config('services.mailgun.endpoint')) === 'api.mailgun.net' ? 'selected' : '' }}>US (api.mailgun.net)</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Choose EU for GDPR compliance</p>
                            </div>
                        </div>

                        {{-- SMTP Fields --}}
                        <div id="smtp-fields" class="hidden space-y-4 mb-5">
                            <div>
                                <label for="mail_host" class="block text-sm font-medium text-white mb-1">SMTP Host <span class="text-red-400">*</span></label>
                                <input type="text" id="mail_host" name="mail_host"
                                       value="{{ old('mail_host', $settings['mail_host']) }}" placeholder="smtp.gmail.com"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Examples: smtp.gmail.com, smtp.office365.com, smtp.mailgun.org</p>
                            </div>
                            <div>
                                <label for="mail_port" class="block text-sm font-medium text-white mb-1">SMTP Port <span class="text-red-400">*</span></label>
                                <input type="number" id="mail_port" name="mail_port"
                                       value="{{ old('mail_port', $settings['mail_port']) }}" placeholder="587"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Common ports: 587 (TLS), 465 (SSL), 25 (Unsecured)</p>
                            </div>
                            <div>
                                <label for="mail_encryption" class="block text-sm font-medium text-white mb-1">Encryption</label>
                                <select id="mail_encryption" name="mail_encryption"
                                        class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="tls" {{ old('mail_encryption', $settings['mail_encryption']) === 'tls' ? 'selected' : '' }}>TLS (Recommended)</option>
                                    <option value="ssl" {{ old('mail_encryption', $settings['mail_encryption']) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ old('mail_encryption', $settings['mail_encryption']) === '' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                            <div>
                                <label for="mail_username" class="block text-sm font-medium text-white mb-1">SMTP Username</label>
                                <input type="text" id="mail_username" name="mail_username"
                                       value="{{ old('mail_username', $settings['mail_username']) }}" placeholder="your-email@gmail.com"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="mail_password" class="block text-sm font-medium text-white mb-1">SMTP Password</label>
                                <input type="password" id="mail_password" name="mail_password"
                                       placeholder="••••••••••••"
                                       class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password</p>
                            </div>
                        </div>

                        {{-- From Email --}}
                        <div class="mb-5">
                            <label for="mail_from_address" class="block text-sm font-medium text-white mb-1">
                                From Email Address <span class="text-red-400">*</span>
                            </label>
                            <input type="email" id="mail_from_address" name="mail_from_address" required
                                   value="{{ old('mail_from_address', $settings['mail_from_address']) }}" placeholder="noreply@bizmark.id"
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        {{-- From Name --}}
                        <div class="mb-6">
                            <label for="mail_from_name" class="block text-sm font-medium text-white mb-1">
                                From Name <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="mail_from_name" name="mail_from_name" required
                                   value="{{ old('mail_from_name', $settings['mail_from_name']) }}" placeholder="Bizmark.id"
                                   class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition">
                                <i class="fas fa-save mr-2"></i>Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Test + Info --}}
        <div class="space-y-5">

            {{-- Test Email --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700 bg-cyan-600/20">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-paper-plane text-cyan-400"></i>Test Email
                    </h5>
                </div>
                <div class="p-5">
                    <p class="text-gray-400 text-sm mb-4">Send a test email to verify your SMTP configuration</p>
                    <div class="mb-4">
                        <label for="test_email" class="block text-sm font-medium text-white mb-1">Test Email Address</label>
                        <input type="email" id="test_email" placeholder="test@example.com"
                               class="w-full bg-gray-900 text-white border border-gray-600 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                    <button type="button" onclick="sendTestEmail()"
                        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium py-2.5 rounded-xl transition text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                    </button>
                    <div id="test-result" class="mt-3"></div>
                </div>
            </div>

            {{-- SMTP Providers Info --}}
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow">
                <div class="px-5 py-4 border-b border-gray-700">
                    <h5 class="text-white font-semibold flex items-center gap-2">
                        <i class="fas fa-info-circle text-gray-400"></i>Popular SMTP Providers
                    </h5>
                </div>
                <div class="p-5 space-y-4 text-sm">
                    @foreach([
                        ['name' => 'Mailgun (Recommended)', 'note' => 'Best for transactional emails', 'extra' => '$35/month for 50k emails'],
                        ['name' => 'SendGrid', 'note' => 'Host: smtp.sendgrid.net', 'extra' => 'Port: 587 (TLS)'],
                        ['name' => 'Gmail', 'note' => 'Host: smtp.gmail.com', 'extra' => 'Port: 587 (TLS)'],
                        ['name' => 'Office 365', 'note' => 'Host: smtp.office365.com', 'extra' => 'Port: 587 (TLS)'],
                    ] as $provider)
                    <div>
                        <p class="text-white font-medium">{{ $provider['name'] }}</p>
                        <p class="text-gray-400 text-xs">{{ $provider['note'] }}</p>
                        <p class="text-gray-500 text-xs">{{ $provider['extra'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    const resultDiv = document.getElementById('test-result');

    if (!email) {
        resultDiv.innerHTML = '<div class="mt-2 flex items-center gap-2 text-yellow-400 text-sm"><i class="fas fa-exclamation-triangle"></i>Please enter an email address</div>';
        return;
    }

    resultDiv.innerHTML = '<div class="mt-2 flex items-center gap-2 text-blue-400 text-sm"><i class="fas fa-spinner fa-spin"></i>Sending test email...</div>';

    fetch('{{ route('admin.email.settings.test') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ test_email: email })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultDiv.innerHTML = '<div class="mt-2 flex items-center gap-2 text-green-400 text-sm"><i class="fas fa-check-circle"></i>' + data.message + '</div>';
        } else {
            resultDiv.innerHTML = '<div class="mt-2 flex items-center gap-2 text-red-400 text-sm"><i class="fas fa-exclamation-circle"></i>' + data.message + '</div>';
        }
    })
    .catch(err => {
        resultDiv.innerHTML = '<div class="mt-2 flex items-center gap-2 text-red-400 text-sm"><i class="fas fa-exclamation-circle"></i>Error: ' + err.message + '</div>';
    });
}

function toggleMailFields() {
    const mailer = document.getElementById('mail_mailer').value;
    document.getElementById('smtp-fields').classList.toggle('hidden', mailer !== 'smtp');
    document.getElementById('mailgun-fields').classList.toggle('hidden', mailer !== 'mailgun');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleMailFields();
});
</script>
@endsection
