<?php
namespace App\Helpers;

use App\bd\BaseDatos;

class AyudaAuditoria
{
    public static function log(int $idUsuario, string $accion): int
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        return BaseDatos::executeNonQuery('sp_registrar_log', [
            ':id_usuario'   => $idUsuario,
            ':accion'       => $accion,
            ':ip_direccion' => $ip,
        ]);
    }

    public static function logCurrentUser(string $accion): int
    {
        $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);
        if ($idUsuario === 0) {
            return 0;
        }
        return self::log($idUsuario, $accion);
    }

    public static function obtenerLogs(): array
    {
        return BaseDatos::executeProcedure('sp_listar_logs');
    }
}