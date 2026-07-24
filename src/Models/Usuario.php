<?php
namespace App\Models;

use App\Core\Modelo;

class Usuario extends Modelo
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_usuarios');
    }

    public function buscar(string $termino, string $rol = ''): array
    {
        return $this->callProcedure('sp_buscar_usuarios', [
            ':p_termino' => $termino,
            ':p_rol'     => $rol,
        ]);
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_usuario_por_id', [
            ':p_id_usuario' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function obtenerPorCorreo(string $correo): ?array
    {
        $result = $this->callProcedure('sp_obtener_usuario_por_correo', [
            ':correo' => $correo,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $nombre, string $correo, string $contrasenia, string $rol): int
    {
        return $this->callNonQuery('sp_insertar_usuario', [
            ':p_nombre'      => $nombre,
            ':p_correo'      => $correo,
            ':p_contrasenia' => $contrasenia,
            ':p_rol'         => $rol,
        ]);
    }

    public function actualizar(int $id, string $nombre, string $correo, string $rol): void
    {
        $this->callNonQuery('sp_actualizar_usuario', [
            ':p_id_usuario'  => $id,
            ':p_nombre'      => $nombre,
            ':p_correo'      => $correo,
            ':p_rol'         => $rol,
        ]);
    }

    public function actualizarContrasenia(int $id, string $contrasenia): int
    {
        return $this->callNonQuery('sp_actualizar_contrasenia', [
            ':p_id_usuario'  => $id,
            ':p_contrasenia' => $contrasenia,
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_usuario', [
            ':p_id_usuario' => $id,
        ]);
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
