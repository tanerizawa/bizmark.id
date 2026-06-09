@extends('layouts.admin')

@section('title', 'Dokumen')
@section('page-title', 'Manajemen Dokumen')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- KPI Cards --}}
    @php
    $totalDocs        = $documents->total();
    $totalSize        = $documents->getCollection()->sum('file_size');
    $perizinanCount   = $documents->getCollection()->whereIn('category', ['perizinan','sk'])->count();
    $confidentialCount= $documents->getCollection()->where('is_confidential', true)->count();
    $fmt = function($bytes) {
        if ($bytes >= 1073741824) return number_format($bytes/1073741824,2).' GB';
        if ($bytes >= 1048576)   return number_format($bytes/1048576,2).' MB';
        return number_format($bytes/1024,2).' KB';
    };

    $statsData = [
        ['label'=>'Total Dokumen',  'value'=>$totalDocs,                 'sub'=>'semua berkas',       'color'=>'var(--dark-text-primary)', 'bg'=>'transparent',          'icon'=>'fa-file-alt'],
        ['label'=>'Total Ukuran',   'value'=>$fmt($totalSize),           'sub'=>'ruang penyimpanan',  'color'=>'var(--apple-purple)',      'bg'=>'var(--apple-purple)',   'icon'=>'fa-hdd'],
        ['label'=>'Perizinan/SK',   'value'=>$perizinanCount,            'sub'=>'dokumen izin',       'color'=>'var(--apple-orange)',      'bg'=>'var(--apple-orange)',   'icon'=>'fa-certificate'],
        ['label'=>'Rahasia',        'value'=>$confidentialCount,         'sub'=>'bersifat confidential','color'=>'var(--apple-red)',       'bg'=>'var(--apple-red)',      'icon'=>'fa-lock'],
    ];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @foreach($statsData as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['bg'] }} 12%,var(--dark-bg-secondary)) 0%,var(--dark-bg-secondary) 100%);border:1px solid color-mix(in srgb,{{ $s['bg'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px;position:relative;overflow:hidden">
            <div style="position:absolute;top:10px;right:14px;font-size:1rem;opacity:.2;color:{{ $s['color'] }}">
                <i class="fas {{ $s['icon'] }}"></i>
            </div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.8;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:2rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Smart Search & Filter Toolbar --}}
    @php
        $activeFilters = collect([
            'search'        => request('search'),
            'category'      => request('category'),
            'document_type' => request('document_type'),
            'project_id'    => request('project_id'),
        ])->filter()->count();
    @endphp
    <form method="GET" action="{{ route('documents.index') }}" id="docs-filter-form">
        <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:12px 14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap">

            {{-- Search --}}
            <div style="position:relative;flex:1;min-width:220px">
                <i class="fas fa-magnifying-glass" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:0.72rem;color:var(--dark-text-tertiary);pointer-events:none;z-index:1"></i>
                <input type="text" name="search" id="df-search" value="{{ request('search') }}"
                       placeholder="Cari judul, nama file, deskripsi…"
                       style="width:100%;padding:8px 36px 8px 34px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;line-height:1.4;outline:none;box-sizing:border-box;transition:border-color .18s"
                       onfocus="this.style.borderColor='var(--apple-blue)'"
                       onblur="this.style.borderColor='var(--dark-separator)'">
                <button type="button" id="df-clear-search"
                        style="display:{{ request('search') ? 'flex' : 'none' }};position:absolute;right:9px;top:50%;transform:translateY(-50%);width:18px;height:18px;align-items:center;justify-content:center;background:var(--dark-text-tertiary);border:none;border-radius:50%;cursor:pointer;padding:0;color:var(--dark-bg-primary);font-size:0.55rem"
                        onclick="document.getElementById('df-search').value='';this.style.display='none';document.getElementById('docs-filter-form').submit()">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            {{-- Separator --}}
            <div style="width:1px;height:26px;background:var(--dark-separator);flex-shrink:0"></div>

            {{-- Filter Pills --}}
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">

                {{-- Kategori pill --}}
                <div style="position:relative">
                    <select name="category"
                            style="padding:6px 28px 6px 10px;background:{{ request('category') ? 'color-mix(in srgb,var(--apple-orange) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('category') ? 'color-mix(in srgb,var(--apple-orange) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('category') ? 'var(--apple-orange)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('category') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category')==$cat ? 'selected':'' }}>{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('category') ? 'var(--apple-orange)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Tipe pill --}}
                <div style="position:relative">
                    <select name="document_type"
                            style="padding:6px 28px 6px 10px;background:{{ request('document_type') ? 'color-mix(in srgb,var(--apple-teal) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('document_type') ? 'color-mix(in srgb,var(--apple-teal) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('document_type') ? 'var(--apple-teal)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('document_type') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Tipe</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type }}" {{ request('document_type')==$type ? 'selected':'' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('document_type') ? 'var(--apple-teal)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Proyek pill --}}
                <div style="position:relative">
                    <select name="project_id"
                            style="padding:6px 28px 6px 10px;background:{{ request('project_id') ? 'color-mix(in srgb,var(--apple-purple) 18%,var(--dark-bg-tertiary))' : 'var(--dark-bg-tertiary)' }};border:1px solid {{ request('project_id') ? 'color-mix(in srgb,var(--apple-purple) 45%,var(--dark-separator))' : 'var(--dark-separator)' }};border-radius:20px;color:{{ request('project_id') ? 'var(--apple-purple)' : 'var(--dark-text-secondary)' }};font-size:0.75rem;font-weight:{{ request('project_id') ? '600' : '500' }};outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;white-space:nowrap;transition:all .18s">
                        <option value="">Proyek</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id')==$project->id ? 'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down" style="position:absolute;right:9px;top:50%;transform:translateY(-50%);font-size:0.5rem;color:{{ request('project_id') ? 'var(--apple-purple)' : 'var(--dark-text-tertiary)' }};pointer-events:none"></i>
                </div>

                {{-- Active filter badge + reset --}}
                @if($activeFilters > 0)
                <a href="{{ route('documents.index') }}"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:color-mix(in srgb,var(--apple-red) 14%,var(--dark-bg-tertiary));border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:20px;font-size:0.72rem;font-weight:600;color:var(--apple-red);text-decoration:none;white-space:nowrap;transition:opacity .18s"
                   onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                    <i class="fas fa-xmark"></i>Reset
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;background:var(--apple-red);color:#fff;border-radius:50%;font-size:0.6rem;font-weight:700">{{ $activeFilters }}</span>
                </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table Card --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Data</p>
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:3px 0 0">Daftar Dokumen</h3>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                @php $isEmptyDocs = ($documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $documents->total() : $documents->count()) === 0; @endphp
                <span style="font-size:0.75rem;color:var(--dark-text-secondary)">
                    @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        @if($documents->total() === 0)
                            0 dokumen
                        @else
                            {{ $documents->firstItem() }}–{{ $documents->lastItem() }} dari {{ $documents->total() }}
                        @endif
                    @endif
                </span>
                @unless($isEmptyDocs)
                <a href="{{ route('documents.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:0.78rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                    <i class="fas fa-upload"></i>Upload Dokumen
                </a>
                @endunless
            </div>
        </div>

        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="background:var(--dark-bg-tertiary)">
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Dokumen</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Kategori</th>
                        <th style="padding:10px 16px;text-align:center;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Tipe</th>
                        <th style="padding:10px 16px;text-align:center;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Ukuran</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Proyek</th>
                        <th style="padding:10px 16px;text-align:left;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Diunggah</th>
                        <th style="padding:10px 16px;text-align:right;font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:rgba(235,235,245,0.85)">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $i => $document)
                        @php
                            $ext = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                            $ftMap = [
                                'pdf'  => ['fa-file-pdf',     'var(--apple-red)'],
                                'doc'  => ['fa-file-word',    'var(--apple-blue)'],
                                'docx' => ['fa-file-word',    'var(--apple-blue)'],
                                'xls'  => ['fa-file-excel',   'var(--apple-green)'],
                                'xlsx' => ['fa-file-excel',   'var(--apple-green)'],
                                'jpg'  => ['fa-file-image',   'var(--apple-purple)'],
                                'jpeg' => ['fa-file-image',   'var(--apple-purple)'],
                                'png'  => ['fa-file-image',   'var(--apple-purple)'],
                                'zip'  => ['fa-file-archive', 'var(--apple-orange)'],
                                'rar'  => ['fa-file-archive', 'var(--apple-orange)'],
                            ];
                            [$ftIcon,$ftColor] = $ftMap[$ext] ?? ['fa-file-alt','var(--dark-text-tertiary)'];

                            $catMap = [
                                'perizinan' => ['fa-file-contract',  'var(--apple-orange)'],
                                'sk'        => ['fa-certificate',    'var(--apple-orange)'],
                                'kontrak'   => ['fa-file-signature', 'var(--apple-purple)'],
                                'laporan'   => ['fa-chart-bar',      'var(--apple-green)'],
                                'teknis'    => ['fa-file-code',      'var(--apple-blue)'],
                                'proposal'  => ['fa-file-alt',       'var(--apple-teal)'],
                                'surat'     => ['fa-envelope',       'var(--apple-indigo)'],
                            ];
                            [$catIcon,$catColor] = $catMap[$document->category] ?? ['fa-folder','var(--dark-text-secondary)'];

                            $fs = $document->file_size ?? 0;
                            $fmtSize = $fs >= 1048576 ? number_format($fs/1048576,2).' MB' : number_format($fs/1024,2).' KB';
                            $rowBg = $i % 2 === 1 ? 'rgba(255,255,255,0.02)' : 'transparent';
                        @endphp
                        <tr style="border-top:1px solid var(--dark-separator);background:{{ $rowBg }};cursor:pointer;transition:background .15s"
                            onmouseover="this.style.background='rgba(255,255,255,0.04)'"
                            onmouseout="this.style.background='{{ $rowBg }}'"
                            onclick="window.location='{{ route('documents.show', $document) }}'">

                            <td style="padding:12px 16px">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:38px;height:38px;border-radius:10px;background:color-mix(in srgb,{{ $ftColor }} 14%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <i class="fas {{ $ftIcon }}" style="font-size:1rem;color:{{ $ftColor }}"></i>
                                    </div>
                                    <div style="min-width:0">
                                        <div style="display:flex;align-items:center;gap:6px">
                                            <span style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px">{{ $document->title }}</span>
                                            @if($document->is_confidential)
                                                <i class="fas fa-lock" style="font-size:0.65rem;color:var(--apple-red);flex-shrink:0" title="Rahasia"></i>
                                            @endif
                                            @if(($document->version ?? 1) > 1)
                                                <span style="font-size:0.65rem;font-weight:600;padding:1px 6px;border-radius:20px;background:color-mix(in srgb,var(--apple-blue) 14%,transparent);color:var(--apple-blue);flex-shrink:0">v{{ $document->version }}</span>
                                            @endif
                                        </div>
                                        <span style="display:block;font-size:0.7rem;color:var(--dark-text-secondary);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px">{{ $document->file_name }}</span>
                                        @if(($document->download_count ?? 0) > 0)
                                            <span style="display:block;font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:1px"><i class="fas fa-download" style="margin-right:3px"></i>{{ $document->download_count }} unduhan</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td style="padding:12px 16px;white-space:nowrap">
                                @if($document->category)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,{{ $catColor }} 14%,transparent);color:{{ $catColor }}">
                                        <i class="fas {{ $catIcon }}" style="font-size:0.65rem"></i>{{ ucfirst($document->category) }}
                                    </span>
                                @else
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">—</span>
                                @endif
                            </td>

                            <td style="padding:12px 16px;text-align:center;white-space:nowrap">
                                <span style="display:inline-block;padding:2px 8px;border-radius:6px;font-size:0.68rem;font-weight:700;letter-spacing:.05em;background:color-mix(in srgb,{{ $ftColor }} 14%,transparent);color:{{ $ftColor }}">{{ strtoupper($ext) }}</span>
                            </td>

                            <td style="padding:12px 16px;text-align:center;white-space:nowrap">
                                <span style="font-size:0.8rem;color:var(--dark-text-primary)">{{ $fmtSize }}</span>
                            </td>

                            <td style="padding:12px 16px">
                                @if($document->project)
                                    <a href="{{ route('projects.show', $document->project) }}"
                                       onclick="event.stopPropagation()"
                                       style="font-size:0.8rem;color:var(--apple-blue);text-decoration:none"
                                       onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                        {{ Str::limit($document->project->name, 28) }}
                                    </a>
                                @else
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">—</span>
                                @endif
                            </td>

                            <td style="padding:12px 16px">
                                @if($document->uploader)
                                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px">
                                        <span style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue);font-size:0.6rem;font-weight:700;flex-shrink:0">{{ strtoupper(substr($document->uploader->name,0,1)) }}</span>
                                        <span style="font-size:0.78rem;color:var(--dark-text-primary)">{{ $document->uploader->name }}</span>
                                    </div>
                                @endif
                                <span style="font-size:0.7rem;color:var(--dark-text-secondary)">{{ $document->created_at->format('d M Y') }}</span>
                                <span style="font-size:0.68rem;color:var(--dark-text-tertiary);display:block">{{ $document->created_at->diffForHumans() }}</span>
                            </td>

                            <td style="padding:12px 16px;text-align:right;white-space:nowrap" onclick="event.stopPropagation()">
                                <div style="display:inline-flex;align-items:center;gap:6px">
                                    <a href="{{ Storage::url($document->file_path) }}" download
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-green);text-decoration:none;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent)"
                                       title="Unduh"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('documents.show', $document) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-blue);text-decoration:none;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent)"
                                       title="Lihat"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('documents.edit', $document) }}"
                                       style="display:inline-flex;align-items:center;padding:5px 10px;font-size:0.72rem;font-weight:600;color:var(--apple-orange);text-decoration:none;background:color-mix(in srgb,var(--apple-orange) 12%,transparent);border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent)"
                                       title="Ubah"
                                       onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:48px 20px;text-align:center">
                                <div style="width:52px;height:52px;border-radius:14px;background:color-mix(in srgb,var(--dark-text-secondary) 10%,var(--dark-bg-tertiary));display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                                    <i class="fas fa-folder-open" style="font-size:1.4rem;color:var(--dark-text-tertiary)"></i>
                                </div>
                                <p style="font-size:0.9rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">
                                    @if($activeFilters > 0) Tidak Ada Hasil @else Belum Ada Dokumen @endif
                                </p>
                                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 18px">
                                    @if($activeFilters > 0) Coba ubah atau reset filter pencarian @else Upload dokumen pertama untuk memulai @endif
                                </p>
                                @if($activeFilters > 0)
                                <a href="{{ route('documents.index') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.78rem;font-weight:600;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);border:1px solid var(--dark-separator);border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.75" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-xmark"></i>Reset Filter
                                </a>
                                @else
                                <a href="{{ route('documents.create') }}"
                                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;font-size:0.8rem;font-weight:600;background:var(--apple-blue);color:#fff;border-radius:8px;text-decoration:none"
                                   onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-upload"></i>Upload Dokumen
                                </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator && $documents->hasPages())
            <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <x-ui.pagination :paginator="$documents->appends(request()->all())" variant="full" :show-info="true" />
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('docs-filter-form');
    if (!form) return;

    // Auto-submit on select change
    form.querySelectorAll('select').forEach(el => el.addEventListener('change', () => form.submit()));

    // Submit on Enter, show/hide clear button
    const searchInput = form.querySelector('#df-search');
    const clearBtn    = form.querySelector('#df-clear-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); form.submit(); } });
        searchInput.addEventListener('input', () => {
            if (clearBtn) clearBtn.style.display = searchInput.value ? 'flex' : 'none';
        });
    }
});
</script>
@endpush
