@extends('layouts.app')
@section('title', 'Guías de Remisión')

@section('content')
<div class="page-head">
    <div>
        <h1>Guías de Remisión</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Guías de remisión electrónica</div>
    </div>
    <a href="{{ route('guias.create') }}" class="btn btn-primary"><i class="fa-solid fa-truck-fast"></i> Nueva guía</a>
</div>

<div class="panel">
    <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
        <div><h3>Guías emitidas</h3><div class="sub">{{ $guias->total() }} registros</div></div>
        <form method="GET" action="{{ route('guias.index') }}" style="display:flex; gap:10px; margin-left:auto;">
            <div class="search" style="max-width:260px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Serie, número o destinatario..."></div>
            <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            @if ($q)<a href="{{ route('guias.index') }}" class="btn btn-light" style="height:40px;">Limpiar</a>@endif
        </form>
    </div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Guía</th><th>Fecha traslado</th><th>Destinatario</th><th>Modalidad</th><th>Estado SUNAT</th><th style="text-align:right;">Acciones</th></tr></thead>
            <tbody>
            @forelse ($guias as $g)
                @php $mapa = ['aceptado'=>'b-success','observado'=>'b-warning','rechazado'=>'b-danger','error'=>'b-danger','anulando'=>'b-warning','pendiente'=>'b-info']; @endphp
                <tr>
                    <td class="cell-strong">{{ $g->serie_numero }}</td>
                    <td class="cell-muted">{{ optional($g->fecha_traslado)->format('d/m/Y') }}</td>
                    <td>{{ $g->destinatario_nombre }}</td>
                    <td><span class="badge-pill b-info">{{ $g->esPrivado() ? 'Privado' : 'Público' }}</span></td>
                    <td><span class="badge-pill {{ $mapa[$g->estado_sunat] ?? 'b-info' }}" title="{{ $g->sunat_observaciones }}">{{ ucfirst($g->estado_sunat ?? 'pendiente') }}</span></td>
                    <td>
                        <div class="row-actions">
                            <a href="{{ route('guias.imprimir', $g) }}" target="_blank" class="ico-btn" title="Imprimir"><i class="fa-solid fa-print"></i></a>
                            @if (in_array($g->estado_sunat, ['pendiente','anulando','error']) && $g->sunat_ticket)
                                <form method="POST" action="{{ route('guias.consultar', $g) }}">
                                    @csrf
                                    <button type="submit" class="ico-btn" title="Consultar estado en SUNAT"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:34px;">No hay guías de remisión.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @include('partials.pager', ['paginator' => $guias])
</div>
@endsection
