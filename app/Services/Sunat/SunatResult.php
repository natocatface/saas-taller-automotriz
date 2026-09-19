<?php

namespace App\Services\Sunat;

/**
 * Resultado normalizado de una operación contra SUNAT
 * (emisión, prueba de conexión, etc.), independiente del driver usado.
 */
class SunatResult
{
    public function __construct(
        public bool $success,
        public string $estado,            // aceptado | rechazado | observado | pendiente | error
        public ?string $mensaje = null,
        public ?string $hash = null,
        public ?string $ticket = null,
        public ?string $xmlPath = null,
        public ?string $cdrPath = null,
        public array $observaciones = [],
    ) {}

    public static function aceptado(string $mensaje, ?string $hash = null, ?string $xmlPath = null, ?string $cdrPath = null, array $obs = []): self
    {
        return new self(true, 'aceptado', $mensaje, $hash, null, $xmlPath, $cdrPath, $obs);
    }

    public static function observado(string $mensaje, array $obs = [], ?string $hash = null, ?string $xmlPath = null, ?string $cdrPath = null): self
    {
        return new self(true, 'observado', $mensaje, $hash, null, $xmlPath, $cdrPath, $obs);
    }

    public static function rechazado(string $mensaje, array $obs = [], ?string $xmlPath = null): self
    {
        return new self(false, 'rechazado', $mensaje, null, null, $xmlPath, null, $obs);
    }

    public static function pendiente(string $mensaje = 'Emisión no realizada: queda pendiente.'): self
    {
        return new self(false, 'pendiente', $mensaje);
    }

    public static function anulado(string $mensaje, ?string $ticket = null, ?string $cdrPath = null): self
    {
        return new self(true, 'anulado', $mensaje, null, $ticket, null, $cdrPath);
    }

    /** Baja/resumen enviado a SUNAT, con ticket pendiente de confirmar. */
    public static function procesando(string $mensaje, string $ticket): self
    {
        return new self(false, 'anulando', $mensaje, null, $ticket);
    }

    public static function error(string $mensaje): self
    {
        return new self(false, 'error', $mensaje);
    }

    /** Atributos a persistir tras una EMISIÓN. */
    public function toComprobanteAttributes(): array
    {
        return [
            'estado_sunat'        => $this->estado,
            'hash_cpe'            => $this->hash,
            'sunat_ticket'        => $this->ticket,
            'xml_path'            => $this->xmlPath,
            'cdr_path'            => $this->cdrPath,
            'sunat_observaciones' => $this->observacionesTexto(),
            'enviado_at'          => now(),
        ];
    }

    /**
     * Atributos a persistir tras una ANULACIÓN.
     * Conserva el hash y el XML de la emisión original; solo actualiza estado, ticket y CDR de baja.
     */
    public function toAnulacionAttributes(): array
    {
        $attrs = [
            'estado_sunat'        => $this->estado,
            'sunat_observaciones' => $this->observacionesTexto(),
        ];
        if ($this->ticket !== null) {
            $attrs['sunat_ticket'] = $this->ticket;
        }
        if ($this->cdrPath !== null) {
            $attrs['cdr_path'] = $this->cdrPath;
        }

        return $attrs;
    }

    private function observacionesTexto(): string
    {
        return $this->mensaje.($this->observaciones ? ' — '.implode('; ', $this->observaciones) : '');
    }
}
