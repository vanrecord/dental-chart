<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dental Chart System')</title>

    <!-- Instant theme & sidebar state application to prevent layout shift -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('odonto_sidebar_collapsed') === 'true' && window.innerWidth >= 992) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        })();
    </script>

    <style>
        :root {
            --primary: #2457c5;
            --primary-hover: #1d48a3;
            --primary-light: #eff6ff;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --danger: #dc2626;
            --success: #16a34a;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 68px;
            --header-height: 60px;
            --transition-timing: 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Layout Structure */
        .app-layout {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        /* Left Sidebar */
        .app-sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background: #ffffff;
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 50;
            transition: width var(--transition-timing), transform var(--transition-timing);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-header {
            height: var(--header-height);
            padding: 0 1rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-main);
            overflow: hidden;
            white-space: nowrap;
        }

        .brand-icon {
            font-size: 1.5rem;
            line-height: 1;
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: opacity var(--transition-timing);
        }

        .brand-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .brand-sub {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        /* Sidebar Navigation Menu */
        .sidebar-menu {
            flex: 1;
            padding: 0.85rem 0.65rem;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .menu-section-label {
            font-size: 0.68rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            padding: 0.6rem 0.6rem 0.3rem;
            white-space: nowrap;
            transition: opacity var(--transition-timing);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 0.75rem;
            border-radius: 9px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            border: 0;
            background: transparent;
            width: 100%;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
            text-align: left;
        }

        .nav-item:hover {
            color: var(--primary);
            background: #f1f5f9;
        }

        .nav-item.active {
            color: var(--primary);
            background: var(--primary-light);
            font-weight: 700;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 22px;
            height: 22px;
            color: inherit;
        }

        .nav-label {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity var(--transition-timing);
        }

        .nav-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
            color: #94a3b8;
        }

        .nav-badge {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 999px;
            background: #dbeafe;
            color: #1e40af;
            letter-spacing: 0.05em;
        }

        /* Submenus */
        .nav-group {
            display: flex;
            flex-direction: column;
        }

        .nav-group.open .nav-arrow {
            transform: rotate(180deg);
        }

        .nav-submenu {
            display: none;
            flex-direction: column;
            gap: 2px;
            padding-left: 2.2rem;
            margin: 2px 0 4px;
        }

        .nav-group.open .nav-submenu {
            display: flex;
        }

        .subnav-item {
            padding: 0.42rem 0.6rem;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
        }

        .subnav-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: #cbd5e1;
            transition: background 0.15s;
        }

        .subnav-item:hover {
            color: var(--primary);
            background: #f8fafc;
        }

        .subnav-item:hover .subnav-dot,
        .subnav-item.active .subnav-dot {
            background: var(--primary);
        }

        .subnav-item.active {
            color: var(--primary);
            font-weight: 700;
        }

        /* Sidebar Footer / User Profile */
        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
            background: #fafbfc;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            overflow: hidden;
        }

        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            white-space: nowrap;
        }

        .user-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0f172a;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .user-role {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* Main Content Wrapper */
        .app-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--bg);
        }

        /* Top Header */
        .app-header {
            height: var(--header-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-toggle {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #334155;
            transition: all 0.15s;
        }

        .btn-toggle:hover {
            background: #f1f5f9;
            color: var(--primary);
            border-color: #cbd5e1;
        }

        .header-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
        }

        .crumb-root { color: var(--text-muted); font-weight: 500; }
        .crumb-separator { color: #cbd5e1; font-size: 0.75rem; }
        .crumb-current { color: #0f172a; font-weight: 700; }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-quick-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 0.4rem 0.75rem;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
        }

        .header-btn-primary {
            background: var(--primary);
            color: #ffffff;
        }
        .header-btn-primary:hover { background: var(--primary-hover); }

        .header-btn-outline {
            background: #ffffff;
            border: 1px solid var(--border);
            color: #334155;
        }
        .header-btn-outline:hover { background: #f8fafc; }

        .header-btn-odontogram {
            background: #eff6ff;
            color: var(--primary);
            border: 1px solid #bfdbfe;
        }
        .header-btn-odontogram:hover { background: #dbeafe; }

        .header-divider {
            width: 1px;
            height: 24px;
            background: var(--border);
        }

        .header-user {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .avatar-small {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-details-mini {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .user-name-mini {
            font-size: 0.8125rem;
            font-weight: 600;
            color: #1e293b;
        }

        .user-status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 2px #dcfce7;
        }

        /* Content Area */
        .app-content {
            flex: 1;
            padding: 1.5rem 1.75rem 3rem;
            width: 100%;
        }

        .container-fluid {
            max-width: 1360px;
            margin: 0 auto;
            width: 100%;
        }

        /* Shared Components */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #0f172a;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-outline { background: #fff; border-color: var(--border); color: var(--text-main); }
        .btn-outline:hover { background: #f1f5f9; }
        .btn-danger { background: #fef2f2; color: var(--danger); border-color: #fee2e2; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: 6px; }

        .table-wrap { overflow-x: auto; margin-top: 1rem; }
        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem; }
        th { padding: 0.75rem 1rem; background: #f8fafc; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:hover td { background: #fbfcfe; }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #475569;
        }
        .badge-success { background: #dcfce7; color: #15803d; }

        .alert {
            padding: 0.85rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem; color: #334155; }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.875rem;
            outline: none;
            background: #fff;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(36,87,197,0.15); }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; }
        .pagination { display: flex; gap: 4px; margin-top: 1.25rem; justify-content: flex-end; }
        .swatch-circle { width: 14px; height: 14px; border-radius: 50%; display: inline-block; vertical-align: middle; margin-right: 6px; border: 1px solid rgba(0,0,0,0.15); }

        /* ======================================================== */
        /* COLLAPSED RAIL STATE (DESKTOP)                           */
        /* ======================================================== */
        @media (min-width: 992px) {
            .sidebar-collapsed .app-sidebar {
                width: var(--sidebar-collapsed-width);
                min-width: var(--sidebar-collapsed-width);
            }

            .sidebar-collapsed .brand-text,
            .sidebar-collapsed .nav-label,
            .sidebar-collapsed .nav-arrow,
            .sidebar-collapsed .nav-badge,
            .sidebar-collapsed .menu-section-label,
            .sidebar-collapsed .user-meta,
            .sidebar-collapsed .nav-submenu {
                display: none !important;
                opacity: 0;
            }

            .sidebar-collapsed .sidebar-header {
                justify-content: center;
                padding: 0;
            }

            .sidebar-collapsed .sidebar-menu {
                padding: 0.75rem 0.4rem;
                align-items: center;
            }

            .sidebar-collapsed .nav-item {
                justify-content: center;
                padding: 0.7rem 0;
                width: 44px;
                height: 44px;
                border-radius: 10px;
            }

            .sidebar-collapsed .user-profile {
                justify-content: center;
            }

            .sidebar-collapsed .sidebar-footer {
                padding: 0.75rem 0;
            }

            /* Tooltip on hover in collapsed mode */
            .sidebar-collapsed .nav-item[data-tooltip] {
                position: relative;
            }

            .sidebar-collapsed .nav-item[data-tooltip]:hover::after {
                content: attr(data-tooltip);
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-left: 12px;
                background: #0f172a;
                color: #ffffff;
                padding: 6px 11px;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 6px;
                white-space: nowrap;
                box-shadow: 0 4px 14px rgba(0,0,0,0.18);
                z-index: 1000;
                pointer-events: none;
                animation: tooltipFadeIn 0.15s ease forwards;
            }

            .sidebar-collapsed .nav-item[data-tooltip]:hover::before {
                content: '';
                position: absolute;
                left: 100%;
                top: 50%;
                transform: translateY(-50%);
                margin-left: 6px;
                border-width: 5px 6px 5px 0;
                border-style: solid;
                border-color: transparent #0f172a transparent transparent;
                z-index: 1000;
                pointer-events: none;
            }
        }

        @keyframes tooltipFadeIn {
            from { opacity: 0; transform: translateY(-50%) translateX(-4px); }
            to { opacity: 1; transform: translateY(-50%) translateX(0); }
        }

        /* ======================================================== */
        /* MOBILE & TABLET DRAWER STATE (< 992px)                   */
        /* ======================================================== */
        @media (max-width: 991.98px) {
            .app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 270px !important;
                min-width: 270px !important;
                transform: translateX(-100%);
                box-shadow: 0 20px 40px rgba(0,0,0,0.25);
                z-index: 200;
            }

            .mobile-open .app-sidebar {
                transform: translateX(0);
            }

            .sidebar-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                backdrop-filter: blur(2px);
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.22s ease;
                z-index: 150;
            }

            .mobile-open .sidebar-backdrop {
                opacity: 1;
                pointer-events: auto;
            }

            .header-user, .crumb-separator, .crumb-root {
                display: none;
            }

            .header-quick-links .header-btn span:not(.btn-plus) {
                display: none;
            }

            .app-content {
                padding: 1.25rem 1rem 2.5rem;
            }
        }

        /* ======================================================== */
        /* PRINT STYLESHEET                                         */
        /* ======================================================== */
        @media print {
            body { background: #fff !important; }
            .app-sidebar,
            .app-header,
            .sidebar-backdrop,
            .no-print {
                display: none !important;
            }
            .app-main { margin: 0 !important; width: 100% !important; }
            .app-content { padding: 0 !important; }
            .container-fluid { max-width: none !important; width: 100% !important; margin: 0 !important; }
        }
    </style>
    @yield('styles')
</head>
<body class="app-body">
    <!-- Semi-transparent mobile backdrop -->
    <div id="sidebarBackdrop" class="sidebar-backdrop no-print" aria-hidden="true"></div>

    <div class="app-layout">
        <!-- Collapsible Left Sidebar -->
        @include('core::layouts.sidebar')

        <!-- Main Workspace Area -->
        <div class="app-main">
            <!-- Top Header Bar -->
            @include('core::layouts.header')

            <!-- Page Content -->
            <main class="app-content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul style="padding-left: 1.2rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Sidebar Interaction & State Script -->
    <script>
    (() => {
        'use strict';
        const STORAGE_KEY = 'odonto_sidebar_collapsed';
        const toggleBtn = document.getElementById('sidebarToggle');
        const backdrop = document.getElementById('sidebarBackdrop');
        const sidebar = document.getElementById('appSidebar');

        function isMobile() {
            return window.innerWidth < 992;
        }

        // Toggle Sidebar state
        function toggleSidebar() {
            if (isMobile()) {
                document.body.classList.toggle('mobile-open');
                const isOpen = document.body.classList.contains('mobile-open');
                toggleBtn?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            } else {
                document.documentElement.classList.toggle('sidebar-collapsed');
                const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                try {
                    localStorage.setItem(STORAGE_KEY, isCollapsed ? 'true' : 'false');
                } catch(e) {}
                toggleBtn?.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
            }
        }

        function closeMobileSidebar() {
            if (document.body.classList.contains('mobile-open')) {
                document.body.classList.remove('mobile-open');
                toggleBtn?.setAttribute('aria-expanded', 'false');
            }
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeMobileSidebar);
        }

        // Close on Escape key press
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.body.classList.contains('mobile-open')) {
                closeMobileSidebar();
            }
        });

        // Close mobile drawer when clicking a navigation link
        sidebar?.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (isMobile()) {
                    closeMobileSidebar();
                }
            });
        });

        // Accordion Submenu Toggles
        sidebar?.querySelectorAll('.nav-parent').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const group = button.closest('.nav-group');
                if (!group) return;

                // If sidebar is collapsed on desktop, expand it so the submenu can be seen cleanly
                if (!isMobile() && document.documentElement.classList.contains('sidebar-collapsed')) {
                    document.documentElement.classList.remove('sidebar-collapsed');
                    try { localStorage.setItem(STORAGE_KEY, 'false'); } catch(err) {}
                    group.classList.add('open');
                    button.setAttribute('aria-expanded', 'true');
                    return;
                }

                const isOpen = group.classList.contains('open');
                group.classList.toggle('open');
                button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });
        });
    })();
    </script>
    @yield('scripts')
</body>
</html>
