<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Orden;
use App\Models\Vehiculo;

class ControladorOrdenes extends Controlador
{
    private Orden $ordenModel;
    private Vehiculo $vehiculoModel;

    public function __construct()
    {
        parent::__construct();
        $this->ordenModel = new Orden();
        $this->vehiculoModel = new Vehiculo();
    }

    public function index(): void
    {
        $this->requireAccess('ordenes');
        $termino = trim($this->getGet('q', ''));
        $estado  = trim($this->getGet('estado', ''));
        if ($termino !== '' || $estado !== '') {
            $ordenes = $this->ordenModel->buscar($termino, $estado);
        } else {
            $ordenes = $this->ordenModel->obtenerTodos();
        }
        $data = [
            'title'       => 'Listado de Órdenes',
            'pageTitle'   => 'Órdenes de Servicio',
            'currentPage' => 'ordenes',
            'ordenes'     => $ordenes,
            'q'           => $termino,
            'estadoFiltro' => $estado,
        ];
        $this->renderWithLayout('ordenes/index', $data);
    }

    public function ver(int $id): void
    {
        $this->requireAccess('ordenes');
        $orden = $this->ordenModel->obtenerPorId($id);
        if (!$orden) {
            $this->showError(404, 'Orden no encontrada.');
            return;
        }
        $detalles = $this->ordenModel->listarDetalles($id);
        $data = [
            'title'       => "Orden #{$id}",
            'pageTitle'   => "Orden de Servicio #{$id}",
            'currentPage' => 'ordenes',
            'orden'       => $orden,
            'detalles'    => $detalles,
        ];
        $this->renderWithLayout('ordenes/ver', $data);
    }

    public function crear(): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        $usuarios = $this->listarUsuarios();
        $vehiculos = $this->vehiculoModel->obtenerTodos();
        $data = [
            'title'       => 'Nueva Orden',
            'pageTitle'   => 'Crear Orden de Servicio',
            'currentPage' => 'ordenes',
            'orden'       => [],
            'vehiculos'   => $vehiculos,
            'usuarios'    => $usuarios,
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('ordenes/form', $data);
    }

