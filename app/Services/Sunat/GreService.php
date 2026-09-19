<?php

namespace App\Services\Sunat;

use App\Models\FacturacionElectronica;
use App\Models\GuiaRemision;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Emisión de Guías de Remisión Electrónicas (GRE) mediante el API REST de SUNAT
 * (Greenter\Api). Requiere client_id / client_secret además de la Clave SOL.
 */
class GreService
{
    // Endpoints del API GRE de SUNAT
    private const AUTH_URL = 'https://api-seguridad.sunat.gob.pe/v1';
    private const CPE_URL = 'https://api-cpe.sunat.gob.pe/v1';

    private FacturacionElectronica $config;

    public function __construct(?FacturacionElectronica $config = null)
    {
        $this->config = $config ?? FacturacionElectronica::actual();
    }

    /** ¿Está instalada la librería y su cliente API? */
    public static function disponible(): bool
    {
        return class_exists(\Greenter\Api::class);
    }

    /** ¿Hay credenciales del API REST configuradas? */
    public function credencialesListas(): bool
    {
        return filled($this->config->client_id)
            && filled($this->config->client_secret)
            && filled($this->config->usuario_sol)
            && filled($this->config->clave_sol)
            && $this->config->certificadoExiste();
    }

    public function emitir(GuiaRemision $guia): SunatResult
    {
        $result = $this->enviar($guia);
        $guia->forceFill($result->toComprobanteAttributes())->save();

        return $result;
    }

    public function consultarTicket(GuiaRemision $guia): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (blank($guia->sunat_ticket)) {
            return SunatResult::error('La guía no tiene un ticket que consultar.');
        }

