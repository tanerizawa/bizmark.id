@extends('layouts.admin')

@section('title', '2FA Challenge')

@section('content')
<div class="max-w-xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-semibold text-white mb-4">Verifikasi 2FA</h1>

    <div class="card-elevated rounded-apple-lg p-6 space-y-4">
        <form method="POST" action="{{ route('admin.security.2fa.verify') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-sm font-medium text-dark-text-secondary">Kode 6 digit (atau backup code)</label>
                <input name="code" type="text" autocomplete="one-time-code"
                       class="input-dark w-full px-3 py-2 rounded-apple text-sm mt-1" required>
                @error('code')
                    <div class="text-sm text-apple-red mt-1">{{ $message }}</div>
                @enderror
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-dark-text-secondary">
                <input type="checkbox" name="trust_device" value="1"
                       class="rounded border-dark-border bg-dark-surface-tertiary text-apple-blue focus:ring-apple-blue">
                Trust this device (30 hari)
            </label>

            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-apple bg-apple-blue text-white hover:bg-apple-blue-dark transition-colors">
                Verifikasi
            </button>
        </form>
    </div>
</div>
@endsection
