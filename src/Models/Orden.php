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
        string $placaVehiculo,
        string $tipoServicio = 'REVISION'
    ): int {
        $result = $this->callProcedure('sp_insertar_orden', [
            ':p_diagnostico_preliminar' => $diagnostico,
            ':p_fecha_ingreso'          => $fechaIngreso,
            ':p_id_recepcionista'       => $idRecepcionista,
            ':p_id_mecanico'            => $idMecanico,
            ':p_placa_vehiculo'         => $placaVehiculo,
            ':p_tipo_servicio'          => $tipoServicio,
        ]);
        return (int) ($result[0]['id_orden'] ?? 0);
    }

    public function actualizar(int $id, string $diagnostico, string $estado, int $idMecanico, string $placaVehiculo, float $costoManoObra, string $tipoServicio = 'REVISION'): int
    {
        return $this->callNonQuery('sp_actualizar_orden', [
            ':p_id_orden'              => $id,
            ':p_diagnostico_preliminar' => $diagnostico,
            ':p_estado'                => $estado,
            ':p_id_mecanico'           => $idMecanico,
            ':p_placa_vehiculo'        => $placaVehiculo,
            ':p_costo_mano_obra'       => $costoManoObra,
            ':p_tipo_servicio'         => $tipoServicio,
        ]);
    }

    public function actualizarEstado(int $id, string $estado): int
    {
        return $this->callNonQuery('sp_actualizar_estado_orden', [
            ':p_id_orden' => $id,
            ':p_estado'   => $estado,
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_orden', [
            ':p_id_orden' => $id,
        ]);
    }

    public function buscar(string $termino, string $estado = ''): array
    {
        return $this->callProcedure('sp_buscar_ordenes', [
            ':p_termino' => $termino,
            ':p_estado'  => $estado,
        ]);
    }

    public function listarDetalles(int $idOrden): array
    {
        return $this->callProcedure('sp_obtener_repuestos_por_orden', [
            ':p_id_orden' => $idOrden,
        ]);
    }

    public function insertarDetalle(int $idOrden, int $idRepuesto, int $cantidad, float $precio): int
    {
        return $this->callNonQuery('sp_insertar_detalle_orden', [
            ':p_id_orden'   => $idOrden,
            ':p_id_repuesto' => $idRepuesto,
            ':p_cantidad'   => $cantidad,
            ':p_precio'     => $precio,
        ]);
    }

    public function eliminarDetalle(int $idDetalle): int
    {
        return $this->callNonQuery('sp_eliminar_detalle_orden', [
            ':p_id_detalle' => $idDetalle,
        ]);
    }

    public function listarMecanicos(): array
    {
        $db = \App\bd\BaseDatos::getConnection();
        $stmt = $db->query("SELECT id_usuario, nombre, rol FROM usuarios WHERE estado_activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
