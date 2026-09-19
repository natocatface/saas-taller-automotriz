@extends('layouts.app')
@section('title', 'Vehículos')

@section('content')
<div class="page-head">
    <div>
        <h1>Vehículos</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Vehículos</div>
    </div>
    <a href="{{ route('vehiculos.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo vehículo</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Parque vehicular</h3><div class="sub">{{ $vehiculos->total() }} vehículos</div></div>
        <form method="GET" action="{{ route('vehiculos.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Placa, marca o modelo..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('vehiculos.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Placa</th><th>Marca / Modelo</th><th>Año</th><th>Color</th><th>Combustible</th><th>Kilometraje</th><th>Propietario</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($vehiculos as $v)
                <tr>
                    <td><span class="badge-pill b-secondary" style="font-weight:700; letter-spacing:.5px;">{{ $v->placa }}</span></td>
                    <td class="cell-strong">{{ $v->marca }} {{ $v->modelo }}</td>
                    <td class="cell-muted">{{ $v->anio }}</td>
                    <td>{{ $v->color }}</td>
                    <td class="cell-muted" style="text-transform:capitalize;">{{ $v->combustible }}</td>
                    <td class="cell-muted">{{ number_format($v->kilometraje) }} km</td>
                    <td>{{ $v->cliente->nombre_completo ?? '—' }}</td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('vehiculos.show', $v) }}" class="ico-btn" title="Ver ficha"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('vehiculos.edit', $v) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('vehiculos.destroy', $v) }}" onsubmit="return confirm('¿Eliminar el vehículo {{ $v->placa }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron vehículos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $vehiculos])
</div>
@endsection
