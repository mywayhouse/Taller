<?php
namespace App\Models;

use App\Core\Modelo;

class Factura extends Modelo
{
    public function obtenerTodos(): array
    {
        return $this->callProcedure('sp_listar_facturas');
    }

    public function obtenerPorId(int $id): ?array
    {
        $result = $this->callProcedure('sp_obtener_factura_por_id', [
            ':p_id_factura' => $id,
        ]);
        return $result[0] ?? null;
    }

    public function obtenerRepuestosPorOrden(int $idOrden): array
    {
        return $this->callProcedure('sp_obtener_repuestos_por_orden', [
            ':p_id_orden' => $idOrden,
        ]);
    }

    public function obtenerOrdenesDisponibles(): array
    {
        return $this->callProcedure('sp_obtener_ordenes_para_facturar');
    }

    public function insertar(
        string $numeroFactura,
        float $subtotalManoObra,
        float $subtotalRepuestos,
        float $isv,
        float $totalPagar,
        int $idOrden,
        string $metodoPago
    ): int {
        return $this->callNonQuery('sp_insertar_factura', [
            ':p_numero_factura'     => $numeroFactura,
            ':p_subtotal_mano_obra' => $subtotalManoObra,
            ':p_subtotal_repuestos' => $subtotalRepuestos,
            ':p_isv'                => $isv,
            ':p_total_pagar'        => $totalPagar,
            ':p_id_orden'           => $idOrden,
            ':p_metodo_pago'        => $metodoPago,
        ]);
    }

    public function anular(int $id): int
    {
        return $this->callNonQuery('sp_anular_factura', [
            ':p_id_factura' => $id,
        ]);
    }

    public function generarNumeroFactura(): string
    {
        $result = $this->callProcedure('sp_generar_numero_factura');
        return $result[0]['nuevo_numero'] ?? 'FAC-' . date('Y') . '-0001';
    }
}
