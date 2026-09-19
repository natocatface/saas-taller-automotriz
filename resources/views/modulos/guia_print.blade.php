<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $guia->serie_numero }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2d33; font-size: 13px; padding: 28px 34px; }
        .doc { max-width: 760px; margin: 0 auto; }
        .top { display: flex; justify-content: space-between; align-items: stretch; margin-bottom: 22px; }
        .brand .logo { width: 48px; height: 48px; border-radius: 10px; background: #0d9488; color: #fff; display: grid; place-items: center; font-size: 24px; font-weight: bold; float: left; margin-right: 12px; }
        .brand b { font-size: 19px; }
        .brand span { font-size: 12px; color: #6b7c84; display: block; }
        .doc-box { border: 2px solid #0d9488; border-radius: 10px; padding: 12px 22px; text-align: center; min-width: 240px; }
        .doc-box .tipo { font-size: 12px; font-weight: 700; color: #0d9488; text-transform: uppercase; }
        .doc-box .ruc { font-size: 12px; color: #6b7c84; margin: 3px 0; }
        .doc-box .num { font-size: 17px; font-weight: 800; }
        .cols { display: flex; gap: 24px; margin-bottom: 16px; }
        .box { flex: 1; background: #f6f8fa; border-radius: 8px; padding: 12px 14px; }
        .box .row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 12.5px; gap: 12px; }
        .box .row span:first-child { color: #6b7c84; }
        .box h4 { font-size: 12px; text-transform: uppercase; color: #0d9488; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #0d9488; color: #fff; text-align: left; padding: 9px 12px; font-size: 11px; text-transform: uppercase; }
        th.r, td.r { text-align: right; }
        td { padding: 8px 12px; border-bottom: 1px solid #e4e9ee; font-size: 12.5px; }
        .cpe { display:flex; gap:18px; align-items:center; margin-top:24px; padding-top:16px; border-top:1px solid #e4e9ee; }
        .qrbox { width:120px; height:120px; flex-shrink:0; }
        .cpe-info { font-size:11.5px; color:#4b5c63; line-height:1.6; }
        .foot { margin-top: 22px; text-align: center; font-size: 11px; color: #9aa8af; border-top: 1px solid #e4e9ee; padding-top: 12px; }
        .toolbar { max-width: 760px; margin: 0 auto 16px; text-align: right; }
        .btn-print { background: #0d9488; color: #fff; border: none; padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
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
            <div class="tipo">Guía de Remisión Electrónica{{ ($fe->habilitado ?? false) ? '' : ' (borrador)' }}</div>
            <div class="ruc">RUC: {{ $fe->ruc ?: $config->ruc }}</div>
            <div class="num">{{ $guia->serie_numero }}</div>
        </div>
    </div>

    <div class="cols">
        <div class="box">
            <h4>Destinatario</h4>
            <div class="row"><span>Nombre</span><b>{{ $guia->destinatario_nombre }}</b></div>
            <div class="row"><span>Documento</span><b>{{ $guia->destinatario_num_doc }}</b></div>
            <div class="row"><span>Motivo</span><b>{{ $guia->motivo_desc }}</b></div>
        </div>
        <div class="box">
            <h4>Traslado</h4>
            <div class="row"><span>Fecha</span><b>{{ optional($guia->fecha_traslado)->format('d/m/Y') }}</b></div>
            <div class="row"><span>Modalidad</span><b>{{ $guia->esPrivado() ? 'Transporte privado' : 'Transporte público' }}</b></div>
            <div class="row"><span>Peso</span><b>{{ number_format($guia->peso_total, 3) }} {{ $guia->unidad_peso }}</b></div>
        </div>
    </div>

    <div class="cols">
        <div class="box"><h4>Partida</h4><div class="row"><span>{{ $guia->partida_ubigeo }}</span> {{ $guia->partida_direccion }}</div></div>
        <div class="box"><h4>Llegada</h4><div class="row"><span>{{ $guia->llegada_ubigeo }}</span> {{ $guia->llegada_direccion }}</div></div>
    </div>

    @if ($guia->esPrivado())
        <div class="box" style="margin-bottom:16px;"><h4>Vehículo y conductor</h4>
            <div class="row"><span>Placa</span><b>{{ $guia->vehiculo_placa }}</b> <span>Conductor</span><b>{{ $guia->chofer_nombre }} · {{ $guia->chofer_doc }} · Lic. {{ $guia->chofer_licencia }}</b></div>
        </div>
    @else
        <div class="box" style="margin-bottom:16px;"><h4>Transportista</h4>
            <div class="row"><span>{{ $guia->transportista_doc }}</span><b>{{ $guia->transportista_nombre }}</b></div>
        </div>
    @endif

    <table>
        <thead><tr><th>Descripción</th><th class="r">Cantidad</th><th class="r">Unidad</th></tr></thead>
        <tbody>
        @foreach (($guia->items ?? []) as $it)
            <tr><td>{{ $it['descripcion'] ?? '' }}</td><td class="r">{{ $it['cantidad'] ?? '' }}</td><td class="r">{{ $it['unidad'] ?? '' }}</td></tr>
        @endforeach
        </tbody>
    </table>

    @php
        $qrData = implode('|', [
            $fe->ruc ?: $config->ruc,
            '09',
            $guia->serie,
            $guia->numero,
            optional($guia->fecha)->format('Y-m-d'),
            $guia->destinatario_tipo_doc,
            $guia->destinatario_num_doc,
            $guia->hash_cpe ?? '',
        ]);
    @endphp

    @if ($fe->habilitado ?? false)
        <div class="cpe">
            <div id="qrcode" class="qrbox"></div>
            <div class="cpe-info">
                <b>Representación impresa de la Guía de Remisión Electrónica</b>
                @if ($guia->hash_cpe)<div>Hash: {{ $guia->hash_cpe }}</div>@endif
                <div>Estado SUNAT: <b style="text-transform:capitalize;">{{ $guia->estado_sunat ?? 'pendiente' }}</b></div>
                <div>Consulte su documento en www.sunat.gob.pe</div>
            </div>
        </div>
    @endif

    <div class="foot">Representación impresa · {{ $config->empresa }} · {{ now()->format('d/m/Y H:i') }}</div>
</div>
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
