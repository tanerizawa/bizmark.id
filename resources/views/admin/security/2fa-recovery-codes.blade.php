@extends('layouts.admin')

@section('title', 'Recovery Codes')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-white mb-4">Backup Codes</h1>

    <div class="card-elevated rounded-apple-lg p-6 space-y-4">
        <div class="text-sm text-dark-text-secondary">
            Simpan kode berikut di tempat aman. Setiap kode hanya bisa dipakai sekali.
        </div>

        <div class="grid grid-cols-2 gap-2">
            @foreach($codes as $c)
                <div class="font-mono bg-dark-surface-tertiary border border-dark-border rounded-apple px-3 py-2 text-dark-text-primary text-sm">{{ $c }}</div>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-apple bg-apple-blue text-white hover:bg-apple-blue-dark transition-colors">
                Selesai
            </a>

            <form method="POST" action="{{ route('admin.security.2fa.recovery.regenerate') }}">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 rounded-apple border border-dark-border text-dark-text-primary bg-dark-surface-tertiary hover:bg-dark-surface-secondary transition-colors">
                    Generate ulang
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
