@extends('layouts.admin')

@section('title', 'Evaluate Test - ' . $session->jobApplication->full_name)

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <a href="{{ route('admin.recruitment.tests.sessions.results', $session) }}" style="display:inline-flex;align-items:center;gap:6px;font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary);text-decoration:none;margin-bottom:6px" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                <i class="fas fa-arrow-left" style="font-size:0.65rem"></i>Hasil Tes
            </a>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-purple);margin:0 0 4px">Evaluasi Manual</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $session->jobApplication->full_name }}</h1>
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">Pending Evaluation</span>
                @if($session->score !== null)
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)">Auto-Score: {{ number_format($session->score, 1) }}%</span>
                @endif
            </div>
            <p style="font-size:0.82rem;color:var(--dark-text-secondary);margin:0">{{ $session->testTemplate->title }}</p>
        </div>
    </div>

    @if(session('success'))
    <div style="background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 30%,transparent);border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
        <i class="fas fa-check-circle" style="color:var(--apple-green)"></i>
        <p style="font-size:0.85rem;font-weight:600;color:var(--apple-green);margin:0">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error') || $errors->any())
    <div style="background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 30%,transparent);border-radius:12px;padding:12px 16px">
        <p style="font-size:0.85rem;font-weight:600;color:var(--apple-red);margin:0 0 4px"><i class="fas fa-exclamation-circle" style="margin-right:5px"></i>{{ session('error') ?? 'Validation Errors:' }}</p>
        @if($errors->any())<ul style="margin:0;padding-left:16px">@foreach($errors->all() as $e)<li style="font-size:0.78rem;color:var(--apple-red);margin-bottom:2px">{{ $e }}</li>@endforeach</ul>@endif
    </div>
    @endif

    {{-- Candidate Info + Stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        @foreach(['Kandidat'=>[$session->jobApplication->full_name,'fa-user','var(--apple-blue)'],'Posisi'=>[$session->jobApplication->jobVacancy->title ?? 'N/A','fa-briefcase','var(--apple-purple)'],'Selesai'=>[$session->completed_at ? $session->completed_at->format('d M Y') : 'N/A','fa-calendar','var(--apple-green)']] as $label=>[$val,$icon,$col])
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $col }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $col }} 25%,var(--dark-separator));border-radius:14px;padding:16px 18px;display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:50%;background:color-mix(in srgb,{{ $col }} 18%,transparent);display:flex;align-items:center;justify-content:center;color:{{ $col }};flex-shrink:0"><i class="fas {{ $icon }}"></i></div>
            <div>
                <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:{{ $col }};margin:0 0 3px">{{ $label }}</p>
                <p style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $val }}</p>
            </div>
        </div>
        @endforeach
    </div>

    @if($session->score !== null)
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        @foreach([['Current Score','var(--apple-blue)','fa-robot',number_format($session->score,1).'%','Dari pertanyaan objektif'],['Perlu Evaluasi','var(--apple-orange)','fa-tasks',count($subjectiveQuestions),'Perlu penilaian manual'],['Passing Score','var(--apple-purple)','fa-graduation-cap',$session->testTemplate->passing_score.'%','Nilai lulus']] as [$l,$col,$ico,$v,$s])
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 18px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $col }};margin:0">{{ $l }}</p>
                <i class="fas {{ $ico }}" style="color:color-mix(in srgb,{{ $col }} 50%,transparent);font-size:0.9rem"></i>
            </div>
            <p style="font-size:1.5rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 3px">{{ $v }}</p>
            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $s }}</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Document Files (if document editing test) --}}
    @if($session->testTemplate->isDocumentEditingTest())
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px"><i class="fas fa-file-word" style="color:var(--apple-blue);margin-right:7px"></i>Dokumen Submission</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div style="padding:14px;background:var(--dark-bg-secondary);border-radius:10px;border:1px solid var(--dark-separator);display:flex;align-items:center;gap:10px">
                <div style="width:40px;height:40px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-blue);flex-shrink:0"><i class="fas fa-download"></i></div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Template File</p>
                    <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ basename($session->testTemplate->template_file_path) }}</p>
                    @if($session->testTemplate->template_file_path && \Storage::disk('private')->exists($session->testTemplate->template_file_path))
                    <a href="{{ route('admin.recruitment.tests.download-template', $session->testTemplate) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--apple-blue);background:color-mix(in srgb,var(--apple-blue) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);text-decoration:none"><i class="fas fa-download" style="font-size:0.6rem"></i>Download</a>
                    @else
                    <span style="font-size:0.7rem;color:var(--apple-red)"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>File not found</span>
                    @endif
                </div>
            </div>
            <div style="padding:14px;background:var(--dark-bg-secondary);border-radius:10px;border:1px solid var(--dark-separator);display:flex;align-items:center;gap:10px">
                <div style="width:40px;height:40px;border-radius:9px;background:color-mix(in srgb,var(--apple-green) 18%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-green);flex-shrink:0"><i class="fas fa-upload"></i></div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Submitted File</p>
                    @if($session->submitted_file_path)
                    <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ basename($session->submitted_file_path) }}</p>
                    <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0 0 6px">{{ $session->submitted_at?->format('d M Y, H:i') }}</p>
                    @if(\Storage::disk('private')->exists($session->submitted_file_path))
                    <a href="{{ route('admin.recruitment.tests.sessions.download-submission', $session) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:0.7rem;font-weight:600;color:var(--apple-green);background:color-mix(in srgb,var(--apple-green) 12%,transparent);padding:4px 9px;border-radius:6px;border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);text-decoration:none"><i class="fas fa-download" style="font-size:0.6rem"></i>Download</a>
                    @else
                    <span style="font-size:0.7rem;color:var(--apple-red)"><i class="fas fa-exclamation-circle" style="margin-right:4px"></i>File not found</span>
                    @endif
                    @else
                    <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0 0 3px">Belum ada submission</p>
                    <p style="font-size:0.7rem;color:var(--dark-text-tertiary);margin:0">Kandidat belum upload</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Evaluation Form --}}
    <form action="{{ $session->testTemplate->isDocumentEditingTest()
        ? route('admin.recruitment.tests.sessions.submit-evaluation', $session)
        : route('admin.recruitment.tests.sessions.submit-evaluation-manual', $session) }}" method="POST">
        @csrf

        {{-- Document Editing Criteria --}}
        @if($session->testTemplate->isDocumentEditingTest() && $session->testTemplate->evaluation_criteria)
        @php $groupedCriteria = collect($session->testTemplate->evaluation_criteria['criteria'])->groupBy('category'); @endphp
        <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($groupedCriteria as $category => $criteriaGroup)
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-folder-open" style="color:var(--apple-blue);font-size:0.85rem"></i>
                    <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $category }}</h3>
                </div>
                <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px">
                    @foreach($criteriaGroup as $criterion)
                    @php $globalIndex = collect($session->testTemplate->evaluation_criteria['criteria'])->search($criterion); @endphp
                    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;padding:14px">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px">
                            <div>
                                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">{{ $criterion['description'] }}</p>
                                <div style="display:flex;gap:6px">
                                    <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">{{ $criterion['type'] }}</span>
                                    <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">Max: {{ $criterion['points'] }} pts</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="criteria_scores[{{ $globalIndex }}][criteria_id]" value="{{ $globalIndex }}">
                        <div style="display:grid;grid-template-columns:1fr 2fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Score (0–{{ $criterion['points'] }}) *</label>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <input type="number" name="criteria_scores[{{ $globalIndex }}][score]" min="0" max="{{ $criterion['points'] }}" step="0.5" required placeholder="0"
                                           style="width:80px;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                           onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                    <span style="font-size:0.8rem;color:var(--dark-text-secondary)">/ {{ $criterion['points'] }}</span>
                                </div>
                                @error('criteria_scores.'.$globalIndex.'.score')<p style="color:var(--apple-red);font-size:0.72rem;margin:4px 0 0">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label style="display:block;font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em">Catatan (opsional)</label>
                                <textarea name="criteria_scores[{{ $globalIndex }}][notes]" rows="2" placeholder="Feedback spesifik..." style="width:100%;padding:8px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.8rem;outline:none;resize:vertical;box-sizing:border-box" onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'"></textarea>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 2px">Total Poin Tersedia</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">Jumlah semua kriteria</p>
                </div>
                <p style="font-size:1.5rem;font-weight:800;color:var(--apple-blue);margin:0">{{ $session->testTemplate->evaluation_criteria['total_points'] ?? 100 }} <span style="font-size:0.75rem;font-weight:600;color:var(--dark-text-secondary)">pts</span></p>
            </div>
        </div>
        @else
        {{-- Subjective Questions --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-clipboard-list" style="color:var(--apple-orange);margin-right:7px"></i>Pertanyaan Subjektif</h3>
            @if(count($subjectiveQuestions) === 0)
            <div style="text-align:center;padding:30px;color:var(--dark-text-secondary)">
                <i class="fas fa-check-circle" style="font-size:2rem;display:block;margin-bottom:8px;color:var(--apple-green);opacity:.6"></i>
                <p style="font-size:0.85rem;margin:0">Tidak ada pertanyaan subjektif. Tes di-grade otomatis sepenuhnya.</p>
            </div>
            @else
            <div style="display:flex;flex-direction:column;gap:14px">
                @foreach($subjectiveQuestions as $index => $item)
                @php
                    $question = $item['question'];
                    $answer = $item['answer'];
                    $answerValue = $answer ? ($answer->answer_data['answer_value'] ?? null) : null;
                    $maxPoints = $question['points'] ?? 10;
                @endphp
                <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px">
                    <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:12px">
                        <span style="width:34px;height:34px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);color:var(--apple-orange)">Q{{ $index + 1 }}</span>
                        <div style="flex:1">
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">{{ $question['question_text'] ?? 'No question text' }}</p>
                            <div style="display:flex;gap:6px">
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)">{{ ucfirst(str_replace('-',' ',$question['question_type'] ?? 'N/A')) }}</span>
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">Max: {{ $maxPoints }} pts</span>
                            </div>
                        </div>
                    </div>
                    <div style="margin-bottom:12px;padding:12px;background:var(--dark-bg-tertiary);border-radius:9px;border-left:3px solid var(--apple-blue)">
                        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 5px">Jawaban Kandidat</p>
                        @if($answer && $answerValue)
                            @if($question['question_type'] === 'essay')
                            <p style="font-size:0.83rem;color:var(--dark-text-primary);white-space:pre-wrap;margin:0">{{ $answerValue }}</p>
                            @elseif(in_array($question['question_type'], ['rating','rating-scale']))
                            <div style="display:flex;align-items:center;gap:8px">
                                <div style="color:#fbbf24">@for($s=1;$s<=($question['max_rating'] ?? 5);$s++)<i class="fas fa-star" style="font-size:0.85rem{{ $s > $answerValue ? ';opacity:.3' : '' }}"></i>@endfor</div>
                                <span style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary)">{{ $answerValue }} / {{ $question['max_rating'] ?? 5 }}</span>
                            </div>
                            @else
                            <p style="font-size:0.83rem;color:var(--dark-text-primary);margin:0">{{ $answerValue }}</p>
                            @endif
                        @else
                        <p style="font-size:0.78rem;color:var(--dark-text-tertiary);font-style:italic;margin:0">Tidak ada jawaban</p>
                        @endif
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label style="font-size:0.78rem;font-weight:600;color:var(--dark-text-primary);white-space:nowrap">Score (0–{{ $maxPoints }}) <span style="color:var(--apple-red)">*</span></label>
                        <input type="number" name="manual_scores[{{ $index }}]" min="0" max="{{ $maxPoints }}" step="0.5" required placeholder="0"
                               style="width:80px;padding:8px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                               onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <span style="font-size:0.8rem;color:var(--dark-text-secondary)">dari {{ $maxPoints }} poin</span>
                        @error('manual_scores.'.$index)<span style="color:var(--apple-red);font-size:0.72rem">{{ $message }}</span>@enderror
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Evaluator Notes --}}
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px"><i class="fas fa-comment-dots" style="color:var(--apple-purple);margin-right:7px"></i>Catatan Evaluator (opsional)</h3>
            <textarea name="evaluator_notes" rows="5" placeholder="Tambahkan komentar umum tentang performa kandidat..."
                      style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-primary);font-size:0.83rem;outline:none;resize:vertical;box-sizing:border-box"
                      onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('evaluator_notes') }}</textarea>
        </div>

        {{-- Submit --}}
        @if(($session->testTemplate->isDocumentEditingTest() && $session->testTemplate->evaluation_criteria) || count($subjectiveQuestions) > 0)
        <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 3px">Siap submit evaluasi?</p>
                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">Skor final akan dihitung setelah submit.</p>
            </div>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:var(--apple-blue);color:#fff;border:none;border-radius:11px;font-size:0.88rem;font-weight:700;cursor:pointer"
                    onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1">
                <i class="fas fa-check"></i>Submit Evaluasi
            </button>
        </div>
        @endif
    </form>
</div>
@endsection
