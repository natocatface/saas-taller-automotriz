<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalle';

    protected $guarded = ['id'];

    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
}
