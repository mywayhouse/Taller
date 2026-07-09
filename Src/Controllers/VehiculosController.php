<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Vehiculo;

class VehiculosController extends Controller
{
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        parent::__construct();
        $this->vehiculoModel = new Vehiculo();
    }

    public function index(): void
    {
        $this->requireAccess('vehiculos');
        $vehiculos = $this->vehiculoModel->obtenerTodos();
        $data = [
            'title'       => 'Listado de Vehiculos',
            'pageTitle'   => 'Vehiculos',
            'currentPage' => 'vehiculos',
            'vehiculos'   => $vehiculos,
        ];
        $this->renderWithLayout('vehiculos/index', $data);
    }

    public function crear(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');
        $data = [
            'title'       => 'Nuevo Vehiculo',
            'pageTitle'   => 'Registrar Vehiculo',
            'currentPage' => 'vehiculos',
            'vehiculo'    => [],
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('vehiculos/form', $data);
    }

    public function guardar(): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');
        if (!$this->isPost()) {
            $this->redirect('vehiculos');
        }
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
        if ($anio <= 1900)        $errores[] = 'El año no es valido.';
        if (empty($tipo))         $errores[] = 'El tipo es obligatorio.';
        if ($idCliente <= 0)      $errores[] = 'Debe seleccionar un cliente valido.';

        $existente = $this->vehiculoModel->obtenerPorPlaca($placa);
        if ($existente) {
            $errores[] = 'Ya existe un vehiculo con esa placa.';
        }

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('vehiculos/crear');
        }

        $this->vehiculoModel->insertar($placa, $marca, $modelo, $anio, $tipo, $idCliente);
        $this->audit("Registro vehiculo: placa {$placa}");
        $_SESSION['mensaje'] = 'Vehiculo registrado exitosamente.';
        $this->redirect('vehiculos');
    }

    public function editar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');
        $vehiculo = $this->vehiculoModel->obtenerPorPlaca($placa);
        if (!$vehiculo) {
            $this->showError(404, 'Vehiculo no encontrado.');
            return;
        }
        $data = [
            'title'       => 'Editar Vehiculo',
            'pageTitle'   => 'Editar Vehiculo',
            'currentPage' => 'vehiculos',
            'vehiculo'    => $vehiculo,
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('vehiculos/form', $data);
    }

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
        if ($anio <= 1900)        $errores[] = 'El año no es valido.';
        if (empty($tipo))         $errores[] = 'El tipo es obligatorio.';
        if ($idCliente <= 0)      $errores[] = 'Debe seleccionar un cliente valido.';

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect("vehiculos/editar/{$placa}");
        }

        $this->vehiculoModel->actualizar($placa, $marca, $modelo, $anio, $tipo, $idCliente);
        $this->audit("Actualizo vehiculo placa {$placa}");
        $_SESSION['mensaje'] = 'Vehiculo actualizado exitosamente.';
        $this->redirect('vehiculos');
    }

    public function eliminar(string $placa): void
    {
        $this->requireAccess('vehiculos');
        $this->requireWriteAccess('vehiculos');
        $this->vehiculoModel->eliminar($placa);
        $this->audit("Elimino vehiculo placa {$placa}");
        $_SESSION['mensaje'] = 'Vehiculo eliminado exitosamente.';
        $this->redirect('vehiculos');
    }

    public function buscarClienteAjax(): void
    {
        $this->requireAuth();
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
