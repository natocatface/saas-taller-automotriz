<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Compra;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->get('desde', Carbon::now()->startOfYear()->toDateString());
        $hasta = $request->get('hasta', Carbon::today()->toDateString());

        // ===== KPIs =====
        $kpis = [
            'ventas' => Comprobante::where('estado', 'emitido')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'ordenes' => Orden::whereBetween('fecha_ingreso', [$desde, $hasta])->count(),
            'compras' => Compra::where('estado', 'registrada')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'ticket' => round((float) Comprobante::where('estado', 'emitido')->whereBetween('fecha', [$desde, $hasta])->avg('total'), 2),
        ];

        // ===== Ventas por mes =====
        $ventasMes = collect();
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $ventasMes->push([
                'label' => $m->translatedFormat('M y'),
                'total' => round((float) Comprobante::where('estado', 'emitido')
                    ->whereYear('fecha', $m->year)->whereMonth('fecha', $m->month)->sum('total'), 2),
            ]);
        }

        // ===== Top servicios =====
        $topServicios = DB::table('orden_servicio')
            ->join('ordenes', 'orden_servicio.orden_id', '=', 'ordenes.id')
            ->whereBetween('ordenes.fecha_ingreso', [$desde, $hasta])
            ->select('orden_servicio.descripcion', DB::raw('SUM(orden_servicio.cantidad) as cant'), DB::raw('SUM(orden_servicio.subtotal) as total'))
            ->groupBy('orden_servicio.descripcion')->orderByDesc('total')->take(8)->get();

        // ===== Top repuestos =====
        $topRepuestos = DB::table('orden_repuesto')
            ->join('ordenes', 'orden_repuesto.orden_id', '=', 'ordenes.id')
            ->whereBetween('ordenes.fecha_ingreso', [$desde, $hasta])
            ->select('orden_repuesto.descripcion', DB::raw('SUM(orden_repuesto.cantidad) as cant'), DB::raw('SUM(orden_repuesto.subtotal) as total'))
            ->groupBy('orden_repuesto.descripcion')->orderByDesc('total')->take(8)->get();

        // ===== Productividad por mecánico =====
        $porMecanico = DB::table('ordenes')
            ->join('users', 'ordenes.mecanico_id', '=', 'users.id')
            ->whereBetween('ordenes.fecha_ingreso', [$desde, $hasta])
            ->select('users.name', DB::raw('count(*) as total'), DB::raw('SUM(ordenes.total) as monto'))
            ->groupBy('users.name')->orderByDesc('total')->take(8)->get();

        // ===== Órdenes por estado =====
        $porEstado = Orden::whereBetween('fecha_ingreso', [$desde, $hasta])
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->toArray();

        return view('modulos.reportes', compact('kpis', 'ventasMes', 'topServicios', 'topRepuestos', 'porMecanico', 'porEstado', 'desde', 'hasta'));
    }
}
