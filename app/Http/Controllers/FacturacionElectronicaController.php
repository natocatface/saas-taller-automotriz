<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\FacturacionElectronica;
use App\Services\Sunat\Drivers\GreenterDriver;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;

class FacturacionElectronicaController extends Controller
{
    public function edit()
    {
        $config = FacturacionElectronica::actual();

        return view('modulos.facturacion_electronica', [
            'config'             => $config,
            'greenterInstalado'  => GreenterDriver::disponible(),
            'certificadoExiste'  => $config->certificadoExiste(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'habilitado'        => 'nullable|boolean',
            'emitir_automatico' => 'nullable|boolean',
            'driver'            => 'required|in:none,greenter',
            'modo'              => 'required|in:beta,produccion',

            'ruc'               => 'required|string|size:11',
            'razon_social'      => 'required|string|max:200',
            'nombre_comercial'  => 'nullable|string|max:200',
            'direccion_fiscal'  => 'nullable|string|max:250',
            'ubigeo'            => 'nullable|string|max:10',
            'departamento'      => 'nullable|string|max:100',
            'provincia'         => 'nullable|string|max:100',
            'distrito'          => 'nullable|string|max:100',

            'usuario_sol'       => 'nullable|string|max:60',
            'clave_sol'         => 'nullable|string|max:100',
            'certificado_path'  => 'nullable|string|max:250',
            'certificado_password' => 'nullable|string|max:100',
            'certificado_file'  => 'nullable|file|max:5120', // .pem / .pfx hasta 5MB
            'client_id'         => 'nullable|string|max:120',
            'client_secret'     => 'nullable|string|max:200',
        ], [
            'ruc.required'  => 'El RUC del emisor es obligatorio.',
            'ruc.size'      => 'El RUC debe tener 11 dígitos.',
            'razon_social.required' => 'La razón social es obligatoria.',
        ]);

        $config = FacturacionElectronica::actual();

        $config->habilitado        = $request->boolean('habilitado');
        $config->emitir_automatico = $request->boolean('emitir_automatico');
        $config->driver            = $data['driver'];
        $config->modo              = $data['modo'];
        $config->ruc               = $data['ruc'];
        $config->razon_social      = $data['razon_social'];
        $config->nombre_comercial  = $data['nombre_comercial'] ?? null;
        $config->direccion_fiscal  = $data['direccion_fiscal'] ?? null;
        $config->ubigeo            = $data['ubigeo'] ?? null;
        $config->departamento      = $data['departamento'] ?? null;
        $config->provincia         = $data['provincia'] ?? null;
        $config->distrito          = $data['distrito'] ?? null;
        $config->usuario_sol       = $data['usuario_sol'] ?? null;
        $config->client_id         = $data['client_id'] ?? null;

        // Solo actualiza secretos si el usuario escribió un valor nuevo
        if ($request->filled('clave_sol')) {
            $config->clave_sol = $data['clave_sol'];
        }
        if ($request->filled('certificado_password')) {
            $config->certificado_password = $data['certificado_password'];
        }
        if ($request->filled('client_secret')) {
            $config->client_secret = $data['client_secret'];
        }

        // Carga de certificado por archivo (opcional)
        if ($request->hasFile('certificado_file')) {
            $file = $request->file('certificado_file');
            $nombre = 'certificate_'.$config->ruc.'.'.$file->getClientOriginalExtension();
            $file->storeAs('facturacion/pe', $nombre, 'local');
            $config->certificado_path = storage_path('app/facturacion/pe/'.$nombre);
        } elseif ($request->filled('certificado_path')) {
            $config->certificado_path = $data['certificado_path'];
        }

        $config->save();

        return redirect()->route('facturacion.electronica.index')
            ->with('ok', 'Configuración de facturación electrónica guardada.');
    }

    /** Prueba la conexión con SUNAT usando el driver configurado. */
    public function probar(Request $request)
    {
        $result = (new SunatService(FacturacionElectronica::actual()))->probarConexion();

        $tipo = $result->success ? 'ok' : 'error';

        return redirect()->route('facturacion.electronica.index')
            ->with($tipo, $result->mensaje);
    }

    /** Tablero de estado de los comprobantes ante SUNAT. */
    public function monitor()
    {
        $config = FacturacionElectronica::actual();

        $conteo = Comprobante::where('estado', 'emitido')
            ->selectRaw('estado_sunat, COUNT(*) as total')
            ->groupBy('estado_sunat')
            ->pluck('total', 'estado_sunat');

        $resumen = [
            'aceptado'  => (int) ($conteo['aceptado'] ?? 0),
            'observado' => (int) ($conteo['observado'] ?? 0),
            'pendiente' => (int) ($conteo['pendiente'] ?? 0),
            'rechazado' => (int) ($conteo['rechazado'] ?? 0) + (int) ($conteo['error'] ?? 0),
            'anulado'   => (int) Comprobante::where('estado', 'anulado')->count(),
        ];

        // Comprobantes que requieren reenvío
        $porEnviar = Comprobante::with('cliente')
            ->where('estado', 'emitido')
            ->whereIn('estado_sunat', ['pendiente', 'rechazado', 'error'])
            ->latest()
            ->limit(100)
            ->get();

        return view('modulos.facturacion_electronica_monitor', [
            'config'    => $config,
            'resumen'   => $resumen,
            'porEnviar' => $porEnviar,
        ]);
    }

    /** Reintenta el envío de todos los comprobantes pendientes / rechazados. */
    public function reintentar()
    {
        $config = FacturacionElectronica::actual();

        if (! $config->habilitado || $config->driver === 'none') {
            return redirect()->route('facturacion.electronica.monitor')
                ->with('error', 'La facturación electrónica no está habilitada o no tiene driver de emisión.');
        }

        $comprobantes = Comprobante::where('estado', 'emitido')
            ->whereIn('estado_sunat', ['pendiente', 'rechazado', 'error'])
            ->latest()
            ->limit(50) // tope por ejecución para evitar tiempos de espera largos
            ->get();

        if ($comprobantes->isEmpty()) {
            return redirect()->route('facturacion.electronica.monitor')
                ->with('ok', 'No hay comprobantes pendientes de envío.');
        }

        $service = new SunatService($config);
        $ok = 0;
        $fallidos = 0;

        foreach ($comprobantes as $comprobante) {
            $result = $service->emitir($comprobante);
            $result->success ? $ok++ : $fallidos++;
        }

        $mensaje = "Reintento completado: {$ok} aceptado(s), {$fallidos} con problemas"
            .($comprobantes->count() === 50 ? ' (procesados los primeros 50).' : '.');

        return redirect()->route('facturacion.electronica.monitor')
            ->with($fallidos > 0 && $ok === 0 ? 'error' : 'ok', $mensaje);
    }
}
