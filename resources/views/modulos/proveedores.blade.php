@extends('layouts.app')
@section('title', 'Proveedores')

@section('content')
<div class="page-head">
    <div>
        <h1>Proveedores</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Proveedores</div>
    </div>
    <a href="{{ route('proveedores.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo proveedor</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Directorio de proveedores</h3><div class="sub">{{ $proveedores->total() }} proveedores</div></div>
        <form method="GET" action="{{ route('proveedores.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre, RUC o contacto..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('proveedores.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Proveedor</th><th>RUC</th><th>Contacto</th><th>Teléfono</th><th>Correo</th><th>Repuestos</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($proveedores as $p)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:11px;">
                            <div class="mini-avatar bg-blue"><i class="fa-solid fa-truck-field"></i></div>
                            <div class="cell-strong">{{ $p->nombre }}</div>
                        </div>
                    </td>
                    <td class="cell-muted">{{ $p->ruc ?? '—' }}</td>
                    <td>{{ $p->contacto ?? '—' }}</td>
                    <td>{{ $p->telefono ?? '—' }}</td>
                    <td class="cell-muted">{{ $p->email ?? '—' }}</td>
                    <td><span class="badge-pill b-primary">{{ $p->repuestos_count }}</span></td>
                    <td><span class="badge-pill {{ $p->activo ? 'b-success' : 'b-secondary' }}">{{ $p->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('proveedores.edit', $p) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('proveedores.destroy', $p) }}" onsubmit="return confirm('¿Eliminar al proveedor {{ $p->nombre }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron proveedores.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $proveedores])
</div>
@endsection
