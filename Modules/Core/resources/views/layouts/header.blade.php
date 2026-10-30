<header class="app-header no-print">
    <div class="header-left">
        <button type="button" id="sidebarToggle" class="btn-toggle" aria-label="Toggle Navigation Sidebar" title="Toggle Sidebar">
            <svg class="hamburger-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <nav class="header-breadcrumb" aria-label="Breadcrumb">
            <span class="crumb-root">OdontoCare</span>
            <span class="crumb-separator" aria-hidden="true">/</span>
            <span class="crumb-current" aria-current="location">@yield('title', 'Workspace')</span>
        </nav>
    </div>

    <div class="header-right">
        <div class="header-quick-links">
            <a href="{{ route('patients.create') }}" class="header-btn header-btn-primary" title="Create New Patient">
                <span class="btn-plus">+</span>
                <span>Patient</span>
            </a>
            <a href="{{ route('dental-charts.create') }}" class="header-btn header-btn-outline" title="Create New Dental Chart">
                <span class="btn-plus">+</span>
                <span>Chart</span>
            </a>
            <a href="{{ url('/dental') }}" class="header-btn header-btn-odontogram" title="Open Interactive Odontogram">
                <span>🦷 Chart</span>
            </a>
        </div>

        <div class="header-divider"></div>

        <!-- User Profile Dropdown Container -->
        <div class="header-user-wrapper" id="headerUserDropdownWrapper">
            <button type="button" class="header-user-btn" id="headerUserBtn" aria-expanded="false" aria-haspopup="true" title="User Menu">
                <div class="avatar-small">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'Dr' }}
                </div>
                <div class="user-details-mini">
                    <span class="user-name-mini">{{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}</span>
                    <span class="user-status-dot" title="Online"></span>
                </div>
                <svg class="user-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div class="header-dropdown-menu" id="headerUserDropdown" role="menu" aria-label="User Account Menu">
                <div class="dropdown-header">
                    <div class="dropdown-user-name">{{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}</div>
                    <div class="dropdown-user-email">{{ auth()->check() ? (auth()->user()->email ?? auth()->user()->username) : 'surgeon@odontocare.com' }}</div>
                    <div class="dropdown-user-role">
                        <span class="badge badge-role">{{ (auth()->check() && auth()->user()->roles->first()) ? auth()->user()->roles->first()->label : 'Dental Surgeon' }}</span>
                    </div>
                </div>

                <div class="dropdown-divider"></div>

                <div class="dropdown-body">
                    <a href="{{ url('/dashboard') }}" class="dropdown-item" role="menuitem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    @can('settings:general:view')
                        <a href="{{ route('settings.general') }}" class="dropdown-item" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="3"></circle>
                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                            </svg>
                            <span>Clinic Settings</span>
                        </a>
                    @endcan

                    <a href="{{ url('/dental') }}" class="dropdown-item" role="menuitem">
                        <span style="font-size: 1rem; line-height: 1; display: inline-flex; align-items: center; justify-content: center; width: 16px;">🦷</span>
                        <span>Odontogram Canvas</span>
                    </a>
                </div>

                <div class="dropdown-divider"></div>

                <div class="dropdown-footer">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-logout-btn" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="dropdown-item dropdown-login-btn" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <span>Sign In</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
