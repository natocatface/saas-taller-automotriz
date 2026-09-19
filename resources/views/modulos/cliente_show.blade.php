@extends('layouts.app')
@section('title', 'Cliente '.$cliente->nombre_completo)

@section('content')
@php $estadosLabels = \App\Models\Orden::estados(); @endphp

<div class="page-head">
    <div>
        <h1>{{ $cliente->nombre_completo }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('clientes.index') }}">Clientes</a> · Ficha</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-light"><i class="fa-solid fa-pen"></i> Editar</a>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva orden</a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="stat-card grad-cyan"><div class="s-label">Vehículos</div><div class="s-value">{{ $stats['vehiculos'] }}</div><div class="s-sub"><i class="fa-solid fa-car"></i> Registrados</div><i class="fa-solid fa-car-side s-icon"></i></div>
    <div class="stat-card grad-teal"><div class="s-label">Órdenes</div><div class="s-value">{{ $stats['ordenes'] }}</div><div class="s-sub"><i class="fa-solid fa-clipboard-list"></i> Totales</div><i class="fa-solid fa-clipboard-list s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">Facturado</div><div class="s-value">S/ {{ number_format($stats['facturado'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-file-invoice-dollar"></i> Comprobantes</div><i class="fa-solid fa-sack-dollar s-icon"></i></div>
    <div class="stat-card grad-amber"><div class="s-label">Última visita</div><div class="s-value" style="font-size:20px;">{{ $stats['ultima_visita'] ? \Illuminate\Support\Carbon::parse($stats['ultima_visita'])->format('d/m/Y') : '—' }}</div><div class="s-sub"><i class="fa-solid fa-calendar"></i> Ingreso reciente</div><i class="fa-solid fa-calendar-check s-icon"></i></div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 1fr; margin-bottom:20px;">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Datos de contacto</h3></div>
        <div class="panel-body">
            <div class="detail-grid" style="grid-template-columns:1fr 1fr;">
                <div class="detail-item"><div class="di-label">Tipo</div><div class="di-value">{{ $cliente->tipo === 'empresa' ? 'Empresa' : 'Persona' }}</div></div>
                <div class="detail-item"><div class="di-label">Documento</div><div class="di-value">{{ $cliente->tipo_documento }} {{ $cliente->documento }}</div></div>
                <div class="detail-item"><div class="di-label">Teléfono</div><div class="di-value">{{ $cliente->telefono ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Correo</div><div class="di-value">{{ $cliente->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Ciudad</div><div class="di-value">{{ $cliente->ciudad ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Dirección</div><div class="di-value">{{ $cliente->direccion ?? '—' }}</div></div>
            </div>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Vehículos</h3><div class="sub">{{ $cliente->vehiculos->count() }} registrados</div></div></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Placa</th><th>Marca / Modelo</th><th>Año</th><th></th></tr></thead>
                <tbody>
                @forelse ($cliente->vehiculos as $v)
                    <tr>
                        <td><span class="badge-pill b-secondary" style="font-weight:700;">{{ $v->placa }}</span></td>
                        <td>{{ $v->marca }} {{ $v->modelo }}</td>
                        <td class="cell-muted">{{ $v->anio }}</td>
                        <td style="text-align:right;"><a href="{{ route('vehiculos.show', $v) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="cell-muted" style="text-align:center; padding:24px;">Sin vehículos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="lists-grid">
    <div class="panel" style="grid-column: span 2; min-width:0;">
        <div class="panel-head"><div><h3>Historial de órdenes</h3><div class="sub">{{ $ordenes->count() }} registros</div></div></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Orden</th><th>Vehículo</th><th>Fecha</th><th>Estado</th><th>Pago</th><th style="text-align:right;">Total</th><th></th></tr></thead>
                <tbody>
                @forelse ($ordenes as $o)
                    <tr>
                        <td class="cell-strong">{{ $o->numero }}</td>
                        <td>{{ $o->vehiculo->placa ?? '—' }}</td>
                        <td class="cell-muted">{{ optional($o->fecha_ingreso)->format('d/m/Y') }}</td>
                        <td><span class="badge-pill b-{{ $o->estadoColor() }}">{{ $estadosLabels[$o->estado] ?? $o->estado }}</span></td>
                        <td>
                            @php $pc = ['pendiente'=>'danger','parcial'=>'warning','pagado'=>'success'][$o->estado_pago] ?? 'secondary'; @endphp
                            <span class="badge-pill b-{{ $pc }}">{{ ucfirst($o->estado_pago) }}</span>
                        </td>
                        <td style="text-align:right;" class="cell-strong">S/ {{ number_format($o->total, 2) }}</td>
                        <td style="text-align:right;"><a href="{{ route('ordenes.show', $o) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="cell-muted" style="text-align:center; padding:30px;">Este cliente aún no tiene órdenes.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
