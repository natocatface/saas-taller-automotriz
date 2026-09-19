<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $comprobante->serie_numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2d33; font-size: 13px; padding: 28px 34px; }
        .doc { max-width: 760px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 22px; }
        .brand .logo { width: 48px; height: 48px; border-radius: 10px; background: #0d9488; color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: bold; float: left; margin-right: 12px; }
        .brand b { font-size: 19px; }
        .brand span { font-size: 12px; color: #6b7c84; display: block; }
        .doc-box { border: 2px solid #0d9488; border-radius: 10px; padding: 12px 22px; text-align: center; min-width: 230px; }
        .doc-box .tipo { font-size: 13px; font-weight: 700; color: #0d9488; text-transform: uppercase; }
        .doc-box .ruc { font-size: 12px; color: #6b7c84; margin: 3px 0; }
        .doc-box .num { font-size: 18px; font-weight: 800; }
        .cols { display: flex; gap: 24px; margin-bottom: 18px; }
        .box { flex: 1; background: #f6f8fa; border-radius: 8px; padding: 12px 14px; }
        .box .row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 12.5px; }
        .box .row span:first-child { color: #6b7c84; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #0d9488; color: #fff; text-align: left; padding: 9px 12px; font-size: 11px; text-transform: uppercase; }
        th.r, td.r { text-align: right; }
        td { padding: 8px 12px; border-bottom: 1px solid #e4e9ee; font-size: 12.5px; }
        .totals { margin-left: auto; width: 280px; margin-top: 12px; }
        .totals .t { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; border-bottom: 1px solid #e4e9ee; }
        .totals .grand { font-size: 18px; font-weight: 800; color: #0d9488; border-bottom: none; padding-top: 10px; }
        .foot { margin-top: 26px; text-align: center; font-size: 11px; color: #9aa8af; border-top: 1px solid #e4e9ee; padding-top: 12px; }
        .anulado { position: fixed; top: 40%; left: 50%; transform: translate(-50%,-50%) rotate(-25deg); font-size: 90px; color: rgba(220,38,38,.16); font-weight: 900; letter-spacing: 6px; }
        .toolbar { max-width: 760px; margin: 0 auto 16px; text-align: right; }
        .btn-print { background: #0d9488; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
@if ($comprobante->estado === 'anulado')<div class="anulado">ANULADO</div>@endif
<div class="toolbar"><button class="btn-print" onclick="window.print()">🖨 Imprimir / Guardar PDF</button></div>
<div class="doc">
    <div class="top">
        <div class="brand">
            <div class="logo">A</div>
            <b>{{ $config->empresa }}</b>
            <span>{{ $config->direccion }}</span>
            <span>Tel: {{ $config->telefono }}</span>
        </div>
        <div class="doc-box">
            <div class="tipo">{{ $comprobante->tipo_label }}{{ ($fe->habilitado ?? false) ? ' electrónica' : '' }}</div>
            <div class="ruc">RUC: {{ $fe->ruc ?: $config->ruc }}</div>
            <div class="num">{{ $comprobante->serie_numero }}</div>
            @if ($comprobante->esNota() && $comprobante->docAfectado)
                <div class="ruc">Afecta: {{ $comprobante->docAfectado->serie_numero }}</div>
                <div class="ruc" style="font-size:11px;">{{ $comprobante->motivo_codigo }} · {{ $comprobante->motivo_desc }}</div>
            @endif
        </div>
    </div>

    <div class="cols">
        <div class="box">
            <div class="row"><span>Cliente</span><b>{{ $comprobante->cliente->nombre_completo ?? 'Cliente varios' }}</b></div>
            <div class="row"><span>Documento</span><b>{{ $comprobante->cliente->documento ?? '—' }}</b></div>
            <div class="row"><span>Fecha de emisión</span><b>{{ optional($comprobante->fecha)->format('d/m/Y') }}</b></div>
        </div>
        <div class="box">
            <div class="row"><span>Orden</span><b>{{ $comprobante->orden->numero ?? '—' }}</b></div>
            <div class="row"><span>Vehículo</span><b>{{ $comprobante->orden->vehiculo->placa ?? '—' }}</b></div>
            <div class="row"><span>Método de pago</span><b style="text-transform:capitalize;">{{ $comprobante->metodo_pago }}</b></div>
        </div>
    </div>

    <table>
        <thead><tr><th>Descripción</th><th class="r">Cant.</th><th class="r">P. Unit.</th><th class="r">Importe</th></tr></thead>
        <tbody>
        @if ($comprobante->orden)
            @foreach ($comprobante->orden->servicios as $s)
                <tr><td>{{ $s->descripcion }}</td><td class="r">{{ $s->cantidad }}</td><td class="r">S/ {{ number_format($s->precio,2) }}</td><td class="r">S/ {{ number_format($s->subtotal,2) }}</td></tr>
            @endforeach
            @foreach ($comprobante->orden->repuestos as $r)
                <tr><td>{{ $r->descripcion }}</td><td class="r">{{ $r->cantidad }}</td><td class="r">S/ {{ number_format($r->precio,2) }}</td><td class="r">S/ {{ number_format($r->subtotal,2) }}</td></tr>
            @endforeach
        @else
            <tr><td>Venta según comprobante</td><td class="r">1</td><td class="r">S/ {{ number_format($comprobante->subtotal,2) }}</td><td class="r">S/ {{ number_format($comprobante->subtotal,2) }}</td></tr>
        @endif
        </tbody>
    </table>

    <div class="totals">
        <div class="t"><span>Op. gravada</span><b>S/ {{ number_format($comprobante->subtotal,2) }}</b></div>
        <div class="t"><span>IGV</span><b>S/ {{ number_format($comprobante->igv,2) }}</b></div>
        <div class="t grand"><span>TOTAL</span><span>S/ {{ number_format($comprobante->total,2) }}</span></div>
    </div>

    @php
        $feHabilitado = $fe->habilitado ?? false;
        $feRuc = $fe->ruc ?: $config->ruc;
        $tipoDocSunat = $comprobante->tipoDocSunat();

        // Catálogo 06 SUNAT del cliente
        $mapDoc = ['RUC' => '6', 'DNI' => '1', 'CE' => '4', 'Pasaporte' => '7'];
        $cli = $comprobante->cliente;
        $cliTipo = $cli ? ($mapDoc[$cli->tipo_documento] ?? '1') : '0';
        $cliNum = $cli->documento ?? null;
        if ($comprobante->tipo === 'factura') { $cliTipo = '6'; }
        if (blank($cliNum)) { $cliTipo = '0'; $cliNum = '00000000'; }

        // Cadena QR SUNAT: RUC | tipo | serie | numero | IGV | total | fecha | tipoDocCli | numDocCli | hash
        $qrData = implode('|', [
            $feRuc,
            $tipoDocSunat,
            $comprobante->serie,
            $comprobante->numero,
            number_format($comprobante->igv, 2, '.', ''),
            number_format($comprobante->total, 2, '.', ''),
            optional($comprobante->fecha)->format('Y-m-d'),
            $cliTipo,
            $cliNum,
            $comprobante->hash_cpe ?? '',
        ]);
    @endphp

    @if ($feHabilitado)
        <div class="cpe">
            <div id="qrcode" class="qrbox"></div>
            <div class="cpe-info">
                <b>Representación impresa de la {{ $comprobante->tipo_label }} Electrónica</b>
                <div class="cpe-row"><span>Autorizado mediante</span> Resolución de Intendencia · SUNAT</div>
                @if ($comprobante->hash_cpe)
                    <div class="cpe-row"><span>Hash</span> {{ $comprobante->hash_cpe }}</div>
                @endif
                <div class="cpe-row"><span>Estado SUNAT</span> <b style="text-transform:capitalize;">{{ $comprobante->estado_sunat ?? 'pendiente' }}</b></div>
                <div class="cpe-row cpe-hint">Consulte su comprobante en www.sunat.gob.pe</div>
            </div>
        </div>
    @endif

    <div class="foot">Representación impresa · {{ $config->empresa }} · {{ now()->format('d/m/Y H:i') }}</div>
</div>
<style>
    .cpe { display:flex; gap:18px; align-items:center; margin-top:26px; padding-top:16px; border-top:1px solid #e4e9ee; }
    .qrbox { width:120px; height:120px; flex-shrink:0; }
    .qrbox img, .qrbox canvas { width:120px !important; height:120px !important; }
    .cpe-info { font-size:11.5px; color:#4b5c63; line-height:1.6; }
    .cpe-info b { font-size:12.5px; color:#1f2d33; }
    .cpe-row span { color:#9aa8af; display:inline-block; min-width:120px; }
    .cpe-row.cpe-hint { color:#9aa8af; margin-top:4px; }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    (function () {
        var el = document.getElementById('qrcode');
        var data = @json($qrData);
        if (el && window.QRCode && data) {
            new QRCode(el, { text: data, width: 120, height: 120, correctLevel: QRCode.CorrectLevel.M });
        }
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 600); });
    })();
</script>
</body>
</html>
