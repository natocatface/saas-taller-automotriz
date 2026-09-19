@extends('layouts.admin')
@section('title', 'Planes de suscripción')

@section('content')
<div class="page-head">
    <div>
        <h1>Planes de suscripción</h1>
        <div class="crumb"><a href="{{ route('admin.dashboard') }}">Panel</a> · Planes</div>
    </div>
    <a href="{{ route('admin.planes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nuevo plan</a>
</div>

@php
    $estilos = [
        ['grad' => 'grad-cyan', 'icon' => 'fa-seedling'],
        ['grad' => 'grad-violet', 'icon' => 'fa-rocket'],
        ['grad' => 'grad-amber', 'icon' => 'fa-crown'],
        ['grad' => 'grad-teal', 'icon' => 'fa-gem'],
        ['grad' => 'grad-rose', 'icon' => 'fa-star'],
        ['grad' => 'grad-blue', 'icon' => 'fa-bolt'],
    ];
@endphp
<div class="plan-grid">
    @forelse ($planes as $plan)
        @php $e = $plan->destacado ? ['grad' => 'grad-violet', 'icon' => 'fa-rocket'] : $estilos[$loop->index % count($estilos)]; @endphp
        <div class="plan-card {{ $plan->destacado ? 'plan-featured' : '' }}">
            <div class="plan-top {{ $e['grad'] }}">
                @if ($plan->destacado)<div class="plan-badge"><i class="fa-solid fa-star"></i> Más popular</div>@endif
                <div class="plan-ic"><i class="fa-solid {{ $e['icon'] }}"></i></div>
                <div class="plan-name">{{ $plan->nombre }}</div>
                <div class="plan-price">S/ {{ number_format($plan->precio_mensual, 0) }}<span>/mes</span></div>
                <div class="plan-anual">o S/ {{ number_format($plan->precio_anual, 0) }} al año · ahorra {{ $plan->precio_mensual > 0 ? round((1 - $plan->precio_anual / ($plan->precio_mensual * 12)) * 100) : 0 }}%</div>
            </div>
            <div class="plan-body">
                <p class="plan-desc">{{ $plan->descripcion }}</p>
                <ul class="plan-features">
                    @foreach ($plan->lista_caracteristicas as $f)
                        <li><i class="fa-solid fa-circle-check"></i> {{ $f }}</li>
                    @endforeach
                </ul>
                <div class="plan-meta">
                    <div class="chip"><i class="fa-solid fa-users"></i> {{ $plan->limite_usuarios > 0 ? $plan->limite_usuarios : '∞' }} usuarios</div>
                    <div class="chip"><i class="fa-solid fa-clipboard-list"></i> {{ $plan->limite_ordenes > 0 ? $plan->limite_ordenes : '∞' }} órdenes/mes</div>
                </div>
                <div class="plan-foot">
                    <span class="badge-pill b-primary"><i class="fa-solid fa-shop"></i> {{ $plan->talleres_count }} talleres</span>
                    <div class="row-actions">
                        <a href="{{ route('admin.planes.edit', $plan) }}" class="ico-btn" title="Editar"><i class="fa-solid fa-pen"></i></a>
                        <form method="POST" action="{{ route('admin.planes.destroy', $plan) }}" onsubmit="return confirm('¿Eliminar el plan {{ $plan->nombre }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="ico-btn ico-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel" style="grid-column:1/-1;"><div class="placeholder-box">
            <div class="ph-icon"><i class="fa-solid fa-tags"></i></div>
            <h2>Aún no hay planes</h2>
            <p>Crea tu primer plan de suscripción para empezar a captar talleres.</p>
            <a href="{{ route('admin.planes.create') }}" class="btn btn-primary" style="margin-top:16px;">Crear plan</a>
        </div></div>
    @endforelse
</div>
@endsection
