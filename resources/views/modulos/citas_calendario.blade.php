@extends('layouts.app')
@section('title', 'Calendario de citas')

@section('content')
@php
    $diasEnMes = $inicio->daysInMonth;
    $offset = $inicio->dayOfWeekIso - 1; // 0 = lunes
    $hoy = now()->format('Y-m-d');
    $estadoColor = ['pendiente'=>'warning','confirmada'=>'info','atendida'=>'success','cancelada'=>'danger'];
@endphp

<div class="page-head">
    <div>
        <h1>Calendario de citas</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · <a href="{{ route('citas.index') }}">Citas</a> · Calendario</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('citas.index') }}" class="btn btn-light"><i class="fa-solid fa-list"></i> Vista lista</a>
        <a href="{{ route('citas.create') }}" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Agendar cita</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <a href="{{ route('citas.calendario', ['mes' => $inicio->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-light" style="height:38px;"><i class="fa-solid fa-chevron-left"></i></a>
        <h3 style="text-transform:capitalize; text-align:center; flex:1;">{{ $inicio->translatedFormat('F Y') }}</h3>
        <a href="{{ route('citas.calendario', ['mes' => $inicio->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-light" style="height:38px;"><i class="fa-solid fa-chevron-right"></i></a>
    </div>
    <div class="panel-body">
        <div class="cal-grid cal-head">
            @foreach (['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
                <div class="cal-weekday">{{ $d }}</div>
            @endforeach
        </div>
        <div class="cal-grid">
            @for ($i = 0; $i < $offset; $i++)
                <div class="cal-cell cal-empty"></div>
            @endfor
            @for ($d = 1; $d <= $diasEnMes; $d++)
                @php
                    $fecha = $inicio->copy()->day($d)->format('Y-m-d');
                    $delDia = $citas[$fecha] ?? collect();
                @endphp
                <div class="cal-cell {{ $fecha === $hoy ? 'cal-today' : '' }}">
                    <div class="cal-daynum">{{ $d }}</div>
                    <div class="cal-events">
                        @foreach ($delDia as $c)
                            <a href="{{ route('citas.edit', $c) }}" class="cal-event ce-{{ $estadoColor[$c->estado] ?? 'secondary' }}" title="{{ $c->titulo }} · {{ $c->cliente->nombre_completo ?? '' }}">
                                <b>{{ $c->fecha_hora->format('H:i') }}</b> {{ \Illuminate\Support\Str::limit($c->cliente->nombre_completo ?? $c->titulo, 14) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endfor
        </div>
        <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:16px; font-size:12px; color:var(--text-muted);">
            <span><span class="dot-leg" style="background:#d97706;"></span> Pendiente</span>
            <span><span class="dot-leg" style="background:#0284c7;"></span> Confirmada</span>
            <span><span class="dot-leg" style="background:#15803d;"></span> Atendida</span>
            <span><span class="dot-leg" style="background:#b91c1c;"></span> Cancelada</span>
        </div>
    </div>
</div>
@endsection
