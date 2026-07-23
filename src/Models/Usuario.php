<?php

namespace App\Models;

use App\Core\Modelo;

class Usuario extends Modelo
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

    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_usuarios');
    }

    public function insertar(string $nombre, string $correo, string $contrasenia, string $rol): int
    {
        return $this->callNonQuery('sp_insertar_usuario', [
            ':nombre'      => $nombre,
            ':correo'      => $correo,
            ':contrasenia' => $contrasenia,
            ':rol'         => $rol,
        ]);
    }

    public function actualizar(int $id, string $nombre, string $correo, string $contrasenia, string $rol): int
    {
        return $this->callNonQuery('sp_actualizar_usuario', [
            ':id_usuario'  => $id,
            ':nombre'      => $nombre,
            ':correo'      => $correo,
            ':contrasenia' => $contrasenia,
            ':rol'         => $rol,
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_usuario', [
            ':id_usuario' => $id,
        ]);
    }

    public function buscar(string $termino): array
    {
        return $this->callProcedure('sp_buscar_usuarios', [
            ':termino' => $termino,
        ]);
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_usuario_por_id', [
            ':p_id' => $id,
        ]);
        return $result[0] ?? null;
    }
}