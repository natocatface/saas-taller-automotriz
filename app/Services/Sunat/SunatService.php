<?php

namespace App\Services\Sunat;

use App\Models\Comprobante;
use App\Models\FacturacionElectronica;
use App\Services\Sunat\Contracts\SunatDriver;
use App\Services\Sunat\Drivers\GreenterDriver;
use App\Services\Sunat\Drivers\NullDriver;

/**
 * Punto de entrada único para la emisión electrónica.
 * Resuelve el driver según la configuración y delega la operación.
 */
class SunatService
{
    private FacturacionElectronica $config;

    public function __construct(?FacturacionElectronica $config = null)
    {
        $this->config = $config ?? FacturacionElectronica::actual();
    }

    public function config(): FacturacionElectronica
    {
        return $this->config;
    }

    /** Instancia el driver de emisión configurado. */
    public function driver(): SunatDriver
    {
        return match ($this->config->driver) {
            'greenter' => new GreenterDriver($this->config),
            default    => new NullDriver(),
        };
    }

    /**
     * Emite un comprobante ante SUNAT y persiste el resultado en el registro.
     * No lanza excepciones: siempre devuelve un SunatResult.
     */
    public function emitir(Comprobante $comprobante): SunatResult
    {
        if (! $this->config->habilitado) {
            return SunatResult::pendiente('La facturación electrónica está deshabilitada.');
        }

        $result = $this->driver()->emitir($comprobante);

        $comprobante->forceFill($result->toComprobanteAttributes())->save();

        return $result;
    }

    /**
     * Comunica la anulación de un comprobante a SUNAT (baja o resumen)
     * y persiste el resultado en el registro.
     */
    public function anular(Comprobante $comprobante, string $motivo = 'Anulación de la operación'): SunatResult
    {
        if (! $this->config->habilitado) {
            return SunatResult::pendiente('La facturación electrónica está deshabilitada.');
        }

        $result = $this->driver()->anular($comprobante, $motivo);
        $comprobante->forceFill($result->toAnulacionAttributes())->save();

        return $result;
    }

    /** Consulta el estado del ticket de anulación y actualiza el registro. */
    public function consultarTicket(Comprobante $comprobante): SunatResult
    {
        $result = $this->driver()->consultarTicket($comprobante);
        $comprobante->forceFill($result->toAnulacionAttributes())->save();

        return $result;
    }

    /** Prueba la conexión y credenciales con SUNAT. */
    public function probarConexion(): SunatResult
    {
        return $this->driver()->probarConexion();
    }

    /** Emite un comprobante de muestra (homologación), sin persistir nada. */
    public function emitirDemo(string $tipo = 'boleta'): SunatResult
    {
        $driver = $this->driver();

        if ($driver instanceof GreenterDriver) {
            return $driver->emitirDemo($tipo);
        }

        return SunatResult::pendiente('Selecciona el driver "Greenter" para emitir comprobantes de prueba.');
    }
}
