<?php
// ============================================================
// Cliente.php — Modelo de Clientes
// ============================================================
namespace app\models;

use app\core\Model;

class Cliente extends Model
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_clientes');
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_cliente_por_id', [
            ':id_cliente' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $nombre, string $telefono, string $rntDni): int
    {
        return $this->callNonQuery('sp_insertar_cliente', [
            ':nombre'   => $nombre,
            ':telefono' => $telefono,
            ':rnt_dni'  => $rntDni,
        ]);
    }

    public function actualizar(int $id, string $nombre, string $telefono, string $rntDni): int
    {
        return $this->callNonQuery('sp_actualizar_cliente', [
            ':id_cliente' => $id,
            ':nombre'     => $nombre,
            ':telefono'   => $telefono,
            ':rnt_dni'    => $rntDni,
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_cliente', [
            ':id_cliente' => $id,
        ]);
    }

    /**
     * Busca clientes por RTN o Nombre usando Stored Procedures.
     */
    public function buscarPorRtnONombre(string $term): array
    {
        return $this->callProcedure('sp_buscar_cliente_por_rtn_o_nombre', [
            ':p_term' => $term
        ]);
    }
}