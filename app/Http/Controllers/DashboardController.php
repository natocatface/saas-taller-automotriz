<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Vehiculo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $inicioMes = Carbon::now()->startOfMonth();

        $kpis = [
            'ordenes_abiertas' => Orden::whereNotIn('estado', ['entregado', 'anulado'])->count(),
            'ingresos_mes' => Orden::where('estado_pago', 'pagado')
                ->where('updated_at', '>=', $inicioMes)->sum('total'),
            'clientes' => Cliente::count(),
            'vehiculos' => Vehiculo::count(),
            'citas_hoy' => Cita::whereDate('fecha_hora', $hoy)->count(),
            'repuestos_bajos' => Repuesto::whereColumn('stock', '<=', 'stock_minimo')->count(),
        ];

        // Órdenes por estado (para donut)
        $porEstado = Orden::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')->pluck('total', 'estado')->toArray();

        // Ingresos últimos 7 meses (para gráfico de área)
        $meses = collect();
        for ($i = 6; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $total = Orden::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->sum('total');
            $meses->push([
                'label' => $mes->translatedFormat('M'),
                'total' => round($total, 2),
            ]);
        }

        // Top servicios más solicitados (barra horizontal)
        $topServicios = DB::table('orden_servicio')
            ->select('descripcion', DB::raw('SUM(cantidad) as total'))
            ->groupBy('descripcion')
            ->orderByDesc('total')
            ->take(6)->get();

        // Productividad por mecánico (barras)
        $porMecanico = DB::table('ordenes')
            ->join('users', 'ordenes.mecanico_id', '=', 'users.id')
            ->select('users.name', DB::raw('count(*) as total'))
            ->groupBy('users.name')
            ->orderByDesc('total')
            ->take(6)->get();

        // Órdenes recientes
        $recientes = Orden::with(['cliente', 'vehiculo', 'mecanico'])
            ->latest()->take(6)->get();

        // Próximas citas
        $citas = Cita::with(['cliente', 'vehiculo'])
            ->where('fecha_hora', '>=', Carbon::now())
            ->orderBy('fecha_hora')->take(5)->get();

        // Repuestos con stock bajo
        $stockBajo = Repuesto::whereColumn('stock', '<=', 'stock_minimo')
            ->orderBy('stock')->take(5)->get();

        return view('dashboard', compact('kpis', 'porEstado', 'meses', 'topServicios', 'porMecanico', 'recientes', 'citas', 'stockBajo'));
    }
}
