<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Orden;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    private function excel(string $archivo, array $columnas, array $filas)
    {
        $html = view('exports.tabla', compact('columnas', 'filas'))->render();

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$archivo.'.xls"',
        ]);
    }

    public function ordenes()
    {
        $estados = Orden::estados();
        $filas = Orden::with(['cliente', 'vehiculo', 'mecanico'])->latest()->get()->map(fn ($o) => [
            $o->numero,
            $o->cliente->nombre_completo ?? '',
            $o->vehiculo->placa ?? '',
            optional($o->fecha_ingreso)->format('d/m/Y'),
            $o->mecanico->name ?? 'Sin asignar',
            $estados[$o->estado] ?? $o->estado,
            ucfirst($o->estado_pago),
            number_format($o->total, 2),
        ])->toArray();

        return $this->excel('ordenes_'.date('Ymd'),
            ['N° Orden', 'Cliente', 'Vehículo', 'Ingreso', 'Mecánico', 'Estado', 'Pago', 'Total S/'], $filas);
    }

    public function ventas()
    {
        $filas = Comprobante::with('cliente')->latest()->get()->map(fn ($c) => [
            $c->serie_numero,
            ucfirst($c->tipo),
            $c->cliente->nombre_completo ?? 'Varios',
            optional($c->fecha)->format('d/m/Y'),
            ucfirst($c->metodo_pago ?? ''),
            ucfirst($c->estado),
            number_format($c->total, 2),
        ])->toArray();

        return $this->excel('ventas_'.date('Ymd'),
            ['Comprobante', 'Tipo', 'Cliente', 'Fecha', 'Método', 'Estado', 'Total S/'], $filas);
    }

    public function inventario()
    {
        $filas = Repuesto::with('proveedor')->orderBy('nombre')->get()->map(fn ($r) => [
            $r->codigo,
            $r->nombre,
            $r->categoria,
            $r->proveedor->nombre ?? '',
            number_format($r->precio_compra, 2),
            number_format($r->precio_venta, 2),
            $r->stock,
            $r->stock_minimo,
            $r->stock <= $r->stock_minimo ? 'STOCK BAJO' : 'OK',
        ])->toArray();

        return $this->excel('inventario_'.date('Ymd'),
            ['Código', 'Repuesto', 'Categoría', 'Proveedor', 'P. Compra', 'P. Venta', 'Stock', 'Mínimo', 'Estado'], $filas);
    }

    public function reportesPdf(Request $request)
    {
        $desde = $request->get('desde', Carbon::now()->startOfYear()->toDateString());
        $hasta = $request->get('hasta', Carbon::today()->toDateString());

        $kpis = [
            'ventas' => Comprobante::where('estado', 'emitido')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'ordenes' => Orden::whereBetween('fecha_ingreso', [$desde, $hasta])->count(),
            'compras' => \App\Models\Compra::where('estado', 'registrada')->whereBetween('fecha', [$desde, $hasta])->sum('total'),
            'ticket' => round((float) Comprobante::where('estado', 'emitido')->whereBetween('fecha', [$desde, $hasta])->avg('total'), 2),
        ];

        $topServicios = DB::table('orden_servicio')
            ->join('ordenes', 'orden_servicio.orden_id', '=', 'ordenes.id')
            ->whereBetween('ordenes.fecha_ingreso', [$desde, $hasta])
            ->select('orden_servicio.descripcion', DB::raw('SUM(orden_servicio.cantidad) as cant'), DB::raw('SUM(orden_servicio.subtotal) as total'))
            ->groupBy('orden_servicio.descripcion')->orderByDesc('total')->take(15)->get();

        $porMecanico = DB::table('ordenes')
            ->join('users', 'ordenes.mecanico_id', '=', 'users.id')
            ->whereBetween('ordenes.fecha_ingreso', [$desde, $hasta])
            ->select('users.name', DB::raw('count(*) as total'), DB::raw('SUM(ordenes.total) as monto'))
            ->groupBy('users.name')->orderByDesc('total')->get();

        $config = \App\Models\Configuracion::actual();

        return view('exports.reportes_print', compact('kpis', 'topServicios', 'porMecanico', 'desde', 'hasta', 'config'));
    }
}
