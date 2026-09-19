@extends('layouts.app')
@section('title', 'Facturación')

@section('content')
<div class="page-head">
    <div>
        <h1>Facturación y ventas</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Facturación</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('export.ventas') }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Exportar Excel</a>
        <a href="{{ route('facturacion.create') }}" class="btn btn-primary"><i class="fa-solid fa-file-invoice-dollar"></i> Emitir comprobante</a>
    </div>
</div>

<div class="grid kpi-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:20px;">
    <div class="kpi"><div class="kpi-icon bg-teal"><i class="fa-solid fa-receipt"></i></div><div class="kpi-value">{{ $resumen['emitidos'] }}</div><div class="kpi-label">Comprobantes emitidos</div></div>
    <div class="kpi"><div class="kpi-icon bg-blue"><i class="fa-solid fa-sack-dollar"></i></div><div class="kpi-value">S/ {{ number_format($resumen['ventas_mes'], 0) }}</div><div class="kpi-label">Ventas del mes</div></div>
    <div class="kpi"><div class="kpi-icon bg-violet"><i class="fa-solid fa-calculator"></i></div><div class="kpi-value">S/ {{ number_format($resumen['ticket'], 0) }}</div><div class="kpi-label">Ticket promedio</div></div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Comprobantes</h3><div class="sub">{{ $comprobantes->total() }} registros</div></div>
        <form method="GET" action="{{ route('facturacion.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Serie, número o cliente..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('facturacion.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Comprobante</th><th>Tipo</th><th>Cliente</th><th>Orden</th><th>Fecha</th><th>Total</th><th>Estado</th><th>SUNAT</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($comprobantes as $c)
                <tr>
                    <td class="cell-strong">{{ $c->serie_numero }}</td>
                    <td>
                        <span class="badge-pill {{ $c->esNota() ? 'b-warning' : ($c->tipo === 'factura' ? 'b-primary' : 'b-info') }}">{{ $c->tipo_label }}</span>
                        @if ($c->esNota() && $c->docAfectado)<div class="cell-muted" style="font-size:11px;">afecta {{ $c->docAfectado->serie_numero }}</div>@endif
                    </td>
                    <td>{{ $c->cliente->nombre_completo ?? 'Varios' }}</td>
                    <td class="cell-muted">{{ $c->orden->numero ?? '—' }}</td>
                    <td class="cell-muted">{{ optional($c->fecha)->format('d/m/Y') }}</td>
                    <td class="cell-strong">S/ {{ number_format($c->total, 2) }}</td>
                    <td><span class="badge-pill {{ $c->estado === 'emitido' ? 'b-success' : 'b-danger' }}">{{ ucfirst($c->estado) }}</span></td>
                    <td>
                        @php $es = $c->estado_sunat ?? 'pendiente';
                            $mapa = ['aceptado'=>'b-success','observado'=>'b-warning','rechazado'=>'b-danger','error'=>'b-danger','anulado'=>'b-danger','anulando'=>'b-warning','pendiente'=>'b-info'];
                        @endphp
                        <span class="badge-pill {{ $mapa[$es] ?? 'b-info' }}" title="{{ $c->sunat_observaciones }}">{{ ucfirst($es) }}</span>
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('facturacion.imprimir', $c) }}" target="_blank" class="ico-btn" title="Imprimir"><i class="fa-solid fa-print"></i></a>
                            @if ($c->estado === 'emitido' && ! $c->esNota())
                                <a href="{{ route('facturacion.nota.create', $c) }}" class="ico-btn" title="Emitir nota de crédito/débito"><i class="fa-solid fa-file-circle-plus"></i></a>
                            @endif
                            @if ($c->estado === 'emitido' && in_array($c->estado_sunat, ['pendiente','rechazado','error']))
                                <form method="POST" action="{{ route('facturacion.reenviar', $c) }}" onsubmit="return confirm('¿Reenviar el comprobante {{ $c->serie_numero }} a SUNAT?');">
                                    @csrf
                                    <button type="submit" class="ico-btn" title="Reenviar a SUNAT"><i class="fa-solid fa-paper-plane"></i></button>
                                </form>
                            @endif
                            @if ($c->estado === 'emitido')
                                <form method="POST" action="{{ route('facturacion.anular', $c) }}" onsubmit="return anularComprobante(this, '{{ $c->serie_numero }}');">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="motivo" value="">
                                    <button type="submit" class="ico-btn ico-danger" title="Anular"><i class="fa-solid fa-ban"></i></button>
                                </form>
                            @endif
                            @if ($c->estado === 'anulado' && $c->sunat_ticket && $c->estado_sunat === 'anulando')
                                <form method="POST" action="{{ route('facturacion.consultar-baja', $c) }}" title="Consultar baja en SUNAT">
                                    @csrf
                                    <button type="submit" class="ico-btn" title="Consultar baja en SUNAT"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="cell-muted" style="text-align:center; padding:34px;">No hay comprobantes emitidos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $comprobantes])
</div>

@push('scripts')
<script>
    function anularComprobante(form, serie) {
        if (!confirm('¿Anular el comprobante ' + serie + '?')) return false;
        const motivo = prompt('Motivo de la anulación (se comunica a SUNAT):', 'Anulación de la operación');
        if (motivo === null) return false; // canceló
        form.querySelector('input[name="motivo"]').value = motivo || 'Anulación de la operación';
        return true;
    }
</script>
@endpush
@endsection
