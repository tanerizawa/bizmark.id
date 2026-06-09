@extends('layouts.admin')
@section('title', 'Create Email Account')
@section('content')
<div style="display:flex;flex-direction:column;gap:16px">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <p style="font-size:0.6rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--dark-text-secondary);margin:0">Tim Email</p>
            <h1 style="font-size:1.2rem;font-weight:700;color:var(--dark-text-primary);margin:4px 0 2px;display:flex;align-items:center;gap:8px">
                <i class="fas fa-plus-circle" style="color:var(--apple-teal);font-size:1rem"></i>Create Email Account
            </h1>
            <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Add a new company email account</p>
        </div>
        <a href="{{ route('admin.email-accounts.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-secondary);font-size:0.8rem;font-weight:600;text-decoration:none"
           onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
            <i class="fas fa-arrow-left" style="font-size:0.75rem"></i>Back to List
        </a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;align-items:start">

        {{-- Left: Form --}}
        <div style="display:flex;flex-direction:column;gap:14px">
            <form action="{{ route('admin.email-accounts.store') }}" method="POST">
                @csrf

                {{-- Basic Information --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden;margin-bottom:14px">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-info-circle" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Basic Information</h5>
                    </div>
                    <div style="padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:14px">

                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Email Address <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="cs@bizmark.id" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('email')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7"><i class="fas fa-info-circle" style="margin-right:3px"></i>Use @bizmark.id domain</p>
                        </div>

                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Display Name <span style="color:var(--apple-red)">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Customer Service" required
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('name')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Account Type <span style="color:var(--apple-red)">*</span>
                            </label>
                            <select name="type" required onchange="updateTypeHelp(this.value)"
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">Select Type</option>
                                <option value="shared" {{ old('type') === 'shared' ? 'selected' : '' }}>Shared (Multiple Users)</option>
                                <option value="personal" {{ old('type') === 'personal' ? 'selected' : '' }}>Personal (Single User)</option>
                            </select>
                            @error('type')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p id="typeHelp" style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7">Choose shared for team emails (cs@, sales@)</p>
                        </div>

                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">
                                Department <span style="color:var(--apple-red)">*</span>
                            </label>
                            <select name="department" required
                                    style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none"
                                    onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                                <option value="">Select Department</option>
                                @foreach(['cs' => 'Customer Service', 'sales' => 'Sales', 'support' => 'Support', 'finance' => 'Finance', 'hr' => 'HR', 'it' => 'IT', 'marketing' => 'Marketing'] as $val => $label)
                                <option value="{{ $val }}" {{ old('department') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('department')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>

                        <div style="grid-column:span 2">
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Description</label>
                            <textarea name="description" rows="3" placeholder="Enter account description (optional)"
                                      style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                      onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('description') }}</textarea>
                            @error('description')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Email Settings --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden;margin-bottom:14px">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-cog" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Email Settings</h5>
                    </div>
                    <div style="padding:18px;display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Forward To (Optional)</label>
                            <input type="email" name="forward_to" value="{{ old('forward_to') }}" placeholder="forward@example.com"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('forward_to')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7"><i class="fas fa-info-circle" style="margin-right:3px"></i>Auto-forward all emails to this address</p>
                        </div>
                        <div>
                            <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Max Daily Emails</label>
                            <input type="number" name="max_daily_emails" value="{{ old('max_daily_emails', 100) }}" min="1"
                                   style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;box-sizing:border-box"
                                   onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">
                            @error('max_daily_emails')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:4px 0 0;opacity:.7"><i class="fas fa-info-circle" style="margin-right:3px"></i>Maximum emails per day</p>
                        </div>
                        <div style="grid-column:span 2">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:10px">
                                <input type="checkbox" name="auto_reply_enabled" id="autoReplyCheckbox" {{ old('auto_reply_enabled') ? 'checked' : '' }}
                                       onchange="toggleAutoReply(this.checked)">
                                <span style="font-size:0.85rem;color:var(--dark-text-primary)">Enable Auto-Reply</span>
                            </label>
                            <div id="autoReplyFields" style="display:{{ old('auto_reply_enabled') ? 'block' : 'none' }}">
                                <label style="font-size:0.68rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:5px">Auto-Reply Message</label>
                                <textarea name="auto_reply_message" rows="4"
                                          placeholder="Thank you for contacting us. We'll get back to you soon..."
                                          style="width:100%;padding:9px 12px;background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:9px;color:var(--dark-text-primary);font-size:0.85rem;outline:none;resize:vertical;box-sizing:border-box"
                                          onfocus="this.style.borderColor='var(--apple-teal)'" onblur="this.style.borderColor='var(--dark-separator)'">{{ old('auto_reply_message') }}</textarea>
                                @error('auto_reply_message')<p style="font-size:0.72rem;color:var(--apple-red);margin:4px 0 0">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assign Users --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden;margin-bottom:14px">
                    <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                        <i class="fas fa-users" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                        <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Assign Users</h5>
                    </div>
                    <div style="padding:18px">
                        <div id="userAssignments" style="display:flex;flex-direction:column;gap:10px"></div>
                        <button type="button" onclick="addUserAssignment()"
                                style="margin-top:10px;display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-secondary);background:none;font-size:0.8rem;font-weight:600;cursor:pointer"
                                onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                            <i class="fas fa-plus" style="font-size:0.72rem"></i>Add User
                        </button>
                        <p style="font-size:0.72rem;color:var(--dark-text-secondary);margin:6px 0 0;opacity:.7"><i class="fas fa-info-circle" style="margin-right:3px"></i>At least one primary handler required</p>
                    </div>
                </div>

                {{-- Status --}}
                <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;padding:16px 18px;margin-bottom:14px">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                        <input type="checkbox" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span style="font-size:0.85rem;color:var(--dark-text-primary)">Active (Account can send/receive emails)</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:10px">
                    <a href="{{ route('admin.email-accounts.index') }}"
                       style="padding:9px 18px;border:1px solid var(--dark-separator);border-radius:10px;color:var(--dark-text-secondary);font-size:0.82rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px"
                       onmouseover="this.style.color='var(--dark-text-primary)'" onmouseout="this.style.color='var(--dark-text-secondary)'">
                        <i class="fas fa-times"></i>Cancel
                    </a>
                    <button type="submit"
                            style="padding:9px 20px;background:var(--apple-teal);color:#fff;border:none;border-radius:10px;font-size:0.82rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px"
                            onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <i class="fas fa-save"></i>Create Email Account
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Help --}}
        <div style="position:sticky;top:16px">
            <div style="background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:14px;overflow:hidden">
                <div style="padding:14px 18px;border-bottom:1px solid var(--dark-separator);display:flex;align-items:center;gap:8px">
                    <i class="fas fa-question-circle" style="color:var(--dark-text-secondary);font-size:0.8rem"></i>
                    <h5 style="font-size:0.88rem;font-weight:700;color:var(--dark-text-primary);margin:0">Help</h5>
                </div>
                <div style="padding:18px;display:flex;flex-direction:column;gap:16px">
                    <div>
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px;display:flex;align-items:center;gap:6px">
                            <i class="fas fa-users" style="color:var(--apple-green)"></i>Shared Account
                        </p>
                        <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Use for team emails like cs@, sales@, or support@. Multiple users can access and respond.</p>
                    </div>
                    <div>
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 4px;display:flex;align-items:center;gap:6px">
                            <i class="fas fa-user" style="color:var(--apple-purple)"></i>Personal Account
                        </p>
                        <p style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0">Use for individual staff like john@bizmark.id. Only one user can be assigned.</p>
                    </div>
                    <div>
                        <p style="font-size:0.82rem;font-weight:600;color:var(--dark-text-primary);margin:0 0 6px;display:flex;align-items:center;gap:6px">
                            <i class="fas fa-shield-alt" style="color:var(--apple-blue)"></i>User Roles
                        </p>
                        <ul style="font-size:0.78rem;color:var(--dark-text-secondary);margin:0;padding-left:16px;display:flex;flex-direction:column;gap:4px">
                            <li><strong style="color:var(--dark-text-primary)">Primary:</strong> Main handler, full access</li>
                            <li><strong style="color:var(--dark-text-primary)">Backup:</strong> Can send/receive, limited delete</li>
                            <li><strong style="color:var(--dark-text-primary)">Viewer:</strong> Read-only access</li>
                        </ul>
                    </div>
                    <div style="display:flex;align-items:flex-start;gap:8px;background:color-mix(in srgb,var(--apple-blue) 10%,transparent);border:1px solid color-mix(in srgb,var(--apple-blue) 25%,transparent);border-radius:10px;padding:10px 12px">
                        <i class="fas fa-lightbulb" style="color:var(--apple-blue);margin-top:2px;flex-shrink:0;font-size:0.78rem"></i>
                        <span style="font-size:0.75rem;color:var(--apple-blue)"><strong>Tip:</strong> Configure Cloudflare Email Routing to point to your webhook URL for incoming emails.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let userIndex = 0;

