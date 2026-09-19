<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $vehiculos = Vehiculo::with('cliente')->withCount('ordenes')
            ->when($q, fn ($query) => $query->where('placa', 'like', "%{$q}%")
                ->orWhere('marca', 'like', "%{$q}%")
                ->orWhere('modelo', 'like', "%{$q}%"))
            ->latest()->paginate(12)->withQueryString();

        return view('modulos.vehiculos', compact('vehiculos', 'q'));
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load('cliente');
        $ordenes = $vehiculo->ordenes()->with('mecanico')->latest()->get();

        $stats = [
            'total_ordenes' => $ordenes->count(),
            'gasto' => $ordenes->where('estado', '!=', 'anulado')->sum('total'),
            'ultimo_km' => $vehiculo->kilometraje,
            'ultima_visita' => optional($ordenes->first())->fecha_ingreso,
        ];

        return view('modulos.vehiculo_show', compact('vehiculo', 'ordenes', 'stats'));
    }

    public function create()
    {
        return view('modulos.vehiculo_form', [
            'vehiculo' => new Vehiculo(['combustible' => 'gasolina']),
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Vehiculo::create($this->validar($request));

        return redirect()->route('vehiculos.index')->with('ok', 'Vehículo registrado correctamente.');
    }

    public function edit(Vehiculo $vehiculo)
    {
        return view('modulos.vehiculo_form', [
            'vehiculo' => $vehiculo,
            'clientes' => Cliente::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $vehiculo->update($this->validar($request));

        return redirect()->route('vehiculos.index')->with('ok', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $vehiculo->delete();

        return redirect()->route('vehiculos.index')->with('ok', 'Vehículo eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa' => 'required|string|max:20',
            'marca' => 'nullable|string|max:60',
            'modelo' => 'nullable|string|max:60',
            'anio' => 'nullable|integer|min:1950|max:'.(date('Y') + 1),
            'color' => 'nullable|string|max:40',
            'vin' => 'nullable|string|max:60',
            'motor' => 'nullable|string|max:60',
            'combustible' => 'required|in:gasolina,diesel,glp,gnv,electrico,hibrido',
            'transmision' => 'nullable|in:manual,automatica',
            'kilometraje' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string',
        ], [
            'cliente_id.required' => 'Selecciona el propietario.',
            'placa.required' => 'La placa es obligatoria.',
        ]);
    }
}
