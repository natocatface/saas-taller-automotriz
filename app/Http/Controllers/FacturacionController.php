<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Configuracion;
use App\Models\FacturacionElectronica;
use App\Models\MovimientoCaja;
use App\Models\Orden;
use App\Services\Sunat\SunatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturacionController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $comprobantes = Comprobante::with(['cliente', 'orden', 'docAfectado'])
            ->when($q, fn ($query) => $query->where('numero', 'like', "%{$q}%")
                ->orWhere('serie', 'like', "%{$q}%")
                ->orWhereHas('cliente', fn ($c) => $c->where('nombre', 'like', "%{$q}%")))
            ->latest()->paginate(12)->withQueryString();

        $resumen = [
            'emitidos' => Comprobante::where('estado', 'emitido')->count(),
            'ventas_mes' => Comprobante::where('estado', 'emitido')->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year)->sum('total'),
            'ticket' => round((float) Comprobante::where('estado', 'emitido')->avg('total'), 2),
        ];

        return view('modulos.facturacion', compact('comprobantes', 'resumen', 'q'));
    }

    public function create()
    {
        // Órdenes facturables: con total > 0 y sin comprobante emitido
        $facturadas = Comprobante::where('estado', 'emitido')->whereNotNull('orden_id')->pluck('orden_id');

        $ordenes = Orden::with(['cliente', 'vehiculo'])
            ->where('total', '>', 0)
            ->whereNotIn('estado', ['anulado'])
            ->whereNotIn('id', $facturadas)
            ->latest()->get();

        return view('modulos.facturacion_form', compact('ordenes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'orden_id' => 'required|exists:ordenes,id',
            'tipo' => 'required|in:boleta,factura',
            'metodo_pago' => 'required|string|max:30',
        ], [
            'orden_id.required' => 'Selecciona la orden a facturar.',
        ]);

        $comprobante = DB::transaction(function () use ($data) {
            $orden = Orden::findOrFail($data['orden_id']);
            $config = Configuracion::actual();
            $serie = $data['tipo'] === 'factura' ? $config->serie_factura : $config->serie_boleta;

            $ultimo = Comprobante::where('serie', $serie)->max(DB::raw('CAST(numero AS UNSIGNED)')) ?? 0;

            $comprobante = Comprobante::create([
                'tipo' => $data['tipo'],
                'serie' => $serie,
                'numero' => $ultimo + 1,
                'orden_id' => $orden->id,
                'cliente_id' => $orden->cliente_id,
                'user_id' => auth()->id(),
                'fecha' => now()->toDateString(),
                'subtotal' => $orden->subtotal - $orden->descuento,
                'igv' => $orden->impuesto,
                'total' => $orden->total,
                'metodo_pago' => $data['metodo_pago'],
                'estado' => 'emitido',
            ]);

            // Marcar la orden como pagada
            $orden->update(['estado_pago' => 'pagado']);

            // Ingreso en caja
            MovimientoCaja::create([
                'tipo' => 'ingreso',
                'concepto' => 'Venta '.$comprobante->serie_numero.' · Orden '.$orden->numero,
                'monto' => $orden->total,
                'fecha' => now()->toDateString(),
                'metodo_pago' => $data['metodo_pago'],
                'orden_id' => $orden->id,
                'comprobante_id' => $comprobante->id,
                'user_id' => auth()->id(),
            ]);

            return $comprobante;
        });

        // ===== Emisión electrónica ante SUNAT (si está habilitada) =====
        $feConfig = FacturacionElectronica::actual();
        $mensaje = 'Comprobante registrado correctamente.';

        if ($feConfig->habilitado && $feConfig->emitir_automatico) {
            $result = (new SunatService($feConfig))->emitir($comprobante);
            $mensaje = match ($result->estado) {
                'aceptado'  => 'Comprobante '.$comprobante->serie_numero.' aceptado por SUNAT.',
                'observado' => 'Comprobante aceptado con observaciones: '.$result->mensaje,
                'rechazado' => 'SUNAT rechazó el comprobante: '.$result->mensaje,
                'error'     => 'Comprobante registrado, pero falló el envío a SUNAT: '.$result->mensaje,
                default     => 'Comprobante registrado (pendiente de envío a SUNAT).',
            };

            if (! $result->success) {
                return redirect()->route('facturacion.index')->with('error', $mensaje);
            }

            return redirect()->route('facturacion.imprimir', $comprobante)->with('ok', $mensaje);
        }

        return redirect()->route('facturacion.imprimir', $comprobante)->with('ok', $mensaje);
    }

    /** Formulario para emitir una nota de crédito / débito sobre un comprobante. */
    public function notaCreate(Comprobante $comprobante)
    {
        if ($comprobante->esNota()) {
            return redirect()->route('facturacion.index')->with('error', 'No se puede emitir una nota sobre otra nota.');
        }
        if ($comprobante->estado !== 'emitido') {
            return redirect()->route('facturacion.index')->with('error', 'Solo se pueden emitir notas sobre comprobantes vigentes.');
        }

        $comprobante->load(['cliente', 'orden']);

        return view('modulos.nota_form', [
            'afectado'       => $comprobante,
            'motivosCredito' => Comprobante::motivosCredito(),
            'motivosDebito'  => Comprobante::motivosDebito(),
        ]);
    }

    /** Registra y emite la nota de crédito / débito. */
    public function notaStore(Request $request, Comprobante $comprobante)
    {
        if ($comprobante->esNota() || $comprobante->estado !== 'emitido') {
            return redirect()->route('facturacion.index')->with('error', 'Comprobante no válido para emitir nota.');
        }

        $data = $request->validate([
            'tipo'          => 'required|in:nota_credito,nota_debito',
            'motivo_codigo' => 'required|string|max:5',
            'motivo_desc'   => 'required|string|max:200',
            'monto'         => 'required|numeric|min:0.01|max:'.$comprobante->total,
        ], [
            'monto.max' => 'El monto no puede superar el total del comprobante (S/ '.number_format($comprobante->total, 2).').',
        ]);

        $nota = DB::transaction(function () use ($data, $comprobante) {
            $rate = $comprobante->subtotal > 0 ? ($comprobante->igv / $comprobante->subtotal) : 0.18;
            $total = round((float) $data['monto'], 2);
            $subtotal = round($total / (1 + $rate), 2);
            $igv = round($total - $subtotal, 2);

            // Serie de la nota: F/B según el afectado + C01 (crédito) / D01 (débito)
            $prefijo = $comprobante->tipo === 'factura' ? 'F' : 'B';
            $serie = $prefijo.($data['tipo'] === 'nota_debito' ? 'D01' : 'C01');

            $ultimo = Comprobante::where('serie', $serie)->max(DB::raw('CAST(numero AS UNSIGNED)')) ?? 0;

            $nota = Comprobante::create([
                'tipo'            => $data['tipo'],
                'serie'           => $serie,
                'numero'          => $ultimo + 1,
                'orden_id'        => $comprobante->orden_id,   // para reconstruir las líneas
                'doc_afectado_id' => $comprobante->id,
                'motivo_codigo'   => $data['motivo_codigo'],
                'motivo_desc'     => $data['motivo_desc'],
                'cliente_id'      => $comprobante->cliente_id,
                'user_id'         => auth()->id(),
                'fecha'           => now()->toDateString(),
                'subtotal'        => $subtotal,
                'igv'             => $igv,
                'total'           => $total,
                'metodo_pago'     => $comprobante->metodo_pago,
                'estado'          => 'emitido',
                'estado_sunat'    => 'pendiente',
            ]);

            // Movimiento de caja: crédito = egreso (devolución) · débito = ingreso (cargo)
            MovimientoCaja::create([
                'tipo'           => $data['tipo'] === 'nota_credito' ? 'egreso' : 'ingreso',
                'concepto'       => $nota->tipo_label.' '.$nota->serie_numero.' · afecta '.$comprobante->serie_numero,
                'monto'          => $total,
                'fecha'          => now()->toDateString(),
                'metodo_pago'    => $comprobante->metodo_pago ?? 'efectivo',
                'comprobante_id' => $nota->id,
                'user_id'        => auth()->id(),
            ]);

            return $nota;
        });

        // Emisión electrónica ante SUNAT (si está habilitada)
        $config = FacturacionElectronica::actual();
        if ($config->habilitado && $config->emitir_automatico && $config->driver !== 'none') {
            $result = (new SunatService($config))->emitir($nota);
            if (! $result->success) {
                return redirect()->route('facturacion.index')
                    ->with('error', $nota->tipo_label.' '.$nota->serie_numero.' registrada, pero SUNAT: '.$result->mensaje);
            }
        }

        return redirect()->route('facturacion.imprimir', $nota)
            ->with('ok', $nota->tipo_label.' '.$nota->serie_numero.' emitida correctamente.');
    }

    public function imprimir(Comprobante $comprobante)
    {
        $comprobante->load(['cliente', 'orden.servicios', 'orden.repuestos', 'orden.vehiculo', 'docAfectado']);
        $config = Configuracion::actual();
        $fe = FacturacionElectronica::actual();

        return view('modulos.comprobante_print', compact('comprobante', 'config', 'fe'));
    }

    /** Reintenta el envío de un comprobante a SUNAT (pendiente, rechazado o con error). */
    public function reenviar(Comprobante $comprobante)
    {
        if ($comprobante->estado !== 'emitido') {
            return redirect()->route('facturacion.index')->with('error', 'Solo se pueden reenviar comprobantes emitidos.');
        }

        if (in_array($comprobante->estado_sunat, ['aceptado', 'observado'], true)) {
            return redirect()->route('facturacion.index')->with('error', 'El comprobante '.$comprobante->serie_numero.' ya fue aceptado por SUNAT.');
        }

        $config = FacturacionElectronica::actual();
        if (! $config->habilitado || $config->driver === 'none') {
            return redirect()->route('facturacion.index')->with('error', 'La facturación electrónica no está habilitada o no tiene un driver de emisión.');
        }

        $result = (new SunatService($config))->emitir($comprobante);

        $mensaje = match ($result->estado) {
            'aceptado'  => 'Comprobante '.$comprobante->serie_numero.' aceptado por SUNAT.',
            'observado' => 'Comprobante aceptado con observaciones: '.$result->mensaje,
            'rechazado' => 'SUNAT rechazó el comprobante: '.$result->mensaje,
            default     => 'No se pudo enviar: '.$result->mensaje,
        };

        return redirect()->route('facturacion.index')->with($result->success ? 'ok' : 'error', $mensaje);
    }

    public function anular(Request $request, Comprobante $comprobante)
    {
        if ($comprobante->estado !== 'emitido') {
            return redirect()->route('facturacion.index')->with('error', 'El comprobante ya está anulado.');
        }

        $motivo = trim($request->input('motivo', '')) ?: 'Anulación de la operación';

        // Anulación local (estado + caja + orden)
        DB::transaction(function () use ($comprobante) {
            $comprobante->update(['estado' => 'anulado']);

            if ($comprobante->orden_id) {
                Orden::where('id', $comprobante->orden_id)->update(['estado_pago' => 'pendiente']);
            }

            // Egreso compensatorio en caja
            MovimientoCaja::create([
                'tipo' => 'egreso',
                'concepto' => 'Anulación comprobante '.$comprobante->serie_numero,
                'monto' => $comprobante->total,
                'fecha' => now()->toDateString(),
                'metodo_pago' => $comprobante->metodo_pago ?? 'efectivo',
                'comprobante_id' => $comprobante->id,
                'user_id' => auth()->id(),
            ]);
        });

        // Comunicación de baja / resumen ante SUNAT (si aplica)
        $config = FacturacionElectronica::actual();
        $mensaje = 'Comprobante '.$comprobante->serie_numero.' anulado.';

        if ($config->habilitado && $config->driver !== 'none'
            && in_array($comprobante->estado_sunat, ['aceptado', 'observado'], true)) {
            $result = (new SunatService($config))->anular($comprobante, $motivo);
            $mensaje .= match ($result->estado) {
                'anulado'  => ' Baja aceptada por SUNAT.',
                'anulando' => ' Baja enviada a SUNAT (ticket '.$result->ticket.'), pendiente de confirmar.',
                'rechazado' => ' SUNAT rechazó la baja: '.$result->mensaje,
                default     => ' No se pudo comunicar la baja: '.$result->mensaje,
            };

            return redirect()->route('facturacion.index')->with($result->estado === 'rechazado' ? 'error' : 'ok', $mensaje);
        }

        return redirect()->route('facturacion.index')->with('ok', $mensaje);
    }

    /** Consulta el ticket de una baja/resumen pendiente ante SUNAT. */
    public function consultarBaja(Comprobante $comprobante)
    {
        if (blank($comprobante->sunat_ticket)) {
            return redirect()->route('facturacion.index')->with('error', 'El comprobante no tiene un ticket de baja pendiente.');
        }

        $result = (new SunatService(FacturacionElectronica::actual()))->consultarTicket($comprobante);

        $mensaje = match ($result->estado) {
            'anulado'  => 'Baja de '.$comprobante->serie_numero.' confirmada por SUNAT.',
            'anulando' => 'SUNAT aún procesa la baja de '.$comprobante->serie_numero.'. Intenta de nuevo en unos minutos.',
            'rechazado' => 'SUNAT rechazó la baja: '.$result->mensaje,
            default     => 'No se pudo consultar la baja: '.$result->mensaje,
        };

        return redirect()->route('facturacion.index')->with($result->estado === 'rechazado' ? 'error' : 'ok', $mensaje);
    }
}
