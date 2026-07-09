<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\AuditHelper;

class LogsController extends Controller
{
    public function index(): void
    {
        $this->requireAccess('logs');
        $logs = AuditHelper::obtenerLogs();
        $data = [
            'title'       => 'Auditoria del Sistema',
            'pageTitle'   => 'Registro de Auditoria',
            'currentPage' => 'logs',
            'logs'        => $logs,
        ];
        $this->renderWithLayout('logs/index', $data);
    }
}
