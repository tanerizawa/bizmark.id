@extends('layouts.admin')
@section('title', 'Buat Campaign Baru')
@section('page-title', 'Buat Campaign')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 22px;position:relative;overflow:hidden">
        <div style="position:absolute;width:192px;height:192px;border-radius:50%;top:-40px;right:-24px;background:color-mix(in srgb,var(--apple-blue) 20%,transparent);filter:blur(48px);pointer-events:none"></div>
        <div style="position:absolute;width:128px;height:128px;border-radius:50%;bottom:-40px;left:32px;background:color-mix(in srgb,var(--apple-purple) 12%,transparent);filter:blur(48px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Management</p>
                <h1 style="font-size:1.2rem;font-weight:700;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:8px">
                    <i class="fas fa-paper-plane" style="color:var(--apple-blue);font-size:0.9rem"></i>Buat Campaign Email Baru
                </h1>
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Isi detail campaign, tentukan penerima, dan kirim atau jadwalkan pengiriman.</p>
            </div>
            <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none"
               onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
    <div style="display:flex;align-items:flex-start;gap:10px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 18%,transparent);border-radius:10px;padding:12px 16px">
        <i class="fas fa-exclamation-circle" style="color:var(--apple-red);flex-shrink:0;margin-top:2px"></i>
        <div>
            <p style="font-size:0.82rem;font-weight:600;color:var(--apple-red);margin:0 0 4px">Terdapat kesalahan pada formulir:</p>
            <ul style="margin:0;padding-left:16px;display:flex;flex-direction:column;gap:2px">
                @foreach($errors->all() as $error)
                <li style="font-size:0.8rem;color:color-mix(in srgb,var(--apple-red) 85%,transparent)">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 18%,transparent);border-radius:10px;padding:12px 16px;color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i>
        <span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Main Form --}}
    <form action="{{ route('admin.campaigns.store') }}" method="POST" id="campaignForm">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

            {{-- Left: Main Content --}}
            <div style="display:flex;flex-direction:column;gap:16px">

                {{-- Basic Information --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                    <div style="display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--dark-separator);margin-bottom:16px">
                        <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-info-circle" style="color:var(--apple-blue);font-size:0.75rem"></i>
                        </div>
                        <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Dasar</h2>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:14px">

                        {{-- Campaign Name --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Nama Campaign <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   placeholder="cth. Newsletter Bulanan — April 2026" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        {{-- Subject --}}
                        <div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
                                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary)">
                                    Subjek Email <span style="color:var(--apple-red)">*</span>
                                </label>
                                <span id="subject_count" style="font-size:0.65rem;color:var(--dark-text-secondary);opacity:.5">0 / 80</span>
                            </div>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                   placeholder="cth. 🎉 Update Terbaru dari Bizmark.ID" maxlength="255" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                   oninput="updateSubjectCount(this)">
                            @error('subject')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7;display:flex;align-items:center;gap:4px">
                                <i class="fas fa-lightbulb" style="color:var(--apple-orange);opacity:.8"></i>
                                Gunakan emoji dan tag untuk meningkatkan open rate. Optimal &le; 80 karakter.
                            </p>
                        </div>

                        {{-- Template Selection --}}
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Template Email <span style="font-size:0.65rem;font-weight:400;opacity:.6">(opsional)</span>
                            </label>
                            <select id="template_id" name="template_id"
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                    onchange="loadTemplate(this.value)">
                                <option value="">— Pilih Template —</option>
                                @foreach($templates as $template)
                                <option value="{{ $template->id }}" data-content="{{ $template->content }}"
                                        {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->name }} ({{ ucfirst($template->category) }})
                                </option>
                                @endforeach
                            </select>
                            @if($templates->isEmpty())
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">
                                <i class="fas fa-info-circle" style="margin-right:3px"></i>Belum ada template aktif.
                                <a href="{{ route('admin.templates.create') }}" style="color:var(--apple-blue);text-decoration:none">Buat template</a>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Email Content --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:20px 22px">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding-bottom:12px;border-bottom:1px solid var(--dark-separator);margin-bottom:14px">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-code" style="color:var(--apple-purple);font-size:0.75rem"></i>
                            </div>
                            <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Konten Email (HTML)</h2>
                        </div>
                        <button type="button" onclick="previewContent()"
                                style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-secondary);background:none;font-size:0.75rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-eye"></i>Preview
                        </button>
                    </div>

                    {{-- Variable chips --}}
                    @php $varOpen = '{' . '{'; $varClose = '}' . '}'; @endphp
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:10px">
                        <span style="font-size:0.72rem;color:var(--dark-text-secondary);opacity:.6">Sisipkan variabel:</span>
                        @foreach(['name', 'email', 'unsubscribe_url'] as $var)
                        <button type="button" onclick="insertVariable('{{ $var }}')"
                                style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:6px;font-size:0.72rem;font-family:'Courier New',Consolas,monospace;font-weight:600;cursor:pointer;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 18%,transparent);color:var(--apple-blue);white-space:nowrap"
                                onmouseover="this.style.opacity='.75'" onmouseout="this.style.opacity='1'">
                            {{ $varOpen . $var . $varClose }}
                        </button>
                        @endforeach
                    </div>

                    <textarea id="content" name="content" rows="18" required
                              placeholder="Tulis konten HTML email di sini..."
                              style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.8rem;outline:none;font-family:'Courier New',Consolas,monospace;line-height:1.6;resize:vertical;min-height:380px;box-sizing:border-box"
                              onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('content') }}</textarea>
                    @error('content')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:6px 0 0;opacity:.7">
                        <i class="fas fa-code" style="margin-right:3px"></i>Gunakan HTML penuh dengan inline CSS. Variabel tersedia: {{ $varOpen }}name{{ $varClose }} {{ $varOpen }}email{{ $varClose }} {{ $varOpen }}unsubscribe_url{{ $varClose }}
                    </p>
                </div>
            </div>

            {{-- Right: Settings Sidebar --}}
            <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:16px">

                {{-- Recipients --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 18px">
                    <div style="display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid var(--dark-separator);margin-bottom:12px">
                        <div style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-users" style="color:var(--apple-green);font-size:0.72rem"></i>
                        </div>
                        <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Penerima</h3>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Kirim Ke</label>
                            <select id="recipient_type" name="recipient_type" required
                                    style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'"
                                    onchange="toggleTagsField()">
                                <option value="all" {{ old('recipient_type') == 'all' ? 'selected' : '' }}>Semua Subscriber</option>
                                <option value="active" {{ old('recipient_type') == 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                                <option value="tags" {{ old('recipient_type') == 'tags' ? 'selected' : '' }}>Filter by Tag</option>
                            </select>
                        </div>
                        <div id="tags_field" style="display:none">
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tags</label>
                            <input type="text" id="recipient_tags" name="recipient_tags" value="{{ old('recipient_tags') }}"
                                   placeholder="cth. customer, vip, prospect"
                                   style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-green)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.6">Pisahkan dengan koma</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;background:color-mix(in srgb,var(--apple-green) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 14%,transparent);border-radius:10px;padding:10px 12px">
                            <i class="fas fa-user-check" style="color:var(--apple-green);font-size:0.88rem;flex-shrink:0"></i>
                            <div>
                                <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">Estimasi penerima</p>
                                <p style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">
                                    <span id="estimated_recipients">{{ $activeSubscribers }}</span>
                                    <span style="font-size:0.72rem;font-weight:400;color:var(--dark-text-secondary)"> subscriber</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Schedule --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 18px">
                    <div style="display:flex;align-items:center;gap:8px;padding-bottom:10px;border-bottom:1px solid var(--dark-separator);margin-bottom:12px">
                        <div style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i class="fas fa-clock" style="color:var(--apple-orange);font-size:0.72rem"></i>
                        </div>
                        <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Jadwal Pengiriman</h3>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <label id="opt_now_wrapper" style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:9px;cursor:pointer;border:1px solid var(--dark-separator);background:color-mix(in srgb,var(--apple-blue) 10%,transparent)">
                            <input type="radio" name="schedule_type" id="send_now" value="now" checked
                                   style="accent-color:var(--apple-blue);width:14px;height:14px;flex-shrink:0"
                                   onchange="toggleScheduleField()">
                            <div>
                                <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;line-height:1.2">Kirim Sekarang</p>
                                <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">Langsung setelah disimpan</p>
                            </div>
                        </label>
                        <label id="opt_later_wrapper" style="display:flex;align-items:center;gap:10px;padding:10px;border-radius:9px;cursor:pointer;border:1px solid var(--dark-separator);background:rgba(255,255,255,0.03)">
                            <input type="radio" name="schedule_type" id="schedule_later" value="later"
                                   style="accent-color:var(--apple-blue);width:14px;height:14px;flex-shrink:0"
                                   onchange="toggleScheduleField()">
                            <div>
                                <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0;line-height:1.2">Jadwalkan</p>
                                <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0">Pilih tanggal &amp; waktu</p>
                            </div>
                        </label>
                    </div>
                    <div id="schedule_field" style="display:none;margin-top:10px">
                        <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Tanggal &amp; Waktu</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}"
                               style="width:100%;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box"
                               onfocus="this.style.borderColor='var(--apple-orange)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.6">Harus di masa yang akan datang</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 18px;display:flex;flex-direction:column;gap:8px">
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 10px;padding-bottom:10px;border-bottom:1px solid var(--dark-separator)">Simpan Campaign</h3>
                    <button type="submit" name="action" value="send" id="btn_send"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;border-radius:10px;font-size:0.88rem;font-weight:600;cursor:pointer;border:none;background:linear-gradient(135deg,var(--apple-blue),#0051D5);color:#fff;box-shadow:0 2px 12px rgba(0,122,255,.35)"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-paper-plane"></i><span>Buat &amp; Kirim</span>
                    </button>
                    <button type="submit" name="action" value="draft"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;border-radius:10px;font-size:0.88rem;font-weight:600;cursor:pointer;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.09);color:var(--dark-text-secondary)"
                            onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                        <i class="fas fa-save"></i><span>Simpan sebagai Draft</span>
                    </button>
                    <a href="{{ route('admin.email-management.index', ['tab' => 'campaigns']) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:10px;border-radius:10px;font-size:0.88rem;font-weight:600;cursor:pointer;background:transparent;border:1px solid rgba(255,255,255,0.05);color:rgba(235,235,245,0.45);text-decoration:none;box-sizing:border-box"
                       onmouseover="this.style.color='var(--apple-red)'" onmouseout="this.style.color='rgba(235,235,245,0.45)'">
                        <i class="fas fa-times"></i><span>Batal</span>
                    </a>
                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);opacity:.5;margin:2px 0 0">
                        <i class="fas fa-info-circle" style="margin-right:3px"></i>"Buat &amp; Kirim" akan langsung mengarahkan ke halaman konfirmasi pengiriman.
                    </p>
                </div>
            </div>
        </div>
    </form>

    {{-- Preview Modal (pure JS) --}}
    <div id="previewModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
        <div style="position:relative;width:100%;max-width:900px;border-radius:14px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.6);background:var(--dark-bg-secondary);border:1px solid var(--dark-separator)">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                <div style="display:flex;align-items:center;gap:8px">
                    <i class="fas fa-eye" style="color:var(--apple-blue);font-size:0.8rem"></i>
                    <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Preview Email</h5>
                </div>
                <button onclick="closePreview()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.2rem;cursor:pointer;padding:0;line-height:1"
                        onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">&times;</button>
            </div>
            <div style="display:flex;align-items:center;gap:10px;padding:10px 18px;border-bottom:1px solid var(--dark-separator);background:rgba(255,255,255,0.04)">
                <span style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--dark-text-secondary)">Subjek:</span>
                <span id="preview_subject" style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary)"></span>
            </div>
            <div style="max-height:70vh;overflow-y:auto;background:#fff">
                <div id="preview_content" style="min-height:200px"></div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
