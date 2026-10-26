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
            <div class="avatar-small">Dr</div>
            <div class="user-details-mini">
                <span class="user-name-mini">Dr. Alex Smith</span>
                <span class="user-status-dot" title="Online"></span>
            </div>
        </div>
    </div>
</header>
