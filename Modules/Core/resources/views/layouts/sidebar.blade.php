<aside id="appSidebar" class="app-sidebar" role="navigation" aria-label="Main Navigation">
    <div class="sidebar-header">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand">
            <span class="brand-icon">🦷</span>
            <div class="brand-text">
                <span class="brand-title">OdontoCare</span>
                <span class="brand-sub">Dental Chart System</span>
            </div>
        </a>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-section-label">Main Navigation</div>

        <!-- Dashboard -->
        <a href="{{ url('/dashboard') }}" class="nav-item {{ (request()->is('dashboard*') || request()->is('/')) ? 'active' : '' }}" data-tooltip="Dashboard">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                    <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                    <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                </svg>
            </span>
            <span class="nav-label">Dashboard</span>
        </a>

        <!-- Patients -->
        <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}" data-tooltip="Patients">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span class="nav-label">Patients</span>
        </a>

        <!-- Dental Charts with Submenu -->
        <div class="nav-group {{ (request()->routeIs('dental-charts.*') || request()->is('dental*')) ? 'open active-group' : '' }}">
            <button type="button" class="nav-item nav-parent {{ (request()->routeIs('dental-charts.*') || request()->is('dental*')) ? 'active' : '' }}" data-tooltip="Dental Charts" aria-expanded="{{ (request()->routeIs('dental-charts.*') || request()->is('dental*')) ? 'true' : 'false' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </span>
                <span class="nav-label">Dental Charts</span>
                <span class="nav-arrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="nav-submenu">
                <a href="{{ route('dental-charts.index') }}" class="subnav-item {{ request()->routeIs('dental-charts.index') ? 'active' : '' }}">
                    <span class="subnav-dot"></span> All Charts
                </a>
                <a href="{{ route('dental-charts.create') }}" class="subnav-item {{ request()->routeIs('dental-charts.create') ? 'active' : '' }}">
                    <span class="subnav-dot"></span> New Chart
                </a>
                <a href="{{ url('/dental') }}" class="subnav-item {{ request()->is('dental*') ? 'active' : '' }}">
                    <span class="subnav-dot"></span> Odontogram Editor
                </a>
            </div>
        </div>

        <!-- Interactive Odontogram Direct -->
        <a href="{{ url('/dental') }}" class="nav-item {{ request()->is('dental*') ? 'active' : '' }}" data-tooltip="Interactive Odontogram">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2C8.5 2 6 4.5 6 7.5c0 3 1.5 5 2 8 .5 3 1.5 6.5 4 6.5s3.5-3.5 4-6.5c.5-3 2-5 2-8C18 4.5 15.5 2 12 2z"></path>
                    <path d="M9 10h6"></path>
                </svg>
            </span>
            <span class="nav-label">Odontogram</span>
            <span class="nav-badge">FDI</span>
        </a>

        <!-- Dental Conditions with Submenu -->
        <div class="nav-group {{ request()->routeIs('dental-conditions.*') ? 'open active-group' : '' }}">
            <button type="button" class="nav-item nav-parent {{ request()->routeIs('dental-conditions.*') ? 'active' : '' }}" data-tooltip="Conditions & Legend" aria-expanded="{{ request()->routeIs('dental-conditions.*') ? 'true' : 'false' }}">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="4"></circle>
                        <line x1="4.93" y1="4.93" x2="9.17" y2="9.17"></line>
                        <line x1="14.83" y1="14.83" x2="19.07" y2="19.07"></line>
                        <line x1="14.83" y1="9.17" x2="19.07" y2="4.93"></line>
                        <line x1="4.93" y1="19.07" x2="9.17" y2="14.83"></line>
                    </svg>
                </span>
                <span class="nav-label">Conditions</span>
                <span class="nav-arrow">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </button>
            <div class="nav-submenu">
                <a href="{{ route('dental-conditions.index') }}" class="subnav-item {{ request()->routeIs('dental-conditions.index') ? 'active' : '' }}">
                    <span class="subnav-dot"></span> Condition List
                </a>
                <a href="{{ route('dental-conditions.create') }}" class="subnav-item {{ request()->routeIs('dental-conditions.create') ? 'active' : '' }}">
                    <span class="subnav-dot"></span> New Condition
                </a>
            </div>
        </div>

        <div class="menu-section-label" style="margin-top: 1rem;">Administration</div>

        <!-- Users -->
        @if(!auth()->check() || auth()->user()->can('users:users:view'))
            <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" data-tooltip="Users">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span class="nav-label">Users</span>
            </a>
        @endif

        <!-- Roles & Permissions -->
        @if(!auth()->check() || auth()->user()->can('users:roles:view'))
            <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}" data-tooltip="Roles & Permissions">
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                </span>
                <span class="nav-label">Roles & Permissions</span>
            </a>
        @endif

        <div class="menu-section-label" style="margin-top: 1rem;">System</div>

        <!-- Activity & API Health -->
        <a href="{{ url('/api/health') }}" target="_blank" class="nav-item" data-tooltip="API Health">
            <span class="nav-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
            </span>
            <span class="nav-label">API Health</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="avatar-circle">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'Dr' }}
            </div>
            <div class="user-meta">
                <span class="user-name">{{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}</span>
                <span class="user-role">{{ (auth()->check() && auth()->user()->roles->first()) ? auth()->user()->roles->first()->label : 'Dental Surgeon' }}</span>
            </div>
        </div>
        @auth
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 8px;">
                @csrf
                <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; font-size: 0.75rem; padding: 4px 8px; color: #dc2626; border-color: #fca5a5;">
                    Sign Out
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm" style="display: block; text-align: center; margin-top: 8px; font-size: 0.75rem; padding: 4px 8px;">
                Sign In
            </a>
        @endauth
    </div>
</aside>

