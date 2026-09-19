<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRepuesto extends Model
{
    protected $table = 'orden_repuesto';

    protected $guarded = ['id'];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function repuesto()
    {
        return $this->belongsTo(Repuesto::class);
    }
}
