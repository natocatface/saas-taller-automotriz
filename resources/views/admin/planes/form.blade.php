@extends('layouts.admin')
@section('title', $plan->exists ? 'Editar plan' : 'Nuevo plan')

@section('content')
@php $editando = $plan->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar plan' : 'Nuevo plan' }}</h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · <a href="{{ route('admin.planes.index') }}">Planes</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('admin.planes.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('admin.planes.update', $plan) : route('admin.planes.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del plan</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $plan->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">Precio mensual (S/) *</label>
                    <input type="number" step="0.01" name="precio_mensual" class="form-control" value="{{ old('precio_mensual', $plan->precio_mensual ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Precio anual (S/) *</label>
                    <input type="number" step="0.01" name="precio_anual" class="form-control" value="{{ old('precio_anual', $plan->precio_anual ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Límite de usuarios (0 = ilimitado) *</label>
                    <input type="number" name="limite_usuarios" class="form-control" value="{{ old('limite_usuarios', $plan->limite_usuarios ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Límite de órdenes/mes (0 = ilimitado) *</label>
                    <input type="number" name="limite_ordenes" class="form-control" value="{{ old('limite_ordenes', $plan->limite_ordenes ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Orden de aparición</label>
                    <input type="number" name="orden" class="form-control" value="{{ old('orden', $plan->orden ?? 0) }}" min="0">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Descripción</label>
                <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $plan->descripcion) }}" placeholder="Frase corta que resume el plan">
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Características (una por línea)</label>
                <textarea name="caracteristicas" class="form-control" style="min-height:130px;" placeholder="Hasta 10 usuarios&#10;500 órdenes por mes&#10;Soporte prioritario">{{ old('caracteristicas', $plan->caracteristicas) }}</textarea>
            </div>
            <div style="display:flex; gap:24px; margin-top:16px;">
                <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                    <input type="checkbox" name="destacado" value="1" @checked(old('destacado', $plan->destacado)) style="width:17px; height:17px; accent-color:var(--primary);"> Plan destacado (más popular)
                </label>
                <label style="display:flex; align-items:center; gap:9px; cursor:pointer; font-size:13.5px;">
                    <input type="checkbox" name="activo" value="1" @checked(old('activo', $plan->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);"> Plan activo (visible en la landing)
                </label>
            </div>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('admin.planes.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Crear plan' }}</button>
    </div>
</form>
@endsection