function toggleAutoReply(checked) {
    document.getElementById('autoReplyFields').style.display = checked ? 'block' : 'none';
}

function updateTypeHelp(val) {
    document.getElementById('typeHelp').textContent = val === 'personal'
        ? 'Personal accounts can only have one user assigned'
        : 'Choose shared for team emails (cs@, sales@)';
}

function addUserAssignment() {
    const container = document.getElementById('userAssignments');
    const idx = userIndex;
    const row = document.createElement('div');
    row.id = 'user-row-' + idx;
    row.style.cssText = 'background:var(--dark-bg-secondary);border:1px solid var(--dark-separator);border-radius:12px;padding:14px 16px';
    const inputStyle = 'width:100%;padding:7px 10px;background:var(--dark-bg-tertiary);border:1px solid var(--dark-separator);border-radius:8px;color:var(--dark-text-primary);font-size:0.82rem;outline:none;box-sizing:border-box';
    const labelStyle = 'font-size:0.65rem;font-weight:600;color:var(--dark-text-secondary);display:block;margin-bottom:3px';
    row.innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end">
            <div>
                <label style="${labelStyle}">User</label>
                <select name="assignments[${idx}][user_id]" required style="${inputStyle}">
                    <option value="">Select User</option>
                    @foreach($availableUsers ?? [] as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="${labelStyle}">Role</label>
                <select name="assignments[${idx}][role]" required style="${inputStyle}">
                    <option value="primary">Primary</option>
                    <option value="backup">Backup</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>
            <div>
                <label style="${labelStyle}">Permissions</label>
                <div style="display:flex;gap:10px;padding:7px 0;flex-wrap:wrap">
                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:0.78rem;color:var(--dark-text-secondary)">
                        <input type="checkbox" name="assignments[${idx}][can_send]" value="1" checked>Send
                    </label>
                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:0.78rem;color:var(--dark-text-secondary)">
                        <input type="checkbox" name="assignments[${idx}][can_receive]" value="1" checked>Receive
                    </label>
                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:0.78rem;color:var(--dark-text-secondary)">
                        <input type="checkbox" name="assignments[${idx}][can_delete]" value="1">Delete
                    </label>
                </div>
            </div>
            <button type="button" onclick="removeUserAssignment(${idx})"
                    style="padding:7px 10px;border:1px solid color-mix(in srgb,var(--apple-red) 30%,var(--dark-separator));border-radius:8px;color:var(--apple-red);background:color-mix(in srgb,var(--apple-red) 10%,transparent);font-size:0.78rem;cursor:pointer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    userIndex++;
}

function removeUserAssignment(idx) {
    document.getElementById('user-row-' + idx)?.remove();
}

document.addEventListener('DOMContentLoaded', () => addUserAssignment());
</script>
@endpush
@endsection
