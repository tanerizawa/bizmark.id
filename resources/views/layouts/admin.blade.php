<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" data-theme="dark">
<head>
    @include('partials.head')
    <title>@yield('title', 'Dashboard') - Bizmark Permit Management</title>
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="@auth authenticated @endauth">
    @auth
    <div class="app-shell">
        @include('partials.navbar.admin')

        <div class="app-main">
            <header class="app-topbar">
                <div>
                    <h2 style="font-size: 1rem; font-weight: 600; color: var(--text-primary); margin: 0;">@yield('page-title', 'Dashboard')</h2>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;">{{ now()->format('l, d F Y') }}</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="position: relative;">
                        <a href="{{ route('admin.notifications') }}" class="sidebar-icon-btn" title="Notifikasi">
                            <i class="fas fa-bell"></i>
                            @if(($permitNotifications['total'] ?? 0) + ($otherNotifications['unread_emails'] ?? 0) > 0)
                                <span style="position: absolute; top: 0.25rem; right: 0.25rem; width: 0.5rem; height: 0.5rem; background: var(--color-error); border-radius: 50%; border: 2px solid var(--surface);"></span>
                            @endif
                        </a>
                    </div>
                    <button onclick="toggleSearch()" class="sidebar-icon-btn" title="Pencarian">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </header>

            <div id="searchOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9999; backdrop-filter: blur(8px);" onclick="toggleSearch()">
                <div style="max-width: 600px; margin: 6rem auto; padding: 0 1.5rem;">
                    <div style="background: var(--surface-raised); border-radius: 16px; padding: 1.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.6);" onclick="event.stopPropagation()">
                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                            <i class="fas fa-search" style="color: var(--text-tertiary);"></i>
                            <input type="text" id="globalSearchInput" placeholder="Cari proyek, dokumen, klien..."
                                   style="flex: 1; background: transparent; border: none; outline: none; color: var(--text-primary); font-size: 1.25rem;" autofocus>
                            <button onclick="toggleSearch()" style="padding: 0.5rem; color: var(--text-tertiary); background: transparent; border: none; cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="searchResults" style="max-height: 400px; overflow-y: auto;">
                            <p style="text-align: center; color: var(--text-tertiary); padding: 2rem;">Mulai ketik untuk mencari...</p>
                        </div>
                    </div>
                </div>
            </div>

            <main class="app-content">
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <div id="app">
        @yield('content')
    </div>
    @endauth

    <script>
    (function() {
        function updateScreenWidth() {
            const width = window.innerWidth;
            fetch('/api/set-screen-width', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ width: width })
            }).catch(() => {});
        }
        updateScreenWidth();
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                updateScreenWidth();
                const currentWidth = window.innerWidth;
                const wasMobile = sessionStorage.getItem('wasMobile') === 'true';
                const isMobileNow = currentWidth < 768;
                if (wasMobile !== isMobileNow) {
                    sessionStorage.setItem('wasMobile', isMobileNow);
                    setTimeout(() => window.location.reload(), 500);
                }
            }, 500);
        });
        sessionStorage.setItem('wasMobile', (window.innerWidth < 768).toString());
    })();

    function toggleSearch() {
        const overlay = document.getElementById('searchOverlay');
        const input = document.getElementById('globalSearchInput');
        if (overlay.style.display === 'none') {
            overlay.style.display = 'block';
            setTimeout(() => input.focus(), 100);
        } else {
            overlay.style.display = 'none';
            input.value = '';
            document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--text-tertiary); padding: 2rem;">Mulai ketik untuk mencari...</p>';
        }
    }

    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            toggleSearch();
        }
        if (e.key === 'Escape') {
            const overlay = document.getElementById('searchOverlay');
            if (overlay && overlay.style.display !== 'none') { toggleSearch(); }
        }
    });

    @auth
    let searchTimeout;
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('globalSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                const query = e.target.value.trim();
                if (query.length < 2) {
                    document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--text-tertiary); padding: 2rem;">Mulai ketik untuk mencari...</p>';
                    return;
                }
                searchTimeout = setTimeout(() => {
                    document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--text-tertiary); padding: 2rem;"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</p>';
                    fetch(`{{ route('admin.search') }}?q=${encodeURIComponent(query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        const rc = document.getElementById('searchResults');
                        if (data.total === 0) {
                            rc.innerHTML = '<div style="padding: 2rem; text-align: center;"><i class="fas fa-search" style="font-size: 2rem; color: var(--text-tertiary); margin-bottom: 1rem;"></i><p style="color: var(--text-secondary);">Tidak ada hasil</p></div>';
                            return;
                        }
                        let html = `<div style="padding: 0.5rem;"><p style="color: var(--text-tertiary); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 1rem; padding: 0 0.5rem;">${data.total} hasil ditemukan</p>`;
                        const cats = [{key:'projects',label:'Proyek'},{key:'tasks',label:'Task'},{key:'documents',label:'Dokumen'},{key:'clients',label:'Klien'},{key:'institutions',label:'Instansi'},{key:'permits',label:'Perizinan'}];
                        cats.forEach(cat => {
                            const items = data.results[cat.key];
                            if (items && items.length > 0) {
                                html += `<div style="margin-bottom: 1.5rem;"><p style="color: var(--text-tertiary); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.5rem; padding: 0 0.5rem;">${cat.label}</p>`;
                                items.forEach(item => {
                                    html += `<a href="${item.url}" class="search-result-item"><div style="width: 2.5rem; height: 2.5rem; border-radius: 8px; background: var(--${item.color}); display: flex; align-items: center; justify-content: center; color: white;"><i class="fas ${item.icon}"></i></div><div style="flex:1;min-width:0;"><div style="font-weight:500;margin-bottom:0.25rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${item.title}</div><div style="font-size:0.75rem;color:var(--text-tertiary);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${item.subtitle}</div></div><div style="font-size:0.75rem;color:var(--text-tertiary);padding:0.25rem 0.5rem;background:var(--surface-cool);border-radius:6px;">${item.type}</div></a>`;
                                });
                                html += `</div>`;
                            }
                        });
                        html += `</div>`;
                        rc.innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById('searchResults').innerHTML = '<p style="text-align: center; color: var(--color-error); padding: 2rem;">Terjadi kesalahan</p>';
                    });
                }, 300);
            });
        }
    });
    @endauth
    </script>

    <script>
    function toggleSubmenu(button) {
        const submenu = button.parentElement;
        const content = submenu.querySelector('.nav-submenu-content');
        const icon = button.querySelector('.submenu-icon');
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
            submenu.classList.add('active');
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
            if (!button.parentElement.querySelector('.nav-sublink.active')) { submenu.classList.remove('active'); }
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.nav-submenu.active').forEach(submenu => {
            const content = submenu.querySelector('.nav-submenu-content');
            const icon = submenu.querySelector('.submenu-icon');
            if (content && icon) { content.style.display = 'block'; icon.style.transform = 'rotate(180deg)'; }
        });
    });
    </script>

    @include('partials.scripts')
</body>
</html>
