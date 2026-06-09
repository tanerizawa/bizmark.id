@extends('layouts.admin')
@section('title', 'Email Account: ' . $emailAccount->email)
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Header --}}
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:18px;padding:20px 24px;position:relative;overflow:hidden">
        <div style="position:absolute;width:220px;height:220px;border-radius:50%;top:-60px;right:-30px;background:color-mix(in srgb,var(--apple-teal) 16%,transparent);filter:blur(56px);pointer-events:none"></div>
        <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div>
                <p style="font-size:0.6rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Email Accounts</p>
                <h1 style="font-size:1.2rem;font-weight:800;color:var(--dark-text-primary);margin:4px 0 4px;display:flex;align-items:center;gap:10px">
                    <span style="width:32px;height:32px;border-radius:10px;background:color-mix(in srgb,var(--apple-teal) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-at" style="color:var(--apple-teal);font-size:0.85rem"></i>
                    </span>
                    {{ $emailAccount->email }}
                </h1>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span style="font-size:0.82rem;color:var(--dark-text-secondary)">{{ $emailAccount->name }}</span>
                    @if($emailAccount->is_active)
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-check-circle"></i>Aktif</span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:600;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red)"><i class="fas fa-times-circle"></i>Nonaktif</span>
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <a href="{{ route('admin.email-accounts.edit', $emailAccount) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-edit" style="font-size:0.75rem"></i>Edit Account
                </a>
                <a href="{{ route('admin.email-accounts.index') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1px solid var(--dark-separator);color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none"
                   onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                    <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="display:flex;align-items:center;gap:10px;border-radius:10px;padding:10px 16px;background:color-mix(in srgb,var(--apple-green) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-green) 25%,transparent);color:var(--apple-green)">
        <i class="fas fa-check-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div style="display:flex;align-items:center;gap:10px;border-radius:10px;padding:10px 16px;background:color-mix(in srgb,var(--apple-red) 12%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);color:var(--apple-red)">
        <i class="fas fa-exclamation-circle" style="flex-shrink:0"></i><span style="font-size:0.85rem">{{ session('error') }}</span>
    </div>
    @endif

    {{-- 2-col Layout --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

        {{-- LEFT --}}
        <div style="display:flex;flex-direction:column;gap:16px">

            {{-- Account Info --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-teal) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-info-circle" style="color:var(--apple-teal);font-size:0.72rem"></i></span>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Informasi Akun</h2>
                </div>
                <div style="padding:18px 20px;display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
                    <div>
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Email</p>
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $emailAccount->email }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Nama</p>
                        <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $emailAccount->name }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Tipe</p>
                        @if($emailAccount->type === 'shared')
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-users"></i>Shared</span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-purple) 15%,transparent);color:var(--apple-purple)"><i class="fas fa-user"></i>Personal</span>
                        @endif
                    </div>
                    <div>
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Departemen</p>
                        <p style="font-size:0.85rem;color:var(--dark-text-primary);margin:0">{{ ucfirst($emailAccount->department) }}</p>
                    </div>
                    <div>
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Status</p>
                        @if($emailAccount->is_active)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-check-circle"></i>Aktif</span>
                        @else
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red)"><i class="fas fa-times-circle"></i>Nonaktif</span>
                        @endif
                    </div>
                    @if($emailAccount->description)
                    <div style="grid-column:1/4">
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Deskripsi</p>
                        <p style="font-size:0.85rem;color:var(--dark-text-primary);margin:0">{{ $emailAccount->description }}</p>
                    </div>
                    @endif
                    @if($emailAccount->forward_to)
                    <div style="grid-column:1/4">
                        <p style="font-size:0.62rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0 0 4px">Forward To</p>
                        <p style="font-size:0.85rem;color:var(--dark-text-primary);margin:0;display:flex;align-items:center;gap:6px">
                            <i class="fas fa-arrow-right" style="color:var(--apple-blue);font-size:0.75rem"></i>{{ $emailAccount->forward_to }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Assigned Users --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-users" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
                        <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">
                            Assigned Users
                            <span style="display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 5px;border-radius:10px;font-size:0.7rem;font-weight:700;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);color:var(--apple-blue);margin-left:4px">{{ $emailAccount->users->count() }}</span>
                        </h2>
                    </div>
                    <button type="button" onclick="openAssignModal()"
                            style="display:inline-flex;align-items:center;gap:5px;padding:6px 14px;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.78rem;font-weight:600;border:none;cursor:pointer"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-plus" style="font-size:0.7rem"></i>Assign User
                    </button>
                </div>
                @if($emailAccount->users->count() > 0)
                <div style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse">
                        <thead style="background:var(--dark-bg-tertiary)">
                            <tr>
                                @foreach(['User','Role','Permissions','Assigned','Aksi'] as $col)
                                <th style="padding:10px 16px;font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);text-align:left;border-bottom:1px solid var(--dark-separator);white-space:nowrap">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emailAccount->users as $user)
                            @php $assignment = $emailAccount->assignments->where('user_id', $user->id)->first(); @endphp
                            <tr style="border-bottom:1px solid var(--dark-separator)" onmouseover="this.style.background='var(--dark-bg-tertiary)'" onmouseout="this.style.background='transparent'">
                                <td style="padding:12px 16px">
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--apple-blue),var(--apple-indigo));color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;flex-shrink:0">
                                            {{ strtoupper(substr($user->name,0,1)) }}
                                        </div>
                                        <div>
                                            <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0">{{ $user->name }}</p>
                                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:12px 16px">
                                    @if($assignment->role === 'primary')
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);color:var(--apple-blue)"><i class="fas fa-star"></i>Primary</span>
                                    @elseif($assignment->role === 'backup')
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-orange) 15%,transparent);color:var(--apple-orange)"><i class="fas fa-user-shield"></i>Backup</span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-secondary) 15%,transparent);color:var(--dark-text-secondary)"><i class="fas fa-eye"></i>Viewer</span>
                                    @endif
                                </td>
                                <td style="padding:12px 16px">
                                    <div style="display:flex;flex-wrap:wrap;gap:4px">
                                        @if($assignment->can_send)
                                            <span style="padding:2px 8px;border-radius:6px;font-size:0.7rem;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)"><i class="fas fa-paper-plane" style="margin-right:3px"></i>Send</span>
                                        @endif
                                        @if($assignment->can_receive)
                                            <span style="padding:2px 8px;border-radius:6px;font-size:0.7rem;background:color-mix(in srgb,var(--apple-teal) 15%,transparent);color:var(--apple-teal)"><i class="fas fa-inbox" style="margin-right:3px"></i>Receive</span>
                                        @endif
                                        @if($assignment->can_delete)
                                            <span style="padding:2px 8px;border-radius:6px;font-size:0.7rem;background:color-mix(in srgb,var(--apple-red) 15%,transparent);color:var(--apple-red)"><i class="fas fa-trash" style="margin-right:3px"></i>Delete</span>
                                        @endif
                                        @if($assignment->can_assign_others)
                                            <span style="padding:2px 8px;border-radius:6px;font-size:0.7rem;background:color-mix(in srgb,var(--apple-yellow) 15%,transparent);color:var(--apple-yellow)"><i class="fas fa-user-plus" style="margin-right:3px"></i>Assign</span>
                                        @endif
                                    </div>
                                </td>
                                <td style="padding:12px 16px">
                                    <span style="font-size:0.75rem;color:var(--dark-text-secondary)">{{ $assignment->created_at->diffForHumans() }}</span>
                                </td>
                                <td style="padding:12px 16px">
                                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px">
                                        <button type="button"
                                            onclick="openEditPerms({{ $emailAccount->id }}, {{ $user->id }}, '{{ $assignment->role }}', {{ $assignment->can_send ? 'true' : 'false' }}, {{ $assignment->can_receive ? 'true' : 'false' }}, {{ $assignment->can_delete ? 'true' : 'false' }}, {{ $assignment->can_assign_others ? 'true' : 'false' }})"
                                            style="width:28px;height:28px;border:1px solid var(--dark-separator);border-radius:8px;background:none;color:var(--dark-text-secondary);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:0.78rem"
                                            onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                            onclick="openUnassignModal({{ $emailAccount->id }}, {{ $user->id }}, '{{ addslashes($user->name) }}')"
                                            style="width:28px;height:28px;border:1px solid color-mix(in srgb,var(--apple-red) 25%,transparent);border-radius:8px;background:none;color:var(--apple-red);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:0.78rem"
                                            onmouseover="this.style.background='color-mix(in srgb,var(--apple-red) 10%,transparent)'" onmouseout="this.style.background='none'">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div style="text-align:center;padding:40px 20px">
                    <div style="width:44px;height:44px;border-radius:50%;background:color-mix(in srgb,var(--apple-blue) 15%,transparent);display:flex;align-items:center;justify-content:center;margin:0 auto 10px">
                        <i class="fas fa-users" style="color:var(--apple-blue);font-size:1rem"></i>
                    </div>
                    <p style="font-size:0.88rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px">Belum ada user ditetapkan</p>
                    <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0 0 14px">Assign user untuk memberikan akses ke akun ini</p>
                    <button type="button" onclick="openAssignModal()"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;border:none;cursor:pointer">
                        <i class="fas fa-plus" style="font-size:0.75rem"></i>Assign User Pertama
                    </button>
                </div>
                @endif
            </div>

            {{-- Recent Emails --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--dark-separator)">
                    <div style="display:flex;align-items:center;gap:8px">
                        <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-orange) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-envelope" style="color:var(--apple-orange);font-size:0.72rem"></i></span>
                        <h2 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Email Terbaru</h2>
                    </div>
                    <a href="{{ route('admin.inbox.index', ['email_account_id' => $emailAccount->id]) }}"
                       style="font-size:0.78rem;color:var(--apple-blue);text-decoration:none"
                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                        Lihat semua <i class="fas fa-arrow-right" style="margin-left:3px;font-size:0.7rem"></i>
                    </a>
                </div>
                <div style="padding:16px 20px">
                    @if($recentEmails->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach($recentEmails as $email)
                        <a href="{{ route('admin.inbox.show', $email) }}"
                           style="display:flex;align-items:flex-start;justify-content:space-between;padding:12px 14px;border-radius:10px;border:1px solid var(--dark-separator);background:var(--dark-bg-tertiary);text-decoration:none"
                           onmouseover="this.style.borderColor='color-mix(in srgb,var(--apple-blue) 30%,transparent)'" onmouseout="this.style.borderColor='var(--dark-separator)'">
                            <div style="flex:1;min-width:0">
                                <p style="font-size:0.85rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $email->subject }}</p>
                                <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">From: {{ $email->from_email }}</p>
                            </div>
                            <span style="font-size:0.72rem;color:var(--dark-text-secondary);flex-shrink:0;margin-left:12px">{{ $email->received_at->diffForHumans() }}</span>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div style="text-align:center;padding:24px 0;color:var(--dark-text-secondary)">
                        <i class="fas fa-inbox" style="margin-right:6px;opacity:.5"></i><span style="font-size:0.85rem">Belum ada email</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div style="display:flex;flex-direction:column;gap:14px;position:sticky;top:16px">

            {{-- Statistics --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-purple) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-chart-bar" style="color:var(--apple-purple);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Statistik</h3>
                </div>
                <div style="padding:16px 18px;display:flex;flex-direction:column;gap:14px">
                    @php
                    $stats = [
                        ['label'=>'Total Diterima', 'value'=>$emailAccount->total_received??0, 'pct'=>100, 'color'=>'var(--apple-green)'],
                        ['label'=>'Total Terkirim', 'value'=>$emailAccount->total_sent??0, 'pct'=>$emailAccount->total_received>0?min(($emailAccount->total_sent/max($emailAccount->total_received,1))*100,100):0, 'color'=>'var(--apple-blue)'],
                        ['label'=>'Belum Dibaca', 'value'=>$emailAccount->getUnreadCount(), 'pct'=>$emailAccount->total_received>0?($emailAccount->getUnreadCount()/$emailAccount->total_received)*100:0, 'color'=>'var(--apple-orange)'],
                        ['label'=>'Hari Ini', 'value'=>$emailAccount->getTodayEmailCount(), 'pct'=>($emailAccount->max_daily_emails??100)>0?($emailAccount->getTodayEmailCount()/($emailAccount->max_daily_emails??100))*100:0, 'color'=>'var(--apple-purple)'],
                    ];
                    @endphp
                    @foreach($stats as $stat)
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                            <span style="font-size:0.78rem;color:var(--dark-text-secondary)">{{ $stat['label'] }}</span>
                            <span style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary)">{{ $stat['value'] }}</span>
                        </div>
                        <div style="height:5px;background:var(--dark-bg-tertiary);border-radius:4px;overflow:hidden">
                            <div style="height:100%;border-radius:4px;background:{{ $stat['color'] }};width:{{ round(min($stat['pct'],100)) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    <p style="font-size:0.7rem;color:var(--dark-text-secondary);margin:0;opacity:.6">Limit: {{ $emailAccount->max_daily_emails??100 }} email/hari</p>
                </div>
            </div>

            {{-- Settings --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid var(--dark-separator)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-blue) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-cog" style="color:var(--apple-blue);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--dark-text-primary);margin:0">Pengaturan</h3>
                </div>
                <div style="padding:16px 18px;display:flex;flex-direction:column;gap:12px">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <span style="font-size:0.85rem;color:var(--dark-text-primary)">Auto-Reply</span>
                        @if($emailAccount->auto_reply_enabled)
                            <span style="padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--apple-green) 15%,transparent);color:var(--apple-green)">Aktif</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:600;background:color-mix(in srgb,var(--dark-text-secondary) 12%,transparent);color:var(--dark-text-secondary)">Nonaktif</span>
                        @endif
                    </div>
                    @if($emailAccount->auto_reply_enabled && $emailAccount->auto_reply_message)
                    <div style="border-radius:8px;padding:10px 12px;background:var(--dark-bg-tertiary)">
                        <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">{{ Str::limit($emailAccount->auto_reply_message,100) }}</p>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <span style="font-size:0.85rem;color:var(--dark-text-primary)">Max Harian</span>
                        <span style="font-size:0.85rem;color:var(--dark-text-secondary)">{{ $emailAccount->max_daily_emails??100 }}</span>
                    </div>
                    @if($emailAccount->signature)
                    <div>
                        <p style="font-size:0.78rem;color:var(--dark-text-primary);margin:0 0 6px">Signature</p>
                        <div style="border-radius:8px;padding:10px 12px;background:var(--dark-bg-tertiary)">
                            <p style="font-size:0.75rem;color:var(--dark-text-secondary);margin:0">{!! nl2br(e(Str::limit($emailAccount->signature,100))) !!}</p>
                        </div>
                    </div>
                    @endif
                    <div style="padding-top:10px;border-top:1px solid var(--dark-separator);display:flex;flex-direction:column;gap:4px">
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;opacity:.65"><i class="fas fa-calendar" style="margin-right:5px"></i>Dibuat: {{ $emailAccount->created_at->format('d M Y') }}</p>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:0;opacity:.65"><i class="fas fa-clock" style="margin-right:5px"></i>Diperbarui: {{ $emailAccount->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div style="background:var(--dark-bg-secondary);border:1px solid color-mix(in srgb,var(--apple-red) 22%,transparent);border-radius:16px;overflow:hidden">
                <div style="display:flex;align-items:center;gap:8px;padding:14px 18px;border-bottom:1px solid color-mix(in srgb,var(--apple-red) 18%,transparent)">
                    <span style="width:26px;height:26px;border-radius:8px;background:color-mix(in srgb,var(--apple-red) 18%,transparent);display:inline-flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-exclamation-triangle" style="color:var(--apple-red);font-size:0.72rem"></i></span>
                    <h3 style="font-size:0.85rem;font-weight:700;color:var(--apple-red);margin:0">Danger Zone</h3>
                </div>
                <div style="padding:16px 18px">
                    <p style="font-size:0.82rem;color:var(--dark-text-primary);margin:0 0 12px">Hapus akun email ini secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
                    <button type="button" onclick="openDeleteModal({{ $emailAccount->id }})"
                            style="display:flex;align-items:center;justify-content:center;gap:7px;width:100%;padding:10px;border-radius:10px;background:var(--apple-red);color:#fff;font-size:0.85rem;font-weight:600;border:none;cursor:pointer"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-trash"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Assign User --}}
<div id="assignModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 style="font-size:0.92rem;font-weight:700;color:var(--dark-text-primary);margin:0;display:flex;align-items:center;gap:8px"><i class="fas fa-user-plus" style="color:var(--apple-blue)"></i>Assign User</h5>
            <button onclick="closeAssignModal()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.3rem;cursor:pointer;padding:0;line-height:1">&times;</button>
        </div>
        <form action="{{ route('admin.email-accounts.assign', $emailAccount) }}" method="POST">
            @csrf
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Pilih User</label>
                    <select name="user_id" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="">-- Pilih user --</option>
                        @foreach($availableUsers ?? [] as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Role</label>
                    <select name="role" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                            onfocus="this.style.borderColor='var(--apple-blue)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="primary">Primary Handler</option>
                        <option value="backup">Backup Handler</option>
                        <option value="viewer">Viewer Only</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:8px">Permissions</label>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        @foreach(['can_send'=>'Dapat Kirim Email','can_receive'=>'Dapat Terima Email','can_delete'=>'Dapat Hapus Email','can_assign_others'=>'Dapat Assign User Lain'] as $name => $label)
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                            <input type="checkbox" name="{{ $name }}" value="1" {{ in_array($name,['can_send','can_receive'])?'checked':'' }}
                                   style="width:14px;height:14px;accent-color:var(--apple-blue)">
                            <span style="font-size:0.85rem;color:var(--dark-text-primary)">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <button type="button" onclick="closeAssignModal()"
                        style="padding:7px 16px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:none;cursor:pointer">Batal</button>
                <button type="submit"
                        style="padding:7px 16px;border:none;border-radius:9px;background:var(--apple-blue);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer">
                    <i class="fas fa-save" style="margin-right:5px"></i>Assign User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Permissions --}}
<div id="editPermModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;width:100%;max-width:480px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 style="font-size:0.92rem;font-weight:700;color:var(--dark-text-primary);margin:0;display:flex;align-items:center;gap:8px"><i class="fas fa-edit" style="color:var(--apple-teal)"></i>Edit Permissions</h5>
            <button onclick="closeEditPermModal()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.3rem;cursor:pointer;padding:0;line-height:1">&times;</button>
        </div>
        <form id="editPermForm" method="POST">
            @csrf @method('PATCH')
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px">
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Role</label>
                    <select id="editRole" name="role" required style="width:100%;padding:9px 12px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                            onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                        <option value="primary">Primary Handler</option>
                        <option value="backup">Backup Handler</option>
                        <option value="viewer">Viewer Only</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:0.72rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:8px">Permissions</label>
                    <div style="display:flex;flex-direction:column;gap:8px">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" id="editCanSend" name="can_send" value="1" style="width:14px;height:14px;accent-color:var(--apple-blue)"><span style="font-size:0.85rem;color:var(--dark-text-primary)">Dapat Kirim Email</span></label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" id="editCanReceive" name="can_receive" value="1" style="width:14px;height:14px;accent-color:var(--apple-blue)"><span style="font-size:0.85rem;color:var(--dark-text-primary)">Dapat Terima Email</span></label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" id="editCanDelete" name="can_delete" value="1" style="width:14px;height:14px;accent-color:var(--apple-blue)"><span style="font-size:0.85rem;color:var(--dark-text-primary)">Dapat Hapus Email</span></label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer"><input type="checkbox" id="editCanAssign" name="can_assign_others" value="1" style="width:14px;height:14px;accent-color:var(--apple-blue)"><span style="font-size:0.85rem;color:var(--dark-text-primary)">Dapat Assign User Lain</span></label>
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
                <button type="button" onclick="closeEditPermModal()"
                        style="padding:7px 16px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:none;cursor:pointer">Batal</button>
                <button type="submit"
                        style="padding:7px 16px;border:none;border-radius:9px;background:var(--apple-teal);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer">
                    <i class="fas fa-save" style="margin-right:5px"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Delete --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;width:100%;max-width:440px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 style="font-size:0.92rem;font-weight:700;color:var(--apple-red);margin:0;display:flex;align-items:center;gap:8px"><i class="fas fa-exclamation-triangle"></i>Konfirmasi Hapus</h5>
            <button onclick="closeDeleteModal()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.3rem;cursor:pointer;padding:0;line-height:1">&times;</button>
        </div>
        <div style="padding:20px">
            <p style="font-size:0.88rem;color:var(--dark-text-primary);margin:0 0 10px">Yakin ingin menghapus akun email ini?</p>
            <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;border-radius:9px;background:color-mix(in srgb,var(--apple-red) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-red) 18%,transparent)">
                <i class="fas fa-info-circle" style="color:var(--apple-red);flex-shrink:0;margin-top:2px"></i>
                <p style="font-size:0.8rem;color:var(--apple-red);margin:0">Tindakan ini tidak dapat dibatalkan. Semua riwayat email dan assignment akan dihapus permanen.</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <button type="button" onclick="closeDeleteModal()"
                    style="padding:7px 16px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:none;cursor:pointer">Batal</button>
            <form id="deleteForm" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:7px 16px;border:none;border-radius:9px;background:var(--apple-red);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer">
                    <i class="fas fa-trash" style="margin-right:5px"></i>Hapus Permanen
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Unassign User --}}
<div id="unassignModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.7)">
    <div style="background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:16px;width:100%;max-width:440px;box-shadow:0 24px 60px rgba(0,0,0,.6)">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--dark-separator)">
            <h5 style="font-size:0.92rem;font-weight:700;color:var(--apple-orange);margin:0;display:flex;align-items:center;gap:8px"><i class="fas fa-user-times"></i>Hapus Akses User</h5>
            <button onclick="closeUnassignModal()" style="background:none;border:none;color:var(--dark-text-secondary);font-size:1.3rem;cursor:pointer;padding:0;line-height:1">&times;</button>
        </div>
        <div style="padding:20px">
            <p style="font-size:0.88rem;color:var(--dark-text-primary);margin:0 0 10px">Yakin ingin menghapus akses <strong id="unassignUserName" style="color:var(--dark-text-primary)"></strong> dari akun email ini?</p>
            <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;border-radius:9px;background:color-mix(in srgb,var(--apple-orange) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-orange) 18%,transparent)">
                <i class="fas fa-info-circle" style="color:var(--apple-orange);flex-shrink:0;margin-top:2px"></i>
                <p style="font-size:0.8rem;color:var(--apple-orange);margin:0">User akan kehilangan akses ke semua email di akun ini.</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid var(--dark-separator)">
            <button type="button" onclick="closeUnassignModal()"
                    style="padding:7px 16px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;background:none;cursor:pointer">Batal</button>
            <form id="unassignForm" method="POST" style="display:inline">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:7px 16px;border:none;border-radius:9px;background:var(--apple-orange);color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer">
                    <i class="fas fa-user-times" style="margin-right:5px"></i>Hapus Akses
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).style.display='flex'; }
function closeModal(id) { document.getElementById(id).style.display='none'; }

