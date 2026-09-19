<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoSuscripcion extends Model
{
    protected $table = 'pagos_suscripcion';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_pago' => 'date',
        'cubre_hasta' => 'date',
    ];

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
