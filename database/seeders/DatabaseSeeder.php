<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Proveedor;
use App\Models\Repuesto;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Usuarios =====
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@taller.com',
            'password' => Hash::make('password'),
            'rol' => 'admin',
            'telefono' => '999888777',
        ]);

        $mecanicos = collect([
            ['name' => 'Carlos Ramírez', 'email' => 'carlos@taller.com', 'rol' => 'mecanico'],
            ['name' => 'Luis Torres', 'email' => 'luis@taller.com', 'rol' => 'mecanico'],
            ['name' => 'Miguel Ángel Díaz', 'email' => 'miguel@taller.com', 'rol' => 'mecanico'],
            ['name' => 'Gerente General', 'email' => 'gerente@taller.com', 'rol' => 'gerente'],
        ])->map(fn ($u) => User::create([
            'name' => $u['name'],
            'email' => $u['email'],
            'password' => Hash::make('password'),
            'rol' => $u['rol'],
        ]));

        // ===== Proveedores =====
        $prov = collect([
            ['nombre' => 'Repuestos San Martín', 'ruc' => '20123456781', 'telefono' => '014567890'],
            ['nombre' => 'AutoPartes del Perú', 'ruc' => '20456789012', 'telefono' => '013334455'],
            ['nombre' => 'Lubricantes Premium', 'ruc' => '20567890123', 'telefono' => '015556677'],
        ])->map(fn ($p) => Proveedor::create($p));

        // ===== Servicios =====
        $servicios = [
            ['nombre' => 'Cambio de aceite y filtro', 'categoria' => 'Mantenimiento', 'precio' => 120, 'duracion_min' => 40],
            ['nombre' => 'Alineamiento y balanceo', 'categoria' => 'Suspensión', 'precio' => 90, 'duracion_min' => 60],
            ['nombre' => 'Cambio de pastillas de freno', 'categoria' => 'Frenos', 'precio' => 150, 'duracion_min' => 90],
            ['nombre' => 'Afinamiento de motor', 'categoria' => 'Motor', 'precio' => 280, 'duracion_min' => 120],
            ['nombre' => 'Diagnóstico por escáner', 'categoria' => 'Electrónica', 'precio' => 80, 'duracion_min' => 45],
            ['nombre' => 'Cambio de batería', 'categoria' => 'Electricidad', 'precio' => 60, 'duracion_min' => 30],
            ['nombre' => 'Revisión de suspensión', 'categoria' => 'Suspensión', 'precio' => 100, 'duracion_min' => 60],
            ['nombre' => 'Cambio de correa de distribución', 'categoria' => 'Motor', 'precio' => 450, 'duracion_min' => 180],
        ];
        foreach ($servicios as $i => $s) {
            Servicio::create(array_merge($s, ['codigo' => 'SRV-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT)]));
        }

        // ===== Repuestos =====
        $repuestos = [
            ['nombre' => 'Filtro de aceite', 'categoria' => 'Filtros', 'precio_compra' => 15, 'precio_venta' => 28, 'stock' => 45, 'stock_minimo' => 10],
            ['nombre' => 'Aceite 15W-40 (1L)', 'categoria' => 'Lubricantes', 'precio_compra' => 22, 'precio_venta' => 38, 'stock' => 60, 'stock_minimo' => 15],
            ['nombre' => 'Pastillas de freno delanteras', 'categoria' => 'Frenos', 'precio_compra' => 55, 'precio_venta' => 95, 'stock' => 8, 'stock_minimo' => 12],
            ['nombre' => 'Batería 12V 70Ah', 'categoria' => 'Eléctrico', 'precio_compra' => 180, 'precio_venta' => 260, 'stock' => 12, 'stock_minimo' => 5],
            ['nombre' => 'Bujías (juego x4)', 'categoria' => 'Motor', 'precio_compra' => 40, 'precio_venta' => 75, 'stock' => 30, 'stock_minimo' => 8],
            ['nombre' => 'Filtro de aire', 'categoria' => 'Filtros', 'precio_compra' => 18, 'precio_venta' => 32, 'stock' => 4, 'stock_minimo' => 10],
            ['nombre' => 'Correa de distribución', 'categoria' => 'Motor', 'precio_compra' => 85, 'precio_venta' => 140, 'stock' => 15, 'stock_minimo' => 5],
        ];
        foreach ($repuestos as $i => $r) {
            Repuesto::create(array_merge($r, [
                'codigo' => 'REP-'.str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'proveedor_id' => $prov->random()->id,
                'unidad' => 'UND',
            ]));
        }

        // ===== Clientes y vehículos =====
        $clientesData = [
            ['nombre' => 'Juan', 'apellidos' => 'Pérez Gómez', 'documento' => '45678912', 'telefono' => '987654321', 'email' => 'juan.perez@gmail.com'],
            ['nombre' => 'María', 'apellidos' => 'López Vargas', 'documento' => '41236547', 'telefono' => '912345678', 'email' => 'maria.lopez@gmail.com'],
            ['nombre' => 'Pedro', 'apellidos' => 'Sánchez Ruiz', 'documento' => '40012345', 'telefono' => '998877665', 'email' => 'pedro.sanchez@gmail.com'],
            ['nombre' => 'Ana', 'apellidos' => 'Flores Díaz', 'documento' => '44556677', 'telefono' => '955443322', 'email' => 'ana.flores@gmail.com'],
            ['nombre' => 'Roberto', 'apellidos' => 'Castro León', 'documento' => '43219876', 'telefono' => '966554433', 'email' => 'roberto.castro@gmail.com'],
        ];
        $marcas = [
            ['Toyota', 'Corolla'], ['Hyundai', 'Accent'], ['Kia', 'Rio'],
            ['Nissan', 'Sentra'], ['Volkswagen', 'Gol'], ['Chevrolet', 'Sail'],
        ];
        $placas = ['ABC-123', 'XYZ-789', 'DEF-456', 'GHI-321', 'JKL-654', 'MNO-987'];
        $colores = ['Blanco', 'Negro', 'Gris', 'Rojo', 'Azul', 'Plata'];

        $vehiculos = collect();
        foreach ($clientesData as $i => $c) {
            $cliente = Cliente::create(array_merge($c, [
                'tipo_documento' => 'DNI',
                'tipo' => 'persona',
                'ciudad' => 'Lima',
            ]));
            $m = $marcas[$i % count($marcas)];
            $vehiculos->push(Vehiculo::create([
                'cliente_id' => $cliente->id,
                'placa' => $placas[$i % count($placas)],
                'marca' => $m[0],
                'modelo' => $m[1],
                'anio' => rand(2012, 2023),
                'color' => $colores[$i % count($colores)],
                'combustible' => 'gasolina',
                'transmision' => rand(0, 1) ? 'manual' : 'automatica',
                'kilometraje' => rand(20000, 150000),
            ]));
        }
        // Cliente empresa
        $empresa = Cliente::create([
            'tipo' => 'empresa', 'tipo_documento' => 'RUC', 'documento' => '20601234567',
            'razon_social' => 'Transportes El Rápido S.A.C.', 'nombre' => 'Transportes El Rápido',
            'telefono' => '014441122', 'email' => 'flota@elrapido.com', 'ciudad' => 'Lima',
        ]);
        $vehiculos->push(Vehiculo::create([
            'cliente_id' => $empresa->id, 'placa' => 'F1B-200', 'marca' => 'Toyota',
            'modelo' => 'Hiace', 'anio' => 2020, 'color' => 'Blanco', 'combustible' => 'diesel',
            'transmision' => 'manual', 'kilometraje' => 98000,
        ]));

        // ===== Órdenes de trabajo =====
        $estados = ['recepcion', 'diagnostico', 'en_proceso', 'esperando_repuestos', 'terminado', 'entregado'];
        $srvAll = Servicio::all();
        $repAll = Repuesto::all();
        for ($i = 1; $i <= 18; $i++) {
            $veh = $vehiculos->random();
            $estado = $estados[array_rand($estados)];
            $fechaIng = now()->subDays(rand(0, 25));
            $orden = Orden::create([
                'numero' => 'OT-'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'cliente_id' => $veh->cliente_id,
                'vehiculo_id' => $veh->id,
                'mecanico_id' => $mecanicos->random()->id,
                'fecha_ingreso' => $fechaIng->toDateString(),
                'fecha_entrega' => in_array($estado, ['terminado', 'entregado']) ? $fechaIng->copy()->addDays(rand(1, 4))->toDateString() : null,
                'kilometraje' => rand(20000, 150000),
                'diagnostico' => 'Revisión general y mantenimiento preventivo.',
                'estado' => $estado,
                'prioridad' => ['baja', 'media', 'alta'][array_rand([0, 1, 2])],
                'estado_pago' => in_array($estado, ['entregado']) ? 'pagado' : 'pendiente',
            ]);

            $subtotal = 0;
            foreach ($srvAll->random(rand(1, 3)) as $srv) {
                $orden->servicios()->create([
                    'servicio_id' => $srv->id,
                    'descripcion' => $srv->nombre,
                    'cantidad' => 1,
                    'precio' => $srv->precio,
                    'subtotal' => $srv->precio,
                ]);
                $subtotal += $srv->precio;
            }
            foreach ($repAll->random(rand(0, 2)) as $rep) {
                $cant = rand(1, 2);
                $sub = $rep->precio_venta * $cant;
                $orden->repuestos()->create([
                    'repuesto_id' => $rep->id,
                    'descripcion' => $rep->nombre,
                    'cantidad' => $cant,
                    'precio' => $rep->precio_venta,
                    'subtotal' => $sub,
                ]);
                $subtotal += $sub;
            }
            $impuesto = round($subtotal * 0.18, 2);
            $orden->update([
                'subtotal' => $subtotal,
                'impuesto' => $impuesto,
                'total' => $subtotal + $impuesto,
            ]);
        }

        // ===== Citas =====
        foreach (range(1, 8) as $i) {
            $veh = $vehiculos->random();
            Cita::create([
                'cliente_id' => $veh->cliente_id,
                'vehiculo_id' => $veh->id,
                'mecanico_id' => $mecanicos->random()->id,
                'titulo' => 'Mantenimiento preventivo',
                'fecha_hora' => now()->addDays(rand(0, 10))->setHour(rand(8, 17))->setMinute(0),
                'duracion_min' => 60,
                'motivo' => 'Cliente solicita revisión general.',
                'estado' => ['pendiente', 'confirmada'][array_rand([0, 1])],
            ]);
        }
    }
}
