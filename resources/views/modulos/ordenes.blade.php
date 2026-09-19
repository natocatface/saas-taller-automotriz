@extends('layouts.app')
@section('title', 'Órdenes de servicio')

@section('content')
@php $estadosLabels = \App\Models\Orden::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Órdenes de servicio</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Órdenes</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('export.ordenes') }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Exportar Excel</a>
        <a href="{{ route('ordenes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva orden</a>
    </div>
</div>

<div class="grid kpi-grid" style="grid-template-columns:repeat(4,1fr); margin-bottom:20px;">
    <div class="kpi"><div class="kpi-icon bg-teal"><i class="fa-solid fa-clipboard-list"></i></div><div class="kpi-value">{{ $resumen['total'] }}</div><div class="kpi-label">Total órdenes</div></div>
    <div class="kpi"><div class="kpi-icon bg-amber"><i class="fa-solid fa-spinner"></i></div><div class="kpi-value">{{ $resumen['abiertas'] }}</div><div class="kpi-label">Abiertas</div></div>
    <div class="kpi"><div class="kpi-icon bg-blue"><i class="fa-solid fa-flag-checkered"></i></div><div class="kpi-value">{{ $resumen['terminadas'] }}</div><div class="kpi-label">Terminadas</div></div>
    <div class="kpi"><div class="kpi-icon bg-violet"><i class="fa-solid fa-handshake"></i></div><div class="kpi-value">{{ $resumen['entregadas'] }}</div><div class="kpi-label">Entregadas</div></div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Listado de órdenes</h3><div class="sub">{{ $ordenes->total() }} registros</div></div>
        <form method="GET" action="{{ route('ordenes.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; margin-left:auto;">
            <div class="search" style="max-width:260px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="{{ $q }}" placeholder="N° orden, cliente o placa...">
            </div>
            <select name="estado" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach ($estadosLabels as $k => $label)
                    <option value="{{ $k }}" @selected($estado === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="height:40px;">Filtrar</button>
            @if ($q || $estado)
                <a href="{{ route('ordenes.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>
            @endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>N° Orden</th><th>Cliente</th><th>Vehículo</th><th>Ingreso</th><th>Mecánico</th><th>Estado</th><th>Pago</th><th>Total</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($ordenes as $o)
                <tr>
                    <td class="cell-strong">{{ $o->numero }}</td>
                    <td>{{ $o->cliente->nombre_completo ?? '—' }}</td>
                    <td>{{ $o->vehiculo->placa ?? '—' }} <div class="cell-muted">{{ $o->vehiculo->marca ?? '' }} {{ $o->vehiculo->modelo ?? '' }}</div></td>
                    <td class="cell-muted">{{ optional($o->fecha_ingreso)->format('d/m/Y') }}</td>
                    <td class="cell-muted">{{ $o->mecanico->name ?? 'Sin asignar' }}</td>
                    <td><span class="badge-pill b-{{ $o->estadoColor() }}">{{ $estadosLabels[$o->estado] ?? $o->estado }}</span></td>
                    <td>
                        @php $pc = ['pendiente'=>'danger','parcial'=>'warning','pagado'=>'success'][$o->estado_pago] ?? 'secondary'; @endphp
                        <span class="badge-pill b-{{ $pc }}">{{ ucfirst($o->estado_pago) }}</span>
                    </td>
                    <td class="cell-strong">S/ {{ number_format($o->total, 2) }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('ordenes.show', $o) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('ordenes.edit', $o) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <a href="{{ route('ordenes.imprimir', $o) }}" target="_blank" class="ico-btn" title="Imprimir"><i class="fa-solid fa-print"></i></a>
                            <form method="POST" action="{{ route('ordenes.destroy', $o) }}" onsubmit="return confirm('¿Eliminar la orden {{ $o->numero }}? Se devolverá el stock de repuestos.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron órdenes con esos criterios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $ordenes])
</div>
@endsection
