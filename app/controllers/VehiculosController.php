<?php
namespace App\controllers;

use App\core\Controller;
use App\models\Vehiculo;

class VehiculosController extends Controller
{
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        parent::__construct();
        $this->vehiculoModel = new Vehiculo();
    }

    /**
     * Listado de todos los vehículos.
     */
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

    /**
     * Muestra formulario de creación.
     */
    public function crear(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        $data = [
            'title'       => 'Nuevo Vehículo',
            'pageTitle'   => 'Registrar Vehículo',
            'currentPage' => 'vehiculos',
            'vehiculo'    => [],
            'errores'     => $_SESSION['errores'] ?? [],
        ];

        unset($_SESSION['errores']);
        $this->renderWithLayout('vehiculos/form', $data);
    }

    /**
     * Procesa el formulario de creación.
     */
    public function guardar(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        if (!$this->isPost()) {
            $this->redirect('vehiculos');
        }

        // Obtener y limpiar datos
        $placa      = strtoupper(trim($this->getPost('placa', '')));
        $marca      = trim($this->getPost('marca', ''));
        $modelo     = trim($this->getPost('modelo', ''));
        $anio       = (int) $this->getPost('anio', 0);
        $tipo       = trim($this->getPost('tipo', ''));
        $idCliente  = (int) $this->getPost('id_cliente', 0);

        $errores = [];
        if (empty($placa))        $errores[] = 'La placa es obligatoria.';
        if (empty($marca))        $errores[] = 'La marca es obligatoria.';
        if (empty($modelo))       $errores[] = 'El modelo es obligatorio.';
        if ($anio <= 1900)        $errores[] = 'El año no es válido.';
        if (empty($tipo))         $errores[] = 'El tipo es obligatorio.';
        if ($idCliente <= 0)      $errores[] = 'Debe seleccionar un cliente válido.';

        // Validar placa única
        $existente = $this->vehiculoModel->obtenerPorPlaca($placa);
        if ($existente) {
            $errores[] = 'Ya existe un vehículo con esa placa.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('vehiculos/crear');
        }

        // Insertar
        $this->vehiculoModel->insertar($placa, $marca, $modelo, $anio, $tipo, $idCliente);

        $this->audit("Registró vehículo: placa {$placa}");
        $_SESSION['mensaje'] = 'Vehículo registrado exitosamente.';
        $this->redirect('vehiculos');
    }

    /**
     * Muestra formulario de edición.
     */
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

    /**
     * Procesa la actualización.
     */
    public function actualizar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        if (!$this->isPost()) {
            $this->redirect('vehiculos');
        }

        $marca      = trim($this->getPost('marca', ''));
        $modelo     = trim($this->getPost('modelo', ''));
        $anio       = (int) $this->getPost('anio', 0);
        $tipo       = trim($this->getPost('tipo', ''));
        $idCliente  = (int) $this->getPost('id_cliente', 0);

        $errores = [];
        if (empty($marca))        $errores[] = 'La marca es obligatoria.';
        if (empty($modelo))       $errores[] = 'El modelo es obligatorio.';
        if ($anio <= 1900)        $errores[] = 'El año no es válido.';
        if (empty($tipo))         $errores[] = 'El tipo es obligatorio.';
        if ($idCliente <= 0)      $errores[] = 'Debe seleccionar un cliente válido.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect("vehiculos/editar/{$placa}");
        }

        $this->vehiculoModel->actualizar($placa, $marca, $modelo, $anio, $tipo, $idCliente);

        $this->audit("Actualizó vehículo placa {$placa}");
        $_SESSION['mensaje'] = 'Vehículo actualizado exitosamente.';
        $this->redirect('vehiculos');
    }

    /**
     * Elimina un vehículo.
     */
    public function eliminar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');

        $this->vehiculoModel->eliminar($placa);

        $this->audit("Eliminó vehículo placa {$placa}");
        $_SESSION['mensaje'] = 'Vehículo eliminado exitosamente.';
        $this->redirect('vehiculos');
    }

    /**
     * Endpoint AJAX: busca cliente por RTN/DNI y devuelve JSON.
     */
    public function buscarClienteAjax(): void
    {
        $this->requireAuth(); // solo usuarios logueados

        $rtn = trim($_GET['rtn'] ?? '');

        if (empty($rtn)) {
            $this->jsonResponse(['exito' => false, 'mensaje' => 'RTN/DNI no proporcionado.']);
        }

        $cliente = $this->vehiculoModel->buscarClientePorRtn($rtn);

        if ($cliente) {
            $this->jsonResponse([
                'exito'  => true,
                'cliente' => [
                    'id'     => $cliente['id_cliente'],
                    'nombre' => $cliente['nombre'],
                    'rtn'    => $cliente['rnt_dni']
                ]
            ]);
        } else {
            $this->jsonResponse(['exito' => false, 'mensaje' => 'Cliente no encontrado.']);
        }
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