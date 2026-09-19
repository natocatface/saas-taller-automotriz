<?php

namespace App\Http\Controllers;

use App\Models\MovimientoCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->get('desde', Carbon::today()->toDateString());
        $hasta = $request->get('hasta', Carbon::today()->toDateString());

        $movimientos = MovimientoCaja::with('user')
            ->whereBetween('fecha', [$desde, $hasta])
            ->latest()->paginate(15)->withQueryString();

        $ingresosHoy = MovimientoCaja::whereDate('fecha', Carbon::today())->where('tipo', 'ingreso')->sum('monto');
        $egresosHoy = MovimientoCaja::whereDate('fecha', Carbon::today())->where('tipo', 'egreso')->sum('monto');

        $ingresosRango = MovimientoCaja::whereBetween('fecha', [$desde, $hasta])->where('tipo', 'ingreso')->sum('monto');
        $egresosRango = MovimientoCaja::whereBetween('fecha', [$desde, $hasta])->where('tipo', 'egreso')->sum('monto');

        $resumen = [
            'ingresos_hoy' => $ingresosHoy,
            'egresos_hoy' => $egresosHoy,
            'saldo_hoy' => $ingresosHoy - $egresosHoy,
            'saldo_total' => MovimientoCaja::where('tipo', 'ingreso')->sum('monto') - MovimientoCaja::where('tipo', 'egreso')->sum('monto'),
            'ingresos_rango' => $ingresosRango,
            'egresos_rango' => $egresosRango,
        ];

        return view('modulos.caja', compact('movimientos', 'resumen', 'desde', 'hasta'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo' => 'required|in:ingreso,egreso',
            'concepto' => 'required|string|max:150',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string|max:30',
        ], [
            'concepto.required' => 'El concepto es obligatorio.',
            'monto.required' => 'Ingresa el monto.',
            'monto.min' => 'El monto debe ser mayor a cero.',
        ]);

        $data['user_id'] = auth()->id();
        MovimientoCaja::create($data);

        return redirect()->route('caja.index')->with('ok', 'Movimiento registrado correctamente.');
    }

    public function destroy(MovimientoCaja $movimiento)
    {
        $movimiento->delete();

        return redirect()->route('caja.index')->with('ok', 'Movimiento eliminado.');
    }
}
