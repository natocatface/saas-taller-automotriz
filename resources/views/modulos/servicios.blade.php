@extends('layouts.app')
@section('title', 'Servicios')

@section('content')
<div class="page-head">
    <div>
        <h1>Catálogo de servicios</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Servicios</div>
    </div>
    <a href="{{ route('servicios.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo servicio</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Servicios y mano de obra</h3><div class="sub">{{ $servicios->total() }} servicios</div></div>
        <form method="GET" action="{{ route('servicios.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre o categoría..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('servicios.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Código</th><th>Servicio</th><th>Categoría</th><th>Duración</th><th>Precio</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($servicios as $s)
                <tr>
                    <td class="cell-muted">{{ $s->codigo }}</td>
                    <td class="cell-strong">{{ $s->nombre }}</td>
                    <td><span class="badge-pill b-info">{{ $s->categoria }}</span></td>
                    <td class="cell-muted">{{ $s->duracion_min }} min</td>
                    <td class="cell-strong">S/ {{ number_format($s->precio, 2) }}</td>
                    <td><span class="badge-pill {{ $s->activo ? 'b-success' : 'b-secondary' }}">{{ $s->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('servicios.edit', $s) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('servicios.destroy', $s) }}" onsubmit="return confirm('¿Eliminar el servicio {{ $s->nombre }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron servicios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $servicios])
</div>
@endsection
