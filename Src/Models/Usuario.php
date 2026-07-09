<?php
namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    public function obtenerPorCorreo(string $correo): ?array
    {
        $result = $this->callProcedure('sp_obtener_usuario_por_correo', [
            ':correo' => $correo,
        ]);
        return $result[0] ?? null;
    }

    public function registrarLog(int $idUsuario, string $accion, string $ip): int
    {
        return $this->callNonQuery('sp_registrar_log', [
            ':id_usuario'   => $idUsuario,
            ':accion'       => $accion,
            ':ip_direccion' => $ip,
        ]);
    }
}
