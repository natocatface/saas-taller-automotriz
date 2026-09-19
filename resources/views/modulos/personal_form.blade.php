@extends('layouts.app')
@section('title', $user->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('content')
@php $editando = $user->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('personal.index') }}">Personal</a> · {{ $editando ? 'Editar' : 'Nuevo' }}</div>
    </div>
    <a href="{{ route('personal.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('personal.update', $user) : route('personal.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos del usuario</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Nombre completo *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>
                <div>
                    <label class="form-label">Correo *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div>
                    <label class="form-label">Rol *</label>
                    <select name="rol" class="form-control">
                        @foreach (['admin'=>'Administrador','gerente'=>'Gerente','mecanico'=>'Mecánico','empleado'=>'Empleado'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('rol', $user->rol)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}">
                </div>
                <div>
                    <label class="form-label">Contraseña {{ $editando ? '(dejar en blanco para no cambiar)' : '*' }}</label>
                    <input type="password" name="password" class="form-control" placeholder="{{ $editando ? '••••••••' : 'Mínimo 6 caracteres' }}" {{ $editando ? '' : 'required' }}>
                </div>
            </div>
            <label style="display:flex; align-items:center; gap:9px; margin-top:16px; cursor:pointer; font-size:13.5px;">
                <input type="checkbox" name="activo" value="1" @checked(old('activo', $user->activo ?? true)) style="width:17px; height:17px; accent-color:var(--primary);">
                Usuario activo (puede iniciar sesión)
            </label>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('personal.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Crear usuario' }}</button>
    </div>
</form>
@endsection
