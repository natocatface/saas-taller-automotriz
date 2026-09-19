@extends('layouts.app')
@section('title', 'Reportes')

@section('content')
@php $estadosLabels = \App\Models\Orden::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Reportes</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Reportes</div>
    </div>
    <form method="GET" action="{{ route('reportes.index') }}" style="display:flex; gap:8px; align-items:center;">
        <input type="date" name="desde" value="{{ $desde }}" class="form-control" style="width:150px;">
        <input type="date" name="hasta" value="{{ $hasta }}" class="form-control" style="width:150px;">
        <button type="submit" class="btn btn-primary" style="height:40px;"><i class="fa-solid fa-filter"></i> Aplicar</button>
        <a href="{{ route('export.reportes', ['desde' => $desde, 'hasta' => $hasta]) }}" target="_blank" class="btn btn-light" style="height:40px;"><i class="fa-solid fa-file-pdf"></i> PDF</a>
    </form>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="stat-card grad-teal"><div class="s-label">Ventas del periodo</div><div class="s-value">S/ {{ number_format($kpis['ventas'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-sack-dollar"></i> Comprobantes emitidos</div><i class="fa-solid fa-chart-line s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">Órdenes</div><div class="s-value">{{ $kpis['ordenes'] }}</div><div class="s-sub"><i class="fa-solid fa-clipboard-list"></i> Ingresadas</div><i class="fa-solid fa-clipboard s-icon"></i></div>
    <div class="stat-card grad-rose"><div class="s-label">Compras</div><div class="s-value">S/ {{ number_format($kpis['compras'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-cart-shopping"></i> A proveedores</div><i class="fa-solid fa-cart-shopping s-icon"></i></div>
    <div class="stat-card grad-violet"><div class="s-label">Ticket promedio</div><div class="s-value">S/ {{ number_format($kpis['ticket'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-calculator"></i> Por comprobante</div><i class="fa-solid fa-receipt s-icon"></i></div>
</div>

<div class="charts-grid" style="margin-bottom:20px;">
    <div class="panel">
        <div class="panel-head"><div><h3>Ventas por mes</h3><div class="sub">Últimos 12 meses</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartVentas"></canvas></div></div>
    </div>
    <div class="panel">
        <div class="panel-head"><div><h3>Órdenes por estado</h3><div class="sub">En el periodo</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartEstados"></canvas></div></div>
    </div>
    <div class="panel">
        <div class="panel-head"><div><h3>Top servicios</h3><div class="sub">Por facturación</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartServicios"></canvas></div></div>
    </div>
    <div class="panel">
        <div class="panel-head"><div><h3>Productividad por mecánico</h3><div class="sub">Órdenes atendidas</div></div></div>
        <div class="panel-body"><div class="chart-box"><canvas id="chartMecanicos"></canvas></div></div>
    </div>
</div>

<div class="lists-grid">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Servicios más vendidos</h3></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Servicio</th><th style="text-align:right;">Cant.</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody>
                @forelse ($topServicios as $s)
                    <tr><td>{{ $s->descripcion }}</td><td style="text-align:right;">{{ $s->cant }}</td><td style="text-align:right;" class="cell-strong">S/ {{ number_format($s->total,2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="cell-muted" style="text-align:center; padding:20px;">Sin datos en el periodo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Repuestos más vendidos</h3></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Repuesto</th><th style="text-align:right;">Cant.</th><th style="text-align:right;">Total</th></tr></thead>
                <tbody>
                @forelse ($topRepuestos as $r)
                    <tr><td>{{ $r->descripcion }}</td><td style="text-align:right;">{{ $r->cant }}</td><td style="text-align:right;" class="cell-strong">S/ {{ number_format($r->total,2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="cell-muted" style="text-align:center; padding:20px;">Sin datos en el periodo.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ventasMes = @json($ventasMes);
    const estadosData = @json($porEstado);
    const estadosLabels = @json($estadosLabels);
    const serviciosData = @json($topServicios);
    const mecanicosData = @json($porMecanico);

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#7a8a92';
    const common = { responsive: true, maintainAspectRatio: false };

    const cV = document.getElementById('chartVentas').getContext('2d');
    const g = cV.createLinearGradient(0,0,0,300); g.addColorStop(0,'rgba(13,148,136,.35)'); g.addColorStop(1,'rgba(13,148,136,0)');
    new Chart(cV, { type:'line', data:{ labels: ventasMes.map(m=>m.label), datasets:[{ data: ventasMes.map(m=>m.total), borderColor:'#0d9488', backgroundColor:g, borderWidth:3, fill:true, tension:.4, pointRadius:3 }] },
        options:{ ...common, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, grid:{color:'#eef1f4'}, ticks:{callback:v=>'S/ '+v}}, x:{grid:{display:false}} } } });

    const palette = { recepcion:'#94a3b8', diagnostico:'#0ea5e9', en_proceso:'#f59e0b', esperando_repuestos:'#f97316', terminado:'#16a34a', entregado:'#0d9488', anulado:'#dc2626' };
    const keys = Object.keys(estadosData);
    new Chart(document.getElementById('chartEstados'), { type:'doughnut', data:{ labels:keys.map(k=>estadosLabels[k]||k), datasets:[{ data:keys.map(k=>estadosData[k]), backgroundColor:keys.map(k=>palette[k]||'#94a3b8'), borderWidth:2, borderColor:'#fff' }] },
        options:{ ...common, cutout:'62%', plugins:{legend:{position:'bottom', labels:{boxWidth:12, padding:12, font:{size:11}}}} } });

    new Chart(document.getElementById('chartServicios'), { type:'bar', data:{ labels:serviciosData.map(s=>s.descripcion), datasets:[{ data:serviciosData.map(s=>s.total), backgroundColor:'#6366f1', borderRadius:6, maxBarThickness:24 }] },
        options:{ ...common, indexAxis:'y', plugins:{legend:{display:false}}, scales:{ x:{beginAtZero:true, grid:{color:'#eef1f4'}}, y:{grid:{display:false}, ticks:{font:{size:10}}} } } });

    const cM = document.getElementById('chartMecanicos').getContext('2d');
    const gM = cM.createLinearGradient(0,0,0,300); gM.addColorStop(0,'#f59e0b'); gM.addColorStop(1,'#ea580c');
    new Chart(cM, { type:'bar', data:{ labels:mecanicosData.map(m=>m.name), datasets:[{ data:mecanicosData.map(m=>m.total), backgroundColor:gM, borderRadius:8, maxBarThickness:44 }] },
        options:{ ...common, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true, grid:{color:'#eef1f4'}, ticks:{precision:0}}, x:{grid:{display:false}, ticks:{font:{size:10}}} } } });
</script>
@endpush
