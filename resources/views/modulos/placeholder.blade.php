@extends('layouts.app')
@section('title', $titulo)

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $titulo }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · {{ $titulo }}</div>
    </div>
</div>

<div class="panel">
    <div class="placeholder-box">
        <div class="ph-icon"><i class="fa-solid {{ $icono }}"></i></div>
        <h2>{{ $titulo }}</h2>
        <p>{{ $descripcion }}</p>
        <span class="tag-soon"><i class="fa-solid fa-screwdriver-wrench"></i> Módulo en desarrollo</span>
    </div>
</div>
@endsection
