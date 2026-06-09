<aside class="app-sidebar">
    <div class="sidebar-header">
        <h1 style="font-size: 1.25rem; font-weight: 700; color: var(--dark-text-primary); margin: 0; display: flex; align-items: center;">
            <i class="fas fa-shield-alt" style="color: var(--accent); margin-right: 0.5rem;"></i>
            Bizmark.ID
        </h1>
        <p style="font-size: 0.75rem; color: var(--dark-text-secondary); margin: 0.25rem 0 0 0;">Admin Portal</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-links">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-home"></i><span>Dashboard</span></div>
                </a>
                <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-project-diagram"></i><span>Proyek</span></div>
                    @if(isset($navCounts['projects']) && $navCounts['projects'] > 0)
                        <span class="nav-badge">{{ $navCounts['projects'] }}</span>
                    @endif
                </a>
                <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-tasks"></i><span>Tugas</span></div>
                    @if(isset($navCounts['pending_tasks']) && $navCounts['pending_tasks'] > 0)
                        <span class="nav-badge badge-alert">{{ $navCounts['pending_tasks'] }}</span>
                    @endif
                </a>
                <a href="{{ route('documents.index') }}" class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-file-alt"></i><span>Dokumen</span></div>
                    @if(isset($navCounts['documents']) && $navCounts['documents'] > 0)
                        <span class="nav-badge">{{ $navCounts['documents'] }}</span>
                    @endif
                </a>
                <a href="{{ route('institutions.index') }}" class="nav-link {{ request()->routeIs('institutions.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-building"></i><span>Instansi</span></div>
                    @if(isset($navCounts['institutions']) && $navCounts['institutions'] > 0)
                        <span class="nav-badge">{{ $navCounts['institutions'] }}</span>
                    @endif
                </a>
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-users"></i><span>Klien</span></div>
                    @if(isset($navCounts['clients']) && $navCounts['clients'] > 0)
                        <span class="nav-badge">{{ $navCounts['clients'] }}</span>
                    @endif
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-cog"></i><span>Pengaturan</span></div>
                </a>
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.ai-settings.index') }}" class="nav-link {{ request()->routeIs('admin.ai-settings.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-brain"></i><span>AI Settings</span></div>
                </a>
                @endif
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Lead Management</div>
            <div class="nav-links">
                <a href="{{ route('admin.leads.index') }}" class="nav-link {{ request()->routeIs('admin.leads.*') || request()->routeIs('admin.service-inquiries.*') || request()->routeIs('admin.consultation-leads.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-user-tag"></i><span>Kelola Lead</span></div>
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Human Resource</div>
            <div class="nav-links">
                <a href="{{ route('admin.permits.index') }}" class="nav-link {{ request()->routeIs('admin.permits.*') || request()->routeIs('admin.permit-dashboard') || request()->routeIs('admin.permit-applications.*') || request()->routeIs('permit-types.*') || request()->routeIs('admin.payments.*') || request()->routeIs('admin.master-data.*') || request()->routeIs('admin.settings.kbli.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-briefcase"></i><span>Kelola Perizinan</span></div>
                    @if($permitNotifications['total'] > 0)
                        <span class="nav-badge badge-alert">{{ $permitNotifications['total'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Recruitment</div>
            <div class="nav-links">
                <a href="{{ route('admin.recruitment.index') }}" class="nav-link {{ request()->routeIs('admin.recruitment.*') || request()->routeIs('admin.jobs.*') || request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-user-tie"></i><span>Kelola Rekrutmen</span></div>
                    @if($otherNotifications['pending_job_apps'] > 0)
                        <span class="nav-badge badge-alert">{{ $otherNotifications['pending_job_apps'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Komunikasi</div>
            <div class="nav-links">
                <a href="{{ route('admin.email-management.index') }}" class="nav-link {{ request()->routeIs('admin.email-management.*') || request()->routeIs('admin.inbox.*') || request()->routeIs('admin.campaigns.*') || request()->routeIs('admin.subscribers.*') || request()->routeIs('admin.templates.*') || request()->routeIs('admin.email.settings.*') || request()->routeIs('admin.email-accounts.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-envelope"></i><span>Kelola Email</span></div>
                    @if($otherNotifications['unread_emails'] > 0)
                        <span class="nav-badge badge-alert">{{ $otherNotifications['unread_emails'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Keuangan</div>
            <div class="nav-links">
                <a href="{{ route('cash-accounts.index') }}" class="nav-link {{ request()->routeIs('cash-accounts.*') || request()->routeIs('reconciliations.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-wallet"></i><span>Akun Kas & Bank</span></div>
                    @if($otherNotifications['pending_reconciliations'] > 0)
                        <span class="nav-badge badge-warning">{{ $otherNotifications['pending_reconciliations'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">Konten</div>
            <div class="nav-links">
                <a href="{{ route('articles.index') }}" class="nav-link {{ request()->routeIs('articles.*') && !request()->routeIs('auto-post.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-newspaper"></i><span>Artikel & Berita</span></div>
                </a>
                <a href="{{ route('admin.services.index') }}" class="nav-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-layer-group"></i><span>Kelola Layanan</span></div>
                </a>
                @can('content.manage')
                <a href="{{ route('auto-post.index') }}" class="nav-link {{ request()->routeIs('auto-post.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-robot"></i><span>Auto-Post AI</span></div>
                </a>
                @endcan
                <a href="{{ route('admin.seo.command-center') }}" class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}">
                    <div class="nav-link-content"><i class="fas fa-search-dollar"></i><span>SEO Command</span></div>
                </a>
            </div>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <a href="{{ route('admin.profile.edit') }}" class="sidebar-profile-link">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 600; font-size: 0.875rem; flex-shrink: 0;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <p style="font-size: 0.875rem; font-weight: 500; color: var(--text-primary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="sidebar-logout-btn" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
