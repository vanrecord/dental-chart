<aside id="appSidebar" class="app-sidebar" role="navigation" aria-label="Main Navigation">
    <div class="sidebar-header">
        <a href="{{ url('/dashboard') }}" class="sidebar-brand">
            @if(!empty($clinicSetting?->logo_url))
                <img src="{{ $clinicSetting->logo_url }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain; border-radius: 6px; flex-shrink: 0;">
            @else
                <span class="brand-icon">🦷</span>
            @endif
            <div class="brand-text">
                <span class="brand-title"
                      id="sidebarBrandTitle"
                      data-text="{{ $clinicSetting?->english_title ?: 'OdontoCare' }}"
                      title="{{ $clinicSetting?->english_title ?: 'OdontoCare' }}"
                      style="{{ !empty($clinicSetting?->english_title_color) ? 'color: ' . $clinicSetting->english_title_color . ';' : '' }}">
                    <span class="brand-marquee-track">
                        <span class="brand-marquee-content">{{ $clinicSetting?->english_title ?: 'OdontoCare' }}</span>
                    </span>
                </span>
                <span class="brand-sub"
                      id="sidebarBrandSub"
                      data-text="{{ $clinicSetting?->khmer_title ?: 'Dental Chart System' }}"
                      title="{{ $clinicSetting?->khmer_title ?: 'Dental Chart System' }}"
                      style="{{ !empty($clinicSetting?->khmer_title_color) ? 'color: ' . $clinicSetting->khmer_title_color . ';' : '' }}">
                    <span class="brand-marquee-track">
                        <span class="brand-marquee-content">{{ $clinicSetting?->khmer_title ?: 'Dental Chart System' }}</span>
                    </span>
                </span>
            </div>
        </a>
    </div>

    <nav class="sidebar-menu">
        <div class="menu-section-label">Main Navigation</div>

        <!-- Dashboard -->
        <a href="{{ url('/dashboard') }}" class="nav-item {{ (request()->is('dashboard*') || request()->is('/')) ? 'active' : '' }}" data-tooltip="Dashboard" {!! (request()->is('dashboard*') || request()->is('/')) ? 'aria-current="page"' : '' !!}>
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

        <!-- Register -->
        @can('register:register:view')
            <a href="{{ route('register.index') }}" class="nav-item {{ request()->routeIs('register.*') ? 'active' : '' }}" data-tooltip="Register" {!! request()->routeIs('register.*') ? 'aria-current="page"' : '' !!}>
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </span>
                <span class="nav-label">Register</span>
            </a>
        @endcan

        <!-- Patients -->
        @can('patient:patients:view')
            <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.*') ? 'active' : '' }}" data-tooltip="Patients" {!! request()->routeIs('patients.*') ? 'aria-current="page"' : '' !!}>
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
        @endcan

        <!-- Doctors -->
        @can('hospital:doctors:view')
            <a href="{{ route('hospital.doctors.index') }}" class="nav-item {{ request()->routeIs('hospital.doctors.*') ? 'active' : '' }}" data-tooltip="Doctors" {!! request()->routeIs('hospital.doctors.*') ? 'aria-current="page"' : '' !!}>
                <span class="nav-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 3v5a6.5 6.5 0 0 0 13 0V3"></path>
                        <path d="M6 3h-3"></path>
                        <path d="M21 3h-3"></path>
                        <path d="M11 14.5v3.5a4 4 0 0 0 8 0v-2"></path>
                        <circle cx="19" cy="14" r="2"></circle>
                    </svg>
                </span>
                <span class="nav-label">Doctors</span>
            </a>
        @endcan

        <!-- Billing Section Divider -->
        @if(auth()->user()?->can('invoice:invoices:view') || auth()->user()?->can('payment:payments:view'))
            <div class="menu-section-divider"></div>
            <div class="menu-section-label">Billing & Finance</div>

            <!-- Invoice -->
            @can('invoice:invoices:view')
                <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}" data-tooltip="Invoice" {!! request()->routeIs('invoices.*') ? 'aria-current="page"' : '' !!}>
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="6" y1="8" x2="10" y2="8"></line>
                            <line x1="6" y1="12" x2="18" y2="12"></line>
                            <line x1="6" y1="16" x2="14" y2="16"></line>
                        </svg>
                    </span>
                    <span class="nav-label">Invoice</span>
                </a>
            @endcan

            <!-- Payment -->
            @can('payment:payments:view')
                <a href="{{ route('payments.index') }}" class="nav-item {{ request()->routeIs('payments.*') ? 'active' : '' }}" data-tooltip="Payment" {!! request()->routeIs('payments.*') ? 'aria-current="page"' : '' !!}>
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Payment</span>
                </a>
            @endcan
        @endif

        <!-- Dental & Clinical Section Divider -->
        @if(auth()->user()?->can('odontogram:dental_chart:view') || auth()->user()?->can('conditions:dental_condition:view') || auth()->user()?->can('hospital:service-categories:view') || auth()->user()?->can('hospital:product-categories:view') || auth()->user()?->can('hospital:services:view') || auth()->user()?->can('hospital:products:view') || auth()->user()?->can('hospital:doctors:view'))
            <div class="menu-section-divider"></div>
            <div class="menu-section-label">Clinical & Services</div>

            <!-- Dental Charts with Submenu -->
            @can('odontogram:dental_chart:view')
                <div class="nav-group {{ (request()->routeIs('dental-charts.*', 'dental.*') || request()->is('dental', 'dental/*')) ? 'open active-group' : '' }}">
                    <button type="button" class="nav-item nav-parent {{ (request()->routeIs('dental-charts.*', 'dental.*') || request()->is('dental', 'dental/*')) ? 'active' : '' }}" data-tooltip="Dental Charts" aria-expanded="{{ (request()->routeIs('dental-charts.*', 'dental.*') || request()->is('dental', 'dental/*')) ? 'true' : 'false' }}" aria-haspopup="true">
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
                        <a href="{{ route('dental-charts.index') }}" class="subnav-item {{ request()->routeIs('dental-charts.index') ? 'active' : '' }}" {!! request()->routeIs('dental-charts.index') ? 'aria-current="page"' : '' !!}>
                            <span class="subnav-dot"></span>
                            <span class="subnav-text">All Charts</span>
                        </a>
                        @can('odontogram:dental_chart:create')
                            <a href="{{ route('dental-charts.create') }}" class="subnav-item {{ request()->routeIs('dental-charts.create') ? 'active' : '' }}" {!! request()->routeIs('dental-charts.create') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">New Chart</span>
                            </a>
                        @endcan
                        <a href="{{ url('/dental') }}" class="subnav-item {{ (request()->routeIs('dental.*') || request()->is('dental', 'dental/*')) ? 'active' : '' }}" {!! (request()->routeIs('dental.*') || request()->is('dental', 'dental/*')) ? 'aria-current="page"' : '' !!}>
                            <span class="subnav-dot"></span>
                            <span class="subnav-text">Odontogram Editor</span>
                        </a>
                    </div>
                </div>
            @endcan

            <!-- Dental Conditions with Submenu -->
            @can('conditions:dental_condition:view')
                <div class="nav-group {{ request()->routeIs('dental-conditions.*') ? 'open active-group' : '' }}">
                    <button type="button" class="nav-item nav-parent {{ request()->routeIs('dental-conditions.*') ? 'active' : '' }}" data-tooltip="Conditions & Legend" aria-expanded="{{ request()->routeIs('dental-conditions.*') ? 'true' : 'false' }}" aria-haspopup="true">
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
                        <a href="{{ route('dental-conditions.index') }}" class="subnav-item {{ request()->routeIs('dental-conditions.*') ? 'active' : '' }}" {!! request()->routeIs('dental-conditions.*') ? 'aria-current="page"' : '' !!}>
                            <span class="subnav-dot"></span>
                            <span class="subnav-text">Condition List</span>
                        </a>
                    </div>
                </div>
            @endcan

            <!-- Hospital with Submenu -->
            @if(auth()->user()?->can('hospital:service-categories:view') || auth()->user()?->can('hospital:product-categories:view') || auth()->user()?->can('hospital:services:view') || auth()->user()?->can('hospital:products:view') || auth()->user()?->can('hospital:doctors:view'))
                <div class="nav-group {{ request()->routeIs('hospital.*') ? 'open active-group' : '' }}">
                    <button type="button" class="nav-item nav-parent {{ request()->routeIs('hospital.*') ? 'active' : '' }}" data-tooltip="Hospital" aria-expanded="{{ request()->routeIs('hospital.*') ? 'true' : 'false' }}" aria-haspopup="true">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21h18"></path>
                                <path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"></path>
                                <path d="M9 21v-4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4"></path>
                                <line x1="10" y1="9" x2="14" y2="9"></line>
                                <line x1="12" y1="7" x2="12" y2="11"></line>
                            </svg>
                        </span>
                        <span class="nav-label">Hospital</span>
                        <span class="nav-arrow">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </span>
                    </button>
                    <div class="nav-submenu">
                        @can('hospital:service-categories:view')
                            <a href="{{ route('hospital.service-categories.index') }}" class="subnav-item {{ request()->routeIs('hospital.service-categories.*') ? 'active' : '' }}" {!! request()->routeIs('hospital.service-categories.*') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">Service Categories</span>
                            </a>
                        @endcan
                        @can('hospital:product-categories:view')
                            <a href="{{ route('hospital.product-categories.index') }}" class="subnav-item {{ request()->routeIs('hospital.product-categories.*') ? 'active' : '' }}" {!! request()->routeIs('hospital.product-categories.*') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">Product Categories</span>
                            </a>
                        @endcan
                        @can('hospital:services:view')
                            <a href="{{ route('hospital.services.index') }}" class="subnav-item {{ request()->routeIs('hospital.services.*') ? 'active' : '' }}" {!! request()->routeIs('hospital.services.*') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">Services</span>
                            </a>
                        @endcan
                        @can('hospital:products:view')
                            <a href="{{ route('hospital.products.index') }}" class="subnav-item {{ request()->routeIs('hospital.products.*') ? 'active' : '' }}" {!! request()->routeIs('hospital.products.*') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">Products</span>
                            </a>
                        @endcan
                        @can('hospital:doctors:view')
                            <a href="{{ route('hospital.doctors.index') }}" class="subnav-item {{ request()->routeIs('hospital.doctors.*') ? 'active' : '' }}" {!! request()->routeIs('hospital.doctors.*') ? 'aria-current="page"' : '' !!}>
                                <span class="subnav-dot"></span>
                                <span class="subnav-text">Doctors</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endif
        @endif

        @if(auth()->user()?->can('users:users:view') || auth()->user()?->can('users:roles:view'))
            <div class="menu-section-divider"></div>
            <div class="menu-section-label">Administration</div>

            <!-- Users -->
            @can('users:users:view')
                <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" data-tooltip="Users" {!! request()->routeIs('users.*') ? 'aria-current="page"' : '' !!}>
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <span class="nav-label">Users</span>
                </a>
            @endcan

            <!-- Roles & Permissions -->
            @can('users:roles:view')
                <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}" data-tooltip="Roles & Permissions" {!! request()->routeIs('roles.*') ? 'aria-current="page"' : '' !!}>
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Roles & Permissions</span>
                </a>
            @endcan
        @endif

        <div class="menu-section-divider"></div>
        <div class="menu-section-label">System</div>

        <!-- Settings with Submenu -->
        @can('settings:general:view')
            <div class="nav-group {{ request()->routeIs('settings.*') ? 'open active-group' : '' }}">
                <button type="button" class="nav-item nav-parent {{ request()->routeIs('settings.*') ? 'active' : '' }}" data-tooltip="Settings" aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}" aria-haspopup="true">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </span>
                    <span class="nav-label">Settings</span>
                    <span class="nav-arrow">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </span>
                </button>
                <div class="nav-submenu">
                    <a href="{{ route('settings.general') }}" class="subnav-item {{ request()->routeIs('settings.general*') ? 'active' : '' }}" {!! request()->routeIs('settings.general*') ? 'aria-current="page"' : '' !!}>
                        <span class="subnav-dot"></span>
                        <span class="subnav-text">General</span>
                    </a>
                </div>
            </div>
        @endcan

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
                <span class="user-name" title="{{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}">
                    {{ auth()->check() ? auth()->user()->name : 'Dr. Alex Smith' }}
                </span>
                <span class="user-role" title="{{ (auth()->check() && auth()->user()->roles->first()) ? auth()->user()->roles->first()->label : 'Dental Surgeon' }}">
                    {{ (auth()->check() && auth()->user()->roles->first()) ? auth()->user()->roles->first()->label : 'Dental Surgeon' }}
                </span>
            </div>
        </div>
        @auth
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="sidebar-signout-btn" title="Sign out of system">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="sidebar-signin-btn" style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 10px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                <span>Sign In</span>
            </a>
        @endauth
    </div>
</aside>
