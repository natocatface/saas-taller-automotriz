@extends('layouts.app')
@section('title', 'Personal')

@section('content')
<div class="page-head">
    <div>
        <h1>Personal</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Personal</div>
    </div>
    <a href="{{ route('personal.create') }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i> Nuevo usuario</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Equipo de trabajo</h3><div class="sub">{{ $personal->total() }} usuarios</div></div>
        <form method="GET" action="{{ route('personal.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre, correo o rol..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('personal.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Usuario</th><th>Correo</th><th>Rol</th><th>Teléfono</th><th>Órdenes atendidas</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($personal as $u)
                @php
                    $colors = ['bg-teal','bg-blue','bg-violet','bg-amber','bg-cyan','bg-rose'];
                    $ini = collect(explode(' ', $u->name))->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
                    $rc = ['admin'=>'b-danger','gerente'=>'b-primary','mecanico'=>'b-info','empleado'=>'b-secondary'][$u->rol] ?? 'b-secondary';
                @endphp
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:11px;">
                            <div class="mini-avatar {{ $colors[$u->id % 6] }}">{{ mb_strtoupper($ini) }}</div>
                            <div class="cell-strong">{{ $u->name }}</div>
                        </div>
                    </td>
                    <td class="cell-muted">{{ $u->email }}</td>
                    <td><span class="badge-pill {{ $rc }}" style="text-transform:capitalize;">{{ $u->rol }}</span></td>
                    <td>{{ $u->telefono ?? '—' }}</td>
                    <td><span class="badge-pill b-primary">{{ $u->ordenes_count }}</span></td>
                    <td><span class="badge-pill {{ $u->activo ? 'b-success' : 'b-secondary' }}">{{ $u->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('personal.edit', $u) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('personal.destroy', $u) }}" onsubmit="return confirm('¿Eliminar al usuario {{ $u->name }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron usuarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $personal])
</div>
@endsection
