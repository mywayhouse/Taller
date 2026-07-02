<?php
// ============================================================
// DashboardController.php — Panel principal del sistema
// ============================================================
// Controlador por defecto. Se ejecuta cuando el usuario
// ingresa a la raíz del sistema sin especificar ruta.
// ============================================================

namespace app\controllers;

use Controller;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con estadísticas generales.
     */
    public function index(): void
    {
        // Verificar que el usuario haya iniciado sesión
        $this->requireAuth();

        $data = [
            'title' => 'Panel de Control',
            'pageTitle' => 'Dashboard',
            'currentPage' => 'dashboard',
        ];

        $this->renderWithLayout('dashboard/index', $data);
    }

    /**
     * Redirige al login si no hay sesión activa.
     */
    private function requireAuth(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('auth/login');
        }
    }
}
