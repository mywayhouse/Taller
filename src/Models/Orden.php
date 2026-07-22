<?php
namespace App\Models;

use App\Core\Modelo;

class Orden extends Modelo
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_ordenes');
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_orden_por_id', [
            ':p_id_orden' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(
        string $diagnostico,
        string $fechaIngreso,
        int $idRecepcionista,
        int $idMecanico,
        string $placaVehiculo
    ): int {
        return $this->callNonQuery('sp_insertar_orden', [
            ':p_diagnostico_preliminar' => $diagnostico,
            ':p_fecha_ingreso'          => $fechaIngreso,
            ':p_id_recepcionista'       => $idRecepcionista,
            ':p_id_mecanico'            => $idMecanico,
            ':p_placa_vehiculo'         => $placaVehiculo,
        ]);
    }

    public function actualizarEstado(int $id, string $estado): int
    {
        return $this->callNonQuery('sp_actualizar_estado_orden', [
            ':p_id_orden' => $id,
            ':p_estado'   => $estado,
        ]);
    }
}
