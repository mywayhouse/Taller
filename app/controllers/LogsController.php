<?php
namespace App\controllers;

use Controller;
use App\helpers\AuditHelper;

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
