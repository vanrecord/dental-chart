<header class="app-header no-print">
    <div class="header-left">
        <button type="button" id="sidebarToggle" class="btn-toggle" aria-label="Toggle Navigation Sidebar" title="Toggle Sidebar">
            <svg class="hamburger-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <div class="header-breadcrumb">
            <span class="crumb-root">OdontoCare</span>
            <span class="crumb-separator">/</span>
            <span class="crumb-current">@yield('title', 'Workspace')</span>
        </div>
    </div>

    <div class="header-right">
        <div class="header-quick-links">
            <a href="{{ route('patients.create') }}" class="header-btn header-btn-primary">
                <span class="btn-plus">+</span>
                <span>Patient</span>
            </a>
            <a href="{{ route('dental-charts.create') }}" class="header-btn header-btn-outline">
                <span class="btn-plus">+</span>
                <span>Chart</span>
            </a>
            <a href="{{ url('/dental') }}" class="header-btn header-btn-odontogram" title="Open Interactive Odontogram">
                <span>🦷 Chart</span>
            </a>
        </div>

        <div class="header-divider"></div>

        <div class="header-user">
            <div class="avatar-small">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'Dr' }}
            </div>
            <div class="user-details-mini">
                <span class="user-name-mini">{{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}</span>
                <span class="user-status-dot" title="Online"></span>
            </div>
            @auth
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Sign out" style="background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 4px; display: flex; align-items: center;" aria-label="Sign out">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </button>
                </form>
            @endauth
        </div>
    </div>
</header>

