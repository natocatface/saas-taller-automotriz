@extends('layouts.app')
@section('title', $proveedor->exists ? 'Editar proveedor' : 'Nuevo proveedor')

@section('content')
@php $editando = $proveedor->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar proveedor' : 'Nuevo proveedor' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('proveedores.index') }}">Proveedores</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('proveedores.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('proveedores.update', $proveedor) : route('proveedores.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del proveedor</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $proveedor->ruc) }}">
                </div>
                <div>
                    <label class="form-label">Nombre / Razón social *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $proveedor->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">Persona de contacto</label>
                    <input type="text" name="contacto" class="form-control" value="{{ old('contacto', $proveedor->contacto) }}">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $proveedor->telefono) }}">
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $proveedor->email) }}">
                </div>
                <div>
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $proveedor->direccion) }}">
                </div>
            </div>
            <label style="display:flex; align-items:center; gap:9px; margin-top:16px; cursor:pointer; font-size:13.5px;">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $proveedor->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);">
                Proveedor activo
            </label>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('proveedores.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar proveedor' }}</button>
    </div>
</form>
@endsection
