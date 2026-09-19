<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PagoSuscripcion;
use App\Models\Taller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $pagos = PagoSuscripcion::with(['taller', 'plan'])
            ->when($q, fn ($query) => $query->where('referencia', 'like', "%{$q}%")
                ->orWhereHas('taller', fn ($t) => $t->where('nombre', 'like', "%{$q}%")))
            ->latest('fecha_pago')->paginate(15)->withQueryString();

        $resumen = [
            'total' => PagoSuscripcion::sum('monto'),
            'mes' => PagoSuscripcion::whereMonth('fecha_pago', now()->month)->whereYear('fecha_pago', now()->year)->sum('monto'),
            'cantidad' => PagoSuscripcion::count(),
        ];

        $talleres = Taller::orderBy('nombre')->get();

        return view('admin.pagos.index', compact('pagos', 'resumen', 'talleres', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'taller_id' => 'required|exists:talleres,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'periodo' => 'required|in:mensual,anual',
            'metodo' => 'required|string|max:30',
            'referencia' => 'nullable|string|max:60',
        ], [
            'taller_id.required' => 'Selecciona el taller.',
            'monto.required' => 'Ingresa el monto del pago.',
        ]);

        DB::transaction(function () use ($data) {
            $taller = Taller::findOrFail($data['taller_id']);
            $base = ($taller->fecha_vencimiento && Carbon::parse($taller->fecha_vencimiento)->isFuture())
                ? Carbon::parse($taller->fecha_vencimiento)
                : Carbon::today();
            $cubreHasta = $data['periodo'] === 'anual' ? $base->copy()->addYear() : $base->copy()->addMonth();

            PagoSuscripcion::create([
                'taller_id' => $taller->id,
                'plan_id' => $taller->plan_id,
                'monto' => $data['monto'],
                'fecha_pago' => $data['fecha_pago'],
                'periodo' => $data['periodo'],
                'metodo' => $data['metodo'],
                'referencia' => $data['referencia'] ?? null,
                'cubre_hasta' => $cubreHasta->toDateString(),
            ]);

            // Renovar suscripción
            $taller->update([
                'estado' => 'activo',
                'periodo' => $data['periodo'],
                'fecha_vencimiento' => $cubreHasta->toDateString(),
            ]);
        });

        return redirect()->route('admin.pagos.index')->with('ok', 'Pago registrado y suscripción renovada.');
    }

    public function destroy(PagoSuscripcion $pago)
    {
        $pago->delete();

        return redirect()->route('admin.pagos.index')->with('ok', 'Pago eliminado.');
    }
}
