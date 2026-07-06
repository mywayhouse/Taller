<?php
namespace app\controllers;

use app\models\Cliente;
use Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAccess('dashboard');

        $clienteModel = new Cliente();
        $totalClientes = $clienteModel->contarActivos();

        $data = [
            'title' => 'Panel de Control',
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
            'totalClientesActivos' => $clienteModel->contarActivos()
        ];

        $this->renderWithLayout('dashboard/index', $data);
    }
}
