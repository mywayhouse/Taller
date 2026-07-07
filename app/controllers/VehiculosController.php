<?php
namespace app\controllers;

use Controller;
use app\models\Vehiculo;
use app\models\Cliente;
use PDOException;

class VehiculosController extends Controller
{
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        parent::__construct();
        $this->vehiculoModel = new Vehiculo();
    }

    // GET /vehiculos
    public function index(): void
    {
        $this->requireAccess('vehiculos');

        $vehiculos = $this->vehiculoModel->obtenerTodos();

        $data = [
            'title'       => 'Listado de Vehículos',
            'pageTitle'   => 'Vehículos',
            'currentPage' => 'vehiculos',
            'vehiculos'   => $vehiculos,
        ];

        $this->renderWithLayout('vehiculos/index', $data);
    }

    // GET /vehiculos/crear
    public function crear(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        $data = [
            'title'       => 'Nuevo Vehículo',
            'pageTitle'   => 'Registrar Vehículo',
            'currentPage' => 'vehiculos',
            'vehiculo'    => [],          // Vacío para crear
            'errores'     => $_SESSION['errores'] ?? [],
        ];

        unset($_SESSION['errores']);
        $this->renderWithLayout('vehiculos/form', $data);
    }

    // POST /vehiculos/guardar
    public function guardar(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        if (!$this->isPost()) {
            $this->redirect('vehiculos');
        }

        $placa     = strtoupper(trim($this->getPost('placa', '')));
        $marca     = trim($this->getPost('marca', ''));
        $modelo    = trim($this->getPost('modelo', ''));
        $anio      = (int) $this->getPost('anio', 0);
        $tipo      = trim($this->getPost('tipo', ''));
        $idCliente = (int) $this->getPost('id_cliente', 0);

        $errores = [];
        if (empty($placa))       $errores[] = 'La placa es obligatoria.';
        if (empty($marca))       $errores[] = 'La marca es obligatoria.';
        if ($idCliente <= 0)     $errores[] = 'Debe seleccionar un cliente válido.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('vehiculos/crear');
        }

        try {
            $this->vehiculoModel->insertar($placa, $marca, $modelo, $anio, $tipo, $idCliente);
            $this->audit("Registró vehículo placa: {$placa}");
            $_SESSION['mensaje'] = 'Vehículo registrado exitosamente.';
            $this->redirect('vehiculos');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['errores'] = ['La placa ingresada ya existe en el sistema.'];
            } else {
                $_SESSION['errores'] = ['Error al guardar el vehículo.'];
            }
            $this->redirect('vehiculos/crear');
        }
    }

    // GET /vehiculos/editar/{placa}
    public function editar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        $vehiculo = $this->vehiculoModel->obtenerPorPlaca($placa);

        if (!$vehiculo) {
            $this->showError(404, 'Vehículo no encontrado.');
            return;
        }

        $data = [
            'title'       => 'Editar Vehículo',
            'pageTitle'   => 'Editar Vehículo',
            'currentPage' => 'vehiculos',
            'vehiculo'    => $vehiculo,
            'errores'     => $_SESSION['errores'] ?? [],
        ];

        unset($_SESSION['errores']);
        $this->renderWithLayout('vehiculos/form', $data);
    }

    // POST /vehiculos/actualizar/{placa}
    public function actualizar(string $placaOriginal): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        if (!$this->isPost()) {
            $this->redirect('vehiculos');
        }

        $placaNueva = strtoupper(trim($this->getPost('placa', '')));
        $marca      = trim($this->getPost('marca', ''));
        $modelo     = trim($this->getPost('modelo', ''));
        $anio       = (int) $this->getPost('anio', 0);
        $tipo       = trim($this->getPost('tipo', ''));
        $idCliente  = (int) $this->getPost('id_cliente', 0);

        $errores = [];
        if (empty($placaNueva)) $errores[] = 'La placa es obligatoria.';
        if (empty($marca))      $errores[] = 'La marca es obligatoria.';
        if ($idCliente <= 0)   $errores[] = 'Debe seleccionar un cliente válido.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect("vehiculos/editar/{$placaOriginal}");
        }

        try {
            $this->vehiculoModel->actualizar($placaOriginal, $placaNueva, $marca, $modelo, $anio, $tipo, $idCliente);
            $this->audit("Actualizó vehículo placa original {$placaOriginal} a {$placaNueva}");
            $_SESSION['mensaje'] = 'Vehículo actualizado exitosamente.';
            $this->redirect('vehiculos');
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $_SESSION['errores'] = ['La placa nueva ya existe en el sistema.'];
            } else {
                $_SESSION['errores'] = ['Error al actualizar el vehículo.'];
            }
            $this->redirect("vehiculos/editar/{$placaOriginal}");
        }
    }

    // GET /vehiculos/eliminar/{placa}  (con confirmación vía GET, igual que en clientes)
    public function eliminar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        $vehiculo = $this->vehiculoModel->obtenerPorPlaca($placa);
        $placaMostrar = $vehiculo['placa'] ?? $placa;

        $this->vehiculoModel->eliminar($placa);

        $this->audit("Eliminó vehículo placa: {$placaMostrar}");
        $_SESSION['mensaje'] = 'Vehículo eliminado exitosamente.';
        $this->redirect('vehiculos');
    }

    // AJAX: GET /vehiculos/buscarClientes?term=...
    public function buscarClientes(): void
    {
        // Opcional: restringir acceso solo a roles autorizados
        $this->requireAccess('vehiculos');

        if (!isset($_GET['term']) || strlen(trim($_GET['term'])) < 2) {
            echo json_encode([]);
            exit;
        }

        $term = trim($_GET['term']);
        $clienteModel = new Cliente();
        // Método que agregaremos a Cliente a continuación
        $resultados = $clienteModel->buscarPorRtnONombre($term);
        echo json_encode($resultados);
        exit;
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