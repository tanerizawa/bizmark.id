@extends('layouts.admin')

@section('title', 'Template & Sesi Tes')

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.index') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Rekrutmen
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-orange);margin:0 0 4px">Manajemen Talenta</p>
            <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 4px;line-height:1.2">Template & Sesi Tes</h1>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">Kelola template penilaian dan pantau sesi tes kandidat</p>
        </div>
        <a href="{{ route('admin.recruitment.tests.create') }}"
           style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.85rem;font-weight:700;text-decoration:none;transition:opacity .2s;align-self:flex-end"
           onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>Buat Template Tes
        </a>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @php $testStats = [
            ['label'=>'Total Template',  'value'=>$stats['total_templates'],  'sub'=>'Seluruh template',      'color'=>'var(--apple-blue)'],
            ['label'=>'Template Aktif',  'value'=>$stats['active_templates'], 'sub'=>'Dapat ditugaskan',      'color'=>'var(--apple-green)'],
            ['label'=>'Sesi Aktif',      'value'=>$stats['active_sessions'],  'sub'=>'Kandidat sedang tes',   'color'=>'var(--apple-orange)'],
            ['label'=>'Selesai Hari Ini','value'=>$stats['completed_today'],  'sub'=>'Tes selesai',           'color'=>'var(--apple-teal)'],
        ]; @endphp
        @foreach($testStats as $s)
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $s['color'] }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $s['color'] }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px">
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:{{ $s['color'] }};opacity:.85;margin:0">{{ $s['label'] }}</p>
            <p style="font-size:1.8rem;font-weight:800;color:{{ $s['color'] }};margin:4px 0 2px;line-height:1">{{ $s['value'] }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $s['sub'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 20px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Pencarian & Filter</p>
                <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Susun Template</h3>
            </div>
            <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $templates->total() }} template</span>
        </div>
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px">
            <div style="position:relative">
                <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.75rem;pointer-events:none"></i>
                <input type="text" id="searchTest" placeholder="Judul, tipe, deskripsi..."
                       style="width:100%;padding:9px 12px 9px 32px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box"
                       onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
            </div>
            <div style="position:relative">
                <select id="filterType"
                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <option value="">Semua Tipe</option>
                    @foreach(['psychology','psychometric','technical','aptitude','personality','document_editing'] as $type)
                    <option value="{{ $type }}">{{ ucfirst(str_replace('_',' ',$type)) }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
            </div>
            <div style="position:relative">
                <select id="filterStatus"
                        style="width:100%;padding:9px 32px 9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;appearance:none"
                        onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <i class="fas fa-chevron-down" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);color:var(--dark-text-secondary);font-size:0.65rem;pointer-events:none"></i>
            </div>
        </div>
    </div>

    {{-- Templates Table --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
        @if($templates->count())
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse" id="templatesTable">
                <thead style="background:var(--dark-bg-secondary)">
                    <tr>
                        @foreach(['Template','Tipe','Pertanyaan','Durasi','Sesi','Status','Aksi'] as $col)
                        <th style="padding:10px 14px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:{{ $col === 'Aksi' ? 'right' : 'left' }};border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    @php
                        $typeColors = ['psychology'=>'var(--apple-blue)','psychometric'=>'var(--apple-yellow)','technical'=>'var(--apple-red)','aptitude'=>'var(--apple-green)','personality'=>'var(--apple-purple)','document_editing'=>'var(--apple-teal)'];
                        $tc = $typeColors[$template->test_type] ?? 'var(--dark-text-secondary)';
                    @endphp
                    <tr class="test-row" style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 14px">
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:700;background:linear-gradient(135deg,color-mix(in srgb,var(--apple-purple) 60%,var(--apple-blue)),var(--apple-blue));color:#fff">
                                    {{ strtoupper(substr($template->title, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="test-title" style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);display:block">{{ $template->title }}</span>
                                    <span style="font-size:0.7rem;color:var(--dark-text-secondary)">Dibuat {{ $template->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 14px">
                            <span class="test-type" style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $tc }} 15%,transparent);color:{{ $tc }}">{{ ucfirst(str_replace('_',' ',$template->test_type)) }}</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ $template->total_questions }} pertanyaan</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ $template->duration_minutes }} menit</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ $template->test_sessions_count }} sesi</span>
                        </td>
                        <td style="padding:12px 14px">
                            <span class="test-status" style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $template->is_active ? 'var(--apple-green)' : 'var(--dark-text-secondary)' }} 15%,transparent);color:{{ $template->is_active ? 'var(--apple-green)' : 'var(--dark-text-secondary)' }}">{{ $template->is_active ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td style="padding:12px 14px;text-align:right">
                            <div style="display:flex;justify-content:flex-end;gap:6px">
                                <a href="{{ route('admin.recruitment.tests.show', $template) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-teal);background:color-mix(in srgb,var(--apple-teal) 12%,transparent);padding:4px 9px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-teal) 25%,transparent);text-decoration:none"
                                   onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-eye"></i>Detail
                                </a>
                                <a href="{{ route('admin.recruitment.tests.edit', $template) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-orange);background:color-mix(in srgb,var(--apple-orange) 12%,transparent);padding:4px 9px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-orange) 25%,transparent);text-decoration:none"
                                   onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                    <i class="fas fa-edit"></i>Edit
                                </a>
                                <form action="{{ route('admin.recruitment.tests.destroy', $template) }}" method="POST" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;gap:5px;font-size:0.72rem;font-weight:600;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 12%,transparent);padding:4px 9px;border-radius:7px;border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);cursor:pointer"
                                            onmouseover="this.style.opacity=.7" onmouseout="this.style.opacity=1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
        <div style="padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <x-ui.pagination :paginator="$templates->appends(request()->all())" variant="full" :show-info="true" />
        </div>
        @endif
        @else
        <div style="padding:48px;text-align:center">
            <i class="fas fa-clipboard-list" style="font-size:2rem;color:var(--dark-text-secondary);opacity:.4;display:block;margin-bottom:12px"></i>
            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum Ada Template</p>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 16px">Buat template tes pertama untuk mulai menilai kandidat</p>
            <a href="{{ route('admin.recruitment.tests.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:var(--apple-blue);color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;text-decoration:none">
                <i class="fas fa-plus"></i>Buat Template Pertama
            </a>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTest');
    const filterType  = document.getElementById('filterType');
    const filterStatus= document.getElementById('filterStatus');
    const rows = document.querySelectorAll('.test-row');

    function applyFilters() {
        const search = searchInput.value.toLowerCase();
        const type   = filterType.value.toLowerCase();
        const status = filterStatus.value.toLowerCase();
        rows.forEach(row => {
            const title  = row.querySelector('.test-title')?.textContent.toLowerCase() ?? '';
            const typeEl = row.querySelector('.test-type')?.textContent.toLowerCase() ?? '';
            const statusEl = row.querySelector('.test-status')?.textContent.toLowerCase() ?? '';
            const matchSearch = !search || title.includes(search) || typeEl.includes(search);
            const matchType   = !type   || typeEl.includes(type);
            const matchStatus = !status || statusEl.includes(status);
            row.style.display = (matchSearch && matchType && matchStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', applyFilters);
    filterType.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
});
</script>
@endpush
