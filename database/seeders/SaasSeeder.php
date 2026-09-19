<?php

namespace Database\Seeders;

use App\Models\PagoSuscripcion;
use App\Models\Plan;
use App\Models\Taller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class SaasSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Usuario Super Admin =====
        User::updateOrCreate(
            ['email' => 'superadmin@autotaller.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('password'),
                'rol' => 'superadmin',
                'telefono' => '900000000',
                'activo' => true,
            ]
        );

        if (Plan::count() > 0) {
            $this->command?->warn('Los planes ya existen. No se generaron duplicados.');

            return;
        }

        // ===== Planes =====
        $basico = Plan::create([
            'nombre' => 'Básico', 'precio_mensual' => 99, 'precio_anual' => 990,
            'limite_usuarios' => 3, 'limite_ordenes' => 100, 'orden' => 1,
            'descripcion' => 'Ideal para talleres que están empezando.',
            'caracteristicas' => "Hasta 3 usuarios\n100 órdenes por mes\nGestión de clientes y vehículos\nInventario básico\nSoporte por correo",
        ]);
        $pro = Plan::create([
            'nombre' => 'Profesional', 'precio_mensual' => 199, 'precio_anual' => 1990,
            'limite_usuarios' => 10, 'limite_ordenes' => 500, 'destacado' => true, 'orden' => 2,
            'descripcion' => 'El favorito de los talleres en crecimiento.',
            'caracteristicas' => "Hasta 10 usuarios\n500 órdenes por mes\nFacturación y caja\nReportes avanzados\nImportar / exportar datos\nSoporte prioritario",
        ]);
        $empresarial = Plan::create([
            'nombre' => 'Empresarial', 'precio_mensual' => 399, 'precio_anual' => 3990,
            'limite_usuarios' => 0, 'limite_ordenes' => 0, 'orden' => 3,
            'descripcion' => 'Para cadenas y talleres de alto volumen.',
            'caracteristicas' => "Usuarios ilimitados\nÓrdenes ilimitadas\nMulti-sucursal\nRoles y permisos avanzados\nAPI e integraciones\nSoporte 24/7 dedicado",
        ]);

        // ===== Talleres suscritos (demo) =====
        $planes = [$basico, $pro, $empresarial];
        $data = [
            ['Taller MotorMax', 'activo', 'anual', -300, 40],
            ['Servicentro El Rápido', 'activo', 'mensual', -20, 10],
            ['AutoFix Premium', 'prueba', 'mensual', -5, 9],
            ['Mecánica González', 'activo', 'mensual', -60, 3],
            ['Taller Los Andes', 'suspendido', 'mensual', -50, -10],
            ['CarService Pro', 'activo', 'anual', -120, 240],
            ['Lubricentro Central', 'prueba', 'mensual', -2, 12],
            ['Taller Hermanos Ruiz', 'cancelado', 'mensual', -200, -80],
            ['AutoElite', 'activo', 'mensual', -15, 15],
            ['Multiservicios Díaz', 'activo', 'anual', -400, 330],
        ];

        foreach ($data as $i => $d) {
            $plan = $planes[$i % 3];
            $inicio = Carbon::today()->addDays($d[3]);
            $venc = Carbon::today()->addDays($d[4]);
            $taller = Taller::create([
                'nombre' => $d[0],
                'ruc' => '20'.rand(100000000, 999999999),
                'contacto_nombre' => 'Contacto '.($i + 1),
                'email' => strtolower(str_replace(' ', '', $d[0])).'@correo.com',
                'telefono' => '9'.rand(10000000, 99999999),
                'ciudad' => ['Lima', 'Arequipa', 'Trujillo', 'Cusco'][array_rand([0, 1, 2, 3])],
                'plan_id' => $plan->id,
                'estado' => $d[1],
                'periodo' => $d[2],
                'fecha_inicio' => $inicio->toDateString(),
                'fecha_vencimiento' => $venc->toDateString(),
            ]);

            // Pagos históricos
            if (in_array($d[1], ['activo', 'suspendido', 'cancelado'])) {
                $monto = $d[2] === 'anual' ? $plan->precio_anual : $plan->precio_mensual;
                foreach (range(1, rand(1, 4)) as $k) {
                    PagoSuscripcion::create([
                        'taller_id' => $taller->id,
                        'plan_id' => $plan->id,
                        'monto' => $monto,
                        'fecha_pago' => Carbon::today()->subMonths($k)->toDateString(),
                        'periodo' => $d[2],
                        'metodo' => ['transferencia', 'tarjeta', 'yape'][array_rand([0, 1, 2])],
                        'referencia' => 'PAG-'.rand(10000, 99999),
                        'cubre_hasta' => Carbon::today()->subMonths($k - 1)->toDateString(),
                    ]);
                }
            }
        }

        $this->command?->info('Datos SaaS (planes, talleres, pagos y super admin) generados.');
    }
}
