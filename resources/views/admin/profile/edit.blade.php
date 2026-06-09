@extends('layouts.admin')

@section('page-title', 'Edit Profil')

@section('content')
<!-- Hero Section with Gradient Background -->
<div class="card-elevated rounded-apple-xl p-5 md:p-6 mb-6 relative overflow-hidden">
    <!-- Gradient Blur Background -->
    <div class="w-72 h-72 bg-apple-blue opacity-30 blur-3xl rounded-full absolute -top-16 -right-10"></div>
    <div class="w-48 h-48 bg-apple-purple opacity-20 blur-2xl rounded-full absolute bottom-0 left-10"></div>
    
    <div class="relative z-10">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-dark-text-tertiary uppercase tracking-wider mb-1">PENGATURAN AKUN</p>
                <h1 class="text-2xl font-bold text-dark-text-primary mb-2">Edit Profil</h1>
                <p class="text-sm text-dark-text-secondary">
                    Kelola informasi profil dan pengaturan akun Anda
                </p>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 p-4 rounded-apple-lg" style="background: rgba(52,199,89,0.12); border: 1px solid rgba(52,199,89,0.3);">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-apple-green mr-3"></i>
            <span class="text-apple-green font-medium">{{ session('success') }}</span>
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Profile Avatar -->
    <div class="lg:col-span-1">
        <div class="card-elevated rounded-apple-xl p-6">
            <h2 class="text-lg font-semibold text-dark-text-primary mb-4">Foto Profil</h2>
            
            <div class="flex flex-col items-center">
                <div class="w-32 h-32 rounded-full bg-apple-blue flex items-center justify-center text-white text-xl font-bold mb-4 overflow-hidden">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    @endif
                </div>
                
                <h3 class="text-lg font-semibold text-dark-text-primary text-center">{{ $user->name }}</h3>
                <p class="text-sm text-dark-text-secondary text-center mb-4">{{ $user->email }}</p>
                
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="w-full">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    
                    <label class="block">
                        <span class="sr-only">Choose avatar</span>
                        <input type="file" 
                               name="avatar" 
                               accept="image/*"
                               class="block w-full text-sm text-dark-text-secondary
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-apple file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-apple-blue file:text-white
                                      hover:file:bg-apple-blue-dark
                                      file:cursor-pointer cursor-pointer">
                    </label>
                    
                    <button type="submit" class="mt-4 w-full py-2 px-4 bg-apple-blue text-white rounded-apple-lg font-medium hover:bg-apple-blue-dark transition">
                        <i class="fas fa-upload mr-2"></i>Upload Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Information & Password -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Information -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h2 class="text-lg font-semibold text-dark-text-primary mb-4 flex items-center">
                <i class="fas fa-user mr-2 text-apple-blue"></i>
                Informasi Personal
            </h2>
            
            <form action="{{ route('admin.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Nama Lengkap
                        </label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Email
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Nomor Telepon
                        </label>
                        <input type="text" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               placeholder="+62 xxx xxxx xxxx">
                        @error('phone')
                            <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-2.5 bg-apple-blue text-white rounded-apple-lg font-medium hover:bg-apple-blue-dark transition">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="card-elevated rounded-apple-xl p-6">
            <h2 class="text-lg font-semibold text-dark-text-primary mb-4 flex items-center">
                <i class="fas fa-lock mr-2 text-apple-orange"></i>
                Ubah Password
            </h2>
            
            <form action="{{ route('admin.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Password Saat Ini
                        </label>
                        <input type="password" 
                               name="current_password" 
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               required>
                        @error('current_password')
                            <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Password Baru
                        </label>
                        <input type="password" 
                               name="password" 
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               required>
                        @error('password')
                            <p class="mt-1 text-sm text-apple-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-dark-text-secondary mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="w-full px-4 py-2 rounded-apple-lg bg-dark-bg-tertiary border border-dark-separator text-dark-text-primary focus:outline-none focus:border-apple-blue"
                               required>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="px-6 py-2.5 bg-apple-orange text-white rounded-apple-lg font-medium hover:opacity-90 transition">
                            <i class="fas fa-key mr-2"></i>Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
