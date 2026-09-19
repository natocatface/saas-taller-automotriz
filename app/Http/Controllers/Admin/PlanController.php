<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::withCount('talleres')->orderBy('orden')->orderBy('precio_mensual')->get();

        return view('admin.planes.index', compact('planes'));
    }

    public function create()
    {
        return view('admin.planes.form', ['plan' => new Plan(['activo' => true, 'limite_usuarios' => 3, 'limite_ordenes' => 100])]);
    }

    public function store(Request $request)
    {
        Plan::create($this->validar($request));

        return redirect()->route('admin.planes.index')->with('ok', 'Plan creado correctamente.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.planes.form', ['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan)
    {
        $plan->update($this->validar($request));

        return redirect()->route('admin.planes.index')->with('ok', 'Plan actualizado correctamente.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->talleres()->count() > 0) {
            return redirect()->route('admin.planes.index')->with('ok', 'No se puede eliminar: hay talleres usando este plan.');
        }
        $plan->delete();

        return redirect()->route('admin.planes.index')->with('ok', 'Plan eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:80',
            'precio_mensual' => 'required|numeric|min:0',
            'precio_anual' => 'required|numeric|min:0',
            'limite_usuarios' => 'required|integer|min:0',
            'limite_ordenes' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'caracteristicas' => 'nullable|string',
            'orden' => 'nullable|integer|min:0',
        ], [
            'nombre.required' => 'El nombre del plan es obligatorio.',
        ]);

        $data['destacado'] = $request->boolean('destacado');
        $data['activo'] = $request->boolean('activo');
        $data['orden'] = $data['orden'] ?? 0;

        return $data;
    }
}
