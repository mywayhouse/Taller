<?php
// ============================================================
// ClientesController.php — CRUD de Clientes
// ============================================================
// Controlador para la gestión de clientes del taller.
// Cada método se mapea desde la URL:
//   /clientes        -> index()
//   /clientes/crear  -> crear()
//   /clientes/editar/5 -> editar(5)
//   /clientes/eliminar/5 -> eliminar(5)
// ============================================================

namespace app\controllers;

use Controller;
use app\models\Cliente;

class ClientesController extends Controller
{
    private Cliente $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
    }

    /**
     * Listado de clientes.
     * GET /clientes
     */
    public function index(): void
    {
        $clientes = $this->clienteModel->obtenerTodos();

        $data = [
            'title'       => 'Listado de Clientes',
            'pageTitle'   => 'Clientes',
            'currentPage' => 'clientes',
            'clientes'    => $clientes,
        ];

        $this->renderWithLayout('clientes/index', $data);
    }

    /**
     * Formulario para crear un nuevo cliente.
     * GET /clientes/crear
     */
    public function crear(): void
    {
        $data = [
            'title'       => 'Nuevo Cliente',
            'pageTitle'   => 'Registrar Cliente',
            'currentPage' => 'clientes',
            'cliente'     => [],
            'errores'     => $_SESSION['errores'] ?? [],
        ];

        unset($_SESSION['errores']);
        $this->renderWithLayout('clientes/form', $data);
    }

    /**
     * Guarda un nuevo cliente en la BD.
     * POST /clientes/guardar
     */
    public function guardar(): void
    {
        if (!$this->isPost()) {
            $this->redirect('clientes');
        }

        $nombre    = trim($this->getPost('nombre', ''));
        $telefono  = trim($this->getPost('telefono', ''));
        $rntDni    = trim($this->getPost('rnt_dni', ''));

        // Validación básica
        $errores = [];
        if (empty($nombre))  $errores[] = 'El nombre es obligatorio.';
        if (empty($rntDni))  $errores[] = 'El RTN/DNI es obligatorio.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('clientes/crear');
        }

        // Llamar al SP: sp_insertar_cliente
        $this->clienteModel->insertar($nombre, $telefono, $rntDni);

        $_SESSION['mensaje'] = 'Cliente registrado exitosamente.';
        $this->redirect('clientes');
    }

    /**
     * Formulario para editar un cliente existente.
     * GET /clientes/editar/{id}
     */
    public function editar(int $id): void
    {
        $cliente = $this->clienteModel->obtenerPorId($id);

        if (!$cliente) {
            $this->showError(404, 'Cliente no encontrado.');
            return;
        }

        $data = [
            'title'       => 'Editar Cliente',
            'pageTitle'   => 'Editar Cliente',
            'currentPage' => 'clientes',
            'cliente'     => $cliente,
            'errores'     => $_SESSION['errores'] ?? [],
        ];

        unset($_SESSION['errores']);
        $this->renderWithLayout('clientes/form', $data);
    }

    /**
     * Actualiza los datos de un cliente.
     * POST /clientes/actualizar/{id}
     */
    public function actualizar(int $id): void
    {
        if (!$this->isPost()) {
            $this->redirect('clientes');
        }

        $nombre    = trim($this->getPost('nombre', ''));
        $telefono  = trim($this->getPost('telefono', ''));
        $rntDni    = trim($this->getPost('rnt_dni', ''));

        $this->clienteModel->actualizar($id, $nombre, $telefono, $rntDni);

        $_SESSION['mensaje'] = 'Cliente actualizado exitosamente.';
        $this->redirect('clientes');
    }

    /**
     * Elimina (desactiva) un cliente.
     * GET /clientes/eliminar/{id}
     */
    public function eliminar(int $id): void
    {
        $this->clienteModel->eliminar($id);
        $_SESSION['mensaje'] = 'Cliente desactivado exitosamente.';
        $this->redirect('clientes');
    }

    /**
     * Muestra una página de error.
     */
    private function showError(int $code, string $message): void
    {
        http_response_code($code);
        $errorView = VIEWS . "/errors/{$code}.php";
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "<h1>Error {$code}</h1><p>{$message}</p>";
        }
        exit;
    }
}
