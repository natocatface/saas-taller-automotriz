<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenController extends Controller
{
    /* ============ LISTADO CON FILTROS ============ */
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $estado = $request->get('estado', '');

        $ordenes = Orden::with(['cliente', 'vehiculo', 'mecanico'])
            ->when($q, function ($query) use ($q) {
                $query->where('numero', 'like', "%{$q}%")
                    ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%")->orWhere('razon_social', 'like', "%{$q}%")->orWhere('apellidos', 'like', "%{$q}%"))
                    ->orWhereHas('vehiculo', fn ($v) => $v->where('placa', 'like', "%{$q}%"));
            })
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->latest()->paginate(12)->withQueryString();

        $resumen = [
            'total' => Orden::count(),
            'abiertas' => Orden::whereNotIn('estado', ['entregado', 'anulado'])->count(),
            'terminadas' => Orden::where('estado', 'terminado')->count(),
            'entregadas' => Orden::where('estado', 'entregado')->count(),
        ];

        return view('modulos.ordenes', compact('ordenes', 'resumen', 'q', 'estado'));
    }

    /* ============ CREAR ============ */
    public function create()
    {
        return view('modulos.orden_form', $this->formData() + [
            'orden' => new Orden(['fecha_ingreso' => now()->toDateString(), 'prioridad' => 'media', 'estado' => 'recepcion']),
            'itemsServicios' => [],
            'itemsRepuestos' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        DB::transaction(function () use ($request, $data) {
            $orden = Orden::create([
                'numero' => $this->siguienteNumero(),
                'cliente_id' => $data['cliente_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'mecanico_id' => $data['mecanico_id'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'fecha_entrega' => $data['fecha_entrega'] ?? null,
                'kilometraje' => $data['kilometraje'] ?? null,
                'diagnostico' => $data['diagnostico'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => $data['estado'],
                'prioridad' => $data['prioridad'],
                'descuento' => $data['descuento'] ?? 0,
            ]);

            $this->guardarItems($request, $orden);
            $this->recalcular($orden, $data['descuento'] ?? 0);
        });

        return redirect()->route('ordenes.index')->with('ok', 'Orden de servicio creada correctamente.');
    }

    /* ============ VER ============ */
    public function show(Orden $orden)
    {
        $orden->load(['cliente', 'vehiculo', 'mecanico', 'servicios', 'repuestos']);

        return view('modulos.orden_show', compact('orden'));
    }

    /* ============ EDITAR ============ */
    public function edit(Orden $orden)
    {
        $orden->load(['servicios', 'repuestos']);

        return view('modulos.orden_form', $this->formData() + [
            'orden' => $orden,
            'itemsServicios' => $orden->servicios,
            'itemsRepuestos' => $orden->repuestos,
        ]);
    }

    public function update(Request $request, Orden $orden)
    {
        $data = $this->validar($request);

        DB::transaction(function () use ($request, $orden, $data) {
            // Devolver stock de los repuestos anteriores antes de reemplazar
            $this->devolverStock($orden);
            $orden->servicios()->delete();
            $orden->repuestos()->delete();

            $orden->update([
                'cliente_id' => $data['cliente_id'],
                'vehiculo_id' => $data['vehiculo_id'],
                'mecanico_id' => $data['mecanico_id'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'fecha_entrega' => $data['fecha_entrega'] ?? null,
                'kilometraje' => $data['kilometraje'] ?? null,
                'diagnostico' => $data['diagnostico'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => $data['estado'],
                'prioridad' => $data['prioridad'],
            ]);

            $this->guardarItems($request, $orden);
            $this->recalcular($orden, $data['descuento'] ?? 0);
        });

        return redirect()->route('ordenes.show', $orden)->with('ok', 'Orden actualizada correctamente.');
    }

    /* ============ CAMBIAR ESTADO ============ */
    public function cambiarEstado(Request $request, Orden $orden)
    {
        $request->validate([
            'estado' => 'required|in:recepcion,diagnostico,en_proceso,esperando_repuestos,terminado,entregado,anulado',
            'estado_pago' => 'nullable|in:pendiente,parcial,pagado',
        ]);

        // Si se anula, devolver el stock
        if ($request->estado === 'anulado' && $orden->estado !== 'anulado') {
            $this->devolverStock($orden);
        }

        $orden->update([
            'estado' => $request->estado,
            'estado_pago' => $request->estado_pago ?? $orden->estado_pago,
            'fecha_entrega' => in_array($request->estado, ['terminado', 'entregado']) && ! $orden->fecha_entrega
                ? now()->toDateString() : $orden->fecha_entrega,
        ]);

        return back()->with('ok', 'Estado de la orden actualizado.');
    }

    /* ============ ELIMINAR ============ */
    public function destroy(Orden $orden)
    {
        DB::transaction(function () use ($orden) {
            if ($orden->estado !== 'anulado') {
                $this->devolverStock($orden);
            }
            $orden->servicios()->delete();
            $orden->repuestos()->delete();
            $orden->delete();
        });

        return redirect()->route('ordenes.index')->with('ok', 'Orden eliminada.');
    }

    /* ============ IMPRIMIR / PDF ============ */
    public function imprimir(Orden $orden)
    {
        $orden->load(['cliente', 'vehiculo', 'mecanico', 'servicios', 'repuestos']);

        return view('modulos.orden_print', compact('orden'));
    }

    /* ===================== HELPERS ===================== */

    private function formData(): array
    {
        return [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'vehiculos' => Vehiculo::orderBy('placa')->get(['id', 'placa', 'marca', 'modelo', 'cliente_id']),
            'mecanicos' => User::whereIn('rol', ['mecanico', 'admin', 'gerente'])->orderBy('name')->get(),
            'servicios' => Servicio::where('activo', true)->orderBy('nombre')->get(),
            'repuestos' => Repuesto::where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'fecha_ingreso' => 'required|date',
            'fecha_entrega' => 'nullable|date',
            'kilometraje' => 'nullable|integer|min:0',
            'diagnostico' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:recepcion,diagnostico,en_proceso,esperando_repuestos,terminado,entregado,anulado',
            'prioridad' => 'required|in:baja,media,alta',
            'descuento' => 'nullable|numeric|min:0',
            'servicios' => 'nullable|array',
            'repuestos' => 'nullable|array',
        ], [
            'cliente_id.required' => 'Selecciona un cliente.',
            'vehiculo_id.required' => 'Selecciona un vehículo.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
        ]);
    }

    private function guardarItems(Request $request, Orden $orden): void
    {
        foreach ((array) $request->input('servicios', []) as $s) {
            $desc = trim($s['descripcion'] ?? '');
            if ($desc === '') {
                continue;
            }
            $cant = max(1, (int) ($s['cantidad'] ?? 1));
            $precio = (float) ($s['precio'] ?? 0);
            $orden->servicios()->create([
                'servicio_id' => $s['servicio_id'] ?? null,
                'descripcion' => $desc,
                'cantidad' => $cant,
                'precio' => $precio,
                'subtotal' => $cant * $precio,
            ]);
        }

        foreach ((array) $request->input('repuestos', []) as $r) {
            $desc = trim($r['descripcion'] ?? '');
            if ($desc === '') {
                continue;
            }
            $cant = max(1, (int) ($r['cantidad'] ?? 1));
            $precio = (float) ($r['precio'] ?? 0);
            $orden->repuestos()->create([
                'repuesto_id' => $r['repuesto_id'] ?? null,
                'descripcion' => $desc,
                'cantidad' => $cant,
                'precio' => $precio,
                'subtotal' => $cant * $precio,
            ]);

            // Control de stock automático
            if (! empty($r['repuesto_id'])) {
                $rep = Repuesto::find($r['repuesto_id']);
                if ($rep) {
                    $rep->stock = max(0, $rep->stock - $cant);
                    $rep->save();
                }
            }
        }
    }

    private function devolverStock(Orden $orden): void
    {
        foreach ($orden->repuestos()->whereNotNull('repuesto_id')->get() as $item) {
            $rep = Repuesto::find($item->repuesto_id);
            if ($rep) {
                $rep->stock += $item->cantidad;
                $rep->save();
            }
        }
    }

    private function recalcular(Orden $orden, float $descuento = 0): void
    {
        $subtotal = $orden->servicios()->sum('subtotal') + $orden->repuestos()->sum('subtotal');
        $base = max(0, $subtotal - $descuento);
        $impuesto = round($base * 0.18, 2);

        $orden->update([
            'subtotal' => $subtotal,
            'descuento' => $descuento,
            'impuesto' => $impuesto,
            'total' => $base + $impuesto,
        ]);
    }

    private function siguienteNumero(): string
    {
        $ultimo = Orden::max('id') ?? 0;

        return 'OT-'.str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }
}
