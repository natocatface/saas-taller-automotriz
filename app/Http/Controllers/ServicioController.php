<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $servicios = Servicio::when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
            ->orWhere('categoria', 'like', "%{$q}%")
            ->orWhere('codigo', 'like', "%{$q}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('modulos.servicios', compact('servicios', 'q'));
    }

    public function create()
    {
        return view('modulos.servicio_form', ['servicio' => new Servicio(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Servicio::create($this->validar($request));

        return redirect()->route('servicios.index')->with('ok', 'Servicio registrado correctamente.');
    }

    public function edit(Servicio $servicio)
    {
        return view('modulos.servicio_form', compact('servicio'));
    }

    public function update(Request $request, Servicio $servicio)
    {
        $servicio->update($this->validar($request));

        return redirect()->route('servicios.index')->with('ok', 'Servicio actualizado correctamente.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();

        return redirect()->route('servicios.index')->with('ok', 'Servicio eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'codigo' => 'nullable|string|max:30',
            'nombre' => 'required|string|max:150',
            'categoria' => 'nullable|string|max:80',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'duracion_min' => 'nullable|integer|min:0',
        ], [
            'nombre.required' => 'El nombre del servicio es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
        ]);

        $data['activo'] = $request->boolean('activo');

        return $data;
    }
}
