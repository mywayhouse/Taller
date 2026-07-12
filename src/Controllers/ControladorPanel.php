<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\bd\BaseDatos;

class ControladorPanel extends Controlador
{
    public function index(): void
    {
        $this->requireAccess('dashboard');

        // Obtener estadísticas del dashboard
        $stats = BaseDatos::executeProcedure('sp_contar_dashboard');
        $stats = $stats[0] ?? [
            'ordenes_pendientes' => 0,
            'clientes_activos' => 0,
            'vehiculos_en_taller' => 0,
            'repuestos_stock_bajo' => 0
        ];

        // Obtener últimas órdenes
        $ultimasOrdenes = BaseDatos::executeProcedure('sp_listar_ordenes');
        $ultimasOrdenes = array_slice($ultimasOrdenes, 0, 5);

        $data = [
            'title' => 'Panel de Control',
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
            'ordenes_pendientes' => $stats['ordenes_pendientes'],
            'clientes_activos' => $stats['clientes_activos'],
            'vehiculos_en_taller' => $stats['vehiculos_en_taller'],
            'repuestos_stock_bajo' => $stats['repuestos_stock_bajo'],
            'ultimasOrdenes' => $ultimasOrdenes,
        ];
        $this->renderWithLayout('dashboard/index', $data);
    }
}