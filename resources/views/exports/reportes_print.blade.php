<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte {{ $desde }} a {{ $hasta }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2d33; font-size: 13px; padding: 28px 34px; }
        .doc { max-width: 820px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #0d9488; padding-bottom: 14px; margin-bottom: 20px; }
        .top b { font-size: 20px; }
        .top .sub { font-size: 12px; color: #6b7c84; }
        .kpis { display: flex; gap: 14px; margin-bottom: 24px; }
        .kpi { flex: 1; background: #f6f8fa; border-radius: 8px; padding: 14px; text-align: center; }
        .kpi .v { font-size: 20px; font-weight: 800; color: #0d9488; }
        .kpi .l { font-size: 11px; color: #6b7c84; text-transform: uppercase; margin-top: 3px; }
        h3 { font-size: 14px; margin: 20px 0 8px; color: #0f766e; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #0d9488; color: #fff; text-align: left; padding: 8px 12px; font-size: 11px; text-transform: uppercase; }
        th.r, td.r { text-align: right; }
        td { padding: 7px 12px; border-bottom: 1px solid #e4e9ee; font-size: 12.5px; }
        .foot { margin-top: 26px; text-align: center; font-size: 11px; color: #9aa8af; border-top: 1px solid #e4e9ee; padding-top: 12px; }
        .toolbar { max-width: 820px; margin: 0 auto 16px; text-align: right; }
        .btn-print { background: #0d9488; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="toolbar"><button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button></div>
<div class="doc">
    <div class="top">
        <div>
            <b>{{ $config->empresa }}</b>
            <div class="sub">Reporte de gestión · {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</div>
        </div>
        <div class="sub">Generado: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="kpis">
        <div class="kpi"><div class="v">S/ {{ number_format($kpis['ventas'],2) }}</div><div class="l">Ventas</div></div>
        <div class="kpi"><div class="v">{{ $kpis['ordenes'] }}</div><div class="l">Órdenes</div></div>
        <div class="kpi"><div class="v">S/ {{ number_format($kpis['compras'],2) }}</div><div class="l">Compras</div></div>
        <div class="kpi"><div class="v">S/ {{ number_format($kpis['ticket'],2) }}</div><div class="l">Ticket prom.</div></div>
    </div>

    <h3>Servicios más vendidos</h3>
    <table>
        <thead><tr><th>Servicio</th><th class="r">Cantidad</th><th class="r">Total S/</th></tr></thead>
        <tbody>
        @forelse ($topServicios as $s)
            <tr><td>{{ $s->descripcion }}</td><td class="r">{{ $s->cant }}</td><td class="r">{{ number_format($s->total,2) }}</td></tr>
        @empty
            <tr><td colspan="3" style="text-align:center; color:#9aa8af;">Sin datos en el periodo.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h3>Productividad por mecánico</h3>
    <table>
        <thead><tr><th>Mecánico</th><th class="r">Órdenes</th><th class="r">Monto S/</th></tr></thead>
        <tbody>
        @forelse ($porMecanico as $m)
            <tr><td>{{ $m->name }}</td><td class="r">{{ $m->total }}</td><td class="r">{{ number_format($m->monto,2) }}</td></tr>
        @empty
            <tr><td colspan="3" style="text-align:center; color:#9aa8af;">Sin datos en el periodo.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="foot">{{ $config->empresa }} · Reporte generado por AutoTaller Pro</div>
</div>
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
