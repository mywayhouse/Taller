<?php
// ============================================================
// Usuario.php — Modelo de Usuarios
// ============================================================
// Capa de acceso a datos para la tabla "usuarios".
// Incluye autenticación y registro de logs.
//
// SP esperados en la BD:
//   sp_obtener_usuario_por_correo(:correo)
//   sp_registrar_log(:id_usuario, :accion, :ip_direccion)
// ============================================================

namespace App\models;

use App\core\Model;

class Usuario extends Model
{
    /**
     * Busca un usuario por su correo electrónico.
     * Llama al SP: sp_obtener_usuario_por_correo(:correo)
     *
     * @param string $correo Correo del usuario.
     * @return array|null Datos del usuario o null.
     */
    public function obtenerPorCorreo(string $correo): ?array
    {
        $result = $this->callProcedure('sp_obtener_usuario_por_correo', [
            ':correo' => $correo,
        ]);

        return $result[0] ?? null;
    }

    /**
     * Registra una acción en la bitácora del sistema.
     * Llama al SP: sp_registrar_log(:id_usuario, :accion, :ip_direccion)
     *
     * @param int    $idUsuario  ID del usuario.
     * @param string $accion     Descripción de la acción.
     * @param string $ip         Dirección IP.
     * @return int Filas afectadas.
     */
    public function registrarLog(int $idUsuario, string $accion, string $ip): int
    {
        return $this->callNonQuery('sp_registrar_log', [
            ':id_usuario'   => $idUsuario,
            ':accion'       => $accion,
            ':ip_direccion' => $ip,
        ]);
    }
}
