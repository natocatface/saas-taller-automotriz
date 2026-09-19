@extends('layouts.app')
@section('title', 'Repuestos')

@section('content')
<div class="page-head">
    <div>
        <h1>Repuestos e inventario</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Repuestos</div>
    </div>
    <div style="display:flex; gap:10px;">
        @if (\App\Support\Permisos::puede('import'))
            <a href="{{ route('import.index', ['tipo' => 'repuestos']) }}" class="btn btn-light"><i class="fa-solid fa-file-import"></i> Importar</a>
        @endif
        <a href="{{ route('export.inventario') }}" class="btn btn-light"><i class="fa-solid fa-file-excel"></i> Exportar Excel</a>
        <a href="{{ route('repuestos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo repuesto</a>
    </div>
</div>

<div class="grid kpi-grid" style="grid-template-columns:repeat(3,1fr); margin-bottom:20px;">
    <div class="kpi"><div class="kpi-icon bg-teal"><i class="fa-solid fa-boxes-stacked"></i></div><div class="kpi-value">{{ $resumen['items'] }}</div><div class="kpi-label">Ítems en catálogo</div></div>
    <div class="kpi"><div class="kpi-icon bg-rose"><i class="fa-solid fa-triangle-exclamation"></i></div><div class="kpi-value">{{ $resumen['stock_bajo'] }}</div><div class="kpi-label">Con stock bajo</div></div>
    <div class="kpi"><div class="kpi-icon bg-blue"><i class="fa-solid fa-warehouse"></i></div><div class="kpi-value">S/ {{ number_format($resumen['valor'], 0) }}</div><div class="kpi-label">Valor de inventario</div></div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Inventario</h3><div class="sub">{{ $repuestos->total() }} productos</div></div>
        <form method="GET" action="{{ route('repuestos.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre, código o categoría..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('repuestos.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Código</th><th>Repuesto</th><th>Categoría</th><th>Proveedor</th><th>P. Compra</th><th>P. Venta</th><th>Stock</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($repuestos as $r)
                <tr>
                    <td class="cell-muted">{{ $r->codigo }}</td>
                    <td class="cell-strong">{{ $r->nombre }}</td>
                    <td class="cell-muted">{{ $r->categoria }}</td>
                    <td class="cell-muted">{{ $r->proveedor->nombre ?? '—' }}</td>
                    <td class="cell-muted">S/ {{ number_format($r->precio_compra, 2) }}</td>
                    <td class="cell-strong">S/ {{ number_format($r->precio_venta, 2) }}</td>
                    <td class="cell-strong">{{ $r->stock }} <span class="cell-muted">/ {{ $r->stock_minimo }}</span></td>
                    <td>
                        @if ($r->stock <= $r->stock_minimo)
                            <span class="badge-pill b-danger">Stock bajo</span>
                        @else
                            <span class="badge-pill b-success">Disponible</span>
                        @endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('repuestos.edit', $r) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('repuestos.destroy', $r) }}" onsubmit="return confirm('¿Eliminar el repuesto {{ $r->nombre }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron repuestos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $repuestos])
</div>
@endsection
