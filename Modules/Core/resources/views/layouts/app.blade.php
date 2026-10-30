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
            --primary-border: #bfdbfe;
            --primary-dark: #1e40af;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --border-subtle: #f1f5f9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --success: #16a34a;
            --success-light: #f0fdf4;
            --sidebar-width: 264px;
            --sidebar-collapsed-width: 68px;
            --header-height: 60px;
            --transition-timing: 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
            box-shadow: 1px 0 3px rgba(0, 0, 0, 0.02);
        }

        .sidebar-header {
            height: var(--header-height);
            padding: 0 1.15rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
            background: #ffffff;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-main);
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
            min-width: 0;
            padding: 4px 0;
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
            min-width: 0;
            flex: 1;
        }

        .brand-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            display: block;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .brand-sub {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            display: block;
            max-width: 100%;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .brand-marquee-track {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            max-width: 100%;
        }

        .brand-marquee-content {
            display: inline-block;
            white-space: nowrap;
        }

        /* Running marquee for long titles when characters overflow / hide */
        .brand-title.has-marquee,
        .brand-sub.has-marquee {
            overflow: hidden;
            mask-image: linear-gradient(to right, transparent, black 4px, black calc(100% - 4px), transparent);
            -webkit-mask-image: linear-gradient(to right, transparent, black 4px, black calc(100% - 4px), transparent);
        }

        .brand-title.has-marquee .brand-marquee-track,
        .brand-sub.has-marquee .brand-marquee-track {
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            width: max-content;
            max-width: none;
            animation: brandMarqueeRTL linear infinite;
            will-change: transform;
        }

        .brand-title.has-marquee:hover .brand-marquee-track,
        .brand-sub.has-marquee:hover .brand-marquee-track {
            animation-play-state: paused;
        }

        .brand-marquee-spacer {
            display: inline-block;
            padding: 0 0.75rem;
            opacity: 0.4;
            user-select: none;
        }

        @keyframes brandMarqueeRTL {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }

        /* ======================================================== */
        /* SIDEBAR NAVIGATION MENU (CONSISTENT PADDING & SPACING)   */
        /* ======================================================== */
        .sidebar-menu {
            flex: 1;
            padding: 0.75rem 0.65rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .menu-section-divider {
            height: 1px;
            background: var(--border);
            margin: 0.85rem 0.5rem 0.4rem;
            opacity: 0.9;
        }

        .menu-section-label {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 0.5rem 0.85rem 0.25rem;
            white-space: nowrap;
            transition: opacity var(--transition-timing);
            display: flex;
            align-items: center;
            user-select: none;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.625rem 0.85rem;
            min-height: 42px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.4;
            color: #334155;
            text-decoration: none;
            border: 0;
            background: transparent;
            width: 100%;
            cursor: pointer;
            transition: all var(--transition-timing);
            position: relative;
            text-align: left;
            user-select: none;
            box-sizing: border-box;
        }

        button.nav-item {
            font-family: inherit;
            font-size: 0.875rem;
            line-height: 1.4;
            -webkit-appearance: none;
            appearance: none;
        }

        .nav-item:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        .nav-item:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .nav-item:hover .nav-icon {
            color: var(--primary);
        }

        /* Active Nav Item - High Contrast with Accent Indicator Bar */
        .nav-item.active {
            color: #1d4ed8;
            background: #eff6ff;
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 3.5px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }

        .nav-item.active .nav-icon {
            color: var(--primary);
            background: #dbeafe;
            border-radius: 6px;
        }

        .nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 24px;
            height: 24px;
            color: #64748b;
            transition: all var(--transition-timing);
        }

        .nav-label {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity var(--transition-timing);
            font-size: 0.875rem;
            letter-spacing: -0.01em;
            line-height: 1.4;
        }

        .nav-arrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            color: #94a3b8;
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .nav-item:hover .nav-arrow {
            color: #64748b;
        }

        /* Parent Menus & Accordion Submenus */
        .nav-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 2px;
        }

        .nav-parent {
            position: relative;
        }

        .nav-group.open > .nav-parent {
            background: #f8fafc;
            color: #0f172a;
            font-weight: 600;
        }

        .nav-group.open > .nav-parent .nav-arrow {
            transform: rotate(180deg);
            color: var(--primary);
        }

        /* Active parent group indicator when a child is active */
        .nav-group.active-group > .nav-parent {
            color: #1d4ed8;
            font-weight: 600;
        }

        .nav-group.active-group > .nav-parent .nav-icon {
            color: var(--primary);
        }

        /* Modern Tree Guide Rail for Submenus with generous indentation and padding */
        .nav-submenu {
            display: none;
            flex-direction: column;
            gap: 3px;
            margin: 4px 0 6px 1.5rem;
            padding: 3px 0 3px 0.85rem;
            border-left: 2px solid #e2e8f0;
            position: relative;
        }

        .nav-group.open .nav-submenu {
            display: flex;
        }

        .subnav-item {
            padding: 0.5rem 0.85rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 500;
            line-height: 1.4;
            color: #475569;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s ease;
            position: relative;
            min-height: 36px;
            box-sizing: border-box;
        }

        .subnav-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cbd5e1;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .subnav-text {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .subnav-item:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .subnav-item:hover .subnav-dot {
            background: var(--primary);
            transform: scale(1.2);
        }

        .subnav-item.active {
            color: #1d4ed8;
            font-weight: 600;
            background: #eff6ff;
        }

        .subnav-item.active .subnav-dot {
            background: var(--primary);
            box-shadow: 0 0 0 3px #dbeafe;
            transform: scale(1.15);
        }

        .subnav-item:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        /* Sidebar Footer / User Profile */
        .sidebar-footer {
            padding: 0.9rem 1rem;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
            background: #fafbfc;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 11px;
            overflow: hidden;
        }

        .avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.8125rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 2px solid #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .user-meta {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            white-space: nowrap;
            min-width: 0;
            flex: 1;
        }

        .user-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0f172a;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            line-height: 1.35;
        }

        .user-role {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--text-muted);
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            line-height: 1.35;
            margin-top: 1px;
        }

        .sidebar-signout-btn,
        .sidebar-signin-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            width: 100%;
            padding: 7px 12px;
            min-height: 34px;
            border-radius: 7px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .sidebar-signout-btn {
            background: #ffffff;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .sidebar-signout-btn:hover {
            background: #fef2f2;
            border-color: #f87171;
            color: #b91c1c;
        }

        .sidebar-signin-btn {
            background: #ffffff;
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .sidebar-signin-btn:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        /* ======================================================== */
        /* TOP HEADER & DROPDOWN NAVIGATION                         */
        /* ======================================================== */
        .app-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background: var(--bg);
        }

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
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
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
            transition: all 0.15s ease;
        }

        .btn-toggle:hover {
            background: #f1f5f9;
            color: var(--primary);
            border-color: #cbd5e1;
        }

        .btn-toggle:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        .header-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
            padding: 4px 0;
        }

        .crumb-root {
            color: #64748b;
            font-weight: 500;
        }

        .crumb-separator {
            color: #cbd5e1;
            font-size: 0.75rem;
            user-select: none;
        }

        .crumb-current {
            color: #0f172a;
            font-weight: 700;
        }

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
            justify-content: center;
            gap: 6px;
            padding: 0.45rem 0.85rem;
            min-height: 34px;
            border-radius: 7px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            line-height: 1.4;
            box-sizing: border-box;
        }

        .header-btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        .header-btn-primary {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(36, 87, 197, 0.2);
        }
        .header-btn-primary:hover { background: var(--primary-hover); }

        .header-btn-outline {
            background: #ffffff;
            border: 1px solid var(--border);
            color: #334155;
        }
        .header-btn-outline:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .header-btn-odontogram {
            background: #eff6ff;
            color: var(--primary);
            border: 1px solid #bfdbfe;
        }
        .header-btn-odontogram:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .header-divider {
            width: 1px;
            height: 24px;
            background: var(--border);
        }

        /* User Profile Dropdown Button & Menu */
        .header-user-wrapper {
            position: relative;
        }

        .header-user-btn {
            display: flex;
            align-items: center;
            gap: 9px;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 4px 8px;
            min-height: 38px;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .header-user-btn:hover {
            background: #f8fafc;
            border-color: var(--border);
        }

        .header-user-btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
        }

        .avatar-small {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #334155;
            font-weight: 700;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
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
            flex-shrink: 0;
        }

        .user-chevron {
            color: #94a3b8;
            transition: transform 0.18s ease;
        }

        .header-user-btn[aria-expanded="true"] .user-chevron {
            transform: rotate(180deg);
            color: var(--primary);
        }

        /* Header Dropdown Floating Menu */
        .header-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 240px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            animation: dropdownFadeIn 0.16s ease-out;
        }

        .header-dropdown-menu.show {
            display: flex;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-header {
            padding: 0.85rem 1rem;
            background: #fafbfc;
            border-bottom: 1px solid var(--border);
        }

        .dropdown-user-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-user-email {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-user-role {
            margin-top: 6px;
        }

        .badge-role {
            background: #eff6ff;
            color: #1e40af;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        .dropdown-body,
        .dropdown-footer {
            padding: 5px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 0.85rem;
            min-height: 36px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #334155;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            background: transparent;
            width: 100%;
            cursor: pointer;
            text-align: left;
            transition: all 0.12s ease;
            line-height: 1.4;
            box-sizing: border-box;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .dropdown-item:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: -1px;
        }

        .dropdown-logout-btn {
            color: #dc2626;
        }

        .dropdown-logout-btn:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* ======================================================== */
        /* MAIN WORKSPACE CONTENT & SHARED COMPONENTS               */
        /* ======================================================== */
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

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
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

        .btn:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 1px;
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
            .sidebar-collapsed .menu-section-label,
            .sidebar-collapsed .menu-section-divider,
            .sidebar-collapsed .user-meta,
            .sidebar-collapsed .sidebar-signout-btn span,
            .sidebar-collapsed .sidebar-signin-btn span,
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
                padding: 0;
                width: 44px;
                height: 44px;
                min-height: 44px;
                border-radius: 10px;
                margin: 2px auto;
            }

            .sidebar-collapsed .nav-icon {
                width: 24px;
                height: 24px;
            }

            .sidebar-collapsed .nav-item.active .nav-icon {
                width: 32px;
                height: 32px;
                border-radius: 8px;
            }

            .sidebar-collapsed .nav-item.active::before {
                top: 8px;
                bottom: 8px;
                left: -4px;
                width: 4px;
            }

            .sidebar-collapsed .user-profile {
                justify-content: center;
            }

            .sidebar-collapsed .sidebar-footer {
                padding: 0.75rem 0.4rem;
            }

            /* Sleek High-Contrast Tooltip in Collapsed Rail Mode */
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
                padding: 6px 12px;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 6px;
                white-space: nowrap;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
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
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
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

            .header-user-wrapper, .crumb-separator, .crumb-root {
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

        /* ======================================================== */
        /* FLATPICKR DATEPICKER MODERN THEME                        */
        /* ======================================================== */
        input[type="date"],
        input.datepicker,
        input.flatpickr-input {
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px 16px;
            padding-right: 32px;
            cursor: pointer;
        }
        input[type="date"]::-webkit-calendar-picker-indicator,
        input.datepicker::-webkit-calendar-picker-indicator {
            display: none !important;
            -webkit-appearance: none;
        }
        .flatpickr-calendar {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            font-family: inherit;
            font-size: 0.875rem;
            width: 308px;
            padding: 4px;
            z-index: 999999 !important;
        }
        .flatpickr-calendar.arrowTop:before,
        .flatpickr-calendar.arrowTop:after,
        .flatpickr-calendar.arrowBottom:before,
        .flatpickr-calendar.arrowBottom:after {
            border-bottom-color: var(--border);
        }
        .flatpickr-months {
            background: #f8fafc;
            border-radius: 8px 8px 0 0;
            padding: 4px 0;
            border-bottom: 1px solid var(--border);
            align-items: center;
        }
        .flatpickr-months .flatpickr-month {
            color: var(--text-main);
            font-weight: 700;
            fill: var(--text-main);
            height: 34px;
        }
        .flatpickr-current-month {
            font-size: 0.95rem;
            padding-top: 4px;
            font-weight: 700;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 700;
            color: var(--text-main);
        }
        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            padding: 8px 10px;
            color: var(--text-muted);
            fill: var(--text-muted);
            border-radius: 6px;
        }
        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            color: var(--primary);
            fill: var(--primary);
            background: #eff6ff;
        }
        span.flatpickr-weekday {
            color: #64748b;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .flatpickr-day {
            border-radius: 6px;
            font-size: 0.875rem;
            color: var(--text-main);
            height: 38px;
            line-height: 38px;
            margin: 1px 0;
        }
        .flatpickr-day:hover,
        .flatpickr-day:focus {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1e40af;
        }
        .flatpickr-day.today {
            border-color: var(--primary);
            font-weight: 700;
        }
        .flatpickr-day.today:hover {
            background: #eff6ff;
            color: #1e40af;
        }
        .flatpickr-day.selected,
        .flatpickr-day.selected:hover,
        .flatpickr-day.selected:focus {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #ffffff !important;
            font-weight: 700;
        }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            color: #cbd5e1;
        }
    </style>
    <!-- Flatpickr Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

        // Top Header User Account Dropdown Toggle
        const userBtn = document.getElementById('headerUserBtn');
        const userDropdown = document.getElementById('headerUserDropdown');

        if (userBtn && userDropdown) {
            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isOpen = userDropdown.classList.contains('show');
                userDropdown.classList.toggle('show');
                userBtn.setAttribute('aria-expanded', !isOpen ? 'true' : 'false');
            });

            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target) && !userBtn.contains(e.target)) {
                    userDropdown.classList.remove('show');
                    userBtn.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && userDropdown.classList.contains('show')) {
                    userDropdown.classList.remove('show');
                    userBtn.setAttribute('aria-expanded', 'false');
                    userBtn.focus();
                }
            });
        }

        // Running Marquee for Brand Title & Subtitle when long characters hide text
        function escapeMarqueeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function updateBrandMarquee() {
            const targets = [
                document.getElementById('sidebarBrandTitle'),
                document.getElementById('sidebarBrandSub')
            ];

            targets.forEach(el => {
                if (!el) return;

                // When sidebar is collapsed on desktop, skip marquee calculation
                if (document.documentElement.classList.contains('sidebar-collapsed')) {
                    el.classList.remove('has-marquee');
                    return;
                }

                const track = el.querySelector('.brand-marquee-track');
                if (!track) return;

                const firstContent = track.querySelector('.brand-marquee-content');
                if (!firstContent) return;

                const containerWidth = el.clientWidth;
                const textWidth = firstContent.getBoundingClientRect().width || firstContent.scrollWidth;

                // Only activate running marquee when characters are long and hide / overflow container
                if (containerWidth > 0 && textWidth > containerWidth + 2) {
                    const text = el.getAttribute('data-text') || firstContent.textContent.trim();
                    if (!el.classList.contains('has-marquee')) {
                        track.innerHTML = `
                            <span class="brand-marquee-content">${escapeMarqueeHtml(text)}</span>
                            <span class="brand-marquee-spacer" aria-hidden="true">&bull;</span>
                            <span class="brand-marquee-content" aria-hidden="true">${escapeMarqueeHtml(text)}</span>
                            <span class="brand-marquee-spacer" aria-hidden="true">&bull;</span>
                        `;
                        el.classList.add('has-marquee');
                    }

                    // Smooth speed: ~28px per second
                    const distance = track.scrollWidth / 2;
                    const speed = 28;
                    const duration = Math.max(6, Math.round(distance / speed));
                    track.style.animationDuration = duration + 's';
                } else if (containerWidth > 0) {
                    // Fits inside container without hiding: keep static
                    if (el.classList.contains('has-marquee')) {
                        el.classList.remove('has-marquee');
                        const text = el.getAttribute('data-text') || firstContent.textContent.trim();
                        track.innerHTML = `<span class="brand-marquee-content">${escapeMarqueeHtml(text)}</span>`;
                        track.style.animationDuration = '';
                    }
                }
            });
        }

        // Initialize and observe size / font / layout changes
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateBrandMarquee);
        } else {
            updateBrandMarquee();
        }

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(updateBrandMarquee);
        }

        window.addEventListener('resize', () => {
            requestAnimationFrame(updateBrandMarquee);
        });

        const sidebarHeader = document.querySelector('.sidebar-header');
        if (window.ResizeObserver && sidebarHeader) {
            new ResizeObserver(() => {
                updateBrandMarquee();
            }).observe(sidebarHeader);
        }
    })();
    </script>

    <!-- SweetAlert2 library & global confirmation handler -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-swal, [data-confirm-delete]');
        if (!btn) return;

        e.preventDefault();
        const form = btn.closest('form');
        if (!form) return;

        const type = form.dataset.type || btn.dataset.type || 'item';
        const name = form.dataset.name || btn.dataset.name || '';
        const title = form.dataset.title || btn.dataset.title || (
            type === 'invoice' ? 'Delete Invoice?' :
            type === 'payment' ? 'Delete Payment?' : 'Are you sure?'
        );

        let text = form.dataset.text || btn.dataset.text;
        if (!text) {
            if (type === 'invoice') {
                text = name 
                    ? `Are you sure you want to delete invoice "${name}"? This action cannot be undone.`
                    : 'Are you sure you want to delete this invoice? This action cannot be undone.';
            } else if (type === 'payment') {
                text = name 
                    ? `Are you sure you want to delete payment "${name}"? This will restore the balance on the invoice.`
                    : 'Are you sure you want to delete this payment? This will restore the balance on the invoice.';
            } else {
                text = name 
                    ? `Are you sure you want to delete "${name}"? This action cannot be undone.`
                    : 'Are you sure you want to delete this item? This action cannot be undone.';
            }
        }

        const confirmBtnText = form.dataset.confirmBtn || (
            type === 'payment' ? 'Yes, delete payment!' : 
            type === 'invoice' ? 'Yes, delete invoice!' : 'Yes, delete it!'
        );

        if (window.Swal) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: confirmBtnText,
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm(text)) {
                form.submit();
            }
        }
    });
    </script>
    <!-- Flatpickr Datepicker JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <script>
    (function() {
        function initDatepickers(context) {
            if (typeof flatpickr === 'undefined') return;

            const root = context || document;
            let elements = [];
            if (root instanceof Element && (root.matches('input[type="date"]') || root.matches('.datepicker'))) {
                elements = [root];
            } else {
                elements = Array.from(root.querySelectorAll('input[type="date"], input.datepicker'));
            }

            elements.forEach(function(el) {
                if (el._flatpickr) return;

                const currentVal = el.value;

                // Change type="date" to type="text" with class="datepicker" to prevent browser native picker clash
                if (el.type === 'date') {
                    try {
                        el.type = 'text';
                    } catch(e) {}
                    el.classList.add('datepicker');
                }

                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    defaultDate: currentVal || undefined,
                    allowInput: true,
                    disableMobile: true,
                    monthSelectorType: 'static',
                    onChange: function(selectedDates, dateStr, instance) {
                        instance.element.dispatchEvent(new Event('change', { bubbles: true }));
                        instance.element.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            });
        }

        window.initDatepickers = initDatepickers;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initDatepickers();
            });
        } else {
            initDatepickers();
        }
    })();
    </script>
    @yield('scripts')
</body>
</html>
