@extends('layouts.app')
@section('title', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('content')
@php $editando = $cliente->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar cliente' : 'Nuevo cliente' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('clientes.index') }}">Clientes</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('clientes.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('clientes.update', $cliente) : route('clientes.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del cliente</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Tipo *</label>
                    <select name="tipo" class="form-control">
                        <option value="persona" @selected(old('tipo', $cliente->tipo)==='persona')>Persona</option>
                        <option value="empresa" @selected(old('tipo', $cliente->tipo)==='empresa')>Empresa</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Tipo de documento *</label>
                    <select name="tipo_documento" class="form-control">
                        @foreach (['DNI','RUC','CE','Pasaporte'] as $td)
                            <option value="{{ $td }}" @selected(old('tipo_documento', $cliente->tipo_documento)===$td)>{{ $td }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">N° de documento</label>
                    <input type="text" name="documento" class="form-control" value="{{ old('documento', $cliente->documento) }}">
                </div>
                <div>
                    <label class="form-label">Nombre / Nombres *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="apellidos" class="form-control" value="{{ old('apellidos', $cliente->apellidos) }}">
                </div>
                <div>
                    <label class="form-label">Razón social (empresa)</label>
                    <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social', $cliente->razon_social) }}">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}">
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}">
                </div>
                <div>
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $cliente->direccion) }}">
                </div>
                <div>
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $cliente->ciudad) }}">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control">{{ old('observaciones', $cliente->observaciones) }}</textarea>
            </div>
            <label style="display:flex; align-items:center; gap:9px; margin-top:16px; cursor:pointer; font-size:13.5px;">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $cliente->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);">
                Cliente activo
            </label>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('clientes.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar cliente' }}</button>
    </div>
</form>
@endsection
