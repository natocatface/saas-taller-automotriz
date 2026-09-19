<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\FacturacionElectronica;
use App\Models\GuiaRemision;
use App\Models\Orden;
use App\Services\Sunat\GreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuiaRemisionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $guias = GuiaRemision::with('cliente')
            ->when($q, fn ($query) => $query->where('serie', 'like', "%{$q}%")
                ->orWhere('numero', 'like', "%{$q}%")
                ->orWhere('destinatario_nombre', 'like', "%{$q}%"))
            ->latest()->paginate(12)->withQueryString();

        return view('modulos.guias', compact('guias', 'q'));
    }

    public function create()
    {
        return view('modulos.guia_form', [
            'guia'      => new GuiaRemision(['fecha_traslado' => now()->toDateString(), 'modalidad' => '02', 'unidad_peso' => 'KGM']),
            'clientes'  => Cliente::orderBy('nombre')->get(),
            'ordenes'   => Orden::with('vehiculo')->latest()->limit(100)->get(),
            'motivos'   => GuiaRemision::motivosTraslado(),
            'modalidades' => GuiaRemision::modalidades(),
            'fe'        => FacturacionElectronica::actual(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha'                 => 'required|date',
            'motivo_codigo'         => 'required|string|max:5',
            'motivo_desc'           => 'nullable|string|max:200',
            'modalidad'             => 'required|in:01,02',
            'fecha_traslado'        => 'required|date',
            'peso_total'            => 'required|numeric|min:0',
            'unidad_peso'           => 'required|string|max:5',
            'num_bultos'            => 'nullable|integer|min:0',
            'partida_ubigeo'        => 'required|string|max:10',
            'partida_direccion'     => 'required|string|max:250',
            'llegada_ubigeo'        => 'required|string|max:10',
            'llegada_direccion'     => 'required|string|max:250',
            'destinatario_tipo_doc' => 'required|string|max:2',
            'destinatario_num_doc'  => 'required|string|max:20',
            'destinatario_nombre'   => 'required|string|max:200',
            'cliente_id'            => 'nullable|exists:clientes,id',
            'orden_id'              => 'nullable|exists:ordenes,id',
            // Transporte
            'transportista_doc'     => 'required_if:modalidad,01|nullable|string|max:20',
            'transportista_nombre'  => 'required_if:modalidad,01|nullable|string|max:200',
            'transportista_mtc'     => 'nullable|string|max:30',
            'vehiculo_placa'        => 'required_if:modalidad,02|nullable|string|max:15',
            'chofer_doc'            => 'required_if:modalidad,02|nullable|string|max:20',
            'chofer_licencia'       => 'required_if:modalidad,02|nullable|string|max:30',
            'chofer_nombre'         => 'required_if:modalidad,02|nullable|string|max:200',
            // Ítems
            'items'                 => 'required|array|min:1',
            'items.*.descripcion'   => 'required|string|max:250',
            'items.*.cantidad'      => 'required|numeric|min:0.01',
            'items.*.unidad'        => 'required|string|max:5',
        ], [
            'items.required' => 'Agrega al menos un bien a trasladar.',
            'transportista_doc.required_if' => 'El RUC del transportista es obligatorio en transporte público.',
            'vehiculo_placa.required_if' => 'La placa del vehículo es obligatoria en transporte privado.',
        ]);

        $serie = 'T001';
        $ultimo = GuiaRemision::where('serie', $serie)->max(DB::raw('CAST(numero AS UNSIGNED)')) ?? 0;

        $guia = GuiaRemision::create(array_merge($data, [
            'serie'   => $serie,
            'numero'  => $ultimo + 1,
            'items'   => array_values($data['items']),
            'user_id' => auth()->id(),
            'estado'  => 'emitido',
            'estado_sunat' => 'pendiente',
        ]));

        // Emisión electrónica (GRE) si está habilitada
        $config = FacturacionElectronica::actual();
        if ($config->habilitado && $config->driver !== 'none') {
            $result = (new GreService($config))->emitir($guia);
            if (! $result->success && $result->estado !== 'anulando') {
                return redirect()->route('guias.index')
                    ->with('error', 'Guía '.$guia->serie_numero.' registrada, pero SUNAT: '.$result->mensaje);
            }
        }

        return redirect()->route('guias.index')->with('ok', 'Guía de remisión '.$guia->serie_numero.' registrada.');
    }

    public function imprimir(GuiaRemision $guia)
    {
        $guia->load(['cliente', 'orden.vehiculo']);
        $config = Configuracion::actual();
        $fe = FacturacionElectronica::actual();

        return view('modulos.guia_print', compact('guia', 'config', 'fe'));
    }

    public function consultar(GuiaRemision $guia)
    {
        $result = (new GreService(FacturacionElectronica::actual()))->consultarTicket($guia);

        return redirect()->route('guias.index')->with($result->success ? 'ok' : 'error', $result->mensaje);
    }
}
