@extends('layouts.app')
@section('title', 'Emitir comprobante')

@section('content')
<div class="page-head">
    <div>
        <h1>Emitir comprobante</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('facturacion.index') }}">Facturación</a> · Emitir</div>
    </div>
    <a href="{{ route('facturacion.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

@if ($ordenes->isEmpty())
    <div class="panel"><div class="placeholder-box">
        <div class="ph-icon"><i class="fa-solid fa-circle-check"></i></div>
        <h2>No hay órdenes pendientes de facturar</h2>
        <p>Todas las órdenes con importe ya tienen su comprobante, o aún no hay órdenes registradas.</p>
        <a href="{{ route('ordenes.index') }}" class="btn btn-primary" style="margin-top:16px;">Ir a órdenes</a>
    </div></div>
@else
<form method="POST" action="{{ route('facturacion.store') }}">
    @csrf
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3>Datos del comprobante</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Orden a facturar *</label>
                    <select name="orden_id" id="ordenSel" class="form-control" required>
                        <option value="">Seleccionar orden...</option>
                        @foreach ($ordenes as $o)
                            <option value="{{ $o->id }}" data-total="{{ $o->total }}" data-cliente="{{ $o->cliente->nombre_completo ?? '' }}" @selected(old('orden_id') == $o->id)>
                                {{ $o->numero }} · {{ $o->cliente->nombre_completo ?? '' }} · S/ {{ number_format($o->total,2) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipo de comprobante *</label>
                    <select name="tipo" class="form-control">
                        <option value="boleta" @selected(old('tipo')==='boleta')>Boleta</option>
                        <option value="factura" @selected(old('tipo')==='factura')>Factura</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Método de pago *</label>
                    <select name="metodo_pago" class="form-control">
                        @foreach (['efectivo'=>'Efectivo','tarjeta'=>'Tarjeta','transferencia'=>'Transferencia','yape'=>'Yape / Plin'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('metodo_pago')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="totals-box" style="margin-top:20px;">
                <div class="trow"><span>Cliente</span><b id="pCliente">—</b></div>
                <div class="trow grand"><span>Total a cobrar</span><span id="pTotal">S/ 0.00</span></div>
            </div>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin-bottom:30px;">
        <a href="{{ route('facturacion.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-file-invoice-dollar"></i> Emitir comprobante</button>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script>
    const sel = document.getElementById('ordenSel');
    if (sel) {
        const money = n => 'S/ ' + (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);
        sel.addEventListener('change', () => {
            const opt = sel.options[sel.selectedIndex];
            document.getElementById('pTotal').textContent = money(parseFloat(opt.dataset.total || 0));
            document.getElementById('pCliente').textContent = opt.dataset.cliente || '—';
        });
    }
</script>
@endpush
