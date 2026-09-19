@extends('layouts.app')
@section('title', $servicio->exists ? 'Editar servicio' : 'Nuevo servicio')

@section('content')
@php $editando = $servicio->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar servicio' : 'Nuevo servicio' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('servicios.index') }}">Servicios</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('servicios.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('servicios.update', $servicio) : route('servicios.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del servicio</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $servicio->codigo) }}">
                </div>
                <div>
                    <label class="form-label">Nombre del servicio *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $servicio->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">Categoría</label>
                    <input type="text" name="categoria" class="form-control" value="{{ old('categoria', $servicio->categoria) }}" placeholder="Ej. Motor, Frenos, Suspensión">
                </div>
                <div>
                    <label class="form-label">Precio (S/) *</label>
                    <input type="number" step="0.01" name="precio" class="form-control" value="{{ old('precio', $servicio->precio ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Duración (minutos)</label>
                    <input type="number" name="duracion_min" class="form-control" value="{{ old('duracion_min', $servicio->duracion_min) }}" min="0">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            </div>
            <label style="display:flex; align-items:center; gap:9px; margin-top:16px; cursor:pointer; font-size:13.5px;">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $servicio->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);">
                Servicio activo
            </label>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('servicios.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar servicio' }}</button>
    </div>
</form>
@endsection
