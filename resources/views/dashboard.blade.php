@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
@php
    $estadosLabels = \App\Models\Orden::estados();
@endphp

<div class="page-head">
    <div>
        <h1>Dashboard</h1>
        <div class="crumb">Resumen general de tu taller · {{ now()->translatedFormat('l, d \d\e F Y') }}</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('ordenes.index') }}" class="btn btn-light"><i class="fa-solid fa-clipboard-list"></i> Ver órdenes</a>
        <a href="{{ route('ordenes.index') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva orden</a>
    </div>
</div>

{{-- ===== Tarjetas KPI estilo tiles ===== --}}
<div class="stats-grid" style="margin-bottom:22px;">
    <div class="stat-card grad-teal">
        <div class="s-label">Órdenes abiertas</div>
        <div class="s-value">{{ $kpis['ordenes_abiertas'] }}</div>
        <div class="s-sub"><i class="fa-solid fa-wrench"></i> Activas en el taller</div>
        <i class="fa-solid fa-clipboard-list s-icon"></i>
    </div>
    <div class="stat-card grad-blue">
        <div class="s-label">Ingresos del mes</div>
        <div class="s-value">S/ {{ number_format($kpis['ingresos_mes'], 0) }}</div>
        <div class="s-sub"><i class="fa-solid fa-arrow-trend-up"></i> Órdenes pagadas</div>
        <i class="fa-solid fa-sack-dollar s-icon"></i>
    </div>
    <div class="stat-card grad-cyan">
        <div class="s-label">Clientes</div>
        <div class="s-value">{{ $kpis['clientes'] }}</div>
        <div class="s-sub"><i class="fa-solid fa-user-check"></i> Registrados</div>
        <i class="fa-solid fa-users s-icon"></i>
    </div>
    <div class="stat-card grad-violet">
        <div class="s-label">Vehículos</div>
        <div class="s-value">{{ $kpis['vehiculos'] }}</div>
        <div class="s-sub"><i class="fa-solid fa-car"></i> En el parque</div>
        <i class="fa-solid fa-car-side s-icon"></i>
    </div>
    <div class="stat-card grad-amber">
        <div class="s-label">Citas hoy</div>
        <div class="s-value">{{ $kpis['citas_hoy'] }}</div>
        <div class="s-sub"><i class="fa-solid fa-calendar-day"></i> Agendadas</div>
        <i class="fa-solid fa-calendar-check s-icon"></i>
    </div>
    <div class="stat-card grad-rose">
        <div class="s-label">Stock bajo</div>
        <div class="s-value">{{ $kpis['repuestos_bajos'] }}</div>
        <div class="s-sub"><i class="fa-solid fa-triangle-exclamation"></i> Requieren reposición</div>
        <i class="fa-solid fa-boxes-stacked s-icon"></i>
    </div>
</div>

{{-- ===== 4 Gráficos estadísticos ===== --}}
<div class="charts-grid" style="margin-bottom:20px;">
    <div class="panel">
        <div class="panel-head">
            <div><h3>Ingresos por mes</h3><div class="sub">Últimos 7 meses (S/)</div></div>
            <span class="badge-pill b-primary">Facturación</span>
        </div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartIngresos"></canvas></div></div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div><h3>Órdenes por estado</h3><div class="sub">Distribución actual</div></div>
            <span class="badge-pill b-info">Operaciones</span>
        </div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartEstados"></canvas></div></div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div><h3>Servicios más solicitados</h3><div class="sub">Ranking por cantidad</div></div>
            <span class="badge-pill b-success">Servicios</span>
        </div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartServicios"></canvas></div></div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div><h3>Productividad por mecánico</h3><div class="sub">Órdenes atendidas</div></div>
            <span class="badge-pill b-warning">Personal</span>
        </div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartMecanicos"></canvas></div></div>
    </div>
</div>

