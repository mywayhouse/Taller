<?php
namespace app\models;

use PDOException;
use Model; // Core Model

class Vehiculo extends Model
{
    public function obtenerTodos(): array
    {
        $sql = "CALL sp_listar_vehiculos()";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerPorPlaca(string $placa): ?array
    {
        $sql = "CALL sp_obtener_vehiculo_por_placa(:placa)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['placa' => $placa]);
        $vehiculo = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $vehiculo ?: null;
    }

    public function insertar(string $placa, string $marca, string $modelo, int $anio, string $tipo, int $idCliente): bool
    {
        $sql = "CALL sp_insertar_vehiculo(:placa, :marca, :modelo, :anio, :tipo, :id_cliente)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'placa'      => $placa,
            'marca'      => $marca,
            'modelo'     => $modelo,
            'anio'       => $anio,
            'tipo'       => $tipo,
            'id_cliente' => $idCliente,
        ]);
    }

    public function actualizar(string $placaOriginal, string $placaNueva, string $marca, string $modelo, int $anio, string $tipo, int $idCliente): bool
    {
        $sql = "CALL sp_actualizar_vehiculo(:placa_original, :placa_nueva, :marca, :modelo, :anio, :tipo, :id_cliente)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'placa_original' => $placaOriginal,
            'placa_nueva'    => $placaNueva,
            'marca'          => $marca,
            'modelo'         => $modelo,
            'anio'           => $anio,
            'tipo'           => $tipo,
            'id_cliente'     => $idCliente,
        ]);
    }

    public function eliminar(string $placa): bool
    {
        $sql = "CALL sp_eliminar_vehiculo(:placa)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['placa' => $placa]);
    }
}