@extends('layouts.app')
@section('title', $orden->exists ? 'Editar orden' : 'Nueva orden')

@section('content')
@php
    $estadosLabels = \App\Models\Orden::estados();
    $editando = $orden->exists;

    // Items iniciales (repuebla desde old() tras error de validación, o desde la orden en edición)
    $oldServ = old('servicios');
    if ($oldServ === null) {
        $oldServ = collect($itemsServicios)->map(fn ($i) => [
            'servicio_id' => $i->servicio_id, 'descripcion' => $i->descripcion,
            'cantidad' => $i->cantidad, 'precio' => (float) $i->precio,
        ])->values()->all();
    }
    $oldRep = old('repuestos');
    if ($oldRep === null) {
        $oldRep = collect($itemsRepuestos)->map(fn ($i) => [
            'repuesto_id' => $i->repuesto_id, 'descripcion' => $i->descripcion,
            'cantidad' => $i->cantidad, 'precio' => (float) $i->precio,
        ])->values()->all();
    }
@endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar orden '.$orden->numero : 'Nueva orden de servicio' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('ordenes.index') }}">Órdenes</a> · {{ $editando ? 'Editar' : 'Nueva' }}</div>
    </div>
    <a href="{{ route('ordenes.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@if ($errors->any())
    <div class="err-banner">
        <b><i class="fa-solid fa-circle-exclamation"></i> Revisa los siguientes campos:</b>
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ $editando ? route('ordenes.update', $orden) : route('ordenes.store') }}" id="ordenForm">
    @csrf
    @if ($editando) @method('PUT') @endif

    {{-- Datos generales --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3>Datos de la orden</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Cliente *</label>
                    <select name="cliente_id" id="clienteSel" class="form-control" required>
                        <option value="">Seleccionar cliente...</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" @selected(old('cliente_id', $orden->cliente_id) == $c->id)>{{ $c->nombre_completo }} @if($c->documento)· {{ $c->documento }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Vehículo *</label>
                    <select name="vehiculo_id" id="vehiculoSel" class="form-control" required>
                        <option value="">Seleccionar vehículo...</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Mecánico asignado</label>
                    <select name="mecanico_id" class="form-control">
                        <option value="">Sin asignar</option>
                        @foreach ($mecanicos as $m)
                            <option value="{{ $m->id }}" @selected(old('mecanico_id', $orden->mecanico_id) == $m->id)>{{ $m->name }} ({{ ucfirst($m->rol) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fecha de ingreso *</label>
                    <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', optional($orden->fecha_ingreso)->format('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Fecha de entrega estimada</label>
                    <input type="date" name="fecha_entrega" class="form-control" value="{{ old('fecha_entrega', optional($orden->fecha_entrega)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Kilometraje</label>
                    <input type="number" name="kilometraje" class="form-control" min="0" value="{{ old('kilometraje', $orden->kilometraje) }}" placeholder="Ej. 45000">
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        @foreach ($estadosLabels as $k => $label)
                            <option value="{{ $k }}" @selected(old('estado', $orden->estado) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Prioridad</label>
                    <select name="prioridad" class="form-control">
                        @foreach (['baja'=>'Baja','media'=>'Media','alta'=>'Alta'] as $k => $label)
                            <option value="{{ $k }}" @selected(old('prioridad', $orden->prioridad) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-grid" style="margin-top:16px; grid-template-columns:1fr 1fr;">
                <div>
                    <label class="form-label">Diagnóstico</label>
                    <textarea name="diagnostico" class="form-control" placeholder="Diagnóstico o trabajo solicitado...">{{ old('diagnostico', $orden->diagnostico) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" placeholder="Notas internas...">{{ old('observaciones', $orden->observaciones) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Servicios --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Servicios / mano de obra</h3><div class="sub">Agrega los trabajos a realizar</div></div></div>
        <div class="panel-body">
            <div class="table-wrap">
                <table class="items-table">
                    <thead><tr><th>Servicio</th><th>Descripción</th><th class="col-num">Cant.</th><th class="col-money">Precio</th><th class="col-money">Subtotal</th><th class="col-x"></th></tr></thead>
                    <tbody id="servBody"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-light btn-add-row" onclick="addServ()"><i class="fa-solid fa-plus"></i> Agregar servicio</button>
        </div>
    </div>

    {{-- Repuestos --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Repuestos / materiales</h3><div class="sub">Se descuentan del inventario al guardar</div></div></div>
        <div class="panel-body">
            <div class="table-wrap">
                <table class="items-table">
                    <thead><tr><th>Repuesto</th><th>Descripción</th><th class="col-num">Cant.</th><th class="col-money">Precio</th><th class="col-money">Subtotal</th><th class="col-x"></th></tr></thead>
                    <tbody id="repBody"></tbody>
                </table>
            </div>
            <button type="button" class="btn btn-light btn-add-row" onclick="addRep()"><i class="fa-solid fa-plus"></i> Agregar repuesto</button>
        </div>
    </div>

    {{-- Totales --}}
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-body">
            <div class="totals-box">
                <div class="trow"><span>Subtotal</span><b id="tSubtotal">S/ 0.00</b></div>
                <div class="trow"><span>Descuento</span>
                    <input type="number" name="descuento" id="descuento" step="0.01" min="0" value="{{ old('descuento', $orden->descuento ?? 0) }}" style="width:120px; text-align:right; height:34px; border:1px solid var(--border); border-radius:8px; padding:0 10px;">
                </div>
                <div class="trow"><span>IGV (18%)</span><b id="tIgv">S/ 0.00</b></div>
                <div class="trow grand"><span>Total</span><span id="tTotal">S/ 0.00</span></div>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end; margin-bottom:30px;">
        <a href="{{ route('ordenes.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Crear orden' }}</button>
    </div>
</form>
@endsection

@php
    $servJs = $servicios->map(fn ($s) => ['id' => $s->id, 'nombre' => $s->nombre, 'precio' => (float) $s->precio])->values();
    $repJs = $repuestos->map(fn ($r) => ['id' => $r->id, 'nombre' => $r->nombre, 'precio' => (float) $r->precio_venta, 'stock' => $r->stock])->values();
    $vehJs = $vehiculos->map(fn ($v) => ['id' => $v->id, 'placa' => $v->placa, 'marca' => $v->marca, 'modelo' => $v->modelo, 'cliente_id' => $v->cliente_id])->values();
@endphp
@push('scripts')
<script>
    const SERVICIOS = @json($servJs);
    const REPUESTOS = @json($repJs);
    const VEHICULOS = @json($vehJs);
    const OLD_SERV = @json($oldServ);
    const OLD_REP  = @json($oldRep);
    const SEL_VEH  = @json(old('vehiculo_id', $orden->vehiculo_id));

    let idx = 0;
    const money = n => 'S/ ' + (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);

    /* ---- Vehículos dependientes del cliente ---- */
    const clienteSel = document.getElementById('clienteSel');
    const vehiculoSel = document.getElementById('vehiculoSel');
    function cargarVehiculos(sel) {
        const cid = parseInt(clienteSel.value);
        vehiculoSel.innerHTML = '<option value="">Seleccionar vehículo...</option>';
        VEHICULOS.filter(v => v.cliente_id === cid).forEach(v => {
            const o = document.createElement('option');
            o.value = v.id; o.textContent = `${v.placa} · ${v.marca||''} ${v.modelo||''}`.trim();
            if (sel && v.id == sel) o.selected = true;
            vehiculoSel.appendChild(o);
        });
    }
    clienteSel.addEventListener('change', () => cargarVehiculos(null));

    /* ---- Filas de servicios ---- */
    function addServ(data = {}) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="servicios[${i}][servicio_id]" class="js-serv-sel"><option value="">— Personalizado —</option>${SERVICIOS.map(s=>`<option value="${s.id}">${s.nombre}</option>`).join('')}</select></td>
            <td><input type="text" name="servicios[${i}][descripcion]" class="js-desc" placeholder="Descripción del servicio"></td>
            <td class="col-num"><input type="number" name="servicios[${i}][cantidad]" class="js-cant" min="1" value="1"></td>
            <td class="col-money"><input type="number" step="0.01" name="servicios[${i}][precio]" class="js-precio" value="0"></td>
            <td class="col-money js-sub">S/ 0.00</td>
            <td class="col-x"><button type="button" class="ico-btn ico-danger js-del"><i class="fa-solid fa-xmark"></i></button></td>`;
        document.getElementById('servBody').appendChild(tr);
        const sel = tr.querySelector('.js-serv-sel');
        sel.addEventListener('change', () => {
            const s = SERVICIOS.find(x => x.id == sel.value);
            if (s) { tr.querySelector('.js-desc').value = s.nombre; tr.querySelector('.js-precio').value = s.precio; }
            recalc();
        });
        tr.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
        tr.querySelector('.js-del').addEventListener('click', () => { tr.remove(); recalc(); });
        if (data.servicio_id) sel.value = data.servicio_id;
        if (data.descripcion) tr.querySelector('.js-desc').value = data.descripcion;
        if (data.cantidad) tr.querySelector('.js-cant').value = data.cantidad;
        if (data.precio != null) tr.querySelector('.js-precio').value = data.precio;
        recalc();
    }

    /* ---- Filas de repuestos ---- */
    function addRep(data = {}) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="repuestos[${i}][repuesto_id]" class="js-rep-sel"><option value="">— Personalizado —</option>${REPUESTOS.map(r=>`<option value="${r.id}">${r.nombre} (stock: ${r.stock})</option>`).join('')}</select></td>
            <td><input type="text" name="repuestos[${i}][descripcion]" class="js-desc" placeholder="Descripción del repuesto"></td>
            <td class="col-num"><input type="number" name="repuestos[${i}][cantidad]" class="js-cant" min="1" value="1"></td>
            <td class="col-money"><input type="number" step="0.01" name="repuestos[${i}][precio]" class="js-precio" value="0"></td>
            <td class="col-money js-sub">S/ 0.00</td>
            <td class="col-x"><button type="button" class="ico-btn ico-danger js-del"><i class="fa-solid fa-xmark"></i></button></td>`;
        document.getElementById('repBody').appendChild(tr);
        const sel = tr.querySelector('.js-rep-sel');
        sel.addEventListener('change', () => {
            const r = REPUESTOS.find(x => x.id == sel.value);
            if (r) { tr.querySelector('.js-desc').value = r.nombre; tr.querySelector('.js-precio').value = r.precio; }
            recalc();
        });
        tr.querySelectorAll('input').forEach(inp => inp.addEventListener('input', recalc));
        tr.querySelector('.js-del').addEventListener('click', () => { tr.remove(); recalc(); });
        if (data.repuesto_id) sel.value = data.repuesto_id;
        if (data.descripcion) tr.querySelector('.js-desc').value = data.descripcion;
        if (data.cantidad) tr.querySelector('.js-cant').value = data.cantidad;
        if (data.precio != null) tr.querySelector('.js-precio').value = data.precio;
        recalc();
    }

    /* ---- Recalcular totales ---- */
    function recalc() {
        let subtotal = 0;
        document.querySelectorAll('#servBody tr, #repBody tr').forEach(tr => {
            const cant = parseFloat(tr.querySelector('.js-cant').value) || 0;
            const precio = parseFloat(tr.querySelector('.js-precio').value) || 0;
            const sub = cant * precio;
            tr.querySelector('.js-sub').textContent = money(sub);
            subtotal += sub;
        });
        const desc = parseFloat(document.getElementById('descuento').value) || 0;
        const base = Math.max(0, subtotal - desc);
        const igv = base * 0.18;
        document.getElementById('tSubtotal').textContent = money(subtotal);
        document.getElementById('tIgv').textContent = money(igv);
        document.getElementById('tTotal').textContent = money(base + igv);
    }
    document.getElementById('descuento').addEventListener('input', recalc);

    /* ---- Inicialización ---- */
    cargarVehiculos(SEL_VEH);
    if (OLD_SERV.length) OLD_SERV.forEach(addServ); else addServ();
    if (OLD_REP.length) OLD_REP.forEach(addRep);
    recalc();
</script>
@endpush
