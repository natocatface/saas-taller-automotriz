@extends('layouts.app')
@section('title', 'Nueva guía de remisión')

@section('content')
<div class="page-head">
    <div>
        <h1>Nueva guía de remisión</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('guias.index') }}">Guías</a> · Nueva</div>
    </div>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ route('guias.store') }}">
    @csrf

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Motivo y traslado</h3><div class="sub">Catálogo 20 SUNAT</div></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Motivo de traslado *</label>
                    <select name="motivo_codigo" id="motivo_codigo" class="form-control" onchange="autoMotivo()">
                        @foreach ($motivos as $cod => $desc)
                            <option value="{{ $cod }}" data-desc="{{ $desc }}" {{ old('motivo_codigo') === $cod ? 'selected' : '' }}>{{ $cod }} · {{ $desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Descripción del motivo</label>
                    <input type="text" name="motivo_desc" id="motivo_desc" class="form-control" value="{{ old('motivo_desc') }}" maxlength="200">
                </div>
                <div>
                    <label class="form-label">Fecha de emisión *</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', now()->toDateString()) }}" required>
                </div>
                <div>
                    <label class="form-label">Fecha de traslado *</label>
                    <input type="date" name="fecha_traslado" class="form-control" value="{{ old('fecha_traslado', now()->toDateString()) }}" required>
                </div>
                <div>
                    <label class="form-label">Peso total *</label>
                    <input type="number" step="0.001" min="0" name="peso_total" class="form-control" value="{{ old('peso_total', 1) }}" required>
                </div>
                <div>
                    <label class="form-label">Unidad de peso *</label>
                    <input type="text" name="unidad_peso" class="form-control" value="{{ old('unidad_peso', 'KGM') }}" required>
                </div>
                <div>
                    <label class="form-label">N.º de bultos</label>
                    <input type="number" min="0" name="num_bultos" class="form-control" value="{{ old('num_bultos') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Puntos de partida y llegada</h3></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div><label class="form-label">Ubigeo partida *</label><input type="text" name="partida_ubigeo" class="form-control" value="{{ old('partida_ubigeo', $fe->ubigeo ?? '150101') }}" maxlength="6" required></div>
                <div><label class="form-label">Dirección partida *</label><input type="text" name="partida_direccion" class="form-control" value="{{ old('partida_direccion') }}" required></div>
                <div><label class="form-label">Ubigeo llegada *</label><input type="text" name="llegada_ubigeo" class="form-control" value="{{ old('llegada_ubigeo') }}" maxlength="6" required></div>
                <div><label class="form-label">Dirección llegada *</label><input type="text" name="llegada_direccion" class="form-control" value="{{ old('llegada_direccion') }}" required></div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Destinatario</h3></div></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Tipo de documento *</label>
                    <select name="destinatario_tipo_doc" class="form-control">
                        <option value="6" {{ old('destinatario_tipo_doc') === '6' ? 'selected' : '' }}>RUC</option>
                        <option value="1" {{ old('destinatario_tipo_doc') === '1' ? 'selected' : '' }}>DNI</option>
                        <option value="4" {{ old('destinatario_tipo_doc') === '4' ? 'selected' : '' }}>Carné de extranjería</option>
                    </select>
                </div>
                <div><label class="form-label">N.º documento *</label><input type="text" name="destinatario_num_doc" class="form-control" value="{{ old('destinatario_num_doc') }}" required></div>
                <div><label class="form-label">Nombre / razón social *</label><input type="text" name="destinatario_nombre" class="form-control" value="{{ old('destinatario_nombre') }}" required></div>
                <div>
                    <label class="form-label">Cliente (opcional)</label>
                    <select name="cliente_id" class="form-control">
                        <option value="">—</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><div><h3>Transporte</h3><div class="sub">Modalidad y datos del transportista</div></div></div>
        <div class="panel-body">
            <div class="form-grid" style="margin-bottom:14px;">
                <div>
                    <label class="form-label">Modalidad *</label>
                    <select name="modalidad" id="modalidad" class="form-control" onchange="toggleModalidad()">
                        @foreach ($modalidades as $cod => $desc)
                            <option value="{{ $cod }}" {{ old('modalidad', '02') === $cod ? 'selected' : '' }}>{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="privado" class="form-grid">
                <div><label class="form-label">Placa del vehículo *</label><input type="text" name="vehiculo_placa" class="form-control" value="{{ old('vehiculo_placa') }}" maxlength="15"></div>
                <div><label class="form-label">DNI del conductor *</label><input type="text" name="chofer_doc" class="form-control" value="{{ old('chofer_doc') }}"></div>
                <div><label class="form-label">Licencia de conducir *</label><input type="text" name="chofer_licencia" class="form-control" value="{{ old('chofer_licencia') }}"></div>
                <div><label class="form-label">Nombre del conductor *</label><input type="text" name="chofer_nombre" class="form-control" value="{{ old('chofer_nombre') }}"></div>
            </div>

            <div id="publico" class="form-grid" style="display:none;">
                <div><label class="form-label">RUC del transportista *</label><input type="text" name="transportista_doc" class="form-control" value="{{ old('transportista_doc') }}" maxlength="11"></div>
                <div><label class="form-label">Razón social del transportista *</label><input type="text" name="transportista_nombre" class="form-control" value="{{ old('transportista_nombre') }}"></div>
                <div><label class="form-label">Registro MTC</label><input type="text" name="transportista_mtc" class="form-control" value="{{ old('transportista_mtc') }}"></div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head">
            <div><h3>Bienes a trasladar</h3></div>
            <button type="button" class="btn btn-light" style="margin-left:auto;" onclick="addItem()"><i class="fa-solid fa-plus"></i> Agregar</button>
        </div>
        <div class="panel-body">
            <table class="items-table" style="width:100%;">
                <thead><tr>
                    <th style="text-align:left; font-size:12px; color:var(--text-muted); padding:6px;">Descripción</th>
                    <th style="width:110px; font-size:12px; color:var(--text-muted); padding:6px;">Cantidad</th>
                    <th style="width:110px; font-size:12px; color:var(--text-muted); padding:6px;">Unidad</th>
                    <th style="width:44px;"></th>
                </tr></thead>
                <tbody id="items-body">
                    {{-- filas por JS --}}
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:space-between; margin-bottom:30px;">
        <a href="{{ route('guias.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-truck-fast"></i> Emitir guía</button>
    </div>
</form>

@push('scripts')
<script>
    let idx = 0;
    function addItem(desc = '', cant = '1', uni = 'NIU') {
        const tr = document.createElement('tr');
        tr.innerHTML =
            '<td style="padding:4px;"><input type="text" name="items[' + idx + '][descripcion]" value="' + desc + '" placeholder="Descripción del bien" required></td>' +
            '<td style="padding:4px;"><input type="number" step="0.01" min="0.01" name="items[' + idx + '][cantidad]" value="' + cant + '" required></td>' +
            '<td style="padding:4px;"><input type="text" name="items[' + idx + '][unidad]" value="' + uni + '" required></td>' +
            '<td style="padding:4px; text-align:center;"><button type="button" class="ico-btn ico-danger" onclick="this.closest(\'tr\').remove()"><i class="fa-solid fa-trash"></i></button></td>';
        document.getElementById('items-body').appendChild(tr);
        idx++;
    }
    function toggleModalidad() {
        const priv = document.getElementById('modalidad').value === '02';
        document.getElementById('privado').style.display = priv ? '' : 'none';
        document.getElementById('publico').style.display = priv ? 'none' : '';
    }
    function autoMotivo() {
        const opt = document.getElementById('motivo_codigo').selectedOptions[0];
        const d = document.getElementById('motivo_desc');
        if (!d.value) d.value = opt.dataset.desc || '';
    }
    document.addEventListener('DOMContentLoaded', () => { addItem(); toggleModalidad(); autoMotivo(); });
</script>
@endpush
@endsection
