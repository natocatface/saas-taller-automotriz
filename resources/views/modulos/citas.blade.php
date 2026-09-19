@extends('layouts.app')
@section('title', 'Citas')

@section('content')
<div class="page-head">
    <div>
        <h1>Citas / Agenda</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Citas</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('citas.calendario') }}" class="btn btn-light"><i class="fa-solid fa-calendar-days"></i> Vista calendario</a>
        <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Agendar cita</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Agenda de citas</h3><div class="sub">{{ $citas->total() }} citas</div></div>
        <form method="GET" action="{{ route('citas.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Título o cliente..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('citas.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Fecha y hora</th><th>Cliente</th><th>Vehículo</th><th>Motivo</th><th>Mecánico</th><th>Duración</th><th>Estado</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($citas as $c)
                @php $ec = ['pendiente'=>'warning','confirmada'=>'info','atendida'=>'success','cancelada'=>'danger'][$c->estado] ?? 'secondary'; @endphp
                <tr>
                    <td class="cell-strong">{{ $c->fecha_hora->translatedFormat('d M Y') }}<div class="cell-muted">{{ $c->fecha_hora->format('h:i A') }}</div></td>
                    <td>{{ $c->cliente->nombre_completo ?? '—' }}</td>
                    <td>{{ $c->vehiculo->placa ?? '—' }}</td>
                    <td class="cell-muted">{{ $c->titulo }}</td>
                    <td class="cell-muted">{{ $c->mecanico->name ?? 'Sin asignar' }}</td>
                    <td class="cell-muted">{{ $c->duracion_min }} min</td>
                    <td><span class="badge-pill b-{{ $ec }}">{{ ucfirst($c->estado) }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('citas.edit', $c) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('citas.destroy', $c) }}" onsubmit="return confirm('¿Eliminar esta cita?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="cell-muted" style="text-align:center; padding:34px;">No se encontraron citas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $citas])
</div>
@endsection
