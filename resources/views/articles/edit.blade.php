@extends('layouts.admin')

@section('title', 'Edit Artikel')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <p style="font-size:0.7rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Edit Artikel</p>
            <h1 style="font-size:1.15rem;font-weight:800;color:var(--dark-text-primary);margin:0"><i class="fas fa-edit" style="margin-right:8px;color:var(--apple-blue)"></i>{{ Str::limit($article->title, 50) }}</h1>
        </div>
        <a href="{{ route('articles.index') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none;background:rgba(255,255,255,.04)" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
        </a>
    </div>

    {{-- Form --}}
    <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start">

            {{-- Main Content --}}
            <div style="display:flex;flex-direction:column;gap:14px">

                {{-- Title --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <label for="title" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Judul Artikel *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $article->title) }}" required
                           style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('title') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:9px;color:var(--dark-text-primary);font-size:0.9rem;outline:none;box-sizing:border-box">
                    @error('title')<p style="margin:6px 0 0;font-size:0.78rem;color:var(--apple-red)">{{ $message }}</p>@enderror
                </div>

                {{-- Excerpt --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <label for="excerpt" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="3"
                              style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('excerpt') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;resize:vertical">{{ old('excerpt', $article->excerpt) }}</textarea>
                    <p style="margin:6px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Ringkasan singkat artikel (opsional, akan di-generate otomatis jika kosong)</p>
                    @error('excerpt')<p style="margin:4px 0 0;font-size:0.78rem;color:var(--apple-red)">{{ $message }}</p>@enderror
                </div>

                {{-- Content --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <label for="content" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:8px">Konten Artikel *</label>
                    <div class="ckeditor-wrapper">
                        <textarea name="content" id="content" style="width:100%">{{ old('content', $article->content) }}</textarea>
                    </div>
                    @error('content')<p style="margin:6px 0 0;font-size:0.78rem;color:var(--apple-red)">{{ $message }}</p>@enderror
                </div>

                {{-- SEO --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
                        <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-search" style="font-size:0.65rem;color:var(--apple-blue)"></i></div>
                        <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">SEO Settings</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div>
                            <label for="meta_title" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $article->meta_title) }}" maxlength="60"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                            <p style="margin:4px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Rekomendasi: 50-60 karakter</p>
                        </div>
                        <div>
                            <label for="meta_description" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="2" maxlength="160"
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box;resize:vertical">{{ old('meta_description', $article->meta_description) }}</textarea>
                            <p style="margin:4px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Rekomendasi: 150-160 karakter</p>
                        </div>
                        <div>
                            <label for="meta_keywords" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords', $article->meta_keywords) }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                            <p style="margin:4px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Pisahkan dengan koma (contoh: lb3, amdal, lingkungan)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div style="display:flex;flex-direction:column;gap:14px">

                {{-- Publish Settings --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                        <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-cog" style="font-size:0.65rem;color:var(--apple-green)"></i></div>
                        <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Pengaturan Publikasi</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div>
                            <label for="status" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Status *</label>
                            <select name="status" id="status" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                                <option value="draft" {{ old('status',$article->status)=='draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status',$article->status)=='published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status',$article->status)=='archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div>
                            <label for="published_at" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Tanggal Publikasi</label>
                            <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                            <p style="margin:4px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Kosongkan untuk publikasi sekarang</p>
                        </div>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured',$article->is_featured) ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--apple-blue)">
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">Jadikan artikel unggulan</span>
                        </label>
                    </div>
                </div>

                {{-- Featured Image --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                        <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-image" style="font-size:0.65rem;color:var(--apple-purple)"></i></div>
                        <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Featured Image</span>
                    </div>
                    @if($article->featured_image_url)
                    <div id="current-image" style="margin-bottom:12px">
                        <img src="{{ $article->featured_image_url }}" alt="Current image" style="width:100%;border-radius:8px;margin-bottom:6px;display:block">
                        <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">Gambar saat ini</p>
                    </div>
                    @endif
                    <div id="image-preview" class="hidden" style="margin-bottom:12px">
                        <img src="" alt="Preview" style="width:100%;border-radius:8px;display:block">
                        <button type="button" id="remove-image" style="margin-top:8px;font-size:0.78rem;color:var(--apple-red);background:transparent;border:none;cursor:pointer;padding:0">
                            <i class="fas fa-times" style="margin-right:4px"></i>Hapus gambar baru
                        </button>
                    </div>
                    <input type="file" name="featured_image" id="featured_image" accept="image/*"
                           style="width:100%;padding:8px;background:var(--dark-bg-tertiary);border:1px solid {{ $errors->has('featured_image') ? 'var(--apple-red)' : 'var(--dark-separator)' }};border-radius:9px;color:var(--dark-text-primary);font-size:0.82rem;box-sizing:border-box">
                    <input type="hidden" name="pexels_image_path" id="pexels_image_path">
                    <p style="margin:6px 0 10px;font-size:0.72rem;color:var(--dark-text-tertiary)">Format: JPG, PNG, GIF (max 2MB)</p>
                    <button type="button" id="browse-pexels"
                            style="width:100%;padding:8px;border-radius:9px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);font-size:0.82rem;font-weight:600;cursor:pointer;box-sizing:border-box;display:flex;align-items:center;justify-content:center;gap:6px"
                            onmouseover="this.style.background='rgba(255,255,255,.08)'" onmouseout="this.style.background='var(--dark-bg-tertiary)'">
                        <i class="fas fa-search" style="font-size:0.75rem"></i>Cari dari Pexels
                    </button>
                    @error('featured_image')<p style="margin:6px 0 0;font-size:0.78rem;color:var(--apple-red)">{{ $message }}</p>@enderror
                </div>

                {{-- Category & Tags --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                        <div style="width:24px;height:24px;border-radius:7px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:flex;align-items:center;justify-content:center"><i class="fas fa-tags" style="font-size:0.65rem;color:var(--apple-orange)"></i></div>
                        <span style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary)">Kategori & Tag</span>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div>
                            <label for="category" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Kategori *</label>
                            <select name="category" id="category" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                                @foreach($categories as $key => $label)
                                <option value="{{ $key }}" {{ old('category',$article->category)==$key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tags-input" style="display:block;font-size:0.82rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:6px">Tags</label>
                            <input type="text" id="tags-input" placeholder="Ketik tag dan tekan Enter"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box">
                            <div id="tags-container" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px"></div>
                            <p style="margin:6px 0 0;font-size:0.72rem;color:var(--dark-text-tertiary)">Tekan Enter untuk menambah tag</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px">
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <button type="submit" style="width:100%;padding:10px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;box-sizing:border-box" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-save" style="margin-right:6px"></i>Update Artikel
                        </button>
                        <a href="{{ route('articles.index') }}" style="display:block;width:100%;padding:10px;border-radius:10px;background:var(--dark-bg-tertiary);color:var(--dark-text-primary);text-align:center;font-size:0.85rem;font-weight:600;text-decoration:none;box-sizing:border-box" onmouseover="this.style.background='rgba(255,255,255,.08)'" onmouseout="this.style.background='var(--dark-bg-tertiary)'">
                            <i class="fas fa-times" style="margin-right:6px"></i>Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Pexels Modal --}}
<div id="pexels-modal" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:50;display:none;align-items:center;justify-content:center;padding:16px">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;width:100%;max-width:900px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column">
        <div style="padding:20px;border-bottom:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
                <h2 style="font-size:1.1rem;font-weight:700;color:var(--dark-text-primary);margin:0">
                    <i class="fas fa-images" style="margin-right:8px;color:var(--apple-purple)"></i>Cari Gambar dari Pexels
                </h2>
                <button type="button" id="close-pexels-modal" style="background:transparent;border:none;cursor:pointer;color:var(--dark-text-tertiary);font-size:1.1rem;padding:4px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-tertiary)'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div style="display:flex;gap:10px;margin-bottom:10px">
                <input type="text" id="pexels-search-input" placeholder="Cari gambar (contoh: nature, business, technology)"
                       style="flex:1;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none">
                <button type="button" id="pexels-search-btn" style="padding:9px 18px;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer;white-space:nowrap" onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-search" style="margin-right:6px"></i>Cari
                </button>
            </div>
            <div style="display:flex;gap:10px">
                <select id="pexels-orientation" style="flex:1;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.82rem;outline:none">
                    <option value="">Semua Orientasi</option>
                    <option value="landscape">Landscape</option>
                    <option value="portrait">Portrait</option>
                    <option value="square">Square</option>
                </select>
                <select id="pexels-size" style="flex:1;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.82rem;outline:none">
                    <option value="">Semua Ukuran</option>
                    <option value="large">Large (24MP)</option>
                    <option value="medium">Medium (12MP)</option>
                    <option value="small">Small (4MP)</option>
                </select>
            </div>
        </div>
        <div style="flex:1;overflow-y:auto;padding:20px">
            <div id="pexels-loading" class="hidden" style="text-align:center;padding:48px 0">
                <i class="fas fa-spinner fa-spin" style="font-size:2.5rem;color:var(--apple-blue);display:block;margin-bottom:12px"></i>
                <p style="color:var(--dark-text-secondary);margin:0">Mencari gambar...</p>
            </div>
            <div id="pexels-empty" style="text-align:center;padding:48px 0">
                <i class="fas fa-search" style="font-size:3rem;color:var(--dark-text-tertiary);display:block;margin-bottom:12px"></i>
                <p style="color:var(--dark-text-secondary);margin:0 0 6px">Ketik kata kunci dan tekan Cari untuk menemukan gambar</p>
                <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">Foto gratis dari Pexels.com</p>
            </div>
            <div id="pexels-error" class="hidden" style="text-align:center;padding:48px 0">
                <i class="fas fa-exclamation-triangle" style="font-size:3rem;color:var(--apple-red);display:block;margin-bottom:12px"></i>
                <p id="pexels-error-message" style="color:var(--dark-text-secondary);margin:0">Terjadi kesalahan</p>
            </div>
            <div id="pexels-results" class="hidden">
                <div id="pexels-grid"></div>
                <div style="margin-top:20px;display:flex;align-items:center;justify-content:center;gap:10px">
                    <button type="button" id="pexels-prev" style="padding:8px 16px;border-radius:9px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);font-size:0.82rem;font-weight:600;cursor:pointer" disabled><i class="fas fa-chevron-left" style="margin-right:4px"></i>Sebelumnya</button>
                    <span style="color:var(--dark-text-secondary);font-size:0.82rem">Halaman <span id="pexels-current-page">1</span></span>
                    <button type="button" id="pexels-next" style="padding:8px 16px;border-radius:9px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);color:var(--dark-text-primary);font-size:0.82rem;font-weight:600;cursor:pointer" disabled>Berikutnya<i class="fas fa-chevron-right" style="margin-left:4px"></i></button>
                </div>
            </div>
        </div>
        <div style="padding:12px 20px;border-top:1px solid var(--dark-separator);background:var(--dark-bg-tertiary);text-align:center">
            <p style="font-size:0.72rem;color:var(--dark-text-tertiary);margin:0">
                Foto disediakan oleh <a href="https://www.pexels.com" target="_blank" style="color:var(--apple-blue)">Pexels</a> •
                Gratis untuk digunakan sesuai <a href="https://www.pexels.com/license/" target="_blank" style="color:var(--apple-blue)">Lisensi Pexels</a>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
@push('styles')
<style>
    .hidden { display:none !important; }
    .ckeditor-wrapper .ck-editor__editable { min-height:500px;background-color:#1c1c1e !important;color:#f5f5f7 !important; }
    .ck.ck-editor__main > .ck-editor__editable { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck.ck-toolbar { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-button:not(.ck-disabled):hover,.ck.ck-button:not(.ck-disabled):active { background-color:#3a3a3c !important; }
    .ck.ck-button.ck-on { background-color:#0a84ff !important;color:white !important; }
    .ck.ck-dropdown__panel { background-color:#2c2c2e !important;border-color:#38383a !important; }
    .ck.ck-list__item:hover { background-color:#3a3a3c !important; }
    .ck.ck-labeled-field-view>.ck-labeled-field-view__input-wrapper>.ck-input { background-color:#1c1c1e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
    .ck-content h1,.ck-content h2,.ck-content h3,.ck-content h4,.ck-content h5,.ck-content h6 { color:#f5f5f7 !important; }
    .ck-content a { color:#0a84ff !important; }
    .ck-content blockquote { border-left-color:#0a84ff !important; }
    .ck-content code { background-color:#2c2c2e !important;color:#ff453a !important; }
    .ck-content pre { background-color:#2c2c2e !important;color:#f5f5f7 !important;border-color:#38383a !important; }
</style>
@endpush
<script>
    class MyUploadAdapter {
        constructor(loader) { this.loader = loader; }
        upload() {
            return this.loader.file.then(file => new Promise((resolve, reject) => {
                const data = new FormData();
                data.append('image', file);
                fetch('{{ route("articles.upload-image") }}', { method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}, body:data })
                .then(r => r.json())
                .then(result => { if (result.success) resolve({default:result.url}); else reject(result.message||'Upload failed'); })
                .catch(error => reject('Upload failed: '+error));
            }));
        }
        abort() {}
    }
    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new MyUploadAdapter(loader);
    }

    let editorInstance;
    ClassicEditor.create(document.querySelector('#content'), {
        extraPlugins:[MyCustomUploadAdapterPlugin],
        toolbar:['heading','|','bold','italic','|','link','uploadImage','blockQuote','insertTable','|','bulletedList','numberedList','|','undo','redo'],
        image:{toolbar:['imageTextAlternative','linkImage']},
        table:{contentToolbar:['tableColumn','tableRow','mergeTableCells']},
        link:{addTargetToExternalLinks:true}
    }).then(editor => {
        editorInstance = editor;
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('#content').value = editorInstance.getData();
        });
    }).catch(err => console.error(err));

    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = e.target.result;
                preview.classList.remove('hidden'); preview.style.display = 'block';
                const cur = document.getElementById('current-image');
                if (cur) { cur.classList.add('hidden'); cur.style.display = 'none'; }
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('remove-image')?.addEventListener('click', function() {
        document.getElementById('featured_image').value = '';
        const preview = document.getElementById('image-preview');
        preview.classList.add('hidden'); preview.style.display = 'none';
        const cur = document.getElementById('current-image');
        if (cur) { cur.classList.remove('hidden'); cur.style.display = 'block'; }
    });

    let tags = @json(old('tags', $article->tags ?? []));
    const tagsInput = document.getElementById('tags-input');
    const tagsContainer = document.getElementById('tags-container');
    renderTags();
    tagsInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tag = this.value.trim();
            if (tag && !tags.includes(tag)) { tags.push(tag); renderTags(); this.value = ''; }
        }
    });
    function renderTags() {
        tagsContainer.innerHTML = '';
        tags.forEach((tag, index) => {
            const tagEl = document.createElement('span');
            tagEl.style.cssText = 'display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:0.75rem;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)';
            tagEl.innerHTML = `${tag}<button type="button" onclick="removeTag(${index})" style="margin-left:6px;background:transparent;border:none;cursor:pointer;color:var(--apple-blue);padding:0;font-size:0.75rem"><i class="fas fa-times"></i></button><input type="hidden" name="tags[]" value="${tag}">`;
            tagsContainer.appendChild(tagEl);
        });
    }
    function removeTag(index) { tags.splice(index, 1); renderTags(); }

    // Pexels Integration
    let currentPexelsPage = 1, currentPexelsQuery = '', pexelsHasMore = false;
    const pexelsModal = document.getElementById('pexels-modal');
    const browsePexelsBtn = document.getElementById('browse-pexels');
    const closePexelsBtn = document.getElementById('close-pexels-modal');
    const pexelsSearchInput = document.getElementById('pexels-search-input');
    const pexelsSearchBtn = document.getElementById('pexels-search-btn');
    const pexelsOrientation = document.getElementById('pexels-orientation');
    const pexelsSize = document.getElementById('pexels-size');
    const pexelsLoading = document.getElementById('pexels-loading');
    const pexelsEmpty = document.getElementById('pexels-empty');
    const pexelsError = document.getElementById('pexels-error');
    const pexelsErrorMessage = document.getElementById('pexels-error-message');
    const pexelsResults = document.getElementById('pexels-results');
    const pexelsGrid = document.getElementById('pexels-grid');
    const pexelsPrevBtn = document.getElementById('pexels-prev');
    const pexelsNextBtn = document.getElementById('pexels-next');
    const pexelsCurrentPageSpan = document.getElementById('pexels-current-page');

    browsePexelsBtn.addEventListener('click', function() {
        pexelsModal.classList.remove('hidden'); pexelsModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        if (!currentPexelsQuery) loadCuratedPhotos();
    });
    closePexelsBtn.addEventListener('click', closePexelsModal);
    pexelsModal.addEventListener('click', function(e) { if (e.target === pexelsModal) closePexelsModal(); });
    function closePexelsModal() { pexelsModal.classList.add('hidden'); pexelsModal.style.display = 'none'; document.body.style.overflow = ''; }
    pexelsSearchBtn.addEventListener('click', performSearch);
    pexelsSearchInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') performSearch(); });
    function performSearch() {
        const query = pexelsSearchInput.value.trim();
        if (query.length < 2) { showPexelsError('Masukkan minimal 2 karakter'); return; }
        currentPexelsQuery = query; currentPexelsPage = 1; searchPexels();
    }
    function searchPexels() {
        showLoading();
        const params = new URLSearchParams({query:currentPexelsQuery,page:currentPexelsPage,per_page:20});
        if (pexelsOrientation.value) params.append('orientation',pexelsOrientation.value);
        if (pexelsSize.value) params.append('size',pexelsSize.value);
        fetch(`{{ route('pexels.search') }}?${params}`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.json()).then(data=>{ if(data.success) displayResults(data.data); else showPexelsError(data.message||'Gagal mencari foto'); })
        .catch(()=>showPexelsError('Terjadi kesalahan saat mencari foto'));
    }
    function loadCuratedPhotos() {
        showLoading();
        fetch(`{{ route('pexels.curated') }}?page=${currentPexelsPage}&per_page=20`,{headers:{'X-Requested-With':'XMLHttpRequest'}})
        .then(r=>r.json()).then(data=>{ if(data.success) displayResults(data.data); else showPexelsError(data.message||'Gagal memuat foto'); })
        .catch(()=>showPexelsError('Terjadi kesalahan saat memuat foto'));
    }
    function displayResults(data) {
        pexelsLoading.classList.add('hidden'); pexelsEmpty.classList.add('hidden'); pexelsError.classList.add('hidden'); pexelsResults.classList.remove('hidden');
        pexelsGrid.innerHTML = '';
        if (!data.photos || data.photos.length === 0) { showEmpty(); return; }
        pexelsGrid.style.cssText = 'display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;';
        if (window.innerWidth >= 768) pexelsGrid.style.gridTemplateColumns = 'repeat(3,1fr)';
        if (window.innerWidth >= 1024) pexelsGrid.style.gridTemplateColumns = 'repeat(4,1fr)';
        data.photos.forEach(photo => pexelsGrid.appendChild(createPhotoCard(photo)));
        pexelsCurrentPageSpan.textContent = data.page;
        pexelsPrevBtn.disabled = data.page <= 1; pexelsNextBtn.disabled = !data.next_page; pexelsHasMore = !!data.next_page;
    }
    function createPhotoCard(photo) {
        const div = document.createElement('div');
        div.style.cssText = 'aspect-ratio:1;min-height:200px;height:200px;width:100%;background-color:#2c2c2e;border-radius:8px;overflow:hidden;display:block;position:relative;cursor:pointer;';
        const img = document.createElement('img');
        img.src = photo.src.medium; img.alt = photo.alt||'Photo from Pexels';
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;display:block;'; img.loading = 'eager';
        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:absolute;inset:0;background-color:rgba(0,0,0,0);display:flex;align-items:center;justify-content:center;transition:background-color 0.3s;';
        overlay.innerHTML = '<button type="button" style="opacity:0;padding:.5rem 1rem;background-color:#0a84ff;color:white;border-radius:8px;font-size:.875rem;transition:opacity 0.3s;border:none;cursor:pointer;"><i class="fas fa-check" style="margin-right:.25rem;"></i>Pilih</button>';
        const credit = document.createElement('div');
        credit.style.cssText = 'position:absolute;bottom:0;left:0;right:0;padding:.5rem;background:linear-gradient(to top,rgba(0,0,0,.8),transparent);opacity:0;transition:opacity 0.3s;';
        credit.innerHTML = `<p style="color:white;font-size:.75rem;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">oleh ${photo.photographer}</p>`;
        div.addEventListener('mouseenter',()=>{ overlay.style.backgroundColor='rgba(0,0,0,.5)'; overlay.querySelector('button').style.opacity='1'; credit.style.opacity='1'; });
        div.addEventListener('mouseleave',()=>{ overlay.style.backgroundColor='rgba(0,0,0,0)'; overlay.querySelector('button').style.opacity='0'; credit.style.opacity='0'; });
        div.appendChild(img); div.appendChild(overlay); div.appendChild(credit);
        div.addEventListener('click',()=>selectPhoto(photo));
        return div;
    }
    function selectPhoto(photo) {
        const loadingDiv = document.createElement('div');
        loadingDiv.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:60;display:flex;align-items:center;justify-content:center;';
        loadingDiv.innerHTML = '<div style="text-align:center"><i class="fas fa-spinner fa-spin" style="font-size:3rem;color:#0a84ff;display:block;margin-bottom:12px"></i><p style="color:white;font-size:1rem;margin:0">Mengunduh foto dari Pexels...</p></div>';
        document.body.appendChild(loadingDiv);
        fetch('{{ route('pexels.download') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({photo_id:photo.id,photo_url:photo.src.large2x,photographer_name:photo.photographer,photographer_url:photo.photographer_url,pexels_url:photo.url})})
        .then(r=>r.json())
        .then(data=>{
            document.body.removeChild(loadingDiv);
            if (data.success) {
                const preview = document.getElementById('image-preview');
                preview.querySelector('img').src = data.url; preview.classList.remove('hidden'); preview.style.display = 'block';
                const cur = document.getElementById('current-image');
                if (cur) { cur.classList.add('hidden'); cur.style.display = 'none'; }
                document.getElementById('pexels_image_path').value = data.path;
                document.getElementById('featured_image').value = '';
                closePexelsModal(); alert('Foto berhasil dipilih dari Pexels!');
            } else { alert(data.message||'Gagal mengunduh foto'); }
        })
        .catch(()=>{ document.body.removeChild(loadingDiv); alert('Terjadi kesalahan saat mengunduh foto'); });
    }
    pexelsPrevBtn.addEventListener('click',()=>{ if(currentPexelsPage>1){currentPexelsPage--;currentPexelsQuery?searchPexels():loadCuratedPhotos();} });
    pexelsNextBtn.addEventListener('click',()=>{ if(pexelsHasMore){currentPexelsPage++;currentPexelsQuery?searchPexels():loadCuratedPhotos();} });
    function showLoading() { pexelsLoading.classList.remove('hidden');pexelsEmpty.classList.add('hidden');pexelsError.classList.add('hidden');pexelsResults.classList.add('hidden'); }
    function showEmpty() { pexelsLoading.classList.add('hidden');pexelsEmpty.classList.remove('hidden');pexelsError.classList.add('hidden');pexelsResults.classList.add('hidden'); }
    function showPexelsError(msg) { pexelsLoading.classList.add('hidden');pexelsEmpty.classList.add('hidden');pexelsError.classList.remove('hidden');pexelsResults.classList.add('hidden');pexelsErrorMessage.textContent=msg; }
</script>
@endsection
