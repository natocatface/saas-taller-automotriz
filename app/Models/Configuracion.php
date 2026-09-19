<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $guarded = ['id'];

    /** Devuelve la fila de configuración (crea una por defecto si no existe). */
    public static function actual(): self
    {
        return static::first() ?? static::create(['empresa' => 'AutoTaller Pro', 'igv' => 18]);
    }
}
