@extends('layouts.app')
@section('title', 'Vehículo '.$vehiculo->placa)

@section('content')
@php $estadosLabels = \App\Models\Orden::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Vehículo {{ $vehiculo->placa }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('vehiculos.index') }}">Vehículos</a> · {{ $vehiculo->placa }}</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-light"><i class="fa-solid fa-pen"></i> Editar</a>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva orden</a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="stat-card grad-teal"><div class="s-label">Órdenes</div><div class="s-value">{{ $stats['total_ordenes'] }}</div><div class="s-sub"><i class="fa-solid fa-clipboard-list"></i> Historial de servicio</div><i class="fa-solid fa-clipboard-list s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">Gasto acumulado</div><div class="s-value">S/ {{ number_format($stats['gasto'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-coins"></i> En reparaciones</div><i class="fa-solid fa-sack-dollar s-icon"></i></div>
    <div class="stat-card grad-violet"><div class="s-label">Último kilometraje</div><div class="s-value">{{ number_format($stats['ultimo_km']) }}</div><div class="s-sub"><i class="fa-solid fa-gauge"></i> km registrados</div><i class="fa-solid fa-gauge-high s-icon"></i></div>
    <div class="stat-card grad-amber"><div class="s-label">Última visita</div><div class="s-value" style="font-size:20px;">{{ $stats['ultima_visita'] ? \Illuminate\Support\Carbon::parse($stats['ultima_visita'])->format('d/m/Y') : '—' }}</div><div class="s-sub"><i class="fa-solid fa-calendar"></i> Ingreso más reciente</div><i class="fa-solid fa-calendar-check s-icon"></i></div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Ficha del vehículo</h3></div>
        <div class="panel-body">
            <div class="detail-grid" style="grid-template-columns:1fr 1fr;">
                <div class="detail-item"><div class="di-label">Propietario</div><div class="di-value">{{ $vehiculo->cliente->nombre_completo ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Placa</div><div class="di-value">{{ $vehiculo->placa }}</div></div>
                <div class="detail-item"><div class="di-label">Marca</div><div class="di-value">{{ $vehiculo->marca ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Modelo</div><div class="di-value">{{ $vehiculo->modelo ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Año</div><div class="di-value">{{ $vehiculo->anio ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Color</div><div class="di-value">{{ $vehiculo->color ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Combustible</div><div class="di-value" style="text-transform:capitalize;">{{ $vehiculo->combustible }}</div></div>
                <div class="detail-item"><div class="di-label">Transmisión</div><div class="di-value" style="text-transform:capitalize;">{{ $vehiculo->transmision ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">VIN</div><div class="di-value">{{ $vehiculo->vin ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Motor</div><div class="di-value">{{ $vehiculo->motor ?? '—' }}</div></div>
            </div>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Historial de órdenes</h3><div class="sub">{{ $ordenes->count() }} registros</div></div></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Orden</th><th>Fecha</th><th>Mecánico</th><th>Estado</th><th style="text-align:right;">Total</th><th></th></tr></thead>
                <tbody>
                @forelse ($ordenes as $o)
                    <tr>
                        <td class="cell-strong">{{ $o->numero }}</td>
                        <td class="cell-muted">{{ optional($o->fecha_ingreso)->format('d/m/Y') }}</td>
                        <td class="cell-muted">{{ $o->mecanico->name ?? 'Sin asignar' }}</td>
                        <td><span class="badge-pill b-{{ $o->estadoColor() }}">{{ $estadosLabels[$o->estado] ?? $o->estado }}</span></td>
                        <td style="text-align:right;" class="cell-strong">S/ {{ number_format($o->total, 2) }}</td>
                        <td style="text-align:right;"><a href="{{ route('ordenes.show', $o) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:30px;">Este vehículo aún no tiene órdenes.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
