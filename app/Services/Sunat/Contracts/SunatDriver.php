<?php

namespace App\Services\Sunat\Contracts;

use App\Models\Comprobante;
use App\Services\Sunat\SunatResult;

interface SunatDriver
{
    /** Firma y envía el comprobante a SUNAT. */
    public function emitir(Comprobante $comprobante): SunatResult;

    /** Comunica la anulación del comprobante a SUNAT (baja o resumen). */
    public function anular(Comprobante $comprobante, string $motivo): SunatResult;

    /** Consulta el estado de un ticket asíncrono (baja / resumen). */
    public function consultarTicket(Comprobante $comprobante): SunatResult;

    /** Verifica la conexión / credenciales contra SUNAT. */
    public function probarConexion(): SunatResult;
}
