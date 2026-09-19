@extends('layouts.app')
@section('title', $repuesto->exists ? 'Editar repuesto' : 'Nuevo repuesto')

@section('content')
@php $editando = $repuesto->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar repuesto' : 'Nuevo repuesto' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('repuestos.index') }}">Repuestos</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('repuestos.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('repuestos.update', $repuesto) : route('repuestos.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del repuesto</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Código</label>
                    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $repuesto->codigo) }}">
                </div>
                <div>
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $repuesto->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">Categoría</label>
                    <input type="text" name="categoria" class="form-control" value="{{ old('categoria', $repuesto->categoria) }}">
                </div>
                <div>
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-control">
                        <option value="">Sin proveedor</option>
                        @foreach ($proveedores as $p)
                            <option value="{{ $p->id }}" @selected(old('proveedor_id', $repuesto->proveedor_id) == $p->id)>{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Unidad</label>
                    <input type="text" name="unidad" class="form-control" value="{{ old('unidad', $repuesto->unidad ?? 'UND') }}">
                </div>
                <div>
                    <label class="form-label">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control" value="{{ old('ubicacion', $repuesto->ubicacion) }}" placeholder="Ej. Estante A-3">
                </div>
                <div>
                    <label class="form-label">Precio de compra (S/) *</label>
                    <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', $repuesto->precio_compra ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Precio de venta (S/) *</label>
                    <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta', $repuesto->precio_venta ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Stock actual *</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $repuesto->stock ?? 0) }}" min="0" required>
                </div>
                <div>
                    <label class="form-label">Stock mínimo *</label>
                    <input type="number" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $repuesto->stock_minimo ?? 0) }}" min="0" required>
                </div>
            </div>
            <label style="display:flex; align-items:center; gap:9px; margin-top:16px; cursor:pointer; font-size:13.5px;">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $repuesto->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);">
                Repuesto activo
            </label>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('repuestos.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar repuesto' }}</button>
    </div>
</form>
@endsection
