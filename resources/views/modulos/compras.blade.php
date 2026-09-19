@extends('layouts.app')
@section('title', 'Compras')

@section('content')
<div class="page-head">
    <div>
        <h1>Compras</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Compras</div>
    </div>
    <a href="{{ route('compras.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nueva compra</a>
</div>

<div class="grid kpi-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:20px;">
    <div class="kpi"><div class="kpi-icon bg-teal"><i class="fa-solid fa-cart-shopping"></i></div><div class="kpi-value">{{ $resumen['total'] }}</div><div class="kpi-label">Compras registradas</div></div>
    <div class="kpi"><div class="kpi-icon bg-blue"><i class="fa-solid fa-money-bill-wave"></i></div><div class="kpi-value">S/ {{ number_format($resumen['mes'], 0) }}</div><div class="kpi-label">Comprado este mes</div></div>
    <div class="kpi"><div class="kpi-icon bg-violet"><i class="fa-solid fa-truck-field"></i></div><div class="kpi-value">{{ $resumen['proveedores'] }}</div><div class="kpi-label">Proveedores activos</div></div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Historial de compras</h3><div class="sub">{{ $compras->total() }} registros</div></div>
        <form method="GET" action="{{ route('compras.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="N° compra o proveedor..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('compras.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>N° Compra</th><th>Proveedor</th><th>Fecha</th><th>Documento</th><th>Total</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($compras as $c)
                <tr>
                    <td class="cell-strong">{{ $c->numero }}</td>
                    <td>{{ $c->proveedor->nombre ?? '—' }}</td>
                    <td class="cell-muted">{{ optional($c->fecha)->format('d/m/Y') }}</td>
                    <td class="cell-muted" style="text-transform:capitalize;">{{ $c->tipo_documento }} {{ $c->documento_ref }}</td>
                    <td class="cell-strong">S/ {{ number_format($c->total, 2) }}</td>
                    <td><span class="badge-pill {{ $c->estado === 'registrada' ? 'b-success' : 'b-danger' }}">{{ ucfirst($c->estado) }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('compras.show', $c) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <form method="POST" action="{{ route('compras.destroy', $c) }}" onsubmit="return confirm('¿Anular la compra {{ $c->numero }}? Se revertirá el stock ingresado.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Anular"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="cell-muted" style="text-align:center; padding:34px;">No hay compras registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $compras])
</div>
@endsection
