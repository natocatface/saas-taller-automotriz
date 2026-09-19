<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagoSuscripcion;
use App\Models\Plan;
use App\Models\Taller;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $activos = Taller::where('estado', 'activo')->with('plan')->get();

        // MRR: ingreso mensual recurrente equivalente
        $mrr = $activos->sum(function ($t) {
            if (! $t->plan) {
                return 0;
            }

            return $t->periodo === 'anual' ? $t->plan->precio_anual / 12 : $t->plan->precio_mensual;
        });

        $kpis = [
            'talleres' => Taller::count(),
            'activos' => Taller::where('estado', 'activo')->count(),
            'prueba' => Taller::where('estado', 'prueba')->count(),
            'suspendidos' => Taller::whereIn('estado', ['suspendido', 'cancelado'])->count(),
            'mrr' => round($mrr, 2),
            'arr' => round($mrr * 12, 2),
            'ingresos_mes' => PagoSuscripcion::whereMonth('fecha_pago', now()->month)->whereYear('fecha_pago', now()->year)->sum('monto'),
            'por_vencer' => Taller::where('estado', 'activo')
                ->whereNotNull('fecha_vencimiento')
                ->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays(7)->toDateString()])->count(),
        ];

        // Talleres por plan (donut)
        $porPlan = Plan::withCount(['talleres' => fn ($q) => $q->where('estado', 'activo')])
            ->get()->mapWithKeys(fn ($p) => [$p->nombre => $p->talleres_count])->toArray();

        // Talleres por estado
        $porEstado = Taller::selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado')->toArray();

        // Ingresos por mes (últimos 6)
        $ingresos = collect();
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $ingresos->push([
                'label' => $m->translatedFormat('M'),
                'total' => round((float) PagoSuscripcion::whereYear('fecha_pago', $m->year)->whereMonth('fecha_pago', $m->month)->sum('monto'), 2),
            ]);
        }

        $recientes = Taller::with('plan')->latest()->take(6)->get();
        $vencimientos = Taller::with('plan')->where('estado', 'activo')
            ->whereNotNull('fecha_vencimiento')->orderBy('fecha_vencimiento')->take(6)->get();

        return view('admin.dashboard', compact('kpis', 'porPlan', 'porEstado', 'ingresos', 'recientes', 'vencimientos'));
    }
}
