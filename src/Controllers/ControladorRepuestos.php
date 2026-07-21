<?php
namespace App\Controllers;

use App\Core\Controlador;
use App\Models\Repuesto;

class ControladorRepuestos extends Controlador
{
    private Repuesto $repuestoModel;

    public function __construct()
    {
        parent::__construct();
        $this->repuestoModel = new Repuesto();
    }

    public function index(): void
    {
        $this->requireAccess('repuestos');
        $termino   = trim($this->getGet('q', ''));
        $stockBajo = $this->getGet('stock_bajo', '0') === '1';
        $estado    = (int) $this->getGet('estado', '-1');

        if ($termino !== '' || $stockBajo || $estado !== -1) {
            $repuestos = $this->repuestoModel->buscar($termino, $stockBajo, $estado);
        } else {
            $repuestos = $this->repuestoModel->obtenerTodos();
        }

        $q          = $termino;
        $stockBajoChecked = $stockBajo ? '1' : '0';

        $this->renderWithLayout('repuestos/index', [
            'title'             => 'Inventario de Repuestos',
            'pageTitle'         => 'Repuestos',
            'currentPage'       => 'repuestos',
            'repuestos'         => $repuestos,
            'q'                 => $q,
            'stockBajoChecked'  => $stockBajoChecked,
            'estadoFiltro'      => $estado,
        ]);
    }

    public function crear(): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        $this->renderWithLayout('repuestos/form', [
            'title'       => 'Nuevo Repuesto',
            'pageTitle'   => 'Registrar Repuesto',
            'currentPage' => 'repuestos',
            'repuesto'    => [],
            'errores'     => $_SESSION['errores'] ?? [],
        ]);
        unset($_SESSION['errores']);
    }

    public function guardar(): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        if (!$this->isPost()) {
            $this->redirect('repuestos');
        }
        $nombre       = trim($this->getPost('nombre', ''));
        $stockActual  = (int) $this->getPost('stock_actual', 0);
        $stockMinimo  = (int) $this->getPost('stock_minimo', 0);
        $unidadMedida = trim($this->getPost('unidad_medida', ''));
        $precioVenta  = (float) $this->getPost('precio_venta', 0);

        $errores = [];
        if (empty($nombre))          $errores[] = 'El nombre del repuesto es obligatorio.';
        if ($stockActual < 0)        $errores[] = 'El stock actual no puede ser negativo.';
        if ($stockMinimo < 0)        $errores[] = 'El stock mínimo no puede ser negativo.';
        if ($precioVenta <= 0)       $errores[] = 'El precio de venta debe ser mayor a cero.';
        if (empty($unidadMedida))    $errores[] = 'La unidad de medida es obligatoria.';
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $this->redirect('repuestos/crear');
        }

        $this->repuestoModel->insertar($nombre, $stockActual, $stockMinimo, $unidadMedida, $precioVenta);
        $this->audit("Creo el repuesto: {$nombre} (stock: {$stockActual}, precio: {$precioVenta})");
        $_SESSION['mensaje'] = 'Repuesto registrado exitosamente.';
        $this->redirect('repuestos');
    }

    public function editar(int $id): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        $repuesto = $this->repuestoModel->obtenerPorId($id);
        if (!$repuesto) {
            $this->showError(404, 'Repuesto no encontrado.');
            return;
        }
        $this->renderWithLayout('repuestos/form', [
            'title'       => 'Editar Repuesto',
            'pageTitle'   => 'Editar Repuesto',
            'currentPage' => 'repuestos',
            'repuesto'    => $repuesto,
            'errores'     => $_SESSION['errores'] ?? [],
        ]);
        unset($_SESSION['errores']);
    }

    public function actualizar(int $id): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        if (!$this->isPost()) {
            $this->redirect('repuestos');
        }
        $nombre       = trim($this->getPost('nombre', ''));
        $stockActual  = (int) $this->getPost('stock_actual', 0);
        $stockMinimo  = (int) $this->getPost('stock_minimo', 0);
        $unidadMedida = trim($this->getPost('unidad_medida', ''));
        $precioVenta  = (float) $this->getPost('precio_venta', 0);

        $this->repuestoModel->actualizar($id, $nombre, $stockActual, $stockMinimo, $unidadMedida, $precioVenta);
        $this->audit("Actualizo el repuesto ID {$id}: {$nombre}");
        $_SESSION['mensaje'] = 'Repuesto actualizado exitosamente.';
        $this->redirect('repuestos');
    }

    public function eliminar(int $id): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        $repuesto = $this->repuestoModel->obtenerPorId($id);
        $nombre   = $repuesto['nombre'] ?? "ID {$id}";
        $this->repuestoModel->eliminar($id);
        $this->audit("Desactivo el repuesto: {$nombre}");
        $_SESSION['mensaje'] = 'Repuesto desactivado exitosamente.';
        $this->redirect('repuestos');
    }

    public function movimientos(int $id): void
    {
        $this->requireAccess('repuestos');
        $repuesto = $this->repuestoModel->obtenerPorId($id);
        if (!$repuesto) {
            $this->showError(404, 'Repuesto no encontrado.');
            return;
        }
        $movimientos = $this->repuestoModel->obtenerMovimientos($id);
        $this->renderWithLayout('repuestos/movimientos', [
            'title'       => 'Movimientos - ' . $repuesto['nombre'],
            'pageTitle'   => 'Movimientos: ' . htmlspecialchars($repuesto['nombre']),
            'currentPage' => 'repuestos',
            'repuesto'    => $repuesto,
            'movimientos' => $movimientos,
        ]);
    }

    public function ajustarStock(int $id): void
    {
        $this->requireAccess('repuestos');
        $this->requireWriteAccess('repuestos');
        if (!$this->isPost()) {
            $this->redirect('repuestos');
        }
        $repuesto = $this->repuestoModel->obtenerPorId($id);
        if (!$repuesto) {
            $this->showError(404, 'Repuesto no encontrado.');
            return;
        }
        $nuevoStock  = (int) $this->getPost('nuevo_stock', -1);
        $observacion = trim($this->getPost('observacion', ''));
        if ($nuevoStock < 0) {
            $_SESSION['error'] = 'El nuevo stock no puede ser negativo.';
            $this->redirect('repuestos/movimientos/' . $id);
        }
        $diferencia = $nuevoStock - $repuesto['stock_actual'];
        $tipo = $diferencia > 0 ? 'ENTRADA' : ($diferencia < 0 ? 'SALIDA' : 'AJUSTE');
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $idUsuario = (int) ($_SESSION['usuario_id'] ?? 0);
        $this->repuestoModel->ajustarStock($id, $nuevoStock, $idUsuario, $ip, $observacion);
        $this->audit("Ajusto stock del repuesto ID {$id} ({$repuesto['nombre']}): {$tipo} " . abs($diferencia) . " unidades (nuevo stock: {$nuevoStock})");
        $this->audit("Ajusto inventario: {$repuesto['nombre']} - {$tipo} " . abs($diferencia) . " unidades");
        $_SESSION['mensaje'] = "Stock ajustado exitosamente. {$tipo}: " . abs($diferencia) . " unidades.";
        $this->redirect('repuestos/movimientos/' . $id);
    }

    public function buscarAjax(): void
    {
        $this->requireAccess('repuestos');
        $termino = trim($this->getGet('q', ''));
        $resultados = $this->repuestoModel->buscar($termino);
        $this->jsonResponse($resultados);
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
