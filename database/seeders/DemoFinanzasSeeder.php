<?php

namespace Database\Seeders;

use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Configuracion;
use App\Models\MovimientoCaja;
use App\Models\Orden;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoFinanzasSeeder extends Seeder
{
    public function run(): void
    {
        if (Comprobante::count() > 0 || Compra::count() > 0) {
            $this->command?->warn('Ya existen datos de finanzas. No se generaron duplicados.');

            return;
        }

        $config = Configuracion::actual();
        $admin = User::where('rol', 'admin')->first() ?? User::first();
        $metodos = ['efectivo', 'tarjeta', 'transferencia', 'yape'];

        // ===== Comprobantes desde órdenes con importe =====
        $numB = 0;
        $numF = 0;
        foreach (Orden::where('total', '>', 0)->get() as $o) {
            if (rand(1, 10) > 7) {
                continue; // ~30% quedan sin facturar
            }
            $tipo = rand(0, 1) ? 'factura' : 'boleta';
            $serie = $tipo === 'factura' ? $config->serie_factura : $config->serie_boleta;
            $numero = $tipo === 'factura' ? ++$numF : ++$numB;
            $fecha = Carbon::parse($o->fecha_ingreso)->addDays(rand(0, 3));
            $metodo = $metodos[array_rand($metodos)];

            $comp = Comprobante::create([
                'tipo' => $tipo,
                'serie' => $serie,
                'numero' => $numero,
                'orden_id' => $o->id,
                'cliente_id' => $o->cliente_id,
                'user_id' => $admin?->id,
                'fecha' => $fecha->toDateString(),
                'subtotal' => $o->subtotal - $o->descuento,
                'igv' => $o->impuesto,
                'total' => $o->total,
                'metodo_pago' => $metodo,
                'estado' => 'emitido',
            ]);

            $o->update(['estado_pago' => 'pagado']);

            MovimientoCaja::create([
                'tipo' => 'ingreso',
                'concepto' => 'Venta '.$comp->serie_numero.' · Orden '.$o->numero,
                'monto' => $o->total,
                'fecha' => $fecha->toDateString(),
                'metodo_pago' => $metodo,
                'orden_id' => $o->id,
                'comprobante_id' => $comp->id,
                'user_id' => $admin?->id,
            ]);
        }

        // ===== Compras a proveedores (últimos 6 meses) =====
        $proveedores = Proveedor::all();
        $repuestos = Repuesto::all();
        if ($proveedores->isNotEmpty() && $repuestos->isNotEmpty()) {
            for ($i = 1; $i <= 12; $i++) {
                $fecha = Carbon::now()->subMonths(rand(0, 5))->subDays(rand(0, 25));
                $prov = $proveedores->random();

                $compra = Compra::create([
                    'numero' => 'CMP-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                    'proveedor_id' => $prov->id,
                    'user_id' => $admin?->id,
                    'fecha' => $fecha->toDateString(),
                    'tipo_documento' => 'factura',
                    'documento_ref' => 'F'.rand(100, 999).'-'.rand(1000, 9999),
                    'estado' => 'registrada',
                ]);

                $subtotal = 0;
                foreach ($repuestos->random(min(3, $repuestos->count())) as $rep) {
                    $cant = rand(5, 20);
                    $precio = (float) $rep->precio_compra ?: rand(10, 80);
                    $sub = $cant * $precio;
                    $subtotal += $sub;

                    $compra->detalles()->create([
                        'repuesto_id' => $rep->id,
                        'descripcion' => $rep->nombre,
                        'cantidad' => $cant,
                        'precio' => $precio,
                        'subtotal' => $sub,
                    ]);

                    $rep->increment('stock', $cant);
                }

                $impuesto = round($subtotal * ((float) $config->igv) / 100, 2);
                $total = $subtotal + $impuesto;
                $compra->update(['subtotal' => $subtotal, 'impuesto' => $impuesto, 'total' => $total]);

                MovimientoCaja::create([
                    'tipo' => 'egreso',
                    'concepto' => 'Compra '.$compra->numero.' · '.$prov->nombre,
                    'monto' => $total,
                    'fecha' => $fecha->toDateString(),
                    'metodo_pago' => 'transferencia',
                    'user_id' => $admin?->id,
                ]);
            }
        }

        // ===== Movimientos de caja varios =====
        $gastos = [
            ['Pago de alquiler del local', 1500],
            ['Servicios (luz, agua, internet)', 480],
            ['Compra de insumos de limpieza', 120],
            ['Pago de planilla', 4200],
            ['Mantenimiento de herramientas', 260],
        ];
        foreach ($gastos as $g) {
            MovimientoCaja::create([
                'tipo' => 'egreso',
                'concepto' => $g[0],
                'monto' => $g[1],
                'fecha' => Carbon::now()->subDays(rand(0, 40))->toDateString(),
                'metodo_pago' => 'efectivo',
                'user_id' => $admin?->id,
            ]);
        }

        $this->command?->info('Datos demo de finanzas generados correctamente.');
    }
}
