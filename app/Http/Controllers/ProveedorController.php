<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $proveedores = Proveedor::withCount('repuestos')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('ruc', 'like', "%{$q}%")
                ->orWhere('contacto', 'like', "%{$q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('modulos.proveedores', compact('proveedores', 'q'));
    }

    public function create()
    {
        return view('modulos.proveedor_form', ['proveedor' => new Proveedor(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Proveedor::create($this->validar($request));

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('modulos.proveedor_form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $proveedor->update($this->validar($request));

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('ok', 'Proveedor eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'ruc' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:120',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
        ], [
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
        ]);

        $data['activo'] = $request->boolean('activo');

        return $data;
    }
}
