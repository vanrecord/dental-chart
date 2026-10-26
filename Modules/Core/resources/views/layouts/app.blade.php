<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dental Chart System')</title>
    <style>
        :root {
            --primary: #2457c5;
            --primary-hover: #1d48a3;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --border: #e2e8f0;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --danger: #dc2626;
            --success: #16a34a;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .nav-brand {
            font-size: 1.125rem;
            font-weight: 800;
            color: #0f172a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-links {
            display: flex;
            gap: 8px;
            list-style: none;
        }
        .nav-link {
            text-decoration: none;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.15s ease;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
            background: #eff6ff;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            width: 100%;
            flex: 1;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
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
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar">
        <a href="{{ url('/') }}" class="nav-brand">
            🦷 <span>Dental Chart</span>
        </a>
        <ul class="nav-links">
            <li><a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">Patients</a></li>
            <li><a href="{{ route('dental-charts.index') }}" class="nav-link {{ request()->routeIs('dental-charts.*') ? 'active' : '' }}">Charts</a></li>
            <li><a href="{{ route('dental-conditions.index') }}" class="nav-link {{ request()->routeIs('dental-conditions.*') ? 'active' : '' }}">Conditions</a></li>
            <li><a href="{{ url('/dental') }}" class="nav-link {{ request()->is('dental*') ? 'active' : '' }}">Interactive Chart</a></li>
        </ul>
    </nav>

    <div class="container">
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

    @yield('scripts')
</body>
</html>
