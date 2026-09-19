@extends('layouts.admin')
@section('title', $taller->nombre)

@section('content')
@php $estados = \App\Models\Taller::estados(); @endphp

<div class="page-head">
    <div>
        <h1>{{ $taller->nombre }}
            <span class="badge-pill b-{{ $taller->estadoColor() }}" style="vertical-align:middle; margin-left:8px;">{{ $estados[$taller->estado] ?? $taller->estado }}</span>
        </h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · <a href="{{ route('admin.talleres.index') }}">Talleres</a> · {{ $taller->nombre }}</div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('admin.pagos.index') }}" class="btn btn-light"><i class="fa-solid fa-money-bill-trend-up"></i> Registrar pago</a>
        <a href="{{ route('admin.talleres.edit', $taller) }}" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Editar</a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="stat-card grad-violet"><div class="s-label">Plan actual</div><div class="s-value" style="font-size:22px;">{{ $taller->plan->nombre ?? '—' }}</div><div class="s-sub"><i class="fa-solid fa-tags"></i> {{ ucfirst($taller->periodo) }}</div><i class="fa-solid fa-tags s-icon"></i></div>
    <div class="stat-card {{ $taller->vencido ? 'grad-rose' : 'grad-teal' }}"><div class="s-label">Días restantes</div><div class="s-value">{{ $taller->dias_restantes !== null ? $taller->dias_restantes : '—' }}</div><div class="s-sub"><i class="fa-solid fa-clock"></i> Hasta el vencimiento</div><i class="fa-solid fa-hourglass s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">Total pagado</div><div class="s-value">S/ {{ number_format($taller->pagos->sum('monto'), 0) }}</div><div class="s-sub"><i class="fa-solid fa-receipt"></i> {{ $taller->pagos->count() }} pagos</div><i class="fa-solid fa-sack-dollar s-icon"></i></div>
    <div class="stat-card grad-amber"><div class="s-label">Vence</div><div class="s-value" style="font-size:20px;">{{ optional($taller->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</div><div class="s-sub"><i class="fa-solid fa-calendar"></i> Fecha límite</div><i class="fa-solid fa-calendar-check s-icon"></i></div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Datos de contacto</h3></div>
        <div class="panel-body">
            <div class="detail-grid" style="grid-template-columns:1fr;">
                <div class="detail-item"><div class="di-label">RUC</div><div class="di-value">{{ $taller->ruc ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Contacto</div><div class="di-value">{{ $taller->contacto_nombre ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Correo</div><div class="di-value">{{ $taller->email ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Teléfono</div><div class="di-value">{{ $taller->telefono ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Ciudad</div><div class="di-value">{{ $taller->ciudad ?? '—' }}</div></div>
                <div class="detail-item"><div class="di-label">Inicio</div><div class="di-value">{{ optional($taller->fecha_inicio)->format('d/m/Y') ?? '—' }}</div></div>
            </div>
            @if ($taller->notas)
                <div style="margin-top:16px;"><div class="di-label" style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:600; margin-bottom:4px;">Notas</div><div>{{ $taller->notas }}</div></div>
            @endif
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head"><div><h3>Historial de pagos</h3><div class="sub">{{ $taller->pagos->count() }} registros</div></div></div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Fecha</th><th>Plan</th><th>Periodo</th><th>Método</th><th>Referencia</th><th style="text-align:right;">Monto</th></tr></thead>
                <tbody>
                @forelse ($taller->pagos->sortByDesc('fecha_pago') as $p)
                    <tr>
                        <td class="cell-muted">{{ optional($p->fecha_pago)->format('d/m/Y') }}</td>
                        <td>{{ $p->plan->nombre ?? '—' }}</td>
                        <td class="cell-muted" style="text-transform:capitalize;">{{ $p->periodo }}</td>
                        <td class="cell-muted" style="text-transform:capitalize;">{{ $p->metodo }}</td>
                        <td class="cell-muted">{{ $p->referencia }}</td>
                        <td style="text-align:right;" class="cell-strong">S/ {{ number_format($p->monto, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:26px;">Sin pagos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
