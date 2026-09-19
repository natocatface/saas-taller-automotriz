<?php

namespace App\Console\Commands;

use App\Models\FacturacionElectronica;
use App\Services\Sunat\SunatService;
use Illuminate\Console\Command;

class SunatTest extends Command
{
    protected $signature = 'sunat:test {tipo=boleta : boleta o factura}';

    protected $description = 'Envía un comprobante de prueba a SUNAT (entorno beta) para validar la homologación.';

    public function handle(): int
    {
        $tipo = strtolower($this->argument('tipo'));
        if (! in_array($tipo, ['boleta', 'factura'], true)) {
            $this->error('El tipo debe ser "boleta" o "factura".');

            return self::FAILURE;
        }

        $config = FacturacionElectronica::actual();

        $this->line('');
        $this->info('  Facturación Electrónica · Prueba SUNAT');
        $this->line('  RUC emisor : '.$config->ruc);
        $this->line('  Driver     : '.$config->driver_label);
        $this->line('  Entorno    : '.$config->modo_label);
        $this->line('  Certificado: '.($config->certificadoExiste() ? 'OK' : 'NO ENCONTRADO'));
        $this->line('');

        if ($config->modo === 'produccion' && ! $this->confirm('Estás en PRODUCCIÓN. ¿Enviar igualmente un comprobante real?', false)) {
            $this->warn('Cancelado.');

            return self::SUCCESS;
        }

        $this->line('  Emitiendo '.$tipo.' de prueba...');
        $result = (new SunatService($config))->emitirDemo($tipo);

        $this->line('');
        match ($result->estado) {
            'aceptado'  => $this->info('  ✔ ACEPTADO: '.$result->mensaje),
            'observado' => $this->warn('  ▲ OBSERVADO: '.$result->mensaje),
            'rechazado' => $this->error('  ✘ RECHAZADO: '.$result->mensaje),
            'pendiente' => $this->warn('  … PENDIENTE: '.$result->mensaje),
            default     => $this->error('  ✘ ERROR: '.$result->mensaje),
        };

        if ($result->hash) {
            $this->line('  Hash: '.$result->hash);
        }
        foreach ($result->observaciones as $obs) {
            $this->line('  · '.$obs);
        }
        $this->line('');

        return $result->success ? self::SUCCESS : self::FAILURE;
    }
}
