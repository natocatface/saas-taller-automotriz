@extends('layouts.app')
@section('title', 'Importar datos')

@section('content')
<div class="page-head">
    <div>
        <h1>Importar {{ $tipo === 'clientes' ? 'clientes' : 'repuestos' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Importar</div>
    </div>
    <a href="{{ route($tipo.'.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<div style="display:flex; gap:8px; margin-bottom:20px;">
    <a href="{{ route('import.index', ['tipo' => 'clientes']) }}" class="btn {{ $tipo === 'clientes' ? 'btn-primary' : 'btn-light' }}"><i class="fa-solid fa-users"></i> Clientes</a>
    <a href="{{ route('import.index', ['tipo' => 'repuestos']) }}" class="btn {{ $tipo === 'repuestos' ? 'btn-primary' : 'btn-light' }}"><i class="fa-solid fa-boxes-stacked"></i> Repuestos</a>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Paso 1 · Descarga la plantilla</h3></div></div>
        <div class="panel-body">
            <p style="color:var(--text-muted); margin-bottom:16px;">Descarga el archivo CSV, llénalo con tus datos en Excel y guárdalo como <b>CSV</b>. Respeta el orden de las columnas.</p>
            <div style="background:#f6f8fa; border-radius:10px; padding:14px; margin-bottom:16px;">
                <div class="di-label" style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:600; margin-bottom:8px;">Columnas</div>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @foreach ($columnas as $c)
                        <span class="badge-pill b-info">{{ $c }}</span>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('import.plantilla', $tipo) }}" class="btn btn-light"><i class="fa-solid fa-download"></i> Descargar plantilla CSV</a>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Paso 2 · Sube tu archivo</h3></div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo" value="{{ $tipo }}">
                <label class="form-label">Archivo CSV *</label>
                <input type="file" name="archivo" class="form-control" accept=".csv,.txt" required style="height:auto; padding:9px 12px;">
                <p style="color:var(--text-muted); font-size:12.5px; margin:12px 0 18px;">
                    <i class="fa-solid fa-circle-info"></i> Se agregarán como registros nuevos. Las filas sin nombre se omiten.
                </p>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-file-import"></i> Importar {{ $tipo }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
