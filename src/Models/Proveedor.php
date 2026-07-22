<?php
namespace App\Models;

use App\Core\Modelo;

class Proveedor extends Modelo
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_proveedores');
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_proveedor_por_id', [
            ':p_id_proveedor' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $nombre, ?string $contacto, ?string $telefono, ?string $direccion): int
    {
        return $this->callNonQuery('sp_insertar_proveedor', [
            ':p_nombre'    => $nombre,
            ':p_contacto'  => $contacto ?? '',
            ':p_telefono'  => $telefono ?? '',
            ':p_direccion' => $direccion ?? '',
        ]);
    }

    public function actualizar(int $id, string $nombre, ?string $contacto, ?string $telefono, ?string $direccion): int
    {
        return $this->callNonQuery('sp_actualizar_proveedor', [
            ':p_id_proveedor' => $id,
            ':p_nombre'       => $nombre,
            ':p_contacto'     => $contacto ?? '',
            ':p_telefono'     => $telefono ?? '',
            ':p_direccion'    => $direccion ?? '',
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_proveedor', [
            ':p_id_proveedor' => $id,
        ]);
    }
}
