<?php

namespace App\Services\Sunat\Drivers;

use App\Models\Comprobante;
use App\Services\Sunat\Contracts\SunatDriver;
use App\Services\Sunat\SunatResult;

/**
 * Driver "Ninguno": no emite ante SUNAT, deja el comprobante pendiente.
 * Útil para operar el POS sin conexión electrónica activa.
 */
class NullDriver implements SunatDriver
{
    public function emitir(Comprobante $comprobante): SunatResult
    {
        return SunatResult::pendiente('El driver de emisión es "Ninguno": el comprobante queda pendiente de envío a SUNAT.');
    }

    public function anular(Comprobante $comprobante, string $motivo): SunatResult
    {
        return SunatResult::pendiente('El driver de emisión es "Ninguno": la anulación no se comunica a SUNAT.');
    }

    public function consultarTicket(Comprobante $comprobante): SunatResult
    {
        return SunatResult::pendiente('No hay driver de emisión para consultar el ticket.');
    }

    public function probarConexion(): SunatResult
    {
        return SunatResult::pendiente('No hay driver de emisión configurado. Selecciona "Greenter" para emitir ante SUNAT.');
    }
}
