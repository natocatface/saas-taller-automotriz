<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturacionElectronica extends Model
{
    protected $table = 'facturacion_electronica';

    protected $guarded = ['id'];

    protected $casts = [
        'habilitado'           => 'boolean',
        'emitir_automatico'    => 'boolean',
        // Secretos guardados cifrados en la base de datos
        'clave_sol'            => 'encrypted',
        'certificado_password' => 'encrypted',
        'client_secret'        => 'encrypted',
    ];

    /** Devuelve la fila única de configuración (crea una por defecto si no existe). */
    public static function actual(): self
    {
        return static::first() ?? static::create([
            'habilitado' => false,
            'driver'     => 'none',
            'modo'       => 'beta',
        ]);
    }

    /** ¿Está lista para emitir electrónicamente? */
    public function estaOperativa(): bool
    {
        return $this->habilitado
            && $this->driver !== 'none'
            && $this->certificadoExiste()
            && filled($this->ruc)
            && filled($this->usuario_sol);
    }

    /** ¿Existe el archivo del certificado en la ruta indicada? */
    public function certificadoExiste(): bool
    {
        return filled($this->certificado_path) && is_file($this->certificado_path);
    }

    /** Etiqueta legible del entorno SUNAT. */
    public function getModoLabelAttribute(): string
    {
        return $this->modo === 'produccion' ? 'Producción' : 'Beta (homologación)';
    }

    /** Etiqueta legible del driver de emisión. */
    public function getDriverLabelAttribute(): string
    {
        return match ($this->driver) {
            'greenter' => 'Greenter (firma local)',
            default    => 'Ninguno (deja pendiente)',
        };
    }
}
