@extends('layouts.admin')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-lg font-bold text-gray-900 dark:text-white">Analytics Auto-Post</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Monitor performa sistem auto-posting AI</p>
    </div>

    <!-- Period Filter -->
    <div class="mb-6">
        <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-1">
            @foreach(['24hours' => '24 Jam', '7days' => '7 Hari', '30days' => '30 Hari', '90days' => '90 Hari'] as $key => $label)
                <a href="{{ route('auto-post.analytics', ['period' => $key]) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md {{ $period === $key ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Artikel Dibuat</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['total_articles'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Success Rate</p>
                    <p class="text-xl font-bold text-green-600 mt-2">{{ $stats['success_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Topics Tersedia</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-2">{{ $stats['available_topics'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lightbulb text-2xl text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-xl font-bold text-yellow-600 mt-2">{{ $stats['pending_schedules'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $performanceMetrics['total_attempts'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Total Attempts</p>
            </div>
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-lg font-bold text-green-600">{{ $performanceMetrics['successful'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Successful</p>
            </div>
            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <p class="text-lg font-bold text-red-600">{{ $performanceMetrics['failed'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Failed</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <p class="text-lg font-bold text-yellow-600">{{ $performanceMetrics['quality_issues'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Quality Issues</p>
            </div>
            <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                <p class="text-lg font-bold text-orange-600">{{ $performanceMetrics['duplicates'] }}</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Duplicates</p>
            </div>
        </div>
    </div>

    <!-- Daily Generation Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Generation</h2>
        <div class="h-64 flex items-end justify-between space-x-2">
            @foreach($dailyGeneration as $day)
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-t relative" style="height: {{ $day->count > 0 ? ($day->count / $dailyGeneration->max('count')) * 100 : 0 }}%">
                        <div class="absolute bottom-0 left-0 right-0 bg-green-500 rounded-t" style="height: {{ $day->success > 0 ? ($day->success / $day->count) * 100 : 0 }}%"></div>
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                        {{ \Carbon\Carbon::parse($day->date)->format('d M') }}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex items-center justify-center space-x-4 mt-4 text-xs">
            <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded mr-1"></div>
                <span class="text-gray-600 dark:text-gray-400">Success</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-red-500 rounded mr-1"></div>
                <span class="text-gray-600 dark:text-gray-400">Failed</span>
            </div>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Category Distribution</h2>
        <div class="space-y-3">
            @foreach($categoryDistribution as $cat)
                @php
                    $percentage = $stats['total_articles'] > 0 ? ($cat->count / $stats['total_articles']) * 100 : 0;
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">{{ ucfirst($cat->category) }}</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $cat->count }} ({{ number_format($percentage, 1) }}%)</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Activity Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h2>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900 sticky top-0">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Topic</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentLogs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->format('d M H:i') }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-900 dark:text-white">
                                {{ str_replace('_', ' ', ucfirst($log->event)) }}
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $log->schedule?->topic?->title ?? '-' }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($log->level === 'success') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                    @elseif($log->level === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                    @elseif($log->level === 'error') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300
                                    @else bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                    @endif">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
