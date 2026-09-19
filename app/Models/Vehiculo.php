<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $guarded = ['id'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    public function getDescripcionAttribute(): string
    {
        return trim("{$this->marca} {$this->modelo} {$this->anio}");
    }
}