#opt_now_wrapper:has(input:checked) { background: color-mix(in srgb,var(--apple-blue) 10%,transparent); border-color: color-mix(in srgb,var(--apple-blue) 22%,transparent); }
#opt_later_wrapper:has(input:checked) { background: color-mix(in srgb,var(--apple-blue) 10%,transparent); border-color: color-mix(in srgb,var(--apple-blue) 22%,transparent); }
.subject-count-warn { color: var(--apple-orange) !important; opacity:1 !important; }
.subject-count-danger { color: var(--apple-red) !important; opacity:1 !important; }
</style>
@endpush

@push('scripts')
<script>
function loadTemplate(templateId) {
    if (!templateId) return;
    const option = document.querySelector('#template_id option[value="' + templateId + '"]');
    if (option) {
        const content = option.getAttribute('data-content');
        if (content) document.getElementById('content').value = content;
    }
}

function insertVariable(varName) {
    const textarea = document.getElementById('content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    const insertion = '{' + '{' + varName + '}' + '}';
    textarea.value = text.substring(0, start) + insertion + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + insertion.length;
    textarea.focus();
}

function updateSubjectCount(input) {
    const len = input.value.length;
    const counter = document.getElementById('subject_count');
    counter.textContent = len + ' / 80';
    counter.className = '';
    if (len > 80) counter.classList.add('subject-count-danger');
    else if (len > 60) counter.classList.add('subject-count-warn');
    else { counter.style.color = ''; counter.style.opacity = '.5'; }
}

function toggleTagsField() {
    const recipientType = document.getElementById('recipient_type').value;
    document.getElementById('tags_field').style.display = recipientType === 'tags' ? 'block' : 'none';
}

function toggleScheduleField() {
    const scheduleType = document.querySelector('input[name="schedule_type"]:checked').value;
    const scheduleField = document.getElementById('schedule_field');
    const btnSend = document.getElementById('btn_send');
    scheduleField.style.display = scheduleType === 'later' ? 'block' : 'none';
    if (scheduleType === 'later') {
        btnSend.querySelector('span').textContent = 'Jadwalkan Campaign';
        btnSend.querySelector('i').className = 'fas fa-calendar-check';
        btnSend.setAttribute('value', 'draft');
    } else {
        btnSend.querySelector('span').innerHTML = 'Buat &amp; Kirim';
        btnSend.querySelector('i').className = 'fas fa-paper-plane';
        btnSend.setAttribute('value', 'send');
    }
}

function previewContent() {
    const subject = document.getElementById('subject').value;
    const content = document.getElementById('content').value;
    document.getElementById('preview_subject').textContent = subject || '(Belum ada subjek)';
    document.getElementById('preview_content').innerHTML = content || '<p style="color:#999; padding: 2rem; text-align:center;">Belum ada konten</p>';
    const modal = document.getElementById('previewModal');
    modal.style.display = 'flex';
    document.addEventListener('keydown', handlePreviewEsc);
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    document.removeEventListener('keydown', handlePreviewEsc);
}

function handlePreviewEsc(e) { if (e.key === 'Escape') closePreview(); }

document.getElementById('previewModal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

document.addEventListener('DOMContentLoaded', function () {
    toggleTagsField();
    const subjectInput = document.getElementById('subject');
    if (subjectInput && subjectInput.value) updateSubjectCount(subjectInput);
});
</script>
@endpush
@endsection
