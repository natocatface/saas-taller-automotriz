<?php

namespace App\Support;

use App\Models\User;

class Permisos
{
    /**
     * Módulo => roles con acceso. El rol "admin" siempre tiene acceso total.
     * Un módulo no listado se considera permitido para todos los autenticados.
     */
    public static function mapa(): array
    {
        return [
            'dashboard'     => ['admin', 'gerente', 'mecanico', 'empleado'],
            'ordenes'       => ['admin', 'gerente', 'mecanico', 'empleado'],
            'citas'         => ['admin', 'gerente', 'mecanico', 'empleado'],
            'servicios'     => ['admin', 'gerente'],
            'clientes'      => ['admin', 'gerente', 'empleado'],
            'vehiculos'     => ['admin', 'gerente', 'mecanico', 'empleado'],
            'repuestos'     => ['admin', 'gerente'],
            'proveedores'   => ['admin', 'gerente'],
            'compras'       => ['admin', 'gerente'],
            'facturacion'   => ['admin', 'gerente', 'empleado'],
            'guias'         => ['admin', 'gerente', 'empleado'],
            'caja'          => ['admin', 'gerente', 'empleado'],
            'personal'      => ['admin'],
            'reportes'      => ['admin', 'gerente'],
            'configuracion' => ['admin'],
            'perfil'        => ['admin', 'gerente', 'mecanico', 'empleado'],
            'export'        => ['admin', 'gerente', 'empleado'],
            'import'        => ['admin', 'gerente'],
        ];
    }

    public static function puede(string $modulo, ?User $user = null): bool
    {
        $user = $user ?: auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->rol === 'admin') {
            return true;
        }
        $roles = static::mapa()[$modulo] ?? null;

        return $roles === null ? true : in_array($user->rol, $roles, true);
    }

    /** Etiquetas legibles de cada rol. */
    public static function rolLabel(string $rol): string
    {
        return [
            'admin' => 'Administrador',
            'gerente' => 'Gerente',
            'mecanico' => 'Mecánico',
            'empleado' => 'Empleado',
        ][$rol] ?? ucfirst($rol);
    }
}
