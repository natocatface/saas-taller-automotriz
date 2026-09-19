@extends('layouts.app')
@section('title', $cita->exists ? 'Editar cita' : 'Nueva cita')

@section('content')
@php $editando = $cita->exists; @endphp

<div class="page-head">
    <div>
        <h1>{{ $editando ? 'Editar cita' : 'Agendar cita' }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('citas.index') }}">Citas</a> · {{ $editando ? 'Editar' : 'Nueva' }}</div>
    </div>
    <a href="{{ route('citas.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver</a>
</div>

@include('partials.form_errors')

<form method="POST" action="{{ $editando ? route('citas.update', $cita) : route('citas.store') }}">
    @csrf
    @if ($editando) @method('PUT') @endif
    <div class="panel">
        <div class="panel-head"><h3>Datos de la cita</h3></div>
        <div class="panel-body">
            <div class="form-grid">
                <div>
                    <label class="form-label">Título *</label>
                    <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $cita->titulo) }}" placeholder="Ej. Mantenimiento preventivo" required>
                </div>
                <div>
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" id="clienteSel" class="form-control">
                        <option value="">Seleccionar cliente...</option>
                        @foreach ($clientes as $c)
                            <option value="{{ $c->id }}" @selected(old('cliente_id', $cita->cliente_id) == $c->id)>{{ $c->nombre_completo }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Vehículo</label>
                    <select name="vehiculo_id" id="vehiculoSel" class="form-control">
                        <option value="">Seleccionar vehículo...</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Mecánico</label>
                    <select name="mecanico_id" class="form-control">
                        <option value="">Sin asignar</option>
                        @foreach ($mecanicos as $m)
                            <option value="{{ $m->id }}" @selected(old('mecanico_id', $cita->mecanico_id) == $m->id)>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Fecha y hora *</label>
                    <input type="datetime-local" name="fecha_hora" class="form-control" value="{{ old('fecha_hora', optional($cita->fecha_hora)->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div>
                    <label class="form-label">Duración (minutos) *</label>
                    <input type="number" name="duracion_min" class="form-control" value="{{ old('duracion_min', $cita->duracion_min ?? 60) }}" min="15" required>
                </div>
                <div>
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        @foreach (['pendiente'=>'Pendiente','confirmada'=>'Confirmada','atendida'=>'Atendida','cancelada'=>'Cancelada'] as $k=>$v)
                            <option value="{{ $k }}" @selected(old('estado', $cita->estado)===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top:16px;">
                <label class="form-label">Motivo / notas</label>
                <textarea name="motivo" class="form-control">{{ old('motivo', $cita->motivo) }}</textarea>
            </div>
        </div>
    </div>
    <div style="display:flex; gap:12px; justify-content:flex-end; margin:20px 0 30px;">
        <a href="{{ route('citas.index') }}" class="btn btn-light">Cancelar</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> {{ $editando ? 'Guardar cambios' : 'Agendar cita' }}</button>
    </div>
</form>
@endsection

@php
    $vehJs = $vehiculos->map(fn ($v) => ['id' => $v->id, 'placa' => $v->placa, 'marca' => $v->marca, 'modelo' => $v->modelo, 'cliente_id' => $v->cliente_id])->values();
@endphp
@push('scripts')
<script>
    const VEHICULOS = @json($vehJs);
    const SEL_VEH = @json(old('vehiculo_id', $cita->vehiculo_id));
    const clienteSel = document.getElementById('clienteSel');
    const vehiculoSel = document.getElementById('vehiculoSel');
    function cargarVehiculos(sel) {
        const cid = parseInt(clienteSel.value);
        vehiculoSel.innerHTML = '<option value="">Seleccionar vehículo...</option>';
        VEHICULOS.filter(v => !cid || v.cliente_id === cid).forEach(v => {
            const o = document.createElement('option');
            o.value = v.id; o.textContent = `${v.placa} · ${v.marca||''} ${v.modelo||''}`.trim();
            if (sel && v.id == sel) o.selected = true;
            vehiculoSel.appendChild(o);
        });
    }
    clienteSel.addEventListener('change', () => cargarVehiculos(null));
    cargarVehiculos(SEL_VEH);
</script>
@endpush
