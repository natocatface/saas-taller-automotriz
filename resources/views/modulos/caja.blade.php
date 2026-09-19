@extends('layouts.app')
@section('title', 'Caja y pagos')

@section('content')
<div class="page-head">
    <div>
        <h1>Caja y pagos</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Inicio</a> · Caja</div>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
    <div class="stat-card grad-teal">
        <div class="s-label">Ingresos de hoy</div><div class="s-value">S/ {{ number_format($resumen['ingresos_hoy'], 2) }}</div>
        <div class="s-sub"><i class="fa-solid fa-arrow-down"></i> Entradas del día</div><i class="fa-solid fa-arrow-trend-up s-icon"></i>
    </div>
    <div class="stat-card grad-rose">
        <div class="s-label">Egresos de hoy</div><div class="s-value">S/ {{ number_format($resumen['egresos_hoy'], 2) }}</div>
        <div class="s-sub"><i class="fa-solid fa-arrow-up"></i> Salidas del día</div><i class="fa-solid fa-arrow-trend-down s-icon"></i>
    </div>
    <div class="stat-card grad-blue">
        <div class="s-label">Saldo de hoy</div><div class="s-value">S/ {{ number_format($resumen['saldo_hoy'], 2) }}</div>
        <div class="s-sub"><i class="fa-solid fa-scale-balanced"></i> Ingresos − egresos</div><i class="fa-solid fa-cash-register s-icon"></i>
    </div>
    <div class="stat-card grad-violet">
        <div class="s-label">Saldo acumulado</div><div class="s-value">S/ {{ number_format($resumen['saldo_total'], 2) }}</div>
        <div class="s-sub"><i class="fa-solid fa-vault"></i> Histórico total</div><i class="fa-solid fa-piggy-bank s-icon"></i>
    </div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 2fr;">
    {{-- Registrar movimiento --}}
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Registrar movimiento</h3></div>
        <div class="panel-body">
            @include('partials.form_errors')
            <form method="POST" action="{{ route('caja.store') }}">
                @csrf
                <label class="form-label">Tipo *</label>
                <select name="tipo" class="form-control" style="margin-bottom:14px;">
                    <option value="ingreso">Ingreso</option>
                    <option value="egreso">Egreso</option>
                </select>
                <label class="form-label">Concepto *</label>
                <input type="text" name="concepto" class="form-control" style="margin-bottom:14px;" placeholder="Ej. Pago de orden OT-0001" required>
                <label class="form-label">Monto (S/) *</label>
                <input type="number" step="0.01" name="monto" class="form-control" style="margin-bottom:14px;" min="0.01" required>
                <label class="form-label">Fecha *</label>
                <input type="date" name="fecha" class="form-control" style="margin-bottom:14px;" value="{{ now()->toDateString() }}" required>
                <label class="form-label">Método de pago *</label>
                <select name="metodo_pago" class="form-control" style="margin-bottom:18px;">
                    @foreach (['efectivo'=>'Efectivo','tarjeta'=>'Tarjeta','transferencia'=>'Transferencia','yape'=>'Yape / Plin'] as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fa-solid fa-plus"></i> Registrar</button>
            </form>
        </div>
    </div>

    {{-- Historial --}}
    <div class="panel" style="min-width:0;">
        <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
            <div><h3>Movimientos</h3><div class="sub">Del {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($hasta)->format('d/m/Y') }}</div></div>
            <form method="GET" action="{{ route('caja.index') }}" style="display:flex; gap:8px; margin-left:auto; align-items:center;">
                <input type="date" name="desde" value="{{ $desde }}" class="form-control" style="width:150px;">
                <input type="date" name="hasta" value="{{ $hasta }}" class="form-control" style="width:150px;">
                <button type="submit" class="btn btn-primary" style="height:40px;">Filtrar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Fecha</th><th>Concepto</th><th>Método</th><th>Tipo</th><th style="text-align:right;">Monto</th><th style="text-align:right;"></th></tr></thead>
                <tbody>
                @forelse ($movimientos as $m)
                    <tr>
                        <td class="cell-muted">{{ optional($m->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $m->concepto }}<div class="cell-muted">{{ $m->user->name ?? '' }}</div></td>
                        <td class="cell-muted" style="text-transform:capitalize;">{{ $m->metodo_pago }}</td>
                        <td><span class="badge-pill {{ $m->tipo === 'ingreso' ? 'b-success' : 'b-danger' }}">{{ ucfirst($m->tipo) }}</span></td>
                        <td style="text-align:right;" class="cell-strong">{{ $m->tipo === 'ingreso' ? '+' : '−' }} S/ {{ number_format($m->monto, 2) }}</td>
                        <td style="text-align:right;">
                            <form method="POST" action="{{ route('caja.destroy', $m) }}" onsubmit="return confirm('¿Eliminar este movimiento?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:34px;">Sin movimientos en el rango seleccionado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pager', ['paginator' => $movimientos])
    </div>
</div>
@endsection
