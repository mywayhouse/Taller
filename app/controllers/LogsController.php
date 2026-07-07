<?php
namespace app\controllers;

use app\core\Controller;
use app\helpers\AuditHelper;

class LogsController extends Controller
{
    public function index(): void
    {
        $this->requireAccess('logs');

        $logs = AuditHelper::obtenerLogs();

        $data = [
            'title'       => 'Auditoría del Sistema',
            'pageTitle'   => 'Registro de Auditoría',
            'currentPage' => 'logs',
            'logs'        => $logs,
        ];

        $this->renderWithLayout('logs/index', $data);
    }
}
