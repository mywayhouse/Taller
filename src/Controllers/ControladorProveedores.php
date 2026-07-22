<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Proveedor;

class ControladorProveedores extends Controlador
{
    private Proveedor $proveedorModel;

    public function __construct()
    {
        parent::__construct();
        $this->proveedorModel = new Proveedor();
    }

    public function index(): void
    {
        $this->requireAccess('proveedores');
        $proveedores = $this->proveedorModel->obtenerTodos();
        $this->renderWithLayout('proveedores/index', [
            'title'       => 'Listado de Proveedores',
            'pageTitle'   => 'Proveedores',
            'currentPage' => 'proveedores',
            'proveedores' => $proveedores,
        ]);
    }

    public function crear(): void
    {
        $this->requireAccess('proveedores');
        $this->requireWriteAccess('proveedores');
        $this->renderWithLayout('proveedores/form', [
            'title'      => 'Nuevo Proveedor',
            'pageTitle'  => 'Registrar Proveedor',
            'currentPage' => 'proveedores',
            'proveedor'  => [],
            'errores'    => $_SESSION['errores'] ?? [],
        ]);
        unset($_SESSION['errores']);
    }

    public function guardar(): void
    {
        $this->requireAccess('proveedores');
        $this->requireWriteAccess('proveedores');
        if (!$this->isPost()) {
            $this->redirect('proveedores');
        }
        $nombre   = trim($this->getPost('nombre', ''));
        $contacto = trim($this->getPost('contacto', ''));
        $telefono = trim($this->getPost('telefono', ''));
        $direccion = trim($this->getPost('direccion', ''));

        $errores = [];
        if (empty($nombre)) $errores[] = 'El nombre del proveedor es obligatorio.';
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('proveedores/crear');
        }

        $this->proveedorModel->insertar($nombre, $contacto, $telefono, $direccion);
        $this->audit("Creo el proveedor: {$nombre}");
        $_SESSION['mensaje'] = 'Proveedor registrado exitosamente.';
        $this->redirect('proveedores');
    }

    public function editar(int $id): void
    {
        $this->requireAccess('proveedores');
        $this->requireWriteAccess('proveedores');
        $proveedor = $this->proveedorModel->obtenerPorId($id);
        if (!$proveedor) {
            $this->showError(404, 'Proveedor no encontrado.');
            return;
        }
        $this->renderWithLayout('proveedores/form', [
            'title'      => 'Editar Proveedor',
            'pageTitle'  => 'Editar Proveedor',
            'currentPage' => 'proveedores',
            'proveedor'  => $proveedor,
            'errores'    => $_SESSION['errores'] ?? [],
        ]);
        unset($_SESSION['errores']);
    }

    public function actualizar(int $id): void
    {
        $this->requireAccess('proveedores');
        $this->requireWriteAccess('proveedores');
        if (!$this->isPost()) {
            $this->redirect('proveedores');
        }
        $nombre   = trim($this->getPost('nombre', ''));
        $contacto = trim($this->getPost('contacto', ''));
        $telefono = trim($this->getPost('telefono', ''));
        $direccion = trim($this->getPost('direccion', ''));

        $this->proveedorModel->actualizar($id, $nombre, $contacto, $telefono, $direccion);
        $this->audit("Actualizo el proveedor ID {$id}: {$nombre}");
        $_SESSION['mensaje'] = 'Proveedor actualizado exitosamente.';
        $this->redirect('proveedores');
    }

    public function eliminar(int $id): void
    {
        $this->requireAccess('proveedores');
        $this->requireWriteAccess('proveedores');
        $proveedor = $this->proveedorModel->obtenerPorId($id);
        $nombre = $proveedor['nombre'] ?? "ID {$id}";
        $this->proveedorModel->eliminar($id);
        $this->audit("Elimino el proveedor: {$nombre}");
        $_SESSION['mensaje'] = 'Proveedor eliminado exitosamente.';
        $this->redirect('proveedores');
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
