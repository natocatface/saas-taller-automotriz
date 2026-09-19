<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Taller extends Model
{
    protected $table = 'talleres';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoSuscripcion::class);
    }

    public static function estados(): array
    {
        return [
            'prueba' => 'En prueba',
            'activo' => 'Activo',
            'suspendido' => 'Suspendido',
            'cancelado' => 'Cancelado',
        ];
    }

    public function estadoColor(): string
    {
        return [
            'prueba' => 'info',
            'activo' => 'success',
            'suspendido' => 'warning',
            'cancelado' => 'danger',
        ][$this->estado] ?? 'secondary';
    }

    public function getDiasRestantesAttribute(): ?int
    {
        if (! $this->fecha_vencimiento) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->fecha_vencimiento, false);
    }

    public function getPorVencerAttribute(): bool
    {
        $d = $this->dias_restantes;

        return $d !== null && $d >= 0 && $d <= 7;
    }

    public function getVencidoAttribute(): bool
    {
        $d = $this->dias_restantes;

        return $d !== null && $d < 0;
    }
}
