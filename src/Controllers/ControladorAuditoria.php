<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\bd\BaseDatos;

class ControladorAuditoria extends Controlador
{
    public function index(): void
    {
        $this->requireAccess('logs');
        $termino    = trim($this->getGet('q', ''));
        $fechaDesde = $this->getGet('fecha_desde', '');
        $fechaHasta = $this->getGet('fecha_hasta', '');

        if ($termino !== '' || $fechaDesde !== '' || $fechaHasta !== '') {
            $logs = BaseDatos::executeProcedure('sp_buscar_logs', [
                ':termino'      => $termino,
                ':fecha_desde'  => $fechaDesde !== '' ? $fechaDesde . ' 00:00:00' : null,
                ':fecha_hasta'  => $fechaHasta !== '' ? $fechaHasta . ' 23:59:59' : null,
            ]);
        } else {
            $logs = BaseDatos::executeProcedure('sp_listar_logs');
        }

        $data = [
            'title'       => 'Auditoria del Sistema',
            'pageTitle'   => 'Registro de Auditoria',
            'currentPage' => 'logs',
            'logs'        => $logs,
            'q'           => $termino,
            'fechaDesde'  => $fechaDesde,
            'fechaHasta'  => $fechaHasta,
        ];
        $this->renderWithLayout('logs/index', $data);
    }
}
