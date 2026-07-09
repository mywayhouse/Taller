<?php
namespace App\Helpers;

class AccessHelper
{
    private static array $permissions = [
        'dashboard' => ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'],
        'clientes'  => ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'],
        'vehiculos' => ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'],
        'ordenes'   => ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'],
        'repuestos' => ['ADMINISTRADOR', 'RECEPCIONISTA', 'MECANICO'],
        'facturas'  => ['ADMINISTRADOR', 'RECEPCIONISTA'],
        'usuarios'  => ['ADMINISTRADOR'],
        'logs'      => ['ADMINISTRADOR'],
    ];

    private static array $writePermissions = [
        'clientes'  => ['ADMINISTRADOR', 'RECEPCIONISTA'],
        'vehiculos' => ['ADMINISTRADOR', 'RECEPCIONISTA'],
        'ordenes'   => ['ADMINISTRADOR', 'RECEPCIONISTA'],
        'repuestos' => ['ADMINISTRADOR'],
        'facturas'  => ['ADMINISTRADOR'],
        'usuarios'  => ['ADMINISTRADOR'],
    ];

    public static function hasAccess(string $module): bool
    {
        $rol = $_SESSION['usuario_rol'] ?? '';
        return in_array($rol, self::$permissions[$module] ?? []);
    }

    public static function hasWriteAccess(string $module): bool
    {
        $rol = $_SESSION['usuario_rol'] ?? '';
        return in_array($rol, self::$writePermissions[$module] ?? []);
    }

    public static function requireAccess(string $module): void
    {
        if (!self::hasAccess($module)) {
            $_SESSION['error'] = 'No tiene permisos para acceder a este modulo.';
            header('Location: ' . \APP_URL . '/dashboard');
            exit;
        }
    }

    public static function requireWriteAccess(string $module): void
    {
        if (!self::hasWriteAccess($module)) {
            $_SESSION['error'] = 'No tiene permisos para realizar esta accion.';
            header('Location: ' . \APP_URL . '/' . $module);
            exit;
        }
    }

    public static function getAccessibleModules(): array
    {
        $rol = $_SESSION['usuario_rol'] ?? '';
        $accessible = [];
        foreach (self::$permissions as $module => $roles) {
            if (in_array($rol, $roles)) {
                $accessible[] = $module;
            }
        }
        return $accessible;
    }
}