        try {
            $api = $this->buildApi();
            $result = $this->resolverEstado($api->getStatus($guia->sunat_ticket), $guia);
            $guia->forceFill($result->toComprobanteAttributes())->save();

            return $result;
        } catch (Throwable $e) {
            return SunatResult::error('Error al consultar el ticket: '.$e->getMessage());
        }
    }

    /* ===================== Interno ===================== */

    private function enviar(GuiaRemision $guia): SunatResult
    {
        if (! self::disponible()) {
            return SunatResult::error('La librería Greenter no está instalada. Ejecuta: composer require greenter/lite');
        }
        if (! $this->config->habilitado) {
            return SunatResult::pendiente('La facturación electrónica está deshabilitada.');
        }
        if (! $this->credencialesListas()) {
            return SunatResult::error('Faltan credenciales del API GRE (client_id, client_secret, Clave SOL o certificado).');
        }

        try {
            $api = $this->buildApi();
            $despatch = $this->buildDespatch($guia);

            $res = $api->send($despatch);

            $xmlPath = $this->guardarXml($guia, method_exists($res, 'getXml') ? $res->getXml() : null);

            if (! $res->isSuccess()) {
                $err = $res->getError();

                return SunatResult::rechazado(
                    'SUNAT rechazó la guía: '.($err ? $err->getCode().' - '.$err->getMessage() : 'error desconocido'),
                    xmlPath: $xmlPath,
                );
            }

            $ticket = $res->getTicket();
            $guia->forceFill(['sunat_ticket' => $ticket, 'xml_path' => $xmlPath])->save();

            // El estado se confirma consultando el ticket
            $estado = $this->resolverEstado($api->getStatus($ticket), $guia);

            return $estado;
        } catch (Throwable $e) {
            return SunatResult::error('Error al emitir la guía: '.$e->getMessage());
        }
    }

    private function resolverEstado($status, GuiaRemision $guia): SunatResult
    {
        if (! $status->isSuccess()) {
            $err = $status->getError();

            // Código 98 = en proceso
            if ($err && (string) $err->getCode() === '98') {
                return SunatResult::procesando('SUNAT aún procesa la guía. Consulta el ticket en unos minutos.', $guia->sunat_ticket);
            }

            return SunatResult::rechazado('SUNAT rechazó la guía: '.($err ? $err->getCode().' - '.$err->getMessage() : 'error desconocido'));
        }

        $cdr = method_exists($status, 'getCdrResponse') ? $status->getCdrResponse() : null;
        $zip = method_exists($status, 'getCdrZip') ? $status->getCdrZip() : null;
        $cdrPath = $this->guardarZip('facturacion/pe/gre/cdr/R-'.$guia->serie_numero.'.zip', $zip);

        return SunatResult::aceptado(
            $cdr ? $cdr->getDescription() : 'Guía aceptada por SUNAT.',
            null,
            $guia->xml_path,
            $cdrPath,
        );
    }

    private function buildApi(): \Greenter\Api
    {
        $api = new \Greenter\Api([
            'auth' => self::AUTH_URL,
            'cpe'  => self::CPE_URL,
        ]);

        return $api
            ->setBuilderOptions([
                'strict_variables' => true,
                'optimizations'    => 0,
                'debug'            => false,
            ])
            ->setApiCredentials($this->config->client_id, (string) $this->config->client_secret)
            ->setClaveSOL($this->config->ruc, $this->config->usuario_sol, (string) $this->config->clave_sol)
            ->setCertificate(file_get_contents($this->config->certificado_path));
    }

    private function buildDespatch(GuiaRemision $guia): \Greenter\Model\Despatch\Despatch
    {
        $company = (new \Greenter\Model\Company\Company())
            ->setRuc($this->config->ruc)
            ->setRazonSocial($this->config->razon_social)
            ->setNombreComercial($this->config->nombre_comercial ?: $this->config->razon_social);

        $destinatario = (new \Greenter\Model\Client\Client())
            ->setTipoDoc($guia->destinatario_tipo_doc ?: '6')
            ->setNumDoc($guia->destinatario_num_doc)
            ->setRznSocial($guia->destinatario_nombre ?: 'DESTINATARIO');

        $partida = (new \Greenter\Model\Despatch\Direction($guia->partida_ubigeo, $guia->partida_direccion));
        $llegada = (new \Greenter\Model\Despatch\Direction($guia->llegada_ubigeo, $guia->llegada_direccion));

        $envio = (new \Greenter\Model\Despatch\Shipment())
            ->setCodTraslado($guia->motivo_codigo)
            ->setDesTraslado($guia->motivo_desc ?: 'Traslado')
            ->setModTraslado($guia->modalidad)
            ->setFecTraslado(new \DateTime($guia->fecha_traslado?->toDateString() ?? 'now'))
            ->setPesoTotal((float) $guia->peso_total)
            ->setUndPesoTotal($guia->unidad_peso ?: 'KGM')
            ->setNumBultos($guia->num_bultos)
            ->setPartida($partida)
            ->setLlegada($llegada);

        if ($guia->esPrivado()) {
            $vehiculo = (new \Greenter\Model\Despatch\Vehicle())->setPlaca($guia->vehiculo_placa);
            $chofer = (new \Greenter\Model\Despatch\Driver())
                ->setTipo('Principal')
                ->setTipoDoc('1')
                ->setNroDoc($guia->chofer_doc)
                ->setLicencia($guia->chofer_licencia)
                ->setNombres($guia->chofer_nombre ?: '-')
                ->setApellidos('-');
            $envio->setVehiculo($vehiculo)->setChoferes([$chofer]);
        } else {
            $transportista = (new \Greenter\Model\Despatch\Transportist())
                ->setTipoDoc('6')
                ->setNumDoc($guia->transportista_doc)
                ->setRznSocial($guia->transportista_nombre ?: '-')
                ->setNroMtc($guia->transportista_mtc);
            $envio->setTransportista($transportista);
        }

        $details = [];
        foreach (($guia->items ?? []) as $i => $item) {
            $details[] = (new \Greenter\Model\Despatch\DespatchDetail())
                ->setCantidad((float) ($item['cantidad'] ?? 1))
                ->setUnidad($item['unidad'] ?? 'NIU')
                ->setDescripcion($item['descripcion'] ?? 'Bien')
                ->setCodigo($item['codigo'] ?? ('ITEM'.($i + 1)));
        }

        return (new \Greenter\Model\Despatch\Despatch())
            ->setVersion('2022')
            ->setTipoDoc('09') // Guía de remisión remitente
            ->setSerie($guia->serie)
            ->setCorrelativo((string) $guia->numero)
            ->setFechaEmision(new \DateTime())
            ->setCompany($company)
            ->setDestinatario($destinatario)
            ->setEnvio($envio)
            ->setDetails($details);
    }

    private function guardarXml(GuiaRemision $guia, ?string $xml): ?string
    {
        if (blank($xml)) {
            return null;
        }
        $ruta = 'facturacion/pe/gre/xml/'.$guia->serie_numero.'.xml';
        Storage::disk('local')->put($ruta, $xml);

        return $ruta;
    }

    private function guardarZip(string $ruta, ?string $zip): ?string
    {
        if (blank($zip)) {
            return null;
        }
        Storage::disk('local')->put($ruta, $zip);

        return $ruta;
    }
}
