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


        return $result[0] ?? null;
    }

    /**
     * Inserta un nuevo cliente.
     * Llama al SP: sp_insertar_cliente(:nombre, :telefono, :rnt_dni)
     *
     * @param string $nombre   Nombre completo.
     * @param string $telefono Teléfono (opcional).
     * @param string $rntDni   RTN o DNI.
     * @return int Filas afectadas.
     */
    public function insertar(string $nombre, string $telefono, string $rntDni): int
    {
        return $this->callNonQuery('sp_insertar_cliente', [
            ':nombre'   => $nombre,
            ':telefono' => $telefono,
            ':rnt_dni'  => $rntDni,
        ]);
    }

    /**
     * Actualiza los datos de un cliente existente.
     * Llama al SP: sp_actualizar_cliente(:id_cliente, :nombre, :telefono, :rnt_dni)
     *
     * @param int    $id       ID del cliente.
     * @param string $nombre   Nombre actualizado.
     * @param string $telefono Teléfono actualizado.
     * @param string $rntDni   RTN/DNI actualizado.
     * @return int Filas afectadas.
     */
    public function actualizar(int $id, string $nombre, string $telefono, string $rntDni): int
    {
        return $this->callNonQuery('sp_actualizar_cliente', [
            ':id_cliente' => $id,
            ':nombre'     => $nombre,
            ':telefono'   => $telefono,
            ':rnt_dni'    => $rntDni,
        ]);
    }

    /**
     * Elimina (desactiva lógicamente) un cliente.
     * Llama al SP: sp_eliminar_cliente(:id_cliente)
     *
     * @param int $id ID del cliente.
     * @return int Filas afectadas.
     */
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
}
