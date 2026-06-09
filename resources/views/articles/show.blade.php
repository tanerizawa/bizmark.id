@extends('layouts.admin')

@section('title', $article->title)

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Preview Artikel</p>
            <h1 style="font-size:1.15rem;font-weight:800;color:var(--dark-text-primary);margin:0">{{ Str::limit($article->title, 60) }}</h1>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <a href="{{ route('articles.edit', $article) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-edit" style="font-size:0.75rem"></i>Edit
            </a>
            <a href="{{ route('articles.index') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none;background:rgba(255,255,255,.04)"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start">

        {{-- Main Article --}}
        <div style="display:flex;flex-direction:column;gap:16px">
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;padding:24px">
                @if($article->featured_image_url)
                <img src="{{ $article->featured_image_url }}" alt="{{ $article->title }}" style="width:100%;border-radius:12px;margin-bottom:20px;display:block">
                @endif

                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:6px;margin-bottom:14px">
                    {!! $article->status_badge !!}
                    <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">{{ $article->category_label }}</span>
                    @if($article->is_featured)
                    <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">
                        <i class="fas fa-star" style="margin-right:4px;font-size:0.6rem"></i>Featured
                    </span>
                    @endif
                </div>

                <h1 style="font-size:1.6rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 14px;line-height:1.3">{{ $article->title }}</h1>

                <div style="display:flex;align-items:center;flex-wrap:wrap;gap:16px;font-size:0.78rem;color:var(--dark-text-secondary);margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--dark-separator)">
                    <span><i class="fas fa-user" style="margin-right:5px"></i>{{ $article->author->name }}</span>
                    <span><i class="fas fa-calendar" style="margin-right:5px"></i>{{ $article->published_at ? $article->formatted_published_at : 'Belum dipublikasikan' }}</span>
                    <span><i class="fas fa-clock" style="margin-right:5px"></i>{{ $article->reading_time_text }}</span>
                    <span><i class="fas fa-eye" style="margin-right:5px"></i>{{ number_format($article->views_count) }} views</span>
                </div>

                @if($article->excerpt)
                <div style="font-size:1rem;color:var(--dark-text-secondary);margin-bottom:20px;font-style:italic;border-left:3px solid var(--apple-blue);padding-left:14px">
                    {{ $article->excerpt }}
                </div>
                @endif

                <div class="article-prose">{!! $article->content !!}</div>

                @if($article->tags && count($article->tags) > 0)
                <div style="margin-top:24px;padding-top:20px;border-top:1px solid var(--dark-separator)">
                    <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 10px">Tags:</p>
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        @foreach($article->tags as $tag)
                        <span style="display:inline-flex;padding:4px 12px;border-radius:20px;font-size:0.78rem;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">#{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:14px">

            {{-- Stats --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-chart-line" style="font-size:0.65rem;color:var(--apple-blue)"></i></div>
                    <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Statistik Artikel</span>
                </div>
                <div style="font-size:0.82rem">
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dark-separator)">
                        <span style="color:var(--dark-text-secondary)">Total Views</span>
                        <span style="font-weight:600;color:var(--dark-text-primary)">{{ number_format($article->views_count) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dark-separator)">
                        <span style="color:var(--dark-text-secondary)">Waktu Baca</span>
                        <span style="font-weight:600;color:var(--dark-text-primary)">{{ $article->reading_time }} menit</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dark-separator)">
                        <span style="color:var(--dark-text-secondary)">Dibuat</span>
                        <span style="font-weight:600;color:var(--dark-text-primary)">{{ $article->created_at->format('d M Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:8px 0">
                        <span style="color:var(--dark-text-secondary)">Terakhir Update</span>
                        <span style="font-weight:600;color:var(--dark-text-primary)">{{ $article->updated_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- SEO Info --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-search" style="font-size:0.65rem;color:var(--apple-green)"></i></div>
                    <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">SEO Information</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:12px;font-size:0.82rem">
                    <div>
                        <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em">Meta Title</p>
                        <p style="color:var(--dark-text-primary);margin:0">{{ $article->meta_title ?: $article->title }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em">Meta Description</p>
                        <p style="color:var(--dark-text-primary);margin:0">{{ $article->meta_description ?: $article->excerpt }}</p>
                    </div>
                    @if($article->meta_keywords)
                    <div>
                        <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em">Meta Keywords</p>
                        <p style="color:var(--dark-text-primary);margin:0">{{ $article->meta_keywords }}</p>
                    </div>
                    @endif
                    <div>
                        <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin:0 0 4px;text-transform:uppercase;letter-spacing:.06em">Slug</p>
                        <p style="color:var(--apple-blue);margin:0;font-family:monospace;font-size:0.78rem;word-break:break-all">{{ $article->slug }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-bolt" style="font-size:0.65rem;color:var(--apple-orange)"></i></div>
                    <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Quick Actions</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px">
                    @if($article->status == 'draft')
                    <form action="{{ route('articles.publish', $article) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:9px;border-radius:10px;background:var(--apple-green);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-check-circle" style="margin-right:6px"></i>Publikasikan
                        </button>
                    </form>
                    @elseif($article->status == 'published')
                    <form action="{{ route('articles.unpublish', $article) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:9px;border-radius:10px;background:var(--apple-orange);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-pause-circle" style="margin-right:6px"></i>Unpublish
                        </button>
                    </form>
                    @endif

                    @if($article->status != 'archived')
                    <form action="{{ route('articles.archive', $article) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:9px;border-radius:10px;background:var(--apple-purple);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-archive" style="margin-right:6px"></i>Arsipkan
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('articles.edit', $article) }}" style="display:block;width:100%;padding:9px;border-radius:10px;background:var(--apple-blue);color:#fff;text-align:center;font-size:0.82rem;font-weight:600;text-decoration:none;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-edit" style="margin-right:6px"></i>Edit Artikel
                    </a>

                    <form action="{{ route('articles.destroy', $article) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width:100%;padding:9px;border-radius:10px;background:var(--apple-red);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-trash" style="margin-right:6px"></i>Hapus Artikel
                        </button>
                    </form>
                </div>
            </div>

            {{-- Author --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                    <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-teal) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-user" style="font-size:0.65rem;color:var(--apple-teal)"></i></div>
                    <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Penulis</span>
                </div>
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--apple-blue);display:flex;align-items:center;justify-content:center;color:#fff;font-size:0.9rem;font-weight:700;flex-shrink:0">
                        {{ strtoupper(substr($article->author->name, 0, 2)) }}
                    </div>
                    <div>
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">{{ $article->author->name }}</p>
                        <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">{{ $article->author->email }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .article-prose { color:var(--dark-text-primary); line-height:1.75; }
    .article-prose h1,.article-prose h2,.article-prose h3,.article-prose h4,.article-prose h5,.article-prose h6 { color:var(--dark-text-primary);font-weight:700;margin-top:1.5em;margin-bottom:.5em; }
    .article-prose h2 { font-size:1.5em; }
    .article-prose h3 { font-size:1.25em; }
    .article-prose p { margin-bottom:1em; }
    .article-prose a { color:var(--apple-blue);text-decoration:underline; }
    .article-prose ul,.article-prose ol { margin-left:1.5em;margin-bottom:1em; }
    .article-prose li { margin-bottom:.5em; }
    .article-prose img { border-radius:.75rem;margin:1.5em 0;max-width:100%; }
    .article-prose blockquote { border-left:4px solid var(--apple-blue);padding-left:1em;margin:1.5em 0;font-style:italic;color:var(--dark-text-secondary); }
    .article-prose code { background:var(--dark-bg-tertiary);padding:.2em .4em;border-radius:.25rem;font-family:'Courier New',monospace;font-size:.9em; }
    .article-prose pre { background:var(--dark-bg-tertiary);padding:1em;border-radius:.5rem;overflow-x:auto;margin:1.5em 0; }
    .article-prose pre code { background:transparent;padding:0; }
</style>
@endpush
