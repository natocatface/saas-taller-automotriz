@extends('layouts.app')
@section('title', 'Nota de crédito / débito')

@section('content')
<div class="page-head">
    <div>
        <h1>Emitir nota electrónica</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('facturacion.index') }}">Facturación</a> · Nota</div>
    </div>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ route('facturacion.nota.store', $afectado) }}">
    @csrf

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Comprobante afectado</h3><div class="sub">La nota modifica este documento ante SUNAT</div></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div><label class="form-label">Documento</label><input type="text" class="form-control" value="{{ $afectado->tipo_label }} {{ $afectado->serie_numero }}" disabled></div>
                <div><label class="form-label">Cliente</label><input type="text" class="form-control" value="{{ $afectado->cliente->nombre_completo ?? 'Varios' }}" disabled></div>
                <div><label class="form-label">Total del comprobante</label><input type="text" class="form-control" value="S/ {{ number_format($afectado->total, 2) }}" disabled></div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Datos de la nota</h3><div class="sub">Tipo, motivo y monto</div></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Tipo de nota *</label>
                    <select name="tipo" id="tipo" class="form-control" onchange="cambiarMotivos()">
                        <option value="nota_credito" {{ old('tipo') === 'nota_debito' ? '' : 'selected' }}>Nota de crédito (devolución / anulación)</option>
                        <option value="nota_debito" {{ old('tipo') === 'nota_debito' ? 'selected' : '' }}>Nota de débito (cargo adicional)</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Motivo *</label>
                    <select name="motivo_codigo" id="motivo_codigo" class="form-control" onchange="autollenarMotivo()">
                        {{-- Poblado por JS según el tipo --}}
                    </select>
                </div>
                <div>
                    <label class="form-label">Descripción del motivo *</label>
                    <input type="text" name="motivo_desc" id="motivo_desc" class="form-control" value="{{ old('motivo_desc') }}" maxlength="200" required>
                </div>
                <div>
                    <label class="form-label">Monto (S/) *</label>
                    <input type="number" step="0.01" min="0.01" max="{{ $afectado->total }}" name="monto" class="form-control" value="{{ old('monto', number_format($afectado->total, 2, '.', '')) }}" required>
                    <small class="cell-muted" style="font-size:12px;">Máximo S/ {{ number_format($afectado->total, 2) }} (total del comprobante).</small>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:space-between; margin-bottom:30px;">
        <a href="{{ route('facturacion.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-file-circle-plus"></i> Emitir nota</button>
    </div>
</form>

@push('scripts')
<script>
    const MOTIVOS = {
        nota_credito: @json($motivosCredito),
        nota_debito: @json($motivosDebito),
    };
    function cambiarMotivos() {
        const tipo = document.getElementById('tipo').value;
        const sel = document.getElementById('motivo_codigo');
        sel.innerHTML = '';
        Object.entries(MOTIVOS[tipo]).forEach(([cod, desc]) => {
            const o = document.createElement('option');
            o.value = cod; o.textContent = cod + ' · ' + desc;
            sel.appendChild(o);
        });
        autollenarMotivo();
    }
    function autollenarMotivo() {
        const tipo = document.getElementById('tipo').value;
        const cod = document.getElementById('motivo_codigo').value;
        document.getElementById('motivo_desc').value = MOTIVOS[tipo][cod] || '';
    }
    document.addEventListener('DOMContentLoaded', cambiarMotivos);
</script>
@endpush
@endsection
