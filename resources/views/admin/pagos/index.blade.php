@extends('layouts.admin')
@section('title', 'Pagos de suscripción')

@section('content')
<div class="page-head">
    <div>
        <h1>Pagos de suscripción</h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · Pagos</div>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:22px; grid-template-columns:repeat(auto-fit,minmax(210px,1fr));">
    <div class="stat-card grad-violet"><div class="s-label">Total recaudado</div><div class="s-value">S/ {{ number_format($resumen['total'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-vault"></i> Histórico</div><i class="fa-solid fa-sack-dollar s-icon"></i></div>
    <div class="stat-card grad-teal"><div class="s-label">Este mes</div><div class="s-value">S/ {{ number_format($resumen['mes'], 0) }}</div><div class="s-sub"><i class="fa-solid fa-calendar"></i> Cobrado</div><i class="fa-solid fa-money-bill-wave s-icon"></i></div>
    <div class="stat-card grad-blue"><div class="s-label">Pagos registrados</div><div class="s-value">{{ $resumen['cantidad'] }}</div><div class="s-sub"><i class="fa-solid fa-receipt"></i> Transacciones</div><i class="fa-solid fa-receipt s-icon"></i></div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 2fr;">
    <div class="panel" style="min-width:0;">
        <div class="panel-head"><h3>Registrar pago</h3></div>
        <div class="panel-body">
            @include('partials.form_errors')
            <form method="POST" action="{{ route('admin.pagos.store') }}">
                @csrf
                <label class="form-label">Taller *</label>
                <select name="taller_id" class="form-control" style="margin-bottom:14px;" required>
                    <option value="">Seleccionar taller...</option>
                    @foreach ($talleres as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
                <label class="form-label">Monto (S/) *</label>
                <input type="number" step="0.01" name="monto" class="form-control" style="margin-bottom:14px;" min="0" required>
                <label class="form-label">Fecha de pago *</label>
                <input type="date" name="fecha_pago" class="form-control" style="margin-bottom:14px;" value="{{ now()->toDateString() }}" required>
                <label class="form-label">Periodo *</label>
                <select name="periodo" class="form-control" style="margin-bottom:14px;">
                    <option value="mensual">Mensual (+1 mes)</option>
                    <option value="anual">Anual (+1 año)</option>
                </select>
                <label class="form-label">Método *</label>
                <select name="metodo" class="form-control" style="margin-bottom:14px;">
                    @foreach (['transferencia'=>'Transferencia','tarjeta'=>'Tarjeta','yape'=>'Yape / Plin','efectivo'=>'Efectivo'] as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                <label class="form-label">Referencia</label>
                <input type="text" name="referencia" class="form-control" style="margin-bottom:18px;" placeholder="N° de operación">
                <button type="submit" class="btn btn-primary" style="width:100%;"><i class="fa-solid fa-plus"></i> Registrar y renovar</button>
            </form>
        </div>
    </div>

    <div class="panel" style="min-width:0;">
        <div class="panel-head" style="gap:12px; flex-wrap:wrap;">
            <div><h3>Historial de pagos</h3><div class="sub">{{ $pagos->total() }} registros</div></div>
            <form method="GET" action="{{ route('admin.pagos.index') }}" style="display:flex; gap:10px; margin-left:auto;">
                <div class="search" style="max-width:220px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" value="{{ $q }}" placeholder="Taller o referencia..."></div>
                <button type="submit" class="btn btn-primary" style="height:40px;">Buscar</button>
            </form>
        </div>
        <div class="table-wrap">
            <table class="tbl">
                <thead><tr><th>Fecha</th><th>Taller</th><th>Plan</th><th>Método</th><th style="text-align:right;">Monto</th><th></th></tr></thead>
                <tbody>
                @forelse ($pagos as $p)
                    <tr>
                        <td class="cell-muted">{{ optional($p->fecha_pago)->format('d/m/Y') }}</td>
                        <td class="cell-strong">{{ $p->taller->nombre ?? '—' }}</td>
                        <td>{{ $p->plan->nombre ?? '—' }}</td>
                        <td class="cell-muted" style="text-transform:capitalize;">{{ $p->metodo }}</td>
                        <td style="text-align:right;" class="cell-strong">S/ {{ number_format($p->monto, 2) }}</td>
                        <td style="text-align:right;">
                            <form method="POST" action="{{ route('admin.pagos.destroy', $p) }}" onsubmit="return confirm('¿Eliminar este pago?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="cell-muted" style="text-align:center; padding:30px;">Sin pagos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @include('partials.pager', ['paginator' => $pagos])
    </div>
</div>
@endsection
