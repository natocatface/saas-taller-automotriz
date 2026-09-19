<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repuesto extends Model
{
    use HasFactory;

    protected $table = 'repuestos';

    protected $guarded = ['id'];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
