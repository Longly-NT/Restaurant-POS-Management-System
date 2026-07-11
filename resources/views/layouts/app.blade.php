<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Restaurant POS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f5f7; }
        .navbar-brand { font-weight: 700; }
        .card-table { cursor: pointer; transition: transform .1s ease-in-out; }
        .card-table:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.1); }
        .badge-status { font-size: .75rem; }
        .table-occupied { border: 2px solid #dc3545; }
        .table-available { border: 2px solid #198754; }
    </style>
</head>
<body>
@auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}"><i class="bi bi-shop"></i> Restaurant POS</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">Staff</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.menu-items.index') }}">Menu</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.orders.index') }}">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.tables.index') }}">Tables (Staff view)</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('chef.orders.index') }}">Kitchen (Chef view)</a></li>
                    @elseif(auth()->user()->isStaff())
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.tables.index') }}">Tables</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('staff.orders.mine') }}">My Orders</a></li>
                    @elseif(auth()->user()->isChef())
                        <li class="nav-item"><a class="nav-link" href="{{ route('chef.orders.index') }}">Kitchen</a></li>
                    @endif
                </ul>
                <span class="navbar-text me-3 text-white-50">
                    {{ auth()->user()->name }} <span class="badge bg-secondary text-uppercase">{{ auth()->user()->role }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<div class="container-fluid px-4 pb-5">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
