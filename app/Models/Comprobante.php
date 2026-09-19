<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $table = 'comprobantes';

    protected $guarded = ['id'];

    protected $casts = ['fecha' => 'date'];

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

    /** Comprobante afectado por esta nota (crédito/débito). */
    public function docAfectado()
    {
        return $this->belongsTo(Comprobante::class, 'doc_afectado_id');
    }

    /** Notas de crédito/débito emitidas sobre este comprobante. */
    public function notas()
    {
        return $this->hasMany(Comprobante::class, 'doc_afectado_id');
    }

    public function getSerieNumeroAttribute(): string
    {
        return $this->serie.'-'.str_pad($this->numero, 6, '0', STR_PAD_LEFT);
    }

    public function esNota(): bool
    {
        return in_array($this->tipo, ['nota_credito', 'nota_debito'], true);
    }

    /** Etiqueta legible del tipo de comprobante. */
    public function getTipoLabelAttribute(): string
    {
        return [
            'boleta'       => 'Boleta',
            'factura'      => 'Factura',
            'nota_credito' => 'Nota de crédito',
            'nota_debito'  => 'Nota de débito',
        ][$this->tipo] ?? ucfirst($this->tipo);
    }

    /** Código de tipo de documento SUNAT (catálogo 01). */
    public function tipoDocSunat(): string
    {
        return [
            'boleta'       => '03',
            'factura'      => '01',
            'nota_credito' => '07',
            'nota_debito'  => '08',
        ][$this->tipo] ?? '03';
    }

    /** Catálogo 09 SUNAT: motivos de nota de crédito. */
    public static function motivosCredito(): array
    {
        return [
            '01' => 'Anulación de la operación',
            '02' => 'Anulación por error en el RUC',
            '03' => 'Corrección por error en la descripción',
            '04' => 'Descuento global',
            '05' => 'Descuento por ítem',
            '06' => 'Devolución total',
            '07' => 'Devolución por ítem',
            '08' => 'Bonificación',
            '09' => 'Disminución en el valor',
            '10' => 'Otros conceptos',
        ];
    }

    /** Catálogo 10 SUNAT: motivos de nota de débito. */
    public static function motivosDebito(): array
    {
        return [
            '01' => 'Intereses por mora',
            '02' => 'Aumento en el valor',
            '03' => 'Penalidades / otros conceptos',
        ];
    }
}