function openAssignModal() { openModal('assignModal'); }
function closeAssignModal() { closeModal('assignModal'); }

function openEditPerms(accountId, userId, role, canSend, canReceive, canDelete, canAssign) {
    document.getElementById('editPermForm').action = `/admin/email-accounts/${accountId}/permissions/${userId}`;
    document.getElementById('editRole').value = role;
    document.getElementById('editCanSend').checked = canSend;
    document.getElementById('editCanReceive').checked = canReceive;
    document.getElementById('editCanDelete').checked = canDelete;
    document.getElementById('editCanAssign').checked = canAssign;
    openModal('editPermModal');
}
function closeEditPermModal() { closeModal('editPermModal'); }

function openDeleteModal(id) {
    document.getElementById('deleteForm').action = `/admin/email-accounts/${id}`;
    openModal('deleteModal');
}
function closeDeleteModal() { closeModal('deleteModal'); }

function openUnassignModal(accountId, userId, name) {
    document.getElementById('unassignForm').action = `/admin/email-accounts/${accountId}/unassign/${userId}`;
    document.getElementById('unassignUserName').textContent = name;
    openModal('unassignModal');
}
function closeUnassignModal() { closeModal('unassignModal'); }

document.addEventListener('keydown', function(e) {
    if (e.key !== 'Escape') return;
    ['assignModal','editPermModal','deleteModal','unassignModal'].forEach(closeModal);
});
['assignModal','editPermModal','deleteModal','unassignModal'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
</script>
@endpush
@endsection
