@extends('layouts.app')
@section('title', 'Nueva compra')

@section('content')
<div class="page-head">
    <div>
        <h1>Nueva compra</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('compras.index') }}">Compras</a> · Nueva</div>
    </div>
    <a href="{{ route('compras.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ route('compras.store') }}">
    @csrf
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3>Datos de la compra</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Proveedor *</label>
                    <select name="proveedor_id" class="form-control" required>
                        <option value="">Seleccionar proveedor...</option>
                        @foreach ($proveedores as $p)
                            <option value="{{ $p->id }}" @selected(old('proveedor_id') == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fecha *</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $compra->fecha) }}" required>
                </div>
                <div>
                    <label class="form-label">Tipo de documento *</label>
                    <select name="tipo_documento" class="form-control">
                        @foreach (['factura'=>'Factura','boleta'=>'Boleta','guia'=>'Guía'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('tipo_documento', $compra->tipo_documento)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">N° de documento</label>
                    <input type="text" name="documento_ref" class="form-control" value="{{ old('documento_ref') }}" placeholder="Ej. F001-1234">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Repuestos comprados</h3><div class="sub">Se sumarán al inventario al guardar</div></div></div>
        <div class="panel-body">
            <div class="table-wrap">
                <table class="items-table">
                    <thead><tr><th>Repuesto</th><th>Descripción</th><th class="col-num">Cant.</th><th class="col-money">P. Compra</th><th class="col-money">Subtotal</th><th class="col-x"></th></tr></thead>
                    <tbody id="itemsBody"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-light btn-add-row" onclick="addItem()"><i class="fa-solid fa-plus"></i> Agregar repuesto</button>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-body">
            <div class="totals-box">
                <div class="trow"><span>Subtotal</span><b id="tSubtotal">S/ 0.00</b></div>
                <div class="trow"><span>IGV ({{ $igv }}%)</span><b id="tIgv">S/ 0.00</b></div>
                <div class="trow grand"><span>Total</span><span id="tTotal">S/ 0.00</span></div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end; margin-bottom:30px;">
        <a href="{{ route('compras.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Registrar compra</button>
    </div>
</form>
@endsection

@php
    $repJs = $repuestos->map(fn ($r) => ['id' => $r->id, 'nombre' => $r->nombre, 'precio' => (float) $r->precio_compra])->values();
@endphp
@push('scripts')
<script>
    const REPUESTOS = @json($repJs);
    const IGV = {{ $igv }};
    let idx = 0;
    const money = n => 'S/ ' + (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);

    function addItem(data = {}) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="items[${i}][repuesto_id]" class="js-sel"><option value="">— Nuevo / sin catálogo —</option>${REPUESTOS.map(r=>`<option value="${r.id}">${r.nombre}</option>`).join('')}</select></td>
            <td><input type="text" name="items[${i}][descripcion]" class="js-desc" placeholder="Descripción"></td>
            <td class="col-num"><input type="number" name="items[${i}][cantidad]" class="js-cant" min="1" value="1"></td>
            <td class="col-money"><input type="number" step="0.01" name="items[${i}][precio]" class="js-precio" value="0"></td>
            <td class="col-money js-sub">S/ 0.00</td>
            <td class="col-x"><button type="button" class="ico-btn ico-danger js-del"><i class="fa-solid fa-xmark"></i></button></td>`;
        document.getElementById('itemsBody').appendChild(tr);
        const sel = tr.querySelector('.js-sel');
        sel.addEventListener('change', () => {
            const r = REPUESTOS.find(x => x.id == sel.value);
            if (r) { tr.querySelector('.js-desc').value = r.nombre; tr.querySelector('.js-precio').value = r.precio; }
            recalc();
        });
        tr.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
        tr.querySelector('.js-del').addEventListener('click', () => { tr.remove(); recalc(); });
        recalc();
    }

    function recalc() {
        let subtotal = 0;
        document.querySelectorAll('#itemsBody tr').forEach(tr => {
            const cant = parseFloat(tr.querySelector('.js-cant').value) || 0;
            const precio = parseFloat(tr.querySelector('.js-precio').value) || 0;
            const sub = cant * precio;
            tr.querySelector('.js-sub').textContent = money(sub);
            subtotal += sub;
        });
        const igv = subtotal * IGV / 100;
        document.getElementById('tSubtotal').textContent = money(subtotal);
        document.getElementById('tIgv').textContent = money(igv);
        document.getElementById('tTotal').textContent = money(subtotal + igv);
    }
    addItem();
</script>
@endpush
