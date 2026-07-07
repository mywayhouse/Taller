<?php
namespace app\controllers;

use app\core\Controller;

class DashboardController extends Controller
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
