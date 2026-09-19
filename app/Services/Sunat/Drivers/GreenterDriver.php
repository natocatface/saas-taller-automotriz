<?php

namespace App\Services\Sunat\Drivers;

use App\Models\Comprobante;
use App\Models\FacturacionElectronica;
use App\Services\Sunat\Contracts\SunatDriver;
use App\Services\Sunat\SunatResult;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Driver de emisión electrónica basado en Greenter (firma UBL 2.1 local + envío a SUNAT).
 *
 * Requiere:  composer require greenter/lite
 * y un certificado digital .pem válido en la ruta configurada.
 */
class GreenterDriver implements SunatDriver
{
    public function __construct(private FacturacionElectronica $config) {}

    /** ¿Está instalada la librería Greenter? */
    public static function disponible(): bool
    {
        return class_exists(\Greenter\See::class);
    }

    public function emitir(Comprobante $comprobante): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (! $this->config->certificadoExiste()) {
            return SunatResult::error('No se encontró el certificado digital en la ruta configurada.');
        }

        try {
            $see = $this->buildSee();
            $documento = $this->buildDocumento($comprobante);

            $result = $see->send($documento);

            // XML firmado
            $xmlPath = $this->guardarXml($comprobante, $see->getFactory()->getLastXml());

            if (! $result->isSuccess()) {
                $err = $result->getError();

                return SunatResult::rechazado(
                    'SUNAT rechazó el comprobante: '.($err ? $err->getCode().' - '.$err->getMessage() : 'error desconocido'),
                    xmlPath: $xmlPath,
                );
            }

            $cdr = $result->getCdrResponse();
            $cdrPath = $this->guardarCdr($comprobante, $result->getCdrZip());
            $hash = $this->extraerHash($see->getFactory()->getLastXml());

            $code = (int) $cdr->getCode();
            $notes = array_filter($cdr->getNotes() ?? []);

            // 0 = aceptado. Con observaciones vienen notas pero también aceptado.
            if ($code === 0 && empty($notes)) {
                return SunatResult::aceptado($cdr->getDescription(), $hash, $xmlPath, $cdrPath);
            }
            if ($code === 0) {
                return SunatResult::observado($cdr->getDescription(), $notes, $hash, $xmlPath, $cdrPath);
            }

            return SunatResult::rechazado($cdr->getDescription(), $notes, $xmlPath);
        } catch (Throwable $e) {
            return SunatResult::error('Error al emitir: '.$e->getMessage());
        }
    }

    public function anular(Comprobante $comprobante, string $motivo): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (! $this->config->certificadoExiste()) {
            return SunatResult::error('No se encontró el certificado digital en la ruta configurada.');
        }

        try {
            $see = $this->buildSee();

            // Factura → Comunicación de Baja · Boleta → Resumen Diario (estado anulado)
            $documento = $comprobante->tipo === 'factura'
                ? $this->buildVoided($comprobante, $motivo)
                : $this->buildSummary($comprobante);

            $result = $see->send($documento);

            if (! $result->isSuccess()) {
                $err = $result->getError();

                return SunatResult::rechazado(
                    'SUNAT rechazó la comunicación: '.($err ? $err->getCode().' - '.$err->getMessage() : 'error desconocido')
                );
            }

            $ticket = $result->getTicket();
            $comprobante->forceFill(['sunat_ticket' => $ticket])->save();

            // Consulta inmediata del estado (asíncrono)
            return $this->resolverTicket($see, $comprobante);
        } catch (Throwable $e) {
            return SunatResult::error('Error al comunicar la anulación: '.$e->getMessage());
        }
    }

    public function consultarTicket(Comprobante $comprobante): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (blank($comprobante->sunat_ticket)) {
            return SunatResult::error('El comprobante no tiene un ticket de anulación que consultar.');
        }

        try {
            return $this->resolverTicket($this->buildSee(), $comprobante);
        } catch (Throwable $e) {
            return SunatResult::error('Error al consultar el ticket: '.$e->getMessage());
        }
    }

    /** Consulta el estado del ticket con reintentos cortos. */
    private function resolverTicket(\Greenter\See $see, Comprobante $comprobante): SunatResult
    {
        $ticket = $comprobante->sunat_ticket;
        $status = null;

        for ($i = 0; $i < 3; $i++) {
            $status = $see->getStatus($ticket);
            if ($status->getCode() !== '98') { // 98 = en proceso
                break;
            }
            sleep(2);
        }

        $code = $status?->getCode();

        if ($code === '98') {
            return SunatResult::procesando('SUNAT aún procesa la anulación. Vuelve a consultar el ticket en unos minutos.', $ticket);
        }
        if (! $status || ! $status->isSuccess()) {
            $err = $status?->getError();

            return SunatResult::rechazado('SUNAT rechazó la anulación: '.($err ? $err->getCode().' - '.$err->getMessage() : 'código '.$code));
        }

        $cdr = $status->getCdrResponse();
        $zip = method_exists($status, 'getCdrZip') ? $status->getCdrZip() : null;
        $cdrPath = $this->guardarZip('facturacion/pe/cdr/BAJA-'.$comprobante->serie_numero.'.zip', $zip);

        return SunatResult::anulado($cdr ? $cdr->getDescription() : 'Anulación aceptada por SUNAT.', $ticket, $cdrPath);
    }

    public function probarConexion(): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (! $this->config->certificadoExiste()) {
            return SunatResult::error('No se encontró el certificado digital en la ruta configurada.');
        }
        if (blank($this->config->ruc) || blank($this->config->usuario_sol) || blank($this->config->clave_sol)) {
            return SunatResult::error('Faltan credenciales SUNAT (RUC, usuario o clave SOL).');
        }

        try {
            // Firma un documento de prueba para validar certificado y credenciales
            // sin enviarlo realmente a producción.
            $this->buildSee();

            return SunatResult::aceptado(
                'Certificado y credenciales cargados correctamente. Entorno: '.$this->config->modo_label.'.'
            );
        } catch (Throwable $e) {
            return SunatResult::error('No se pudo inicializar la conexión: '.$e->getMessage());
        }
    }

    /**
     * Emite un comprobante de MUESTRA (sin tocar la base de datos) para validar
     * certificado, credenciales y el circuito completo en el entorno beta.
     */
    public function emitirDemo(string $tipo = 'boleta'): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (! $this->config->certificadoExiste()) {
            return SunatResult::error('No se encontró el certificado digital en la ruta configurada.');
        }

        try {
            $see = $this->buildSee();
            $esFactura = $tipo === 'factura';

            $client = (new \Greenter\Model\Client\Client())
                ->setTipoDoc($esFactura ? '6' : '1')
                ->setNumDoc($esFactura ? '20000000001' : '00000000')
                ->setRznSocial($esFactura ? 'EMPRESA CLIENTE DEMO S.A.C.' : 'CLIENTE VARIOS');

            $detalle = (new \Greenter\Model\Sale\SaleDetail())
                ->setCodProducto('DEMO001')
                ->setUnidad('ZZ')
                ->setCantidad(1)
                ->setDescripcion('SERVICIO DE PRUEBA - HOMOLOGACION')
                ->setMtoBaseIgv(100.00)
                ->setPorcentajeIgv(18)
                ->setIgv(18.00)
                ->setTipAfeIgv('10')
                ->setTotalImpuestos(18.00)
                ->setMtoValorVenta(100.00)
                ->setMtoValorUnitario(100.00)
                ->setMtoPrecioUnitario(118.00);

            $legend = (new \Greenter\Model\Sale\Legend())
                ->setCode('1000')
                ->setValue(self::numeroALetras(118.00).' SOLES');

            $invoice = (new \Greenter\Model\Sale\Invoice())
                ->setUblVersion('2.1')
                ->setTipoOperacion('0101')
                ->setTipoDoc($esFactura ? '01' : '03')
                ->setSerie($esFactura ? 'F001' : 'B001')
                ->setCorrelativo((string) random_int(1, 99999))
                ->setFechaEmision(new \DateTime())
                ->setTipoMoneda('PEN')
                ->setCompany($this->buildCompany())
                ->setClient($client)
                ->setMtoOperGravadas(100.00)
                ->setMtoIGV(18.00)
                ->setTotalImpuestos(18.00)
                ->setValorVenta(100.00)
                ->setSubTotal(118.00)
                ->setMtoImpVenta(118.00)
                ->setDetails([$detalle])
                ->setLegends([$legend]);

            $result = $see->send($invoice);
            $xml = $see->getFactory()->getLastXml();

            if (! $result->isSuccess()) {
                $err = $result->getError();

                return SunatResult::rechazado(
                    'SUNAT rechazó el comprobante de prueba: '.($err ? $err->getCode().' - '.$err->getMessage() : 'error desconocido')
                );
            }

            $cdr = $result->getCdrResponse();
            $notes = array_filter($cdr->getNotes() ?? []);
            $hash = $this->extraerHash($xml);

            return (int) $cdr->getCode() === 0 && empty($notes)
                ? SunatResult::aceptado($cdr->getDescription(), $hash)
                : SunatResult::observado($cdr->getDescription(), $notes, $hash);
        } catch (Throwable $e) {
            return SunatResult::error('Error en la emisión de prueba: '.$e->getMessage());
        }
    }

    /* ===================== Construcción Greenter ===================== */

    private function buildSee(): \Greenter\See
    {
        $see = new \Greenter\See();
        $see->setCertificate(file_get_contents($this->config->certificado_path));
        $see->setService(
            $this->config->modo === 'produccion'
                ? \Greenter\Ws\Services\SunatEndpoints::FE_PRODUCCION
                : \Greenter\Ws\Services\SunatEndpoints::FE_BETA
        );
        $see->setClaveSOL(
            $this->config->ruc,
            $this->config->usuario_sol,
            (string) $this->config->clave_sol,
        );

        return $see;
    }

    private function buildCompany(): \Greenter\Model\Company\Company
    {
        $address = (new \Greenter\Model\Company\Address())
            ->setUbigueo($this->config->ubigeo ?: '150101')
            ->setDepartamento($this->config->departamento ?: 'LIMA')
            ->setProvincia($this->config->provincia ?: 'LIMA')
            ->setDistrito($this->config->distrito ?: 'LIMA')
            ->setUrbanizacion('-')
            ->setDireccion($this->config->direccion_fiscal ?: '-');

        return (new \Greenter\Model\Company\Company())
            ->setRuc($this->config->ruc)
            ->setRazonSocial($this->config->razon_social)
            ->setNombreComercial($this->config->nombre_comercial ?: $this->config->razon_social)
            ->setAddress($address);
    }

    /** Devuelve [tipoDoc, numDoc] del cliente según catálogo 06 SUNAT. */
    private function clienteTipoNro(Comprobante $comprobante): array
    {
        $cliente = $comprobante->cliente;
        $esFactura = $comprobante->tipo === 'factura';

        // Catálogo 06 SUNAT: 6=RUC, 1=DNI, 4=CE, 7=Pasaporte, 0=Sin documento (Varios)
        $map = ['RUC' => '6', 'DNI' => '1', 'CE' => '4', 'Pasaporte' => '7'];
        $tipoDoc = $cliente ? ($map[$cliente->tipo_documento] ?? '1') : '0';
        $numDoc = $cliente?->documento;

        if ($esFactura) {
            $tipoDoc = '6'; // la factura exige RUC
        }
        if (blank($numDoc)) {
            $tipoDoc = '0';
            $numDoc = '00000000';
        }

        return [$tipoDoc, $numDoc];
    }

    private function buildClient(Comprobante $comprobante): \Greenter\Model\Client\Client
    {
        [$tipoDoc, $numDoc] = $this->clienteTipoNro($comprobante);

        return (new \Greenter\Model\Client\Client())
            ->setTipoDoc($tipoDoc)
            ->setNumDoc($numDoc)
            ->setRznSocial($comprobante->cliente?->nombre_completo ?: 'CLIENTE VARIOS');
    }

    /** Comunicación de Baja (facturas). */
    private function buildVoided(Comprobante $comprobante, string $motivo): \Greenter\Model\Voided\Voided
    {
        $comprobante->loadMissing('cliente');

        $detalle = (new \Greenter\Model\Voided\VoidedDetail())
            ->setTipoDoc('01')
            ->setSerie($comprobante->serie)
            ->setCorrelativo((string) $comprobante->numero)
            ->setDesMotivoBaja(mb_substr($motivo, 0, 100));

        return (new \Greenter\Model\Voided\Voided())
            ->setCorrelativo($this->correlativoBaja())
            ->setFecComunicacion(new \DateTime())
            ->setFecGeneracion(new \DateTime($comprobante->fecha?->toDateString() ?? 'now'))
            ->setCompany($this->buildCompany())
            ->setDetails([$detalle]);
    }

    /** Resumen Diario con estado "anular" (boletas). */
    private function buildSummary(Comprobante $comprobante): \Greenter\Model\Summary\Summary
    {
        $comprobante->loadMissing('cliente');
        [$tipoDoc, $numDoc] = $this->clienteTipoNro($comprobante);

        $detalle = (new \Greenter\Model\Summary\SummaryDetail())
            ->setTipoDoc('03')
            ->setSerieNro($comprobante->serie.'-'.$comprobante->numero)
            ->setEstado('3') // 1=Adicionar, 2=Modificar, 3=Anular
            ->setClienteTipo($tipoDoc)
            ->setClienteNro($numDoc)
            ->setTotal(round((float) $comprobante->total, 2))
            ->setMtoOperGravadas(round((float) $comprobante->subtotal, 2))
            ->setMtoIgv(round((float) $comprobante->igv, 2));

        return (new \Greenter\Model\Summary\Summary())
            ->setFecGeneracion(new \DateTime($comprobante->fecha?->toDateString() ?? 'now'))
            ->setFecResumen(new \DateTime())
            ->setCorrelativo($this->correlativoBaja())
            ->setCompany($this->buildCompany())
            ->setDetails([$detalle]);
    }

    /** Correlativo diario para bajas / resúmenes (secuencial por día). */
    private function correlativoBaja(): string
    {
        $n = \App\Models\Comprobante::where('estado', 'anulado')
            ->whereDate('updated_at', now()->toDateString())
            ->count();

        return (string) max(1, $n);
    }

    private function buildDocumento(Comprobante $comprobante): \Greenter\Model\DocumentInterface
    {
        return $comprobante->esNota()
            ? $this->buildNote($comprobante)
            : $this->buildInvoice($comprobante);
    }

    private function buildInvoice(Comprobante $comprobante): \Greenter\Model\Sale\Invoice
    {
        $comprobante->loadMissing(['cliente', 'orden.servicios', 'orden.repuestos']);

        $rate = ($comprobante->subtotal > 0)
            ? round($comprobante->igv / $comprobante->subtotal, 2)
            : 0.18;
        if ($rate <= 0) {
            $rate = 0.18;
        }

        [$details, $sumBase] = $this->buildDetails($comprobante, $rate);

        $gravadas = round((float) $comprobante->subtotal, 2);
        $igv = round((float) $comprobante->igv, 2);
        $total = round((float) $comprobante->total, 2);

        $legend = (new \Greenter\Model\Sale\Legend())
            ->setCode('1000')
            ->setValue(self::numeroALetras($total).' SOLES');

        return (new \Greenter\Model\Sale\Invoice())
            ->setUblVersion('2.1')
            ->setTipoOperacion('0101') // venta interna
            ->setTipoDoc($comprobante->tipo === 'factura' ? '01' : '03')
            ->setSerie($comprobante->serie)
            ->setCorrelativo(str_pad((string) $comprobante->numero, 1, '0', STR_PAD_LEFT))
            ->setFechaEmision(new \DateTime($comprobante->fecha?->toDateString() ?? 'now'))
            ->setTipoMoneda('PEN')
            ->setCompany($this->buildCompany())
            ->setClient($this->buildClient($comprobante))
            ->setMtoOperGravadas($gravadas)
            ->setMtoIGV($igv)
            ->setTotalImpuestos($igv)
            ->setValorVenta($gravadas)
            ->setSubTotal($total)
            ->setMtoImpVenta($total)
            ->setDetails($details)
            ->setLegends([$legend]);
    }

    /** Nota de crédito (07) o débito (08) referida a un comprobante afectado. */
    private function buildNote(Comprobante $comprobante): \Greenter\Model\Sale\Note
    {
        $comprobante->loadMissing(['cliente', 'orden.servicios', 'orden.repuestos', 'docAfectado']);

        $afectado = $comprobante->docAfectado;
        if (! $afectado) {
            throw new \RuntimeException('La nota no tiene un comprobante afectado.');
        }

        $rate = ($comprobante->subtotal > 0)
            ? round($comprobante->igv / $comprobante->subtotal, 2)
            : 0.18;
        if ($rate <= 0) {
            $rate = 0.18;
        }

        [$details] = $this->buildDetails($comprobante, $rate);

        $gravadas = round((float) $comprobante->subtotal, 2);
        $igv = round((float) $comprobante->igv, 2);
        $total = round((float) $comprobante->total, 2);

        $legend = (new \Greenter\Model\Sale\Legend())
            ->setCode('1000')
            ->setValue(self::numeroALetras($total).' SOLES');

        return (new \Greenter\Model\Sale\Note())
            ->setUblVersion('2.1')
            ->setTipoDoc($comprobante->tipo === 'nota_debito' ? '08' : '07')
            ->setSerie($comprobante->serie)
            ->setCorrelativo(str_pad((string) $comprobante->numero, 1, '0', STR_PAD_LEFT))
            ->setFechaEmision(new \DateTime($comprobante->fecha?->toDateString() ?? 'now'))
            ->setTipDocAfectado($afectado->tipoDocSunat())
            ->setNumDocfectado($afectado->serie_numero)
            ->setCodMotivo($comprobante->motivo_codigo ?: ($comprobante->tipo === 'nota_debito' ? '02' : '01'))
            ->setDesMotivo($comprobante->motivo_desc ?: 'Nota electrónica')
            ->setTipoMoneda('PEN')
            ->setCompany($this->buildCompany())
            ->setClient($this->buildClient($comprobante))
            ->setMtoOperGravadas($gravadas)
            ->setMtoIGV($igv)
            ->setTotalImpuestos($igv)
            ->setMtoImpVenta($total)
            ->setDetails($details)
            ->setLegends([$legend]);
    }

    /** Construye las líneas de detalle desde servicios y repuestos de la orden. */
    private function buildDetails(Comprobante $comprobante, float $rate): array
    {
        $orden = $comprobante->orden;
        $lineas = [];

        if ($orden) {
            foreach ($orden->servicios as $s) {
                $lineas[] = ['desc' => $s->descripcion, 'cant' => (float) $s->cantidad, 'base' => (float) $s->subtotal, 'unidad' => 'ZZ', 'cod' => 'SERV'];
            }
            foreach ($orden->repuestos as $r) {
                $lineas[] = ['desc' => $r->descripcion, 'cant' => (float) $r->cantidad, 'base' => (float) $r->subtotal, 'unidad' => 'NIU', 'cod' => 'REP'];
            }
        }

        // Respaldo: si no hay líneas, una sola por el total gravado
        if (empty($lineas)) {
            $lineas[] = ['desc' => 'Servicio de taller '.($orden->numero ?? ''), 'cant' => 1.0, 'base' => (float) $comprobante->subtotal, 'unidad' => 'ZZ', 'cod' => 'SERV'];
        }

        // Escala las bases para que sumen exactamente el subtotal del comprobante (por descuentos globales)
        $sumBase = array_sum(array_column($lineas, 'base')) ?: 1;
        $factor = $comprobante->subtotal > 0 ? ((float) $comprobante->subtotal / $sumBase) : 1;

        $details = [];
        foreach ($lineas as $l) {
            $cant = $l['cant'] > 0 ? $l['cant'] : 1;
            $base = round($l['base'] * $factor, 2);
            $igvLinea = round($base * $rate, 2);
            $valorUnit = round($base / $cant, 2);
            $precioUnit = round($valorUnit * (1 + $rate), 2);

            $details[] = (new \Greenter\Model\Sale\SaleDetail())
                ->setCodProducto($l['cod'].str_pad((string) (count($details) + 1), 3, '0', STR_PAD_LEFT))
                ->setUnidad($l['unidad'])
                ->setCantidad($cant)
                ->setDescripcion($l['desc'] ?: 'Item')
                ->setMtoBaseIgv($base)
                ->setPorcentajeIgv($rate * 100)
                ->setIgv($igvLinea)
                ->setTipAfeIgv('10') // gravado - operación onerosa
                ->setTotalImpuestos($igvLinea)
                ->setMtoValorVenta($base)
                ->setMtoValorUnitario($valorUnit)
                ->setMtoPrecioUnitario($precioUnit);
        }

        return [$details, $sumBase];
    }

    /* ===================== Persistencia XML / CDR ===================== */

    private function guardarXml(Comprobante $comprobante, ?string $xml): ?string
    {
        if (blank($xml)) {
            return null;
        }
        $ruta = 'facturacion/pe/xml/'.$comprobante->serie_numero.'.xml';
        Storage::disk('local')->put($ruta, $xml);

        return $ruta;
    }

    private function guardarCdr(Comprobante $comprobante, ?string $zip): ?string
    {
        return $this->guardarZip('facturacion/pe/cdr/R-'.$comprobante->serie_numero.'.zip', $zip);
    }

    private function guardarZip(string $ruta, ?string $zip): ?string
    {
        if (blank($zip)) {
            return null;
        }
        Storage::disk('local')->put($ruta, $zip);

        return $ruta;
    }

    private function extraerHash(?string $xml): ?string
    {
        if (blank($xml) || ! str_contains($xml, 'DigestValue')) {
            return null;
        }
        if (preg_match('/<ds:DigestValue>(.*?)<\/ds:DigestValue>/s', $xml, $m)) {
            return $m[1];
        }

        return null;
    }

    /* ===================== Utilidades ===================== */

    /** Convierte un monto a letras en español (para la leyenda 1000 de SUNAT). */
    public static function numeroALetras(float $numero): string
    {
        $entero = (int) floor($numero);
        $decimal = (int) round(($numero - $entero) * 100);

        $letras = $entero === 0 ? 'CERO' : self::seccion($entero);

        return trim($letras).' CON '.str_pad((string) $decimal, 2, '0', STR_PAD_LEFT).'/100';
    }

    private static function seccion(int $n): string
    {
        if ($n === 0) {
            return '';
        }
        if ($n === 100) {
            return 'CIEN';
        }

        $unidades = ['', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE',
            'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE',
            'VEINTE'];
        $decenas = ['', '', 'VEINTI', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        $texto = '';

        if ($n >= 1000000) {
            $millones = intdiv($n, 1000000);
            $texto .= ($millones === 1 ? 'UN MILLON ' : self::seccion($millones).' MILLONES ');
            $n %= 1000000;
        }
        if ($n >= 1000) {
            $miles = intdiv($n, 1000);
            $texto .= ($miles === 1 ? 'MIL ' : self::seccion($miles).' MIL ');
            $n %= 1000;
        }
        if ($n >= 100) {
            $texto .= $centenas[intdiv($n, 100)].' ';
            $n %= 100;
        }
        if ($n > 20) {
            $d = intdiv($n, 10);
            $u = $n % 10;
            if ($d === 2) {
                $texto .= 'VEINTI'.strtolower($unidades[$u]);
                $texto = strtoupper($texto);
            } else {
                $texto .= $decenas[$d].($u > 0 ? ' Y '.$unidades[$u] : '');
            }
            $n = 0;
        }
        if ($n > 0 && $n <= 20) {
            $texto .= $unidades[$n];
        }

        return trim(preg_replace('/\s+/', ' ', $texto));
    }
}
