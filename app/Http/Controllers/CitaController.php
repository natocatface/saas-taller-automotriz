<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $citas = Cita::with(['cliente', 'vehiculo', 'mecanico'])
            ->when($q, fn ($query) => $query->where('titulo', 'like', "%{$q}%")
                ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%")))
            ->orderBy('fecha_hora')->paginate(15)->withQueryString();

        return view('modulos.citas', compact('citas', 'q'));
    }

    public function calendario(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));
        try {
            $inicio = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $mes.'-01')->startOfMonth();
        } catch (\Exception $e) {
            $inicio = now()->startOfMonth();
        }
        $fin = $inicio->copy()->endOfMonth();

        $citas = Cita::with(['cliente', 'vehiculo'])
            ->whereBetween('fecha_hora', [$inicio->copy()->startOfDay(), $fin->copy()->endOfDay()])
            ->orderBy('fecha_hora')->get()
            ->groupBy(fn ($c) => $c->fecha_hora->format('Y-m-d'));

        return view('modulos.citas_calendario', compact('citas', 'inicio'));
    }

    public function create()
    {
        return view('modulos.cita_form', $this->formData() + [
            'cita' => new Cita(['fecha_hora' => now()->addDay()->setHour(9)->setMinute(0), 'duracion_min' => 60, 'estado' => 'pendiente']),
        ]);
    }

    public function store(Request $request)
    {
        Cita::create($this->validar($request));

        return redirect()->route('citas.index')->with('ok', 'Cita agendada correctamente.');
    }

    public function edit(Cita $cita)
    {
        return view('modulos.cita_form', $this->formData() + ['cita' => $cita]);
    }

    public function update(Request $request, Cita $cita)
    {
        $cita->update($this->validar($request));

        return redirect()->route('citas.index')->with('ok', 'Cita actualizada correctamente.');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index')->with('ok', 'Cita eliminada.');
    }

    private function formData(): array
    {
        return [
            'clientes' => Cliente::orderBy('nombre')->get(),
            'vehiculos' => Vehiculo::orderBy('placa')->get(['id', 'placa', 'marca', 'modelo', 'cliente_id']),
            'mecanicos' => User::whereIn('rol', ['mecanico', 'admin', 'gerente'])->orderBy('name')->get(),
        ];
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'mecanico_id' => 'nullable|exists:users,id',
            'titulo' => 'required|string|max:150',
            'fecha_hora' => 'required|date',
            'duracion_min' => 'required|integer|min:15',
            'motivo' => 'nullable|string',
            'estado' => 'required|in:pendiente,confirmada,atendida,cancelada',
        ], [
            'titulo.required' => 'El título de la cita es obligatorio.',
            'fecha_hora.required' => 'La fecha y hora son obligatorias.',
        ]);
    }
}
