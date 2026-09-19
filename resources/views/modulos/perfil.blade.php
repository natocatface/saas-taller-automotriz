@extends('layouts.app')
@section('title', 'Mi perfil')

@section('content')
@php
    $ini = collect(explode(' ', $user->name))->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
@endphp

<div class="page-head">
    <div>
        <h1>Mi perfil</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Perfil</div>
    </div>
</div>

@include('partials.form_errors')

<div class="charts-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="panel" style="min-width:0;">
        <div class="panel-body" style="text-align:center; padding:34px 20px;">
            <div class="avatar" style="width:88px; height:88px; border-radius:22px; font-size:32px; margin:0 auto 16px;">{{ mb_strtoupper($ini) }}</div>
            <h2 style="font-size:19px; margin-bottom:4px;">{{ $user->name }}</h2>
            <div class="cell-muted">{{ $user->email }}</div>
            <span class="badge-pill b-primary" style="margin-top:12px; text-transform:capitalize;">{{ \App\Support\Permisos::rolLabel($user->rol) }}</span>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Datos y seguridad</h3><div class="sub">Actualiza tu información y contraseña</div></div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('perfil.update') }}">
                @csrf @method('PUT')
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
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}">
                    </div>
                </div>
                <div style="border-top:1px solid var(--border); margin:22px 0 18px;"></div>
                <div class="sub" style="margin-bottom:14px; font-weight:600; color:var(--text);">Cambiar contraseña (opcional)</div>
                <div class="form-grid">
                    <div>
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
                    </div>
                    <div>
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña">
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; margin-top:22px;">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
