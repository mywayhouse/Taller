<?php
namespace App\Models;

use App\Core\Model;

class Vehiculo extends Model
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_vehiculos');
    }

    public function obtenerPorPlaca(string $placa): ?array
    {
        $result = $this->callProcedure('sp_obtener_vehiculo_por_placa', [
            ':p_placa' => $placa
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $placa, string $marca, string $modelo, int $anio, string $tipo, int $idCliente): int
    {
        return $this->callNonQuery('sp_insertar_vehiculo', [
            ':p_placa'      => $placa,
            ':p_marca'      => $marca,
            ':p_modelo'     => $modelo,
            ':p_anio'       => $anio,
            ':p_tipo'       => $tipo,
            ':p_id_cliente' => $idCliente
        ]);
    }

    public function actualizar(string $placa, string $marca, string $modelo, int $anio, string $tipo, int $idCliente): int
    {
        return $this->callNonQuery('sp_actualizar_vehiculo', [
            ':p_placa'      => $placa,
            ':p_marca'      => $marca,
            ':p_modelo'     => $modelo,
            ':p_anio'       => $anio,
            ':p_tipo'       => $tipo,
            ':p_id_cliente' => $idCliente
        ]);
    }

    public function eliminar(string $placa): int
    {
        return $this->callNonQuery('sp_eliminar_vehiculo', [
            ':p_placa' => $placa
        ]);
    }

    public function buscarClientePorRtn(string $rtn): ?array
    {
        $result = $this->callProcedure('sp_buscar_cliente_por_rtn', [
            ':p_rtn_dni' => $rtn
        ]);
        return $result[0] ?? null;
    }
}
