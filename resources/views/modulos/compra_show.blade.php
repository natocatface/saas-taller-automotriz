@extends('layouts.app')
@section('title', 'Compra '.$compra->numero)

@section('content')
<div class="page-head">
    <div>
        <h1>Compra {{ $compra->numero }}
            <span class="badge-pill {{ $compra->estado === 'registrada' ? 'b-success' : 'b-danger' }}" style="vertical-align:middle; margin-left:8px;">{{ ucfirst($compra->estado) }}</span>
        </h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('compras.index') }}">Compras</a> · {{ $compra->numero }}</div>
    </div>
    <a href="{{ route('compras.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

<div class="panel" style="margin-bottom:20px;">
    <div class="panel-head"><h3>Información</h3></div>
    <div class="panel-body">
        <div class="detail-grid">
            <div class="detail-item"><div class="di-label">Proveedor</div><div class="di-value">{{ $compra->proveedor->nombre ?? '—' }}</div></div>
            <div class="detail-item"><div class="di-label">Fecha</div><div class="di-value">{{ optional($compra->fecha)->format('d/m/Y') }}</div></div>
            <div class="detail-item"><div class="di-label">Documento</div><div class="di-value" style="text-transform:capitalize;">{{ $compra->tipo_documento }} {{ $compra->documento_ref }}</div></div>
            <div class="detail-item"><div class="di-label">Registrado por</div><div class="di-value">{{ $compra->user->name ?? '—' }}</div></div>
        </div>
        @if ($compra->observaciones)
            <div style="margin-top:16px;"><div class="di-label" style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Observaciones</div><div>{{ $compra->observaciones }}</div></div>
        @endif
    </div>
</div>

<div class="panel">
    <div class="panel-head"><h3>Detalle</h3></div>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Repuesto</th><th>Cant.</th><th style="text-align:right;">P. Compra</th><th style="text-align:right;">Subtotal</th></tr></thead>
            <tbody>
            @foreach ($compra->detalles as $d)
                <tr><td>{{ $d->descripcion }}</td><td>{{ $d->cantidad }}</td><td style="text-align:right;">S/ {{ number_format($d->precio,2) }}</td><td style="text-align:right;" class="cell-strong">S/ {{ number_format($d->subtotal,2) }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="panel-body">
        <div class="totals-box">
            <div class="trow"><span>Subtotal</span><b>S/ {{ number_format($compra->subtotal,2) }}</b></div>
            <div class="trow"><span>IGV</span><b>S/ {{ number_format($compra->impuesto,2) }}</b></div>
            <div class="trow grand"><span>Total</span><span>S/ {{ number_format($compra->total,2) }}</span></div>
        </div>
    </div>
</div>
@endsection
