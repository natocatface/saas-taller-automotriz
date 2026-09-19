@extends('layouts.admin')
@section('title', 'Dashboard SaaS')

@section('content')
@php $estados = \App\Models\Taller::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Dashboard SaaS</h1>
        <div class="crumb">Resumen del negocio · {{ now()->translatedFormat('l, d \d\e F Y') }}</div>
    </div>
    <a href="{{ route('admin.talleres.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo taller</a>
</div>

<div class="stats-grid" style="margin-bottom:22px;">
    <div class="stat-card grad-violet"><div class="s-label">MRR</div><div class="s-value">S/ {{ number_format($kpis['mrr'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-arrow-trend-up"></i> Ingreso recurrente mensual</div><i class="fa-solid fa-money-bill-trend-up s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">ARR proyectado</div><div class="s-value">S/ {{ number_format($kpis['arr'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-calendar"></i> Anual estimado</div><i class="fa-solid fa-chart-line s-icon"></i></div>
    <div class="stat-card grad-teal"><div class="s-label">Talleres activos</div><div class="s-value">{{ $kpis['activos'] }}</div><div class="s-sub"><i class="fa-solid fa-shop"></i> de {{ $kpis['talleres'] }} totales</div><i class="fa-solid fa-shop s-icon"></i></div>
    <div class="stat-card grad-cyan"><div class="s-label">En prueba</div><div class="s-value">{{ $kpis['prueba'] }}</div><div class="s-sub"><i class="fa-solid fa-hourglass-half"></i> Periodo gratuito</div><i class="fa-solid fa-hourglass s-icon"></i></div>
    <div class="stat-card grad-amber"><div class="s-label">Ingresos del mes</div><div class="s-value">S/ {{ number_format($kpis['ingresos_mes'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-receipt"></i> Pagos cobrados</div><i class="fa-solid fa-sack-dollar s-icon"></i></div>
    <div class="stat-card grad-rose"><div class="s-label">Por vencer</div><div class="s-value">{{ $kpis['por_vencer'] }}</div><div class="s-sub"><i class="fa-solid fa-triangle-exclamation"></i> En los próximos 7 días</div><i class="fa-solid fa-clock s-icon"></i></div>
</div>

<div class="charts-grid" style="grid-template-columns: 2fr 1fr; margin-bottom:20px;">
    <div class="panel">
        <div class="panel-head"><div><h3>Ingresos por suscripciones</h3><div class="sub">Últimos 6 meses</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartIngresos"></canvas></div></div>
    </div>
    <div class="panel">
        <div class="panel-head"><div><h3>Talleres por plan</h3><div class="sub">Activos</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartPlanes"></canvas></div></div>
    </div>
</div>

<div class="lists-grid">
    <div class="panel" style="grid-column: span 2; min-width:0;">
        <div class="panel-head"><div><h3>Talleres recientes</h3></div><a href="{{ route('admin.talleres.index') }}" class="btn btn-light" style="height:34px;">Ver todos</a></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Taller</th><th>Plan</th><th>Estado</th><th>Vencimiento</th><th></th></tr></thead>
                <tbody>
                @forelse ($recientes as $t)
                    <tr>
                        <td class="cell-strong">{{ $t->nombre }}<div class="cell-muted">{{ $t->ciudad }}</div></td>
                        <td>{{ $t->plan->nombre ?? '—' }}</td>
                        <td><span class="badge-pill b-{{ $t->estadoColor() }}">{{ $estados[$t->estado] ?? $t->estado }}</span></td>
                        <td class="cell-muted">{{ optional($t->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</td>
                        <td style="text-align:right;"><a href="{{ route('admin.talleres.show', $t) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="cell-muted" style="text-align:center; padding:26px;">Aún no hay talleres registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Próximos vencimientos</h3></div></div>
        <div class="panel-body" style="padding-top:6px; padding-bottom:6px;">
            @forelse ($vencimientos as $t)
                <div class="list-row">
                    <div class="mini-avatar {{ $t->por_vencer ? 'bg-rose' : 'bg-teal' }}"><i class="fa-solid fa-shop"></i></div>
                    <div>
                        <div class="lr-title">{{ $t->nombre }}</div>
                        <div class="lr-sub">{{ $t->plan->nombre ?? '' }}</div>
                    </div>
                    <div class="lr-right">
                        <div class="lr-title" style="font-size:12.5px;">{{ optional($t->fecha_vencimiento)->format('d/m/Y') }}</div>
                        <div class="lr-sub">{{ $t->dias_restantes !== null ? $t->dias_restantes.' días' : '—' }}</div>
                    </div>
                </div>
            @empty
                <div class="cell-muted" style="text-align:center; padding:20px;">Sin vencimientos próximos.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ingresos = @json($ingresos);
    const porPlan = @json($porPlan);

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#7a8a92';
    const common = { responsive: true, maintainAspectRatio: false };

    const cI = document.getElementById('chartIngresos').getContext('2d');
    const g = cI.createLinearGradient(0,0,0,300); g.addColorStop(0,'#a855f7'); g.addColorStop(1,'#7c3aed');
    new Chart(cI, { type:'bar', data:{ labels: ingresos.map(m=>m.label), datasets:[{ data: ingresos.map(m=>m.total), backgroundColor:g, borderRadius:8, maxBarThickness:46 }] },
        options:{ ...common, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, grid:{color:'#eef1f4'}, ticks:{callback:v=>'S/ '+v}}, x:{grid:{display:false}} } } });

    const keys = Object.keys(porPlan);
    new Chart(document.getElementById('chartPlanes'), { type:'doughnut', data:{ labels:keys, datasets:[{ data:keys.map(k=>porPlan[k]), backgroundColor:['#8b5cf6','#ec4899','#06b6d4','#f59e0b','#14b8a6'], borderWidth:2, borderColor:'#fff' }] },
        options:{ ...common, cutout:'62%', plugins:{legend:{position:'bottom', labels:{boxWidth:12, padding:12, font:{size:11}}}} } });
</script>
@endpush
