@extends('layouts.app')
@section('title', 'Clientes')

@section('content')
<div class="page-head">
    <div>
        <h1>Clientes</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Clientes</div>
    </div>
    <div style="display:flex; gap:10px;">
        @if (\App\Support\Permisos::puede('import'))
            <a href="{{ route('import.index', ['tipo' => 'clientes']) }}" class="btn btn-light"><i class="fa-solid fa-file-import"></i> Importar</a>
        @endif
        <a href="{{ route('clientes.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo cliente</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Directorio de clientes</h3><div class="sub">{{ $clientes->total() }} registrados</div></div>
        <form method="GET" action="{{ route('clientes.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre, documento o teléfono..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('clientes.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Cliente</th><th>Documento</th><th>Teléfono</th><th>Correo</th><th>Vehículos</th><th>Órdenes</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($clientes as $c)
                @php $colors = ['bg-teal','bg-blue','bg-violet','bg-amber','bg-cyan','bg-rose']; @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:11px;">
                            <div class="mini-avatar {{ $colors[$c->id % 6] }}">{{ mb_strtoupper(mb_substr($c->nombre,0,1)) }}</div>
                            <div>
                                <div class="cell-strong">{{ $c->nombre_completo }}</div>
                                <div class="cell-muted">{{ $c->tipo === 'empresa' ? 'Empresa' : 'Persona' }} · {{ $c->ciudad }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="cell-muted">{{ $c->tipo_documento }} {{ $c->documento }}</td>
                    <td>{{ $c->telefono ?? '—' }}</td>
                    <td class="cell-muted">{{ $c->email ?? '—' }}</td>
                    <td><span class="badge-pill b-info">{{ $c->vehiculos_count }}</span></td>
                    <td><span class="badge-pill b-primary">{{ $c->ordenes_count }}</span></td>
                    <td><span class="badge-pill {{ $c->activo ? 'b-success' : 'b-secondary' }}">{{ $c->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('clientes.show', $c) }}" class="ico-btn" title="Ver ficha"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('clientes.edit', $c) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('clientes.destroy', $c) }}" onsubmit="return confirm('¿Eliminar a {{ $c->nombre_completo }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron clientes.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $clientes])
</div>
@endsection