{{-- ===== Listas ===== --}}
<div class="lists-grid">
    <div class="panel" style="grid-column: span 2; min-width:0;">
        <div class="panel-head">
            <div><h3>Órdenes recientes</h3><div class="sub">Últimos ingresos al taller</div></div>
            <a href="{{ route('ordenes.index') }}" class="btn btn-light" style="height:34px;">Ver todas</a>
        </div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Orden</th><th>Cliente</th><th>Vehículo</th><th>Mecánico</th><th>Estado</th><th>Total</th></tr></thead>
                <tbody>
                @forelse ($recientes as $o)
                    <tr>
                        <td class="cell-strong">{{ $o->numero }}</td>
                        <td>{{ $o->cliente->nombre_completo ?? '—' }}</td>
                        <td>{{ $o->vehiculo->placa ?? '—' }} <span class="cell-muted">{{ $o->vehiculo->marca ?? '' }}</span></td>
                        <td class="cell-muted">{{ $o->mecanico->name ?? 'Sin asignar' }}</td>
                        <td><span class="badge-pill b-{{ $o->estadoColor() }}">{{ $estadosLabels[$o->estado] ?? $o->estado }}</span></td>
                        <td class="cell-strong">S/ {{ number_format($o->total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:30px;">Sin órdenes registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Próximas citas</h3></div></div>
        <div class="panel-body" style="padding-top:6px; padding-bottom:6px;">
            @forelse ($citas as $c)
                <div class="list-row">
                    <div class="mini-avatar bg-amber"><i class="fa-solid fa-calendar-day"></i></div>
                    <div>
                        <div class="lr-title">{{ $c->cliente->nombre_completo ?? 'Cliente' }}</div>
                        <div class="lr-sub">{{ $c->vehiculo->placa ?? '' }} · {{ $c->titulo }}</div>
                    </div>
                    <div class="lr-right">
                        <div class="lr-title" style="font-size:12.5px;">{{ $c->fecha_hora->translatedFormat('d M') }}</div>
                        <div class="lr-sub">{{ $c->fecha_hora->format('h:i A') }}</div>
                    </div>
                </div>
            @empty
                <div class="cell-muted" style="text-align:center; padding:20px;">Sin citas próximas.</div>
            @endforelse
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Stock bajo</h3><div class="sub">Requieren reposición</div></div></div>
        <div class="panel-body" style="padding-top:6px; padding-bottom:6px;">
            @forelse ($stockBajo as $r)
                <div class="list-row">
                    <div class="mini-avatar bg-rose"><i class="fa-solid fa-box"></i></div>
                    <div>
                        <div class="lr-title">{{ $r->nombre }}</div>
                        <div class="lr-sub">{{ $r->categoria }}</div>
                    </div>
                    <div class="lr-right">
                        <span class="badge-pill b-danger">{{ $r->stock }} / {{ $r->stock_minimo }}</span>
                    </div>
                </div>
            @empty
                <div class="cell-muted" style="text-align:center; padding:20px;">Todo el stock está en orden. 👍</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const mesesData    = @json($meses);
    const estadosData  = @json($porEstado);
    const estadosLabels = @json($estadosLabels);
    const serviciosData = @json($topServicios);
    const mecanicosData = @json($porMecanico);

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#7a8a92';
    const commonOpts = { responsive: true, maintainAspectRatio: false };

    // ---- 1) Ingresos por mes (barras con degradado) ----
    const ctxI = document.getElementById('chartIngresos').getContext('2d');
    const gI = ctxI.createLinearGradient(0, 0, 0, 300);
    gI.addColorStop(0, '#14b8a6'); gI.addColorStop(1, '#0d9488');
    new Chart(ctxI, {
        type: 'bar',
        data: {
            labels: mesesData.map(m => m.label),
            datasets: [{ label: 'Ingresos S/', data: mesesData.map(m => m.total),
                backgroundColor: gI, borderRadius: 8, maxBarThickness: 42 }]
        },
        options: { ...commonOpts,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#eef1f4' }, ticks: { callback: v => 'S/ ' + v } },
                      x: { grid: { display: false } } } }
    });

    // ---- 2) Órdenes por estado (dona) ----
    const palette = { recepcion:'#94a3b8', diagnostico:'#0ea5e9', en_proceso:'#f59e0b',
        esperando_repuestos:'#f97316', terminado:'#16a34a', entregado:'#0d9488', anulado:'#dc2626' };
    const keys = Object.keys(estadosData);
    new Chart(document.getElementById('chartEstados'), {
        type: 'doughnut',
        data: { labels: keys.map(k => estadosLabels[k] || k),
            datasets: [{ data: keys.map(k => estadosData[k]),
                backgroundColor: keys.map(k => palette[k] || '#94a3b8'), borderWidth: 2, borderColor: '#fff' }] },
        options: { ...commonOpts, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } } } }
    });

    // ---- 3) Servicios más solicitados (barras horizontales) ----
    new Chart(document.getElementById('chartServicios'), {
        type: 'bar',
        data: { labels: serviciosData.map(s => s.descripcion),
            datasets: [{ label: 'Cantidad', data: serviciosData.map(s => s.total),
                backgroundColor: '#6366f1', borderRadius: 6, maxBarThickness: 26 }] },
        options: { ...commonOpts, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#eef1f4' }, ticks: { precision: 0 } },
                      y: { grid: { display: false }, ticks: { font: { size: 11 } } } } }
    });

    // ---- 4) Productividad por mecánico (barras) ----
    const ctxM = document.getElementById('chartMecanicos').getContext('2d');
    const gM = ctxM.createLinearGradient(0, 0, 0, 300);
    gM.addColorStop(0, '#f59e0b'); gM.addColorStop(1, '#ea580c');
    new Chart(ctxM, {
        type: 'bar',
        data: { labels: mecanicosData.map(m => m.name),
            datasets: [{ label: 'Órdenes', data: mecanicosData.map(m => m.total),
                backgroundColor: gM, borderRadius: 8, maxBarThickness: 46 }] },
        options: { ...commonOpts,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#eef1f4' }, ticks: { precision: 0 } },
                      x: { grid: { display: false }, ticks: { font: { size: 11 } } } } }
    });
</script>
@endpush
