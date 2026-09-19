<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaRemision extends Model
{
    protected $table = 'guias_remision';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha'          => 'date',
        'fecha_traslado' => 'date',
        'items'          => 'array',
        'enviado_at'     => 'datetime',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSerieNumeroAttribute(): string
    {
        return $this->serie.'-'.str_pad($this->numero, 8, '0', STR_PAD_LEFT);
    }

    public function esPrivado(): bool
    {
        return $this->modalidad === '02';
    }

    /** Catálogo 20 SUNAT: motivos de traslado (subconjunto habitual de un taller). */
    public static function motivosTraslado(): array
    {
        return [
            '01' => 'Venta',
            '02' => 'Compra',
            '04' => 'Traslado entre establecimientos de la misma empresa',
            '08' => 'Importación',
            '09' => 'Exportación',
            '13' => 'Otros',
            '14' => 'Venta sujeta a confirmación del comprador',
            '18' => 'Traslado emisor itinerante de comprobantes',
        ];
    }

    public static function modalidades(): array
    {
        return [
            '02' => 'Transporte privado',
            '01' => 'Transporte público',
        ];
    }
}
