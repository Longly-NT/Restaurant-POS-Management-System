<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Restaurant POS')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --leaf: #355E3B;
            --leaf-dark: #274630;
            --leaf-darker: #1F3924;
            --leaf-tint: #EAF1EA;
            --leaf-tint-strong: #DCEADD;
            --ink: #22261F;
            --muted: #767C74;
            --border: #E7E9E4;
            --bg: #F8F9F6;
            --danger: #B3452E;
            --success: #3E7A4D;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 2px rgba(31, 41, 24, .05), 0 1px 3px rgba(31, 41, 24, .06);
            --shadow-md: 0 8px 24px rgba(31, 41, 24, .08);
        }

        * { box-sizing: border-box; }

        body {
            background-color: var(--bg);
            color: var(--ink);
            font-family: 'Plus Jakarta Sans', 'Segoe UI', system-ui, sans-serif;
            letter-spacing: -.01em;
        }

        h1, h2, h3, h4, h5, h6 { font-weight: 700; letter-spacing: -.02em; color: var(--ink); }
        a { color: inherit; }

        /* ---------- App shell ---------- */
        .app-shell { display: flex; align-items: stretch; min-height: 100vh; }

        /* ---------- Sidebar ---------- */
        .app-sidebar {
            width: 84px;
            flex-shrink: 0;
            background: linear-gradient(180deg, var(--leaf-dark), var(--leaf-darker));
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 22px 0 16px;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        .app-sidebar .brand {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: rgba(255,255,255,.12);
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 15px;
            margin-bottom: 26px;
            letter-spacing: 0;
        }
        .app-sidebar .nav-rail {
            list-style: none; margin: 0; padding: 0;
            display: flex; flex-direction: column; gap: 6px;
            width: 100%; align-items: center;
        }
        .app-sidebar .nav-rail a {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 4px;
            width: 64px; padding: 9px 4px 8px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.62);
            text-decoration: none;
            font-size: 10.5px; font-weight: 600;
            letter-spacing: .01em;
            transition: background-color .15s ease, color .15s ease;
        }
        .app-sidebar .nav-rail a svg { width: 21px; height: 21px; }
        .app-sidebar .nav-rail a:hover { color: #fff; background: rgba(255,255,255,.08); }
        .app-sidebar .nav-rail a.active { color: var(--leaf-darker); background: #fff; }
        .app-sidebar .rail-spacer { flex: 1; }
        .app-sidebar .rail-foot { width: 100%; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .app-sidebar .user-chip {
            width: 40px; height: 40px; border-radius: 50%;
            background: rgba(255,255,255,.12); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
        }
        .app-sidebar .logout-btn {
            border: 0; background: transparent; color: rgba(255,255,255,.5);
            width: 64px; padding: 8px 4px; border-radius: var(--radius-sm);
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            font-size: 10.5px; font-weight: 600;
        }
        .app-sidebar .logout-btn:hover { color: #fff; background: rgba(255,255,255,.08); }
        .app-sidebar .logout-btn svg { width: 20px; height: 20px; }

        /* ---------- Main area ---------- */
        .app-main { flex: 1; min-width: 0; }
        .app-topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 18px 28px;
            background: #fff;
            border-bottom: 1px solid var(--border);
        }
        .app-topbar .role-pill {
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
            color: var(--leaf-dark); background: var(--leaf-tint);
            padding: 4px 10px; border-radius: 999px;
        }
        .app-content { padding: 26px 28px 48px; }

        /* ---------- Reusable components ---------- */
        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            background: #fff;
            font-weight: 700;
            border-bottom: 1px solid var(--border);
            border-top-left-radius: var(--radius) !important;
            border-top-right-radius: var(--radius) !important;
        }

        .btn { border-radius: var(--radius-sm); font-weight: 600; letter-spacing: -.005em; }
        .btn-dark, .btn-primary {
            background-color: var(--leaf); border-color: var(--leaf); color: #fff;
        }
        .btn-dark:hover, .btn-primary:hover {
            background-color: var(--leaf-dark); border-color: var(--leaf-dark); color: #fff;
        }
        .btn-success { background-color: var(--success); border-color: var(--success); }
        .btn-outline-secondary { border-color: var(--border); color: var(--ink); }
        .btn-outline-danger { color: var(--danger); border-color: var(--danger); }
        .btn-outline-danger:hover { background-color: var(--danger); border-color: var(--danger); }

        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border-color: var(--border);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--leaf);
            box-shadow: 0 0 0 .2rem var(--leaf-tint);
        }
        .form-label { font-weight: 600; font-size: 13.5px; color: var(--ink); }

        .table thead.table-light th {
            background: var(--leaf-tint);
            color: var(--leaf-dark);
            font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em;
            border-bottom: none;
        }

        .badge.bg-success { background-color: var(--success) !important; }
        .badge.bg-danger { background-color: var(--danger) !important; }
        .badge.bg-secondary { background-color: #8B9088 !important; }

        /* ---------- Guest (login) shell ---------- */
        .guest-shell { min-height: 100vh; }
        .guest-alerts {
            position: fixed; top: 16px; left: 50%; transform: translateX(-50%);
            z-index: 1080; width: min(92vw, 480px);
        }

        /* ---------- Legacy classes kept for staff/tables/index.blade.php ---------- */
        .card-table { cursor: pointer; transition: transform .12s ease, box-shadow .12s ease; }
        .card-table:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .badge-status { font-size: .75rem; }
        .table-occupied { border: 1.5px solid var(--danger); }
        .table-available { border: 1.5px solid var(--success); }
    </style>
    @yield('styles')
</head>
<body>

@auth
<div class="app-shell">
    <nav class="app-sidebar">
        <div class="brand">V</div>

        <ul class="nav-rail">
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                    Dashboard
                </a></li>
                <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3.25"/><path d="M2.75 20c.7-3.4 3.2-5.5 6.25-5.5s5.55 2.1 6.25 5.5"/><circle cx="17.25" cy="8.75" r="2.5"/><path d="M15.5 14.9c2.55.15 4.55 1.95 5.1 4.6"/></svg>
                    Staff
                </a></li>
                <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12.6 3.5h5.9a2 2 0 012 2v5.9a2 2 0 01-.6 1.42l-8.6 8.6a2 2 0 01-2.83 0l-6.9-6.9a2 2 0 010-2.83l8.6-8.6a2 2 0 011.42-.6z"/><circle cx="16.5" cy="7.5" r="1.4"/></svg>
                    Categories
                </a></li>
                <li><a href="{{ route('admin.menu-items.index') }}" class="{{ request()->routeIs('admin.menu-items.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4.75h11.25A2.75 2.75 0 0118 7.5v12l-3-2-3 2-3-2-3 2v-12A2.75 2.75 0 014 4.75z"/><path d="M7.25 8.5h7.5M7.25 11.5h7.5"/></svg>
                    Menu
                </a></li>
                <li><a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3.5h9.5L19 7v13.5H6z"/><path d="M15.5 3.5V7H19"/><path d="M9 12h6M9 15.5h6"/></svg>
                    Orders
                </a></li>
                <li><a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M11 20V4M18 20v-7"/></svg>
                    Reports
                </a></li>
                <li><a href="{{ route('staff.tables.index') }}" class="{{ request()->routeIs('staff.tables.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.25" y="5" width="17.5" height="14" rx="2"/><path d="M3.25 10.5h17.5M9.5 10.5V19"/></svg>
                    Tables
                </a></li>
                <li><a href="{{ route('chef.orders.index') }}" class="{{ request()->routeIs('chef.orders.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c1.6 2 1.9 3.4.9 4.9C14.9 8.6 16 10 16 11.8A4 4 0 018 11.8c0-1.4.6-2.3 1.5-3.2C8.3 7.4 8.7 5.4 12 3z"/><path d="M7.5 15.5h9a1.5 1.5 0 011.5 1.5v0a3.5 3.5 0 01-3.5 3.5h-5A3.5 3.5 0 016 17v0a1.5 1.5 0 011.5-1.5z"/></svg>
                    Kitchen
                </a></li>
            @elseif(auth()->user()->isStaff())
                <li><a href="{{ route('staff.tables.index') }}" class="{{ request()->routeIs('staff.tables.*') || request()->routeIs('staff.orders.show') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.25" y="5" width="17.5" height="14" rx="2"/><path d="M3.25 10.5h17.5M9.5 10.5V19"/></svg>
                    Tables
                </a></li>
                <li><a href="{{ route('staff.orders.mine') }}" class="{{ request()->routeIs('staff.orders.mine') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3.5h9.5L19 7v13.5H6z"/><path d="M15.5 3.5V7H19"/><path d="M9 12h6M9 15.5h6"/></svg>
                    My Orders
                </a></li>
            @elseif(auth()->user()->isChef())
                <li><a href="{{ route('chef.orders.index') }}" class="{{ request()->routeIs('chef.orders.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c1.6 2 1.9 3.4.9 4.9C14.9 8.6 16 10 16 11.8A4 4 0 018 11.8c0-1.4.6-2.3 1.5-3.2C8.3 7.4 8.7 5.4 12 3z"/><path d="M7.5 15.5h9a1.5 1.5 0 011.5 1.5v0a3.5 3.5 0 01-3.5 3.5h-5A3.5 3.5 0 016 17v0a1.5 1.5 0 011.5-1.5z"/></svg>
                    Kitchen
                </a></li>
            @endif
        </ul>

        <div class="rail-spacer"></div>

        <div class="rail-foot">
            <div class="user-chip" title="{{ auth()->user()->name }}">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit" title="Log out">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4H6a2 2 0 00-2 2v12a2 2 0 002 2h3M16 16l4-4-4-4M20 12H9"/></svg>
                    Log out
                </button>
            </form>
        </div>
    </nav>

    <div class="app-main">
        <div class="app-topbar">
            <div>
                <h5 class="mb-0">@yield('title', 'Restaurant POS')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="role-pill">{{ auth()->user()->role }}</span>
                <span class="text-muted small">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <div class="app-content">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>
@else
    <div class="guest-shell">
        @if (session('status') || $errors->any())
            <div class="guest-alerts">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
