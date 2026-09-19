<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Repuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepuestoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $repuestos = Repuesto::with('proveedor')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('codigo', 'like', "%{$q}%")
                ->orWhere('categoria', 'like', "%{$q}%"))
            ->latest()->paginate(15)->withQueryString();

        $resumen = [
            'items' => Repuesto::count(),
            'stock_bajo' => Repuesto::whereColumn('stock', '<=', 'stock_minimo')->count(),
            'valor' => Repuesto::sum(DB::raw('stock * precio_compra')),
        ];

        return view('modulos.repuestos', compact('repuestos', 'resumen', 'q'));
    }

    public function create()
    {
        return view('modulos.repuesto_form', [
            'repuesto' => new Repuesto(['unidad' => 'UND', 'activo' => true, 'stock' => 0, 'stock_minimo' => 0]),
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Repuesto::create($this->validar($request));

        return redirect()->route('repuestos.index')->with('ok', 'Repuesto registrado correctamente.');
    }

    public function edit(Repuesto $repuesto)
    {
        return view('modulos.repuesto_form', [
            'repuesto' => $repuesto,
            'proveedores' => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Repuesto $repuesto)
    {
        $repuesto->update($this->validar($request));

        return redirect()->route('repuestos.index')->with('ok', 'Repuesto actualizado correctamente.');
    }

    public function destroy(Repuesto $repuesto)
    {
        $repuesto->delete();

        return redirect()->route('repuestos.index')->with('ok', 'Repuesto eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'codigo' => 'nullable|string|max:30',
            'nombre' => 'required|string|max:150',
            'categoria' => 'nullable|string|max:80',
            'unidad' => 'nullable|string|max:20',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:60',
        ], [
            'nombre.required' => 'El nombre del repuesto es obligatorio.',
        ]);

        $data['activo'] = $request->boolean('activo');

        return $data;
    }
}
