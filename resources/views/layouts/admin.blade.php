<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · Super Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="admin-theme">
<div class="overlay" onclick="document.body.classList.remove('sidebar-open')"></div>

@php
    $u = auth()->user();
    $iniciales = collect(explode(' ', $u->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
@endphp

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo"><i class="fa-solid fa-layer-group"></i></div>
        <div class="brand-text">
            <b>AutoTaller SaaS</b>
            <span>Panel Super Admin</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Plataforma</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a href="{{ route('admin.talleres.index') }}" class="nav-item {{ request()->routeIs('admin.talleres.*') ? 'active' : '' }}"><i class="fa-solid fa-shop"></i> Talleres</a>
        <a href="{{ route('admin.planes.index') }}" class="nav-item {{ request()->routeIs('admin.planes.*') ? 'active' : '' }}"><i class="fa-solid fa-tags"></i> Planes</a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-item {{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}"><i class="fa-solid fa-money-bill-trend-up"></i> Pagos</a>

        <div class="nav-section">Sitio</div>
        <a href="{{ route('landing') }}" target="_blank" class="nav-item"><i class="fa-solid fa-globe"></i> Ver landing</a>
    </nav>
</aside>

<div class="main">
    <header class="topbar">
        <button class="menu-toggle" onclick="document.body.classList.toggle('sidebar-open')"><i class="fa-solid fa-bars"></i></button>
        <div class="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Buscar talleres, planes...">
        </div>
        <div class="topbar-right">
            <span class="badge-pill b-primary" style="height:32px;"><i class="fa-solid fa-crown"></i> Super Admin</span>
            <div class="dropdown">
                <button type="button" class="user-chip" onclick="toggleDrop('dropUser')" style="border:none; background:none;">
                    <div class="avatar">{{ strtoupper($iniciales) }}</div>
                    <div style="text-align:left;">
                        <div class="u-name">{{ $u->name }}</div>
                        <div class="u-role">Super Administrador</div>
                    </div>
                    <i class="fa-solid fa-chevron-down" style="font-size:11px; color:var(--text-muted);"></i>
                </button>
                <div class="dropdown-menu" id="dropUser" style="right:0;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dd-item" style="width:100%; border:none; background:none; cursor:pointer; color:var(--danger);"><i class="fa-solid fa-right-from-bracket" style="width:18px;"></i> Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="content">
        @if (session('ok'))
            <div class="alert-flash"><i class="fa-solid fa-circle-check"></i> {{ session('ok') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    function toggleDrop(id) {
        const el = document.getElementById(id);
        const abierto = el.classList.contains('open');
        document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
        if (!abierto) el.classList.add('open');
    }
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
    });
</script>
@stack('scripts')
</body>
</html>
