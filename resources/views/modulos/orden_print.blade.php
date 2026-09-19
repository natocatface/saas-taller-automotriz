<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden {{ $orden->numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2d33; font-size: 13px; padding: 28px 34px; }
        .doc { max-width: 800px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0d9488; padding-bottom: 16px; margin-bottom: 20px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand .logo { width: 48px; height: 48px; border-radius: 10px; background: #0d9488; color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: bold; }
        .brand b { font-size: 20px; }
        .brand span { font-size: 12px; color: #6b7c84; display: block; }
        .doc-meta { text-align: right; }
        .doc-meta .num { font-size: 22px; font-weight: 800; color: #0d9488; }
        .doc-meta .date { font-size: 12px; color: #6b7c84; margin-top: 4px; }
        .cols { display: flex; gap: 24px; margin-bottom: 20px; }
        .box { flex: 1; background: #f6f8fa; border-radius: 8px; padding: 14px 16px; }
        .box h4 { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: #0d9488; margin-bottom: 8px; }
        .box .row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 12.5px; }
        .box .row span:first-child { color: #6b7c84; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #0d9488; color: #fff; text-align: left; padding: 9px 12px; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; }
        th.r, td.r { text-align: right; }
        td { padding: 9px 12px; border-bottom: 1px solid #e4e9ee; font-size: 12.5px; }
        .section-title { font-size: 13px; font-weight: 700; margin: 18px 0 8px; color: #0f766e; }
        .totals { margin-left: auto; width: 300px; margin-top: 14px; }
        .totals .t { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #e4e9ee; }
        .totals .grand { font-size: 18px; font-weight: 800; color: #0d9488; border-bottom: none; padding-top: 10px; }
        .signs { display: flex; gap: 40px; margin-top: 60px; }
        .sign { flex: 1; text-align: center; border-top: 1px solid #1f2d33; padding-top: 6px; font-size: 12px; color: #6b7c84; }
        .foot { margin-top: 30px; text-align: center; font-size: 11px; color: #9aa8af; border-top: 1px solid #e4e9ee; padding-top: 12px; }
        .toolbar { max-width: 800px; margin: 0 auto 16px; text-align: right; }
        .btn-print { background: #0d9488; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="toolbar">
    <button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button>
</div>
<div class="doc">
    <div class="top">
        <div class="brand">
            <div class="logo">A</div>
            <div><b>AutoTaller Pro</b><span>Servicio automotriz especializado</span><span>RUC: 20601234567 · Lima, Perú</span></div>
        </div>
        <div class="doc-meta">
            <div class="num">{{ $orden->numero }}</div>
            <div class="date">Orden de servicio</div>
            <div class="date">Ingreso: {{ optional($orden->fecha_ingreso)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="cols">
        <div class="box">
            <h4>Cliente</h4>
            <div class="row"><span>Nombre</span><b>{{ $orden->cliente->nombre_completo ?? '—' }}</b></div>
            <div class="row"><span>Documento</span><b>{{ $orden->cliente->documento ?? '—' }}</b></div>
            <div class="row"><span>Teléfono</span><b>{{ $orden->cliente->telefono ?? '—' }}</b></div>
            <div class="row"><span>Correo</span><b>{{ $orden->cliente->email ?? '—' }}</b></div>
        </div>
        <div class="box">
            <h4>Vehículo</h4>
            <div class="row"><span>Placa</span><b>{{ $orden->vehiculo->placa ?? '—' }}</b></div>
            <div class="row"><span>Marca / Modelo</span><b>{{ $orden->vehiculo->marca ?? '' }} {{ $orden->vehiculo->modelo ?? '' }}</b></div>
            <div class="row"><span>Año</span><b>{{ $orden->vehiculo->anio ?? '—' }}</b></div>
            <div class="row"><span>Kilometraje</span><b>{{ $orden->kilometraje ? number_format($orden->kilometraje).' km' : '—' }}</b></div>
        </div>
    </div>

    @if ($orden->diagnostico)
        <div class="section-title">Diagnóstico</div>
        <div style="font-size:12.5px; color:#38484f;">{{ $orden->diagnostico }}</div>
    @endif

    @if ($orden->servicios->count())
        <div class="section-title">Servicios / mano de obra</div>
        <table>
            <thead><tr><th>Descripción</th><th class="r">Cant.</th><th class="r">Precio</th><th class="r">Subtotal</th></tr></thead>
            <tbody>
            @foreach ($orden->servicios as $s)
                <tr><td>{{ $s->descripcion }}</td><td class="r">{{ $s->cantidad }}</td><td class="r">S/ {{ number_format($s->precio,2) }}</td><td class="r">S/ {{ number_format($s->subtotal,2) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    @endif

    @if ($orden->repuestos->count())
        <div class="section-title">Repuestos / materiales</div>
        <table>
            <thead><tr><th>Descripción</th><th class="r">Cant.</th><th class="r">Precio</th><th class="r">Subtotal</th></tr></thead>
            <tbody>
            @foreach ($orden->repuestos as $r)
                <tr><td>{{ $r->descripcion }}</td><td class="r">{{ $r->cantidad }}</td><td class="r">S/ {{ number_format($r->precio,2) }}</td><td class="r">S/ {{ number_format($r->subtotal,2) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    @endif

    <div class="totals">
        <div class="t"><span>Subtotal</span><b>S/ {{ number_format($orden->subtotal,2) }}</b></div>
        <div class="t"><span>Descuento</span><b>- S/ {{ number_format($orden->descuento,2) }}</b></div>
        <div class="t"><span>IGV (18%)</span><b>S/ {{ number_format($orden->impuesto,2) }}</b></div>
        <div class="t grand"><span>TOTAL</span><span>S/ {{ number_format($orden->total,2) }}</span></div>
    </div>

    <div class="signs">
        <div class="sign">Firma del cliente</div>
        <div class="sign">Firma del taller</div>
    </div>

    <div class="foot">
        Documento generado por AutoTaller Pro · {{ now()->format('d/m/Y H:i') }} · ¡Gracias por su preferencia!
    </div>
</div>
<script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
