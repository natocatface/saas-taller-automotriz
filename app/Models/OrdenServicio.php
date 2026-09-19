<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    protected $table = 'orden_servicio';

    protected $guarded = ['id'];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
