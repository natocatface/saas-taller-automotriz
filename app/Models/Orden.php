<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_entrega' => 'date',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function mecanico()
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }

    public function servicios()
    {
        return $this->hasMany(OrdenServicio::class);
    }

    public function repuestos()
    {
        return $this->hasMany(OrdenRepuesto::class);
    }

    public static function estados(): array
    {
        return [
            'recepcion' => 'Recepción',
            'diagnostico' => 'Diagnóstico',
            'en_proceso' => 'En proceso',
            'esperando_repuestos' => 'Esperando repuestos',
            'terminado' => 'Terminado',
            'entregado' => 'Entregado',
            'anulado' => 'Anulado',
        ];
    }

    public function estadoColor(): string
    {
        return [
            'recepcion' => 'secondary',
            'diagnostico' => 'info',
            'en_proceso' => 'warning',
            'esperando_repuestos' => 'orange',
            'terminado' => 'success',
            'entregado' => 'primary',
            'anulado' => 'danger',
        ][$this->estado] ?? 'secondary';
    }
}
