<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="overlay" onclick="document.body.classList.remove('sidebar-open')"></div>

@php
    $u = auth()->user();
    $iniciales = collect(explode(' ', $u->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('');
    $alertasStock = \App\Models\Repuesto::whereColumn('stock', '<=', 'stock_minimo')->orderBy('stock')->take(6)->get();
    $alertasCount = \App\Models\Repuesto::whereColumn('stock', '<=', 'stock_minimo')->count();
@endphp

{{-- ======================= SIDEBAR ======================= --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo"><i class="fa-solid fa-car-on"></i></div>
        <div class="brand-text">
            <b>AutoTaller Pro</b>
            <span>Gestión de taller</span>
        </div>
    </div>
    @use('App\Support\Permisos')
    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>

        @if (Permisos::puede('ordenes') || Permisos::puede('citas') || Permisos::puede('servicios'))
            <div class="nav-section">Operaciones</div>
            @if (Permisos::puede('ordenes'))
                <a href="{{ route('ordenes.index') }}" class="nav-item {{ request()->routeIs('ordenes.*') ? 'active' : '' }}"><i class="fa-solid fa-clipboard-list"></i> Órdenes de servicio</a>
            @endif
            @if (Permisos::puede('citas'))
                <a href="{{ route('citas.index') }}" class="nav-item {{ request()->routeIs('citas.*') ? 'active' : '' }}"><i class="fa-solid fa-calendar-check"></i> Citas / Agenda</a>
            @endif
            @if (Permisos::puede('servicios'))
                <a href="{{ route('servicios.index') }}" class="nav-item {{ request()->routeIs('servicios.*') ? 'active' : '' }}"><i class="fa-solid fa-screwdriver-wrench"></i> Servicios</a>
            @endif
        @endif

        @if (Permisos::puede('clientes') || Permisos::puede('vehiculos'))
            <div class="nav-section">Clientes</div>
            @if (Permisos::puede('clientes'))
                <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Clientes</a>
            @endif
            @if (Permisos::puede('vehiculos'))
                <a href="{{ route('vehiculos.index') }}" class="nav-item {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}"><i class="fa-solid fa-car"></i> Vehículos</a>
            @endif
        @endif

        @if (Permisos::puede('repuestos') || Permisos::puede('proveedores') || Permisos::puede('compras'))
            <div class="nav-section">Inventario</div>
            @if (Permisos::puede('repuestos'))
                <a href="{{ route('repuestos.index') }}" class="nav-item {{ request()->routeIs('repuestos.*') ? 'active' : '' }}"><i class="fa-solid fa-boxes-stacked"></i> Repuestos</a>
            @endif
            @if (Permisos::puede('proveedores'))
                <a href="{{ route('proveedores.index') }}" class="nav-item {{ request()->routeIs('proveedores.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-field"></i> Proveedores</a>
            @endif
            @if (Permisos::puede('compras'))
                <a href="{{ route('compras.index') }}" class="nav-item {{ request()->routeIs('compras.*') ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping"></i> Compras</a>
            @endif
        @endif

        @if (Permisos::puede('facturacion') || Permisos::puede('caja'))
            <div class="nav-section">Finanzas</div>
            @if (Permisos::puede('facturacion'))
                <a href="{{ route('facturacion.index') }}" class="nav-item {{ request()->routeIs('facturacion.index') || request()->routeIs('facturacion.create') || request()->routeIs('facturacion.imprimir') || request()->routeIs('facturacion.anular') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice-dollar"></i> Facturación</a>
                <a href="{{ route('facturacion.electronica.index') }}" class="nav-item {{ request()->routeIs('facturacion.electronica.*') ? 'active' : '' }}"><i class="fa-solid fa-file-invoice"></i> Facturación Electrónica</a>
            @endif
            @if (Permisos::puede('guias'))
                <a href="{{ route('guias.index') }}" class="nav-item {{ request()->routeIs('guias.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-fast"></i> Guías de Remisión</a>
            @endif
            @if (Permisos::puede('caja'))
                <a href="{{ route('caja.index') }}" class="nav-item {{ request()->routeIs('caja.*') ? 'active' : '' }}"><i class="fa-solid fa-cash-register"></i> Caja y pagos</a>
            @endif
        @endif

        @if (Permisos::puede('personal') || Permisos::puede('reportes') || Permisos::puede('configuracion'))
            <div class="nav-section">Administración</div>
            @if (Permisos::puede('personal'))
                <a href="{{ route('personal.index') }}" class="nav-item {{ request()->routeIs('personal.*') ? 'active' : '' }}"><i class="fa-solid fa-user-gear"></i> Personal</a>
            @endif
            @if (Permisos::puede('reportes'))
                <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->routeIs('reportes.*') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Reportes</a>
            @endif
            @if (Permisos::puede('configuracion'))
                <a href="{{ route('configuracion.index') }}" class="nav-item {{ request()->routeIs('configuracion.*') ? 'active' : '' }}"><i class="fa-solid fa-gear"></i> Configuración</a>
            @endif
        @endif
    </nav>
</aside>

{{-- ======================= MAIN ======================= --}}
<div class="main">
    <header class="topbar">
        <button class="menu-toggle" onclick="document.body.classList.toggle('sidebar-open')"><i class="fa-solid fa-bars"></i></button>
        <div class="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Buscar órdenes, clientes, placas...">
        </div>
        <div class="topbar-right">
            <div class="dropdown">
                <button type="button" class="topbar-icon" title="Alertas de stock" onclick="toggleDrop('dropStock')">
                    <i class="fa-regular fa-bell"></i>
                    @if ($alertasCount > 0)<span class="count-badge">{{ $alertasCount > 9 ? '9+' : $alertasCount }}</span>@endif
                </button>
                <div class="dropdown-menu" id="dropStock">
                    <div class="dd-head">Alertas de stock bajo</div>
                    @forelse ($alertasStock as $r)
                        <a href="{{ \App\Support\Permisos::puede('repuestos') ? route('repuestos.index') : '#' }}" class="dd-item">
                            <div class="mini-avatar bg-rose" style="width:30px;height:30px;font-size:11px;"><i class="fa-solid fa-box"></i></div>
                            <div style="flex:1;">
                                <div style="font-weight:600; font-size:13px;">{{ $r->nombre }}</div>
                                <div class="cell-muted" style="font-size:11.5px;">Quedan {{ $r->stock }} · mínimo {{ $r->stock_minimo }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="dd-empty">Todo el stock está en orden 👍</div>
                    @endforelse
                </div>
            </div>
            <a href="{{ route('perfil.index') }}" class="topbar-icon" title="Mi perfil"><i class="fa-regular fa-user"></i></a>
            <div class="dropdown">
                <button type="button" class="user-chip" onclick="toggleDrop('dropUser')" style="border:none; background:none;">
                    <div class="avatar">{{ strtoupper($iniciales) }}</div>
                    <div style="text-align:left;">
                        <div class="u-name">{{ $u->name }}</div>
                        <div class="u-role">{{ \App\Support\Permisos::rolLabel($u->rol) }}</div>
                    </div>
                    <i class="fa-solid fa-chevron-down" style="font-size:11px; color:var(--text-muted);"></i>
                </button>
                <div class="dropdown-menu" id="dropUser" style="right:0;">
                    <a href="{{ route('perfil.index') }}" class="dd-item"><i class="fa-solid fa-user" style="width:18px;"></i> Mi perfil</a>
                    @if (\App\Support\Permisos::puede('configuracion'))
                        <a href="{{ route('configuracion.index') }}" class="dd-item"><i class="fa-solid fa-gear" style="width:18px;"></i> Configuración</a>
                    @endif
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
        @if (session('error'))
            <div class="alert-flash" style="background:#fee2e2; color:#b91c1c; border-color:#fecaca;"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div>
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
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu.open').forEach(m => m.classList.remove('open'));
        }
    });
</script>
@stack('scripts')
</body>
</html>
