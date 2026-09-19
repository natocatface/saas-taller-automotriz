<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $guarded = ['id'];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class);
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo === 'empresa') {
            return $this->razon_social ?: $this->nombre;
        }
        return trim($this->nombre.' '.$this->apellidos);
    }
}
