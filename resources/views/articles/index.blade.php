@extends('layouts.admin')

@section('title', 'Artikel & Berita')
@section('page-title', 'Artikel & Berita')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:240px;height:240px;border-radius:50%;top:-70px;right:-40px;background:color-mix(in srgb,var(--apple-purple) 14%,transparent);filter:blur(60px);pointer-events:none"></div>
        <div style="position:absolute;width:160px;height:160px;border-radius:50%;bottom:-30px;left:30px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);filter:blur(50px);pointer-events:none"></div>
        <div style="position:relative">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;margin-bottom:16px">
                <div>
                    <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Content Management</p>
                    <h1 style="font-size:1.25rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 6px">Artikel & Berita</h1>
                    <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Kelola konten artikel manual dan auto-generated AI dengan mudah.</p>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
                    <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0"><i class="fas fa-sync-alt" style="margin-right:5px"></i>{{ now()->locale('id')->isoFormat('D MMM Y, HH:mm') }}</p>
                    <a href="{{ route('articles.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-plus" style="font-size:0.75rem"></i>Buat Artikel Baru
                    </a>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
                <div style="background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border-radius:12px;padding:12px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 25%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-newspaper" style="font-size:0.75rem;color:var(--apple-blue)"></i></div>
                        <div><p style="font-size:1.1rem;font-weight:700;color:#fff;margin:0">{{ number_format($stats['all']) }}</p><p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">Total Artikel</p></div>
                    </div>
                </div>
                <div style="background:color-mix(in srgb,var(--apple-green) 12%,transparent);border-radius:12px;padding:12px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 25%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-check" style="font-size:0.75rem;color:var(--apple-green)"></i></div>
                        <div><p style="font-size:1.1rem;font-weight:700;color:var(--apple-green);margin:0">{{ number_format($stats['published']) }}</p><p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">Published</p></div>
                    </div>
                </div>
                <div style="background:color-mix(in srgb,var(--apple-orange) 12%,transparent);border-radius:12px;padding:12px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 25%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-pencil-alt" style="font-size:0.75rem;color:var(--apple-orange)"></i></div>
                        <div><p style="font-size:1.1rem;font-weight:700;color:var(--apple-orange);margin:0">{{ number_format($stats['draft']) }}</p><p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">Draft</p></div>
                    </div>
                </div>
                <div style="background:color-mix(in srgb,var(--apple-purple) 12%,transparent);border-radius:12px;padding:12px">
                    <div style="display:flex;align-items:center;gap:8px">
                        <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 25%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-robot" style="font-size:0.75rem;color:var(--apple-purple)"></i></div>
                        <div><p style="font-size:1.1rem;font-weight:700;color:var(--apple-purple);margin:0">{{ number_format($stats['auto_generated']) }}</p><p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">AI Generated</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div style="padding:12px 16px;border-radius:10px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);color:var(--apple-green);font-size:0.85rem">
        <i class="fas fa-check-circle" style="margin-right:6px"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="padding:12px 16px;border-radius:10px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);color:var(--apple-red);font-size:0.85rem">
        <i class="fas fa-exclamation-circle" style="margin-right:6px"></i>{{ session('error') }}
    </div>
    @endif

    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">

        {{-- Tab Bar --}}
        <div style="display:flex;flex-wrap:wrap;border-bottom:1px solid var(--dark-separator)">
            <a href="{{ route('articles.index', ['tab' => 'all'] + request()->except('tab', 'page')) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:14px 20px;font-size:0.85rem;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab==='all' ? 'var(--apple-blue)' : 'transparent' }};color:{{ $tab==='all' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};background:{{ $tab==='all' ? 'color-mix(in srgb,var(--apple-blue) 10%,transparent)' : 'transparent' }}">
                <i class="fas fa-list" style="font-size:0.75rem"></i>Semua
                <span style="background:rgba(255,255,255,.1);border-radius:4px;padding:1px 6px;font-size:0.68rem">{{ $stats['all'] }}</span>
            </a>
            <a href="{{ route('articles.index', ['tab' => 'manual'] + request()->except('tab', 'page')) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:14px 20px;font-size:0.85rem;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab==='manual' ? 'var(--apple-blue)' : 'transparent' }};color:{{ $tab==='manual' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};background:{{ $tab==='manual' ? 'color-mix(in srgb,var(--apple-blue) 10%,transparent)' : 'transparent' }}">
                <i class="fas fa-pen" style="font-size:0.75rem"></i>Manual
                <span style="background:rgba(255,255,255,.1);border-radius:4px;padding:1px 6px;font-size:0.68rem">{{ $stats['manual'] }}</span>
            </a>
            <a href="{{ route('articles.index', ['tab' => 'auto-generated'] + request()->except('tab', 'page')) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:14px 20px;font-size:0.85rem;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab==='auto-generated' ? 'var(--apple-blue)' : 'transparent' }};color:{{ $tab==='auto-generated' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};background:{{ $tab==='auto-generated' ? 'color-mix(in srgb,var(--apple-blue) 10%,transparent)' : 'transparent' }}">
                <i class="fas fa-robot" style="font-size:0.75rem"></i>AI
                <span style="background:rgba(255,255,255,.1);border-radius:4px;padding:1px 6px;font-size:0.68rem">{{ $stats['auto_generated'] }}</span>
            </a>
            <a href="{{ route('articles.index', ['tab' => 'auto-post-settings'] + request()->except('tab', 'page')) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:14px 20px;font-size:0.85rem;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab==='auto-post-settings' ? 'var(--apple-blue)' : 'transparent' }};color:{{ $tab==='auto-post-settings' ? 'var(--apple-blue)' : 'var(--dark-text-secondary)' }};background:{{ $tab==='auto-post-settings' ? 'color-mix(in srgb,var(--apple-blue) 10%,transparent)' : 'transparent' }}">
                <i class="fas fa-cog" style="font-size:0.75rem"></i>Auto-Post
            </a>
        </div>

        @if($tab === 'auto-post-settings')
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-cog" style="font-size:0.7rem;color:var(--apple-blue)"></i></div>
                            <span style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary)">Konfigurasi Auto-Post</span>
                        </div>
                        <button type="button" id="autoPostToggle" class="toggle-switch {{ $autoPostConfig && $autoPostConfig->is_enabled ? 'active' : '' }}" data-url="{{ route('auto-post.config.toggle') }}" data-csrf="{{ csrf_token() }}">
                            <span class="toggle-slider"></span>
                        </button>
                    </div>
                    @if($autoPostConfig)
                    <div style="font-size:0.82rem">
                        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--dark-separator)">
                            <span style="color:var(--dark-text-secondary)">Status:</span>
                            <span data-status-row style="font-weight:600;color:{{ $autoPostConfig->is_enabled ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $autoPostConfig->is_enabled ? '✓ Aktif' : '✗ Non-aktif' }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--dark-separator)">
                            <span style="color:var(--dark-text-secondary)">Posts per Day:</span>
                            <span style="font-weight:600;color:var(--dark-text-primary)">{{ $autoPostConfig->posts_per_day }}x</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--dark-separator)">
                            <span style="color:var(--dark-text-secondary)">Post Times:</span>
                            <span style="font-weight:600;color:var(--dark-text-primary)">{{ implode(', ', $autoPostConfig->post_times ?? []) }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--dark-separator)">
                            <span style="color:var(--dark-text-secondary)">AI Model:</span>
                            <span style="font-weight:600;color:var(--dark-text-primary)">{{ $autoPostConfig->ai_model }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;padding:9px 0">
                            <span style="color:var(--dark-text-secondary)">Auto Publish:</span>
                            <span style="font-weight:600;color:{{ $autoPostConfig->auto_publish ? 'var(--apple-green)' : 'var(--apple-red)' }}">{{ $autoPostConfig->auto_publish ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-top:20px">
                        <a href="{{ route('auto-post.config') }}" style="display:block;padding:10px;border-radius:10px;background:var(--apple-blue);color:#fff;text-align:center;font-size:0.82rem;font-weight:600;text-decoration:none" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-cog" style="margin-right:6px"></i>Edit Konfigurasi Lengkap
                        </a>
                        <a href="{{ route('auto-post.topics.index') }}" style="display:block;padding:10px;border-radius:10px;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);text-align:center;font-size:0.82rem;font-weight:600;text-decoration:none" onmouseover="this.style.background='rgba(255,255,255,.08)'" onmouseout="this.style.background='var(--dark-bg-tertiary)'">
                            <i class="fas fa-lightbulb" style="margin-right:6px"></i>Kelola Topic Pool
                        </a>
                    </div>
                    @else
                    <p style="font-size:0.82rem;color:var(--dark-text-secondary)">Konfigurasi auto-post belum diatur.</p>
                    @endif
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                        <div style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-calendar-alt" style="font-size:0.7rem;color:var(--apple-purple)"></i></div>
                        <span style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary)">Jadwal Mendatang</span>
                    </div>
                    @if($upcomingSchedules->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach($upcomingSchedules as $schedule)
                        <div style="padding:12px;border-radius:10px;background:rgba(255,255,255,.05)">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
                                <div style="flex:1">
                                    <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">{{ $schedule->topic ? Str::limit($schedule->topic->title, 50) : 'Topic tidak ditemukan' }}</p>
                                    <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0"><i class="far fa-clock" style="margin-right:4px"></i>{{ optional($schedule->scheduled_at ?? $schedule->scheduled_for)->format('d M Y, H:i') ?? '-' }}</p>
                                </div>
                                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange);white-space:nowrap">Pending</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="margin-top:16px">
                        <a href="{{ route('auto-post.schedules.index') }}" style="display:block;padding:10px;border-radius:10px;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);text-align:center;font-size:0.82rem;font-weight:600;text-decoration:none" onmouseover="this.style.background='rgba(255,255,255,.08)'" onmouseout="this.style.background='var(--dark-bg-tertiary)'">
                            <i class="fas fa-calendar-check" style="margin-right:6px"></i>Lihat Semua Jadwal
                        </a>
                    </div>
                    @else
                    <div style="text-align:center;padding:32px 0">
                        <i class="fas fa-calendar-times" style="font-size:2.5rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                        <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 16px">Belum ada jadwal yang akan datang</p>
                        <form action="{{ route('auto-post.schedules.generate-batch') }}" method="POST" style="display:inline">
                            @csrf
                            <input type="hidden" name="date" value="{{ now()->addDay()->format('Y-m-d') }}">
                            <button type="submit" style="padding:8px 18px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                                <i class="fas fa-magic" style="margin-right:6px"></i>Generate Tomorrow's Posts
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-top:20px">
                <a href="{{ route('auto-post.analytics') }}" style="padding:16px;border-radius:12px;background:color-mix(in srgb,var(--apple-blue) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 30%,transparent);text-decoration:none;display:block" onmouseover="this.style.background='color-mix(in srgb,var(--apple-blue) 16%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-blue) 10%,transparent)'">
                    <i class="fas fa-chart-line" style="font-size:1.5rem;color:var(--apple-blue);display:block;margin-bottom:8px"></i>
                    <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Analytics Dashboard</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">Monitor performa auto-posting</p>
                </a>
                <a href="{{ route('auto-post.logs.index') }}" style="padding:16px;border-radius:12px;background:color-mix(in srgb,var(--apple-purple) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-purple) 30%,transparent);text-decoration:none;display:block" onmouseover="this.style.background='color-mix(in srgb,var(--apple-purple) 16%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-purple) 10%,transparent)'">
                    <i class="fas fa-file-alt" style="font-size:1.5rem;color:var(--apple-purple);display:block;margin-bottom:8px"></i>
                    <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Activity Logs</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">Lihat log aktivitas sistem</p>
                </a>
                <a href="{{ route('auto-post.topics.create') }}" style="padding:16px;border-radius:12px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);text-decoration:none;display:block" onmouseover="this.style.background='color-mix(in srgb,var(--apple-green) 16%,transparent)'" onmouseout="this.style.background='color-mix(in srgb,var(--apple-green) 10%,transparent)'">
                    <i class="fas fa-plus-circle" style="font-size:1.5rem;color:var(--apple-green);display:block;margin-bottom:8px"></i>
                    <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Add New Topic</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">Tambah topic ke pool</p>
                </a>
            </div>
        </div>

        @else
        <div style="padding:20px">
            <form action="{{ route('articles.index') }}" method="GET" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;margin-bottom:20px">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..." style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                <select name="status" style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status')=='published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status')=='archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <select name="category" style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                    <option value="">Semua Kategori</option>
                    @foreach(App\Models\Article::getCategories() as $key => $label)
                    <option value="{{ $key }}" {{ request('category')==$key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" style="padding:9px 18px;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;white-space:nowrap" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:6px"></i>Filter
                </button>
            </form>

            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                        <tr style="background:var(--dark-bg-tertiary);border-bottom:1px solid var(--dark-separator)">
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Artikel</th>
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Kategori</th>
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Status</th>
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Penulis</th>
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Views</th>
                            <th style="padding:10px 14px;text-align:left;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Tanggal</th>
                            <th style="padding:10px 14px;text-align:right;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-tertiary)">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                        <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='rgba(255,255,255,.03)'" onmouseout="this.style.background='transparent'">
                            <td style="padding:12px 14px">
                                <div style="display:flex;align-items:center;gap:10px">
                                    @if($article->featured_image_url)
                                    <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" style="width:56px;height:56px;object-fit:cover;border-radius:8px;flex-shrink:0">
                                    @else
                                    <div style="width:56px;height:56px;background:var(--dark-bg-tertiary);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <i class="fas fa-image" style="font-size:1.1rem;color:var(--dark-text-tertiary)"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">{{ Str::limit($article->title, 50) }}</p>
                                        <div style="display:flex;gap:4px;flex-wrap:wrap">
                                            @if($article->is_featured)
                                            <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)"><i class="fas fa-star" style="margin-right:3px;font-size:0.6rem"></i>Featured</span>
                                            @endif
                                            @if($article->source_type === 'auto-generated')
                                            <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)"><i class="fas fa-robot" style="margin-right:3px;font-size:0.6rem"></i>AI</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $article->category_label }}</td>
                            <td style="padding:12px 14px">{!! $article->status_badge !!}</td>
                            <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $article->author?->name ?? 'System AI' }}</td>
                            <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)"><i class="fas fa-eye" style="margin-right:4px"></i>{{ number_format($article->views_count) }}</td>
                            <td style="padding:12px 14px;font-size:0.82rem;color:var(--dark-text-secondary)">{{ $article->published_at ? $article->formatted_published_at : '-' }}</td>
                            <td style="padding:12px 14px">
                                <div style="display:flex;align-items:center;justify-content:flex-end;gap:4px">
                                    @if($article->status == 'draft')
                                    <form action="{{ route('articles.publish', $article) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" title="Publikasikan" style="padding:6px;border-radius:7px;background:transparent;border:none;cursor:pointer;color:var(--apple-green);font-size:0.85rem" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'"><i class="fas fa-check-circle"></i></button>
                                    </form>
                                    @elseif($article->status == 'published')
                                    <form action="{{ route('articles.unpublish', $article) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button type="submit" title="Unpublish" style="padding:6px;border-radius:7px;background:transparent;border:none;cursor:pointer;color:var(--apple-orange);font-size:0.85rem" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'"><i class="fas fa-pause-circle"></i></button>
                                    </form>
                                    @endif
                                    <a href="{{ route('articles.show', $article) }}" title="Lihat" style="padding:6px;border-radius:7px;color:var(--apple-blue);font-size:0.85rem;display:inline-flex;align-items:center;text-decoration:none" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('articles.edit', $article) }}" title="Edit" style="padding:6px;border-radius:7px;color:var(--dark-text-secondary);font-size:0.85rem;display:inline-flex;align-items:center;text-decoration:none" onmouseover="this.style.background='var(--dark-bg-tertiary)';this.style.color='var(--dark-text-primary)'" onmouseout="this.style.background='transparent';this.style.color='var(--dark-text-secondary)'"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('articles.destroy', $article) }}" method="POST" style="display:inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" style="padding:6px;border-radius:7px;background:transparent;border:none;cursor:pointer;color:var(--apple-red);font-size:0.85rem" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="padding:48px;text-align:center">
                                <i class="fas fa-newspaper" style="font-size:2.5rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                                <p style="font-size:0.95rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">Belum ada artikel</p>
                                <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0 0 16px">
                                    @if($tab === 'auto-generated')AI belum menghasilkan artikel. Pastikan auto-post aktif dan jadwal tersedia.@else Mulai dengan membuat artikel pertama Anda @endif
                                </p>
                                @if($tab !== 'auto-generated')
                                <a href="{{ route('articles.create') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none">
                                    <i class="fas fa-plus" style="font-size:0.75rem"></i>Buat Artikel Baru
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($articles->hasPages())
            <div style="margin-top:20px">{{ $articles->appends(request()->query())->links() }}</div>
            @endif
        </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
    .toggle-switch { position:relative;display:inline-block;width:48px;height:26px;background:rgba(255,255,255,.2);border-radius:26px;cursor:pointer;transition:background-color .3s ease;border:none;padding:0; }
    .toggle-switch.active { background:#34C759; }
    .toggle-slider { position:absolute;top:3px;left:3px;width:20px;height:20px;background:white;border-radius:50%;transition:transform .3s ease;pointer-events:none; }
    .toggle-switch.active .toggle-slider { transform:translateX(22px); }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('autoPostToggle');
    if (!toggleBtn) return;
    toggleBtn.addEventListener('click', function () {
        const btn = this;
        btn.style.opacity = '0.5'; btn.style.pointerEvents = 'none';
        fetch(this.dataset.url, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':this.dataset.csrf,'Accept':'application/json'} })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                btn.classList.toggle('active', data.is_enabled);
                const s = document.querySelector('[data-status-row]');
                if (s) { s.style.color = data.is_enabled ? 'var(--apple-green)' : 'var(--apple-red)'; s.textContent = data.is_enabled ? '✓ Aktif' : '✗ Non-aktif'; }
            }
        })
        .catch(() => window.location.reload())
        .finally(() => { btn.style.opacity='1'; btn.style.pointerEvents='auto'; });
    });
});
</script>
@endpush
