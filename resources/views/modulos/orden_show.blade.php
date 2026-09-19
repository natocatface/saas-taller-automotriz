@extends('layouts.app')
@section('title', 'Orden '.$orden->numero)

@section('content')
@php $estadosLabels = \App\Models\Orden::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Orden {{ $orden->numero }}
            <span class="badge-pill b-{{ $orden->estadoColor() }}" style="vertical-align:middle; margin-left:8px;">{{ $estadosLabels[$orden->estado] ?? $orden->estado }}</span>
        </h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('ordenes.index') }}">Órdenes</a> · {{ $orden->numero }}</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('ordenes.imprimir', $orden) }}" target="_blank" class="btn btn-light"><i class="fa-solid fa-print"></i> Imprimir / PDF</a>
        <a href="{{ route('ordenes.edit', $orden) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Editar</a>
    </div>
</div>

<div class="charts-grid" style="grid-template-columns: 2fr 1fr; margin-bottom:20px;">
    {{-- Datos --}}
    <div style="display:flex; flex-direction:column; gap:20px; min-width:0;">
        <div class="panel">
            <div class="panel-head"><h3>Información general</h3></div>
            <div class="panel-body">
                <div class="detail-grid">
                    <div class="detail-item"><div class="di-label">Cliente</div><div class="di-value">{{ $orden->cliente->nombre_completo ?? '—' }}</div></div>
                    <div class="detail-item"><div class="di-label">Teléfono</div><div class="di-value">{{ $orden->cliente->telefono ?? '—' }}</div></div>
                    <div class="detail-item"><div class="di-label">Vehículo</div><div class="di-value">{{ $orden->vehiculo->placa ?? '—' }}</div></div>
                    <div class="detail-item"><div class="di-label">Marca / Modelo</div><div class="di-value">{{ $orden->vehiculo->marca ?? '' }} {{ $orden->vehiculo->modelo ?? '' }}</div></div>
                    <div class="detail-item"><div class="di-label">Mecánico</div><div class="di-value">{{ $orden->mecanico->name ?? 'Sin asignar' }}</div></div>
                    <div class="detail-item"><div class="di-label">Ingreso</div><div class="di-value">{{ optional($orden->fecha_ingreso)->format('d/m/Y') }}</div></div>
                    <div class="detail-item"><div class="di-label">Entrega</div><div class="di-value">{{ optional($orden->fecha_entrega)->format('d/m/Y') ?? '—' }}</div></div>
                    <div class="detail-item"><div class="di-label">Kilometraje</div><div class="di-value">{{ $orden->kilometraje ? number_format($orden->kilometraje).' km' : '—' }}</div></div>
                    <div class="detail-item"><div class="di-label">Prioridad</div><div class="di-value" style="text-transform:capitalize;">{{ $orden->prioridad }}</div></div>
                </div>
                @if ($orden->diagnostico)
                    <div style="margin-top:18px;"><div class="di-label" style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Diagnóstico</div><div>{{ $orden->diagnostico }}</div></div>
                @endif
                @if ($orden->observaciones)
                    <div style="margin-top:14px;"><div class="di-label" style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Observaciones</div><div>{{ $orden->observaciones }}</div></div>
                @endif
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3>Servicios</h3></div>
            <div class="table-wrap">
                <table class="tbl">
                    <thead><tr><th>Descripción</th><th class="col-num">Cant.</th><th style="text-align:right;">Precio</th><th style="text-align:right;">Subtotal</th></tr></thead>
                    <tbody>
                    @forelse ($orden->servicios as $s)
                        <tr><td>{{ $s->descripcion }}</td><td>{{ $s->cantidad }}</td><td style="text-align:right;">S/ {{ number_format($s->precio,2) }}</td><td style="text-align:right;" class="cell-strong">S/ {{ number_format($s->subtotal,2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="cell-muted" style="text-align:center; padding:20px;">Sin servicios.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3>Repuestos</h3></div>
            <div class="table-wrap">
                <table class="tbl">
                    <thead><tr><th>Descripción</th><th class="col-num">Cant.</th><th style="text-align:right;">Precio</th><th style="text-align:right;">Subtotal</th></tr></thead>
                    <tbody>
                    @forelse ($orden->repuestos as $r)
                        <tr><td>{{ $r->descripcion }}</td><td>{{ $r->cantidad }}</td><td style="text-align:right;">S/ {{ number_format($r->precio,2) }}</td><td style="text-align:right;" class="cell-strong">S/ {{ number_format($r->subtotal,2) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="cell-muted" style="text-align:center; padding:20px;">Sin repuestos.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Panel lateral: estado + totales --}}
    <div style="display:flex; flex-direction:column; gap:20px; min-width:0;">
        <div class="panel">
            <div class="panel-head"><h3>Estado de la orden</h3></div>
            <div class="panel-body">
                <form method="POST" action="{{ route('ordenes.estado', $orden) }}">
                    @csrf @method('PATCH')
                    <label class="form-label">Estado del trabajo</label>
                    <select name="estado" class="form-control" style="margin-bottom:14px;">
                        @foreach ($estadosLabels as $k => $label)
                            <option value="{{ $k }}" @selected($orden->estado === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="form-label">Estado de pago</label>
                    <select name="estado_pago" class="form-control" style="margin-bottom:16px;">
                        @foreach (['pendiente'=>'Pendiente','parcial'=>'Parcial','pagado'=>'Pagado'] as $k => $label)
                            <option value="{{ $k }}" @selected($orden->estado_pago === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fa-solid fa-rotate"></i> Actualizar estado</button>
                </form>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head"><h3>Resumen de costos</h3></div>
            <div class="panel-body">
                <div class="totals-box" style="max-width:none;">
                    <div class="trow"><span>Subtotal</span><b>S/ {{ number_format($orden->subtotal,2) }}</b></div>
                    <div class="trow"><span>Descuento</span><b>- S/ {{ number_format($orden->descuento,2) }}</b></div>
                    <div class="trow"><span>IGV (18%)</span><b>S/ {{ number_format($orden->impuesto,2) }}</b></div>
                    <div class="trow grand"><span>Total</span><span>S/ {{ number_format($orden->total,2) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
