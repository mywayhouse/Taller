<?php
namespace App\Controllers;

use App\Core\Controlador;

class ControladorPanel extends Controlador
{
    public function index(): void
    {
        $this->requireAccess('dashboard');
        $data = [
            'title' => 'Panel de Control',
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
        ];
        $this->renderWithLayout('dashboard/index', $data);
    }
}
