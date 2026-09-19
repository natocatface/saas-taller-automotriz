<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plan extends Model
{
    protected $table = 'planes';

    protected $guarded = ['id'];

    protected $casts = [
        'destacado' => 'boolean',
        'activo' => 'boolean',
    ];

    public function talleres()
    {
        return $this->hasMany(Taller::class);
    }

    public function getListaCaracteristicasAttribute(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->caracteristicas))
            ->map(fn ($l) => trim($l))->filter()->values()->all();
    }

    public function getLimiteUsuariosTextoAttribute(): string
    {
        return $this->limite_usuarios > 0 ? $this->limite_usuarios.' usuarios' : 'Usuarios ilimitados';
    }

    public function getLimiteOrdenesTextoAttribute(): string
    {
        return $this->limite_ordenes > 0 ? $this->limite_ordenes.' órdenes/mes' : 'Órdenes ilimitadas';
    }

    protected static function booted(): void
    {
        static::saving(function (Plan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->nombre);
            }
        });
    }
}
