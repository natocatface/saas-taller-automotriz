@extends('layouts.app')
@section('title', 'Configuración')

@section('content')
<div class="page-head">
    <div>
        <h1>Configuración</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Configuración</div>
    </div>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ route('configuracion.update') }}">
    @csrf @method('PUT')
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Datos de la empresa</h3><div class="sub">Aparecen en los comprobantes y órdenes impresas</div></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Nombre de la empresa *</label>
                    <input type="text" name="empresa" class="form-control" value="{{ old('empresa', $config->empresa) }}" required>
                </div>
                <div>
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $config->ruc) }}">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $config->telefono) }}">
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $config->email) }}">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $config->direccion) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Parámetros de facturación</h3><div class="sub">Impuesto y series de comprobantes</div></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Moneda *</label>
                    <input type="text" name="moneda" class="form-control" value="{{ old('moneda', $config->moneda) }}" required>
                </div>
                <div>
                    <label class="form-label">IGV (%) *</label>
                    <input type="number" step="0.01" name="igv" class="form-control" value="{{ old('igv', $config->igv) }}" min="0" max="100" required>
                </div>
                <div>
                    <label class="form-label">Serie de boleta *</label>
                    <input type="text" name="serie_boleta" class="form-control" value="{{ old('serie_boleta', $config->serie_boleta) }}" required>
                </div>
                <div>
                    <label class="form-label">Serie de factura *</label>
                    <input type="text" name="serie_factura" class="form-control" value="{{ old('serie_factura', $config->serie_factura) }}" required>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end; margin-bottom:30px;">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar configuración</button>
    </div>
</form>
@endsection
