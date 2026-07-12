<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Helpers\AyudaAuditoria;

class ControladorAuditoria extends Controlador
{
    public function index(): void
    {
        $this->requireAccess('logs');
        $logs = AyudaAuditoria::obtenerLogs();
        $data = [
            'title'       => 'Auditoria del Sistema',
            'pageTitle'   => 'Registro de Auditoria',
            'currentPage' => 'logs',
            'logs'        => $logs,
        ];
        $this->renderWithLayout('logs/index', $data);
    }
}
