<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Configuracion;
use App\Models\MovimientoCaja;
use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $compras = Compra::with('proveedor')
            ->when($q, fn ($query) => $query->where('numero', 'like', "%{$q}%")
                ->orWhereHas('proveedor', fn ($p) => $p->where('nombre', 'like', "%{$q}%")))
            ->latest()->paginate(12)->withQueryString();

        $resumen = [
            'total' => Compra::where('estado', 'registrada')->count(),
            'mes' => Compra::where('estado', 'registrada')->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->sum('total'),
            'proveedores' => Proveedor::where('activo', true)->count(),
        ];

        return view('modulos.compras', compact('compras', 'resumen', 'q'));
    }

    public function create()
    {
        return view('modulos.compra_form', [
            'compra' => new Compra(['fecha' => now()->toDateString(), 'tipo_documento' => 'factura']),
            'proveedores' => Proveedor::where('activo', true)->orderBy('nombre')->get(),
            'repuestos' => Repuesto::where('activo', true)->orderBy('nombre')->get(),
            'igv' => (float) Configuracion::actual()->igv,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha' => 'required|date',
            'tipo_documento' => 'required|in:factura,boleta,guia',
            'documento_ref' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'items' => 'required|array|min:1',
        ], [
            'proveedor_id.required' => 'Selecciona un proveedor.',
            'items.required' => 'Agrega al menos un repuesto a la compra.',
            'items.min' => 'Agrega al menos un repuesto a la compra.',
        ]);

        DB::transaction(function () use ($request, $data) {
            $igvPct = (float) Configuracion::actual()->igv;

            $compra = Compra::create([
                'numero' => 'CMP-'.str_pad((Compra::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT),
                'proveedor_id' => $data['proveedor_id'],
                'user_id' => auth()->id(),
                'fecha' => $data['fecha'],
                'tipo_documento' => $data['tipo_documento'],
                'documento_ref' => $data['documento_ref'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $subtotal = 0;
            foreach ((array) $request->input('items', []) as $it) {
                $desc = trim($it['descripcion'] ?? '');
                if ($desc === '') {
                    continue;
                }
                $cant = max(1, (int) ($it['cantidad'] ?? 1));
                $precio = (float) ($it['precio'] ?? 0);
                $sub = $cant * $precio;
                $subtotal += $sub;

                $compra->detalles()->create([
                    'repuesto_id' => $it['repuesto_id'] ?? null,
                    'descripcion' => $desc,
                    'cantidad' => $cant,
                    'precio' => $precio,
                    'subtotal' => $sub,
                ]);

                // Reponer stock y actualizar precio de compra
                if (! empty($it['repuesto_id'])) {
                    $rep = Repuesto::find($it['repuesto_id']);
                    if ($rep) {
                        $rep->stock += $cant;
                        $rep->precio_compra = $precio;
                        $rep->save();
                    }
                }
            }

            $impuesto = round($subtotal * $igvPct / 100, 2);
            $total = $subtotal + $impuesto;
            $compra->update(['subtotal' => $subtotal, 'impuesto' => $impuesto, 'total' => $total]);

            // Registrar egreso en caja
            MovimientoCaja::create([
                'tipo' => 'egreso',
                'concepto' => 'Compra '.$compra->numero.' · '.$compra->proveedor->nombre,
                'monto' => $total,
                'fecha' => $data['fecha'],
                'metodo_pago' => 'efectivo',
                'user_id' => auth()->id(),
            ]);
        });

        return redirect()->route('compras.index')->with('ok', 'Compra registrada y stock actualizado.');
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'detalles', 'user']);

        return view('modulos.compra_show', compact('compra'));
    }

    public function destroy(Compra $compra)
    {
        DB::transaction(function () use ($compra) {
            if ($compra->estado === 'registrada') {
                // Revertir stock
                foreach ($compra->detalles()->whereNotNull('repuesto_id')->get() as $d) {
                    $rep = Repuesto::find($d->repuesto_id);
                    if ($rep) {
                        $rep->stock = max(0, $rep->stock - $d->cantidad);
                        $rep->save();
                    }
                }
            }
            $compra->detalles()->delete();
            $compra->delete();
        });

        return redirect()->route('compras.index')->with('ok', 'Compra anulada y stock revertido.');
    }
}
