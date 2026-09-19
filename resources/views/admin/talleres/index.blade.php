@extends('layouts.admin')
@section('title', 'Talleres')

@section('content')
@php $estados = \App\Models\Taller::estados(); @endphp

<div class="page-head">
    <div>
        <h1>Talleres suscritos</h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · Talleres</div>
    </div>
    <a href="{{ route('admin.talleres.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo taller</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Listado</h3><div class="sub">{{ $talleres->total() }} talleres</div></div>
        <form method="GET" action="{{ route('admin.talleres.index') }}" style="display:flex; gap:10px; flex-wrap:wrap; margin-left:auto;">
            <div class="search" style="max-width:240px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Nombre, RUC o correo..."></div>
            <select name="estado" class="filter-select" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach ($estados as $k => $label)
                    <option value="{{ $k }}" @selected($estado === $k)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary" style="height:40px;">Filtrar</button>
            @if ($q || $estado)<a href="{{ route('admin.talleres.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Taller</th><th>Plan</th><th>Periodo</th><th>Estado</th><th>Vencimiento</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($talleres as $t)
                <tr>
                    <td>
                        <div class="cell-strong">{{ $t->nombre }}</div>
                        <div class="cell-muted">{{ $t->ciudad }} · {{ $t->email }}</div>
                    </td>
                    <td>{{ $t->plan->nombre ?? '—' }}</td>
                    <td class="cell-muted" style="text-transform:capitalize;">{{ $t->periodo }}</td>
                    <td><span class="badge-pill b-{{ $t->estadoColor() }}">{{ $estados[$t->estado] ?? $t->estado }}</span></td>
                    <td>
                        {{ optional($t->fecha_vencimiento)->format('d/m/Y') ?? '—' }}
                        @if ($t->vencido)<div><span class="badge-pill b-danger">Vencido</span></div>
                        @elseif ($t->por_vencer)<div><span class="badge-pill b-warning">{{ $t->dias_restantes }} días</span></div>@endif
                    </td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('admin.talleres.show', $t) }}" class="ico-btn" title="Ver"><i class="fa-solid fa-eye"></i></a>
                            <a href="{{ route('admin.talleres.edit', $t) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.talleres.destroy', $t) }}" onsubmit="return confirm('¿Eliminar el taller {{ $t->nombre }}?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron talleres.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $talleres])
</div>
@endsection
