<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Str;

class GenerateDummyData extends Command
{
    protected $signature = 'app:generate-dummy-data';
    protected $description = 'Generates 10 dummy records for each module in the past 6 months.';

    public function handle()
    {
        $faker = Faker::create();
        $this->info('Generating dummy data...');

        // Clientes
        $clienteIds = [];
        for ($i = 0; $i < 10; $i++) {
            $clienteIds[] = DB::table('clientes')->insertGetId([
                'tipo_documento' => 'DNI',
                'documento' => $faker->unique()->numerify('########'),
                'nombre' => $faker->firstName,
                'apellidos' => $faker->lastName,
                'telefono' => $faker->numerify('9########'),
                'email' => $faker->unique()->safeEmail,
                'ciudad' => 'Lima',
                'tipo' => 'persona',
                'activo' => 1,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Clientes generados.');

        // Vehículos
        $vehiculoIds = [];
        foreach ($clienteIds as $clienteId) {
            $vehiculoIds[] = DB::table('vehiculos')->insertGetId([
                'cliente_id' => $clienteId,
                'placa' => strtoupper($faker->bothify('???-###')),
                'marca' => $faker->randomElement(['Toyota', 'Nissan', 'Honda', 'Kia', 'Hyundai']),
                'modelo' => $faker->word,
                'anio' => $faker->numberBetween(2000, 2024),
                'color' => $faker->safeColorName,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Vehículos generados.');

        // Proveedores
        $proveedorIds = [];
        for ($i = 0; $i < 10; $i++) {
            $proveedorIds[] = DB::table('proveedores')->insertGetId([
                'ruc' => $faker->unique()->numerify('20########1'),
                'nombre' => $faker->company,
                'contacto' => $faker->name,
                'telefono' => $faker->numerify('01#######'),
                'email' => $faker->unique()->safeEmail,
                'direccion' => $faker->address,
                'activo' => 1,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Proveedores generados.');

        // Servicios
        $servicioIds = [];
        for ($i = 0; $i < 10; $i++) {
            $servicioIds[] = DB::table('servicios')->insertGetId([
                'codigo' => strtoupper($faker->bothify('SRV-####')),
                'nombre' => 'Servicio ' . $faker->word,
                'categoria' => $faker->randomElement(['Mecánica', 'Electricidad', 'Planchado']),
                'precio' => $faker->randomFloat(2, 50, 500),
                'duracion_min' => $faker->randomElement([30, 60, 90, 120]),
                'activo' => 1,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Servicios generados.');

        // Repuestos
        $repuestoIds = [];
        for ($i = 0; $i < 10; $i++) {
            $repuestoIds[] = DB::table('repuestos')->insertGetId([
                'proveedor_id' => $faker->randomElement($proveedorIds),
                'codigo' => strtoupper($faker->bothify('REP-####')),
                'nombre' => 'Repuesto ' . $faker->word,
                'categoria' => 'Filtros',
                'precio_compra' => $faker->randomFloat(2, 10, 100),
                'precio_venta' => $faker->randomFloat(2, 20, 200),
                'stock' => $faker->numberBetween(5, 50),
                'stock_minimo' => 5,
                'activo' => 1,
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Repuestos generados.');

        // Ordenes
        $users = DB::table('users')->pluck('id')->toArray();
        if (empty($users)) {
            $users = [1];
        }
        $ordenIds = [];
        for ($i = 0; $i < 10; $i++) {
            $fecha = $faker->dateTimeBetween('-6 months', 'now');
            $estado = $faker->randomElement(['en_proceso', 'esperando_repuestos', 'terminado', 'entregado']);
            $estadoPago = ($estado == 'entregado' || $estado == 'terminado') ? 'pagado' : 'pendiente';
            
            $total = $faker->randomFloat(2, 100, 1500);
            $ordenId = DB::table('ordenes')->insertGetId([
                'numero' => 'OT-DUMMY-' . rand(10000, 99999),
                'cliente_id' => $faker->randomElement($clienteIds),
                'vehiculo_id' => $faker->randomElement($vehiculoIds),
                'mecanico_id' => $faker->randomElement($users),
                'fecha_ingreso' => $fecha,
                'fecha_entrega' => $estado == 'entregado' ? $fecha : null,
                'estado' => $estado,
                'estado_pago' => $estadoPago,
                'total' => $total,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
            $ordenIds[] = $ordenId;

            // Orden_servicio
            DB::table('orden_servicio')->insert([
                'orden_id' => $ordenId,
                'servicio_id' => $faker->randomElement($servicioIds),
                'descripcion' => 'Servicio ' . $faker->word,
                'cantidad' => 1,
                'precio' => $total,
                'subtotal' => $total,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }
        $this->info('Órdenes generadas.');

        // Citas
        for ($i = 0; $i < 10; $i++) {
            DB::table('citas')->insert([
                'cliente_id' => $faker->randomElement($clienteIds),
                'vehiculo_id' => $faker->randomElement($vehiculoIds),
                'mecanico_id' => $faker->randomElement($users),
                'titulo' => 'Cita ' . $faker->word,
                'fecha_hora' => $faker->dateTimeBetween('-6 months', '+1 month'),
                'duracion_min' => 60,
                'estado' => $faker->randomElement(['pendiente', 'confirmada', 'atendida']),
                'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                'updated_at' => Carbon::now(),
            ]);
        }
        $this->info('Citas generadas.');

        // Compras
        for ($i = 0; $i < 10; $i++) {
            $fecha = $faker->dateTimeBetween('-6 months', 'now');
            $compraId = DB::table('compras')->insertGetId([
                'numero' => 'C-DUMMY-' . rand(10000, 99999),
                'proveedor_id' => $faker->randomElement($proveedorIds),
                'user_id' => $users[0] ?? 1,
                'fecha' => $fecha,
                'total' => $faker->randomFloat(2, 500, 2000),
                'estado' => 'registrada',
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }
        $this->info('Compras generadas.');

        $this->info('Dummy data generation completed successfully!');
    }
}
