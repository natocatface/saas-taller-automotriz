<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $clientes = Cliente::withCount('vehiculos', 'ordenes')
            ->when($q, fn ($query) => $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('apellidos', 'like', "%{$q}%")
                ->orWhere('razon_social', 'like', "%{$q}%")
                ->orWhere('documento', 'like', "%{$q}%")
                ->orWhere('telefono', 'like', "%{$q}%"))
            ->latest()->paginate(12)->withQueryString();

        return view('modulos.clientes', compact('clientes', 'q'));
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('vehiculos');
        $ordenes = $cliente->ordenes()->with('vehiculo')->latest()->get();
        $comprobantes = \App\Models\Comprobante::where('cliente_id', $cliente->id)->latest()->get();

        $stats = [
            'vehiculos' => $cliente->vehiculos->count(),
            'ordenes' => $ordenes->count(),
            'facturado' => $comprobantes->where('estado', 'emitido')->sum('total'),
            'ultima_visita' => optional($ordenes->first())->fecha_ingreso,
        ];

        return view('modulos.cliente_show', compact('cliente', 'ordenes', 'comprobantes', 'stats'));
    }

    public function create()
    {
        return view('modulos.cliente_form', ['cliente' => new Cliente(['tipo' => 'persona', 'tipo_documento' => 'DNI', 'activo' => true])]);
    }

    public function store(Request $request)
    {
        Cliente::create($this->validar($request));

        return redirect()->route('clientes.index')->with('ok', 'Cliente registrado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        return view('modulos.cliente_form', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request));

        return redirect()->route('clientes.index')->with('ok', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('ok', 'Cliente eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'tipo' => 'required|in:persona,empresa',
            'tipo_documento' => 'required|string|max:20',
            'documento' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:150',
            'apellidos' => 'nullable|string|max:150',
            'razon_social' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:200',
            'ciudad' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $data['activo'] = $request->boolean('activo');

        return $data;
    }
}
