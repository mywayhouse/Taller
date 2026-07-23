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
            ':id_proveedor' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $nombre, ?string $contacto, ?string $telefono, ?string $correo, ?string $direccion, ?string $rtn): int
    {
        return $this->callNonQuery('sp_insertar_proveedor', [
            ':nombre'    => $nombre,
            ':contacto'  => $contacto ?? '',
            ':telefono'  => $telefono ?? '',
            ':correo'    => $correo ?? '',
            ':direccion' => $direccion ?? '',
            ':rtn'       => $rtn ?? '',
        ]);
    }

    public function actualizar(int $id, string $nombre, ?string $contacto, ?string $telefono, ?string $correo, ?string $direccion, ?string $rtn): int
    {
        return $this->callNonQuery('sp_actualizar_proveedor', [
            ':id_proveedor' => $id,
            ':nombre'       => $nombre,
            ':contacto'     => $contacto ?? '',
            ':telefono'     => $telefono ?? '',
            ':correo'       => $correo ?? '',
            ':direccion'    => $direccion ?? '',
            ':rtn'          => $rtn ?? '',
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_proveedor', [
            ':id_proveedor' => $id,
        ]);
    }

    public function buscar(string $termino): array
    {
        return $this->callProcedure('sp_buscar_proveedores', [
            ':termino' => $termino,
        ]);
    }
}
