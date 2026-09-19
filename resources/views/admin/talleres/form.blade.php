@extends('layouts.admin')
@section('title', $taller->exists ? 'Editar taller' : 'Nuevo taller')

@section('content')
@php $editando = $taller->exists; $estados = \App\Models\Taller::estados(); @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar taller' : 'Nuevo taller' }}</h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · <a href="{{ route('admin.talleres.index') }}">Talleres</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('admin.talleres.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('admin.talleres.update', $taller) : route('admin.talleres.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3>Datos del taller</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Nombre del taller *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $taller->nombre) }}" required>
                </div>
                <div>
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $taller->ruc) }}">
                </div>
                <div>
                    <label class="form-label">Persona de contacto</label>
                    <input type="text" name="contacto_nombre" class="form-control" value="{{ old('contacto_nombre', $taller->contacto_nombre) }}">
                </div>
                <div>
                    <label class="form-label">Correo</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $taller->email) }}">
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $taller->telefono) }}">
                </div>
                <div>
                    <label class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control" value="{{ old('ciudad', $taller->ciudad) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-bottom:20px;">
        <div class="panel-head"><h3>Suscripción</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Plan</label>
                    <select name="plan_id" class="form-control">
                        <option value="">Sin plan</option>
                        @foreach ($planes as $p)
                            <option value="{{ $p->id }}" @selected(old('plan_id', $taller->plan_id) == $p->id)>{{ $p->nombre }} — S/ {{ number_format($p->precio_mensual,0) }}/mes</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Estado *</label>
                    <select name="estado" class="form-control">
                        @foreach ($estados as $k => $label)
                            <option value="{{ $k }}" @selected(old('estado', $taller->estado)===$k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Periodo *</label>
                    <select name="periodo" class="form-control">
                        <option value="mensual" @selected(old('periodo', $taller->periodo)==='mensual')>Mensual</option>
                        <option value="anual" @selected(old('periodo', $taller->periodo)==='anual')>Anual</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Fecha de inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', optional($taller->fecha_inicio)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Fecha de vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', optional($taller->fecha_vencimiento)->format('Y-m-d')) }}">
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Notas internas</label>
                <textarea name="notas" class="form-control">{{ old('notas', $taller->notas) }}</textarea>
            </div>
        </div>
    </div>

    <div style="display:flex; gap:12px; justify-content:flex-end; margin-bottom:30px;">
        <a href="{{ route('admin.talleres.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Registrar taller' }}</button>
    </div>
</form>
@endsection
