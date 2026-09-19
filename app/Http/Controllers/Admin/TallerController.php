<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Taller;
use Illuminate\Http\Request;

class TallerController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $estado = $request->get('estado', '');

        $talleres = Taller::with('plan')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('ruc', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"))
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->latest()->paginate(12)->withQueryString();

        return view('admin.talleres.index', compact('talleres', 'q', 'estado'));
    }

    public function create()
    {
        return view('admin.talleres.form', [
            'taller' => new Taller(['estado' => 'prueba', 'periodo' => 'mensual', 'fecha_inicio' => now()->toDateString(), 'fecha_vencimiento' => now()->addDays(30)->toDateString()]),
            'planes' => Plan::where('activo', true)->orderBy('orden')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Taller::create($this->validar($request));

        return redirect()->route('admin.talleres.index')->with('ok', 'Taller registrado correctamente.');
    }

    public function show(Taller $taller)
    {
        $taller->load(['plan', 'pagos.plan']);

        return view('admin.talleres.show', compact('taller'));
    }

    public function edit(Taller $taller)
    {
        return view('admin.talleres.form', [
            'taller' => $taller,
            'planes' => Plan::where('activo', true)->orderBy('orden')->get(),
        ]);
    }

    public function update(Request $request, Taller $taller)
    {
        $taller->update($this->validar($request));

        return redirect()->route('admin.talleres.index')->with('ok', 'Taller actualizado correctamente.');
    }

    public function destroy(Taller $taller)
    {
        $taller->delete();

        return redirect()->route('admin.talleres.index')->with('ok', 'Taller eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:150',
            'ruc' => 'nullable|string|max:20',
            'contacto_nombre' => 'nullable|string|max:120',
            'email' => 'nullable|email|max:150',
            'telefono' => 'nullable|string|max:30',
            'ciudad' => 'nullable|string|max:80',
            'plan_id' => 'nullable|exists:planes,id',
            'estado' => 'required|in:prueba,activo,suspendido,cancelado',
            'periodo' => 'required|in:mensual,anual',
            'fecha_inicio' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
            'notas' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre del taller es obligatorio.',
        ]);
    }
}
