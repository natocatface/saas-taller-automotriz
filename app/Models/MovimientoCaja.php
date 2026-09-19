<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoCaja extends Model
{
    protected $table = 'movimientos_caja';

    protected $guarded = ['id'];

    protected $casts = ['fecha' => 'date'];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
