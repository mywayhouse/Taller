<?php
namespace App\controllers;

use App\core\Controller;
use App\models\Cliente;

class ClientesController extends Controller
{
    private Cliente $clienteModel;

    public function __construct()
    {
        parent::__construct();
        $this->clienteModel = new Cliente();
    }

    public function index(): void
    {
        $this->requireAccess('clientes');

        $clientes = $this->clienteModel->obtenerTodos();

        $data = [
            'title'       => 'Listado de Clientes',
            'pageTitle'   => 'Clientes',
            'currentPage' => 'clientes',
            'clientes'    => $clientes,
        ];

        $this->renderWithLayout('clientes/index', $data);
    }

    public function crear(): void
    {
        $this->requireAccess('clientes');
        $this->requireWriteAccess('clientes');

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

    public function guardar(): void
    {
        $this->requireAccess('clientes');
        $this->requireWriteAccess('clientes');

        if (!$this->isPost()) {
            $this->redirect('clientes');
        }

        $nombre    = trim($this->getPost('nombre', ''));
        $telefono  = trim($this->getPost('telefono', ''));
        $rntDni    = trim($this->getPost('rnt_dni', ''));

        $errores = [];
        if (empty($nombre))  $errores[] = 'El nombre es obligatorio.';
        if (empty($rntDni))  $errores[] = 'El RTN/DNI es obligatorio.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('clientes/crear');
        }

        $this->clienteModel->insertar($nombre, $telefono, $rntDni);

        $this->audit("Creó el cliente: {$nombre}");
        $_SESSION['mensaje'] = 'Cliente registrado exitosamente.';
        $this->redirect('clientes');
    }

    public function editar(int $id): void
    {
        $this->requireAccess('clientes');
        $this->requireWriteAccess('clientes');

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

    public function actualizar(int $id): void
    {
        $this->requireAccess('clientes');
        $this->requireWriteAccess('clientes');

        if (!$this->isPost()) {
            $this->redirect('clientes');
        }

        $nombre    = trim($this->getPost('nombre', ''));
        $telefono  = trim($this->getPost('telefono', ''));
        $rntDni    = trim($this->getPost('rnt_dni', ''));

        $this->clienteModel->actualizar($id, $nombre, $telefono, $rntDni);

        $this->audit("Actualizó el cliente ID {$id}: {$nombre}");
        $_SESSION['mensaje'] = 'Cliente actualizado exitosamente.';
        $this->redirect('clientes');
    }

    public function eliminar(int $id): void
    {
        $this->requireAccess('clientes');
        $this->requireWriteAccess('clientes');

        $cliente = $this->clienteModel->obtenerPorId($id);
        $nombreCliente = $cliente['nombre'] ?? "ID {$id}";

        $this->clienteModel->eliminar($id);

        $this->audit("Desactivó el cliente: {$nombreCliente}");
        $_SESSION['mensaje'] = 'Cliente desactivado exitosamente.';
        $this->redirect('clientes');
    }

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
