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
            'repuestos_stock_bajo' => 0,
            'tiempo_promedio_pedidos' => 0
        ];
        //CHARTS
        $repuestosMasVendidos = BaseDatos::executeProcedure('sp_repuestos_mas_vendidos');
        $ingresosSemanales = BaseDatos::executeProcedure('sp_ingresos_semanales');
        // Obtener últimas órdenes
        $ultimasOrdenes = BaseDatos::executeProcedure('sp_listar_ordenes');
        $ultimasOrdenes = array_slice($ultimasOrdenes, 0, 5);
        //tiempo promedio
        $tiempoPromedio = isset($stats['tiempo_promedio_pedidos']) 
            ? round($stats['tiempo_promedio_pedidos'], 1) . ' hrs' 
            : '0 hrs';

        $data = [
            'title' => 'Panel de Control',
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
            'ordenes_pendientes' => $stats['ordenes_pendientes'],
            'clientes_activos' => $stats['clientes_activos'],
            'vehiculos_en_taller' => $stats['vehiculos_en_taller'],
            'repuestos_stock_bajo' => $stats['repuestos_stock_bajo'],
            'tiempo_promedio_pedidos' => $tiempoPromedio,
            'ultimasOrdenes' => $ultimasOrdenes,

            //DATOS DE JSON A JS PARA EL CHART
            'repuestos_labels' => json_encode(array_column($repuestosMasVendidos, 'repuesto')),
            'repuestos_data' => json_encode(array_column($repuestosMasVendidos, 'total_vendido')),
            'ingresos_totales' => json_encode(array_column($ingresosSemanales, 'ingresos_totales')),
            'ingresos_mano_obra' => json_encode(array_column($ingresosSemanales, 'ingresos_mano_obra')),
            'ingresos_semanales_raw' => json_encode($ingresosSemanales),
        ];


        $this->renderWithLayout('dashboard/index', $data);
    }
}