    public function guardar(): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        if (!$this->isPost()) {
            $this->redirect('ordenes');
        }
        $diagnostico    = trim($this->getPost('diagnostico_preliminar', ''));
        $fechaIngreso   = trim($this->getPost('fecha_ingreso', ''));
        $idRecepcionista = (int) $this->getPost('id_recepcionista', 0);
        $idMecanico     = (int) $this->getPost('id_mecanico', 0);
        $placaVehiculo  = trim($this->getPost('placa_vehiculo', ''));
        $tipoServicio   = trim($this->getPost('tipo_servicio', 'REVISION'));
        $errores = [];
        if (empty($diagnostico)) $errores[] = 'El diagnóstico es obligatorio.';
        if (empty($fechaIngreso)) $errores[] = 'La fecha de ingreso es obligatoria.';
        if (empty($idRecepcionista)) $errores[] = 'El recepcionista es obligatorio.';
        if (empty($idMecanico)) $errores[] = 'El mecánico es obligatorio.';
        if (empty($placaVehiculo)) $errores[] = 'La placa del vehículo es obligatoria.';
        if (empty($tipoServicio)) $errores[] = 'El tipo de servicio es obligatorio.';
        if (empty($errores)) {
            $vehiculo = $this->vehiculoModel->obtenerPorPlaca($placaVehiculo);
            if (!$vehiculo) {
                $marca      = trim($this->getPost('marca', ''));
                $modelo     = trim($this->getPost('modelo', ''));
                $anio       = (int) $this->getPost('anio', 0);
                $tipo       = trim($this->getPost('tipo', ''));
                $idCliente  = (int) $this->getPost('id_cliente', 0);
                if (empty($marca))  $errores[] = 'La marca del vehículo es obligatoria.';
                if (empty($modelo)) $errores[] = 'El modelo del vehículo es obligatorio.';
                if ($anio <= 1900)  $errores[] = 'El año del vehículo no es válido.';
                if (empty($tipo))   $errores[] = 'El tipo del vehículo es obligatorio.';
                if ($idCliente <= 0) $errores[] = 'Debe buscar y seleccionar un cliente válido.';
                if (empty($errores)) {
                    $this->vehiculoModel->insertar($placaVehiculo, $marca, $modelo, $anio, $tipo, $idCliente);
                }
            }
        }
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('ordenes/crear');
        }
        $id = $this->ordenModel->insertar($diagnostico, $fechaIngreso, $idRecepcionista, $idMecanico, $placaVehiculo, $tipoServicio);
        $this->audit("Creo la orden #{$id}");
        $_SESSION['mensaje'] = "Orden #{$id} creada exitosamente.";
        $this->redirect('ordenes');
    }

    public function editar(int $id): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        $orden = $this->ordenModel->obtenerPorId($id);
        if (!$orden) {
            $this->showError(404, 'Orden no encontrada.');
            return;
        }
        $usuarios = $this->listarUsuarios();
        $vehiculos = $this->vehiculoModel->obtenerTodos();
        $data = [
            'title'       => "Editar Orden #{$id}",
            'pageTitle'   => "Editar Orden de Servicio #{$id}",
            'currentPage' => 'ordenes',
            'orden'       => $orden,
            'vehiculos'   => $vehiculos,
            'usuarios'    => $usuarios,
            'errores'     => $_SESSION['errores'] ?? [],
        ];
        unset($_SESSION['errores']);
        $this->renderWithLayout('ordenes/form', $data);
    }

    public function actualizar(int $id): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        if (!$this->isPost()) {
            $this->redirect('ordenes');
        }
        $diagnostico  = trim($this->getPost('diagnostico_preliminar', ''));
        $estado       = trim($this->getPost('estado', ''));
        $idMecanico   = (int) $this->getPost('id_mecanico', 0);
        $placaVehiculo = trim($this->getPost('placa_vehiculo', ''));
        $costoManoObra = (float) $this->getPost('costo_mano_obra', 0);
        $tipoServicio = trim($this->getPost('tipo_servicio', 'REVISION'));
        $this->ordenModel->actualizar($id, $diagnostico, $estado, $idMecanico, $placaVehiculo, $costoManoObra, $tipoServicio);
        $this->audit("Actualizo la orden #{$id}");
        $_SESSION['mensaje'] = "Orden #{$id} actualizada exitosamente.";
        $this->redirect('ordenes');
    }

    public function cambiarEstado(int $id): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        if (!$this->isPost()) {
            $this->redirect('ordenes');
        }
        $estado = trim($this->getPost('estado', ''));
        $this->ordenModel->actualizarEstado($id, $estado);
        $this->audit("Cambio estado orden #{$id} a {$estado}");
        $_SESSION['mensaje'] = "Estado de orden #{$id} actualizado a {$estado}.";
        $this->redirect('ordenes/ver/' . $id);
    }

    public function eliminar(int $id): void
    {
        $this->requireAccess('ordenes');
        $this->requireWriteAccess('ordenes');
        $this->ordenModel->eliminar($id);
        $this->audit("Elimino la orden #{$id}");
        $_SESSION['mensaje'] = "Orden #{$id} eliminada exitosamente.";
        $this->redirect('ordenes');
    }

    public function verificarVehiculoAjax(): void
    {
        $this->requireAuth();
        $placa = trim($this->getGet('placa', ''));
        if (empty($placa)) {
            $this->jsonResponse(['existe' => false]);
        }
        $vehiculo = $this->vehiculoModel->obtenerPorPlaca(strtoupper($placa));
        if ($vehiculo) {
            $info = $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ' (' . ($vehiculo['nombre_cliente'] ?? '') . ')';
            $this->jsonResponse(['existe' => true, 'info' => $info]);
        } else {
            $this->jsonResponse(['existe' => false]);
        }
    }

    private function listarUsuarios(): array
    {
        $db = \App\bd\BaseDatos::getConnection();
        $stmt = $db->query("SELECT id_usuario, nombre, rol FROM usuarios WHERE estado_activo = 1 ORDER BY nombre");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
