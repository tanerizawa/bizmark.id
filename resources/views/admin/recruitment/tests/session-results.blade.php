@extends('layouts.admin')

@section('title', 'Test Session Results - ' . $session->jobApplication->full_name)

@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
        <div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.7rem;color:var(--dark-text-tertiary);margin-bottom:6px;flex-wrap:wrap">
                <a href="{{ route('admin.recruitment.index') }}" style="color:var(--dark-text-secondary);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Rekrutmen</a>
                <span>/</span>
                <a href="{{ route('admin.recruitment.tests.index') }}" style="color:var(--dark-text-secondary);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">Test</a>
                <span>/</span>
                <a href="{{ route('admin.recruitment.tests.show', $session->testTemplate) }}" style="color:var(--dark-text-secondary);text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">{{ $session->testTemplate->title }}</a>
                <span>/</span>
                <span style="color:var(--dark-text-primary)">Hasil Sesi</span>
            </div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--apple-blue);margin:0 0 4px">Hasil Tes</p>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px">
                <h1 style="font-size:1.4rem;font-weight:800;color:var(--dark-text-primary);margin:0;line-height:1.2">{{ $session->jobApplication->full_name }}</h1>
                @php
                    $sColors = ['completed'=>'var(--apple-green)','in-progress'=>'var(--apple-blue)','expired'=>'var(--apple-red)','pending'=>'var(--apple-yellow)'];
                    $sIcons = ['completed'=>'check-circle','in-progress'=>'clock','expired'=>'times-circle','pending'=>'hourglass-half'];
                    $sCol = $sColors[$session->status] ?? 'var(--dark-text-secondary)';
                    $sIco = $sIcons[$session->status] ?? 'circle';
                @endphp
                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,{{ $sCol }} 15%,transparent);color:{{ $sCol }}"><i class="fas fa-{{ $sIco }}"></i>{{ ucfirst($session->status) }}</span>
                @if($session->requires_manual_review)
                <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)"><i class="fas fa-flag" style="margin-right:4px"></i>Perlu Review</span>
                @endif
                @if($session->score !== null)
                    @if($session->passed)
                    <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-trophy" style="margin-right:4px"></i>Lulus</span>
                    @else
                    <span style="display:inline-flex;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red)"><i class="fas fa-times" style="margin-right:4px"></i>Tidak Lulus</span>
                    @endif
                @endif
            </div>
            <p style="font-size:0.8rem;color:var(--dark-text-secondary);margin:0">{{ $session->testTemplate->title }}</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-start">
            @if($session->requires_manual_review && !$session->evaluated_at)
            <a href="{{ route('admin.recruitment.tests.sessions.evaluate-manual', $session) }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-blue);color:#fff;border-radius:11px;font-size:0.82rem;font-weight:700;text-decoration:none" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1"><i class="fas fa-edit"></i>Evaluate Now</a>
            @endif
            <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.82rem;font-weight:700;cursor:pointer" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'"><i class="fas fa-print"></i>Print</button>
            <a href="{{ route('admin.recruitment.tests.show', $session->testTemplate) }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--dark-bg-tertiary);color:var(--dark-text-secondary);border:1px solid var(--dark-separator);border-radius:11px;font-size:0.82rem;font-weight:700;text-decoration:none" onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'"><i class="fas fa-arrow-left"></i>Back</a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px">
        @foreach([
            ['Score','var(--apple-blue)','fa-star', $session->score !== null ? number_format($session->score,1).'%' : 'N/A', $session->score !== null ? 'Passing: '.$session->testTemplate->passing_score.'%' : 'Belum dinilai'],
            ['Dijawab','var(--apple-green)','fa-check-double', $session->testAnswers->count(), 'Dari '.count($session->testTemplate->questions_data ?? []).' soal'],
            ['Durasi','var(--apple-orange)','fa-clock', ($session->time_taken_minutes ?? ($session->started_at && $session->completed_at ? $session->started_at->diffInMinutes($session->completed_at) : 0)).' min', 'Waktu pengerjaan'],
            ['Tab Switch','var(--apple-purple)','fa-window-restore', $session->tab_switches, 'Anti-cheat'],
            ['Selesai','var(--apple-teal)','fa-calendar-check', $session->completed_at ? $session->completed_at->format('d M Y') : 'N/A', $session->completed_at ? $session->completed_at->format('H:i') : 'Belum selesai'],
        ] as [$lbl,$col,$ico,$val,$sub])
        <div style="background:linear-gradient(135deg,color-mix(in srgb,{{ $col }} 12%,var(--dark-bg-tertiary)) 0%,var(--dark-bg-tertiary) 100%);border:1px solid color-mix(in srgb,{{ $col }} 25%,var(--dark-separator));border-radius:14px;padding:14px 16px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:{{ $col }};margin:0">{{ $lbl }}</p>
                <i class="fas {{ $ico }}" style="color:color-mix(in srgb,{{ $col }} 50%,transparent);font-size:0.9rem"></i>
            </div>
            <p style="font-size:1.3rem;font-weight:800;color:var(--dark-text-primary);margin:0 0 3px">{{ $val }}</p>
            <p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0">{{ $sub }}</p>
        </div>
        @endforeach
    </div>

    {{-- Candidate Info --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <h2 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-user" style="color:var(--apple-blue);margin-right:7px"></i>Informasi Kandidat</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            @foreach([['Nama Lengkap',$session->jobApplication->full_name,'fa-user'],['Email',$session->jobApplication->email,'fa-envelope'],['Telepon',$session->jobApplication->phone ?? 'N/A','fa-phone'],['Posisi',$session->jobApplication->jobVacancy->title ?? 'N/A','fa-briefcase'],['Tanggal Daftar',$session->jobApplication->created_at->format('d M Y, H:i'),'fa-calendar'],['IP Address',$session->ip_address ?? 'N/A','fa-globe']] as [$l,$v,$ico])
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px;background:var(--dark-bg-secondary);border-radius:9px;border:1px solid var(--dark-separator)">
                <div style="width:28px;height:28px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-blue);flex-shrink:0;font-size:0.75rem"><i class="fas {{ $ico }}"></i></div>
                <div>
                    <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 3px">{{ $l }}</p>
                    <p style="font-size:0.83rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $v }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Document Editing Files --}}
    @if($session->testTemplate->isDocumentEditingTest())
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <h2 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 16px"><i class="fas fa-file-word" style="color:var(--apple-blue);margin-right:7px"></i>Dokumen Submission</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:{{ $session->testTemplate->evaluation_criteria ? '20px' : '0' }}">
            <div style="padding:14px;background:var(--dark-bg-secondary);border-radius:10px;border:1px solid var(--dark-separator);display:flex;align-items:center;gap:10px">
                <div style="width:40px;height:40px;border-radius:9px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:flex;align-items:center;justify-content:center;color:var(--apple-blue);flex-shrink:0"><i class="fas fa-download"></i></div>
                <div style="flex:1;min-width:0">
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 3px">Template File</p>
                    <p style="font-size:0.8rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ basename($session->testTemplate->template_file_path ?? '') }}</p>
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

        @if($session->testTemplate->evaluation_criteria && isset($session->testTemplate->evaluation_criteria['criteria']))
        @php
            $groupedCriteria = collect($session->testTemplate->evaluation_criteria['criteria'])->groupBy('category');
            $evaluationScores = $session->evaluation_scores['criteria_scores'] ?? [];
        @endphp
        <h3 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 12px"><i class="fas fa-clipboard-check" style="color:var(--apple-orange);margin-right:7px"></i>Evaluation Criteria</h3>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($groupedCriteria as $category => $criteriaGroup)
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;overflow:hidden">
                <div style="padding:10px 14px;border-bottom:1px solid var(--dark-separator)">
                    <h4 style="font-size:0.82rem;font-weight:700;color:var(--dark-text-primary);margin:0">{{ $category }}</h4>
                </div>
                <div style="padding:10px 14px;display:flex;flex-direction:column;gap:8px">
                    @foreach($criteriaGroup as $criterion)
                    @php
                        $globalIndex = collect($session->testTemplate->evaluation_criteria['criteria'])->search($criterion);
                        $score = collect($evaluationScores)->firstWhere('criteria_id', $globalIndex);
                    @endphp
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:10px;background:var(--dark-bg-tertiary);border-radius:8px">
                        <div style="flex:1;min-width:0">
                            <p style="font-size:0.83rem;color:var(--dark-text-primary);margin:0 0 4px">{{ $criterion['description'] }}</p>
                            @if(isset($criterion['type']))<p style="font-size:0.68rem;color:var(--dark-text-secondary);margin:0"><i class="fas fa-tag" style="margin-right:3px"></i>{{ $criterion['type'] }}</p>@endif
                            @if($score && isset($score['notes']) && $score['notes'])
                            <p style="font-size:0.72rem;margin:5px 0 0;padding:5px 8px;border-radius:6px;background:color-mix(in srgb,var(--apple-blue) 12%,transparent);color:var(--apple-blue)"><i class="fas fa-comment" style="margin-right:4px"></i>{{ $score['notes'] }}</p>
                            @endif
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <p style="font-size:0.95rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 3px">{{ $score ? $score['score'] : '—' }} / {{ $criterion['points'] }}</p>
                            @if($score)<span style="display:inline-flex;padding:2px 7px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-check"></i></span>@endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @if($session->evaluation_scores && isset($session->evaluation_scores['total_score']))
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between">
                <div>
                    <p style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 2px">Total Score</p>
                    <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">Dari {{ $session->testTemplate->evaluation_criteria['total_points'] ?? 100 }} poin</p>
                </div>
                <div style="text-align:right">
                    <p style="font-size:1.6rem;font-weight:800;color:var(--apple-green);margin:0 0 2px">{{ $session->evaluation_scores['total_score'] }}</p>
                    <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">({{ number_format(($session->evaluation_scores['total_score'] / ($session->testTemplate->evaluation_criteria['total_points'] ?? 100)) * 100, 1) }}%)</p>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- Grading Info Banner --}}
    @if($session->requires_manual_review && !$session->evaluated_at)
    <div style="background:color-mix(in srgb,var(--apple-orange) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-orange) 30%,transparent);border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div style="display:flex;align-items:flex-start;gap:12px">
            <i class="fas fa-exclamation-triangle" style="color:var(--apple-orange);font-size:1.2rem;margin-top:2px;flex-shrink:0"></i>
            <div>
                <p style="font-size:0.88rem;font-weight:700;color:var(--apple-orange);margin:0 0 3px">Evaluasi Diperlukan</p>
                <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Tes ini mengandung soal esai/rating yang perlu dinilai secara manual oleh evaluator.</p>
            </div>
        </div>
        <a href="{{ route('admin.recruitment.tests.sessions.evaluate-manual', $session) }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:var(--apple-orange);color:#fff;border-radius:11px;font-size:0.82rem;font-weight:700;text-decoration:none;flex-shrink:0" onmouseover="this.style.opacity=.85" onmouseout="this.style.opacity=1"><i class="fas fa-edit"></i>Evaluate Now</a>
    </div>
    @endif

    {{-- Questions & Answers --}}
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
            <h2 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0"><i class="fas fa-list-alt" style="color:var(--apple-green);margin-right:7px"></i>Pertanyaan & Jawaban</h2>
            <span style="font-size:0.78rem;color:var(--dark-text-secondary)">{{ $session->testAnswers->count() }} / {{ count($session->testTemplate->questions_data ?? []) }} dijawab</span>
        </div>

        @php
            $questions = $session->testTemplate->questions_data ?? [];
            $answers = $session->testAnswers->keyBy('question_id');
        @endphp

        @if(count($questions) > 0)
        <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($questions as $index => $question)
            @php
                $answer = $answers->get($index);
                $answerData = $answer ? $answer->answer_data : null;
                $answerValue = $answerData['answer_value'] ?? null;
            @endphp
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:16px">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:12px">
                    <div style="display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0">
                        <span style="width:32px;height:32px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);color:var(--apple-blue)">{{ $index + 1 }}</span>
                        <div style="flex:1;min-width:0">
                            <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px">{{ $question['question_text'] ?? 'No question text' }}</p>
                            <div style="display:flex;gap:6px;flex-wrap:wrap">
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)">{{ ucfirst(str_replace('-',' ',$question['question_type'] ?? 'N/A')) }}</span>
                                @if(isset($question['points']))<span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">{{ $question['points'] }} pts</span>@endif
                                @if($question['question_type'] === 'multiple-choice' && isset($question['correct_answer']))
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)"><i class="fas fa-robot" style="margin-right:3px"></i>Auto-graded</span>
                                @elseif(in_array($question['question_type'], ['essay','rating-scale']))
                                <span style="display:inline-flex;padding:2px 8px;border-radius:12px;font-size:0.65rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)"><i class="fas fa-user-edit" style="margin-right:3px"></i>Manual</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($answer)
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green);flex-shrink:0"><i class="fas fa-check"></i>Dijawab</span>
                    @else
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.68rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-tertiary) 20%,transparent);color:var(--dark-text-tertiary);flex-shrink:0"><i class="fas fa-minus"></i>Kosong</span>
                    @endif
                </div>

                @if($answer)
                <div style="padding-left:42px">
                    @if($question['question_type'] === 'multiple-choice' && isset($question['options']))
                    <div style="display:flex;flex-direction:column;gap:6px">
                        @foreach($question['options'] as $optIndex => $option)
                        @php
                            $isSelected = $answerValue == $optIndex;
                            $isCorrect = isset($question['correct_answer']) && $question['correct_answer'] == $optIndex;
                        @endphp
                        <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;background:{{ $isSelected ? ($isCorrect ? 'color-mix(in srgb,var(--apple-green) 12%,transparent)' : 'color-mix(in srgb,var(--apple-red) 12%,transparent)') : 'rgba(255,255,255,0.03)' }};border:1px solid {{ $isSelected ? ($isCorrect ? 'color-mix(in srgb,var(--apple-green) 30%,transparent)' : 'color-mix(in srgb,var(--apple-red) 30%,transparent)') : 'transparent' }}">
                            <span style="width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.72rem;font-weight:700;background:{{ $isSelected ? ($isCorrect ? 'color-mix(in srgb,var(--apple-green) 20%,transparent)' : 'color-mix(in srgb,var(--apple-red) 20%,transparent)') : 'rgba(142,142,147,0.2)' }};color:{{ $isSelected ? ($isCorrect ? 'var(--apple-green)' : 'var(--apple-red)') : 'var(--dark-text-tertiary)' }}">{{ chr(65 + $optIndex) }}</span>
                            <p style="flex:1;font-size:0.83rem;color:{{ $isSelected ? 'var(--dark-text-primary)' : 'var(--dark-text-secondary)' }};font-weight:{{ $isSelected ? '600' : '400' }};margin:0">{{ $option }}</p>
                            @if($isSelected)
                                @if($isCorrect)<i class="fas fa-check-circle" style="color:var(--apple-green);font-size:0.9rem"></i>@else<i class="fas fa-times-circle" style="color:var(--apple-red);font-size:0.9rem"></i>@endif
                            @elseif($isCorrect)
                                <i class="fas fa-check" style="color:var(--apple-green);font-size:0.75rem"></i>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @elseif($question['question_type'] === 'essay')
                    <div style="padding:10px 12px;background:rgba(255,255,255,0.04);border-radius:8px;border-left:3px solid var(--apple-blue)">
                        <p style="font-size:0.83rem;color:var(--dark-text-primary);white-space:pre-wrap;margin:0">{{ $answerValue ?? 'Tidak ada jawaban' }}</p>
                    </div>
                    @elseif($question['question_type'] === 'rating-scale')
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="color:#fbbf24">@for($i=1;$i<=($question['max_rating'] ?? 5);$i++)<i class="fas fa-star" style="font-size:0.95rem{{ $i > $answerValue ? ';opacity:.3' : '' }}"></i>@endfor</div>
                        <span style="font-size:1rem;font-weight:700;color:var(--dark-text-primary)">{{ $answerValue ?? 0 }} / {{ $question['max_rating'] ?? 5 }}</span>
                    </div>
                    @else
                    <div style="padding:10px 12px;background:rgba(255,255,255,0.04);border-radius:8px">
                        <p style="font-size:0.83rem;color:var(--dark-text-primary);margin:0">{{ $answerValue ?? 'Tidak ada jawaban' }}</p>
                    </div>
                    @endif
                    @if($answer->answered_at)
                    <p style="font-size:0.68rem;color:var(--dark-text-tertiary);margin-top:6px"><i class="fas fa-clock" style="margin-right:3px"></i>Dijawab pukul {{ $answer->answered_at->format('H:i:s') }}</p>
                    @endif
                </div>
                @else
                <div style="padding-left:42px">
                    <p style="font-size:0.8rem;font-style:italic;color:var(--dark-text-tertiary);margin:0">Kandidat tidak menjawab soal ini.</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div style="text-align:center;padding:40px">
            <i class="fas fa-inbox" style="font-size:2rem;color:var(--dark-text-tertiary);display:block;margin-bottom:8px;opacity:.5"></i>
            <p style="font-size:0.85rem;color:var(--dark-text-secondary);margin:0">Tidak ada pertanyaan di template tes ini.</p>
        </div>
        @endif
    </div>

    {{-- Evaluator Notes --}}
    @if($session->evaluator_notes || $session->evaluator_id)
    <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:22px 24px">
        <h2 style="font-size:0.9rem;font-weight:700;color:var(--dark-text-primary);margin:0 0 14px"><i class="fas fa-comment-dots" style="color:var(--apple-purple);margin-right:7px"></i>Catatan Evaluator</h2>
        <div style="display:flex;flex-direction:column;gap:10px">
            @if($session->evaluator_id)
            <div>
                <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 4px">Dievaluasi Oleh</p>
                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $session->evaluator->name ?? 'Unknown' }}</p>
            </div>
            @endif
            @if($session->evaluated_at)
            <div>
                <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 4px">Waktu Evaluasi</p>
                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $session->evaluated_at->format('d M Y, H:i') }}</p>
            </div>
            @endif
            @if($session->evaluator_notes)
            <div>
                <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--dark-text-secondary);margin:0 0 6px">Catatan</p>
                <div style="padding:12px 14px;background:rgba(255,255,255,0.04);border-radius:9px;border-left:3px solid var(--apple-purple)">
                    <p style="font-size:0.83rem;color:var(--dark-text-primary);white-space:pre-wrap;margin:0">{{ $session->evaluator_notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
@media print { button { display:none !important; } }
@endpush
@endsection
