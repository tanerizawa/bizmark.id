@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Jadwal Manual</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Buat jadwal posting untuk topic tertentu
            </p>
        </div>

        @if($availableTopics->isEmpty())
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Tidak ada topic yang tersedia untuk dijadwalkan.
                            <a href="{{ route('auto-post.topics.create') }}" class="font-medium underline">Tambah topic baru</a>
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('auto-post.schedules.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Topic Selection -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Pilih Topic <span class="text-red-500">*</span>
                </label>
                <select 
                    name="topic_id" 
                    required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Topic --</option>
                    @foreach($availableTopics as $topic)
                        <option value="{{ $topic->id }}" {{ old('topic_id') == $topic->id ? 'selected' : '' }}>
                            {{ $topic->title }} (Priority: {{ $topic->priority }})
                        </option>
                    @endforeach
                </select>
                @error('topic_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Schedule Date & Time -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Waktu Publikasi <span class="text-red-500">*</span>
                </label>
                <input 
                    type="datetime-local" 
                    name="scheduled_at" 
                    value="{{ old('scheduled_at') }}"
                    min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                    required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Minimal 1 jam dari sekarang
                </p>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('auto-post.schedules.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Buat Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
