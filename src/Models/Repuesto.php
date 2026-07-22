<?php
namespace App\Models;

use App\Core\Modelo;

class Repuesto extends Modelo
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_repuestos');
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_repuesto_por_id', [
            ':id_repuesto' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function insertar(string $nombre, int $stockActual, int $stockMinimo, string $unidadMedida, float $precioVenta): int
    {
        return $this->callNonQuery('sp_insertar_repuesto', [
            ':nombre'         => $nombre,
            ':stock_actual'   => $stockActual,
            ':stock_minimo'   => $stockMinimo,
            ':unidad_medida'  => $unidadMedida,
            ':precio_venta'   => $precioVenta,
        ]);
    }

    public function actualizar(int $id, string $nombre, int $stockActual, int $stockMinimo, string $unidadMedida, float $precioVenta): int
    {
        return $this->callNonQuery('sp_actualizar_repuesto', [
            ':id_repuesto'   => $id,
            ':nombre'        => $nombre,
            ':stock_actual'  => $stockActual,
            ':stock_minimo'  => $stockMinimo,
            ':unidad_medida' => $unidadMedida,
            ':precio_venta'  => $precioVenta,
        ]);
    }

    public function eliminar(int $id): int
    {
        return $this->callNonQuery('sp_eliminar_repuesto', [
            ':id_repuesto' => $id,
        ]);
    }

    public function buscar(string $termino, bool $stockBajo = false, int $estado = -1): array
    {
        return $this->callProcedure('sp_buscar_repuestos_todos', [
            ':termino'    => $termino,
            ':stock_bajo' => $stockBajo ? 1 : 0,
            ':estado'     => $estado,
        ]);
    }

    public function ajustarStock(int $idRepuesto, int $nuevoStock, int $idUsuario, string $ip, string $observacion = ''): int
    {
        return $this->callNonQuery('sp_ajustar_stock_repuesto', [
            ':id_repuesto'  => $idRepuesto,
            ':nuevo_stock'  => $nuevoStock,
            ':id_usuario'   => $idUsuario,
            ':ip_direccion' => $ip,
            ':observacion'  => $observacion,
        ]);
    }

    public function descontarStock(int $idRepuesto, int $cantidad): int
    {
        return $this->callNonQuery('sp_descontar_stock_repuesto', [
            ':id_repuesto' => $idRepuesto,
            ':cantidad'    => $cantidad,
        ]);
    }

    public function obtenerMovimientos(int $idRepuesto): array
    {
        return $this->callProcedure('sp_listar_movimientos_repuesto', [
            ':id_repuesto' => $idRepuesto,
        ]);
    }

    public function registrarEntrada(int $idRepuesto, int $cantidad, ?int $idProveedor, int $idUsuario, string $ip, string $observacion = ''): int
    {
        return $this->callNonQuery('sp_registrar_entrada_repuesto', [
            ':id_repuesto'  => $idRepuesto,
            ':cantidad'     => $cantidad,
            ':id_proveedor' => $idProveedor,
            ':id_usuario'   => $idUsuario,
            ':ip_direccion' => $ip,
            ':observacion'  => $observacion,
        ]);
    }
}
