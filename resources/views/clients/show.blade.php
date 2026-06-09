@extends('layouts.admin')

@section('title', 'Detail Klien - ' . $client->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center">
            <a href="{{ route('clients.index') }}" class="text-apple-blue-dark hover:text-apple-blue mr-4">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-white">{{ $client->name }}</h1>
                <div class="flex items-center space-x-3 text-xs mt-1 text-dark-text-secondary">
                    <span class="flex items-center">
                        <i class="fas fa-calendar-alt mr-1.5"></i>{{ $client->created_at->format('d M Y') }}
                    </span>
                    <span class="flex items-center">
                        <i class="fas fa-folder mr-1.5"></i>{{ $client->projects->count() }} Proyek
                    </span>
                </div>
            </div>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('clients.edit', $client) }}"
               class="px-3 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center bg-apple-orange/90 text-white">
                <i class="fas fa-edit mr-1.5"></i>Edit
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-blue-dark) 100%);">
                    <i class="fas fa-briefcase text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-dark-text-secondary">Total Proyek</p>
                    <h3 class="text-xl font-semibold text-white">{{ $client->projects->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-green) 0%, #28a745 100%);">
                    <i class="fas fa-tasks text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-dark-text-secondary">Proyek Aktif</p>
                    <h3 class="text-xl font-semibold text-white">{{ $client->activeProjectsCount() }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-orange) 0%, #fd7e14 100%);">
                    <i class="fas fa-money-bill-wave text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-dark-text-secondary">Total Nilai</p>
                    <h3 class="text-sm font-semibold text-white">Rp {{ number_format($client->totalProjectValue ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <div class="card-elevated rounded-apple-lg p-3">
            <div class="flex items-center">
                <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, var(--apple-teal) 0%, #17a2b8 100%);">
                    <i class="fas fa-check-circle text-white text-base"></i>
                </div>
                <div class="ml-3">
                    <p class="text-xs text-dark-text-secondary">Total Dibayar</p>
                    <h3 class="text-sm font-semibold text-white">Rp {{ number_format($client->totalPaid ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
        <!-- Client Information -->
        <div class="card-elevated rounded-apple-lg">
            <div class="px-4 py-2.5 border-b border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-info-circle mr-2 text-apple-blue"></i>Informasi Klien
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Klien</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->name }}</p>
                </div>

                @if($client->company_name)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Nama Perusahaan</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->company_name }}</p>
                </div>
                @endif

                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Tipe Klien</label>
                    <p class="text-sm">
                        @if($client->client_type == 'individual')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-teal/15 text-apple-teal">Individual</span>
                        @elseif($client->client_type == 'company')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-blue/15 text-apple-blue">Perusahaan</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-purple/15 text-apple-purple">Pemerintah</span>
                        @endif
                    </p>
                </div>

                @if($client->industry)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Industri</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->industry }}</p>
                </div>
                @endif

                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Status</label>
                    <p class="text-sm">
                        @if($client->status == 'active')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-green/15 text-apple-green">Aktif</span>
                        @elseif($client->status == 'inactive')
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-red/15 text-apple-red">Tidak Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-apple bg-apple-orange/15 text-apple-orange">Potensial</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card-elevated rounded-apple-lg">
            <div class="px-4 py-2.5 border-b border-white/10">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-address-book mr-2 text-apple-green"></i>Informasi Kontak
                </h3>
            </div>
            <div class="p-4 space-y-3">
                @if($client->contact_person)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Contact Person</label>
                    <p class="text-sm text-dark-text-primary">{{ $client->contact_person }}</p>
                </div>
                @endif

                @if($client->email)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Email</label>
                    <p class="text-sm text-dark-text-primary flex items-center">
                        <i class="fas fa-envelope mr-2 text-apple-blue"></i>
                        <a href="mailto:{{ $client->email }}" class="text-apple-blue hover:underline">{{ $client->email }}</a>
                    </p>
                </div>
                @endif

                @if($client->phone)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Telepon</label>
                    <p class="text-sm text-dark-text-primary flex items-center">
                        <i class="fas fa-phone mr-2 text-apple-blue"></i>
                        <a href="tel:{{ $client->phone }}" class="text-apple-blue hover:underline">{{ $client->phone }}</a>
                    </p>
                </div>
                @endif

                @if($client->mobile)
                <div class="pb-3 border-b border-white/10 last:border-b-0 last:pb-0">
                    <label class="text-xs font-medium text-dark-text-secondary block mb-1">Handphone / WhatsApp</label>
                    <p class="text-sm flex items-center space-x-2">
                        <i class="fab fa-whatsapp text-apple-green"></i>
                        <a href="tel:{{ $client->mobile }}" class="text-apple-blue hover:underline">{{ $client->mobile }}</a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $client->mobile) }}" target="_blank" 
                           class="inline-flex items-center px-2 py-1 rounded-apple text-xs font-medium transition-apple bg-apple-green/15 text-apple-green border border-apple-green/30 hover:bg-apple-green/25">
                            <i class="fab fa-whatsapp mr-1"></i>Chat
                        </a>
                    </p>
                </div>
                @endif

                @if(!$client->contact_person && !$client->email && !$client->phone && !$client->mobile)
                <p class="text-xs text-dark-text-tertiary">Tidak ada informasi kontak tersedia</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Projects List -->
    @if($client->projects->count() > 0)
    <div class="card-elevated rounded-apple-lg">
        <div class="px-4 py-2.5 border-b border-white/10">
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold flex items-center text-white">
                    <i class="fas fa-folder mr-2 text-apple-orange"></i>Daftar Proyek ({{ $client->projects->count() }})
                </h3>
                <a href="{{ route('projects.create', ['client_id' => $client->id]) }}" class="btn-primary px-3 py-1.5 text-white rounded-lg text-xs font-medium inline-flex items-center">
                    <i class="fas fa-plus mr-1.5"></i>Tambah Proyek
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-dark-bg-secondary">
                    <tr>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase text-dark-text-secondary">Nama Proyek</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase text-dark-text-secondary">Status</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase text-dark-text-secondary">Deadline</th>
                        <th scope="col" class="px-4 py-2.5 text-left text-xs font-medium uppercase text-dark-text-secondary">Nilai Kontrak</th>
                        <th scope="col" class="px-4 py-2.5 text-center text-xs font-medium uppercase text-dark-text-secondary">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-dark-bg-secondary">
                    @foreach($client->projects as $project)
                        <tr class="hover-lift transition-apple hover:bg-dark-bg-tertiary">
                            <td class="px-4 py-3">
                                <div class="font-medium text-sm text-white">{{ $project->name }}</div>
                                @if($project->description)
                                    <div class="text-xs mt-0.5 text-dark-text-secondary">{{ Str::limit($project->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($project->status)
                                    <span class="px-2 py-0.5 text-xs font-medium rounded" style="background-color: {{ $project->status->color }}33; color: {{ $project->status->color }};">
                                        {{ $project->status->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-dark-text-secondary">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-dark-text-secondary">
                                @if($project->deadline)
                                    {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-white">
                                Rp {{ number_format($project->contract_value ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium transition-colors bg-apple-blue/15 text-apple-blue">
                                    <i class="fas fa-eye mr-1.5"></i>Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($client->notes)
    <div class="card-elevated rounded-apple-lg mt-4">
        <div class="px-4 py-2.5 border-b border-white/10">
            <h3 class="text-sm font-semibold flex items-center text-white">
                <i class="fas fa-sticky-note mr-2 text-apple-purple"></i>Catatan
            </h3>
        </div>
        <div class="p-4">
            <p class="text-sm whitespace-pre-line text-dark-text-primary/80">{{ $client->notes }}</p>
        </div>
    </div>
    @endif
</div>

@endsection
