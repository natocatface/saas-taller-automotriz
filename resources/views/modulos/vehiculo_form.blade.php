@extends('layouts.app')
@section('title', $vehiculo->exists ? 'Editar vehículo' : 'Nuevo vehículo')

@section('content')
@php $editando = $vehiculo->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar vehículo' : 'Nuevo vehículo' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('vehiculos.index') }}">Vehículos</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('vehiculos.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('vehiculos.update', $vehiculo) : route('vehiculos.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del vehículo</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Propietario *</label>
                    <select name="cliente_id" class="form-control" required>
                        <option value="">Seleccionar cliente...</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" @selected(old('cliente_id', $vehiculo->cliente_id) == $c->id)>{{ $c->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Placa *</label>
                    <input type="text" name="placa" class="form-control" value="{{ old('placa', $vehiculo->placa) }}" required style="text-transform:uppercase;">
                </div>
                <div>
                    <label class="form-label">Marca</label>
                    <input type="text" name="marca" class="form-control" value="{{ old('marca', $vehiculo->marca) }}">
                </div>
                <div>
                    <label class="form-label">Modelo</label>
                    <input type="text" name="modelo" class="form-control" value="{{ old('modelo', $vehiculo->modelo) }}">
                </div>
                <div>
                    <label class="form-label">Año</label>
                    <input type="number" name="anio" class="form-control" value="{{ old('anio', $vehiculo->anio) }}" min="1950" max="{{ date('Y') + 1 }}">
                </div>
                <div>
                    <label class="form-label">Color</label>
                    <input type="text" name="color" class="form-control" value="{{ old('color', $vehiculo->color) }}">
                </div>
                <div>
                    <label class="form-label">Combustible *</label>
                    <select name="combustible" class="form-control">
                        @foreach (['gasolina'=>'Gasolina','diesel'=>'Diésel','glp'=>'GLP','gnv'=>'GNV','electrico'=>'Eléctrico','hibrido'=>'Híbrido'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('combustible', $vehiculo->combustible)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Transmisión</label>
                    <select name="transmision" class="form-control">
                        <option value="">—</option>
                        <option value="manual" @selected(old('transmision', $vehiculo->transmision)==='manual')>Manual</option>
                        <option value="automatica" @selected(old('transmision', $vehiculo->transmision)==='automatica')>Automática</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kilometraje</label>
                    <input type="number" name="kilometraje" class="form-control" value="{{ old('kilometraje', $vehiculo->kilometraje) }}" min="0">
                </div>
                <div>
                    <label class="form-label">VIN / Chasis</label>
                    <input type="text" name="vin" class="form-control" value="{{ old('vin', $vehiculo->vin) }}">
                </div>
                <div>
                    <label class="form-label">N° de motor</label>
                    <input type="text" name="motor" class="form-control" value="{{ old('motor', $vehiculo->motor) }}">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control">{{ old('observaciones', $vehiculo->observaciones) }}</textarea>
            </div>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('vehiculos.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar vehículo' }}</button>
    </div>
</form>
@endsection
