@extends('layouts.app')
@section('title', 'Estado SUNAT')

@section('content')
<div class="page-head">
    <div>
        <h1>Estado SUNAT</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Facturación · Estado SUNAT</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('facturacion.electronica.index') }}" class="btn btn-light"><i class="fa-solid fa-gear"></i> Configuración</a>
        @if ($resumen['pendiente'] + $resumen['rechazado'] > 0)
            <form method="POST" action="{{ route('facturacion.electronica.reintentar') }}" onsubmit="return confirm('¿Reintentar el envío de los comprobantes pendientes y rechazados a SUNAT?');">
                @csrf
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Reintentar pendientes</button>
            </form>
        @endif
    </div>
</div>

@unless ($config->habilitado && $config->driver !== 'none')
    <div class="alert-flash" style="background:#fef3c7; color:#b45309; border-color:#fde68a; margin-bottom:20px;">
        <i class="fa-solid fa-triangle-exclamation"></i>
        La facturación electrónica está deshabilitada o sin driver. Ve a
        <a href="{{ route('facturacion.electronica.index') }}" style="text-decoration:underline;">Configuración</a> para activarla.
    </div>
@endunless

<div class="grid kpi-grid" style="grid-template-columns:repeat(5,1fr); margin-bottom:22px;">
    <div class="kpi"><div class="kpi-icon bg-teal"><i class="fa-solid fa-circle-check"></i></div><div class="kpi-value">{{ $resumen['aceptado'] }}</div><div class="kpi-label">Aceptados</div></div>
    <div class="kpi"><div class="kpi-icon bg-amber"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="kpi-value">{{ $resumen['observado'] }}</div><div class="kpi-label">Observados</div></div>
    <div class="kpi"><div class="kpi-icon bg-blue"><i class="fa-solid fa-clock"></i></div><div class="kpi-value">{{ $resumen['pendiente'] }}</div><div class="kpi-label">Pendientes</div></div>
    <div class="kpi"><div class="kpi-icon bg-rose"><i class="fa-solid fa-circle-xmark"></i></div><div class="kpi-value">{{ $resumen['rechazado'] }}</div><div class="kpi-label">Rechazados</div></div>
    <div class="kpi"><div class="kpi-icon bg-violet"><i class="fa-solid fa-ban"></i></div><div class="kpi-value">{{ $resumen['anulado'] }}</div><div class="kpi-label">Anulados</div></div>
</div>

<div class="panel">
    <div class="panel-head">
        <div><h3>Comprobantes por enviar</h3><div class="sub">Pendientes, rechazados o con error · {{ $porEnviar->count() }} en cola</div></div>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Comprobante</th><th>Tipo</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado SUNAT</th><th>Detalle</th><th style="text-align:right;">Acción</th></tr></thead>
            <tbody>
            @forelse ($porEnviar as $c)
                @php $mapa = ['rechazado'=>'b-danger','error'=>'b-danger','pendiente'=>'b-info']; @endphp
                <tr>
                    <td class="cell-strong">{{ $c->serie_numero }}</td>
                    <td><span class="badge-pill {{ $c->tipo === 'factura' ? 'b-primary' : 'b-info' }}" style="text-transform:capitalize;">{{ $c->tipo }}</span></td>
                    <td>{{ $c->cliente->nombre_completo ?? 'Varios' }}</td>
                    <td class="cell-muted">{{ optional($c->fecha)->format('d/m/Y') }}</td>
                    <td class="cell-strong">S/ {{ number_format($c->total, 2) }}</td>
                    <td><span class="badge-pill {{ $mapa[$c->estado_sunat] ?? 'b-info' }}">{{ ucfirst($c->estado_sunat ?? 'pendiente') }}</span></td>
                    <td class="cell-muted" style="max-width:260px; font-size:12px;">{{ \Illuminate\Support\Str::limit($c->sunat_observaciones, 90) }}</td>
                    <td>
                        <div class="row-actions">
                            <form method="POST" action="{{ route('facturacion.reenviar', $c) }}" onsubmit="return confirm('¿Reenviar {{ $c->serie_numero }} a SUNAT?');">
                                @csrf
                                <button type="submit" class="ico-btn" title="Reenviar a SUNAT"><i class="fa-solid fa-paper-plane"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="cell-muted" style="text-align:center; padding:34px;"><i class="fa-solid fa-circle-check" style="color:var(--success);"></i> Todos los comprobantes están al día con SUNAT.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
